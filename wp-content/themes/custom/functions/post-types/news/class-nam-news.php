<?php

class NAM_News extends NAM_Custom_Post_Type {

    public static $slug = 'news';

    public static $singular_name = 'News Story';

    public static $plural_name = 'News';

    public static $post_options = array(
        'menu_icon'                 => 'dashicons-media-text',
        'hierarchical'              => false,
        'has_archive'               => true,
        'menu_position'             => 5,
        'supports'                  => array(
                                        'title',
                                        'editor',
                                        'thumbnail',
                                        'revisions'
                                    ),
        'rewrite'                   => array(
                                        'slug' => 'news',
                                        'with_front' => false,
                                        'feeds' => true,
                                        'pages' => true
                                    ),
        'taxonomies'                => array(  )

    );

    public static $query_options = array(

    );

    /**
     * ==== Instance Members and Methods ====
     */
    public function __construct( $id ) {

        $this->id = $id;

    }

    public function validate() {

    }

    public function create() {

    }

}

?>
