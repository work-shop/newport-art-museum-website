<?php
/**
 * WooCommerce setup
 *
 * @package WooCommerce
 * @since   3.2.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Main WC_Name_Your_Price class.
 *
 * @class WC_Name_Your_Price
 **/
final class WC_Name_Your_Price {

	/**
	 * @var WC_Name_Your_Price - the single instance of the class
	 * @since 2.0
	 */
	protected static $_instance = null;           

	/**
	 * @var plugin version
	 * @since 2.0
	 */
	public $version = '2.9.2';   

	/**
	 * @var required WooCommerce version
	 * @since 2.1
	 */
	public $required_woo = '3.0.0';

	/**
	 * Main WC_Name_Your_Price Instance.
	 *
	 * Ensures only one instance of WC_Name_Your_Price is loaded or can be loaded.
	 *
	 * @static
	 * @see WC_Name_Your_Price()
	 * @return WC_Name_Your_Price - Main instance
	 * @since 2.0
	 */
	public static function instance() {

		if ( ! did_action( 'woocommerce_loaded' ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'You cannot initialize Name Your Price before WooCommerce is loaded.', 'wc_name_your_price' ), null );
		} elseif ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning this object is forbidden.', 'wc_name_your_price' ) );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'wc_name_your_price' ) );
	}
  
	/**
	 * WC_Name_Your_Price Constructor.
	 *
	 * @access public
     * @return WC_Name_Your_Price
	 * @since 1.0
	 */
	public function __construct() { 
		$this->includes();
		$this->init_hooks();
    }

	/**
	 * Hook into actions and filters.
	 *
	 * @since 3.0
	 */
	private function init_hooks() {
		// Load translation files
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		// Settings Link for Plugin page
		add_filter( 'plugin_action_links_' . plugin_basename( WC_NYP_PLUGIN_FILE ), array( $this, 'add_action_link' ), 10, 2 );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Helper Functions */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 * @since  2.0
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', WC_NYP_PLUGIN_FILE ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 * @since  2.0
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( WC_NYP_PLUGIN_FILE ) );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Required Files */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @return void
	 * @since  1.0
	 */
	public function includes(){

		// include all helper functions
		include_once( 'class-wc-name-your-price-helpers.php' );

		// include admin class to handle all backend functions
		if( is_admin() ){
			$this->admin_includes();
		}

		// include the front-end functions
		if ( ! is_admin() || defined('DOING_AJAX') ) {
			include_once( 'class-wc-name-your-price-display.php' );
			$this->display = new WC_Name_Your_Price_Display();

			include_once( 'class-wc-name-your-price-cart.php' );
			$this->cart = new WC_Name_Your_Price_Cart();

			include_once( 'class-wc-name-your-price-order.php' );
			$this->order = new WC_Name_Your_Price_Order();

		}

		include_once( 'compatibility/class-wc-name-your-price-compatibility.php' );
		$this->compatibility = new WC_Name_Your_Price_Compatibility();

		// Include deprecated functions.
		include_once( 'wc-nyp-deprecated-functions.php' );

		do_action( 'wc_name_your_price_loaded' );

	}


	/**
	 * Displays a warning message if version check fails.
	 * @return string
	 * @since  2.1
	 * @deprecated 3.0.0
	 */
	public function admin_notice() {
		_deprecated_function( __FUNCTION__, '3.0.0', 'WC_Name_Your_Price_Core_Compatibility::admin_environment_failure_notice' );
		return WC_Name_Your_Price_Core_Compatibility::admin_environment_failure_notice();
	}


	/**
	 * Load the admin files.
	 * @return void
	 * @since  2.2
	 */
	public function admin_includes() {
	    include_once( 'admin/class-name-your-price-admin.php' );
	}


	/*-----------------------------------------------------------------------------------*/
	/* Localization */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/wc_name_your_price/wc_name_your_price-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/wc_name_your_price-LOCALE.mo
	 *      - WP_CONTENT_DIR/plugins/woocommerce-name-your-price/languages/wc_name_your_price-LOCALE.mo
	 *
	 * @return void
	 * @since  1.0
	 */
	public function load_plugin_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'wc_name_your_price' );

		load_textdomain( 'wc_name_your_price', WP_LANG_DIR . '/wc_name_your_price/wc_name_your_price-' . $locale . '.mo' );
		load_plugin_textdomain( 'wc_name_your_price', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	}

	/*-----------------------------------------------------------------------------------*/
	/* Plugins Page */
	/*-----------------------------------------------------------------------------------*/

	/*
	 * 'Settings' link on plugin page
	 *
	 * @param array $links
	 * @return array
	 * @since 1.0
	 */
	public function add_action_link( $links ) {
		$settings_link = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=nyp' ). '" title="' . __( 'Go to the settings page', 'wc_name_your_price' ). '">'.__( 'Settings', 'wc_name_your_price' ).'</a>';
		return array_merge( (array) $settings_link, $links );

	}

} //end class: do not remove or there will be no more guacamole for you


/**
 * Returns the main instance of WC_Name_Your_Price to prevent the need to use globals.
 *
 * @since  2.0
 * @return WC_Name_Your_Price
 */
function WC_Name_Your_Price() {
  return WC_Name_Your_Price::instance();
}
