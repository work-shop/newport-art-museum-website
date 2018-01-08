<?php

class NAM_Membership_Tier {

    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {

        if ( function_exists( 'register_post_type' ) ) {
            register_post_type(
                'membership-tiers',
                array(
                    'labels' => array(
                        'name'                          => 'Membership Tiers',
                        'singular_name'                 => 'Membership Tier',
                        'add_new'                       => 'Add New',
                        'add_new_item'                  => 'Add New Membership Tier',
                        'edit_item'                     => 'Edit Membership Tier',
                        'new_item'                      => 'New Membership Tier',
                        'view_item'                     => 'View Membership Tier',
                        'view_items'                    => 'View Membership Tiers',
                        'search_items'                  => 'Search Membership Tiers',
                        'not_found'                     => 'No Membership Tiers found',
                        'not_found_in_trash'            => 'No Membership Tiers found in the trash',
                        'parent_item_colon'             => 'Parent Membership Tier:',
                        'all_items'                     => 'All Membership Tiers',
                        'archives'                      => 'Membership Tier List',
                        'attributes'                    => 'Membership Tier Attributes',
                        'insert_into_item'              => 'Insert into Membership Tier',
                        'uploaded_to_this_item'         => 'Uploaded to this Membership Tier',
                        'featured_image'                => 'Membership Tier Image',
                        'set_featured_image'            => 'Set Membership Tier Image',
                        'remove_featured_image'         => 'Remove Membership Tier Image',
                        'use_featured_image'            => 'Use as Membership Tier Image',
                        'menu_name'                     => 'Membership Tiers' // Default
                        /*
                        'filter_items_list'             => '',
                        'items_list_navigation'         => '',
                        'items_list'                    => '',
                        'name_admin_bar'                => ''
                         */

                    ),
                    'public' => true,
                    'menu_position' => 3, // Before Posts Divider
                    'menu_icon' => 'dashicons-clipboard',
                    'capabilities' => array('membership_tier', 'membership_tiers'),
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
