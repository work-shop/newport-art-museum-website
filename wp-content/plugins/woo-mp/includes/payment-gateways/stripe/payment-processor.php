<?php

namespace Woo_MP\Payment_Gateways\Stripe;

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Stripe\Stripe' ) ) {
    require WOO_MP_PATH . '/includes/payment-gateways/stripe/libraries/stripe-php-6.19.4/init.php';
}

/**
 * Process a payment with Stripe.
 */
class Payment_Processor extends \Woo_MP\Payment_Processor {

    /**
     * The token representing the payment source.
     * 
     * @var string
     */
    private $token;

    /**
     * Set up initial values.
     * 
     * @param array $params The payment information.
     * 
     * [
     *     'token' => 'tok_abc123' // The token representing the payment source.
     * ]
     * 
     * See \Woo_MP\Payment_Processor::__construct() for fields that are required for all payment gateways.
     */
    public function __construct( $params, $title = '' ) {
        parent::__construct( $params, $title );

        $this->token = $params['token'];

        $secret_key = get_option( 'woo_mp_stripe_secret_key' );
        \Stripe\Stripe::setApiKey( $secret_key );

        $http_client = new HTTP_Client();
        \Stripe\ApiRequestor::setHttpClient( $http_client );
    }

    /**
     * Process a payment.
     */
    public function process_payment() {
        $amount = $this->to_smallest_unit( $this->amount, $this->currency );

        $charge_info = [
            'amount'      => $amount,
            'currency'    => $this->currency,
            'source'      => $this->token,
            'description' => $this->description,
            'capture'     => $this->capture,
            'metadata'    => [ 'Order Number' => $this->order->get_order_number() ]
        ];

        if ( get_option( 'woo_mp_stripe_include_name_and_email', 'yes' ) == 'yes' ) {
            $name  = $this->order->get_billing_first_name() . ' ' . $this->order->get_billing_last_name();
            $email = $this->order->get_billing_email();

            $charge_info['metadata']['Customer Name'] = $name;
            $charge_info['metadata']['Customer Email'] = $email;
        }

        try {
            $charge = \Stripe\Charge::create( $charge_info );
        } catch ( \Stripe\Error\Base $error ) {
            throw new \Woo_MP\Detailed_Exception( $error->getMessage(), $error->getStripeCode() );
        }

        $this->trans_id         = $charge->id;
        $this->held_for_review  = $charge->outcome->type === 'manual_review';

        $this->do_success();
    }

}