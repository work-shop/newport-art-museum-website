<?php

class NAM_Donation_Tier extends NAM_Shadowed_Post_Type {

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

    public static function draw_product_meta_box( $post_id ) {

    }

    /**
     * This routine sets all the required product taxonomy terms for reporting
     * purposes.
     *
     * @param int $post_id the id of the post that owns this custom product
     * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
     * @return array category names
     */
    public static function get_product_categories( $post_id ) {

        return array( self::$plural_name );

    }

}

?>
