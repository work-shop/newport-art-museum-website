<?php

namespace Woo_MP\Payment_Gateways\Authorize_Net;

defined( 'ABSPATH' ) || die;

/**
 * Process a payment with Authorize.Net.
 */
class Payment_Processor extends \Woo_MP\Payment_Processor {

    /**
     * The payment nonce.
     * 
     * @var string
     */
    private $token;

    /**
     * The portion of the order total that is made up of taxes.
     * 
     * @var mixed
     */
    private $tax_amount;

    /**
     * The portion of the order total that is made up of duties.
     * 
     * @var mixed
     */
    private $duty_amount;

    /**
     * The portion of the order total that is made up of freight/shipping fees.
     * 
     * @var mixed
     */
    private $freight_amount;

    /**
     * Whether the order is tax-exempt.
     * 
     * @var bool
     */
    private $tax_exempt;

    /**
     * The purchase order number.
     * 
     * @var mixed
     */
    private $po_number;

    private $api;

    /**
     * Set up initial values.
     * 
     * @param array $params The payment information.
     * 
     * [
     *     'token'          => 'abc123', // The payment nonce.
     *     'tax_amount'     => 1.23,     // The portion of the order total that is made up of taxes.
     *     'duty_amount'    => 4.56,     // The portion of the order total that is made up of duties.
     *     'freight_amount' => 7.89,     // The portion of the order total that is made up of freight/shipping fees.
     *     'tax_exempt'     => false,    // Whether the order is tax-exempt.
     *     'po_number'      => 'xyz789'  // The purchase order number.
     * ]
     * 
     * See \Woo_MP\Payment_Processor::__construct() for fields that are required for all payment gateways.
     */
    public function __construct( $params, $title = '' ) {
        parent::__construct( $params, $title );

        $this->token          = $params['token'];
        $this->tax_amount     = $params['tax_amount'];
        $this->duty_amount    = $params['duty_amount'];
        $this->freight_amount = $params['freight_amount'];
        $this->tax_exempt     = $params['tax_exempt'];
        $this->po_number      = $params['po_number'];

        $this->api = new Client( [
            'login_id'        => get_option( 'woo_mp_authorize_net_login_id' ),
            'transaction_key' => get_option( 'woo_mp_authorize_net_transaction_key' ),
            'sandbox'         => get_option( 'woo_mp_authorize_net_test_mode' ) == 'yes'
        ] );
    }

    /**
     * Process a payment.
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
                            'dataValue'      => $this->token
                        ]
                    ],
                    'order'           => [
                        'invoiceNumber' => $this->order->get_order_number(),
                        'description'   => $this->trim_chars( $this->description, 255 )
                    ],
                    'lineItems'       => [],
                    'tax'             => [
                        'amount' => $this->tax_amount
                    ],
                    'duty'            => [
                        'amount' => $this->duty_amount
                    ],
                    'shipping'        => [
                        'amount' => $this->freight_amount,
                    ],
                    'taxExempt'       => $this->tax_exempt,
                    'poNumber'        => $this->po_number,
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
                    'taxable'     => $product ? $product->is_taxable() : true
                ];
            }

            $request['createTransactionRequest']['transactionRequest']['lineItems']['lineItem'] = $line_items;
        }

        if ( get_option( 'woo_mp_authorize_net_include_billing_details', 'yes' ) == 'yes' ) {
            $request['createTransactionRequest']['transactionRequest']['billTo'] = [
                'firstName'   => $this->trim_chars( $this->order->get_billing_first_name(), 50 ),
                'lastName'    => $this->trim_chars( $this->order->get_billing_last_name(), 50 ),
                'company'     => $this->trim_chars( $this->order->get_billing_company(), 50 ),
                'address'     => $this->get_address( 'billing' ),
                'city'        => $this->trim_chars( $this->order->get_billing_city(), 40 ),
                'state'       => $this->trim_chars( $this->order->get_billing_state(), 40 ),
                'zip'         => $this->trim_chars( $this->order->get_billing_postcode(), 20 ),
                'country'     => $this->trim_chars( $this->order->get_billing_country(), 60 ),
                'phoneNumber' => $this->trim_chars( $this->order->get_billing_phone(), 25 )
            ];
        }

        if ( get_option( 'woo_mp_authorize_net_include_shipping_details', 'yes' ) == 'yes' ) {
            $request['createTransactionRequest']['transactionRequest']['shipTo'] = [
                'firstName' => $this->trim_chars( $this->order->get_shipping_first_name(), 50 ),
                'lastName'  => $this->trim_chars( $this->order->get_shipping_last_name(), 50 ),
                'company'   => $this->trim_chars( $this->order->get_shipping_company(), 50 ),
                'address'   => $this->get_address( 'shipping' ),
                'city'      => $this->trim_chars( $this->order->get_shipping_city(), 40 ),
                'state'     => $this->trim_chars( $this->order->get_shipping_state(), 40 ),
                'zip'       => $this->trim_chars( $this->order->get_shipping_postcode(), 20 ),
                'country'   => $this->trim_chars( $this->order->get_shipping_country(), 60 )
            ];
        }

        $request = apply_filters( 'woo_mp_authorize_net_charge_request', $request, $this->order->get_core_order() );

        $response = $this->request( $request );

        $this->trans_id         = $response['response']['transactionResponse']['transId'];
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
     *                        {@see Woo_MP\Payment_Gateways\Authorize_Net\Client::process_response()} here.
     */
    private function request( $request ) {
        $response = $this->api->request( $request );

        if ( $response['status'] === 'error' ) {
            $message = $response['message'];

            if ( strpos( $message, 'timed out' ) !== false ) {
                $message = "Sorry, Authorize.Net did not respond. This means we don't know whether the transaction was successful. Please check your Authorize.Net account to confirm.";
            }

            if ( $response['code'] === 'E00007' ) {
                $message = 'Sorry, the Login ID, or Transaction Key, or both, are not valid. Please check your settings and try again.';
            }

            if ( strpos( $message, "transactionKey' element is invalid" ) !== false ) {
                $message = 'Sorry, the Transaction Key is not valid. Please check your settings and try again.';
            }

            throw new \Woo_MP\Detailed_Exception( $message, $response['code'], [ 'response' => $response ] );
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
        $address = $this->order->{"get_{$type}_address_1"}();

        // Authorize.Net doesn't have an address line 2 field.
        if ( $address_2 = $this->order->{"get_{$type}_address_2"}() ) {
            $address .= ' | ' . $address_2;
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