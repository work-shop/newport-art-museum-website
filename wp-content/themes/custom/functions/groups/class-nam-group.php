<?php

class NAM_Group {

    public static function register() {

        if ( function_exists('register_taxonomy') ) {
            register_taxonomy(
                'group',
                'user',
                array(
                    'labels' => array(
                        'name'                              => 'Groups',
                        'singular_name'                     => 'Group',
                        'menu_name'                         => 'Groups',
                        'all_items'                         => 'All Groups',
                        'edit_item'                         => 'Edit Group',
                        'view_item'                         => 'View Group',
                        'update_item'                       => 'Update Group',
                        'add_new_item'                      => 'Add New Group',
                        'new_item_name'                     => 'New Group Name',
                        'parent_item'                       => 'Parent Group',
                        'parent_item_colon'                 => 'Parent Group:',
                        'search_items'                      => 'Search Groups',
                        'popular_items'                     => 'Frequently used Groups',
                        'separate_items_with_commas'        => 'Separate Groups with commas',
                        'add_or_remove_items'               => 'Add or Remove Groups',
                        'choose_from_most_used'             => 'Choose from the most frequently used Groups',
                        'not_found'                         => 'No Groups found.'
                    ),
                    'public' => true,
                    'show_in_rest' => true,
                    'show_tag_cloud' => false,
                    'show_in_quick_edit' => false,
                    'show_admin_column' => true,
                    'hierarchical' => true,
                    'capabilities' => array(
                        'manage_terms'                      => 'manage_categories',
                        'edit_terms'                        => 'manage_categories',
                        'delete_terms'                      => 'manage_categories',
                        'assign_terms'                      => 'edit_posts'
                    ),
                    'sort' => true
                )
            );
        }


    }

}

?>
