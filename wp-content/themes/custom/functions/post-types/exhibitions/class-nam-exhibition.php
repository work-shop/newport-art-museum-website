<?php

class NAM_Exhibition extends NAM_Custom_Post_Type {

    public static $slug = 'exhibitions';

    public static $singular_name = 'Exhibition';

    public static $plural_name = 'Exhibitions';

    public static $post_options = array(
        'menu_icon'                 => 'dashicons-images-alt',
        'hierarchical'              => false,
        'has_archive'               => false,
        'menu_position'             => 3,
        'supports'                  => array(
                                        'title',
                                        'thumbnail',
                                        'revisions'
                                    ),
        'rewrite'                   => array(
                                        'slug' => 'exhibitions',
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
