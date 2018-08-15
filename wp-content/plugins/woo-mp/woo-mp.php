<?php

/**
 * Plugin Name: WooCommerce Manual Payment
 * Description: Process payments right from the backend. No need to leave the WooCommerce Edit Order screen.
 * Version: 1.13.0
 * Author: bfl
 * Requires at least: 4.4
 * Tested up to: 4.9
 * WC requires at least: 2.6
 * WC tested up to: 3.4
 * Text Domain: woo-mp
 */

/**
 * This file must maintain compatibility with:
 * 
 * PHP: 5.2.4+
 * WordPress: 3.1.0+
 */

defined( 'ABSPATH' ) || die;

if ( ! is_admin() ) {
    return;
}

define( 'WOO_MP_PATH', dirname( __FILE__ ) );
define( 'WOO_MP_URL', plugins_url( '', __FILE__ ) );
define( 'WOO_MP_BASENAME', 'woo-mp/woo-mp.php' ); // Hardcoded to support symlinks.

require WOO_MP_PATH . '/includes/woo-mp-requirement-checks.php';

if ( ! Woo_MP_Requirement_Checks::run() ) {
    return;
}

require WOO_MP_PATH . '/includes/woo-mp.php';