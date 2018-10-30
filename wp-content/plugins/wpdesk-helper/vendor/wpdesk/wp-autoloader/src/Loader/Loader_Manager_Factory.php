<?php

if ( ! class_exists( 'WPDesk_Loader' ) ) {
	require_once 'Loader.php';
}
if ( ! class_exists( 'WPDesk_Loader_Manager' ) ) {
	require_once 'Loader_Manager.php';
}

/**
 * Factory for loader manager
 */
class WPDesk_Loader_Manager_Factory {

	/** @var WPDesk_Loader_Manager */
	private static $instance;

	/**
	 * Builds instance of manager. If called more than once then more than one instance is created.
	 *
	 * @return WPDesk_Loader_Manager
	 */
	public static function build_load_manager() {
		$manager = new WPDesk_Loader_Manager();
		$manager->attach_autoload_hook_once();

		if (function_exists('wc_get_logger' ) ) {
			$manager->set_logger( wc_get_logger() );
		}
		return $manager;
	}

	/**
	 * Not sure if ever needed but we can change the stored instance
	 *
	 * @param WPDesk_Loader_Manager $manager
	 */
	public function set_instance(WPDesk_Loader_Manager $manager) {
		self::$instance = $manager;
	}

	/**
	 * Builds instance if needed and ensures there is only one instance.
	 *
	 * @return WPDesk_Loader_Manager
	 */
	public static function get_manager_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = self::build_load_manager();
		}

		return self::$instance;
	}
}