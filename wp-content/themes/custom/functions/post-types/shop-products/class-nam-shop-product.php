<?php

class NAM_Shop_Product extends NAM_Custom_Post_Type {

    public static $slug = 'shop-products';

    public static $singular_name = 'Shop Product';

    public static $plural_name = 'Shop Products';

    protected static $options = array(
        'menu_icon'                 => 'dashicons-products',
        'hierarchical'              => false,
        'has_archive'               => true,
        'menu_position'             => 4,
        'supports'                  => array(
                                        'title',
                                        'revisions'
                                    ),
        'rewrite'                   => array(
                                        'slug' => 'shop-products',
                                        'with_front' => false,
                                        'feeds' => true,
                                        'pages' => true
                                    ),
        'taxonomies'                => array(  )

    );

    /**
     * Static method that registers the Shop Product custom post type
     * for user management on the back end.
     */
    public static function register() {
        parent::register( NAM_Shop_Product::$slug, NAM_Shop_Product::$singular_name, NAM_Shop_Product::$plural_name, NAM_Shop_Product::$options );
    }



    public function __construct() {

    }

}

?>
