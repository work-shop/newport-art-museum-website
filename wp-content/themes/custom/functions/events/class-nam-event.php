<?php

class NAM_Event {

    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {

        if ( function_exists( 'register_post_type' ) ) {
            register_post_type(
                'events',
                array(
                    'labels' => array(
                        'name'                          => 'Events',
                        'singular_name'                 => 'Event',
                        'add_new'                       => 'Add New',
                        'add_new_item'                  => 'Add New Event',
                        'edit_item'                     => 'Edit Event',
                        'new_item'                      => 'New Event',
                        'view_item'                     => 'View Event',
                        'view_items'                    => 'View Events',
                        'search_items'                  => 'Search Events',
                        'not_found'                     => 'No Events found',
                        'not_found_in_trash'            => 'No Events found in the trash',
                        'parent_item_colon'             => 'Parent Event:',
                        'all_items'                     => 'All Events',
                        'archives'                      => 'Event List',
                        'attributes'                    => 'Event Attributes',
                        'insert_into_item'              => 'Insert into Event',
                        'uploaded_to_this_item'         => 'Uploaded to this Event',
                        'featured_image'                => 'Event Image',
                        'set_featured_image'            => 'Set Event Image',
                        'remove_featured_image'         => 'Remove Event Image',
                        'use_featured_image'            => 'Use as Event Image',
                        'menu_name'                     => 'Events' // Default
                        /*
                        'filter_items_list'             => '',
                        'items_list_navigation'         => '',
                        'items_list'                    => '',
                        'name_admin_bar'                => ''
                         */

                    ),
                    'public' => true,
                    'menu_position' => 3, // Before Posts Divider
                    'menu_icon' => 'dashicons-calendar-alt',
                    'capabilities' => array('event', 'events'),
                    'hierarchical' => false,
                    'supports' => array('title', 'thumbnail', 'revisions'),
                    'taxonomies' => array( 'group' ),
                    'has_archive' => true,
                    'rewrite' => array(
                        'slug' => 'events',
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
