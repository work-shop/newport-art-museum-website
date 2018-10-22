<?php

namespace Woo_MP\Payment_Gateways\Stripe;

defined( 'ABSPATH' ) || die;

/**
 * Stripe payment meta box helper.
 * 
 * The core payment meta box controller uses this class to add
 * all the gateway-specific parts of the frontend.
 */
class Payment_Meta_Box_Helper implements \Woo_MP\Payment_Meta_Box_Helper {

    private $publishable_key;
    private $secret_key;

    /**
     * Set up initial values.
     */
    public function __construct() {
        $this->publishable_key = get_option( 'woo_mp_stripe_publishable_key' );
        $this->secret_key      = get_option( 'woo_mp_stripe_secret_key' );
    }

    public function get_currency( $order_currency ) {
        return $order_currency;
    }

    public function validation() {
        $validation   = [];
        $settings_URL = 'admin.php?page=wc-settings&tab=manual_payment&section=stripe';

        if ( ! $this->publishable_key ) {
            $validation[] = [
                'message' => "Please <a href='$settings_URL'>set your publishable key</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'valid'   => false
            ];
        }

        if ( ! $this->secret_key ) {
            $validation[] = [
                'message' => "Please <a href='$settings_URL'>set your secret key</a>. " . WOO_MP_CONFIG_HELP,
                'type'    => 'info',
                'valid'   => false
            ];
        }

        if ( ! is_ssl() ) {
            $validation[] = [
                'message' => 'Stripe requires SSL. An SSL certificate helps keep your customer\'s payment information secure. Without SSL, only test API keys will work. Click <a href="https://make.wordpress.org/support/user-manual/web-publishing/https-for-wordpress/" target="_blank">here</a> for more information. If you need help activating SSL, please contact your website administrator, web developer, or hosting company.',
                'type'    => 'warning',
                'valid'   => true
            ];
        }

        return $validation;
    }

    public function enqueue_assets() {
        \Woo_MP\script( 'stripe-script', WOO_MP_URL . '/includes/payment-gateways/stripe/assets/script.js' );
        wp_enqueue_script( 'woo-mp-payment-processor-script', 'https://js.stripe.com/v2/' );
    }

    public function client_data() {
        return [
            'publishableKey' => $this->publishable_key
        ];
    }

    public function get_templates() {
        return [
            'charge-form' => WOO_MP_PATH . '/includes/payment-gateways/stripe/templates/charge-form.php'
        ];
    }

}