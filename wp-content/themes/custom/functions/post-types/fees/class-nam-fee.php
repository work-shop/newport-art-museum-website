<?php

class NAM_Fee extends NAM_Shadowed_Post_Type {

    public static $slug = 'fees';

    public static $singular_name = 'Fee';

    public static $plural_name = 'Fees';

    public static $post_options = array(
        'menu_icon'                 => 'dashicons-pressthis',
        'hierarchical'              => false,
        'has_archive'               => false,
        'menu_position'             => 5,
        'supports'                  => array(
                                        'title',
                                        'editor',
                                        'thumbnail',
                                        'revisions'
                                    ),
        'rewrite'                   => array(
                                        'slug' => 'fees',
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

    /**
     * This routine sets all the required product taxonomy terms for reporting
     * purposes.
     *
     * @param int $post_id the id of the post that owns this custom product
     * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
     */
    public static function get_product_categories( $post_id ) {

    }

}

?>
