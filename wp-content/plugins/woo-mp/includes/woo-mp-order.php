<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Transparently adds plugin-specific functionality to a WooCommerce order
 * object (most often an instance of 'WC_Order').
 * 
 * Use it like a regular WooCommerce order object.
 */
class Woo_MP_Order extends WC_Compatibility\WC_Order {

    /**
     * Get a list of charge properties and their default values.
     * 
     * This is for normalizing charge records across versions.
     * 
     * @return array The properties and their defaults.
     */
    private function get_charge_defaults() {
        return [
            'id'              => '',
            'date'            => current_time( 'M d, Y' ),
            'last4'           => '',
            'amount'          => 0,
            'currency'        => '',
            'captured'        => false,
            'held_for_review' => false
        ];
    }

    /**
     * Get manual payments.
     * 
     * @return array All manual payments.
     */
    public function get_woo_mp_payments() {
        $payments = json_decode(
            $this->get_meta( 'woo-mp-' . WOO_MP_PAYMENT_PROCESSOR . '-charges', true ),
            true
        ) ?: [];

        $charge_defaults = $this->get_charge_defaults();

        foreach ( $payments as $key => $payment ) {
            $payments[ $key ] += $charge_defaults;
        }

        return $payments;
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
     *     'captured'        => false, // Whether the charge was captured.
     *     'held_for_review' => false  // Whether the charge was held for review.
     * ]
     */
    public function add_woo_mp_payment( $payment ) {
        $payment += $this->get_charge_defaults();

        $payments = $this->get_woo_mp_payments();

        $payments[] = $payment;

        $this->update_meta_data( 'woo-mp-' . WOO_MP_PAYMENT_PROCESSOR . '-charges', json_encode( $payments ) );
    }

    /**
     * Get the total amount paid.
     * 
     * @param  string $currency The currency code of the payments to include in the calculation.
     *                          Default is order currency.
     * @return float            The amount.
     */
    public function get_total_amount_paid( $currency = '' ) {
        if ( ! $currency ) {
            $currency = $this->get_currency();
        }

        $payments = array_filter( $this->get_woo_mp_payments(), function ( $payment ) use ( $currency ) {
            return $payment['currency'] === $currency;
        } );

        return array_sum( array_column( $payments, 'amount' ) );
    }

    /**
     * Get the total amount unpaid.
     * 
     * If the amount paid is greater than the order total, a negative number will be returned.
     * 
     * @return float The amount.
     */
    public function get_total_amount_unpaid() {
        return $this->get_total() - $this->get_total_amount_paid();
    }

}