<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

class Settings extends \WC_Settings_Page {

    /**
     * Set up hooks.
     */
    public function __construct() {
        $this->id = 'manual_payment';

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
        $sections = [
            ''              => __( 'General', 'woo-mp' ),
            'stripe'        => __( 'Stripe', 'woo-mp' ),
            'authorize_net' => __( 'Authorize.Net', 'woo-mp' ),
            'eway'          => __( 'eWAY', 'woo-mp' )
        ];

        return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
    }

    /**
     * Add settings to the new sections.
     */
    public function get_settings( $section = null ) {
        $text_style = 'width: 400px;';

        $settings = [];

        switch ( $section ) {
            case '' :
                $settings = [
                    [
                        'title'    => __( 'Settings', 'woo-mp' ),
                        'type'     => 'title',
                        'desc'     => WOO_MP_CONFIG_HELP
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
                        'desc_tip'          => TRUE,
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
                        'desc_tip' => TRUE,
				        'css'      => $text_style,
                    ],
                    [
				        'title'    => __( 'Capture Payments', 'woo-mp' ),
				        'desc'     => __( "Capture payments immediately. If unchecked, payments will only be authorized.", 'woo-mp' ),
				        'id'       => 'woo_mp_capture_payments',
                        'default'  => 'yes',
				        'type'     => 'checkbox',
                    ],
                    [
				        'title'    => __( 'Update Order Status When:', 'woo-mp' ),
				        'desc'     => __( 'Choose when you want order statuses to be updated.', 'woo-mp' ),
				        'id'       => 'woo_mp_update_order_status_when',
				        'type'     => 'select',
                        'class'    => 'wc-enhanced-select',
                        'desc_tip' => TRUE,
				        'options'  => [
                            ''                     => __( "Don't update order statuses", 'woo-mp' ),
					        'any_transaction'      => __( 'A payment or authorization is made', 'woo-mp' ),
					        'total_amount_charged' => __( 'The total amount has been paid or authorized', 'woo-mp' )
                        ]
                    ],
                    [
				        'title'    => __( 'Update Order Status To:', 'woo-mp' ),
				        'desc'     => __( 'Choose which status orders should be updated to when the above condition is fulfilled.', 'woo-mp' ),
				        'id'       => 'woo_mp_update_order_status_to',
				        'type'     => 'select',
                        'class'    => 'wc-enhanced-select',
                        'desc_tip' => TRUE,
                        'default'  => 'wc-completed',
				        'options'  => wc_get_order_statuses()
                    ],
                    [
				        'title'    => __( 'Save WooCommerce Payment Record When:', 'woo-mp' ),
				        'desc'     => __( 'Choose when you want an official (native) WooCommerce payment record to be saved to an order. Please note that WooCommerce only supports one official payment per order. This means that if you choose to save a record any time a payment is made, previous payment information will be overwritten. You will still be able to see past payments in the <em>Order notes</em> section.', 'woo-mp' ),
				        'id'       => 'woo_mp_save_wc_payment_when',
				        'type'     => 'select',
                        'class'    => 'wc-enhanced-select',
                        'desc_tip' => TRUE,
                        'default'  => 'first_payment',
				        'options'  => [
                            'first_payment' => __( 'The first payment is made', 'woo-mp' ),
                            'every_payment' => __( 'Any payment is made (see help tip)', 'woo-mp' ),
                            'never'         => __( 'Never', 'woo-mp' )
                        ]
                    ],
                    [
				        'title'    => __( 'Reduce Stock Levels When:', 'woo-mp' ),
				        'desc'     => __( 'Choose when you want order item stock levels to be reduced. Stock levels will never be reduced more than once. Please note that this option only applies when stock management is enabled at both the global and product level.', 'woo-mp' ),
				        'id'       => 'woo_mp_reduce_stock_levels_when',
				        'type'     => 'select',
                        'class'    => 'wc-enhanced-select',
                        'desc_tip' => TRUE,
                        'default'  => 'any_charge',
				        'options'  => [
					        'any_charge'           => __( 'A payment or authorization is made', 'woo-mp' ),
                            'total_amount_charged' => __( 'The total amount has been paid or authorized', 'woo-mp' ),
                            'never'                => __( "Don't reduce stock levels", 'woo-mp' )
                        ]
                    ],
                    [
                        'type'     => 'sectionend'
                    ]
                ];

                break;
            case 'stripe':
                $settings = [
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
                        'type'  => 'sectionend'
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
                        'type'  => 'sectionend'
                    ]
                ];

                break;
            case 'authorize_net':
                $settings = [
                    [
                        'title' => __( 'API Keys', 'woo-mp' ),
                        'type'  => 'title',
                        'desc'  => WOO_MP_CONFIG_HELP
                    ],
                    [
                        'title' => __( 'Login ID', 'woo-mp' ),
                        'type'  => 'text',
                        'desc'  => __( 'Your Authorize.Net API Login ID.', 'woo-mp' ),
                        'id'    => 'woo_mp_authorize_net_login_id',
                        'css'   => $text_style
                    ],
                    [
                        'title' => __( 'Transaction Key', 'woo-mp' ),
                        'type'  => 'text',
                        'desc'  => __( 'Your Authorize.Net Transaction Key.', 'woo-mp' ),
                        'id'    => 'woo_mp_authorize_net_transaction_key',
                        'css'   => $text_style
                    ],
                    [
                        'title' => __( 'Client Key', 'woo-mp' ),
                        'type'  => 'text',
                        'desc'  => __( 'Your Authorize.Net Client Key.', 'woo-mp' ),
                        'id'    => 'woo_mp_authorize_net_client_key',
                        'css'   => $text_style
                    ],
                    [
                        'title' => __( 'Test Mode', 'woo-mp' ),
                        'type'  => 'checkbox',
                        'desc'  => __( 'Enable sandbox mode.', 'woo-mp' ),
                        'id'    => 'woo_mp_authorize_net_test_mode'
                    ],
                    [
                        'type'  => 'sectionend'
                    ],
                    [
                        'title' => __( 'Settings', 'woo-mp' ),
                        'type'  => 'title'
                    ],
                    [
                        'title'   => __( 'Title', 'woo-mp' ),
                        'type'    => 'text',
                        'desc'    => __( 'Choose a payment method title.', 'woo-mp' ),
                        'id'      => 'woo_mp_authorize_net_title',
                        'default' => 'Credit Card (Authorize.Net)',
                        'css'     => $text_style
                    ],
                    [
                        'title'         => __( 'Include Order Details', 'woo-mp' ),
                        'type'          => 'checkbox',
                        'checkboxgroup' => 'start',
                        'desc'          => __( 'Send order billing details to Authorize.Net.', 'woo-mp' ),
                        'id'            => 'woo_mp_authorize_net_include_billing_details',
                        'default'       => 'yes'
                    ],
                    [
                        'type'    => 'checkbox',
                        'checkboxgroup'   => '',
                        'desc'            => __( 'Send order shipping details to Authorize.Net.', 'woo-mp' ),
                        'id'              => 'woo_mp_authorize_net_include_shipping_details',
                        'default'         => 'yes'
                    ],
                    [
                        'type'          => 'checkbox',
                        'checkboxgroup' => 'end',
                        'desc'          => __( 'Send order line item details to Authorize.Net.', 'woo-mp' ),
                        'id'            => 'woo_mp_authorize_net_include_item_details',
                        'default'       => 'yes'
                    ],
                    [
                        'type'  => 'sectionend'
                    ]
                ];

                break;
            case 'eway':
                $settings = [
                    [
                        'title' => __( 'API Keys', 'woo-mp' ),
                        'type'  => 'title',
                        'desc'  => WOO_MP_CONFIG_HELP
                    ],
                    [
                        'title' => __( 'API Key', 'woo-mp' ),
                        'type'  => 'text',
                        'desc'  => __( 'Your eWAY API Key.', 'woo-mp' ),
                        'id'    => 'woo_mp_eway_api_key',
                        'css'   => $text_style
                    ],
                    [
                        'title' => __( 'API Password', 'woo-mp' ),
                        'type'  => 'text',
                        'desc'  => __( 'Your eWAY API Password.', 'woo-mp' ),
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
                        'type'  => 'sectionend'
                    ],
                    [
                        'title' => __( 'Settings', 'woo-mp' ),
                        'type'  => 'title'
                    ],
                    [
                        'title'   => __( 'Title', 'woo-mp' ),
                        'type'    => 'text',
                        'desc'    => __( 'Choose a payment method title.', 'woo-mp' ),
                        'id'      => 'woo_mp_eway_title',
                        'default' => 'Credit Card (eWAY)',
                        'css'     => $text_style
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
                        'type'  => 'sectionend'
                    ]
                ];

                break;
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