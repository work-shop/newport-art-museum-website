<?php
/*
    Plugin Name: Flexible Checkout Fields PRO
    Plugin URI: https://www.wpdesk.net/products/flexible-checkout-fields-pro-woocommerce/
    Description: Extension to the free version. Adds new field types, custom sections and more.
    Version: 1.9.0
    Author: WP Desk
    Author URI: https://www.wpdesk.net/
    Text Domain: flexible-checkout-fields-pro
    Domain Path: /lang/
	Requires at least: 4.6
    Tested up to: 5.1.0
    WC requires at least: 3.1.0
    WC tested up to: 3.5.5

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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Only PHP 5.2 compatible code
if ( ! class_exists( 'WPDesk_Basic_Requirement_Checker' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/wpdesk/wp-basic-requirements/src/Basic_Requirement_Checker.php';
}


/* THESE TWO VARIABLES CAN BE CHANGED AUTOMATICALLY */
$plugin_version           = '1.9.0';
$plugin_release_timestamp = '2018-11-19';

$plugin_name        = 'Flexible Checkout Fields PRO';
$plugin_class_name  = 'Flexible_Checkout_Fields_Pro_Plugin';
$plugin_text_domain = 'flexible-checkout-fields-pro';
$product_id         = 'WooCommerce Flexible Checkout Fields';

define( 'FLEXIBLE_CHECKOUT_FIELDS_PRO_VERSION', $plugin_version );
define( $plugin_class_name, $plugin_version );

$requirements_checker = new WPDesk_Basic_Requirement_Checker(
	__FILE__,
	$plugin_name,
	$plugin_text_domain,
	'5.5',
	'4.5'
);
$requirements_checker->add_plugin_require( 'woocommerce/woocommerce.php', 'Woocommerce' );
$requirements_checker->add_plugin_require( 'flexible-checkout-fields/flexible-checkout-fields.php', 'Flexible Checkout Fields' );

if ( $requirements_checker->are_requirements_met() ) {
	if ( ! class_exists( 'WPDesk_Plugin_Info' ) ) {
		require_once dirname( __FILE__ ) . '/vendor/wpdesk/wp-basic-requirements/src/Plugin/Plugin_Info.php';
	}

	$plugin_info = new WPDesk_Plugin_Info();
	$plugin_info->set_plugin_file_name( plugin_basename( __FILE__ ) );
	$plugin_info->set_plugin_dir( dirname( __FILE__ ) );
	$plugin_info->set_class_name( $plugin_class_name );
	$plugin_info->set_version( $plugin_version );
	$plugin_info->set_product_id( $product_id );
	$plugin_info->set_text_domain( $plugin_text_domain );
	$plugin_info->set_release_date( new DateTime( $plugin_release_timestamp ) );
	$plugin_info->set_plugin_url( plugins_url( dirname( plugin_basename( __FILE__ ) ) ) );

	require_once dirname( __FILE__ ) . '/plugin-load.php';

} else {
	$requirements_checker->disable_plugin_render_notice();
}
