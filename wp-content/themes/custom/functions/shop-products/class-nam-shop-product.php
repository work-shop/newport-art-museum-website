<?php

class NAM_Shop_Product {

    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {

        if ( function_exists( 'register_post_type' ) ) {
            register_post_type(
                'shop-products',
                array(
                    'labels' => array(
                        'name'                          => 'Shop Products',
                        'singular_name'                 => 'Shop Product',
                        'add_new'                       => 'Add New',
                        'add_new_item'                  => 'Add New Shop Product',
                        'edit_item'                     => 'Edit Shop Product',
                        'new_item'                      => 'New Shop Product',
                        'view_item'                     => 'View Shop Product',
                        'view_items'                    => 'View Shop Products',
                        'search_items'                  => 'Search Shop Products',
                        'not_found'                     => 'No Shop Products found',
                        'not_found_in_trash'            => 'No Shop Products found in the trash',
                        'parent_item_colon'             => 'Parent Shop Product:',
                        'all_items'                     => 'All Shop Products',
                        'archives'                      => 'Shop Product List',
                        'attributes'                    => 'Shop Product Attributes',
                        'insert_into_item'              => 'Insert into Shop Product',
                        'uploaded_to_this_item'         => 'Uploaded to this Shop Product',
                        'featured_image'                => 'Product Image',
                        'set_featured_image'            => 'Set Product Image',
                        'remove_featured_image'         => 'Remove Product Image',
                        'use_featured_image'            => 'Use as Product Image',
                        'menu_name'                     => 'Shop Products' // Default
                        /*
                        'filter_items_list'             => '',
                        'items_list_navigation'         => '',
                        'items_list'                    => '',
                        'name_admin_bar'                => ''
                         */

                    ),
                    'public' => true,
                    'menu_position' => 4, // Before Posts
                    'menu_icon' => 'dashicons-products',
                    'capabilities' => array('shop_product', 'shop_products'),
                    'hierarchical' => false,
                    'supports' => array('title', 'thumbnail', 'revisions', 'page-attributes'),
                    'taxonomies' => array( 'group' ),
                    'has_archive' => 'shop',
                    'rewrite' => array(
                        'slug' => 'shop',
                        'with_front' => false,
                        'feeds' => true,
                        'pages' => true
                    ),
                    'query_var' => true,
                    'can_export' => true,
                    'show_in_rest' => true
                )

            );

        }

    }

}

?>
