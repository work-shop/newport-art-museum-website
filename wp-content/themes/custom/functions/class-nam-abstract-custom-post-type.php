<?php

abstract class NAM_Custom_Post_Type {

    private static function default_for_key( $key, $options, $default ) {
        return array_key_exists( $key, $options ) ? $options[$key] : $default;
    }

    protected static function register($slug, $singular_name, $plural_name, $options ) {

        if ( function_exists( 'register_post_type' ) ) {
            register_post_type(
                $slug,
                array(
                    'labels' => array(
                        'name'                          => $plural_name,
                        'singular_name'                 => $singular_name,
                        'add_new'                       => 'Add New',
                        'add_new_item'                  => 'Add New ' . $singular_name,
                        'edit_item'                     => 'Edit ' . $singular_name,
                        'new_item'                      => 'New ' . $singular_name,
                        'view_item'                     => 'View ' . $singular_name,
                        'view_items'                    => 'View ' . $plural_name,
                        'search_items'                  => 'Search ' . $plural_name,
                        'not_found'                     => 'No ' . $plural_name . ' found',
                        'not_found_in_trash'            => 'No ' . $plural_name . ' found in the trash',
                        'parent_item_colon'             => 'Parent ' . $singular_name. ':',
                        'all_items'                     => 'All ' . $plural_name,
                        'archives'                      => $singular_name . ' List',
                        'attributes'                    => $singular_name . ' Attributes',
                        'insert_into_item'              => 'Insert into ' . $singular_name,
                        'uploaded_to_this_item'         => 'Uploaded to this ' . $singular_name,
                        'featured_image'                => $singular_name . ' Image',
                        'set_featured_image'            => 'Set ' . $singular_name . 'Image',
                        'remove_featured_image'         => 'Remove ' . $singular_name . ' Image',
                        'use_featured_image'            => 'Use as ' . $singular_name . ' Image',
                        'menu_name'                     => $plural_name // Default
                        /*
                        'filter_items_list'             => '',
                        'items_list_navigation'         => '',
                        'items_list'                    => '',
                        'name_admin_bar'                => ''
                         */

                    ),
                    'public' => true,
                    'menu_position' => NAM_Custom_Post_Type::default_for_key( 'menu_position', $options, 5), // Before Posts Divider
                    'menu_icon' => NAM_Custom_Post_Type::default_for_key( 'menu_icon', $options, 'dashicons-posts'),
                    'capabilities' => array(str_replace(' ', '_', strtolower( $singular_name ) ), str_replace(' ', '_', strtolower( $plural_name )) ),
                    'hierarchical' => NAM_Custom_Post_Type::default_for_key( 'hierarchical', $options, false),
                    'supports' => NAM_Custom_Post_Type::default_for_key( 'supports', $options, array()),
                    'taxonomies' => array_merge( NAM_Custom_Post_Type::default_for_key( 'taxonomy', $options, array()), array('groups') ),
                    'has_archive' => NAM_Custom_Post_Type::default_for_key( 'has_archive', $options, true),
                    'rewrite' => NAM_Custom_Post_Type::default_for_key( 'rewrite', $options, array() ),
                    'query_var' => true,
                    'can_export' => true,
                    'show_in_rest' => true
                )

            );

        }

    }

}




?>
