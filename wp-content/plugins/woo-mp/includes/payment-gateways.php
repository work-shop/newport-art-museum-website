<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Provides a single entry for accessing payment gateways.
 */
class Payment_Gateways {

    /**
     * All payment gateway IDs and their associated class names.
     * 
     * @var array
     */
    private static $gateways = [
        'stripe'        => \Woo_MP\Payment_Processors\Stripe\Stripe_Payment_Processor::class,
        'authorize_net' => \Woo_MP\Payment_Processors\Authorize_Net\Authorize_Net_Payment_Processor::class,
        'eway'          => \Woo_MP\Payment_Processors\Eway\Eway_Payment_Processor::class
    ];

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
        if ( isset( self::$gateways[ get_option( 'woo_mp_payment_processor' ) ] ) ) {
            return new self::$gateways[ get_option( 'woo_mp_payment_processor' ) ];
        }
    }

}