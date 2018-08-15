<?php

namespace Woo_MP\Payment_Processors\Eway;

use Woo_MP\Payment_Processor;

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Eway\Rapid' ) ) {
    require WOO_MP_PATH . '/libraries/eway-rapid-php-1.3.4/include_eway.php';
}

/**
 * Process a payment with eWAY.
 */
class Eway_Payment_Processor extends Payment_Processor {

    /**
     * eWAY API client.
     * 
     * @var Eway\Rapid\Contract\Client
     */
    private $client;

    /**
     * Set up initial values.
     */
    public function __construct() {
        parent::__construct();
        
        $this->id    = 'eway';
        $this->title = get_option( 'woo_mp_eway_title', 'Credit Card (eWAY)' );

        $this->client = \Eway\Rapid::createClient(
            get_option( 'woo_mp_eway_api_key' ),
            get_option( 'woo_mp_eway_api_password' ),
            get_option( 'woo_mp_eway_sandbox_mode' ) === 'yes'
            ? \Eway\Rapid\Client::MODE_SANDBOX
            : \Eway\Rapid\Client::MODE_PRODUCTION
        );
    }

    /**
     * Process a payment using information from $_POST.
     * 
     * Since this is a multi-step process, this method just routes the request to the appropriate step.
     */
    public function process_payment() {
        if ( isset( $_POST['sub_action'] ) && is_callable( [ $this, $_POST['sub_action'] ] ) ) {
            $this->{$_POST['sub_action']}();
        }
    }

    /**
     * Get an access code needed to process payments.
     * 
     * This is the first step for processing payments.
     * This code needs to be returned to the client-side, where it can then be used to actually process a payment.
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
            'CustomerIP'      => \Woo_MP\wc3( $this->order, 'customer_ip_address' ),
            'RedirectUrl'     => $_POST['redirect_url'],
            'TransactionType' => \Eway\Rapid\Enum\TransactionType::PURCHASE,
        ];

        if ( get_option( 'woo_mp_eway_include_billing_details', 'yes' ) === 'yes' ) {
            $request['Customer'] = [
                'FirstName'   => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_first_name' ), 50 ),
                'LastName'    => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_last_name' ), 50 ),
                'CompanyName' => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_company' ), 50 ),
                'Street1'     => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_address_1' ), 50 ),
                'Street2'     => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_address_2' ), 50 ),
                'City'        => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_city' ), 50 ),
                'State'       => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_state' ), 50 ),
                'PostalCode'  => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_postcode' ), 30 ),
                'Country'     => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_country' ), 2 ),
                'Email'       => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_email' ), 50 ),
                'Phone'       => wc_trim_string( \Woo_MP\wc3( $this->order, 'billing_phone' ), 32 )
            ];
        }

        if ( get_option( 'woo_mp_eway_include_shipping_details', 'yes' ) === 'yes' ) {
            $request['ShippingAddress'] = [
                'FirstName'  => wc_trim_string( \Woo_MP\wc3( $this->order, 'shipping_first_name' ), 50 ),
                'LastName'   => wc_trim_string( \Woo_MP\wc3( $this->order, 'shipping_last_name' ), 50 ),
                'Street1'    => wc_trim_string( \Woo_MP\wc3( $this->order, 'shipping_address_1' ), 50 ),
                'Street2'    => wc_trim_string( \Woo_MP\wc3( $this->order, 'shipping_address_2' ), 50 ),
                'City'       => wc_trim_string( \Woo_MP\wc3( $this->order, 'shipping_city' ), 50 ),
                'State'      => wc_trim_string( \Woo_MP\wc3( $this->order, 'shipping_state' ), 50 ),
                'PostalCode' => wc_trim_string( \Woo_MP\wc3( $this->order, 'shipping_postcode' ), 30 ),
                'Country'    => wc_trim_string( \Woo_MP\wc3( $this->order, 'shipping_country' ), 2 )
            ];
        }

        $response = $this->request( 'createTransaction', \Eway\Rapid\Enum\ApiMethod::TRANSPARENT_REDIRECT, $request );

        $this->respond( 'success', '', [
            'form_action_url' => $response->FormActionURL,
            'access_code'     => $response->AccessCode
        ] );
    }

    /**
     * Get the status of a transaction.
     * 
     * This is the final step for processing payments.
     * This is where the actions taken for successful payments are actually executed.
     * If the transaction failed, the error will be returned.
     */
    private function get_transaction_status() {
        $response = $this->request( 'queryTransaction', $_POST['access_code'] );

        $transaction = $response->Transactions[0];

        $this->trans_id         = $transaction->TransactionID;
        $this->last_four_digits = $_POST['last_4'];
        
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
        $error_code = NULL;

        if ( $response->getErrors() ) {
            $error_code = $response->getErrors()[0];

            if ( $error_code === 'S9993' ) {
                $this->respond( 'error', 'Sorry, the API Key, or API Password, or both, are not valid. Please check your settings and try again.' );
            }
        }

        if ( isset( $response->Transactions ) && ! $response->Transactions[0]->TransactionStatus ) {
            $error_code = explode( ',', $response->Transactions[0]->ResponseMessage )[0];
        }

        if ( $error_code ) {
            $this->respond( 'error', \Eway\Rapid::getMessage( $error_code ), [ 'error_code' => $error_code ] );
        }

        return $response;
    }

    /**
     * Get all eWAY response code messages.
     * 
     * @return array Associative array with codes as keys and messages as values.
     */
    public static function get_response_code_messages() {
        return parse_ini_file( WOO_MP_PATH . '/libraries/eway-rapid-php-1.3.4/resource/lang/en.ini' );
    }

}