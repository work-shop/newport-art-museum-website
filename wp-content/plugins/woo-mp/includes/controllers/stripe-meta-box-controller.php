<?php

namespace Woo_MP\Controllers;

use Woo_MP\Notices;
use Woo_MP\Woo_MP_Order;

defined( 'ABSPATH' ) || die;

/**
 * Controller for the Stripe payment meta box.
 */
class Stripe_Meta_Box_Controller {

    private $publishable_key;
    private $secret_key;
    private $order;
    private $currency;

    public function __construct() {
        $this->publishable_key = get_option( 'woo_mp_stripe_publishable_key' );
        $this->secret_key      = get_option( 'woo_mp_stripe_secret_key' );
        $this->order           = new Woo_MP_Order( wc_get_order() );
        $this->currency        =
            \Woo_MP\is_wc3()
            ? $this->order->get_currency()
            : ( $this->order->get_order_currency() ?: get_woocommerce_currency() );

        if ( $this->validate() ) {
            $this->add_scripts_and_styles();
            $this->display();
        }
    }

    private function validate() {
        $settings_URL = 'admin.php?page=wc-settings&tab=manual_payment&section=stripe';

        if ( ! $this->publishable_key ) {
            Notices::add( [
                'message' => "Please <a href='$settings_URL'>set your publishable key</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'inline'  => TRUE
            ] );

            return FALSE;
        }

        if ( ! $this->secret_key ) {
            Notices::add( [
                'message' => "Please <a href='$settings_URL'>set your secret key</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'inline'  => TRUE
            ] );

            return FALSE;
        }

        if ( ! is_ssl() ) {
            Notices::add( [
                'message'     => 'Stripe requires SSL. An SSL certificate helps keep your customer\'s payment information secure. Without SSL, only test API keys will work. Click <a href="https://make.wordpress.org/support/user-manual/web-publishing/https-for-wordpress/" target="_blank">here</a> for more information. If you need help activating SSL, please contact your website administrator, web developer, or hosting company.',
                'type'        => 'warning',
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
        \Woo_MP\script( 'stripe-script', WOO_MP_URL . '/assets/js/stripe-script.js' );
        wp_enqueue_script( 'woo-mp-payment-processor-script', 'https://js.stripe.com/v2/' );

        if ( WOO_MP_PRO ) {
            \Woo_MP\script( 'pro-script', WOO_MP_PRO_URL . '/assets/js/pro-script.js' );
            \Woo_MP\script( 'pro-stripe-script', WOO_MP_PRO_URL . '/assets/js/stripe-pro-script.js' );
        }

        wp_localize_script( 'woo-mp-script', 'wooMP', [
            'AJAXURL'        => admin_url( 'admin-ajax.php' ),
            'currency'       => $this->currency,
            'currencySymbol' => get_woocommerce_currency_symbol( $this->currency ),
            'publishableKey' => $this->publishable_key
        ] );
    }

    private function display() {
        $order = $this->order;

        require WOO_MP_PATH . '/templates/payments-meta-box.php';
    }

}

new Stripe_Meta_Box_Controller();