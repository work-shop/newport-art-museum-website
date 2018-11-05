<?php

/**
 * This class encapsulates functionality related
 * to managing classes, and imposing constraints on
 * the classes checkout process.
 */
class NAM_Classes {

    public static $classes_category_slug = 'classes';

    /**
     * Adds custom meta keys or settings for WooCommerce products
     * that shadow "classes" custom post types.
     *
     * @param int $post_id the id of the parent post.
     * @param int $product_id the id of the shadowing product.
     */
    public static function do_class_creation_meta( $post_id, $product_id ) {

        if ( get_post_type( $post_id ) != NAM_Class::$slug ) { return; }

        update_post_meta( $product_id, '_sold_individually', 'yes');

    }

    /**
     * This routine checks the current user's cart to see if there's
     * A class product in their cart. If there is a class in
     * their cart, it returns an array with the class ID in it.
     * If not, it returns false.
     *
     * @return bool||WC_Product returns the class product in the cart if there is one, or false.
     *
     */
    public static function has_class_in_cart( ) {

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

            $product = $cart_item['data'];

            if ( has_term( static::$classes_category_slug, 'product_cat', $product->id ) ) {
                return $product;
            }

        }

        return false;

    }

}

?>
