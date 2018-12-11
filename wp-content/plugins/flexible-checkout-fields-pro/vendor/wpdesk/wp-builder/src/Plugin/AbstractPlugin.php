<?php

namespace WPDesk\PluginBuilder\Plugin;

/**
 * Base plugin class for WP Desk plugins.
 *
 * *************************************************************
 * * Important! This class should be not modified!             *
 * * This class is loaded at startup from first loaded plugin! *
 * *************************************************************
 *
 * @author Grzegorz, Dyszczo
 *
 */
abstract class AbstractPlugin implements \WPDesk_Translable {

	/** @var \WPDesk_Plugin_Info */
	protected $plugin_info;

	/** @var string */
	protected $plugin_namespace;

	/** @var string */
	protected $plugin_url;

	/** @var string */
	protected $docs_url;

	/** @var string */
	protected $settings_url;

	/**
	 * AbstractPlugin constructor.
	 *
	 * @param \WPDesk_Plugin_Info $plugin_info
	 */
	public function __construct( $plugin_info ) {
		$this->plugin_info      = $plugin_info;
		$this->plugin_namespace = strtolower( $plugin_info->get_plugin_dir() );
	}

	public function init() {
		$this->init_base_variables();
		$this->hooks();
	}

	public function init_base_variables() {
		$this->plugin_url = plugin_dir_url( $this->plugin_info->get_plugin_dir() );
	}

	/**
	 * @return void
	 */
	protected function hooks() {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ] );

		add_action( 'plugins_loaded', [ $this, 'load_plugin_text_domain' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( $this->get_plugin_file_path() ), [
			$this,
			'links_filter'
		] );

	}

	/**
	 * @return string
	 */
	public function get_plugin_file_path() {
		return $this->plugin_info->get_plugin_file_name();
	}

	/**
	 * @return $this
	 */
	public function get_plugin() {
		return $this;
	}

	/**
	 * @return void
	 */
	public function load_plugin_text_domain() {
		load_plugin_textdomain( $this->get_text_domain(), false, $this->get_namespace() . '/lang/' );
	}

	/**
	 * @return string
	 */
	public function get_text_domain() {
		return $this->plugin_info->get_text_domain();
	}

	/**
	 * @return string
	 */
	public function get_namespace() {
		return $this->plugin_namespace;
	}

	public function get_plugin_assets_url() {
		return esc_url( trailingslashit( $this->get_plugin_url() . 'assets' ) );
	}

	/**
	 *
	 * @return string
	 */
	public function get_plugin_url() {
		return esc_url( trailingslashit( $this->plugin_url ) );
	}

	public function admin_enqueue_scripts() {
	}

	public function wp_enqueue_scripts() {
	}

	/**
	 * action_links function.
	 *
	 * @access public
	 *
	 * @param mixed $links
	 *
	 * @return array
	 */
	public function links_filter( $links ) {
		$support_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/support/' : 'https://www.wpdesk.net/support';

		$plugin_links = [
			'<a href="' . $support_link . '">' . __( 'Support', $this->get_text_domain() ) . '</a>',
		];
		$links        = array_merge( $plugin_links, $links );

		if ( $this->docs_url ) {
			$plugin_links = [
				'<a href="' . $this->docs_url . '">' . __( 'Docs', $this->get_text_domain() ) . '</a>',
			];
			$links        = array_merge( $plugin_links, $links );
		}

		if ( $this->settings_url ) {
			$plugin_links = [
				'<a href="' . $this->settings_url . '">' . __( 'Settings', $this->get_text_domain() ) . '</a>',
			];
			$links        = array_merge( $plugin_links, $links );
		}

		return $links;
	}

}

