<?php



/**
 * This class contains a number of static Methods
 * for dealing with membership, applying membership
 * discounts to products, and working with membership status.
 */
class NAM_Membership {

    public static $membership_category_slug = 'membership-tiers';



    /**
     * Registers actions for the cart which apply membership-based
     * discounts to the products in the cart to which a discount applies.
     *
     */
    public static function register_hooks() {

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

}
