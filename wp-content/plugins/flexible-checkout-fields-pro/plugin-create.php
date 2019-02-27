<?php

use WPDesk\PluginBuilder\BuildDirector\LegacyBuildDirector;
use WPDesk\PluginBuilder\Builder\InfoBuilder;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Plugin info.
 *
 * @var WPDesk_Plugin_Info $plugin_info
 */
$builder        = new InfoBuilder( $plugin_info );
$build_director = new LegacyBuildDirector( $builder );
$build_director->build_plugin();

if ( !function_exists( 'wpdesk_is_plugin_active' ) ) {
	function wpdesk_is_plugin_active( $plugin_file ) {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( $plugin_file, $active_plugins ) || array_key_exists( $plugin_file, $active_plugins );
	}
}

if ( ! wpdesk_is_plugin_active( 'flexible-checkout-fields/flexible-checkout-fields.php' ) ) {
	function flexible_checkout_fields_pro_flexible_checkout_fields_install( $api, $action, $args ) {
		$download_url = 'http://downloads.wordpress.org/plugin/flexible-checkout-fields.latest-stable.zip';

		if ( 'plugin_information' != $action ||
		     false !== $api ||
		     ! isset( $args->slug ) ||
		     'wpdesk-helper' != $args->slug
		) return $api;

		$api = new stdClass();
		$api->name = 'Flexible Checkout Fields';
		$api->version = '1.9.0';
		$api->download_link = esc_url( $download_url );
		return $api;
	}

	add_filter( 'plugins_api', 'flexible_checkout_fields_pro_flexible_checkout_fields_install', 10, 3 );

	function flexible_checkout_fields_pro_notice() {

		if ( wpdesk_is_plugin_active( 'flexible-checkout-fields/flexible-checkout-fields.php' ) ) return;

		$slug = 'flexible-checkout-fields';
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
		$activate_url = 'plugins.php?action=activate&plugin=' . urlencode( 'flexible-checkout-fields/flexible-checkout-fields.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode( wp_create_nonce( 'activate-plugin_flexible-checkout-fields/flexible-checkout-fields.php' ) );

		$message = sprintf( wp_kses( __( 'Flexible Checkout Fields PRO requires free Flexible Checkout Fields plugin. <a href="%s">Install Flexible Checkout Fields →</a>', 'flexible-checkout-fields-pro' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $install_url ) );
		$is_downloaded = false;
		$plugins = array_keys( get_plugins() );
		foreach ( $plugins as $plugin ) {
			if ( strpos( $plugin, 'flexible-checkout-fields/flexible-checkout-fields.php' ) === 0 ) {
				$is_downloaded = true;
				$message = sprintf( wp_kses( __( 'Flexible Checkout Fields PRO requires activating Flexible Checkout Fields plugin. <a href="%s">Activate Flexible Checkout Fields →</a>', 'flexible-checkout-fields-pro' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( admin_url( $activate_url ) ) );
			}
		}
		echo '<div class="error fade"><p>' . $message . '</p></div>' . "\n";
	}
	add_action( 'admin_notices', 'flexible_checkout_fields_pro_notice' );
}
else {

	function flexible_checkout_fields_pro_notice_free_version() {
		$required_fcf_version = '1.9.0';
		if ( ! defined( 'FCF_VERSION' ) || version_compare( FCF_VERSION, $required_fcf_version, '<' ) ) {
			$message = sprintf( // Translators: FCF version.
				__( 'Flexible Checkout Fields PRO requires Flexible Checkout Fields plugin in version %1$s or newer. Please update.', 'flexible-checkout-fields-pro' ),
				$required_fcf_version
			);
			echo '<div class="error fade"><p>' . $message . '</p></div>' . "\n";
		}
	}
	add_action( 'admin_notices', 'flexible_checkout_fields_pro_notice_free_version' );
}
