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

        'fees' =>                           'field_5b7470acd3e33',

        'name_your_price_product' =>        'field_5bd76c14ec5b8',
        'minimum_price' =>                  'field_5bd76f19ec5b9',
        'suggested_price' =>                'field_5bd76f94ec5bb'

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
        add_action( 'acf/save_post', array( get_called_class(), 'do_product_management_actions' ), 20);
        add_action( 'mtphr_post_duplicator_created', array( get_called_class(), 'do_product_management_actions' ), 20 );
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
        remove_action( 'acf/save_post', array( get_called_class(), 'do_product_management_actions' ), 20);
        remove_action( 'mtphr_post_duplicator_created', array( get_called_class(), 'do_product_management_actions' ), 20 );
    }

    /**
     * This function dispatches calls to create_shadow_post and
     * update_shadow_post depending on the context in which the
     * action is triggered.
     *
     * @param int $post_id  the id of the post being saved.
     */
    public static function do_product_management_actions( $post_id ) {

        $post_id = (int) $post_id;

        if ( get_post_type( $post_id ) != static::$slug ) { return; }

        static::deregister_shadowed_post_actions();

        $updated_post = get_post( $post_id );
        $shadow_post = get_field( static::$field_keys['managed_field_related_post'], $post_id );

        if ( !$shadow_post ) {

            static::create_shadowing_product( $post_id, $updated_post );

        } else {

            if ( count( $shadow_post ) > 1 ) {

                throw new Exception( 'Shadowing Post Error – multiple products associated with this post.' );

            } else {

                $parent_posts = static::get_parent_posts( $shadow_post[0]->ID );

                if ( count( $parent_posts ) > 1 ) {

                    delete_field( static::$field_keys['managed_field_related_post'], $post_id );
                    static::create_shadowing_product( $post_id, $updated_post );

                } else {

                    static::update_shadowing_product( $post_id, $updated_post, $shadow_post[0] );

                }

            }

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
    public static function create_shadowing_product( $post_id, $updated_post, $copy=false ) {

        $title_check = ( $copy ) ? ' (Copy From Duplicator)': '';

        $product_id = (int) wp_insert_post( array(
            'post_title'    => $updated_post->post_title . $title_check,
            'post_content'  => '',
            'post_status'   => $updated_post->post_status,
            'post_type'     => 'product',
        ));

        static::set_shadowing_product_object_terms( $updated_post->post_title, $post_id, $product_id );
        static::set_shadowing_product_categories( $updated_post->post_title, $post_id, $product_id );
        static::set_shadowing_product_custom_trackers( $updated_post->post_title, $post_id, $product_id );

        $result = update_field( static::$field_keys['managed_field_related_post'], array( $product_id ), (int) $post_id );

        //throw new Exception('Testing Product Updating');
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

        wp_update_post( array(
            'ID' => $shadowing_post->ID,
            'post_title' => $updated_post->post_title,
            'post_status' => $updated_post->post_status,
            'post_name' => ''
        ) );

        static::set_shadowing_product_object_terms( $updated_post->post_title, $post_id, $shadowing_post->ID );
        static::set_shadowing_product_categories( $updated_post->post_title, $post_id, $shadowing_post->ID );
        static::set_shadowing_product_custom_trackers( $updated_post->post_title, $post_id, $shadowing_post->ID );

        $result = update_field( static::$field_keys['managed_field_related_post'], array( $shadowing_post->ID ), (int) $post_id );

        //throw new Exception('Testing Product Updating');

    }

    /**
     * This function removes the shadowed post associated with a given
     * post, in the case that it's parent is being deleted.
     *
     * NOTE: This function DOES NOT delete the product record associated
     * with the post, it just unlinks the shadowing post.
     *
     * @param int $post_id the id of the post being created.
     */
    public static function remove_shadowing_product( $post_id ) {

        delete_field( static::$field_keys['managed_field_related_post'], (int) $post_id );

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

        static::set_product_meta( $title, $post_id, $product_id );
        static::set_product_type( $title, $post_id, $product_id );

    }

    /**
     * This function dispatches particular configuration actions based on
     * The type of product being added. If the product has registered fees,
     * This routine creates a bundled product which wraps the fees with
     * the specified base product. Otherwise, the product is set to simple.
     *
     * @param string title the Title of this post.
     * @param int $post_id the id of the post that owns this custom product
     * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
     */
    public static function set_product_type( $title, $post_id, $product_id ) {

        $fees = static::get_product_fees( $post_id );

        if ( $fees ) {

            wp_set_object_terms( $product_id, 'bundle', 'product_type' );

            $product = new WC_Product_Bundle( $product_id );

            static::set_product_fees( $product, $fees );

        } else if ( static::$slug == 'membership-tier' ) {

            wp_set_object_terms( $product_id, 'subscription', 'product_type' );

            static::set_subscription_meta( $post_id, $product_id );

        } else {

            wp_set_object_terms( $product_id, 'simple', 'product_type' );

        }

    }

/**
 */

    /**
     * This routine constructs the bundle items for a given bundled product.
     * @see This routine is build off of `WC_PB_Meta_Box_Product_Data::process_bundle_data`
     *
     * @param WC_Product $product the product to bundle.
     * @param array $fees an array of wp_post
     */
    public static function set_product_fees( $product, $fees ) {

        $data_items = array_map( function( $fee, $i ) {

            return array(
                'product_id'    => $fee->get_id(),
                'meta_data'     => array(
                    'priced_individually'       => 'yes',
                    'shipped_individually'      => 'no',
                    'quantity_min'              => 1,
                    'quantity_max'              => 1,
                    'order_visibility'          => 'visible',
                    'discount'                  => 0,
                    'order_price_visibility'    => 'visible',
                    'override_title'            => 'no'
                )
            );

        }, $fees, array_keys( $fees ));

        $product->set_bundled_data_items( $data_items );
        $product->save();

    }


    /**
     * Given a
     *
     *
     */
    public static function set_subscription_meta( $post_id, $product_id ) {

        $price = get_field( static::$field_keys['price'], $post_id );

        update_post_meta( $product_id, '_subscription_price', (double) $price );
        update_post_meta( $product_id, '_subscription_sign_up_fee', 0 );
        update_post_meta( $product_id, '_subscription_period', 'year' );
        update_post_meta( $product_id, '_subscription_period_interval', 1 );
        update_post_meta( $product_id, '_subscription_length', 0 );
        update_post_meta( $product_id, '_subscription_trial_period', '' );
        update_post_meta( $product_id, '_subscription_limit', 'no' );
        update_post_meta( $product_id, '_subscription_one_time_shipping', 'no' );

    }

    /**
     * Given a master post id, get the set of Woocommerce Products
     * associated with that post.
     *
     * @param int $post_id the id of the master CPT to get woocommerce fees for.
     * @return array || boolean false if the object has no fees, array of WC_Products representing fees otherwise.
     */
    public static function get_product_fees( $post_id ) {

        $fees = get_field( static::$field_keys['fees'], $post_id );

        if ( $fees ) {

            $fees = array_map( function( $fee ) {

                $product = get_field( static::$field_keys['managed_field_related_post'], $fee->ID );

                return ( $product[0] ) ? wc_get_product( $product[0]->ID ) : false;

            }, $fees );

            $fees = array_filter( $fees ); // NOTE: No callback, means all elements == false are dropped.

        }

        return ( $fees && count( $fees ) > 0 ) ? $fees : false;

    }

    /**
     * This routine determines the appropriate product type for this custom post type.
     *
     * @param string title the Title of this post.
     * @param int $post_id the id of the post that owns this custom product
     * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
     */
    public static function set_product_meta( $title, $post_id, $product_id ) {



        $price = get_field( static::$field_keys['price'], $post_id );
        $sale_price = get_field( static::$field_keys['sale_price'], $post_id );
        $sale_from = get_field( static::$field_keys['sale_from'], $post_id );
        $sale_to = get_field( static::$field_keys['sale_to'], $post_id );
        $manage_stock = get_field( static::$field_keys['manage_stock'], $post_id );
        $stock_quantity = get_field( static::$field_keys['stock_quantity'], $post_id );
        $name_your_price = get_field( static::$field_keys['name_your_price_product'], $post_id );
        $nyp_minumum_price = get_field( static::$field_keys['minimum_price'], $post_id );
        $nyp_suggested_price = get_field( static::$field_keys['suggested_price'], $post_id );

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

        if ( $name_your_price ) {
            update_post_meta( $product_id, '_nyp', 'yes' );
            update_post_meta( $product_id, '_minimum_price', (double) $nyp_minumum_price );
            update_post_meta( $product_id, '_suggested_price', (double) $nyp_suggested_price );
        } else {
            update_post_meta( $product_id, '_nyp', '' );
            update_post_meta( $product_id, '_minimum_price', '');
            update_post_meta( $product_id, '_suggested_price', '');
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

    public static function get_parent_posts( $product_id ) {
        $parents = get_posts(array(
							'post_type' => static::$slug,
							'meta_query' => array(
								array(
									'key' => 'managed_field_related_post', // name of custom field
									'value' => '"' . $product_id . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
									'compare' => 'LIKE'
								)
							)
						));

        return array_map( function( $p ) { return $p->ID; }, $parents );
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
    public static function set_shadowing_product_categories( $title, $post_id, $product_id ) {

        $existing_categories = get_the_terms( $product_id, 'product_cat' );

        if ( $existing_categories ) {

            $existing_categories = array_map( function( $term ) { return $term->name; }, $existing_categories );

        } else {

            $existing_categories = array();

        }

        $categories = static::get_product_categories( $post_id );

        $categories = array_unique( array_merge( $categories, $existing_categories  ) );

        $term_ids = array();

        foreach( $categories as $category ) {

            if ( $term_object = get_term_by( 'name', $category, 'product_cat' ) ) {

                array_push( $term_ids, (int) $term_object->term_id );

            } else {

                $term = wp_insert_term(
                    $category,
                    'product_cat',
                    array(
                        'description' => '',
                        'slug' => sanitize_title_with_dashes( $category )
                    )
                );

                array_push( $term_ids, (int) $term['term_id'] );

            }

        }

        wp_set_object_terms( $product_id, $term_ids, 'product_cat' );

    }

    /**
     * This routine is implemented by subclasses and is responsible for
     * returning the taxonomies that are relevant to the executing CPT.
     *
     * @param int post_id the post id of the post being processed.
     * @return array an array of category names derived from the categories associated with this Custom Post.
     */
    public static abstract function get_product_categories( $post_id );



}
