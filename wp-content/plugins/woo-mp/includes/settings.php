<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

class Settings extends \WC_Settings_Page {

    /**
     * A list of all payment gateways with gateway IDs as keys and instances of the main gateway objects as values.
     * 
     * @var array
     */
    private $payment_gateways = [];

    /**
     * Set up hooks.
     */
    public function __construct() {
        $this->id = 'manual_payment';

        $this->payment_gateways = Payment_Gateways::get_all();

        add_filter( 'woocommerce_settings_tabs_array', [ $this, 'add_settings_tab' ], 50 );
        add_action( 'woocommerce_sections_' . $this->id, [ $this, 'output_sections' ] );
        add_action( 'woocommerce_settings_' . $this->id, [ $this, 'output' ] );
        add_action( 'woocommerce_settings_save_' . $this->id, [ $this, 'save' ] );
    }

    /**
     * Add new settings tab.
     */
    public function add_settings_tab( $settings_tabs ) {
        $settings_tabs[ $this->id ] = __( 'Manual Payment', 'woo-mp' );
        return $settings_tabs;
    }

    /**
     * Add new sections.
     */
    public function get_sections() {
        $sections = [ '' => __( 'General', 'woo-mp' ) ];

        foreach ( $this->payment_gateways as $id => $gateway ) {
            $sections[ $id ] = $gateway->get_title();
        }

        return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
    }

    /**
     * Add settings to the new sections.
     */
    public function get_settings( $section = null ) {
        $text_style = 'width: 400px;';

        $settings = [];

        if ( $section === '' ) {
            $settings = [
                [
                    'title' => __( 'Settings', 'woo-mp' ),
                    'type'  => 'title',
                    'desc'  => WOO_MP_CONFIG_HELP
                ],
                [
                    'title'             => __( 'Payment Processor', 'woo-mp' ),
                    'desc'              => __( 'Choose your payment processor.', 'woo-mp' ),
                    'id'                => 'woo_mp_payment_processor',
                    'type'              => 'select',
                    'class'             => 'wc-enhanced-select',
                    'custom_attributes' => [
                        'data-placeholder' => __( 'Select a payment processor...', 'woo-mp' ),
                    ],
                    'desc_tip'          => true,
                    'options'           => [
                        ''              => '',
                        'stripe'        => __( 'Stripe', 'woo-mp' ),
                        'authorize_net' => __( 'Authorize.Net', 'woo-mp' ),
                        'eway'          => __( 'eWAY', 'woo-mp' )
                    ]
                ],
                [
                    'title'    => __( 'Transaction Description', 'woo-mp' ),
                    'desc'     => __( "How this description is used depends on your payment processor. Usually it shows up in your payment processor's dashboard, and in your customer's transaction history.", 'woo-mp' ),
                    'id'       => 'woo_mp_transaction_description',
                    'type'     => 'text',
                    'default'  => get_option( 'blogname' ),
                    'desc_tip' => true,
                    'css'      => $text_style,
                ],
                [
                    'title'   => __( 'Capture Payments', 'woo-mp' ),
                    'desc'    => __( "Capture payments immediately. If unchecked, payments will only be authorized.", 'woo-mp' ),
                    'id'      => 'woo_mp_capture_payments',
                    'default' => 'yes',
                    'type'    => 'checkbox',
                ],
                [
                    'title'    => __( 'Update Order Status When', 'woo-mp' ),
                    'desc'     => __( 'Choose when you want order statuses to be updated.', 'woo-mp' ),
                    'id'       => 'woo_mp_update_order_status_when',
                    'type'     => 'select',
                    'class'    => 'wc-enhanced-select',
                    'desc_tip' => true,
                    'options'  => [
                        ''                     => __( "Don't update order statuses", 'woo-mp' ),
                        'any_transaction'      => __( 'A payment or authorization is made', 'woo-mp' ),
                        'total_amount_charged' => __( 'The total amount has been paid or authorized', 'woo-mp' )
                    ]
                ],
                [
                    'title'    => __( 'Update Order Status To', 'woo-mp' ),
                    'desc'     => __( 'Choose which status orders should be updated to when the above condition is fulfilled.', 'woo-mp' ),
                    'id'       => 'woo_mp_update_order_status_to',
                    'type'     => 'select',
                    'class'    => 'wc-enhanced-select',
                    'desc_tip' => true,
                    'default'  => 'wc-completed',
                    'options'  => wc_get_order_statuses()
                ],
                [
                    'title'    => __( 'Save WooCommerce Payment Record When', 'woo-mp' ),
                    'desc'     => __( 'Choose when you want an official (native) WooCommerce payment record to be saved to an order. Please note that WooCommerce only supports one official payment per order. This means that if you choose to save a record any time a payment or authorization is made, previous payment information will be overwritten. You will still be able to see past payments in the <em>Order notes</em> section.', 'woo-mp' ),
                    'id'       => 'woo_mp_save_wc_payment_when',
                    'type'     => 'select',
                    'class'    => 'wc-enhanced-select',
                    'desc_tip' => true,
                    'default'  => 'first_payment',
                    'options'  => [
                        'first_payment' => __( 'The first payment or authorization is made', 'woo-mp' ),
                        'every_payment' => __( 'Any payment or authorization is made (see help tip)', 'woo-mp' ),
                        'never'         => __( "Don't save WooCommerce payment records", 'woo-mp' )
                    ]
                ],
                [
                    'title'    => __( 'Reduce Stock Levels When', 'woo-mp' ),
                    'desc'     => __( 'Choose when you want order item stock levels to be reduced. Stock levels will never be reduced more than once. Please note that this option only applies when stock management is enabled at both the global and product level.', 'woo-mp' ),
                    'id'       => 'woo_mp_reduce_stock_levels_when',
                    'type'     => 'select',
                    'class'    => 'wc-enhanced-select',
                    'desc_tip' => true,
                    'default'  => 'any_charge',
                    'options'  => [
                        'any_charge'           => __( 'A payment or authorization is made', 'woo-mp' ),
                        'total_amount_charged' => __( 'The total amount has been paid or authorized', 'woo-mp' ),
                        'never'                => __( "Don't reduce stock levels", 'woo-mp' )
                    ]
                ],
                [
                    'type' => 'sectionend'
                ]
            ];
        } else {
            if ( isset( $this->payment_gateways[ $section ] ) ) {
                $settings = $this->payment_gateways[ $section ]->get_settings();
            }
        }

        return apply_filters( 'woocommerce_get_settings_manual_payment', $settings, $section );
    }

    /**
     * Output the settings.
     */
    public function output() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        \WC_Admin_Settings::output_fields( $settings );
    }

    /**
     * Save settings.
     */
    public function save() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        \WC_Admin_Settings::save_fields( $settings );
    }

}