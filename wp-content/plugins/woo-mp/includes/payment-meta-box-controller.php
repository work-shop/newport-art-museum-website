<?php

namespace Woo_MP;

use Woo_MP\Notices;
use Woo_MP\Woo_MP_Order;

defined( 'ABSPATH' ) || die;

/**
 * Controller for the payment meta box.
 */
class Payment_Meta_Box_Controller {

    /**
     * The gateway's payment meta box helper class.
     * 
     * @var \Woo_MP\Payment_Meta_Box_Helper
     */
    private $gateway_helper;

    /**
     * The order.
     * 
     * @var object
     */
    private $order;

    /**
     * The currency that payments will be made in.
     * 
     * @var string
     */
    private $payment_currency;

    /**
     * The templates that the gateway is providing.
     * 
     * Keys are template names and values are the paths to those templates.
     * 
     * @var array
     */
    private $gateway_templates;

    /**
     * Set the gateway's payment meta box helper class.
     */
    public function __construct() {
        if ( $gateway = Payment_Gateways::get_active() ) {
            $this->gateway_helper    = $gateway->get_payment_meta_box_helper();
            $this->order             = new Woo_MP_Order( wc_get_order() );
            $this->payment_currency  = $this->gateway_helper->get_currency( $this->order->get_currency() );
            $this->gateway_templates = $this->gateway_helper->get_templates();
        }
    }

    /**
     * Do validation and allow for payment gateways to add their own validation.
     * 
     * @return bool true if valid, false otherwise.
     */
    private function validation() {
        $validation = [];

        if ( $this->gateway_helper ) {
            if ( $this->payment_currency !== $this->order->get_currency() ) {
                $validation[] = [
                    'message' => "Transactions will be processed in $this->payment_currency.",
                    'type'    => 'info',
                    'valid'   => true
                ];
            }

            $validation = array_merge( $validation, $this->gateway_helper->validation() );
        } else {
            $validation[] = [
                'message' => 'Please <a href="admin.php?page=wc-settings&tab=manual_payment">choose your payment processor</a>. ' . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'valid'   => false
            ];
        }

        if ( $validation ) {
            $errors = array_values( array_filter( $validation, function ( $message ) {
                return ! $message['valid'];
            } ) );

            if ( $errors ) {
                $validation = [ $errors[0] ];
            }

            foreach ( $validation as $message ) {
                Notices::add( [
                    'message'     => $message['message'],
                    'type'        => $message['type'],
                    'inline'      => true,
                    'dismissible' => $message['valid']
                ] );
            }

            if ( $errors ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Enqueue assets and make some data available on the client side via a global 'wooMP' JavaScript object.
     */
    private function enqueue_assets() {
        \Woo_MP\style( 'style', WOO_MP_URL . '/assets/css/style.css' );
        wp_enqueue_script( 'jquery-payment', plugins_url( 'assets/js/jquery-payment/jquery.payment.min.js', WC_PLUGIN_FILE ), [], WC_VERSION );
        \Woo_MP\script( 'script', WOO_MP_URL . '/assets/js/script.js' );

        $this->gateway_helper->enqueue_assets();

        $client_data = $this->gateway_helper->client_data() + [
            'AJAXURL'        => admin_url( 'admin-ajax.php' ),
            'nonces'         => [
                'woo_mp_charge' => wp_create_nonce( 'woo_mp_charge_' . $this->order->get_id() )
            ],
            'gatewayID'      => Payment_Gateways::get_active_id(),
            'currency'       => $this->payment_currency,
            'currencySymbol' => get_woocommerce_currency_symbol( $this->payment_currency )
        ];

        wp_localize_script( 'woo-mp-script', 'wooMP', $client_data );
    }

    /**
     * Output a template.
     * 
     * @param string $name The name of the template.
     */
    private function template( $name ) {
        if ( isset( $this->gateway_templates[ $name ] ) ) {
            require $this->gateway_templates[ $name ];
        } else {
            require WOO_MP_PATH . "/templates/$name.php";
        }
    }

    /**
     * Run validation, enqueue assets, and output the meta box content.
     */
    public function display() {
        if ( ! $this->validation() ) {
            return;
        }

        $this->enqueue_assets();

        $this->template( 'payments-meta-box' );
    }

}