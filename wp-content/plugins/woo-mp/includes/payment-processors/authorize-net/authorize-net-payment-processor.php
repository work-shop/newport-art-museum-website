<?php

namespace Woo_MP\Payment_Processors\Authorize_Net;

use Woo_MP\Payment_Processor;

defined( 'ABSPATH' ) || die;

/**
 * Process a payment with Authorize.Net.
 */
class Authorize_Net_Payment_Processor extends Payment_Processor {

    private $api;

    /**
     * Set up initial values.
     */
    public function __construct() {
        parent::__construct();
        
        $this->id    = 'authorize_net';
        $this->title = get_option( 'woo_mp_authorize_net_title', 'Credit Card (Authorize.Net)' );

        $this->api = new Authorize_Net_Client( [
            'login_id'        => get_option( 'woo_mp_authorize_net_login_id' ),
            'transaction_key' => get_option( 'woo_mp_authorize_net_transaction_key' ),
            'sandbox'         => get_option( 'woo_mp_authorize_net_test_mode' ) == 'yes'
        ] );
    }

    /**
     * Process a payment using information from $_POST.
     */
    public function process_payment() {
        $transaction_type = $this->capture ? 'authCaptureTransaction' : 'authOnlyTransaction';

        $request = [
            'createTransactionRequest' => [
                'transactionRequest' => [
                    'transactionType' => $transaction_type,
                    'amount'          => $this->amount,
                    'payment'         => [
                        'opaqueData' => [
                            'dataDescriptor' => 'COMMON.ACCEPT.INAPP.PAYMENT',
                            'dataValue'      => $_POST['token']
                        ]
                    ],
                    'order'           => [
                        'invoiceNumber' => $this->order->get_order_number(),
                        'description'   => $this->trim_chars( $this->description, 255 )
                    ],
                    'lineItems'       => [],
                    'tax'             => [
                        'amount' => $_POST['tax_amount']
                    ],
                    'duty'            => [
                        'amount' => $_POST['duty_amount']
                    ],
                    'shipping'        => [
                        'amount' => $_POST['freight_amount'],
                    ],
                    'taxExempt'       => $_POST['tax_exempt'],
                    'poNumber'        => $_POST['po_number'],
                    'billTo'          => [],
                    'shipTo'          => []
                ]
            ]
        ];

        if ( get_option( 'woo_mp_authorize_net_include_item_details', 'yes' ) == 'yes' ) {
            $line_items = [];

            foreach ( $this->order->get_items() as $item ) {
                if ( \Woo_MP\is_wc3() ) {
                    $product     = $item->get_product();
                    $item_id     = ( $product ? $product->get_sku() : '' ) ?: $item->get_product_id();
                    $name        = $item->get_name() ?: $item_id;
                    $description = $product ? ( $product->get_short_description() ?: $product->get_description() ) : '';
                    $quantity    = $item->get_quantity();
                } else {
                    $product     = wc_get_product( $item['product_id'] );
                    $item_id     = ( $product ? $product->get_sku() : '' ) ?: $item['product_id'];
                    $name        = $item['name'] ?: $item_id;
                    $description = $product ? ( $product->post->post_excerpt ?: $product->post->post_content ) : '';
                    $quantity    = $item['qty'];
                }

                $line_items[] = [
                    'itemId'      => $this->trim_chars( $item_id, 31 ),
                    'name'        => $this->trim_chars( $name, 31 ),
                    'description' => $this->trim_chars( wp_strip_all_tags( strip_shortcodes( $description ) ), 255 ),
                    'quantity'    => $quantity,
                    'unitPrice'   => (float) ( $product ? $product->get_price() : 0 ),
                    'taxable'     => $product ? $product->is_taxable() : TRUE
                ];
            }

            $request['createTransactionRequest']['transactionRequest']['lineItems']['lineItem'] = $line_items;
        }

        if ( get_option( 'woo_mp_authorize_net_include_billing_details', 'yes' ) == 'yes' ) {
            $request['createTransactionRequest']['transactionRequest']['billTo'] = [
                'firstName'   => $this->trim_chars( \Woo_MP\wc3( $this->order, 'billing_first_name' ), 50 ),
                'lastName'    => $this->trim_chars( \Woo_MP\wc3( $this->order, 'billing_last_name' ), 50 ),
                'company'     => $this->trim_chars( \Woo_MP\wc3( $this->order, 'billing_company' ), 50 ),
                'address'     => $this->get_address( 'billing' ),
                'city'        => $this->trim_chars( \Woo_MP\wc3( $this->order, 'billing_city' ), 40 ),
                'state'       => $this->trim_chars( \Woo_MP\wc3( $this->order, 'billing_state' ), 40 ),
                'zip'         => $this->trim_chars( \Woo_MP\wc3( $this->order, 'billing_postcode' ), 20 ),
                'country'     => $this->trim_chars( \Woo_MP\wc3( $this->order, 'billing_country' ), 60 ),
                'phoneNumber' => $this->trim_chars( \Woo_MP\wc3( $this->order, 'billing_phone' ), 25 )
            ];
        }

        if ( get_option( 'woo_mp_authorize_net_include_shipping_details', 'yes' ) == 'yes' ) {
            $request['createTransactionRequest']['transactionRequest']['shipTo'] = [
                'firstName' => $this->trim_chars( \Woo_MP\wc3( $this->order, 'shipping_first_name' ), 50 ),
                'lastName'  => $this->trim_chars( \Woo_MP\wc3( $this->order, 'shipping_last_name' ), 50 ),
                'company'   => $this->trim_chars( \Woo_MP\wc3( $this->order, 'shipping_company' ), 50 ),
                'address'   => $this->get_address( 'shipping' ),
                'city'      => $this->trim_chars( \Woo_MP\wc3( $this->order, 'shipping_city' ), 40 ),
                'state'     => $this->trim_chars( \Woo_MP\wc3( $this->order, 'shipping_state' ), 40 ),
                'zip'       => $this->trim_chars( \Woo_MP\wc3( $this->order, 'shipping_postcode' ), 20 ),
                'country'   => $this->trim_chars( \Woo_MP\wc3( $this->order, 'shipping_country' ), 60 )
            ];
        }

        $response = $this->request( $request );

        $this->trans_id         = $response['response']['transactionResponse']['transId'];
        $this->last_four_digits = substr( $response['response']['transactionResponse']['accountNumber'], -4 );
        $this->held_for_review  = $response['response']['transactionResponse']['responseCode'] === '4';
        
        $this->do_success();
    }

    /**
     * Make a request to Authorize.Net.
     * 
     * You do not need to supply 'merchantAuthentication'.
     * It will be added to every request automatically.
     * 
     * Errors are automatically handled.
     *
     * @param  array $request The request data.
     * @return array          Associative array with the format specified
     *                        {@see Woo_MP\Payment_Processors\Authorize_Net\Authorize_Net_Client::process_response()} here.
     */
    private function request( $request ) {
        $response = $this->api->request( $request );

        if ( $response['status'] === 'error' ) {
            if ( strpos( $response['message'], 'timed out' ) !== FALSE ) {
                $this->respond( 'error', "Sorry, Authorize.Net did not respond. This means we don't know whether the transaction was successful. Please check your Authorize.Net account to confirm." );
            }

            if ( $response['code'] === 'E00007' ) {
                $this->respond( 'error', 'Sorry, the Login ID, or Transaction Key, or both, are not valid. Please check your settings and try again.' );
            }

            if ( strpos( $response['message'], "transactionKey' element is invalid" ) !== FALSE ) {
                $this->respond( 'error', 'Sorry, the Transaction Key is not valid. Please check your settings and try again.' );
            }

            $this->respond( 'error', $response['message'], [
                'details' => $response['additional_response_code_details']
            ] );
        }

        return $response;
    }

    /**
     * Get an address to send to Authorize.Net.
     *
     * @param  string $type The address type. This can be 'shipping' or 'billing'.
     * @return string       The address.
     */
    private function get_address( $type ) {
        $address = \Woo_MP\wc3( $this->order, "{$type}_address_1" );

        // Authorize.Net doesn't have an address line 2 field.
        if ( \Woo_MP\wc3( $this->order, "{$type}_address_2" ) ) {
            $address .= ' | ' . \Woo_MP\wc3( $this->order, "{$type}_address_2" );
        }

        $address = $this->trim_chars( $address, 60 );

        return $address;
    }

    /**
     * Truncate a string to a given length and add
     * an ellipsis if the string needed to be truncated.
     * The last 3 characters are reserved for the 3 dots.
     * Authorize.Net imposes tight character limits on fields.
     *
     * @param  string $str    The string to truncate.
     * @param  int    $length The character limit.
     * @return string         The truncated string.
     */
    private function trim_chars( $str, $length ) {
        if ( mb_strlen( $str ) > $length ) {
            return trim( substr( $str, 0, $length - 3 ) ) . '...';
        }

        return $str;
    }

}