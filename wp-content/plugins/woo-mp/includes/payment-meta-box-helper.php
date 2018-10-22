<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Interface for gateway payment meta box helpers.
 * 
 * The core payment meta box controller uses these classes to add
 * all the gateway-specific parts of the frontend.
 */
interface Payment_Meta_Box_Helper {

    /**
     * Get the currency that payments will be made in.
     * 
     * @param  string $order_currency The order currency.
     * @return string                 The currency.
     */
    public function get_currency( $order_currency );

    /**
     * Get validation messages.
     * 
     * @return array Multidimensional associative array of the following format:
     * 
     * [
     *     [
     *         'message' => '',     // A message for the user.
     *         'type'    => 'info', // Can be: 'error', 'warning', 'success', 'info'
     *         'valid'   => true    // Can the user make payments? Or is this issue a stopper?
     *     ]
     * ]
     */
    public function validation();

    /**
     * Enqueue gateway JS and CSS.
     */
    public function enqueue_assets();

    /**
     * Get gateway-specific data to be made available on the client side via a global 'wooMP' JS object.
     * 
     * @return array Key-value pairs which will be mapped to properties on the global object.
     */
    public function client_data();


    /**
     * Get the templates that the gateway is providing.
     * 
     * @return array Associative array where the keys are template names and the values are the paths to those templates.
     */
    public function get_templates();

}