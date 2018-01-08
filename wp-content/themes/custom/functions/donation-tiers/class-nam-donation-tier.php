<?php

class NAM_Donation_Tier {

    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {

        if ( function_exists( 'register_post_type' ) ) {
            register_post_type(
                'donation-tiers',
                array(
                    'labels' => array(
                        'name'                          => 'Donation Tiers',
                        'singular_name'                 => 'Donation Tier',
                        'add_new'                       => 'Add New',
                        'add_new_item'                  => 'Add New Donation Tier',
                        'edit_item'                     => 'Edit Donation Tier',
                        'new_item'                      => 'New Donation Tier',
                        'view_item'                     => 'View Donation Tier',
                        'view_items'                    => 'View Donation Tiers',
                        'search_items'                  => 'Search Donation Tiers',
                        'not_found'                     => 'No Donation Tiers found',
                        'not_found_in_trash'            => 'No Donation Tiers found in the trash',
                        'parent_item_colon'             => 'Parent Donation Tier:',
                        'all_items'                     => 'All Donation Tiers',
                        'archives'                      => 'Donation Tier List',
                        'attributes'                    => 'Donation Tier Attributes',
                        'insert_into_item'              => 'Insert into Donation Tier',
                        'uploaded_to_this_item'         => 'Uploaded to this Donation Tier',
                        'featured_image'                => 'Donation Tier Image',
                        'set_featured_image'            => 'Set Donation Tier Image',
                        'remove_featured_image'         => 'Remove Donation Tier Image',
                        'use_featured_image'            => 'Use as Donation Tier Image',
                        'menu_name'                     => 'Donation Tiers' // Default
                        /*
                        'filter_items_list'             => '',
                        'items_list_navigation'         => '',
                        'items_list'                    => '',
                        'name_admin_bar'                => ''
                         */

                    ),
                    'public' => true,
                    'menu_position' => 1, // Before Posts Divider
                    'menu_icon' => 'dashicons-book-alt',
                    'capabilities' => array('donation_tier', 'donation_tiers'),
                    'hierarchical' => false,
                    'supports' => array('title', 'revisions'),
                    'taxonomies' => array( 'group' ),
                    'has_archive' => false,
                    'rewrite' => array(
                        'slug' => false,
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
