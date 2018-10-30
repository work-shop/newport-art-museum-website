<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once( __DIR__ . '/vendor/autoload.php' );

WPDesk_Logger_Factory::create_logger();

/**
 * @return WPDesk_Helper
 */
function WPDesk_Helper() {
	return WPDesk_Helper::WPDesk_Helper();
}

if ( ! function_exists( 'should_enable_wpdesk_tracker' ) ) {
	function should_enable_wpdesk_tracker() {
		$tracker_enabled = true;
		if ( ! empty( $_SERVER['SERVER_ADDR'] ) && $_SERVER['SERVER_ADDR'] == '127.0.0.1' ) {
			$tracker_enabled = false;
		}

		return apply_filters( 'wpdesk_tracker_enabled', $tracker_enabled );
		// add_filter( 'wpdesk_tracker_enabled', '__return_true' );
		// add_filter( 'wpdesk_tracker_do_not_ask', '__return_true' );
	}
}

add_action( 'plugins_loaded', 'wpdesk_helper_init', 1 );

add_action( 'activated_plugin', 'wpdesk_helper_activated_plugin', 10, 2 );
if ( ! function_exists( 'wpdesk_helper_activated_plugin' ) ) {
	function wpdesk_helper_activated_plugin( $plugin, $network_wide ) {
		if ( should_enable_wpdesk_tracker() && ! apply_filters( 'wpdesk_tracker_do_not_ask', false ) ) {
			if ( $plugin == 'wpdesk-helper/wpdesk-helper.php' ) {
				$options = get_option( 'wpdesk_helper_options', [] );
				if ( empty( $options ) ) {
					$options = [];
				}
				if ( empty( $options['wpdesk_tracker_agree'] ) ) {
					$options['wpdesk_tracker_agree'] = '1.4.5';
				}
				$wpdesk_tracker_skip_plugin = get_option( 'wpdesk_tracker_skip_wpdesk_helper', '0' );
				if ( $options['wpdesk_tracker_agree'] == '0' && $wpdesk_tracker_skip_plugin == '0' ) {
					update_option( 'wpdesk_tracker_notice', '1' );
					update_option( 'wpdesk_tracker_skip_wpdesk_helper', '1' );
					wp_redirect( admin_url( 'admin.php?page=wpdesk_tracker&plugin=wpdesk-helper/wpdesk-helper.php' ) );
					exit;
				}
			}
		}
	}
}

if ( ! function_exists( 'wpdesk_activated_plugin_activation_date' ) ) {
	function wpdesk_activated_plugin_activation_date( $plugin, $network_wide ) {
		$option_name     = 'plugin_activation_' . $plugin;
		$activation_date = get_option( $option_name, '' );
		if ( $activation_date == '' ) {
			$activation_date = current_time( 'mysql' );
			update_option( $option_name, $activation_date );
		}
	}

	add_action( 'activated_plugin', 'wpdesk_activated_plugin_activation_date', 10, 2 );
}

if ( ! function_exists( 'wpdesk_helper_init' ) ) {
	function wpdesk_helper_init() {
		$tracker_factory = new WPDesk_Tracker_Factory();
		$tracker_factory->create_tracker( basename( dirname( __FILE__ ) ) );

		WPDesk_Helper();
		$options = get_option( 'wpdesk_helper_options', [] );
		if ( ! is_array( $options ) ) {
			$options = [];
		}
		if ( is_array( $options ) && isset( $options['debug_log'] ) && $options['debug_log'] == '1' ) {
			if ( is_writeable( WP_CONTENT_DIR . '/uploads/' ) && ! file_exists( WP_CONTENT_DIR . '/uploads/wpdesk-logs/' ) ) {
				mkdir( WP_CONTENT_DIR . '/uploads/wpdesk-logs', 0777, true );
			}
			if ( file_exists( WP_CONTENT_DIR . '/uploads/wpdesk-logs/' ) ) {
				if ( is_writeable( WP_CONTENT_DIR . '/uploads/wpdesk-logs/' ) && ! file_exists( WP_CONTENT_DIR . '/uploads/wpdesk-logs/index.html' ) ) {
					$index_html = fopen( WP_CONTENT_DIR . '/uploads/wpdesk-logs/index.html', 'w' );
					fclose( $index_html );
				}
			}
			if ( file_exists( WP_CONTENT_DIR . '/uploads/wpdesk-logs/' ) && is_writeable( WP_CONTENT_DIR . '/uploads/wpdesk-logs/' ) ) {
				ini_set( 'log_errors', 1 );
				ini_set( 'error_log', WP_CONTENT_DIR . '/uploads/wpdesk-logs/wpdesk_debug.log' );
			}
		}
	}
}
