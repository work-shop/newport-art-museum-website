<?php

class NAM_Donation_Tier extends NAM_Custom_Post_Type {

    public static $slug = 'donation-tiers';

    public static $singular_name = 'Donation Tier';

    public static $plural_name = 'Donation Tiers';

    protected static $options = array(
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


    public function __construct() {

    }

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {
        parent::register( NAM_Donation_Tier::$slug, NAM_Donation_Tier::$singular_name, NAM_Donation_Tier::$plural_name, NAM_Donation_Tier::$options );
    }

}

?>
