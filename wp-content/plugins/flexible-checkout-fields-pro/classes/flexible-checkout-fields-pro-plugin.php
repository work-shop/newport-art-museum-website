<?php

class Flexible_Checkout_Fields_Pro_Plugin extends WPDesk_Plugin_1_8 {

	private $script_version = '22';

	public $pro_types;

	public function __construct( $base_file, $plugin_data ) {

		$this->plugin_namespace = 'flexible-checkout-fields-pro';
		$this->plugin_text_domain = 'flexible-checkout-fields-pro';

		$this->plugin_has_settings = false;
		if ( is_array( $plugin_data ) && count( $plugin_data ) ) {
			if ( ! class_exists( 'WPDesk_Helper_Plugin' ) ) {
				require_once( 'wpdesk/class-helper.php' );
				add_filter( 'plugins_api', array( $this, 'wpdesk_helper_install' ), 10, 3 );
				add_action( 'admin_notices', array( $this, 'wpdesk_helper_notice' ) );
			}
			$helper = new WPDesk_Helper_Plugin( $plugin_data );
			if ( !$helper->is_active() ) {
				$this->plugin_is_active = false;
			}
		}

		parent::__construct( $base_file, $plugin_data );
		if ( $this->plugin_is_active() ) {
			require_once( plugin_basename( 'flexible-checkout-fields-pro.php' ) );
			$flexible_checkout_fields_pro = new Flexible_Checkout_Fields_Pro( $this );

			require_once( plugin_basename( 'flexible-checkout-fields-conditional-logic.php' ) );
			$flexible_checkout_fields_conditional_logic = new Flexible_Checkout_Fields_Conditional_Logic( $this );

			require_once( plugin_basename( 'flexible-checkout-fields-conditional-logic-checkout.php' ) );
			$flexible_checkout_fields_conditional_logic_checkout = new Flexible_Checkout_Fields_Conditional_Logic_Checkout( $this );
			$flexible_checkout_fields_conditional_logic_checkout->hooks();

			require_once( plugin_basename( 'flexible-checkout-fields-conditional-logic-order.php' ) );
			$flexible_checkout_fields_conditional_logic_order = new Flexible_Checkout_Fields_Conditional_Logic_Order( $this );
			$flexible_checkout_fields_conditional_logic_order->hooks();

			require_once( plugin_basename( 'flexible-checkout-fields-pro-types.php' ) );
			$this->pro_types = new Flexible_Checkout_Fields_Pro_Types( $this );
			require_once( plugin_basename( 'flexible-checkout-fields-pro-docs-metabox.php' ) );
			new Flexible_Checkout_Fields_Pro_Docs_Metabox( $this );
			$this->init();
			$this->hooks();
		}

	}

	public function init() {
	}


	/**
	 * action_links function.
	 *
	 * @access public
	 * @param mixed $links
	 * @return void
	 */
	public function links_filter( $links ) {
		$docs_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/docs//docs/flexible-checkout-fields-docs/' : 'https://www.wpdesk.net/docs/flexible-checkout-fields-docs/';
		$support_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/support/' : 'https://www.wpdesk.net/support';
		$plugin_links = array();

		if ( defined( 'WC_VERSION' ) ) {
			$plugin_links[] = '<a href="' . admin_url( 'admin.php?page=inspire_checkout_fields_settings') . '">' . __( 'Settings', 'flexible-checkout-fields-pro' ) . '</a>';
		}
		$plugin_links[] = '<a href="' . $docs_link . '">' . __( 'Docs', 'flexible-checkout-fields-pro' ) . '</a>';
		$plugin_links[] = '<a href="' . $support_link . '">' . __( 'Support', 'flexible-checkout-fields-pro' ) . '</a>';

		return array_merge( $plugin_links, $links );
	}


	public function admin_enqueue_scripts( $hook ) {

		wp_enqueue_style( 'inspire_checkout_fields_colorpicker_style', trailingslashit( $this->get_plugin_assets_url() ) . 'css/colorpicker.css', array(), $this->script_version );
		wp_enqueue_style( 'inspire_checkout_fields_timepicker_style', trailingslashit( $this->get_plugin_assets_url() ) . 'css/jquery.timeselector.css', array(), $this->script_version );

		wp_enqueue_script( 'inspire_checkout_fields_colorpicker_js', trailingslashit( $this->get_plugin_assets_url() ) . 'js/colorpicker.js', array( 'jquery' ), $this->script_version );
		wp_enqueue_script( 'inspire_checkout_fields_timepicker_js', trailingslashit( $this->get_plugin_assets_url() ) . 'js/jquery.timeselector.js', array( 'jquery' ), $this->script_version );

		wp_enqueue_script( 'inspire_checkout_fields_admin_pro_js', trailingslashit( $this->get_plugin_assets_url() ) . 'js/admin.js', array( 'jquery' ), $this->script_version );

	}

	public function wp_enqueue_scripts() {

		if ( is_checkout() || is_account_page() ) {
			wp_enqueue_script( 'inspire_checkout_fields_front_pro_js', trailingslashit( $this->get_plugin_assets_url() ) . 'js/front.js', array( 'jquery' ), $this->script_version );
			wp_enqueue_style( 'inspire_checkout_fields_colorpicker_style', trailingslashit( $this->get_plugin_assets_url() ) . 'css/colorpicker.css', array(), $this->script_version );
			wp_enqueue_style( 'inspire_checkout_fields_timepicker_style', trailingslashit( $this->get_plugin_assets_url() ) . 'css/jquery.timeselector.css', array(), $this->script_version );

			wp_enqueue_script( 'inspire_checkout_fields_colorpicker_js', trailingslashit( $this->get_plugin_assets_url() ) . 'js/colorpicker.js', array( 'jquery' ), $this->script_version );
			wp_enqueue_script( 'inspire_checkout_fields_timepicker_js', trailingslashit( $this->get_plugin_assets_url() ) . 'js/jquery.timeselector.js', array( 'jquery' ), $this->script_version );
		}
		if ( is_checkout() ) {
			wp_enqueue_script( 'inspire_checkout_fields_pro_checkout_js', trailingslashit( $this->get_plugin_assets_url() ) . 'js/checkout.js', array( 'jquery' ), $this->script_version, true );
		}

	}

	/**
	 * Load installer for the WP Desk Helper.
	 * @return $api Object
	 */
	function wpdesk_helper_install( $api, $action, $args ) {
		$download_url = 'http://www.wpdesk.pl/wp-content/uploads/wpdesk-helper.zip';

		if ( 'plugin_information' != $action ||
		     false !== $api ||
		     ! isset( $args->slug ) ||
		     'wpdesk-helper' != $args->slug
		) return $api;

		$api = new stdClass();
		$api->name = 'WP Desk Helper';
		$api->version = '1.0';
		$api->download_link = esc_url( $download_url );
		return $api;
	}

	/**
	 * Display a notice if the "WP Desk Helper" plugin hasn't been installed.
	 * @return void
	 */
	function wpdesk_helper_notice() {

		$active_plugins = apply_filters( 'active_plugins', get_option('active_plugins' ) );
		if ( in_array( 'wpdesk-helper/wpdesk-helper.php', $active_plugins ) ) return;

		$slug = 'wpdesk-helper';
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
		$activate_url = 'plugins.php?action=activate&plugin=' . urlencode( 'wpdesk-helper/wpdesk-helper.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode( wp_create_nonce( 'activate-plugin_wpdesk-helper/wpdesk-helper.php' ) );

		$message = sprintf( wp_kses( __( '<a href="%s">Install the WP Desk Helper plugin</a> to activate and get updates for your WP Desk plugins.', 'flexible-checkout-fields-pro' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( $install_url ) );
		$plugins = array_keys( get_plugins() );
		foreach ( $plugins as $plugin ) {
			if ( strpos( $plugin, 'wpdesk-helper.php' ) !== false ) {
				$message = sprintf( wp_kses( __( '<a href="%s">Activate the WP Desk Helper plugin</a> to activate and get updates for your WP Desk plugins.', 'flexible-checkout-fields-pro' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( admin_url( $activate_url ) ) );
			}
		}
		echo '<div class="error fade"><p>' . $message . '</p></div>' . "\n";
	}


}

