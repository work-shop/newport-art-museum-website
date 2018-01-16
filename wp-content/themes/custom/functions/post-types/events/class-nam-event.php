<?php

class NAM_Event extends NAM_Custom_Post_Type {

    public static $slug = 'events';

    public static $singular_name = 'Event';

    public static $plural_name = 'Events';

    protected static $options = array(
        'menu_icon'                 => 'dashicons-calendar-alt',
        'hierarchical'              => false,
        'has_archive'               => true,
        'menu_position'             => 3,
        'supports'                  => array(
                                        'title',
                                        'thumbnail',
                                        'revisions'
                                    ),
        'rewrite'                   => array(
                                        'slug' => 'events',
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
        parent::register( NAM_Event::$slug, NAM_Event::$singular_name, NAM_Event::$plural_name, NAM_Event::$options );
    }

}

?>
