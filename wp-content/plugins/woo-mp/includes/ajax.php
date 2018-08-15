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

    public function charge() {
        $gateway = Payment_Gateways::get_active();
        
        try {
            $gateway->process_payment();
        } catch ( \Exception $e ) {
            $gateway->respond( 'error', $gateway->unknown_error . $e->getMessage() );
        }

        die;
    }

    public function get_charge_amount_suggestions() {
        if ( empty( $_REQUEST['order_id'] ) ) {
            wp_send_json( "Field 'order_id' is required." );
        }

        $wc_order = wc_get_order( $_REQUEST['order_id'] );

        if ( ! $wc_order ) {
            wp_send_json( "Order with ID '$_REQUEST[order_id]' not found." );
        }

        $order = new Woo_MP_Order( $wc_order );

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
        update_site_option( 'woo_mp_rated', TRUE );

        die;
    }

}

new AJAX();