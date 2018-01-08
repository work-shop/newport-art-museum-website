<?php

class NAM_Class {

    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {

        if ( function_exists( 'register_post_type' ) ) {
            register_post_type(
                'classes',
                array(
                    'labels' => array(
                        'name'                          => 'Classes',
                        'singular_name'                 => 'Classes',
                        'add_new'                       => 'Add New',
                        'add_new_item'                  => 'Add New Class',
                        'edit_item'                     => 'Edit Class',
                        'new_item'                      => 'New Class',
                        'view_item'                     => 'View Class',
                        'view_items'                    => 'View Classes',
                        'search_items'                  => 'Search Classes',
                        'not_found'                     => 'No Classes found',
                        'not_found_in_trash'            => 'No Classes found in the trash',
                        'parent_item_colon'             => 'Parent Class:',
                        'all_items'                     => 'All Classes',
                        'archives'                      => 'Class List',
                        'attributes'                    => 'Class Attributes',
                        'insert_into_item'              => 'Insert into Class',
                        'uploaded_to_this_item'         => 'Uploaded to this Class',
                        'featured_image'                => 'Class Image',
                        'set_featured_image'            => 'Set Class Image',
                        'remove_featured_image'         => 'Remove Class Image',
                        'use_featured_image'            => 'Use as Class Image',
                        'menu_name'                     => 'Classes' // Default
                        /*
                        'filter_items_list'             => '',
                        'items_list_navigation'         => '',
                        'items_list'                    => '',
                        'name_admin_bar'                => ''
                        */

                    ),
                    'public' => true,
                    'menu_position' => 3, // Before Posts Divider
                    'menu_icon' => 'dashicons-welcome-learn-more',
                    'capabilities' => array('class', 'classes'),
                    'hierarchical' => false,
                    'supports' => array('title', 'thumbnail', 'revisions'),
                    'taxonomies' => array( 'group' ),
                    'has_archive' => true,
                    'rewrite' => array(
                        'slug' => 'classes',
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
