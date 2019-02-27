<?php
/*
	Plugin Name: WP Desk Helper
	Plugin URI: https://www.wpdesk.net/
	Description: Enables WP Desk plugin activation and updates.
	Version: 1.6.5
	Author: WP Desk
	Text Domain: wpdesk-helper
	Domain Path: /lang/
	Author URI: https://www.wpdesk.net/
	Requires at least: 4.6
    Tested up to: 5.1.0
    WC requires at least: 3.1.0
    WC tested up to: 3.5.5
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Only PHP 5.2 compatible code
if ( ! class_exists( 'WPDesk_Basic_Requirement_Checker' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/wpdesk/wp-basic-requirements/src/Basic_Requirement_Checker.php';
}

/* THESE TWO VARIABLES CAN BE CHANGED AUTOMATICALLY */
$plugin_version           = '1.6.5';
$plugin_release_timestamp = '2019-02-05';

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

if ( ! function_exists( 'should_enable_wpdesk_tracker' ) ) {
	/**
	 * Should enable WPDesk Tracker.
	 *
	 * @return bool
	 */
	function should_enable_wpdesk_tracker() {
		$tracker_enabled = true;
		if ( ! empty( $_SERVER['SERVER_ADDR'] ) && $_SERVER['SERVER_ADDR'] == '127.0.0.1' ) {
			$tracker_enabled = false;
		}
		return apply_filters( 'wpdesk_tracker_enabled', $tracker_enabled );
	}
}


if ( ! function_exists( 'wpdesk_helper_activated_plugin' ) ) {
	/**
	 * Activated plugin.
	 *
	 * @param string $plugin Plugin slug.
	 * @param bool   $network_wide Network wide.
	 */
	function wpdesk_helper_activated_plugin( $plugin, $network_wide ) {
		if ( should_enable_wpdesk_tracker() && ! apply_filters( 'wpdesk_tracker_do_not_ask', false ) ) {
			if ( 'wpdesk-helper/wpdesk-helper.php' === $plugin ) {
				$options = get_option( 'wpdesk_helper_options', [] );
				if ( empty( $options ) ) {
					$options = [];
				}
				if ( empty( $options['wpdesk_tracker_agree'] ) ) {
					$options['wpdesk_tracker_agree'] = '1.6.5';
				}
				$wpdesk_tracker_skip_plugin = get_option( 'wpdesk_tracker_skip_wpdesk_helper', '0' );
				if ( '0' === $options['wpdesk_tracker_agree'] && '0' === $wpdesk_tracker_skip_plugin ) {
					update_option( 'wpdesk_tracker_notice', '1' );
					update_option( 'wpdesk_tracker_skip_wpdesk_helper', '1' );
					wp_safe_redirect( admin_url( 'admin.php?page=wpdesk_tracker&plugin=wpdesk-helper/wpdesk-helper.php' ) );
					exit;
				}
			}
		}
	}
	add_action( 'activated_plugin', 'wpdesk_helper_activated_plugin', 10, 2 );
}
