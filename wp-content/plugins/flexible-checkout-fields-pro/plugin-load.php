<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Plugin info.
 *
 * @var WPDesk_Plugin_Info $plugin_info
 */

/**
 * Here we KNOW:
 * - that the PHP and WordPress version is in line with our expectation,
 * - internal PHP modules and settings are correctly set,
 * - what other plugins will be loaded but we don't know the version yet.
 *
 * We DON'T KNOW:
 * - what versions of plugins will be loaded (ie. WooCommerce 2.6 or 3.0?)
 * - autoloader is not working yet
 */
if ( ! class_exists( 'WPDesk_Loader_Manager_Factory' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/wpdesk/wp-autoloader/src/Loader/Loader_Manager_Factory.php';
}
if ( ! class_exists( 'WPDesk_Composer_Loader' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/wpdesk/wp-autoloader/src/Loader/Composer/Composer_Loader.php';
}
if ( ! class_exists( 'WPDesk_Composer_Loader_Info' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/wpdesk/wp-autoloader/src/Loader/Composer/Composer_Loader_Info.php';
}
$loader_info = new WPDesk_Composer_Loader_Info();
$loader_info->set_autoload_file( new \SplFileInfo( realpath( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) );
$loader_info->set_load_priority( $plugin_info->get_release_date()->getTimestamp() );
$loader_info->set_creation_file( new \SplFileInfo( realpath( dirname( __FILE__ ) . '/plugin-create.php' ) ) );
$loader_info->set_plugin_info( $plugin_info );

$composer_loader = new WPDesk_Composer_Loader( $loader_info );

$loader_manager = WPDesk_Loader_Manager_Factory::get_manager_instance();
$loader_manager->attach_loader( $composer_loader );
