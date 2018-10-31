<?php

abstract class NAM_Custom_Post_Type {

    public static $archive_page_id;

    public static $archive_template;

    /**
     * A simple helper utility method to check a map for a specific key,
     * and return a default of it's not present.
     */
    private static function default_for_key( $key, $options, $default ) {
        return array_key_exists( $key, $options ) ? $options[$key] : $default;
    }

    /**
     * The register static method is used to register the instance post type
     * in WordPress
     */
    public static function register( ) {

        $called_class = get_called_class();

        if ( function_exists( 'register_post_type' ) ) {
            register_post_type(
                static::$slug,
                array(
                    'labels' => array(
                        'name'                          => static::$plural_name,
                        'singular_name'                 => static::$singular_name,
                        'add_new'                       => 'Add New',
                        'add_new_item'                  => 'Add New ' . static::$singular_name,
                        'edit_item'                     => 'Edit ' . static::$singular_name,
                        'new_item'                      => 'New ' . static::$singular_name,
                        'view_item'                     => 'View ' . static::$singular_name,
                        'view_items'                    => 'View ' . static::$plural_name,
                        'search_items'                  => 'Search ' . static::$plural_name,
                        'not_found'                     => 'No ' . static::$plural_name . ' found',
                        'not_found_in_trash'            => 'No ' . static::$plural_name . ' found in the trash',
                        'parent_item_colon'             => 'Parent ' . static::$singular_name. ':',
                        'all_items'                     => 'All ' . static::$plural_name,
                        'archives'                      => static::$singular_name . ' List',
                        'attributes'                    => static::$singular_name . ' Attributes',
                        'insert_into_item'              => 'Insert into ' . static::$singular_name,
                        'uploaded_to_this_item'         => 'Uploaded to this ' . static::$singular_name,
                        'featured_image'                => static::$singular_name . ' Featured Image',
                        'set_featured_image'            => 'Set Featured Image',
                        'remove_featured_image'         => 'Remove ' . static::$singular_name . ' Image',
                        'use_featured_image'            => 'Use as ' . static::$singular_name . ' Image',
                        'menu_name'                     => static::$plural_name // Default
                        /*
                        'filter_items_list'             => '',
                        'items_list_navigation'         => '',
                        'items_list'                    => '',
                        'name_admin_bar'                => ''
                         */

                    ),
                    'public' => true,
                    'menu_position' => NAM_Custom_Post_Type::default_for_key( 'menu_position', static::$post_options, 5), // Before Posts Divider
                    'menu_icon' => NAM_Custom_Post_Type::default_for_key( 'menu_icon',  static::$post_options, 'dashicons-posts'),
                    // 'capabilities_type' => array(str_replace(' ', '_', strtolower(  static::$singular_name ) ), str_replace(' ', '_', strtolower(  static::$plural_name )) ),
                    'hierarchical' => NAM_Custom_Post_Type::default_for_key( 'hierarchical',  static::$post_options, false),
                    'supports' => NAM_Custom_Post_Type::default_for_key( 'supports', static::$post_options, array()),
                    'taxonomies' => array_merge( NAM_Custom_Post_Type::default_for_key( 'taxonomy',  static::$post_options, array()), array('groups') ),
                    'has_archive' => NAM_Custom_Post_Type::default_for_key( 'has_archive',  static::$post_options, true),
                    'rewrite' => NAM_Custom_Post_Type::default_for_key( 'rewrite',  static::$post_options, array() ),
                    'query_var' => true,
                    'can_export' => true,
                    'show_in_rest' => true
                )

            );

        }

        add_action( 'add_meta_boxes', array( $called_class, 'add_product_meta_box' ) );

    }


    /**
     * This function returns true if the current URL path represents
     * the archive for this post-type.
     *
     * @return bool true iff this is a post-type archive for this post type.
     *
     */
    public static function is_archive() {
        return ((get_post_type() == static::$slug) && is_archive()) || get_the_ID() == static::get_archive_page_id();
    }


    /**
     * This function returns true if the current URL path represents
     * a single page for this post-type.
     *
     * @return bool true iff this is a post-type single for this post type.
     *
     */
    public static function is_single() {
        return (get_post_type() == static::$slug) && is_single();
    }


    /**
     * This function returns the path to the archive template for this post type.
     *
     * @return string the path to the archive template for this post type
     */
    public static function archive_template() {
        return 'page-' . static::$slug . '.php';
    }


    /**
     * This function returns the path to the single template for this post type.
     *
     * @return string the path to the single template for this post type
     */
    public static function single_template() {
        return 'single-' . static::$slug . '.php';
    }

    /**
     * This routine is hooked to 'the_post', and overrides
     * setup post-data on product archive templates, replacing
     * the typical archive page loop with the data for the page
     * that holds the archive information for this custom post type.
     *
     * @hooked the_post
     */
    public static function setup_archive_query_proxy() {
        if ( static::is_archive() ) { static::setup_wp_query_archive_page(); }
    }


    /**
     * Returns the page of the archive
     *
     */
    public static function get_archive_page_id() {
        return NAM_Custom_Post_Type_Archive_Mapping::$archive_page_ids[ static::$slug ];
    }

    /**
     * This function sets up the post with the correct page-id of
     * The wordpress backend page that proxies content for this archive page.
     *
     * @return Boolean always true
     *
     */
    public static function setup_wp_query_archive_page() {
        global $post, $wp_query;

        $id = static::get_archive_page_id();

        $wp_query = new WP_Query();
        $wp_query->query( array( 'page_id' => $id, 'post_type' => 'any' ) );
        $post = get_post( $id );

        setup_postdata( $post );

    }

    /**
     * This static method retrieves a set of posts for the child's post-type.
     */
    public static function get_posts( $options = array() ) {
        if ( function_exists('get_posts') ) {

            $called_class = get_called_class();
            $opts = array_merge( static::$query_options, $options, array( 'post_type' => static::$slug ) );

            foreach ( ($posts = get_posts( $opts )) as $key => $value ) {
                $posts[ $key ] = new $called_class( $value );
            }

            return $posts;

        } else {

            return array();

        }
    }

    /**
     * This function draw custom metaboxes for this particular post.
     */
    public static function add_product_meta_box( $post_id ) {
        return false;
    }


    /**
     * This function generates a table of the customers
     * who purcased a given product on the backend.
     *
     * @see Plugin: Woocommerce Product Customer List
     */
    function show_purchasers() {
        global $post;
        setup_postdata( $post );

        $shadowing_product = get_field( 'managed_field_related_post', $post->ID );

        if ( is_plugin_active('wc-product-customer-list/wc-product-customer-list.php') ) {
            if ( $shadowing_product ) {

                $product_id = $shadowing_product[0]->ID;

                /**
                 * This shortcode echos a list of purchasers of a given product
                 * to the page, and depends on the Woocommerce Customer List Plugin.
                 *
                 * Details of shortcode parameters [here](https://wordpress.org/plugins/wc-product-customer-list/#description).
                 */

                $shortcode = '[customer_list product="' . $product_id . '" ' .
                                             'order_status="wc-completed,wc-processing" ' .
                                             'show_titles="true" ' .
                                             'billing_email="true" ' .
                                             'billing_phone="true" ' .
                                             'customer_username_link="true" ' .
                                             'order_number="true" ' .
                                             'scrollx="true" ' .
                                             'order_date="true" ' .
                                             'order_variations="false" ' .
                                             'order_qty_total="true" ' .
                                             ']';

                echo do_shortcode( $shortcode );


            } else {

                echo '<p>Looks like this item doesn\'t have a product attached to it!';

            }
        } else {

            echo '<p>Looks like the <strong>Woocommerce Product Customer List</strong> plugin isn\'t active.</p><p>Activate it in the plugins section.</p>';

        }

        wp_reset_postdata();

    }


    /**
     * ==== Instance Members and Methods ====
     */

    protected $post;

    public function __construct( $post ) {
        $this->post = $post;
    }

    public abstract function validate();

    public abstract function create();

    // public abstract function render_card();
    //
    // public abstract function render_page();

}




?>
