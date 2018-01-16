<?php

class NAM_Membership_Tier extends NAM_Custom_Post_Type {

    public static $slug = 'membership-tier';

    public static $singular_name = 'Membership Tier';

    public static $plural_name = 'Membership Tiers';

    protected static $options = array(
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


    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {
        parent::register( NAM_Membership_Tier::$slug, NAM_Membership_Tier::$singular_name, NAM_Membership_Tier::$plural_name, NAM_Membership_Tier::$options );
    }

}

?>
