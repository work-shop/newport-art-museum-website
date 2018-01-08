<?php

class NAM_Exhibition {

    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {

        if ( function_exists( 'register_post_type' ) ) {
            register_post_type(
                'exhibitions',
                array(
                    'labels' => array(
                        'name'                          => 'Exhibitions',
                        'singular_name'                 => 'Exhibition',
                        'add_new'                       => 'Add New',
                        'add_new_item'                  => 'Add New Exhibition',
                        'edit_item'                     => 'Edit Exhibition',
                        'new_item'                      => 'New Exhibition',
                        'view_item'                     => 'View Exhibition',
                        'view_items'                    => 'View Exhibitions',
                        'search_items'                  => 'Search Exhibitions',
                        'not_found'                     => 'No Exhibitions found',
                        'not_found_in_trash'            => 'No Exhibitions found in the trash',
                        'parent_item_colon'             => 'Parent Exhibition:',
                        'all_items'                     => 'All Exhibitions',
                        'archives'                      => 'Exhibition List',
                        'attributes'                    => 'Exhibition Attributes',
                        'insert_into_item'              => 'Insert into Exhibition',
                        'uploaded_to_this_item'         => 'Uploaded to this Exhibition',
                        'featured_image'                => 'Exhibition Image',
                        'set_featured_image'            => 'Set Exhibition Image',
                        'remove_featured_image'         => 'Remove Exhibition Image',
                        'use_featured_image'            => 'Use as Exhibition Image',
                        'menu_name'                     => 'Exhibitions' // Default
                        /*
                        'filter_items_list'             => '',
                        'items_list_navigation'         => '',
                        'items_list'                    => '',
                        'name_admin_bar'                => ''
                         */

                    ),
                    'public' => true,
                    'menu_position' => 3, // Before Posts Divider
                    'menu_icon' => 'dashicons-images-alt',
                    'capabilities' => array('exhibition', 'exhibitions'),
                    'hierarchical' => false,
                    'supports' => array('title', 'thumbnail', 'revisions'),
                    'taxonomies' => array( 'group' ),
                    'has_archive' => true,
                    'rewrite' => array(
                        'slug' => 'exhibition',
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
