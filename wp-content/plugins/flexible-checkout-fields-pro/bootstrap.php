<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

//require_once __DIR__ . '/vendor/autoload.php';
require_once( 'classes/wpdesk/class-plugin.php' );
require_once( 'classes/flexible-shipping-pro-factory.php' );
require_once( 'inc/functions.php' );

WPDesk_Flexible_Shipping_Pro_Factory::get_plugin_instance();