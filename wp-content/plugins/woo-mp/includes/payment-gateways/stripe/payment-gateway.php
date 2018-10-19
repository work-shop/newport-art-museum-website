<?php

namespace Woo_MP\Payment_Gateways\Stripe;

defined( 'ABSPATH' ) || die;

/**
 * Stripe payment gateway.
 */
class Payment_Gateway extends \Woo_MP\Payment_Gateway {

    /**
     * Set up initial values.
     */
    public function __construct() {
        $this->id           = 'stripe';
        $this->title        = 'Stripe';
        $this->custom_title = get_option( 'woo_mp_stripe_title', 'Credit Card (Stripe)' );
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
                'title' => __( 'Secret Key', 'woo-mp' ),
                'type'  => 'text',
                'desc'  => __( 'Your Stripe Secret Key.', 'woo-mp' ),
                'id'    => 'woo_mp_stripe_secret_key',
                'css'   => $text_style
            ],
            [
                'title' => __( 'Publishable Key', 'woo-mp' ),
                'type'  => 'text',
                'desc'  => __( 'Your Stripe Publishable Key.', 'woo-mp' ),
                'id'    => 'woo_mp_stripe_publishable_key',
                'css'   => $text_style
            ],
            [
                'type' => 'sectionend'
            ],
            [
                'title' => __( 'Settings', 'woo-mp' ),
                'type'  => 'title'
            ],
            [
                'title'   => __( 'Title', 'woo-mp' ),
                'type'    => 'text',
                'desc'    => __( 'Choose a payment method title.', 'woo-mp' ),
                'id'      => 'woo_mp_stripe_title',
                'default' => 'Credit Card (Stripe)',
                'css'     => $text_style
            ],
            [
                'title'   => __( 'Include Customer Name and Email', 'woo-mp' ),
                'type'    => 'checkbox',
                'desc'    => __( "Send customer's billing name and email to Stripe.", 'woo-mp' ),
                'id'      => 'woo_mp_stripe_include_name_and_email',
                'default' => 'yes'
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