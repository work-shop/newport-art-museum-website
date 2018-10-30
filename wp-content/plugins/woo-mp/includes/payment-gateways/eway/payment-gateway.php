<?php

namespace Woo_MP\Payment_Gateways\Eway;

defined( 'ABSPATH' ) || die;

/**
 * eWAY payment gateway.
 */
class Payment_Gateway extends \Woo_MP\Payment_Gateway {

    /**
     * Set up initial values.
     */
    public function __construct() {
        $this->id           = 'eway';
        $this->title        = 'eWAY';
        $this->custom_title = get_option( 'woo_mp_eway_title', 'Credit Card (eWAY)' );
    }

    public function get_settings() {
        $text_style = 'width: 400px;';

        return [
            [
                'title' => __( 'API Keys', 'woo-mp' ),
                'type'  => 'title',
                'desc'  => WOO_MP_CONFIG_HELP
            ],
            [
                'title' => __( 'API Key', 'woo-mp' ),
                'type'  => 'text',
                'id'    => 'woo_mp_eway_api_key',
                'css'   => $text_style
            ],
            [
                'title' => __( 'API Password', 'woo-mp' ),
                'type'  => 'text',
                'id'    => 'woo_mp_eway_api_password',
                'css'   => $text_style
            ],
            [
                'title' => __( 'Sandbox Mode', 'woo-mp' ),
                'type'  => 'checkbox',
                'desc'  => __( 'Enable sandbox mode.', 'woo-mp' ),
                'id'    => 'woo_mp_eway_sandbox_mode'
            ],
            [
                'type' => 'sectionend'
            ],
            [
                'title' => __( 'Settings', 'woo-mp' ),
                'type'  => 'title'
            ],
            [
                'title'    => __( 'Title', 'woo-mp' ),
                'type'     => 'text',
                'desc'     => __( 'Choose a payment method title.', 'woo-mp' ),
                'id'       => 'woo_mp_eway_title',
                'default'  => 'Credit Card (eWAY)',
                'desc_tip' => true,
                'css'      => $text_style
            ],
            [
                'title'         => __( 'Include Order Details', 'woo-mp' ),
                'type'          => 'checkbox',
                'checkboxgroup' => 'start',
                'desc'          => __( 'Send order billing details to eWAY.', 'woo-mp' ),
                'id'            => 'woo_mp_eway_include_billing_details',
                'default'       => 'yes'
            ],
            [
                'type'          => 'checkbox',
                'checkboxgroup' => 'end',
                'desc'          => __( 'Send order shipping details to eWAY.', 'woo-mp' ),
                'id'            => 'woo_mp_eway_include_shipping_details',
                'default'       => 'yes'
            ],
            [
                'type' => 'sectionend'
            ]
        ];
    }

    public function get_payment_meta_box_helper() {
        return new Payment_Meta_Box_Helper();
    }

    public function get_payment_processor( $params ) {
        return new Payment_Processor( $params, $this->custom_title );
    }

}