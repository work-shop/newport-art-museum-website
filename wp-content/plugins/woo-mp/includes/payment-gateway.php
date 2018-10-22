<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Represents a payment gateway.
 */
abstract class Payment_Gateway {

    /**
     * The gateway ID.
     * 
     * @var string
     */
    protected $id = '';

    /**
     * The official gateway title.
     * 
     * @var string
     */
    protected $title = '';

    /**
     * The user-chosen title.
     * 
     * @var string
     */
    protected $custom_title = '';

    /**
     * Get the official gateway title.
     * 
     * @return string The title.
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Get the gateway settings.
     * 
     * @return array The settings.
     */
    abstract public function get_settings();

    /**
     * Get an instance of the gateway's payment meta box helper.
     * 
     * The core payment meta box controller uses this class to add
     * all the gateway-specific parts of the frontend.
     * 
     * @return object The helper.
     */
    abstract public function get_payment_meta_box_helper();

    /**
     * Get an instance of the gateway's payment processor.
     * 
     * This class should extend 'Woo_MP\Payment_Processor'.
     * 
     * @param  array                    $params The payment information. See Payment_Processor::__construct() for details.
     * @return Woo_MP\Payment_Processor         The payment processor.
     */
    abstract public function get_payment_processor( $params );

}