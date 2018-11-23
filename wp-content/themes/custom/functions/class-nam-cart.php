<?php

class NAM_Cart {


    public function __construct() {
        $this->register_hooks();
    }

    /**
     * Set up the hooks we need to monitor.
     */
     public function register_hooks() {
 		add_filter('template_redirect', array($this, 'manage_cart'), 10, 0);
 	}

    /**
     * Set up a procedure for managing the products that
     * make it into the cart, so we can ensure that certain
     * product types which are sold individually – variations for events
     * and membership-type products – are only present in the cart once.
     *
     */
 	public function manage_cart() {

         $seen_products = array();

         foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

             $sold_individually = 'yes' === get_post_meta( $cart_item['product_id'], '_sold_individually', true );
             $is_membership = NAM_Membership::is_membership_product( $cart_item['product_id'] );

             if ( $is_membership && $seen_products[ 'membership' ] ) {

                 WC()->cart->remove_cart_item( $cart_item_key );
                 wc_add_notice( 'You already have a ' .  $seen_products[ 'membership' ]->name . ' in your cart.', 'error' );

             } else if ( $seen_products[ $cart_item['product_id'] ] ) {

                 WC()->cart->remove_cart_item( $cart_item_key );
                 wc_add_notice( 'You already have a ticket for ' . $seen_products[ $cart_item['product_id'] ]->name . ' in your cart.', 'error' );

             } else {

                 if ( $sold_individually ) {

                     $seen_products[ $cart_item['product_id'] ] = $cart_item['data'];

                 } else if ( $is_membership ) {

                     $seen_products[ 'membership' ] = $cart_item['data'];

                 }

             }

         }

 	}



    /**
     * Given a specific woocommerce product id, check the cart to
     * see if it contains that product id. Optionally, pass true
     * to function as a second parameter to search through product
     * variation ids, too.
     *
     * @param int $product_id the id to search for.
     * @param boolean $variation true if you want to look for variations also.
     * @return boolean true if the product or variation is contained in the cart.
     */
    public function in_cart( $product_id, $variation=false ) {

        if ( $variation ) {

            foreach (WC()->cart->get_cart() as $item_key => $cart_item) {

                if ( $cart_item['variation_id'] === $product_id || $cart_item['product_id'] === $product_id  ) {

                    // NOTE: check the 'product_id' too, just incase we accidentialy set the variation flag.
                    return true;

                }

            }

            return false;

        } else {

            $product_cart_id = WC()->cart->generate_cart_id( $product_id );
            $in_cart = WC()->cart->find_product_in_cart( $product_cart_id );

            return $in_cart;

        }

    }

}

new NAM_Cart();
