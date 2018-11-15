<?php

/**
 * Class WPDesk_Helper_Debug_Log
 */
class WPDesk_Helper_Debug_Log implements \WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \WPDesk\PluginBuilder\Plugin\PluginAccess;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'plugins_loaded', array( $this, 'init_debug_log_file' ) );
		add_action( 'admin_init', array( $this, 'wpdesk_debug_log_notices' ) );
	}

	/**
	 * Add notice for directory.
	 *
	 * @param string $dir Directory.
	 */
	private function add_notice_for_dir( $dir ) {
		new \WPDesk\Notice\Notice(
			sprintf(
				// Translators: directory.
				__(
					'Can not enable WP Desk Debug log! Cannot create directory %s or this directory is not writeable!',
					'wpdesk-helper'
				),
				$dir
			),
			WPDesk\Notice\Notice::NOTICE_TYPE_ERROR
		);
	}

	/**
	 * Add notice for file.
	 *
	 * @param string $file File..
	 */
	private function add_notice_for_file( $file ) {
		new \WPDesk\Notice\Notice(
			sprintf(
			// Translators: directory.
				__(
					'Can not enable WP Desk Debug log! Cannot create file %s!',
					'wpdesk-helper'
				),
				$file
			),
			WPDesk\Notice\Notice::NOTICE_TYPE_ERROR
		);
	}

	/**
	 * Is debug log writable.
	 *
	 * @return bool
	 */
	private function is_debug_log_writable_or_show_notice() {
		$log_dir    = $this->get_log_dir();
		$log_file   = $this->get_log_file();
		$index_file = $this->get_index_file();
		if ( ! file_exists( $log_dir ) ) {
			if ( ! mkdir( $log_dir, 0777, true) ) {
				$this->add_notice_for_dir( $log_dir );
				return false;
			}
		}
		if ( ! file_exists( $index_file ) ) {
			$index_html = fopen( $index_file, 'w' );
			if ( false === $index_html ) {
				$this->add_notice_for_file( $index_file );
				return false;
			} else {
				fclose( $index_html );
			}
		}
		if ( ! file_exists( $log_file ) ) {
			$log = fopen( $log_file, 'w' );
			if ( false === $log ) {
				$this->add_notice_for_file( $log_file );
				return false;
			} else {
				fclose( $log );
			}
		}
		return true;
	}

	/**
	 * Init debug log file.
	 */
	public function init_debug_log_file() {
		$options = get_option( 'wpdesk_helper_options', [] );
		if ( ! is_array( $options ) ) {
			$options = [];
		}
		$debug_log_enabled = isset( $options['debug_log'] ) && '1' === $options['debug_log'];

		if ( $debug_log_enabled ) {
			if ( $this->is_debug_log_writable_or_show_notice() ) {
				ini_set( 'log_errors', 1 );
				ini_set( 'error_log', $this->get_log_file() );
			}
		}
	}

	/**
	 * Get uploads dir.
	 *
	 * @return string
	 */
	private function get_uploads_dir() {
		$upload_dir = wp_upload_dir();
		return untrailingslashit( $upload_dir['basedir'] );
	}

	/**
	 * Get log dir.
	 *
	 * @return string
	 */
	private function get_log_dir() {
		return trailingslashit( $this->get_uploads_dir() ) . 'wpdesk-logs';
	}

	/**
	 * Get log file.
	 *
	 * @return string
	 */
	private function get_log_file() {
		return trailingslashit( $this->get_log_dir() ) . 'wpdesk_debug.log';
	}

	/**
	 * Get log file.
	 *
	 * @return string
	 */
	private function get_index_file() {
		return trailingslashit( $this->get_log_dir() ) . 'index.html';
	}

	/**
	 * WPDesk Debug Log notices.
	 */
	public function wpdesk_debug_log_notices() {
		$options = get_option( 'wpdesk_helper_options', [] );
		if ( ! is_array( $options ) ) {
			$options = [];
		}
		$debug_log_enabled = isset( $options['debug_log'] ) && '1' === $options['debug_log'];
		if ( $debug_log_enabled ) {
			if ( apply_filters( 'wpdesk_helper_show_log_notices', true ) ) {
				new \WPDesk\Notice\Notice(
					sprintf(
						// Translators: link.
						__(
							'WP Desk Debug Log is enabled. %1$sPlease disable it after testing%2$s.',
							'wpdesk-helper'
						),
						'<a href="' . admin_url( 'admin.php?page=wpdesk-helper-settings' ) . '">',
						'</a>'
					),
					WPDesk\Notice\Notice::NOTICE_TYPE_INFO
				);
			}
		}
	}

}

