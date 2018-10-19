<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Provides a single entry point for accessing payment gateways.
 */
class Payment_Gateways {

    /**
     * All payment gateway IDs and their associated class names.
     * 
     * @var array
     */
    private static $gateways = [
        'stripe'        => \Woo_MP\Payment_Gateways\Stripe\Payment_Gateway::class,
        'authorize_net' => \Woo_MP\Payment_Gateways\Authorize_Net\Payment_Gateway::class,
        'eway'          => \Woo_MP\Payment_Gateways\Eway\Payment_Gateway::class
    ];

    /**
     * Get all payment gateway IDs.
     * 
     * @return array Gateway IDs.
     */
    public static function get_all_ids() {
        return array_keys( self::$gateways );
    }

    /**
     * Get the active payment gateway ID.
     * 
     * @return string|null The gateway ID.
     */
    public static function get_active_id() {
        $id = get_option( 'woo_mp_payment_processor' );

        if ( in_array( $id, self::get_all_ids() ) ) {
            return $id;
        }
    }

    /**
     * Get all payment gateways.
     * 
     * @return array Gateway instances.
     */
    public static function get_all() {
        return array_map( function ( $gateway ) {
            return new $gateway();
        }, self::$gateways );
    }

    /**
     * Get the active payment gateway.
     * 
     * @return object|null An instance of the gateway.
     */
    public static function get_active() {
        if ( self::get_active_id() ) {
            return new self::$gateways[ self::get_active_id() ];
        }
    }

}