<?php

class NAM_Site_Admin {

    public function __construct() {

        add_action('admin_menu', array( $this, 'manage_admin_menu_options' ) );
        add_action('acf/init', array($this, 'add_options_pages'));
        add_action('current_screen', array( 'NAM_Event', 'register_validation_hooks' ));
        add_action( 'admin_head', array( $this, 'admin_css'));

        add_action('wp_dashboard_setup', array($this, 'remove_dashboard_widgets') );
        add_action('tiny_mce_before_init', array($this, 'format_TinyMCE') );
        add_action('wp_dashboard_setup', array($this, 'remove_admin_bar_items'));

        add_action('rest_api_init', array( $this, 'register_get_cpt_for_product_id' ));

        add_filter( 'get_user_metadata', array( $this, 'pages_per_page_wpse_23503'), 10, 4 );

        add_action( 'admin_enqueue_scripts', array( $this, 'register_customer_list_scripts' ) );

        add_filter( 'the_content', array( 'NAM_Classes', 'rewrite_customer_list_headings' ), 99 );

    }

    /**
     * the customer_list plugin requests a specific set of external scripts
     * and styles enqueued to work properly. This routine enqueues the scripts
     * if a shadowed product page is present.
     */
    public function register_customer_list_scripts() {

        $current_screen = get_current_screen();
        $post_type = $current_screen->post_type;
        $active_post_types = array_map( function( $cls ) { return $cls::$slug; }, NAM_Site::$product_post_types );

        if ( is_plugin_active('wc-product-customer-list-premium/wc-product-customer-list.php') && in_array( $post_type, $active_post_types ) ) {

    		// Register styles
    		wp_register_style( 'wpcl-datatables-css', 'https://cdn.datatables.net/t/dt/dt-1.10.11,r-2.0.2/datatables.min.css', false, '1.10.11' );
    		wp_register_style( 'wpcl-datatables-buttons-css', 'https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css', false, '1.2.2' );
    		wp_register_style( 'wpcl-datatables-select-css', 'https://cdn.datatables.net/select/1.2.2/css/select.dataTables.min.css', false, '1.0' );

    		// Register scripts
    		wp_register_script( 'wpcl-datatables-js', 'https://cdn.datatables.net/t/dt/dt-1.10.11,r-2.0.2/datatables.min.js', true, '2.0.2' );
    		wp_register_script( 'wpcl-datatables-buttons-js', 'https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js', true, '1.2.2' );
    		wp_register_script( 'wpcl-datatables-buttons-flash', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js', true, '1.2.2' );
    		wp_register_script( 'wpcl-datatables-print', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js', true, '1.2.2' );
    		wp_register_script( 'wpcl-datatables-jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js', true, '2.5.0' );
    		wp_register_script( 'wpcl-datatables-pdfmake', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js', true, '0.1.36' );
    		wp_register_script( 'wpcl-datatables-vfs-fonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.20/vfs_fonts.js', true, '0.1.20' );
    		wp_register_script( 'wpcl-datatables-buttons-html', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js', true, '1.2.2' );
    		wp_register_script( 'wpcl-datatables-buttons-print', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js', true, '1.2.2' );

    		wp_register_script( 'wpcl-script-shortcode', home_url() . '/wp-content/plugins/wc-product-customer-list-premium/admin/assets/shortcode.js', true, '1.0' );

        }

        $main_js = '/bundles/bundle.js';

        $compiled_resources_dir = get_template_directory();
        $compiled_resources_uri = get_template_directory_uri();

        $main_js_ver = filemtime( $compiled_resources_dir . $main_js ); // version suffixes for cache-busting.

        wp_enqueue_script('admin-js', $compiled_resources_uri . $main_js, array(), $main_js_ver, true);
    }


    /**
     * This function manages visibility of different parts of the Admin view.
     */
    public function manage_admin_menu_options() {

        global $submenu;

        remove_meta_box("dashboard_primary", "dashboard", "side");   // WordPress.com blog
        remove_meta_box("dashboard_secondary", "dashboard", "side"); // Other WordPress news

        remove_post_type_support('post', 'comments');
        remove_post_type_support('page', 'comments');

        remove_menu_page('index.php');  // Remove the dashboard link from the Wordpress sidebar.
        remove_menu_page('edit.php');   // Remove the posts link from the Wordpress sidebar.
        remove_menu_page('edit-comments.php');   // Remove the comments link from the Wordpress sidebar.

        if ( !current_user_can( 'administrator' ) ) {
            remove_menu_page('admin.php?page=wc-settings'); // Remove WC Configuration Settings
            remove_menu_page('admin.php?page=gf_edit_forms'); // Remove Gravity Forms Edit Page

            if ( isset( $submenu['themes.php']) ) {
                foreach ($submenu['themes.php'] as $key => $menu_item ) {
                    if ( in_array('Customize', $menu_item ) ) {
                        unset( $submenu['themes.php'][$key] );
                    }
                    if ( in_array('Themes', $menu_item ) ) {
                        unset( $submenu['themes.php'][$key] );
                    }
                }
            }

        }

    }

    /**
     * Additional ACF options pages can be registered here.
     */
    public function add_options_pages() {
        if ( function_exists('acf_add_options_page') ) {
            acf_add_options_page(array(
                'page_title'    => 'Site Options & Menus',
                'menu_title'    => 'Site Options & Menus',
                'position'      => '50.1',
            ));
            acf_add_options_page(array(
                'page_title'    => 'Ecommerce Content',
                'menu_title'    => 'Ecommerce Content',
                'icon_url'      => 'dashicons-cart',
                'position'      => '50.3',
            ));

            $user = wp_get_current_user();
            $roles = (array) $user->roles;

            //if ( $roles[0] == 'administrator' ) {

                acf_add_options_page(array(
                    'page_title'    => 'Account Creator',
                    'menu_title'    => 'Account Creator',
                    'icon_url'      => 'dashicons-id',
                    'position'      => '50.5',
                ));

                acf_add_options_page(array(
                    'page_title'    => 'Account Importer',
                    'menu_title'    => 'Account Importer',
                    'icon_url'      => 'dashicons-id',
                    'position'      => '50.6',
                ));

            //}

        }
    }

    public function pages_per_page_wpse_23503( $check, $object_id, $meta_key, $single ) {
        if( 'edit_page_per_page' == $meta_key )
            return 100;
        return $check;
    }

    public function admin_css( ) {
        wp_enqueue_style( 'admin_css', get_template_directory_uri() . '/bundles/admin-bundle.css' );
    }


    /**
     * Removes comments icon from the admin bar.
     */
    public function remove_admin_bar_items() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu("comments");
    }

    /**
     * remove admin menu home page widgets
     */
    public function remove_dashboard_widgets() {
        remove_meta_box("dashboard_primary", "dashboard", "side");   // WordPress.com blog
        remove_meta_box("dashboard_secondary", "dashboard", "side"); // Other WordPress news

        global $wp_meta_boxes;

        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    }

    public function format_TinyMCE( $in ) {
        $in['remove_linebreaks'] = false;
        $in['gecko_spellcheck'] = false;
        $in['keep_styles'] = true;
        $in['accessibility_focus'] = true;
        $in['tabfocus_elements'] = 'major-publishing-actions';
        $in['media_strict'] = false;
        $in['paste_remove_styles'] = false;
        $in['paste_remove_spans'] = false;
        $in['paste_strip_class_attributes'] = 'none';
        $in['paste_text_use_dialog'] = true;
        $in['wpeditimage_disable_captions'] = true;
        //$in['plugins'] = 'tabfocus,paste,media,fullscreen,wordpress,wpeditimage,wpgallery,wplink,wpdialogs,wpfullscreen';
        //$in['content_css'] = get_template_directory_uri() . "/bundles/admin-tinymce-bundle.css";
        $in['wpautop'] = true;
        $in['apply_source_formatting'] = false;
        $in['block_formats'] = "Paragraph=p; Heading 3=h3; Heading 4=h4; Heading 5=h5";
        $in['toolbar1'] = 'formatselect,bold,italic,underline,bullist,numlist,link,unlink';
        $in['toolbar2'] = '';
        $in['toolbar3'] = '';
        $in['toolbar4'] = '';
        return $in;
    }

    /**
     * Register a custom rest endpoint that handles
     * a request for a parent CPT given the id of
     * its shadowing product.
     */
    public function register_get_cpt_for_product_id() {
        register_rest_route('nam/v1', 'parent/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => array( $this, 'get_cpt_for_product_id' ),
            'args' => array(
                'id' => array(
                    'validate_callback' => function( $param, $request, $key ) {
                        return is_numeric( $param );
                    },
                    'sanitize_callback' => 'absint'
                )
            )
        ));
    }

    /**
     *
     *
     */
    public function get_cpt_for_product_id( $request ) {

        $requested_id = $request->get_param('id');
        $maybe_parent = NAM_Shadowed_Post_Type::get_parent_posts_for_all_post_types( $requested_id );

        if ( empty( $maybe_parent ) ) {

            return array(
                'success' => FALSE,
                'warning' => FALSE,
                'message' => 'No parent Custom Post Types found for this ID: ' . $requested_id . '.',
                'product_id' => $requested_id
            );

        } else if ( count( $maybe_parent ) == 1 ) {

            return array(
                'success' => TRUE,
                'warning' => FALSE,
                'parent_id' => $maybe_parent[0],
                'product_id' => $requested_id,
                'message' => ''
            );

        } else {

            return array(
                'success' => TRUE,
                'warning' => TRUE,
                'parent_id' => $maybe_parent[0],
                'other_ids' => array_slice( $maybe_parent, 1 ),
                'product_id' => $requested_id,
                'message' => 'Multiple parent Custom Post Types were found for ID ' . $requested_id . '. This could indicate a data integrity error for this product.'
            );

        }

    }

}

?>
