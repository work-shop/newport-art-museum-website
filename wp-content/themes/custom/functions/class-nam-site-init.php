<?php


class NAM_Site {

    public static $product_post_types = array( NAM_Event, NAM_Class, NAM_Shop_Product, NAM_Membership_Tier );

    public function __construct() {

        wp_debug_mode();

        add_action('init', array( $this, 'register_image_sizing') );
        add_action('init', array( $this, 'register_theme_support') );
        add_action('init', array( $this, 'register_post_types_and_taxonomies' ) );

        add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );

        add_filter('show_admin_bar', '__return_false');
        add_filter( 'woocommerce_add_to_cart_fragments', array( $this,'woocommerce_header_add_to_cart_fragment' ) );
        add_filter('max_srcset_image_width', create_function('', 'return 1;'));


        new WS_CDN_Url();

    }

    /**
     * Show cart contents / total Ajax
     */

    public function woocommerce_header_add_to_cart_fragment( $fragments ) {
        global $woocommerce;

        ob_start();

        ?>
        <a class="cart-customlocation" title="View Your Shopping Cart" href="<?php echo wc_get_cart_url(); ?>">
            <span class="icon" data-icon="i"></span>
            <span id="cart-number"><?php echo WC()->cart->get_cart_contents_count(); ?>
        </a>
        <?php
        $fragments['a.cart-customlocation'] = ob_get_clean();
        return $fragments;
    }

    public static function get_page_type() {

    }

    public function register_post_types_and_taxonomies() {

        NAM_Group::register();
        //NAM_News_Category::register();//leaving out news categories for initial round of development
        NAM_Events_Category::register();
        NAM_Classes_Category::register();
        NAM_Classes_Days::register();
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

            add_image_size('acf_preview', 300, 300, false);
            add_image_size('page_hero', 1440, 660, false);
            add_image_size('home_hero', 1920, 1200, false);
            add_image_size('card_wide', 1162, 538, true);
            add_image_size('card_medium', 500, 300, true);

        }
    }

    public function register_theme_support() {
        if ( function_exists( 'add_theme_support' ) ) {

            add_theme_support( 'menus' );
            add_theme_support( 'woocommerce' );

        }
    }


    public function enqueue_scripts_and_styles() {
        if ( function_exists( 'get_template_directory_uri' ) && function_exists( 'wp_enqueue_style' ) && function_exists( 'wp_enqueue_script' ) ) {

            $main_css = '/bundles/bundle.css';
            $main_js = '/bundles/bundle.js';

            $compiled_resources_dir = get_template_directory();
            $compiled_resources_uri = get_template_directory_uri();

            $main_css_ver = filemtime( $compiled_resources_dir . $main_css ); // version suffixes for cache-busting.
            $main_js_ver = filemtime( $compiled_resources_dir . $main_css ); // version suffixes for cache-busting.

            wp_enqueue_style('main-css', $compiled_resources_uri . $main_css, array(), null);
            wp_enqueue_script('jquery');
            wp_enqueue_script('main-js', $compiled_resources_uri . $main_js, array('jquery'), $main_js_ver, true);

            // if (!file_exists( dirname( __FILE__ ) . '/env_prod' )){
            //     wp_register_script( 'cssrefresh', get_template_directory_uri() . '/scripts/cssrefresh.js');
            //     wp_enqueue_script( 'cssrefresh' );
            // }

        }
    }



}

?>
