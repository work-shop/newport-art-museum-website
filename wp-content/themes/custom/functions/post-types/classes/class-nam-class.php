<?php

class NAM_Class extends NAM_Shadowed_Post_Type {

    /**
     * ==== Static Members and Methods ====
     *
     * These members are used to define static parameters
     * to the new post type.
     */

    public static $slug = 'classes';

    public static $singular_name = 'Class';

    public static $plural_name = 'Classes';

    public static $post_options = array(
        'menu_icon'                 => 'dashicons-welcome-learn-more',
        'hierarchical'              => false,
        'has_archive'               => true,
        'menu_position'             => 3,
        'supports'                  => array(
            'title',
            'thumbnail',
            'revisions'
        ),
        'rewrite'                   => array(
            'slug' => 'classes',
            'with_front' => false,
            'feeds' => true,
            'pages' => true
        ),
        'taxonomies'                => array(  )

    );

    public static $query_options = array(

    );

    public static function get_posts() {
        $today = time();
        return get_posts(array(
            'posts_per_page'    => -1,
            'post_type'         => 'classes',
            'meta_key'          => 'class_start_date',
            'orderby'           => 'meta_value',
            'order'             => 'ASC'
            // 'meta_query' => array(
            //     array(
            //         'key' => 'class_end_date',
            //         'value' => date('Ymd', strtotime('now')),
            //         'type' => 'numeric',
            //         'compare' => '>=',
            //     )
            // )
        ));
    }


    /**
     * ==== Instance Members and Methods ====
     */

    public function __construct( $post ) {
        parent::__construct( $post );
    }

    public function validate() {

    }

    public function create() {

    }

    public function draw_card() {

    }

    /**
     * This routine sets all the required product taxonomy terms for reporting
     * purposes.
     *
     * @param int $post_id the id of the post that owns this custom product
     * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
     */
    public static function set_shadowing_product_categories( $title, $post_id, $product_id ) {

    }


}

?>
