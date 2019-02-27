<?php

/**
 * Class Flexible_Checkout_Fields_Pro_Plugin
 */
class Flexible_Checkout_Fields_Pro_Plugin
	extends \WPDesk\PluginBuilder\Plugin\AbstractPlugin
	implements \WPDesk\PluginBuilder\Plugin\HookableCollection {

	use \WPDesk\PluginBuilder\Plugin\HookableParent;
	use \WPDesk\PluginBuilder\Plugin\TemplateLoad;

	/**
	 * Scripts version.
	 *
	 * @var string
	 */
	private $script_version = '29';

	/**
	 * PRO Types.
	 *
	 * @var Flexible_Checkout_Fields_Pro_Types
	 */
	public $pro_types;

	/**
	 * Plugin is active?
	 *
	 * @var bool
	 */
	private $plugin_is_active = true;

	/**
	 * Renderer.
	 *
	 * @var WPDesk\View\Renderer\Renderer;
	 */
	private $renderer;

	/**
	 * Flexible_Checkout_Fields_Plugin
	 *
	 * @var Flexible_Checkout_Fields_Plugin
	 */
	private $flexible_checkout_fields_plugin;

	/**
	 * Select field type.
	 *
	 * @var Flexible_Checkout_Fields_Pro_Select_Field_Type
	 */
	private $select_field_type;

	/**
	 * Multi Select field type.
	 *
	 * @var Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type
	 */
	private $multi_select_field_type;

	/**
	 * Radio field type.
	 *
	 * @var Flexible_Checkout_Fields_Pro_Inspire_Radio_Field_Type
	 */
	private $radio_field_type;

	/**
	 * File field type.
	 *
	 * @var Flexible_Checkout_Fields_Pro_File_Field_Type
	 */
	private $file_field_type;

	/**
	 * HTML field type.
	 *
	 * @var Flexible_Checkout_Fields_Pro_HTML_Field_Type
	 */
	private $html_field_type;


	/**
	 * Flexible_Checkout_Fields_Pro_Plugin constructor.
	 *
	 * @param WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		parent::__construct( $plugin_info );
		$this->check_activation();
		$this->flexible_checkout_fields_plugin = flexible_checkout_fields();
	}

	/**
	 * Get select_field_type.
	 *
	 * @return Flexible_Checkout_Fields_Pro_Select_Field_Type
	 */
	public function get_select_field_type() {
		return $this->select_field_type;
	}

	/**
	 * Get multi_select_field_type.
	 *
	 * @return Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type
	 */
	public function get_multi_select_field_type() {
		return $this->multi_select_field_type;
	}

	/**
	 * Get radio_field_type.
	 *
	 * @return Flexible_Checkout_Fields_Pro_Inspire_Radio_Field_Type
	 */
	public function get_radio_field_type() {
		return $this->radio_field_type;
	}

	/**
	 * Get file_field_type.
	 *
	 * @return Flexible_Checkout_Fields_Pro_File_Field_Type
	 */
	public function get_file_field_type() {
		return $this->file_field_type;
	}



	/**
	 * Get flexible checkout fields plugin.
	 *
	 * @return Flexible_Checkout_Fields_Plugin
	 */
	public function get_flexible_checkout_fields_plugin() {
		return $this->flexible_checkout_fields_plugin;
	}

	/**
	 * Get script version.
	 *
	 * @return string;
	 */
	public function get_script_version() {
		return $this->script_version;
	}

	/**
	 * Check plugin activation.
	 */
	private function check_activation() {
		if ( ! class_exists( 'WPDesk_Helper_Plugin' ) ) {
			require_once 'wpdesk/class-helper.php';
			add_filter( 'plugins_api', array( $this, 'wpdesk_helper_install' ), 10, 3 );
			add_action( 'admin_notices', array( $this, 'wpdesk_helper_notice' ) );
		}
		$plugin_data = array(
			'plugin'     => $this->plugin_info->get_plugin_file_name(),
			'product_id' => $this->plugin_info->get_product_id(),
			'version'    => FLEXIBLE_CHECKOUT_FIELDS_PRO_VERSION,
			'config_uri' => admin_url( 'admin.php?page=inspire_checkout_fields_settings' ),
		);
		$helper      = new WPDesk_Helper_Plugin( $plugin_data );
		if ( ! $helper->is_active() ) {
			$this->plugin_is_active = false;
		}
	}

	/**
	 * Get plugin is active.
	 *
	 * @return bool
	 */
	public function get_plugin_is_active() {
		return $this->plugin_is_active;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		parent::hooks();
		$this->hooks_on_hookable_objects();
	}

	/**
	 * Init base variables for plugin
	 */
	public function init_base_variables() {
		$this->plugin_url = $this->plugin_info->get_plugin_url();

		$this->plugin_path   = $this->plugin_info->get_plugin_dir();
		$this->template_path = $this->plugin_info->get_text_domain();

		$this->plugin_namespace = $this->plugin_info->get_text_domain();
		$this->template_path    = $this->plugin_info->get_text_domain();


	}

	/**
	 * Set renderer.
	 */
	private function init_renderer() {
		$resolver = new \WPDesk\View\Resolver\ChainResolver();
		$resolver->appendResolver( new \WPDesk\View\Resolver\WPThemeResolver( $this->get_template_path() ) );
		$resolver->appendResolver( new \WPDesk\View\Resolver\DirResolver( trailingslashit( $this->plugin_path ) . 'templates' ) );
		$this->renderer = new WPDesk\View\Renderer\SimplePhpRenderer( $resolver );
	}

	/**
	 * Init plugin functionality.
	 */
	public function init() {
		if ( $this->plugin_is_active ) {
			$this->init_base_variables();

			$this->init_renderer();

			$checkout_fields_pro = new Flexible_Checkout_Fields_Pro( $this );

			$this->add_hookable( new Flexible_Checkout_Fields_Conditional_Logic() );

			$this->add_hookable( new Flexible_Checkout_Fields_Conditional_Logic_Checkout( $this ) );

			$this->add_hookable( new Flexible_Checkout_Fields_Conditional_Logic_Order( $this ) );

			new Flexible_Checkout_Fields_Pro_Docs_Metabox( $this );

			$this->select_field_type = new Flexible_Checkout_Fields_Pro_Select_Field_Type( $checkout_fields_pro, $this->renderer );
			$this->add_hookable( $this->select_field_type );

			$this->html_field_type = new Flexible_Checkout_Fields_Pro_HTML_Field_Type( $checkout_fields_pro, $this->renderer );
			$this->add_hookable( $this->html_field_type );

			$this->multi_select_field_type = new Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type( $checkout_fields_pro, $this->renderer );
			$this->add_hookable( $this->multi_select_field_type );
			$this->radio_field_type = new Flexible_Checkout_Fields_Pro_Inspire_Radio_Field_Type( $checkout_fields_pro, $this->renderer );
			$this->add_hookable( $this->radio_field_type );

			$this->file_field_type = new Flexible_Checkout_Fields_Pro_File_Field_Type( $checkout_fields_pro, $this->renderer );
			$this->add_hookable( $this->file_field_type );
			$this->add_hookable( new Flexible_Checkout_Fields_Pro_File_Field_Downloader() );
			$this->add_hookable( new Flexible_Checkout_Fields_Pro_File_Field_Ajax( $checkout_fields_pro ) );
			$this->add_hookable( new Flexible_Checkout_Fields_Pro_File_Field_Order_Metabox( $checkout_fields_pro ) );

			$this->pro_types = new Flexible_Checkout_Fields_Pro_Types( $this );

			$this->add_hookable( new Flexible_Checkout_Fields_Order_Metabox( $checkout_fields_pro, $this->get_flexible_checkout_fields_plugin() ) );

			parent::init();
		}
	}


	/**
	 * Links filter.
	 *
	 * @param array $links Links.
	 * @return array
	 */
	public function links_filter( $links ) {
		$docs_link    = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/docs//docs/flexible-checkout-fields-docs/' : 'https://www.wpdesk.net/docs/flexible-checkout-fields-docs/';
		$support_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/support/' : 'https://www.wpdesk.net/support';
		$plugin_links = array();

		if ( defined( 'WC_VERSION' ) ) {
			$plugin_links[] = '<a href="' . admin_url( 'admin.php?page=inspire_checkout_fields_settings' ) . '">' . __( 'Settings', 'flexible-checkout-fields-pro' ) . '</a>';
		}
		$plugin_links[] = '<a href="' . $docs_link . '">' . __( 'Docs', 'flexible-checkout-fields-pro' ) . '</a>';
		$plugin_links[] = '<a href="' . $support_link . '">' . __( 'Support', 'flexible-checkout-fields-pro' ) . '</a>';

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Renders end returns selected template
	 *
	 * @param string $name Name of the template.
	 * @param string $path Additional inner path to the template.
	 * @param array  $args args Accessible from template.
	 *
	 * @return string
	 */
	public function load_template( $name, $path = '', $args = array() ) {
		if ( '' !== $path ) {
			$template = trailingslashit( $path ) . $name;
		} else {
			$template = $name;
		}
		return $this->renderer->render( $template, $args );
	}

	/**
	 * Should enqueue admin scripts.
	 * Script should be loaded on FCF Settings and order edit.
	 *
	 * @return bool
	 */
	private function should_enqueue_admin_scripts() {
		$current_screen = get_current_screen();
		if ( isset( $current_screen )
			&& ( in_array( $current_screen->id, array(
				'shop_order',
				'woocommerce_page_inspire_checkout_fields_settings',
			), true ) )
		) {
			return true;
		}
		return false;
	}

	/**
	 * Enqueue admin scripts.
	 */
	public function admin_enqueue_scripts() {
		if ( $this->should_enqueue_admin_scripts() ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'inspire_checkout_fields_colorpicker_style',
				trailingslashit( $this->get_plugin_assets_url() ) . 'css/colorpicker' . $suffix . '.css',
				array(),
				$this->script_version
			);
			wp_enqueue_style( 'inspire_checkout_fields_timepicker_style',
				trailingslashit( $this->get_plugin_assets_url() ) . 'css/jquery.timeselector' . $suffix . '.css',
				array(),
				$this->script_version
			);

			wp_enqueue_script( 'inspire_checkout_fields_colorpicker_js',
				trailingslashit( $this->get_plugin_assets_url() ) . 'js/colorpicker' . $suffix . '.js',
				array( 'jquery' ),
				$this->script_version
			);
			wp_enqueue_script( 'inspire_checkout_fields_timepicker_js',
				trailingslashit( $this->get_plugin_assets_url() ) . 'js/jquery.timeselector' . $suffix . '.js',
				array( 'jquery' ), $this->script_version
			);

			wp_enqueue_style( 'inspire_checkout_fields_pro_admin_style',
				trailingslashit( $this->get_plugin_assets_url() ) . 'css/admin' . $suffix . '.css',
				array(), $this->script_version
			);
			wp_enqueue_script( 'inspire_checkout_fields_pro_admin_pro_js',
				trailingslashit( $this->get_plugin_assets_url() ) . 'js/admin' . $suffix . '.js',
				array( 'jquery' ), $this->script_version
			);
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function wp_enqueue_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( is_checkout() || is_account_page() ) {
			wp_enqueue_script( 'inspire_checkout_fields_front_pro_js',
				trailingslashit( $this->get_plugin_assets_url() ) . 'js/front' . $suffix . '.js',
				array( 'jquery' ), $this->script_version
			);
			wp_enqueue_style( 'inspire_checkout_fields_colorpicker_style',
				trailingslashit( $this->get_plugin_assets_url() ) . 'css/colorpicker' . $suffix . '.css',
				array(), $this->script_version
			);
			wp_enqueue_style( 'inspire_checkout_fields_timepicker_style',
				trailingslashit( $this->get_plugin_assets_url() ) . 'css/jquery.timeselector' . $suffix . '.css',
				array(), $this->script_version
			);

			wp_enqueue_script( 'inspire_checkout_fields_colorpicker_js',
				trailingslashit( $this->get_plugin_assets_url() ) . 'js/colorpicker' . $suffix . '.js',
				array( 'jquery' ), $this->script_version
			);
			wp_enqueue_script( 'inspire_checkout_fields_timepicker_js',
				trailingslashit( $this->get_plugin_assets_url() ) . 'js/jquery.timeselector' . $suffix . '.js',
				array( 'jquery' ), $this->script_version
			);
		}
		if ( is_checkout() ) {
			wp_enqueue_script( 'inspire_checkout_fields_pro_checkout_js',
				trailingslashit( $this->get_plugin_assets_url() ) . 'js/checkout' . $suffix . '.js',
				array( 'jquery' ), $this->script_version, true
			);
		}

	}

	/**
	 * Load installer for the WP Desk Helper.
	 *
	 * @param object $api API.
	 * @param string $action Action.
	 * @param object $args Args.
	 *
	 * @return stdClass
	 */
	public function wpdesk_helper_install( $api, $action, $args ) {
		$download_url = 'http://www.wpdesk.pl/wp-content/uploads/wpdesk-helper.zip';

		if ( 'plugin_information' !== $action || false !== $api || ! isset( $args->slug ) || 'wpdesk-helper' !== $args->slug ) {
			return $api;
		}

		$api                = new stdClass();
		$api->name          = 'WP Desk Helper';
		$api->version       = '1.0';
		$api->download_link = esc_url( $download_url );
		return $api;
	}

	/**
	 * Display a notice if the "WP Desk Helper" plugin hasn't been installed.
	 */
	public function wpdesk_helper_notice() {

		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
		if ( in_array( 'wpdesk-helper/wpdesk-helper.php', $active_plugins, true ) ) {
			return;
		}

		$slug         = 'wpdesk-helper';
		$install_url  = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
		$activate_url = 'plugins.php?action=activate&plugin='
		                . rawurlencode( 'wpdesk-helper/wpdesk-helper.php' )
		                . '&plugin_status=all&paged=1&s&_wpnonce='
		                . rawurlencode( wp_create_nonce( 'activate-plugin_wpdesk-helper/wpdesk-helper.php' ) );

		$message = sprintf( wp_kses( __( '<a href="%s">Install the WP Desk Helper plugin</a> to activate and get updates for your WP Desk plugins.', 'flexible-checkout-fields-pro' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $install_url ) );
		$plugins = array_keys( get_plugins() );
		foreach ( $plugins as $plugin ) {
			if ( strpos( $plugin, 'wpdesk-helper.php' ) !== false ) {
				$message = sprintf( wp_kses( __( '<a href="%s">Activate the WP Desk Helper plugin</a> to activate and get updates for your WP Desk plugins.', 'flexible-checkout-fields-pro' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( $activate_url ) ) );
			}
		}
		echo '<div class="error fade"><p>' . $message . '</p></div>' . "\n";
	}

}
