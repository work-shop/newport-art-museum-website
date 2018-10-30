<?php
/*
    Plugin Name: Flexible Checkout Fields PRO
    Plugin URI: https://www.wpdesk.net/products/flexible-checkout-fields-pro-woocommerce/
    Description: Extension to the free version. Adds new field types, custom sections and more.
    Version: 1.6.9
    Author: WP Desk
    Author URI: https://www.wpdesk.net/
    Text Domain: flexible-checkout-fields-pro
    Domain Path: /lang/
	Requires at least: 4.5
    Tested up to: 4.9.8
    WC requires at least: 3.1.0
    WC tested up to: 3.5.0

    Copyright 2018 WP Desk Ltd.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

$plugin_version = '1.6.9';
define( 'FLEXIBLE_CHECKOUT_FIELDS_PRO_VERSION', $plugin_version );


$plugin_data = array(
	'plugin' => plugin_basename( __FILE__ ),
	'product_id' => 'WooCommerce Flexible Checkout Fields',
	'version'    => FLEXIBLE_CHECKOUT_FIELDS_PRO_VERSION,
	'config_uri' => admin_url( 'admin.php?page=inspire_checkout_fields_settings' )
);

if ( ! defined( 'FCF_PRO_VERSION' ) ) {
	define( 'FCF_PRO_VERSION', FLEXIBLE_CHECKOUT_FIELDS_PRO_VERSION );
}

require_once( plugin_basename( 'inc/wpdesk-woo27-functions.php' ) );

require_once( plugin_basename( 'classes/wpdesk/class-plugin.php' ) );

require_once( plugin_basename( 'classes/flexible-checkout-fields-pro-plugin.php' ) );


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
				$api->version = '1.6.9';
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
		if ( ! defined( 'FCF_VERSION' ) || version_compare( FCF_VERSION, '1.6', '<' ) ) {
			$message = __( 'Flexible Checkout Fields PRO requires Flexible Checkout Fields plugin in version 1.6 or newer. Please update.', 'flexible-checkout-fields-pro' );
			echo '<div class="error fade"><p>' . $message . '</p></div>' . "\n";
		}
	}
	add_action( 'admin_notices', 'flexible_checkout_fields_pro_notice_free_version' );
}

$flexible_checkout_fields_pro_plugin_data = $plugin_data;
function flexible_checkout_fields_pro() {
	global $flexible_checkout_fields_pro;
	global $flexible_checkout_fields_pro_plugin_data;
	if ( !isset( $flexible_checkout_fields_pro ) ) {
		$flexible_checkout_fields_pro = new Flexible_Checkout_Fields_PRO_Plugin( __FILE__, $flexible_checkout_fields_pro_plugin_data );
	}
	return $flexible_checkout_fields_pro;
}

if ( wpdesk_is_plugin_active( 'flexible-checkout-fields/flexible-checkout-fields.php' ) ) {
	$_GLOBALS['flexible_checkout_fields_pro'] = flexible_checkout_fields_pro();
}
