<?php

if ( ! interface_exists( 'WPDesk_Translable' ) ) {
	require_once dirname(__FILE__) . '/../Translable.php';
}


/**
 * Have MUST HAVE info for plugin instantion
 *
 * have to be compatible with PHP 5.2.x
 */
interface WPDesk_Has_Plugin_Info extends WPDesk_Translable {
	/**
	 * @return string
	 */
	public function get_plugin_file_name();

	/**
	 * @return string
	 */
	public function get_plugin_dir();

	/**
	 * @return string
	 */
	public function get_version();

}