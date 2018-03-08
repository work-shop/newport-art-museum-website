<?php

class NAM_Donation_Tier extends NAM_Custom_Post_Type {

    public static $slug = 'donation-tiers';

    public static $singular_name = 'Donation Tier';

    public static $plural_name = 'Donation Tiers';

    public static $post_options = array(
        'menu_icon'                 => 'dashicons-book-alt',
        'hierarchical'              => false,
        'has_archive'               => false,
        'menu_position'             => 1,
        'supports'                  => array(
                                        'title',
                                        'thumbnail',
                                        'revisions'
                                    ),
        'rewrite'                   => array(
                                        'slug' => false,
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
