<?php

class NAM_Event extends NAM_Shadowed_Post_Type {

    public static $slug = 'events';

    public static $singular_name = 'Event';

    public static $plural_name = 'Events';

    public static $post_options = array(
        'menu_icon'                 => 'dashicons-calendar-alt',
        'hierarchical'              => false,
        'has_archive'               => false,
        'menu_position'             => 3,
        'supports'                  => array(
            'title',
            'thumbnail',
            'revisions'
        ),
        'rewrite'                   => array(
            'slug' => 'events',
            'with_front' => false,
            'feeds' => true,
            'pages' => true
        ),
        'taxonomies'                => array(  )

    );

    public static $query_options = array(

    );

    public static function get_posts( $options=array() ) {
        $today = time();
        return get_posts(array(
            'posts_per_page'        => -1,
            'post_type'             => 'events',
            'meta_key'              => 'event_date',
            'orderby'               => 'meta_value',
            'order'                 => 'ASC',
            'ignore_custom_sort'    => TRUE,
            'meta_query' => array(
                array(
                    'key' => 'event_date',
                    'value' => date('Ymd', strtotime('now')),
                    'type' => 'numeric',
                    'compare' => '>=',
                )
            )
        ));
    }

    /**
     * Register a meta_box for the product
     */
    public static function add_product_meta_box() {
        if ( function_exists( 'add_meta_box' ) ) {

            $called_class = get_called_class();

            add_meta_box(
                'event-registrees',
                'Event Registrees',
                array( $called_class, 'show_purchasers' ),
                static::$slug,
                'normal',
                'low'
            );

        }
    }

    /**
     * ==== Instance Members and Methods ====
     */
    public function __construct( $id ) {

        $this->id = $id;

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

        $categories = array_map( function( $term ) { return self::$plural_name . ': ' . $term->name; }, wp_get_post_terms( $post_id, 'events-categories' ) );
        array_push( $categories, self::$plural_name );

        return $categories;

    }


    public static function register_validation_hooks() {

        $called_class = get_called_class();
        $current_screen = get_current_screen();

        if ( $current_screen->post_type === 'events' ) {
            add_filter('acf/validate_value/key=' . static::$field_keys['ticket_levels'], array( $called_class, 'validate' ), 10, 4);
        }

    }

    /**
     * Check to see if any ticket levels have been entered.
     */
    public static function validate( $valid, $value, $field, $input ) {

        return $value != '';

    }


}

?>
