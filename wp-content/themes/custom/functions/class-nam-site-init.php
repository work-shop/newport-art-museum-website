<?php


class NAM_Site {

    public function __construct() {

        add_action('init', array( $this, 'register_image_sizing') );
        add_action('init', array( $this, 'register_theme_support') );
        add_action('init', array( $this, 'register_post_types_and_taxonomies' ) );

        add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );

        add_filter('show_admin_bar', '__return_false');

        new WS_CDN_Url();

    }


    public function register_post_types_and_taxonomies() {

        NAM_Group::register();
        //NAM_News_Category::register();//leaving out news categories for initial round of development
        NAM_Events_Category::register();
        NAM_Classes_Category::register();
        NAM_Exhibitions_Category::register();

        NAM_Shop_Product::register();
        NAM_Membership_Tier::register();
        NAM_Donation_Tier::register();
        NAM_Exhibition::register();
        NAM_Event::register();
        NAM_Class::register();
        NAM_News::register();

    }

    public function register_image_sizing() {
        if ( function_exists( 'add_image_size' ) ) {

            add_image_size('social_card', 600, 600, array( 'x_crop_position' => 'center', 'y_crop_position' => 'center'));

        }
    }

    public function register_theme_support() {
        if ( function_exists( 'add_theme_support' ) ) {

            add_theme_support( 'menus' );

        }
    }


    public function enqueue_scripts_and_styles() {
        if ( function_exists( 'get_template_directory_uri' ) && function_exists( 'wp_enqueue_style' ) && function_exists( 'wp_enqueue_script' ) ) {

            $main_css = '/styles/bundle.css';
            $main_js = '/scripts/bundle.js';

            $compiled_resources_dir = get_template_directory() . '/compiled';
            $compiled_resources_uri = get_template_directory_uri() . '/compiled';

            $main_css_ver = filemtime( $compiled_resources_dir . $main_css ); // version suffixes for cache-busting.
            $main_js_ver = filemtime( $compiled_resources_dir . $main_css ); // version suffixes for cache-busting.

            wp_enqueue_style('main-css', $compiled_resources_uri . $main_css, array(), $main_css_ver);
            wp_enqueue_script('jquery');
            wp_enqueue_script('main-js', $compiled_resources_uri . $main_js, array('jquery'), $main_js_ver, true);

        }
    }

}

?>
