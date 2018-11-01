<?php

namespace Woo_MP\Payment_Processors\Stripe;

use Woo_MP\Payment_Processor;

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Stripe\Stripe' ) ) {
    require WOO_MP_PATH . '/libraries/stripe-php-6.4.2/init.php';
}

/**
 * Process a payment with Stripe using information from $_POST.
 */
class Stripe_Payment_Processor extends Payment_Processor {

    /**
     * Set up initial values.
     */
    public function __construct() {
        parent::__construct();

        $this->id    = 'stripe';
        $this->title = get_option( 'woo_mp_stripe_title', 'Credit Card (Stripe)' );

        $secret_key = get_option( 'woo_mp_stripe_secret_key' );
        \Stripe\Stripe::setApiKey( $secret_key );

        $http_client = new Stripe_HTTP_Client();
        \Stripe\ApiRequestor::setHttpClient( $http_client );
    }

    /**
     * Process a payment using information from $_POST.
     */
    public function process_payment() {
        $amount = $this->to_smallest_unit( $this->amount, $this->currency );

        $charge_info = [
            'amount'      => $amount,
            'currency'    => $this->currency,
            'source'      => $_POST['token'],
            'description' => $this->description,
            'capture'     => $this->capture,
            'metadata'    => [ 'Order Number' => $this->order->get_order_number() ]
        ];

        if ( get_option( 'woo_mp_stripe_include_name_and_email', 'yes' ) == 'yes' ) {
            $name  = \Woo_MP\wc3( $this->order, 'billing_first_name' ) . ' ' . \Woo_MP\wc3( $this->order, 'billing_last_name' );
            $email = \Woo_MP\wc3( $this->order, 'billing_email' );

            $charge_info['metadata']['Customer Name'] = $name;
            $charge_info['metadata']['Customer Email'] = $email;
        }

        try {
            $charge = \Stripe\Charge::create( $charge_info );
        } catch ( \Stripe\Error\Base $error ) {
            $this->respond( 'error', $error->getMessage() );
        }

        if ( $charge->status == 'succeeded' ) {
            $this->trans_id         = $charge->id;
            $this->last_four_digits = $charge->source->last4;
            $this->held_for_review  = $charge->outcome->type === 'manual_review';

            $this->do_success();
        }

        $this->respond( 'error', $this->unknown_error . print_r( $charge, TRUE ) );
    }

}