<?php

use WPDesk\PluginBuilder\BuildDirector\LegacyBuildDirector;
use WPDesk\PluginBuilder\Builder\InfoBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var WPDesk_Plugin_Info $plugin_info */
$builder        = new InfoBuilder( $plugin_info );
$build_director = new LegacyBuildDirector( $builder );
$build_director->build_plugin();

WPDesk_Logger_Factory::create_logger();

/**
 * @return WPDesk_Helper
 */
function WPDesk_Helper() {
	$storage = new \WPDesk\PluginBuilder\Storage\StaticStorage();
	return $storage->get_from_storage( WPDesk_Helper::class );
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
					$options['wpdesk_tracker_agree'] = '1.5.0';
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

if ( ! function_exists( 'wpdesk_activated_plugin_activation_date' ) ) {
	/**
	 * Redister plugin activation date.
	 *
	 * @param string $plugin Plugin slug.
	 * @param bool   $network_wide Network wide.
	 */
	function wpdesk_activated_plugin_activation_date( $plugin, $network_wide ) {
		$option_name     = 'plugin_activation_' . $plugin;
		$activation_date = get_option( $option_name, '' );
		if ( '' === $activation_date ) {
			$activation_date = current_time( 'mysql' );
			update_option( $option_name, $activation_date );
		}
	}
	add_action( 'activated_plugin', 'wpdesk_activated_plugin_activation_date', 10, 2 );
}

if ( ! function_exists( 'wpdesk_helper_init' ) ) {
	/**
	 * Init wpdesk helper.
	 */
	function wpdesk_helper_init() {
		$tracker_factory = new WPDesk_Tracker_Factory();
		$tracker_factory->create_tracker( basename( dirname( __FILE__ ) ) );
	}
	add_action( 'plugins_loaded', 'wpdesk_helper_init', '1' );
}
