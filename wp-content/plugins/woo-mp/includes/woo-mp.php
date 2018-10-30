<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

class Woo_MP {

    public function __construct() {
        register_shutdown_function( [ $this, 'maybe_output_error' ] );

        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Ensure that fatal errors are outputted when running AJAX operations.
     * 
     * Enabling the 'display_errors' option would cause all errors that match the criteria
     * in 'error_reporting' to be displayed. We only want to output fatal errors.
     * Setting the 'error_reporting' option would interfere with logging.
     * 
     * This is not a security risk as the plugin only loads in the Administration Screens.
     */
    public function maybe_output_error() {
        $error             = error_get_last();
        $fatal_error_types = [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR ];

        if (
            $error &&
            in_array( $error['type'], $fatal_error_types ) &&
            ( ! ini_get( 'display_errors' ) || ! ( error_reporting() & $error['type'] ) ) &&
            defined( 'DOING_AJAX' ) &&
            isset( $_REQUEST['action'] ) &&
            strpos( $_REQUEST['action'], 'woo_mp_' ) === 0
        ) {
            printf(
                '<b>%s error</b>: %s in <b>%s</b> on line <b>%s</b>',
                $error['type'] === E_PARSE ? 'Parse' : 'Fatal',
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

	private function define_constants() {
        define( 'WOO_MP_PAYMENT_PROCESSOR', str_replace( '_', '-', get_option( 'woo_mp_payment_processor' ) ) );
        define( 'WOO_MP_CONFIG_HELP', 'If you need help, you can find instructions <a href="https://wordpress.org/plugins/woo-mp/#installation" target="_blank">here</a>.' );
        define( 'WOO_MP_PRO', defined( 'WOO_MP_PRO_URL' ) );
	}

    private function includes() {
        spl_autoload_register( [ $this, 'autoload' ] );

        require WOO_MP_PATH . '/includes/functions.php';
        require WOO_MP_PATH . '/includes/notices.php';
        require WOO_MP_PATH . '/includes/upgrade-notices.php';
        require WOO_MP_PATH . '/includes/update-routines.php';
        require WOO_MP_PATH . '/includes/ajax.php';
        require WOO_MP_PATH . '/includes/woo-mp-order.php';
    }

    /**
     * Autoloader following the WordPress file naming convention, but without any 'class-' prefixes.
     * 
     * @param string $name The name of the class, interface, or trait to load. 
     */
    public function autoload( $name ) {
        $namespace_prefix = 'Woo_MP\\';
        $base_directory   = WOO_MP_PATH . '/includes/';

        if ( strpos( $name, $namespace_prefix ) !== 0 ) {
            return;
        }

        $path = substr( $name, strlen( $namespace_prefix ) );
        $path = str_replace( ['\\', '_'], ['/', '-'], strtolower( $path ) );
        $path = $base_directory . $path . '.php';

        if ( is_readable( $path ) ) {
            require $path;
        }
    }

    private function init_hooks() {
        add_action( 'in_admin_header', [ $this, 'setup_notice' ] );
        add_filter( 'plugin_action_links_' . WOO_MP_BASENAME, [ $this, 'add_action_links' ] );
        add_action( 'add_meta_boxes_shop_order', [ $this, 'register_meta_box' ] );
        add_filter( 'woocommerce_get_settings_pages', [ $this, 'add_settings_page' ] );
    }

    /**
     * Display a welcome notice.
     */
    public function setup_notice() {
        if ( ! Payment_Gateways::get_active_id() ) {
            Notices::add( [
                'message' =>
                    'To get started with WooCommerce Manual Payment, ' .
                    '<a href="admin.php?page=wc-settings&tab=manual_payment">select your payment processor</a> ' .
                    'and fill out your API keys. ' . WOO_MP_CONFIG_HELP .
                    " Once that's done, you'll be able to process payments directly from the " .
                    '<strong>Payment</strong> section at the bottom of the <strong>Edit order</strong> screen.'
            ] );
        }
    }

    /**
     * Add action links to the plugins page.
     *
     * @param  array $links The action links.
     * @return array        The updated action links.
     */
    public function add_action_links( $links ) {
        if ( ! WOO_MP_PRO ) {
            $upgrade_link = '<a href="https://www.woo-mp.routeria.com/#section-pricing" target="_blank">' . __( 'Upgrade', 'woo-mp' ) . '</a>';
            array_unshift( $links, $upgrade_link );
        }
        
        $settings_link = '<a href="admin.php?page=wc-settings&tab=manual_payment">' . __( 'Settings', 'woo-mp' ) . '</a>';
        array_unshift( $links, $settings_link );

        return $links;
    }

    /**
     * Add a payment meta box to WooCommerce orders.
     */
    public function register_meta_box() {
        $title = 'Payment';

        if ( ! get_site_option( 'woo_mp_rated' ) ) {
            $title .= '<span id="woo-mp-rating-request" class="woo-mp-rating-request" style="display: none;">If you like this plugin please leave us a <a href="https://wordpress.org/support/plugin/woo-mp/reviews?rate=5#new-post" target="_blank" >&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Thanks!</span>';
        }

        add_meta_box( 'woo-mp', $title, [ new Payment_Meta_Box_Controller(), 'display' ], 'shop_order' );
    }

    /**
     * Add a new settings tab to the WooCommerce settings page.
     *
     * @param  array $settings The settings pages.
     * @return array           The updated settings pages.
     */
    public function add_settings_page( $settings ) {
        $settings[] = new Settings();
        return $settings;
    }

}

new Woo_MP();