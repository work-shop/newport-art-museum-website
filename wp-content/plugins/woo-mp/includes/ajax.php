<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Set up AJAX hooks and handlers.
 */
class AJAX {

    public function __construct() {
        add_action( 'wp_ajax_woo_mp_charge', [ $this, 'charge' ] );
        add_action( 'wp_ajax_woo_mp_get_charge_amount_suggestions', [ $this, 'get_charge_amount_suggestions' ] );
        add_action( 'wp_ajax_woo_mp_rated', [ $this, 'woo_mp_rated' ] );
    }

    /**
     * Send response to client.
     *
     * @param string $status  The status of the operation.
     * @param string $message Optional message.
     * @param mixed  $code    Optional code. Useful for errors.
     * @param mixed  $data    Optional additional data.
     */
    public static function respond( $status, $message = '', $code = null, $data = null ) {
        wp_send_json( [
            'status'  => $status,
            'message' => $message,
            'code'    => $code,
            'data'    => $data
        ] );
    }

    public function charge() {
        check_ajax_referer( 'woo_mp_charge_' . ( isset( $_REQUEST['order_id'] ) ? $_REQUEST['order_id'] : 0 ) );

        if ( isset( $_REQUEST['gateway_id'] ) && $_REQUEST['gateway_id'] !== Payment_Gateways::get_active_id() ) {
            $this->respond(
                'error',
                'The active payment gateway has been switched.' .
                ' Please refresh the page and try again.'
            );
        }

        try {
            $data = Payment_Gateways::get_active()->get_payment_processor( $_POST )->process_payment();

            $this->respond( 'success', '', null, $data );
        } catch ( Detailed_Exception $e ) {
            $this->respond( 'error', $e->getMessage(), $e->getCode(), $e->get_data() );
        } catch ( \Exception $e ) {
            $this->respond( 'error', 'An error has occured: ' . $e->getMessage(), $e->getCode() );
        }

        die;
    }

    public function get_charge_amount_suggestions() {
        if ( empty( $_REQUEST['order_id'] ) ) {
            wp_send_json( "Field 'order_id' is required." );
        }

        if ( empty( $_REQUEST['currency'] ) ) {
            wp_send_json( "Field 'currency' is required." );
        }

        $wc_order = wc_get_order( $_REQUEST['order_id'] );

        if ( ! $wc_order ) {
            wp_send_json( "Order with ID '$_REQUEST[order_id]' not found." );
        }

        $order = new Woo_MP_Order( $wc_order );

        if ( $_REQUEST['currency'] !== $order->get_currency() ) {
            wp_send_json( (object) [] );
        }

        $suggestions = [
            'Order Total'   => (string) $order->get_total(),
            'Amount Unpaid' => (string) $order->get_total_amount_unpaid()
        ];

        $suggestions = (object) array_filter(
            array_unique( $suggestions ),
            function ( $amount ) { return $amount > 0; }
        );

        wp_send_json( $suggestions );
    }

    public function woo_mp_rated() {
        update_site_option( 'woo_mp_rated', true );

        die;
    }

}

new AJAX();