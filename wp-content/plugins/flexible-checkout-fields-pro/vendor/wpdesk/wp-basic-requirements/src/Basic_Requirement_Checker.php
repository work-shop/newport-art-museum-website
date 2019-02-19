<?php

if ( ! interface_exists( 'WPDesk_Translable' ) ) {
	require_once 'Translable.php';
}

/**
 * Checks requirements for plugin
 * have to be compatible with PHP 5.2.x
 */
class WPDesk_Basic_Requirement_Checker implements WPDesk_Translable {
	const EXTENSION_NAME_OPENSSL = 'openssl';
	const HOOK_ADMIN_NOTICES_ACTION = 'admin_notices';

	/** @var string */
	private $plugin_name = '';
	/** @var string */
	private $plugin_file = '';
	/** @var string */
	private $min_php_version;
	/** @var string */
	private $min_wp_version;
	/** @var string|null */
	private $min_wc_version = null;
	/** @var int|null */
	private $min_openssl_version = null;
	/** @var array */
	private $plugin_require;
	/** @var array */
	private $module_require;
	/** @var array */
	private $setting_require;
	/** @var array */
	private $notices;
	/** @var @string */
	private $text_domain;

	/**
	 * @param string $plugin_file
	 * @param string $plugin_name
	 * @param string $text_domain
	 * @param string $php_version
	 * @param string $wp_version
	 */
	public function __construct( $plugin_file, $plugin_name, $text_domain, $php_version, $wp_version ) {
		$this->plugin_file = $plugin_file;
		$this->plugin_name = $plugin_name;
		$this->text_domain = $text_domain;

		$this->set_min_php_require( $php_version );
		$this->set_min_wp_require( $wp_version );

		$this->plugin_require  = array();
		$this->module_require  = array();
		$this->setting_require = array();
		$this->notices         = array();
	}

	/**
	 * @param string $version
	 *
	 * @return $this
	 */
	public function set_min_php_require( $version ) {
		$this->min_php_version = $version;

		return $this;
	}

	/**
	 * @param string $version
	 *
	 * @return $this
	 */
	public function set_min_wp_require( $version ) {
		$this->min_wp_version = $version;

		return $this;
	}

	/**
	 * @param string $version
	 *
	 * @return $this
	 */
	public function set_min_wc_require( $version ) {
		$this->min_wc_version = $version;

		return $this;
	}

	/**
	 * @param $version
	 *
	 * @return $this
	 */
	public function set_min_openssl_require( $version ) {
		$this->min_openssl_version = $version;

		return $this;
	}

	/**
	 * @param string $plugin_name
	 * @param string $nice_plugin_name Nice plugin name for better looks in notice
	 *
	 * @return $this
	 */
	public function add_plugin_require( $plugin_name, $nice_plugin_name = null ) {
		if ( is_null( $nice_plugin_name ) ) {
			$this->plugin_require[ $plugin_name ] = $plugin_name;
		} else {
			$this->plugin_require[ $plugin_name ] = $nice_plugin_name;
		}

		return $this;
	}

	/**
	 * @param string $module_name
	 * @param string $nice_name Nice module name for better looks in notice
	 *
	 * @return $this
	 */
	public function add_php_module_require( $module_name, $nice_name = null ) {
		if ( is_null( $nice_name ) ) {
			$this->module_require[ $module_name ] = $module_name;
		} else {
			$this->module_require[ $module_name ] = $nice_name;
		}

		return $this;
	}

	/**
	 * @param string $setting
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function add_php_setting_require( $setting, $value ) {
		$this->setting_require[ $setting ] = $value;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function are_requirements_met() {
		$this->notices = $this->prepare_requirement_notices();

		return count( $this->notices ) === 0;
	}

	/**
	 * @return array
	 */
	private function prepare_requirement_notices() {
		$notices = array();
		if ( ! $this->is_php_at_least( $this->min_php_version ) ) {
			$notices[] = $this->prepare_notice_message( sprintf( __( 'The &#8220;%s&#8221; plugin cannot run on PHP versions older than %s. Please contact your host and ask them to upgrade.',
				$this->get_text_domain() ), esc_html( $this->plugin_name ), $this->min_php_version ) );
		}
		if ( ! $this->is_wp_at_least( $this->min_wp_version ) ) {
			$notices[] = $this->prepare_notice_message( sprintf( __( 'The &#8220;%s&#8221; plugin cannot run on WordPress versions older than %s. Please update WordPress.',
				$this->get_text_domain() ), esc_html( $this->plugin_name ), $this->min_wp_version ) );
		}
		if ( ! is_null( $this->min_wc_version ) && $this->can_check_plugin_version() && ! $this->is_wc_at_least( $this->min_wc_version ) ) {
			$notices[] = $this->prepare_notice_message( sprintf( __( 'The &#8220;%s&#8221; plugin cannot run on WooCommerce versions older than %s. Please update WooCommerce.',
				$this->get_text_domain() ), esc_html( $this->plugin_name ), $this->min_wc_version ) );
		}
		if ( ! is_null( $this->min_openssl_version ) && ! $this->is_open_ssl_at_least( $this->min_openssl_version ) ) {
			$notices[] = $this->prepare_notice_message( sprintf( __( 'The &#8220;%s&#8221; plugin cannot run without OpenSSL module version at least %s. Please update OpenSSL module.',
				$this->get_text_domain() ), esc_html( $this->plugin_name ),
				'0x' . dechex( $this->min_openssl_version ) ) );
		}

		$notices = $this->append_plugin_require_notices( $notices );
		$notices = $this->append_module_require_notices( $notices );
		$notices = $this->append_settings_require_notices( $notices );

		return $notices;
	}

	/**
	 * @param $min_version
	 *
	 * @return mixed
	 */
	public static function is_php_at_least( $min_version ) {
		return version_compare( phpversion(), $min_version, '>=' );
	}

	/**
	 * Prepares message in html format
	 *
	 * @param string $message
	 *
	 * @return string
	 */
	private function prepare_notice_message( $message ) {
		return '<div class="error"><p>' . $message . '</p></div>';
	}

	public function get_text_domain() {
		return $this->text_domain;
	}

	/**
	 * @param string $min_version
	 *
	 * @return bool
	 */
	public static function is_wp_at_least( $min_version ) {
		return version_compare( get_bloginfo( 'version' ), $min_version, '>=' );
	}

	/**
	 * Are plugins loaded so we can check the version
	 *
	 * @return bool
	 */
	private function can_check_plugin_version() {
		return did_action( 'plugins_loaded' ) > 0;
	}

	/**
	 * Checks if plugin is active and have designated version. Needs to be enabled in deferred way.
	 *
	 * @param string $min_version
	 *
	 * @return bool
	 */
	public static function is_wc_at_least( $min_version ) {
		return defined( 'WC_VERSION' ) &&
		       version_compare( WC_VERSION, $min_version, '>=' );
	}

	/**
	 * Checks if ssl version is valid
	 *
	 * @param int $required_version Version in hex. Version 9.6 is 0x000906000
	 *
	 * @see https://www.openssl.org/docs/man1.1.0/crypto/OPENSSL_VERSION_NUMBER.html
	 *
	 * @return bool
	 */
	public static function is_open_ssl_at_least( $required_version ) {
		return defined( 'OPENSSL_VERSION_NUMBER' ) && OPENSSL_VERSION_NUMBER > (int) $required_version;
	}

	/**
	 * @param array $notices
	 *
	 * @return array
	 */
	private function append_plugin_require_notices( $notices ) {
		if ( count( $this->plugin_require ) > 0 ) {
			foreach ( $this->plugin_require as $plugin_name => $nice_plugin_name ) {
				if ( ! $this->is_wp_plugin_active( $plugin_name ) ) {
					$notices[] = $this->prepare_notice_message( sprintf( __( 'The &#8220;%s&#8221; plugin cannot run without %s active. Please install and activate %s plugin.',
						$this->get_text_domain() ), esc_html( $this->plugin_name ),
						esc_html( basename( $nice_plugin_name ) ), esc_html( basename( $nice_plugin_name ) ) ) );
				}
			}
		}

		return $notices;
	}

	/**
	 * Checks if plugin is active. Needs to be enabled in deferred way.
	 *
	 * @param string $plugin_file
	 *
	 * @return bool
	 */
	public static function is_wp_plugin_active( $plugin_file ) {
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( $plugin_file, $active_plugins ) || array_key_exists( $plugin_file, $active_plugins );
	}

	/**
	 * @param array $notices
	 *
	 * @return array
	 */
	private function append_module_require_notices( $notices ) {
		if ( count( $this->module_require ) > 0 ) {
			foreach ( $this->module_require as $module_name => $nice_module_name ) {
				if ( ! $this->is_module_active( $module_name ) ) {
					$notices[] = $this->prepare_notice_message( sprintf( __( 'The &#8220;%s&#8221; plugin cannot run without %s php module installed. Please contact your host and ask them to install %s.',
						$this->get_text_domain() ), esc_html( $this->plugin_name ),
						esc_html( basename( $nice_module_name ) ), esc_html( basename( $nice_module_name ) ) ) );
				}
			}
		}

		return $notices;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function is_module_active( $name ) {
		return extension_loaded( $name );
	}

	/**
	 * @param array $notices
	 *
	 * @return array
	 */
	private function append_settings_require_notices( $notices ) {
		if ( count( $this->setting_require ) > 0 ) {
			foreach ( $this->setting_require as $setting => $value ) {
				if ( ! $this->is_setting_set( $setting, $value ) ) {
					$notices[] = $this->prepare_notice_message( sprintf( __( 'The &#8220;%s&#8221; plugin cannot run without %s php setting set to %s. Please contact your host and ask them to set %s.',
						$this->get_text_domain() ), esc_html( $this->plugin_name ), esc_html( basename( $setting ) ),
						esc_html( basename( $value ) ), esc_html( basename( $setting ) ) ) );
				}
			}
		}

		return $notices;
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public static function is_setting_set( $name, $value ) {
		return ini_get( $name ) === strval( $value );
	}

	/**
	 * @return void
	 */
	public function disable_plugin_render_notice() {
		add_action( self::HOOK_ADMIN_NOTICES_ACTION, array( $this, 'render_notices_action' ) );
	}

	/**
	 * Shoud be called as WordPress action
	 *
	 * @return void
	 */
	public function render_notices_action() {
		foreach ( $this->notices as $notice ) {
			echo $notice;
		}
	}
}
