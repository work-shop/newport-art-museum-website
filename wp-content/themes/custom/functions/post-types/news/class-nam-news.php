<?php

class NAM_News extends NAM_Custom_Post_Type {

    public static $slug = 'news';

    public static $singular_name = 'News Story';

    public static $plural_name = 'News';

    protected static $options = array(
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


    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {
        parent::register( NAM_News::$slug, NAM_News::$singular_name, NAM_News::$plural_name, NAM_News::$options );
    }

}

?>
