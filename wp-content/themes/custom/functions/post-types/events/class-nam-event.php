<?php

class NAM_Event extends NAM_Custom_Post_Type {

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

    public static function get_posts() {
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
