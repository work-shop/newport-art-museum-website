<?php

namespace Woo_MP\Payment_Gateways\Eway;

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Eway\Rapid' ) ) {
    require WOO_MP_PATH . '/includes/payment-gateways/eway/libraries/eway-rapid-php-1.3.4/include_eway.php';
}

/**
 * Process a payment with eWAY.
 */
class Payment_Processor extends \Woo_MP\Payment_Processor {

    /**
     * The current step in the payment process.
     * 
     * @var string
     */
    private $sub_action;

    /**
     * The redirect URL.
     * 
     * This gateway does not actually redirect the user, but eWAY still requires this field.
     * 
     * @var string
     */
    private $redirect_url;

    /**
     * The transaction access code.
     * 
     * @var string
     */
    private $access_code;

    /**
     * eWAY API client.
     * 
     * @var Eway\Rapid\Contract\Client
     */
    private $client;

    /**
     * Set up initial values.
     * 
     * @param array $params The payment information.
     * 
     * [
     *     'sub_action'   => 'sub_action_name',      // The current step in the payment process.
     *     'redirect_url' => 'https://example.com/', // The redirect URL.
     *     'access_code'  => 'abc123'                // The transaction access code.
     * ]
     * 
     * See \Woo_MP\Payment_Processor::__construct() for fields that are required for all payment gateways.
     */
    public function __construct( $params, $title = '' ) {
        parent::__construct( $params, $title );

        $this->sub_action       = $params['sub_action'];
        $this->redirect_url     = isset( $params['redirect_url'] ) ? $params['redirect_url'] : null;
        $this->access_code      = isset( $params['access_code'] ) ? $params['access_code'] : null;

        $this->client = \Eway\Rapid::createClient(
            get_option( 'woo_mp_eway_api_key' ),
            get_option( 'woo_mp_eway_api_password' ),
            get_option( 'woo_mp_eway_sandbox_mode' ) === 'yes'
            ? \Eway\Rapid\Client::MODE_SANDBOX
            : \Eway\Rapid\Client::MODE_PRODUCTION
        );
    }

    /**
     * Process a payment.
     * 
     * Since this is a multi-step process, this method just routes the request to the appropriate step.
     * 
     * @return mixed Arbitrary gateway-specific data.
     */
    public function process_payment() {
        if ( isset( $this->sub_action ) && is_callable( [ $this, $this->sub_action ] ) ) {
            return $this->{$this->sub_action}();
        }
    }

    /**
     * Get an access code needed to process payments.
     * 
     * This is the first step for processing payments.
     * This code needs to be returned to the client-side, where it can then be used to actually process a payment.
     * 
     * @return mixed Arbitrary gateway-specific data.
     */
    public function get_access_code() {
        $amount   = $this->to_smallest_unit( $this->amount, $this->currency );

        $request = [
            'Payment'         => [
                'TotalAmount'        => $amount,
                'CurrencyCode'       => $this->currency,
                'InvoiceNumber'      => $this->order->get_order_number(),
                'InvoiceReference'   => $this->order->get_order_number(),
                'InvoiceDescription' => $this->description
            ],
            'Capture'         => $this->capture,
            'CustomerIP'      => $this->order->get_customer_ip_address(),
            'RedirectUrl'     => $this->redirect_url,
            'TransactionType' => \Eway\Rapid\Enum\TransactionType::PURCHASE,
        ];

        if ( get_option( 'woo_mp_eway_include_billing_details', 'yes' ) === 'yes' ) {
            $request['Customer'] = [
                'FirstName'   => wc_trim_string( $this->order->get_billing_first_name(), 50 ),
                'LastName'    => wc_trim_string( $this->order->get_billing_last_name(), 50 ),
                'CompanyName' => wc_trim_string( $this->order->get_billing_company(), 50 ), // This field is not functional.
                'Street1'     => wc_trim_string( $this->order->get_billing_address_1(), 50 ),
                'Street2'     => wc_trim_string( $this->order->get_billing_address_2(), 50 ),
                'City'        => wc_trim_string( $this->order->get_billing_city(), 50 ),
                'State'       => wc_trim_string( $this->order->get_billing_state(), 50 ),
                'PostalCode'  => wc_trim_string( $this->order->get_billing_postcode(), 30 ),
                'Country'     => wc_trim_string( $this->order->get_billing_country(), 2 ),
                'Email'       => wc_trim_string( $this->order->get_billing_email(), 50 ),
                'Phone'       => wc_trim_string( $this->order->get_billing_phone(), 32 )
            ];
        }

        if ( get_option( 'woo_mp_eway_include_shipping_details', 'yes' ) === 'yes' ) {
            $request['ShippingAddress'] = [
                'FirstName'  => wc_trim_string( $this->order->get_shipping_first_name(), 50 ),
                'LastName'   => wc_trim_string( $this->order->get_shipping_last_name(), 50 ),
                'Street1'    => wc_trim_string( $this->order->get_shipping_address_1(), 50 ),
                'Street2'    => wc_trim_string( $this->order->get_shipping_address_2(), 50 ),
                'City'       => wc_trim_string( $this->order->get_shipping_city(), 50 ),
                'State'      => wc_trim_string( $this->order->get_shipping_state(), 50 ),
                'PostalCode' => wc_trim_string( $this->order->get_shipping_postcode(), 30 ),
                'Country'    => wc_trim_string( $this->order->get_shipping_country(), 2 )
            ];
        }

        $request = apply_filters( 'woo_mp_eway_charge_request', $request, $this->order->get_core_order() );

        $response = $this->request( 'createTransaction', \Eway\Rapid\Enum\ApiMethod::TRANSPARENT_REDIRECT, $request );

        return [
            'form_action_url' => $response->FormActionURL,
            'access_code'     => $response->AccessCode
        ];
    }

    /**
     * Get the status of a transaction.
     * 
     * This is the final step for processing payments.
     * This is where the actions taken for successful payments are actually executed.
     * If the transaction failed, the error will be returned.
     */
    private function get_transaction_status() {
        $response = $this->request( 'queryTransaction', $this->access_code );

        $transaction = $response->Transactions[0];

        $this->trans_id = $transaction->TransactionID;
        
        $this->do_success();
    }

    /**
     * Make a request to eWAY.
     * 
     * Errors are automatically handled.
     *
     * @param  string $method   The method to call on the client.
     * @param  mixed  $args,... The arguments to pass to the method.
     * @return mixed            The response.
     */
    private function request( $method ) {
        $response   = call_user_func_array( [ $this->client, $method ], array_slice( func_get_args(), 1 ) );
        $error_code = null;

        if ( $response->getErrors() ) {
            $error_code = $response->getErrors()[0];

            if ( $error_code === 'S9993' ) {
                throw new \Woo_MP\Detailed_Exception(
                    'Sorry, the API Key, or API Password, or both, are not valid. Please check your settings and try again.',
                    $error_code
                );
            }
        }

        if ( isset( $response->Transactions ) && ! $response->Transactions[0]->TransactionStatus ) {
            $error_code = explode( ',', $response->Transactions[0]->ResponseMessage )[0];
        }

        if ( $error_code ) {
            throw new \Woo_MP\Detailed_Exception( \Eway\Rapid::getMessage( $error_code ), $error_code );
        }

        return $response;
    }

    /**
     * Get all eWAY response code messages.
     * 
     * @return array Associative array with codes as keys and messages as values.
     */
    public static function get_response_code_messages() {
        return parse_ini_file( WOO_MP_PATH . '/includes/payment-gateways/eway/libraries/eway-rapid-php-1.3.4/resource/lang/en.ini' );
    }

}