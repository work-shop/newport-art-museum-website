<?php

/*
Plugin Name: Product Customer List for WooCommerce
Plugin URI: https://wordpress.org/plugins/wc-product-customer-list/
Description: Displays a list of customers that bought a product on the edit page.
Version: 2.7.8
Author: Kokomo
Author URI: http://www.kokomoweb.com/
Developer: Thierry Lavergne
Developer URI: http://www.kokomoweb.com
Text Domain: wc-product-customer-list
Domain Path: /languages
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
WC requires at least: 3.0.0
WC tested up to: 3.5.0
@fs_premium_only /admin/wpcl-scripts-shortcode.php, /admin/assets/shortcode.js
*/
/**
 * @package WC_Product_Customer_List
 * @version 2.7.6
 */
// Prevent direct access

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}


if ( !function_exists( 'wpcl_activation' ) ) {
    // Create a helper function for easy SDK access.
    function wpcl_activation()
    {
        global  $wpcl_activation ;
        
        if ( !isset( $wpcl_activation ) ) {
            // Include Freemius SDK.
            require_once dirname( __FILE__ ) . '/freemius/start.php';
            $wpcl_activation = fs_dynamic_init( array(
                'id'             => '2009',
                'slug'           => 'wc-product-customer-list',
                'type'           => 'plugin',
                'public_key'     => 'pk_680750999c75010124cc910626309',
                'is_premium'     => false,
                'has_addons'     => false,
                'has_paid_plans' => true,
                'menu'           => array(
                'slug'           => 'wc-settings',
                'override_exact' => true,
                'contact'        => false,
                'support'        => false,
                'parent'         => array(
                'slug' => 'woocommerce',
            ),
                'account'        => false,
                'pricing'        => false,
            ),
                'is_live'        => true,
            ) );
        }
        
        return $wpcl_activation;
    }
    
    // Init Freemius.
    wpcl_activation();
    // Signal that SDK was initiated.
    do_action( 'wpcl_activation_loaded' );
    function wpcl_activation_settings_url()
    {
        return admin_url( 'admin.php?page=wc-settings&tab=products&section=wpcl' );
    }
    
    wpcl_activation()->add_filter( 'connect_url', 'wpcl_activation_settings_url' );
    wpcl_activation()->add_filter( 'after_skip_url', 'wpcl_activation_settings_url' );
    wpcl_activation()->add_filter( 'after_connect_url', 'wpcl_activation_settings_url' );
    wpcl_activation()->add_filter( 'after_pending_connect_url', 'wpcl_activation_settings_url' );
    // Add action links
    
    if ( !function_exists( 'wpcl_action_links' ) ) {
        function wpcl_action_links( $links )
        {
            $actionlinks = array( '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=products&section=wpcl' ) . '">Settings</a>' );
            return array_merge( $links, $actionlinks );
        }
        
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wpcl_action_links' );
    }
    
    // Define plugin path
    define( 'WPCL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
    // Init
    function wpcl_init()
    {
        // Init functions
        require_once WPCL_PLUGIN_PATH . 'functions.php';
        // Check if WooCommerce is activated
        
        if ( class_exists( 'woocommerce' ) ) {
            // Add row action
            require_once WPCL_PLUGIN_PATH . 'admin/wpcl-row-actions.php';
            // Woocommerce Settings
            require_once WPCL_PLUGIN_PATH . 'admin/wpcl-settings.php';
            // Enqueue stylesheets and scripts on post edit page only
            require_once WPCL_PLUGIN_PATH . 'admin/wpcl-scripts.php';
            // Display customer table in product edit page
            
            if ( woocommerce_version_check() ) {
                // 3.0+ customer table
                require_once WPCL_PLUGIN_PATH . 'views/table-customer-list.php';
                // 3.0+ shortcode
                require_once WPCL_PLUGIN_PATH . 'views/shortcodes.php';
            } else {
                // Pre 3.0 customer table
                require_once WPCL_PLUGIN_PATH . 'views/legacy/table-customer-list-2-6.php';
                // Pre 3.0 Shortcode
                require_once WPCL_PLUGIN_PATH . 'views/legacy/shortcodes-2-6.php';
            }
        
        } else {
            // Output error message if Woocommerce is not activated
            add_action( 'admin_notices', 'wpcl_admin_message' );
        }
    
    }
    
    add_action( 'plugins_loaded', 'wpcl_init' );
}
