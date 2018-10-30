<?php

/**
 * WooCommerce API Manager API Key Class
 *
 * @package Update API Manager/Key Handler
 * @author Todd Lahman LLC, WPDesk
 * @copyright   Copyright (c) Todd Lahman LLC
 * @since 1.3
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'WPDesk_API_KEY' ) ) :

	class WPDesk_API_KEY {

		/**
		 * @var The single instance of the class
		 */
		protected static $_instance = null;

		private $api_manager;

		public function __construct( $api_manager ) {
			$this->api_manager = $api_manager;
		}

		// API Key URL
		public function create_software_api_url( $args ) {

			$api_url = add_query_arg( 'wc-api', 'am-software-api', $this->api_manager->upgrade_url );

			return $api_url . '&' . http_build_query( $args );
		}

		public function activate( $args ) {

			$this->api_manager->create_instance_id();

			$defaults = [
				'request'          => 'activation',
				'product_id'       => $this->api_manager->product_id,
				'instance'         => $this->api_manager->instance_id,
				'platform'         => $this->api_manager->domain,
				'software_version' => $this->api_manager->software_version
			];

			$args       = wp_parse_args( $defaults, $args );
			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );
			$target_url = str_replace( '&amp;', '&', $target_url );

			$request = wp_safe_remote_get( $target_url, [ 'timeout' => 30, 'sslverify' => false ] );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				if ( is_wp_error( $request ) ) {
					WPDesk_Logger_Factory::log_wp_error( $request, debug_backtrace() );
				} else {
					WPDesk_Logger_Factory::log_message_backtrace( 'Response is invalid. Response: ' . json_encode($request), WPDesk_Logger::ERROR, debug_backtrace() );
				}
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}

		public function deactivate( $args ) {

			$defaults = [
				'request'    => 'deactivation',
				'product_id' => $this->api_manager->product_id,
				'instance'   => $this->api_manager->instance_id,
				'platform'   => $this->api_manager->domain
			];

			$args = wp_parse_args( $defaults, $args );

			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );
			$target_url = str_replace( '&amp;', '&', $target_url );

			$request = wp_safe_remote_get( $target_url, [ 'timeout' => 30, 'sslverify' => false ] );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				if ( is_wp_error( $request ) ) {
					WPDesk_Logger_Factory::log_wp_error( $request, debug_backtrace() );
				} else {
					WPDesk_Logger_Factory::log_message_backtrace( 'Response is invalid. Response: ' . json_encode($request), WPDesk_Logger::ERROR, debug_backtrace() );
				}
				// Request failed
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}

		/**
		 * Checks if the software is activated or deactivated
		 *
		 * @param  array $args
		 *
		 * @return array
		 */
		public function status( $args ) {

			$defaults = [
				'request'    => 'status',
				'product_id' => $this->api_manager->product_id,
				'instance'   => $this->api_manager->instance_id,
				'platform'   => $this->api_manager->domain
			];

			$args = wp_parse_args( $defaults, $args );

			$target_url = esc_url_raw( $this->create_software_api_url( $args ) );
			$target_url = str_replace( '&amp;', '&', $target_url );

			$request = wp_safe_remote_get( $target_url, [ 'timeout' => 30, 'sslverify' => false ] );

			// $request = wp_remote_post( $this->api_manager->upgrade_url . 'wc-api/am-software-api/', array( 'body' => $args ) );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				if ( is_wp_error( $request ) ) {
					WPDesk_Logger_Factory::log_wp_error( $request, debug_backtrace() );
				} else {
					WPDesk_Logger_Factory::log_message_backtrace( 'Response is invalid. Response: ' . json_encode($request), WPDesk_Logger::ERROR, debug_backtrace() );
				}
				// Request failed
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}

	} // class WPDesk_API_KEY

endif; // if (!class_exists('WPDesk_API_KEY'))