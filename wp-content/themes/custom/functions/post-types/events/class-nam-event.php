<?php

class NAM_Event extends NAM_Custom_Post_Type {

    public static $slug = 'events';

    public static $singular_name = 'Event';

    public static $plural_name = 'Events';

    public static $post_options = array(
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
