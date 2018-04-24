<?php

class NAM_Site_Admin {

    public function __construct() {

        add_action('admin_menu', array( $this, 'manage_admin_menu_options' ) );
        add_action('acf/init', array($this, 'add_options_pages'));
        add_action( 'admin_head', array( $this, 'admin_css'));

        add_action('wp_dashboard_setup', array($this, 'remove_dashboard_widgets') );
        add_action('tiny_mce_before_init', array($this, 'format_TinyMCE') );
        add_action('wp_dashboard_setup', array($this, 'remove_admin_bar_items'));

        add_filter( 'get_user_metadata', array( $this, 'pages_per_page_wpse_23503'), 10, 4 );


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
                "page_title" => "Site Options & Menus",
                "capability" => "edit_posts",
                "position" => 10,
                "icon_url" => "dashicons-admin-home"
            ));
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

}

?>
