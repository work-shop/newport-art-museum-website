<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * This is the parent class for payment processors.
 */
class Payment_Processor {
    
    public    $id = '';
    protected $title = '';
    protected $order_id;
    protected $order;
    protected $amount;
    protected $currency;
    protected $capture;
    protected $description;
    protected $trans_id;
    protected $last_four_digits;
    protected $held_for_review = FALSE;
    public    $unknown_error;

    /**
     * Set up initial values.
     */
    protected function __construct() {
        $this->order_id      = $_POST['order_id'];
        $this->order         = new Woo_MP_Order( wc_get_order( $this->order_id ) );
        $this->amount        = $_POST['amount'];
        $this->currency      = $_POST['currency'];
        $this->unknown_error = 'Sorry, there was an error. If you open a support topic, please include the following:<br>';
        $this->capture       = get_option( 'woo_mp_capture_payments', 'yes' ) == 'yes';
        $this->description   = get_option( 'woo_mp_transaction_description', get_option( 'blogname', '' ) );
    }

    /**
     * Send response to client.
     *
     * @param string $status  The status of the transaction.
     * @param string $message Optional message to display to the client.
     * @param array  $data    Optional additional data.
     */
    public function respond( $status, $message = '', $data = [] ) {
        wp_send_json( [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ] );
    }

    /**
     * Do whatever needs to be done after a successful transaction, and then
     * respond to the client.
     */
    protected function do_success() {
        $this->add_charge_note();
        $this->save_charge();
        $this->update_status();
        $this->reduce_stock();

        $message = $this->capture ? 'Payment successfully processed.' : 'Payment successfully authorized.';

        if ( $this->held_for_review ) {
            $message = 'Payment held for review.';
        }

        Notices::add( [
            'message'     => $message,
            'type'        => 'success',
            'dismissible' => TRUE,
            'post_id'     => $this->order_id
        ] );

        $this->respond( 'success' );
    }

    /**
     * Add a note to the order.
     */
    private function add_charge_note() {
        $action = $this->capture ? 'processed' : 'authorized';

        if ( $this->held_for_review ) {
            $action = 'held for review';
        }

        $this->order->add_order_note(
            "Payment $action with card number ending in $this->last_four_digits. Amount: " .
            wc_price( $this->amount, [ 'currency' => $this->currency ] ),
            0,
            true
        );
    }

    /**
     * Save the charge.
     */
    private function save_charge() {
        $this->order->add_woo_mp_payment( [
            'id'              => $this->trans_id,
            'last4'           => $this->last_four_digits,
            'amount'          => $this->amount,
            'currency'        => $this->currency,
            'captured'        => $this->capture,
            'held_for_review' => $this->held_for_review
        ] );

        $should_save_wc_payment = FALSE;

        switch ( get_option( 'woo_mp_save_wc_payment_when', 'first_payment' ) ) {
            case 'first_payment':
                $should_save_wc_payment = ! (
                    \Woo_MP\is_wc3() ? $this->order->get_date_paid( 'edit' ) : $this->order->paid_date
                );
                break;
            case 'every_payment':
                $should_save_wc_payment = TRUE;
                break;
        }

        if ( $should_save_wc_payment ) {
            if ( \Woo_MP\is_wc3() ) {
                $this->order->set_payment_method( $this->title );
                $this->order->set_payment_method_title( $this->title );
                $this->order->set_transaction_id( $this->trans_id );
                $this->order->set_date_paid( time() );
                $this->order->save();
            } else {
                update_post_meta( $this->order_id, '_payment_method', $this->title );
                update_post_meta( $this->order_id, '_payment_method_title', $this->title );
                update_post_meta( $this->order_id, '_transaction_id', $this->trans_id );
                update_post_meta( $this->order_id, '_paid_date', current_time( 'mysql' ) );
            }
        }
    }

    /**
     * Update order status.
     */
    private function update_status() {
        $update_order_status_when = get_option( 'woo_mp_update_order_status_when' );
        $update_order_status_to   = get_option( 'woo_mp_update_order_status_to', 'wc-completed' );

        $should_update_status = FALSE;

        if ( $update_order_status_when === 'any_transaction' ) {
            $should_update_status = TRUE;
        } elseif ( $update_order_status_when === 'total_amount_charged' ) {
            if ( $this->order->get_total_amount_paid() >= $this->order->get_total() ) {
                $should_update_status = TRUE;
            }
        }

        if ( $should_update_status ) {

            // Patch https://github.com/woocommerce/woocommerce/issues/20057.
            if ( \Woo_MP\is_wc3() && version_compare( WC_VERSION, '3.4.0', '<' ) ) {
                if ( ! $this->order->get_date_created( 'edit' ) ) {
                    $this->order->set_date_created( time() );
                }
            }

            $this->order->update_status( $update_order_status_to );
        }
    }

    /**
     * Reduce order item stock levels.
     */
    private function reduce_stock() {
        switch ( get_option( 'woo_mp_reduce_stock_levels_when', 'any_charge' ) ) {
            case 'total_amount_charged':
                if ( $this->order->get_total_amount_paid() < $this->order->get_total() ) {
                    return;
                }

                break;
            case 'never':
                return;
        }

        if ( \Woo_MP\is_wc3() ) {
            wc_maybe_reduce_stock_levels( $this->order_id );
        } else {
            if ( ! get_post_meta( $this->order_id, '_order_stock_reduced', TRUE ) ) {
                $this->order->reduce_order_stock();
            }
        }
    }

    /**
     * Convert an amount to a given currency's smallest denomination.
     * 
     * For example: to_smallest_unit( 9.99, 'USD' ) === 999
     *              to_smallest_unit( 1000, 'JPY' ) === 1000
     *
     * @param  mixed  $amount   The amount to convert.
     * @param  string $currency The currency of the amount.
     * @return int              The amount in the smallest unit.
     */
    protected function to_smallest_unit( $amount, $currency ) {
        $zero_decimal_currencies = [
            'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA',
            'PYG', 'RWF', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'
        ];

        if ( in_array( $currency, $zero_decimal_currencies ) ) {
            return absint( $amount );
        }

        return round( $amount, 2 ) * 100;
    }

}