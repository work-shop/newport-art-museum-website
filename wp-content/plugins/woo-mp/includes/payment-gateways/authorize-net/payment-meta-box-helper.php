<?php

namespace Woo_MP\Payment_Gateways\Authorize_Net;

defined( 'ABSPATH' ) || die;

/**
 * Authorize.Net payment meta box helper.
 * 
 * The core payment meta box controller uses this class to add
 * all the gateway-specific parts of the frontend.
 */
class Payment_Meta_Box_Helper implements \Woo_MP\Payment_Meta_Box_Helper {

    private $login_ID;
    private $client_key;
    private $transaction_key;

    /**
     * Set up initial values.
     */
    public function __construct() {
        $this->login_ID        = get_option( 'woo_mp_authorize_net_login_id' );
        $this->client_key      = get_option( 'woo_mp_authorize_net_client_key' );
        $this->transaction_key = get_option( 'woo_mp_authorize_net_transaction_key' );
    }

    public function get_currency( $order_currency ) {
        return get_woocommerce_currency();
    }

    public function validation() {
        $validation   = [];
        $settings_URL = 'admin.php?page=wc-settings&tab=manual_payment&section=authorize_net';

        if ( ! is_ssl() ) {
            $validation[] = [
                'message' => 'Authorize.Net requires SSL. An SSL certificate helps keep your customer\'s payment information secure. Click <a href="https://make.wordpress.org/support/user-manual/web-publishing/https-for-wordpress/" target="_blank">here</a> for more information. If you need help activating SSL, please contact your website administrator, web developer, or hosting company.',
                'type'    => 'error',
                'valid'   => false
            ];
        }

        if ( ! $this->login_ID ) {
            $validation[] = [
                'message' => "Please <a href='$settings_URL'>set your login ID</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'valid'   => false
            ];
        }

        if ( ! $this->client_key ) {
            $validation[] = [
                'message' => "Please <a href='$settings_URL'>set your client key</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'valid'   => false
            ];
        }

        if ( ! $this->transaction_key ) {
            $validation[] = [
                'message' => "Please <a href='$settings_URL'>set your transaction key</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'valid'   => false
            ];
        }

        return $validation;
    }

    public function enqueue_assets() {
        \Woo_MP\script( 'authorize-net-script', WOO_MP_URL . '/includes/payment-gateways/authorize-net/assets/script.js' );

        if ( get_option( 'woo_mp_authorize_net_test_mode' ) === 'yes' ) {
            wp_enqueue_script( 'woo-mp-payment-processor-script', 'https://jstest.authorize.net/v1/Accept.js' );
        } else {
            wp_enqueue_script( 'woo-mp-payment-processor-script', 'https://js.authorize.net/v1/Accept.js' );
        }

        add_filter( 'script_loader_tag', [ $this, 'customize_accept_js_script_tag' ], 99999, 3 );
    }

    public function client_data() {
        return [
            'loginID'   => $this->login_ID,
            'clientKey' => $this->client_key
        ];
    }

    public function get_templates() {
        return [
            'charge-form' => WOO_MP_PATH . '/includes/payment-gateways/authorize-net/templates/charge-form.php'
        ];
    }

    public function customize_accept_js_script_tag( $tag, $handle, $src ) {
        if ( $handle === 'woo-mp-payment-processor-script' ) {

            // Authorize.Net validates the source of the Accept.js script. Query arguments cause that check to fail.
            $src = explode( '?', $src )[0];

            // Authorize.Net recommends setting the script tag's charset to UTF-8.
            $tag = "<script type='text/javascript' src='$src' charset='utf-8'></script>";
        }

        return $tag;
    }

}