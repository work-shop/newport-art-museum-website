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
    public static $display_cart_totals = 'woocommerce_cart_item_price';



    /**
     * Registers actions for the cart which apply membership-based
     * discounts to the products in the cart to which a discount applies.
     *
     */
    public static function register_hooks() {

        $called_class = get_called_class();

        add_action( static::$calculate_totals_hook, array( $called_class, 'calculate_membership_discounts'), 20, 1);
        //add_filter( static::$display_cart_totals, array( $called_class, 'show_membership_cart_total'), 10, 3);
        add_filter( static::$display_cart_totals, array( $called_class, 'show_bundle_base_price'), 10, 3);

    }

    /**
     * Calculate the membership discounts for the current cart, based
     * on whether the purchaser is a member, or has a membership product
     * in their cart.
     *
     */
    public static function calculate_membership_discounts( $cart_object ) {



        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) { return; }
        if ( did_action( static::$calculate_totals_hook ) >= 2 ) { return; }

        if( static::is_member() || static::has_membership_in_cart() ) {

            foreach ( $cart_object->get_cart() as $cart_item ) {

                $id = $cart_item[ 'data' ]->id;
                $discount = static::get_membership_discount( $id );

                $final_price = $cart_item[ 'data' ]->get_price() - $discount;

                $cart_item[ 'data' ]->set_price( $final_price );

            }
        }

    }

    /**
     * This function renders the discounted price for members
     * in the cart, next to the old price.
     *
     */
    public static function show_membership_cart_total( $old_display, $cart_item, $cart_item_key ) {

        $product_id = $cart_item['data']->id;
        $membership_discount = static::get_membership_discount( $product_id );

        if ( $membership_discount > 0 && !static::is_member() && !static::has_membership_in_cart() ) {

            return wc_price( $cart_item['data']->get_price() ) . ' ('. wc_price( $cart_item['data']->get_price() - $membership_discount ) .' for members)';

        }

        return $old_display;

    }

    /**
     * Given the old display strong, cart item, and cart item key,
     * renderes the "base price" for a product bundle to the
     * cart table, rather than the total price of the bundle.
     *
     * @hooked woocommerce_cart_item_price
     */
    public static function show_bundle_base_price( $old_display, $cart_item, $cart_item_key ) {

        if ( $cart_item['data'] instanceof WC_Product_Bundle ) {

            return wc_price( $cart_item['data']->get_price() );

        } else {

            return $old_display;

        }

    }

    /**
     * Given a user_id, returns a boolean indicating
     * Whether or not a member has purchased a membership,
     * and has an active subscription.
     *
     * @param int $user_id optional user id to look up a membership for.
     * @return boolean true if $user_id is a member
     */
    public static function is_member( $user_id=null ) {

        if ( null == $user_id ) { $user_id = get_current_user_id(); }
        //if ( 0 == $user_id ) { return false; }

        $subscriptions = get_posts( array(
            'numberposts' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => $user_id,
            'post_type'   => 'shop_subscription',
            'post_status' => 'wc-active',
        ));

        return !empty( $subscriptions );

    }

    /**
     * returns whether or not the current user has
     * a membership item in their cart or not.
     *
     * @return boolean true if the current session has a membership in the cart.
     */
    public static function has_membership_in_cart( ) {

        $has_membership_in_cart = false;

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

            $product = $cart_item['data'];

            if ( has_term( static::$membership_category_slug, 'product_cat', $product->id ) ) {
                $has_membership_in_cart = true;
                break;
            }

        }

        return $has_membership_in_cart;

    }

    /**
     * This function gets the membership discount amount for a given product
     *
     * @param int $product_id the id of the product to get the discount for.
     * @return double the discounted amount to subtract from the product total.
     */
    public static function get_membership_discount( $product_id ) {
        $discount = get_post_meta( $product_id, '_nam_membership_discount', true );
        if ( $discount ) {
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
    public static function is_discountable_product( $product_id ) {
        return has_term( static::$events_category_slug, 'product_cat', $product_id ) || has_term( static::$classes_category_slug, 'product_cat', $product_id );
    }

}
