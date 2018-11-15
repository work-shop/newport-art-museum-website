<?php
/*
	Plugin Name: WP Desk Helper
	Plugin URI: https://www.wpdesk.net/
	Description: Enables WP Desk plugin activation and updates.
	Version: 1.5.0
	Author: WP Desk
	Text Domain: wpdesk-helper
	Domain Path: /lang/
	Author URI: https://www.wpdesk.net/
	Requires at least: 4.5
    Tested up to: 5.0.0
    WC requires at least: 3.1.0
    WC tested up to: 3.5.1
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Only PHP 5.2 compatible code
if ( ! class_exists( 'WPDesk_Basic_Requirement_Checker' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/wpdesk/wp-basic-requirements/src/Basic_Requirement_Checker.php';
}

/* THESE TWO VARIABLES CAN BE CHANGED AUTOMATICALLY */
$plugin_version           = '1.5.0';
$plugin_release_timestamp = '2018-06-11';

$plugin_name        = 'WP Desk Helper';
$plugin_class_name  = 'WPDesk_Helper';
$plugin_text_domain = 'wpdesk-helper';

defined( $plugin_class_name ) || define( $plugin_class_name, $plugin_version );

$requirements_checker = new WPDesk_Basic_Requirement_Checker(
	__FILE__,
	$plugin_name,
	$plugin_text_domain,
	'5.5',
	'4.5'
);

if ( $requirements_checker->are_requirements_met() ) {
	if ( ! class_exists( 'WPDesk_Plugin_Info' ) ) {
		require_once dirname( __FILE__ ) . '/vendor/wpdesk/wp-basic-requirements/src/Plugin/Plugin_Info.php';
	}

	$plugin_info = new WPDesk_Plugin_Info();
	$plugin_info->set_plugin_file_name( plugin_basename( __FILE__ ) );
	$plugin_info->set_plugin_dir( dirname( __FILE__ ) );
	$plugin_info->set_class_name( $plugin_class_name );
	$plugin_info->set_version( $plugin_version );
	$plugin_info->set_product_id( $plugin_text_domain );
	$plugin_info->set_text_domain( $plugin_text_domain );
	$plugin_info->set_release_date( new DateTime( $plugin_release_timestamp ) );
	$plugin_info->set_plugin_url( plugins_url( dirname( plugin_basename( __FILE__ ) ) ) );

	require_once dirname( __FILE__ ) . '/plugin-load.php';
} else {
	$requirements_checker->disable_plugin_render_notice();
}
