<?php

abstract class NAM_Shadowed_Post_Type extends NAM_Custom_Post_Type {

    public static $field_keys = array(
        // Shadowed Post Field
        'managed_field_related_post' =>     'field_5b687e3ad7e8e',

        // Price Fields
        'price' =>                          'field_5b647341dca78',
        'sale_price' =>                     'field_5b6473c0dca7a',
        'sale_from' =>                      'field_5b64742edca7b',
        'sale_to' =>                        'field_5b647490dca7c',

        // Stock Management Price
        'manage_stock' =>                   'field_5b685c94d33c6',
        'stock_quantity' =>                 'field_5b685cdbd33c7',

        // Discounts
        // Fees and Surcharges
    );


    /**
     * The register static method is used to register the instance post type
     * in WordPress
     */
    public static function register( ) {

        parent::register();

        static::register_shadowed_post_actions();

    }

    /**
     * This routine registers specific actions for this post-type
     * that create and delete shadowed woocommerce posts
     * that implement e-commerce functionality programmatically.
     */
    public static function register_shadowed_post_actions() {
        add_action( 'save_post_' . static::$slug, array( get_called_class(), 'do_product_management_actions' ));
    }

    /**
     * This routine removes specific actions for this post-type
     * that create and delete shadowed woocommerce posts
     * that implement e-commerce functionality programmatically.
     *
     * NOTE: this is used to prevent "save_post" loops from ocurring when woocommerce
     *       objects are created.
     */
    public static function deregister_shadowed_post_actions() {
        remove_action( 'save_post_' . static::$slug, array( get_called_class(), 'do_product_management_actions' ));
    }

    /**
     * This function dispatches calls to create_shadow_post and
     * update_shadow_post depending on the context in which the
     * action is triggered.
     *
     * @param int $post_id  the id of the post being saved.
     */
    public static function do_product_management_actions( $post_id ) {

        static::deregister_shadowed_post_actions();

        $updated_post = get_post( $post_id );
        $shadow_post = get_field( static::$field_keys['managed_field_related_post'], $post_id );

        if ( !$shadow_post ) {

            static::create_shadowing_product( $post_id, $updated_post );

        } else {

            static::update_shadowing_product( $post_id, $updated_post, $shadow_post );

        }

        static::register_shadowed_post_actions();

    }

    /**
     * This function creates a new shadowed woocommerce post when
     * The authoring post is saved in the database, and associates it
     * with the authoring post.
     *
     * @param int $post_id the idea of the post being created.
     * @param WP_Post $post the post object being updated.
     */
    public static function create_shadowing_product( $post_id, $updated_post ) {

        $product_id = wp_insert_post( array(
            'post_title'    => $updated_post->post_title,
            'post_content'  => '',
            'post_status'   => 'publish',
            'post_type'     => 'product',
        ));

        static::set_shadowing_product_object_terms( $updated_post->post_title, $post_id, $product_id );
        static::set_shadowing_product_categories( $updated_post->post_title, $post_id, $product_id );
        static::set_shadowing_product_custom_trackers( $updated_post->post_title, $post_id, $product_id );

        update_field( static::$field_keys['managed_field_related_post'], $product_id, $post_id );

        //throw new Exception('Testing New Product Creation');

    }

    /**
     * This function creates a new shadowed woocommerce post when
     * The authoring post is saved in the database, and associates it
     * with the authoring post.
     *
     * @param int $post_id the idea of the post being created.
     * @param WP_Post $post the post object being updated.
     * @param WP_Post $post the shadowed product object being attached.
     */
    public static function update_shadowing_product( $post_id, $updated_post, $shadowing_post ) {

        static::set_shadowing_product_object_terms( $updated_post->post_title, $post_id, $shadowing_post->ID );
        static::set_shadowing_product_categories( $updated_post->post_title, $post_id, $shadowing_post->ID );
        static::set_shadowing_product_custom_trackers( $updated_post->post_title, $post_id, $shadowing_post->ID );

        //throw new Exception('Testing Product Updating');

    }

    /**
     * This function removes the shadowed post associated with a given
     * post, in the case that it's parent is being deleted.
     *
     * @param int $post_id the idea of the post being created.
     */
    public static function remove_shadowing_product() {

    }

    /**
     * This routine sets all the required object terms on a given shadow product,
     * based on the relevant parameters set in the Sales Information box on the
     * the custom post.
     *
     * @param string title the Title of this post.
     * @param int $post_id the id of the post that owns this custom product
     * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
     */
    public static function set_shadowing_product_object_terms( $title, $post_id, $product_id ) {

        static::set_product_type( $title, $post_id, $product_id );

    }

    /**
     * This routine determines the appropriate product type for this custom post type.
     *
     * @param string title the Title of this post.
     * @param int $post_id the id of the post that owns this custom product
     * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
     */
    public static function set_product_type( $title, $post_id, $product_id ) {

        wp_set_object_terms( $product_id, 'simple', 'product_type' );

        $price = get_field( static::$field_keys['price'], $post_id );
        $sale_price = get_field( static::$field_keys['sale_price'], $post_id );
        $sale_from = get_field( static::$field_keys['sale_from'], $post_id );
        $sale_to = get_field( static::$field_keys['sale_to'], $post_id );
        $manage_stock = get_field( static::$field_keys['manage_stock'], $post_id );
        $stock_quantity = get_field( static::$field_keys['stock_quantity'], $post_id );

        update_post_meta( $product_id, 'total_sales', '0' );
        update_post_meta( $product_id, '_downloadable', 'no' );
        update_post_meta( $product_id, '_virtual', 'yes' ); // NOTE: once shop products are launched, we'll need to make this non-constant

        update_post_meta( $product_id, '_price', (double) $price );
        update_post_meta( $product_id, '_regular_price', (double) $price );

        if ( $sale_price ) {
            update_post_meta( $product_id, '_sale_price', (double) $sale_price );
            update_post_meta( $product_id, '_sale_price_dates_from', $sale_from );
            update_post_meta( $product_id, '_sale_price_dates_to', $sale_to );
        } else {
            update_post_meta( $product_id, '_sale_price', '' );
            update_post_meta( $product_id, '_sale_price_dates_from', '' );
            update_post_meta( $product_id, '_sale_price_dates_to', '' );
        }

        update_post_meta( $product_id, '_purchase_note', '' );
        update_post_meta( $product_id, '_featured', 'no' );
        update_post_meta( $product_id, '_weight', '' );
        update_post_meta( $product_id, '_length', '' );
        update_post_meta( $product_id, '_width', '' );
        update_post_meta( $product_id, '_height', '' );
        update_post_meta( $product_id, '_sku', sanitize_title_with_dashes( $title, '', 'save' ) . '-' . mt_rand() );
        update_post_meta( $product_id, '_product_attributes', array() );

        if ( $manage_stock ) {

            update_post_meta( $product_id, '_manage_stock', 'yes' );
            update_post_meta( $product_id, '_backorders', 'no' );
            update_post_meta( $product_id, '_stock', $stock_quantity );
            update_post_meta( $product_id, '_stock_status', ($stock_quantity > 0) ? 'instock' : 'outofstock' );

        } else {

            update_post_meta( $product_id, '_manage_stock', 'no' );
            update_post_meta( $product_id, '_backorders', 'no' );
            update_post_meta( $product_id, '_stock', '' );
            update_post_meta( $product_id, '_stock_status', 'instock' );

        }

    }

    /**
     * This routine sets all the required product taxonomy terms for reporting
     * purposes.
     *
     * @param int $post_id the id of the post that owns this custom product
     * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
     */
    public static function set_shadowing_product_custom_trackers( $title, $post_id, $product_id ) {

    }

    /**
     * This routine sets all the required product taxonomy terms for reporting
     * purposes.
     *
     * @param int $post_id the id of the post that owns this custom product
     * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
     */
    public abstract static function set_shadowing_product_categories( $title, $post_id, $product_id );




}
