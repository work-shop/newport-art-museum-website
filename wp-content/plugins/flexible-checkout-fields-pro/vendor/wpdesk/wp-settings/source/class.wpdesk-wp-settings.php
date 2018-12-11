<?php
/**
 * WP Desk Wordpress Settings class
 *
 * @package     wpdesk\wp-settings
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * WP Desk Wordpress Settings class
 *
 */
class WPDesk_WP_Settings  {

	private $settings;

	/**
	 * WPDesk_WP_Settings constructor.
	 *
	 * @param string $url_path
	 * @param string $slug
	 * @param string $default_tab
	 */
	public function __construct( $url_path = '', $slug = 'wpdesk-settings', $default_tab = 'general' ) {
		$this->settings = new WPDesk_S214_Settings( $url_path, $slug, $default_tab );
	}

	/**
	 * Get single option from settings.
	 *
	 * @param string $key
	 * @param bool $default
	 *
	 * @return mixed
	 */
	public function get_option( $key = '', $default = false ) {
		return $this->settings->get_option( $key, $default );
	}

}

