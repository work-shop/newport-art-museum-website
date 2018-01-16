<?php

class NAM_Class extends NAM_Custom_Post_Type {

    public static $slug = 'classes';

    public static $singular_name = 'Class';

    public static $plural_name = 'Classes';

    protected static $options = array(
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


    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {
        parent::register( NAM_Class::$slug, NAM_Class::$singular_name, NAM_Class::$plural_name, NAM_Class::$options );
    }

}

?>
