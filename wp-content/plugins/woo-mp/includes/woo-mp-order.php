<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Transparently adds plugin-specific functionality to a WooCommerce order
 * object (most often an instance of 'WC_Order').
 * 
 * Use it like a regular WooCommerce order object.
 */
class Woo_MP_Order {

    /**
     * The core order object.
     * 
     * This will normally be an instance of 'WC_Order'.
     * 
     * @var mixed
     */
    private $order;

    /**
     * Set the order object to enhance.
     * 
     * @param mixed $order The order object (normally an instance of 'WC_Order').
     */
    public function __construct( $order ) {
        $this->order = $order;
    }

    /**
     * Get a property from the core order object.
     * 
     * @param  string $name The property to get.
     * @return mixed        The property value.
     */
    public function __get( $name ) {
        return $this->order->$name;
    }

    /**
     * Call a method on the core order object.
     * 
     * @param  string $name      The method to call.
     * @param  array  $arguments The arguments to pass to the method.
     * @return mixed             The return value of the core method.
     */
    public function __call( $name, $arguments ) {
        return call_user_func_array( [ $this->order, $name ], $arguments );
    }

    /**
     * Get manual payments.
     * 
     * @return array All manual payments.
     */
    public function get_woo_mp_payments() {
        return json_decode(
            get_post_meta( \Woo_MP\wc3( $this, 'id' ), 'woo-mp-' . WOO_MP_PAYMENT_PROCESSOR . '-charges', TRUE ) ?: '[]',
            TRUE
        );
    }

    /**
     * Add a manual payment.
     * 
     * @param array $payment Associative array of the following format:
     * 
     * [
     *     'id'              => '',    // The transaction ID.
     *     'last4'           => '',    // The last four digits of the card that was charged.
     *     'amount'          => 0,     // The payment amount.
     *     'currency'        => '',    // The currency the payment was made in. This should be a 3-digit code.
     *     'captured'        => FALSE, // Whether the charge was captured.
     *     'held_for_review' => FALSE  // Whether the charge was held for review.
     * ]
     */
    public function add_woo_mp_payment( $payment ) {
        $payment += [
            'id'              => '',
            'date'            => current_time( 'M d, Y' ),
            'last4'           => '',
            'amount'          => 0,
            'currency'        => '',
            'captured'        => FALSE,
            'held_for_review' => FALSE
        ];

        $payments = $this->get_woo_mp_payments();

        $payments[] = $payment;

        update_post_meta(
            \Woo_MP\wc3( $this, 'id' ),
            'woo-mp-' . WOO_MP_PAYMENT_PROCESSOR . '-charges',
            json_encode( $payments )
        );
    }

    /**
     * Get the total amount paid.
     * 
     * @return float The amount.
     */
    public function get_total_amount_paid() {
        return array_sum( array_column( $this->get_woo_mp_payments(), 'amount' ) );
    }

    /**
     * Get the total amount unpaid.
     * 
     * If the amount paid is greater than the order total, a negative number will be returned.
     * 
     * @return float The amount.
     */
    public function get_total_amount_unpaid() {
        return $this->order->get_total() - $this->get_total_amount_paid();
    }

}