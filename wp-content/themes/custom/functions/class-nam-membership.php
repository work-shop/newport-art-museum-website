<?php

/**
 * This class contains a number of static Methods
 * for dealing with membership, applying membership
 * discounts to products, and working with membership status.
 */
class NAM_Membership {

	public static $membership_category_slug = 'membership-tiers';
	public static $events_category_slug = 'events';
	public static $classes_category_slug = 'classes';
	public static $calculate_totals_hook = 'woocommerce_before_calculate_totals';
	public static $calculate_product_line_total_hook = 'woocommerce_cart_product_subtotal';
	public static $display_cart_totals = 'woocommerce_cart_item_price';

	public static $field_keys = array(
		'membership_discount_eligibility' => 'field_5bf5a1e6af83d',
	);

	/**
	 * Registers actions for the cart which apply membership-based
	 * discounts to the products in the cart to which a discount applies.
	 *
	 */
	public static function register_hooks() {

		$called_class = get_called_class();

		add_action(static::$calculate_totals_hook, array($called_class, 'calculate_membership_discounts'), 20, 1);

		//add_action(static::$calculate_product_line_total_hook, array($called_class, 'calculate_line_discount_total'), 20, 4);
		add_filter(static::$display_cart_totals, array($called_class, 'show_bundle_base_price_minus_fees'), 10, 3);

	}

	/**
	 * Calculate the membership discounts for the current cart, based
	 * on whether the purchaser is a member, or has a membership product
	 * in their cart.
	 *
	 */
	public static function calculate_membership_discounts($cart_object) {

		global $woocommerce;

		if (is_admin() && !defined('DOING_AJAX')) {return;}
		if (did_action(static::$calculate_totals_hook) >= 2) {return;}

		if (static::is_member() || static::has_membership_in_cart()) {

			$discount_membership_multiplier = static::get_membership_discount_multiplier();

			foreach ($cart_object->get_cart() as $key => $cart_item) {

				$name = $cart_item['data']->name;

				$is_event_variation = $cart_item['data'] instanceof WC_Product_Variation;
				$product_id_type = ($is_event_variation) ? 'variation_id' : 'product_id';

				$id = $cart_item[$product_id_type];
				$base_discount = static::get_membership_discount($id);
				$quantity = $cart_item['quantity'];
				$total_discount = $base_discount * min($quantity, $discount_membership_multiplier);

				if ($total_discount > 0) {

					$woocommerce->cart->add_fee('Membership Discount: ' . $name, -(double) $total_discount, true, '');

				}

			}

		}

	}


    public static function show_bundle_base_price_minus_fees( $old_display, $cart_item, $cart_item_key ) {

        // NOTE: if this item is a product bundled with fees.
        if ( $cart_item['data'] instanceof WC_Product_Bundle ) {

            return wc_price($cart_item['data']->get_price());

        } else {

            return $old_display;

        }

    }

	/**
	 * Given the old display strong, cart item, and cart item key,
	 * renderes the "base price" for a product bundle to the
	 * cart table, rather than the total price of the bundle.
	 *
	 * @hooked woocommerce_cart_item_price
	 */
	public static function show_base_price($old_display, $cart_item, $cart_item_key) {

		// NOTE: If it's a class - or an instance of a Product Bundle
		if ($cart_item['data'] instanceof WC_Product_Bundle) {

			if (static::is_member() || static::has_membership_in_cart()) {

				$discount = NAM_Membership::get_membership_discount($cart_item['data']->id);

				return wc_price($cart_item['data']->get_price() + $discount);

			} else {

				return wc_price($cart_item['data']->get_price());

			}

			// NOTE: if it's an event – or an instance of an variation.
		} else if ($cart_item['variation_id'] !== 0) {

			if (static::is_member() || static::has_membership_in_cart()) {

				$discount = NAM_Membership::get_membership_discount($cart_item['variation_id']);

				return wc_price($cart_item['data']->get_price() + $discount);

			} else {

				return wc_price($cart_item['data']->get_price());

			}

			// NOTE: Do the normal old thing.
		} else {

			return $old_display;

		}

	}

	/**
	 * get all the memberships that given user
	 * has, and return a list of membership products.
	 *
	 * defaults to the current user, if no id is specified.
	 */
	public static function get_membership($user_id = null) {
		if (null == $user_id) {$user_id = get_current_user_id();}
		//if ( 0 == $user_id ) { return false; }

		$flat_products = array();

		$subscriptions = get_posts(array(
			'numberposts' => -1,
			'meta_key' => '_customer_user',
			'meta_value' => $user_id,
			'post_type' => 'shop_subscription',
			'post_status' => 'wc-active',
		));

		array_map(function ($sub) use ($flat_products) {

			$sub = wcs_get_subscription($sub->ID);
			$products = $sub->get_items();

			array_merge($flat_products, $products);

			return $products;

		}, $subscriptions);

		return $flat_products;

	}

	/**
	 * Get all the membership products in the cart,
	 * and return them as an array.
	 */
	public static function get_membership_in_cart() {

		$memberships_in_cart = array();

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

			$product = $cart_item['data'];

			if (has_term(static::$membership_category_slug, 'product_cat', $product->id)) {
				$memberships_in_cart[] = $product;
			}

		}

		return $memberships_in_cart;

	}

	/**
	 * Given a user_id, returns a boolean indicating
	 * Whether or not a member has purchased a membership,
	 * and has an active subscription.
	 *
	 * @param int $user_id optional user id to look up a membership for.
	 * @return boolean true if $user_id is a member
	 */
	public static function is_member($user_id = null) {

		return !empty(static::get_membership($user_id));

	}

	/**
	 * returns whether or not the current user has
	 * a membership item in their cart or not.
	 *
	 * @return boolean true if the current session has a membership in the cart.
	 */
	public static function has_membership_in_cart() {

		return !empty(static::get_membership_in_cart());

	}

	/**
	 * Get the discount multiplier applicable to a given cart.
	 * Include existing memberships, as well as memberships that
	 * may be in the cart. Take the max eligible discount multiplier
	 * from this set.
	 */
	public static function get_membership_discount_multiplier() {

		$memberships_in_cart = static::get_membership_in_cart();
		$memberships = static::get_membership();

		$merged_memberships = array_merge($memberships_in_cart, $memberships);

		$discount = array_reduce($merged_memberships, function ($max_multiplier, $membership) {

			$parent = NAM_Membership_Tier::get_parent_posts($membership->id);

			if (count($parent) == 0) {

				return $max_multiplier;

			} else {

				$parent = $parent[0];
				$discount_membership_multiplier = get_field(static::$field_keys['membership_discount_eligibility'], $parent);
				if ($discount_membership_multiplier) {

					return max($max_multiplier, (int) $discount_membership_multiplier);

				} else {
					return $max_multiplier;
				}

			}

		}, 0);

		return $discount;

	}

	/**
	 * This function gets the membership discount amount for a given product
	 *
	 * @param int $product_id the id of the product to get the discount for.
	 * @return double the discounted amount to subtract from the product total.
	 */
	public static function get_membership_discount($product_id) {

		$discount = get_post_meta($product_id, '_nam_membership_discount', true);
		if ($discount) {
			return (double) $discount;
		} else {
			return 0;
		}
	}

	/**
	 * This function returns true if the product is a discountable product.
	 * Currently, discountable products include 'classes' and 'events'.
	 *
	 * @param int $product_id the product id to check
	 * @return boolean true if the product is a discountable product.
	 */
	public static function is_discountable_product($product_id) {
		return has_term(static::$events_category_slug, 'product_cat', $product_id) || has_term(static::$classes_category_slug, 'product_cat', $product_id);
	}

	/**
	 * Given a subscription ID, check and determine whether
	 * this subscription was imported via the membership importer
	 * or membership creator, or was created through the site's frontend.
	 *
	 * @param int $subscription_id the subscription ID to test.
	 * @return boolean true if the membership was imported.
	 */
	public static function membership_was_imported($subscription_id) {

		$subscription_meta = get_post_meta($subscription_id, NAM_Membership_Creator::$imported_membership_meta, true);

		return 'yes' === $subscription_meta;

	}

	/**
	 * Given a subscription ID, check and determine whether
	 * this subscription was imported via the membership importer
	 * or membership creator, or was created through the site's frontend.
	 *
	 * @param int $user_id the subscription ID to test.
	 * @return boolean true if the user was created through the import.
	 */
	public static function user_was_imported($user_id = NULL) {

		if ($user_id == NULL) {$user_id = get_current_user_id();}

		$user_meta = get_user_meta($user_id, NAM_Membership_Creator::$imported_member_meta, true);

		return 'yes' === $user_meta;

	}

	/**
	 * This routine attempts to retrieve the parent post
	 * represented by the order product item in this subscription.
	 *
	 * @param WC_Subscription $subscription a subscription post
	 * @return WP_Post the parent post of the WC_Product in $subscription.
	 */
	public function get_membership_for_subscription($subscription) {

		foreach ($subscription->get_items() as $item) {
			$product = $item->get_product();

			if (has_term(static::$membership_category_slug, 'product_cat', $product->id)) {

				return $product;

			}

		}

		return FALSE;

	}


    /**
     * A simple method for checking whether a product has a membership
     * category, and is therefore a membership product.
     *
     * @param int $product_id the id of the product to check
     * @return boolean true is the product is a membership.
     */
    public static function is_membership_product( $product_id ) {
        return has_term(static::$membership_category_slug, 'product_cat', $product_id);
    }

}
