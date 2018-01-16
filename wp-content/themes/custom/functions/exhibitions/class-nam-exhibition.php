<?php

class NAM_Exhibition extends NAM_Custom_Post_Type {

    public static $slug = 'exhibitions';

    public static $singular_name = 'Exhibition';

    public static $plural_name = 'Exhibitions';

    protected static $options = array(
        'menu_icon'                 => 'dashicons-images-alt',
        'hierarchical'              => false,
        'has_archive'               => true,
        'menu_position'             => 3,
        'supports'                  => array(
                                        'title',
                                        'thumbnail',
                                        'revisions'
                                    ),
        'rewrite'                   => array(
                                        'slug' => 'exhibition',
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
        parent::register( NAM_Exhibition::$slug, NAM_Exhibition::$singular_name, NAM_Exhibition::$plural_name, NAM_Exhibition::$options );
    }

}

?>
