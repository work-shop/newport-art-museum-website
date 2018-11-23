<?php

/**
 * This class encapsulates functionality related
 * to managing classes, and imposing constraints on
 * the classes checkout process.
 */
class NAM_Events {

	/**
	 * Adds custom meta keys or settings for WooCommerce products
	 * that shadow "classes" custom post types.
	 *
	 * @param int $product_id the id of the shadowing product.
	 */
	public static function get_ticket_levels($product) {

		$variations = $product->get_available_variations();

		$variations = array_map(function ($variation) use ($product) {

			$discount = get_post_meta($variation['variation_id'], '_nam_membership_discount', true);
			$ticket_level = get_term_by('slug', $variation['attributes']['attribute_pa_ticket_levels'], 'pa_ticket_levels');

			return array(
				'title' => $variation['attributes']['attribute_pa_ticket_levels'],
				'term' => $ticket_level,
				'price' => $variation['display_price'],
				'in_stock' => $variation['is_in_stock'],
				'max_qty' => $variation['max_qty'],
				'min_qty' => $variation['min_qty'],
				'id' => $variation['variation_id'],
				'product_id' => $product->id,
				'membership_discount' => (double) $discount,
				// 'variation' => $variation
			);

		}, $variations);

		return $variations;
	}

	public static function save_notices() {

		return $_GET['save_notices'] !== NULL || $_POST['save_notices'] !== NULL;

	}

	public function __construct() {
		$this->register_hooks();
	}

	public function register_hooks() {
		add_filter('template_redirect', array($this, 'manage_cart'), 10, 0);
	}


	public function manage_cart() {

        $seen_products = array();

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

            $sold_individually = 'yes' === get_post_meta( $cart_item['product_id'], '_sold_individually', true );
            $is_membership = NAM_Membership::is_membership_product( $cart_item['product_id'] );

            if ( $is_membership && $seen_products[ 'membership' ] ) {

                WC()->cart->remove_cart_item( $cart_item_key );
                wc_print_notice( 'You already have a ' .  $seen_products[ 'membership' ]->name . ' in your cart.', 'notice' );

            } else if ( $seen_products[ $cart_item['product_id'] ] ) {

                WC()->cart->remove_cart_item( $cart_item_key );
                wc_print_notice( 'You already have a ticket for ' . $cart_item['data']->name . ' in your cart.', 'notice' );

            } else {

                if ( $sold_individually ) {

                    $seen_products[ $cart_item['product_id'] ] = true;

                } else if ( $is_membership ) {

                    $seen_products[ 'membership' ] = $cart_item['data'];

                }

            }

        }

	}

}

new NAM_Events();

?>
