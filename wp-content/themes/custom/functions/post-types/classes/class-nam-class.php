<?php

class NAM_Class extends NAM_Custom_Post_Type {

    /**
     * ==== Static Members and Methods ====
     *
     * These members are used to define static parameters
     * to the new post type.
     */

    public static $slug = 'classes';

    public static $singular_name = 'Class';

    public static $plural_name = 'Classes';

    public static $post_options = array(
        'menu_icon'                 => 'dashicons-welcome-learn-more',
        'hierarchical'              => false,
        'has_archive'               => true,
        'menu_position'             => 3,
        'supports'                  => array(
                                        'title',
                                        'thumbnail',
                                        'revisions'
                                    ),
        'rewrite'                   => array(
                                        'slug' => 'classes',
                                        'with_front' => false,
                                        'feeds' => true,
                                        'pages' => true
                                    ),
        'taxonomies'                => array(  )

    );

    public static $query_options = array(
        'posts_per_page'            => -1,
        'offset'                    => 0,
        'category'                  => '',
        'category_name'             => '',
        'orderby'                   => 'menu_order',
        'suppress_filters'          => false
    );


    /**
     * ==== Instance Members and Methods ====
     */

    public function __construct( $post ) {
        parent::__construct( $post );
    }

    public function validate() {

    }

    public function create() {

    }

    public function draw_card() {

    }


}

?>
