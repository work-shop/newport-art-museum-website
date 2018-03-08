<?php

class NAM_Membership_Tier extends NAM_Custom_Post_Type {

    public static $slug = 'membership-tier';

    public static $singular_name = 'Membership Tier';

    public static $plural_name = 'Membership Tiers';

    public static $post_options = array(
        'menu_icon'                 => 'dashicons-clipboard',
        'hierarchical'              => false,
        'has_archive'               => true,
        'menu_position'             => 3,
        'supports'                  => array(
                                        'title',
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
