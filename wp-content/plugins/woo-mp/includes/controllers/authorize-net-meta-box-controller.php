<?php

namespace Woo_MP\Controllers;

use Woo_MP\Notices;
use Woo_MP\Woo_MP_Order;

defined( 'ABSPATH' ) || die;

/**
 * Controller for the Authorize.Net payment meta box.
 */
class Authorize_Net_Meta_Box_Controller {

    private $login_ID;
    private $client_key;
    private $transaction_key;
    private $order;
    private $currency;
    private $order_currency;

    public function __construct() {
        $this->login_ID        = get_option( 'woo_mp_authorize_net_login_id' );
        $this->client_key      = get_option( 'woo_mp_authorize_net_client_key' );
        $this->transaction_key = get_option( 'woo_mp_authorize_net_transaction_key' );
        $this->order           = new Woo_MP_Order( wc_get_order() );
        $this->currency        = get_woocommerce_currency();
        $this->order_currency  =
            \Woo_MP\is_wc3()
            ? $this->order->get_currency()
            : ( $this->order->get_order_currency() ?: get_woocommerce_currency() );

        if ( $this->validate() ) {
            $this->add_scripts_and_styles();
            $this->display();
        }
    }

    private function validate() {
        $settings_URL = 'admin.php?page=wc-settings&tab=manual_payment&section=authorize_net';

        if ( ! is_ssl() ) {
            Notices::add( [
                'message' => 'Authorize.Net requires SSL. An SSL certificate helps keep your customer\'s payment information secure. Click <a href="https://make.wordpress.org/support/user-manual/web-publishing/https-for-wordpress/" target="_blank">here</a> for more information. If you need help activating SSL, please contact your website administrator, web developer, or hosting company.',
                'type'    => 'error',
                'inline'  => TRUE
            ] );
            
            return FALSE;
        }

        if ( ! $this->login_ID ) {
            Notices::add( [
                'message' => "Please <a href='$settings_URL'>set your login ID</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'inline'  => TRUE
            ] );

            return FALSE;
        }

        if ( ! $this->client_key ) {
            Notices::add( [
                'message' => "Please <a href='$settings_URL'>set your client key</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'inline'  => TRUE
            ] );

            return FALSE;
        }

        if ( ! $this->transaction_key ) {
            Notices::add( [
                'message' => "Please <a href='$settings_URL'>set your transaction key</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'inline'  => TRUE
            ] );

            return FALSE;
        }

        if ( $this->currency !== $this->order_currency ) {
            Notices::add( [
                'message'     => 'Transactions will be processed in your Authorize.Net account currency.',
                'type'        => 'info',
                'inline'      => TRUE,
                'dismissible' => TRUE
            ] );
        }

        return TRUE;
    }

    private function add_scripts_and_styles() {
        \Woo_MP\style( 'style', WOO_MP_URL . '/assets/css/style.css' );

        \Woo_MP\script( 'jquery-payment-script', WOO_MP_URL . '/assets/js/jquery.payment.min.js' );
        \Woo_MP\script( 'script', WOO_MP_URL . '/assets/js/script.js' );
        \Woo_MP\script( 'authorize-net-script', WOO_MP_URL . '/assets/js/authorize-net-script.js' );

        if ( get_option( 'woo_mp_authorize_net_test_mode' ) == 'yes' ) {
            wp_enqueue_script( 'woo-mp-payment-processor-script', 'https://jstest.authorize.net/v1/Accept.js' );
        } else {
            wp_enqueue_script( 'woo-mp-payment-processor-script', 'https://js.authorize.net/v1/Accept.js' );
        }

        if ( WOO_MP_PRO ) {
            \Woo_MP\script( 'pro-script', WOO_MP_PRO_URL . '/assets/js/pro-script.js' );
            \Woo_MP\script( 'pro-authorize-net-script', WOO_MP_PRO_URL . '/assets/js/authorize-net-pro-script.js' );
        }

        wp_localize_script( 'woo-mp-script', 'wooMP', [
            'AJAXURL'        => admin_url( 'admin-ajax.php' ),
            'currency'       => $this->currency,
            'currencySymbol' => get_woocommerce_currency_symbol( $this->currency ),
            'loginID'        => $this->login_ID,
            'clientKey'      => $this->client_key
        ] );

        add_filter( 'script_loader_tag', [ $this, 'customize_accept_js_script_tag' ], 99999, 3 );
    }

    public function customize_accept_js_script_tag( $tag, $handle, $src ) {
        if ( $handle == 'woo-mp-payment-processor-script' ) {

            // Authorize.Net validates the source of the Accept.js script. Query arguments cause that check to fail.
            $src = explode( '?', $src )[0];

            // Authorize.Net recommends setting the script tag's charset to UTF-8.
            $tag = "<script type='text/javascript' src='$src' charset='utf-8'></script>";
        }

        return $tag;
    }

    private function display() {
        $order = $this->order;

        require WOO_MP_PATH . '/templates/payments-meta-box.php';
    }

}

new Authorize_Net_Meta_Box_Controller();