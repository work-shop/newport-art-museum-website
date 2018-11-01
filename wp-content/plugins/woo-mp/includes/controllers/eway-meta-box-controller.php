<?php

namespace Woo_MP\Controllers;

use Woo_MP\Notices;
use Woo_MP\Woo_MP_Order;
use Woo_MP\Payment_Processors\Eway\Eway_Payment_Processor;

defined( 'ABSPATH' ) || die;

/**
 * Controller for the eWAY payment meta box.
 */
class Eway_Meta_Box_Controller {

    private $api_key;
    private $api_password;
    private $order;
    private $currency;
    private $order_currency;

    public function __construct() {
        $this->api_key        = get_option( 'woo_mp_eway_api_key' );
        $this->api_password   = get_option( 'woo_mp_eway_api_password' );
        $this->order          = new Woo_MP_Order( wc_get_order() );
        $this->currency       = get_woocommerce_currency();
        $this->order_currency =
            \Woo_MP\is_wc3()
            ? $this->order->get_currency()
            : ( $this->order->get_order_currency() ?: get_woocommerce_currency() );

        if ( $this->validate() ) {
            $this->add_scripts_and_styles();
            $this->display();
        }
    }

    private function validate() {
        $settings_URL = 'admin.php?page=wc-settings&tab=manual_payment&section=eway';

        if ( ! $this->api_key ) {
            Notices::add( [
                'message' => "Please <a href='$settings_URL'>set your API key</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'inline'  => TRUE
            ] );

            return FALSE;
        }

        if ( ! $this->api_password ) {
            Notices::add( [
                'message' => "Please <a href='$settings_URL'>set your API password</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'inline'  => TRUE
            ] );

            return FALSE;
        }

        if ( $this->currency !== $this->order_currency ) {
            Notices::add( [
                'message'     => 'Transactions will be processed in your eWAY account currency.',
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
        \Woo_MP\script( 'eway-script', WOO_MP_URL . '/assets/js/eway-script.js' );
        wp_enqueue_script( 'woo-mp-payment-processor-script', 'https://api.ewaypayments.com/JSONP/v3/js' );

        if ( WOO_MP_PRO ) {
            \Woo_MP\script( 'pro-script', WOO_MP_PRO_URL . '/assets/js/pro-script.js' );
            \Woo_MP\script( 'pro-stripe-script', WOO_MP_PRO_URL . '/assets/js/eway-pro-script.js' );
        }

        wp_localize_script( 'woo-mp-script', 'wooMP', [
            'AJAXURL'              => admin_url( 'admin-ajax.php' ),
            'currency'             => $this->currency,
            'currencySymbol'       => get_woocommerce_currency_symbol( $this->currency ),
            'responseCodeMessages' => Eway_Payment_Processor::get_response_code_messages()
        ] );
    }

    private function display() {
        $order = $this->order;

        require WOO_MP_PATH . '/templates/payments-meta-box.php';
    }

}

new Eway_Meta_Box_Controller();