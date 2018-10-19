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
     * @return bool true if all requirements are met, false otherwise.
     */
    public static function run() {
        if ( version_compare( PHP_VERSION, self::PHP_MIN_REQUIRED, '<' ) ) {
            self::$message = sprintf(
                'WooCommerce Manual Payment requires PHP version %s or above. You have version %s.' .
                ' Please contact your website administrator, web developer,' .
                ' or hosting company to get your server updated.',
                self::PHP_MIN_REQUIRED,
                PHP_VERSION
            );
        } elseif ( version_compare( $GLOBALS['wp_version'], self::WORDPRESS_MIN_REQUIRED, '<' ) ) {
            self::$message = sprintf(
                'WooCommerce Manual Payment requires WordPress version %s or above. You have version %s.' .
                ' Please contact your website administrator, web developer,' .
                ' or hosting company to get your website updated.',
                self::WORDPRESS_MIN_REQUIRED,
                $GLOBALS['wp_version']
            );
        } elseif (
            ! in_array(
                'woocommerce/woocommerce.php',
                array_merge(
                    get_option( 'active_plugins', [] ),
                    array_keys( get_site_option( 'active_sitewide_plugins', [] ) )
                )
            )
        ) {
            self::$message =
                'WooCommerce Manual Payment requires WooCommerce to be installed and active.'
            ;
        } elseif ( version_compare( get_option( 'woocommerce_version' ), self::WOOCOMMERCE_MIN_REQUIRED, '<' ) ) {
            self::$message = sprintf(
                'WooCommerce Manual Payment requires WooCommerce version %s or above. You have version %s.',
                self::WOOCOMMERCE_MIN_REQUIRED,
                get_option( 'woocommerce_version' )
            );
        }

        if ( self::$message ) {
            add_action( 'admin_notices', array( __CLASS__, 'output_notice' ) );

            return false;
        }

        return true;
    }

    /**
     * Display notice.
     */
    public static function output_notice() {
        echo '<div class="error"><p>' . wp_kses_post( self::$message ) . '</p></div>';
    }

}