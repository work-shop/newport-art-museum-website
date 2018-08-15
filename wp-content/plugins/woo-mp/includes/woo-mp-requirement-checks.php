<?php

/**
 * This file must maintain compatibility with:
 * 
 * PHP: 5.2.4+
 * WordPress: 3.1.0+
 */

defined( 'ABSPATH' ) || die;

/**
 * Handle requirements.
 */
class Woo_MP_Requirement_Checks {

    /**
     * Minimum required version of PHP.
     * 
     * @var string
     */
    const PHP_MIN_REQUIRED = '5.5.0';

    /**
     * Minimum required version of WordPress.
     * 
     * @var string
     */
    const WORDPRESS_MIN_REQUIRED = '4.4.0';

    /**
     * Minimum required version of WooCommerce.
     * 
     * @var string
     */
    const WOOCOMMERCE_MIN_REQUIRED = '2.6.0';

    /**
     * A message explaining which requirement was not met.
     * 
     * @var string
     */
    private static $message = '';

    /**
     * Run requirement verification routines.
     * 
     * @return bool TRUE if all requirements are met, FALSE otherwise.
     */
    public static function run() {
        if ( version_compare( PHP_VERSION, self::PHP_MIN_REQUIRED, '<' ) ) {
            self::do_unsupported( 'This plugin requires PHP version ' . self::PHP_MIN_REQUIRED .' or above. You have version ' . PHP_VERSION . '. Please contact your website administrator, web developer, or hosting company to get your server updated.' );
        
            return FALSE;
        }
        
        if ( version_compare( $GLOBALS['wp_version'], self::WORDPRESS_MIN_REQUIRED, '<' ) ) {
            self::do_unsupported( 'This plugin requires WordPress version ' . self::WORDPRESS_MIN_REQUIRED . ' or above. You have version ' . $GLOBALS['wp_version'] . '. Please contact your website administrator, web developer, or hosting company to get your website updated.' );
        
            return FALSE;
        }
        
        if ( ! in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
            self::do_unsupported( 'This plugin requires WooCommerce to be installed and active.' );
        
            return FALSE;
        }
        
        if ( version_compare( get_option( 'woocommerce_version' ), self::WOOCOMMERCE_MIN_REQUIRED, '<' ) ) {
            self::do_unsupported( 'This plugin requires WooCommerce version ' . self::WOOCOMMERCE_MIN_REQUIRED . ' or above. You have version ' . get_option( 'woocommerce_version' ) . '.' );
        
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Deactivate the plugin and display a notice.
     * 
     * @param string $message A message explaining which requirement was not met.
     */
    private static function do_unsupported( $message ) {

        // Wait until someone can actually see the notice before displaying it.
        if ( defined( 'DOING_AJAX' ) ) {
            return;
        }

        add_action( 'admin_init', array( __CLASS__, 'deactivate' ) );
        add_action( 'admin_notices', array( __CLASS__, 'deactivate_notice' ) );

        self::$message = $message;

        // Remove the "Plugin activated." notice (if the user has just activated the plugin).
        unset( $_GET['activate'] );
        unset( $_GET['activate-multi'] );
    }

    /**
     * Deactivate the plugin.
     */
    public static function deactivate() {
        deactivate_plugins( WOO_MP_BASENAME );
    }

    /**
     * Display deactivation notice.
     */
    public static function deactivate_notice() {
        printf(
            '<div class="error"><p>WooCommerce Manual Payment has been deactivated. %s</p></div>',
            wp_kses_post( self::$message )
        );
    }

}