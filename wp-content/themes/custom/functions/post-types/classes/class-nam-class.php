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
     * Register a meta_box for the product
     */
    public static function add_product_meta_box() {
        if ( function_exists( 'add_meta_box' ) ) {

            $called_class = get_called_class();

            add_meta_box(
                'course-registrees',
                'Course Registrees',
                array( $called_class, 'show_purchasers' ),
                static::$slug,
                'normal',
                'low'
            );

        }
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

        $categories = array_map( function( $term ) { return self::$plural_name . ': ' . $term->name; }, wp_get_post_terms( $post_id, 'classes-categories' ) );
        array_push( $categories, self::$plural_name );

        return $categories;

    }


}

?>
