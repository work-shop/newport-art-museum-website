<?php

/**
 * Class WPDesk_Helper_Api_Key_Activator
 */
class WPDesk_Helper_License_Activator {

	/**
	 * Upgrade URLs.
	 *
	 * @var array
	 */
	private $upgrade_urls;

	/**
	 * Plugin data.
	 *
	 * @var array
	 */
	private $plugin_data;

	/**
	 * WPDesk_Helper_Api_Key_Activator constructor.
	 *
	 * @param array $plugin_data Plugin data.
	 * @param array $upgrade_urls Upgrade URLs.
	 */
	public function __construct( array $plugin_data, array $upgrade_urls ) {
		$this->plugin_data  = $plugin_data;
		$this->upgrade_urls = $upgrade_urls;
	}

	/**
	 * Get api manager from plugin data.
	 *
	 * @return WPDesk_API_Manager
	 */
	private function get_api_manager_from_plugin_data() {
		return $this->plugin_data['api_manager'];
	}

	/**
	 * Is activated?
	 *
	 * @param mixed $activate_results Activate result.
	 *
	 * @return bool
	 */
	private function is_activated( $activate_results ) {
		if ( is_array( $activate_results ) ) {
			if ( isset( $activate_results['activated'] ) && true === $activate_results['activated'] ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Is json error?
	 *
	 * @return bool
	 */
	private function is_json_error() {
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return true;
		}
		return false;
	}

	/**
	 * Is invalid api license key?
	 *
	 * @param mixed $activate_results Activate result.
	 * @return bool
	 */
	private function is_invalid_api_license_key( $activate_results ) {
		if ( is_array( $activate_results ) ) {
			if ( isset( $activate_results['code'] ) && '101' === $activate_results['code'] ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Activate and save data.
	 *
	 * @param WPDesk_API_Manager $plugin_api_manager Api manager.
	 * @param string             $activation_email Activation email.
	 * @param string             $api_key Api key.
	 */
	private function activate_and_save_data( $plugin_api_manager, $activation_email, $api_key ) {
		$plugin_api_manager->options[ $plugin_api_manager->api_key ]          = $api_key;
		$plugin_api_manager->options[ $plugin_api_manager->activation_email ] = $activation_email;
		update_option( $plugin_api_manager->data_key, $plugin_api_manager->options );
		update_option( $plugin_api_manager->upgrade_url_key, $plugin_api_manager->upgrade_url );
		update_option( $plugin_api_manager->activated_key, 'Activated' );
	}

	/**
	 * Activate and save data.
	 *
	 * @param WPDesk_API_Manager $plugin_api_manager Api manager.
	 */
	private function deactivate_and_save_data( $plugin_api_manager ) {
		$plugin_api_manager->options[ $plugin_api_manager->api_key ]          = '';
		$plugin_api_manager->options[ $plugin_api_manager->activation_email ] = '';
		update_option( $plugin_api_manager->data_key, $plugin_api_manager->options );
		update_option( $plugin_api_manager->activated_key, 'Deactivated' );
	}

	/**
	 * Show error from reposne.
	 *
	 * @param array $activate_results Activate results.
	 */
	private function show_error( array $activate_results ) {
		if ( ! isset( $activate_results['additional info'] ) ) {
			$activate_results['additional info'] = '';
		}

		$message = "{$activate_results['error']}. {$activate_results['additional info']}";

		add_settings_error( 'api_manager_message', 'api_manager_error', $message, 'error' );
	}

	/**
	 * Show unknown error.
	 */
	private function show_unknown_error() {
		add_settings_error(
			'api_key_check_text',
			'api_key_check_error',
			__( 'Connection failed to the License Key API server. Try again later.', 'wpdesk-helper' ),
			'error'
		);
	}

	/**
	 * Show activation message.
	 *
	 * @param array $activate_results Activation results.
	 */
	private function show_activation_message( array $activate_results ) {
		add_settings_error(
			'activate_text',
			'activate_msg',
			__( 'Plugin activated. ', 'wpdesk-helper' ) . "{$activate_results['message']}.",
			'updated'
		);
	}

	/**
	 * Activate license.
	 *
	 * @param string $activation_email Activation email.
	 * @param string $api_key Api key.
	 */
	public function activate_license( $activation_email, $api_key ) {
		$plugin_api_manager = $this->get_api_manager_from_plugin_data();

		$activation_args = [
			'email'       => $activation_email,
			'licence_key' => $api_key,
		];

		$activate_results = array( 'activated' => false );

		foreach ( $this->upgrade_urls as $upgrade_url ) {
			$plugin_api_manager->upgrade_url = $upgrade_url;

			$activate_raw_response = $plugin_api_manager->key()->activate( $activation_args );
			$activate_results      = json_decode( $activate_raw_response, true );

			if ( $this->is_json_error() ) {
				$activate_results = array(
					'activated'       => false,
					'error'           => 'Invalid response from API Server',
					'additional info' => $activate_raw_response,
					'code'            => '500',
				);
				break;
			}
			if ( $this->is_activated( $activate_results ) || ! $this->is_invalid_api_license_key( $activate_results ) ) {
				break;
			}
		}

		if ( true === $activate_results['activated'] ) {
			$this->activate_and_save_data( $plugin_api_manager, $activation_email, $api_key );
			$this->show_activation_message( $activate_results );
		} else {
			$this->deactivate_and_save_data( $plugin_api_manager );
			if ( isset( $activate_results['code'] ) ) {
				$this->show_error( $activate_results );
			} else {
				$this->show_unknown_error();
			}
		}

	}

}

