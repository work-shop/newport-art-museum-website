<?php
/*
    Plugin Name: Flexible Checkout Fields
    Plugin URI: https://www.wpdesk.net/products/flexible-checkout-fields-pro-woocommerce/
    Description: Manage your WooCommerce checkout fields. Change order, labels, placeholders and add new fields.
    Version: 1.9.0
    Author: WP Desk
    Author URI: https://www.wpdesk.net/
    Text Domain: flexible-checkout-fields
    Domain Path: /lang/
	Requires at least: 4.6
    Tested up to: 5.1.0
    WC requires at least: 3.1.0
    WC tested up to: 3.5.5

    Copyright 2017 WP Desk Ltd.

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	$plugin_version = '1.9.0';
	define( 'FLEXIBLE_CHECKOUT_FIELDS_VERSION', $plugin_version );


	if ( ! defined( 'FCF_VERSION' ) ) {
		define( 'FCF_VERSION', FLEXIBLE_CHECKOUT_FIELDS_VERSION );
	}


	$flexible_checkout_fields_plugin_data = array();

	if ( !function_exists( 'wpdesk_is_plugin_active' ) ) {
		function wpdesk_is_plugin_active( $plugin_file ) {

			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}

			return in_array( $plugin_file, $active_plugins ) || array_key_exists( $plugin_file, $active_plugins );
		}
	}


	require_once( 'classes/wpdesk/class-plugin.php' );

	require_once('inc/wpdesk-woo27-functions.php');

	require_once( 'classes/tracker.php' );

	require_once( __DIR__ . '/vendor/autoload.php' );

    class Flexible_Checkout_Fields_Plugin extends WPDesk_Plugin_1_8 {

        protected $script_version = '1.9.0';

        protected $fields = array();

        public $sections = array();

        public $all_sections = array();

        public $page_size = array();

        public $field_validation;

	    public function __construct( $base_file, $plugin_data ) {

	        $this->plugin_namespace = 'inspire_checkout_fields';
	        $this->plugin_text_domain = 'flexible-checkout-fields';

	        $this->plugin_has_settings = false;

	        parent::__construct( $base_file, $plugin_data );

		    $this->load_dependencies();
	        $this->init();
	        $this->hooks();

	        require_once( 'classes/settings.php' );
            $this->settings = new Flexible_Checkout_Fields_Settings( $this );

		    add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 100 );

            add_action( 'woocommerce_checkout_fields', array( $this, 'changeCheckoutFields' ), 9999 );
            add_action( 'woocommerce_checkout_update_order_meta', array($this, 'updateCheckoutFields'), 9 );

            // commented: https://trello.com/c/RV48nsMG/1919-flexible-checkout-fields-w%C5%82%C4%85czenie-fcf-oraz-zmiana-kolejno%C5%9Bci-powoduje-znikanie-p%C3%B3l-w-edycji-zam%C3%B3wienia-w-adminie
            //add_action( 'woocommerce_admin_billing_fields', array($this, 'changeAdminBillingFields'), 9999 );
            //add_action( 'woocommerce_admin_shipping_fields', array($this, 'changeAdminShippingFields'), 9999 );
            //add_action( 'woocommerce_admin_order_fields', array($this, 'changeAdminOrderFields'), 9999 );

            add_action( 'woocommerce_admin_order_data_after_billing_address', array($this, 'addCustomBillingFieldsToAdmin') );
            add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this, 'addCustomShippingFieldsToAdmin') );
            add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this, 'addCustomOrderFieldsToAdmin') );

            add_action( 'woocommerce_billing_fields', array($this, 'addCustomFieldsBillingFields'), 9999 );
            add_action( 'woocommerce_shipping_fields', array($this, 'addCustomFieldsShippingFields'), 9999 );
            add_action( 'woocommerce_order_fields', array($this, 'addCustomFieldsOrderFields'), 9999 );


            add_action( 'woocommerce_before_checkout_form', array( $this, 'woocommerce_before_checkout_form' ), 10 );
            add_action( 'woocommerce_before_edit_address_form_shipping', array( $this, 'woocommerce_before_checkout_form' ), 10 );
            add_action( 'woocommerce_before_edit_address_form_billing', array( $this, 'woocommerce_before_checkout_form' ), 10 );

            add_filter( 'flexible_chekout_fields_fields', array( $this, 'getCheckoutFields'), 10, 2 );

            add_filter( 'flexible_checkout_fields_field_tabs', array( $this, 'flexible_checkout_fields_field_tabs' ), 10 );

            add_action( 'flexible_checkout_fields_field_tabs_content', array( $this, 'flexible_checkout_fields_field_tabs_content'), 10, 4 );

	        add_action( 'woocommerce_default_address_fields', array( $this, 'woocommerce_default_address_fields' ), 9999 );
	        add_filter( 'woocommerce_get_country_locale', array( $this, 'woocommerce_get_country_locale' ), 9999 );
	        add_filter( 'woocommerce_get_country_locale_base', array( $this, 'woocommerce_get_country_locale_base' ), 9999 );

            add_action( 'woocommerce_get_country_locale_default', array( $this, 'woocommerce_get_country_locale_default' ), 11 );

		    include( 'classes/field.php' );

            include( 'classes/display-options.php' );
            new Flexible_Checkout_Fields_Disaplay_Options( $this );

	        include( 'classes/user-profile.php' );
	        $user_profile = new Flexible_Checkout_Fields_User_Profile( $this );
		    $user_profile->hooks();

		    include( 'classes/filed-validation.php' );
		    $this->field_validation = new Flexible_Checkout_Fields_Field_Validation( $this );
		    $this->field_validation->hooks();

		    include 'classes/myaccount-filed-processor.php';
		    $my_account_fields_processor = new Flexible_Checkout_Fields_Myaccount_Field_Processor( $this );
		    $my_account_fields_processor->hooks();

		    include 'classes/myaccount-edit-address.php';
		    $my_account_edit_address = new Flexible_Checkout_Fields_Myaccount_Edit_Address( $this );
		    $my_account_edit_address->hooks();
        }

	    /**
	     * Load dependencies.
	     */
        private function load_dependencies() {
	    	require_once 'classes/field-options.php';
        }

	    public function init() {
	    }

	    public function hooks() {
		    parent::hooks();

	    }

	    public function load_plugin_text_domain() {
		    $wpdesk_translation = load_plugin_textdomain( 'wpdesk-plugin', FALSE, $this->get_text_domain() . '/classes/wpdesk/lang/' );
		    $plugin_translation = load_plugin_textdomain( $this->get_text_domain(), FALSE, $this->get_text_domain() . '/lang/' );
	    }

	    public function plugins_loaded() {
		    $this->init_fields();
			//do użycia dla pola miasto, kod pocztowy i stan
		    $this->init_sections();
	    }

	    public function getSettingValue( $name, $default = null ) {
		    return get_option( $this->get_namespace() . '_' . $name, $default );
	    }

        public function woocommerce_get_country_locale_base( $base ) {
			foreach ( $base as $key => $field ) {
				unset( $base[$key]['placeholder']);
				unset( $base[$key]['label']);
			}
        	return $base;
        }

        public function woocommerce_get_country_locale( $locale ) {
        	if ( is_checkout() || is_account_page() ) {
		        foreach ( $locale as $country => $fields ) {
			        foreach ( $fields as $field => $settings ) {
				        unset( $locale[ $country ][ $field ]['priority'] );
				        unset( $locale[ $country ][ $field ]['label'] );
				        unset( $locale[ $country ][ $field ]['placeholder'] );
			        }
		        }
	        }
        	return $locale;
        }

        public function woocommerce_default_address_fields( $fields ) {
        	if ( is_checkout() ) {
        		foreach ( $fields as $key => $field ) {
        			unset( $fields[$key]['priority'] );
		        }
	        }
	        return $fields;
        }

		/**
		 * Init sections.
		 */
		public function init_sections() {
			$sections = array(
				'billing'  => array(
					'section'        => 'billing',
					'tab'            => 'fields_billing',
					'tab_title'      => __( 'Billing', 'flexible-checkout-fields' ),
					'custom_section' => false
				),
				'shipping' => array(
					'section'        => 'shipping',
					'tab'            => 'fields_shipping',
					'tab_title'      => __( 'Shipping', 'flexible-checkout-fields' ),
					'custom_section' => false
				),
				'order'    => array(
					'section'        => 'order',
					'tab'            => 'fields_order',
					'tab_title'      => __( 'Order', 'flexible-checkout-fields' ),
					'custom_section' => false
				)
			);

			$all_sections = unserialize( serialize( $sections ) );

			$this->sections = apply_filters( 'flexible_checkout_fields_sections', $sections );

			$this->all_sections = apply_filters( 'flexible_checkout_fields_all_sections', $all_sections );
		}

        function init_fields() {
        	$this->fields['text'] = array(
        			'name'	=> __( 'Single Line Text', 'flexible-checkout-fields' )
        	);

        	$this->fields['textarea'] = array(
        			'name'	=> __( 'Paragraph Text', 'flexible-checkout-fields' )
        	);
        }

        function pro_fields( $fields ) {
            $add_fields = array();

            $add_fields['inspirecheckbox'] = array(
                'name' 				=> __( 'Checkbox', 'flexible-checkout-fields' ),
                'pro'               => true
            );

	        $add_fields['checkbox'] = array(
		        'name' 				=> __( 'Checkbox', 'flexible-checkout-fields' ),
		        'pro'               => true
	        );

            $add_fields['inspireradio'] = array(
                'name' 					=> __( 'Radio button', 'flexible-checkout-fields' ),
                'pro'                   => true
            );

            $add_fields['select'] = array(
                'name' 					=> __( 'Select (Drop Down)', 'flexible-checkout-fields' ),
                'pro'                   => true
            );

	        $add_fields['wpdeskmultiselect'] = array(
		        'name' 					=> __( 'Multi-select', 'flexible-checkout-fields' ),
		        'pro'                   => true
	        );

            $add_fields['datepicker'] = array(
                'name' 					=> __( 'Date', 'flexible-checkout-fields' ),
                'pro'                   => true
            );

            $add_fields['timepicker'] = array(
                'name' 					=> __( 'Time', 'flexible-checkout-fields'),
                'pro'                   => true
            );

            $add_fields['colorpicker'] = array(
                'name' 					=> __( 'Color Picker', 'flexible-checkout-fields' ),
                'pro'                   => true
            );

            $add_fields['heading'] = array(
                'name' 					=> __( 'Headline', 'flexible-checkout-fields' ),
                'pro'                   => true
            );

            $add_fields['info'] = array(
                'name' 					=> __( 'HTML', 'flexible-checkout-fields' ),
                'pro'                   => true
            );

            $add_fields['file'] = array(
                'name' 					=> __( 'File Upload', 'flexible-checkout-fields' ),
                'pro'                   => true
            );

            foreach ( $add_fields as $key => $field ) {
                $fields[$key] = $field;
            }

            return $fields;

        }

        public function get_fields() {
            $this->fields = $this->pro_fields( $this->fields );
        	return apply_filters( 'flexible_checkout_fields_fields' , $this->fields );
        }


		/**
		 * Remove unavailable sections from settings.
		 * Removes sections added by PRO plugin, after PRO plugin disable.
		 *
		 * @param array $settings Settings.
		 * @return array
		 */
		private function get_settings_for_available_sections( array $settings ) {
			$this->init_sections();
			if ( is_array( $settings ) && is_array( $this->sections ) ) {
				foreach ( $settings as $section => $section_settings ) {
					$unset = true;
					foreach ( $this->sections as $section_data ) {
						if ( $section_data['section'] === $section ) {
							$unset = false;
						}
					}
					if ( $unset ) {
						unset( $settings[ $section ] );
					}
				}
			}

			return $settings;
		}

		/**
		 * Get settings.
		 *
		 * @return array
		 */
		public function get_settings() {
			$settings = get_option( 'inspire_checkout_fields_settings', array() );
			if ( ! is_array( $settings ) ) {
				$settings = array();
			}

			return $this->get_settings_for_available_sections( $settings );
		}

        function woocommerce_before_checkout_form() {
        	WC()->session->set( 'checkout-fields', array() );
            $settings = $this->get_settings();
            $args = array( 'settings' => $settings );
            include( 'views/before-checkout-form.php' );
        }

        /**
         * Enqueue admin scripts and styles.
         *
         * @param string $hooq Current admin page name.
         */
        public function admin_enqueue_scripts( $hooq ) {
            if ( isset( $hooq ) && 'woocommerce_page_inspire_checkout_fields_settings' === $hooq ) {
                wp_enqueue_style('jquery-ui-style',
                    '//ajax.googleapis.com/ajax/libs/jqueryui/' . '1.9.2' . '/themes/smoothness/jquery-ui.css');
            }
            wp_enqueue_style( 'inspire_checkout_fields_admin_style', trailingslashit( $this->get_plugin_assets_url() ) . 'css/admin.css', array(), $this->script_version );

	        wp_enqueue_script( 'jquery' );
	        wp_enqueue_script( 'jquery-ui' );
	        wp_enqueue_script( 'jquery-ui-sortable' );
	        wp_enqueue_script( 'jquery-ui-tooltip' );
	        wp_enqueue_script( 'inspire_checkout_fields_admin_js', trailingslashit( $this->get_plugin_assets_url() ) . 'js/admin.js', array(), $this->script_version );
	        wp_enqueue_script( 'jquery-ui-datepicker' );

	        $labels_and_packing_list_params = array(
		        'plugin_url' => $this->get_plugin_assets_url()
	        );
        }

        public function wp_enqueue_scripts() {
        	if ( !defined( 'WC_VERSION' ) ) {
        		return;
	        }
            if ( is_checkout() || is_account_page()){
                if( $this->getSettingValue('css_disable') != 1 ){
                    wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . '1.9.2' . '/themes/smoothness/jquery-ui.css' );
                }

                wp_enqueue_style( 'inspire_checkout_fields_public_style', trailingslashit( $this->get_plugin_assets_url() ) . 'css/front.css', array(), $this->script_version );
            }
	        if ( is_checkout() || is_account_page() ) {
		        wp_enqueue_script( 'jquery' );
		        wp_enqueue_script( 'jquery-ui' );
		        wp_enqueue_script( 'jquery-ui-datepicker' );
		        add_action( 'wp_enqueue_scripts', array( $this, 'wp_localize_jquery_ui_datepicker' ), 1000 );

		        wp_register_script( 'inspire_checkout_fields_checkout_js', trailingslashit( $this->get_plugin_assets_url() ) . 'js/checkout.js', array(), $this->script_version );
		        $translation_array = array(
			        'uploading' => __( 'Uploading file...', 'flexible-checkout-fields' ),
		        );
		        wp_localize_script( 'inspire_checkout_fields_checkout_js', 'words', $translation_array );
		        wp_enqueue_script( 'inspire_checkout_fields_checkout_js' );
	        }
        }


        function wp_localize_jquery_ui_datepicker() {
        	global $wp_locale;
        	global $wp_version;

        	if ( ! wp_script_is( 'jquery-ui-datepicker', 'enqueued' ) || version_compare( $wp_version, '4.6' ) != -1 ) {
        		return;
        	}

        	// Convert the PHP date format into jQuery UI's format.
        	$datepicker_date_format = str_replace(
        			array(
        					'd', 'j', 'l', 'z', // Day.
        					'F', 'M', 'n', 'm', // Month.
        					'Y', 'y'            // Year.
        			),
        			array(
        					'dd', 'd', 'DD', 'o',
        					'MM', 'M', 'm', 'mm',
        					'yy', 'y'
        			),
        			get_option( 'date_format' )
        			);

        	$datepicker_defaults = wp_json_encode( array(
        			'closeText'       => __( 'Close' ),
        			'currentText'     => __( 'Today' ),
        			'monthNames'      => array_values( $wp_locale->month ),
        			'monthNamesShort' => array_values( $wp_locale->month_abbrev ),
        			'nextText'        => __( 'Next' ),
        			'prevText'        => __( 'Previous' ),
        			'dayNames'        => array_values( $wp_locale->weekday ),
        			'dayNamesShort'   => array_values( $wp_locale->weekday_abbrev ),
        			'dayNamesMin'     => array_values( $wp_locale->weekday_initial ),
        			'dateFormat'      => $datepicker_date_format,
        			'firstDay'        => absint( get_option( 'start_of_week' ) ),
        			'isRTL'           => $wp_locale->is_rtl(),
        	) );

        	wp_add_inline_script( 'jquery-ui-datepicker', "jQuery(document).ready(function(jQuery){jQuery.datepicker.setDefaults({$datepicker_defaults});});" );
        }

	    /**
	     * @param array $settings
	     * @param array $fields
	     * @param array $new
	     * @param null|string $request_type
	     *
	     * @return array
	     */
        private function append_other_plugins_fields_to_checkout_fields( $settings, $fields, $new, $request_type ) {
	        if ( $request_type == null ) {
		        if ( ! empty( $fields ) && is_array( $fields ) ) {
			        foreach ( $fields as $section => $section_fields ) {
				        if ( ! empty( $section_fields ) && is_array( $section_fields ) ) {
					        foreach ( $section_fields as $key => $field ) {
						        if ( empty( $settings[ $section ][ $key ] ) ) {
							        $new[ $section ][ $key ] = $field;
						        }
					        }
				        }
			        }
		        }
	        }
	        else {
	        	foreach ( $fields as $key => $field ) {
			        if ( empty( $settings[ $request_type ][ $key ] ) ) {
				        $new[ $request_type ][ $key ] = $field;
			        }
		        }
	        }
	        return $new;
        }

	    /**
	     * @param array $fields
	     * @param null|string $request_type
	     *
	     * @return array
	     */
        public function getCheckoutFields( $fields, $request_type = null ) {
            $settings = $this->get_settings();

            $checkout_field_type = $this->get_fields();
			if ( !empty( $settings ) ) {
				$new = array();
				$priority = 0;
                foreach ( $settings as $key => $type ) {
                	if ( $key != 'billing' && $key != 'shipping' && $key != 'order' ) {
                		if ( get_option('inspire_checkout_fields_' . $key, '0' ) == '0' ) {
                			continue;
                		}
                	}
                	if ( !is_array( $type ) ) {
                		continue;
                	}
                    if ( $request_type == null || $request_type == $key ) {
                    	if ( !isset( $new[$key] ) ) {
                    		$new[$key] = array();
                    	}
                    	$fields_found = true;
                    	foreach ( $type as $field_name => $field ) {
                    		if ( apply_filters( 'flexible_checkout_fields_condition', true, $field ) ) {
	                            if ( $field['visible'] == 0 or
	                            	( ( isset( $_GET['page'] ) && $_GET['page'] == 'inspire_checkout_fields_settings' ) && $field['visible'] == 1) || $field['name'] == 'billing_country' || $field['name'] == 'shipping_country')
	                            {
		                            $custom_field = false;
		                            if( isset( $field['custom_field'] ) && $field['custom_field'] == 1 ) {
		                                $custom_field = true;
		                            }
	                            	if ( isset( $fields[$key][$field['name']] ) ) {
	                            		$new[$key][$field['name']] = $fields[$key][$field['name']];
	                            	}
	                            	else {
	                            		$new[$key][$field['name']] = $type[$field['name']];
	                            	}
	                                if( $field['required'] == 1 ){
	                                    $new[$key][$field['name']]['required'] = true;
	                                }
	                                else{
	                                    $new[$key][$field['name']]['required'] = false;
		                                if ( isset( $new[$key][$field['name']]['validate'] ) ) {
			                                unset( $new[$key][$field['name']]['validate'] );
		                                }
	                                }
	                                if ( isset( $field['label'] ) ) {
		                                $new[ $key ][ $field['name'] ]['label'] = stripcslashes( wpdesk__( $field['label'], 'flexible-checkout-fields' ) );
	                                }
	                                if ( isset( $field['placeholder'] ) ) {
	                                    $new[$key][$field['name']]['placeholder'] = wpdesk__( $field['placeholder'], 'flexible-checkout-fields' );
	                                }
	                                else {
	                                	$new[$key][$field['name']]['placeholder'] = '';
	                                }
	                                if( is_array($field['class'])){
	                                    $new[$key][$field['name']]['class'] = $field['class'];
	                                }
	                                else {
	                                    $new[$key][$field['name']]['class'] = explode( ' ', $field['class'] );
	                                }
	                                if ( ($field['name'] == 'billing_country' || $field['name'] == 'shipping_country') && $field['visible'] == 1 ){
	                                    $new[$key][$field['name']]['class'][1] = "inspire_checkout_fields_hide";
	                                }
	                                if ( ! $custom_field ) {
	                            		if ( isset( $field['validation'] ) && $field['validation'] != '' ) {
	                            			if ( $field['validation'] == 'none' ) {
	                            				unset( $new[$key][$field['name']]['validate'] );
				                            }
				                            else {
					                            $new[$key][$field['name']]['validate'] = array( $field['validation'] );
				                            }
			                            }
	                                }
	                                else {
		                                if ( isset( $field['validation'] ) && $field['validation'] != 'none' ) {
			                                $new[$key][$field['name']]['validate'] = array( $field['validation'] );
		                                }
	                                }

		                            if( !empty( $field['type'] ) ){
			                            $new[$key][$field['name']]['type'] = $field['type'];
		                            }

	                                if ( $custom_field ) {
	                                    $new[$key][$field['name']]['type'] = $field['type'];

	                                    if ( isset( $checkout_field_type[$field['type']]['has_options'] ) ) {
	                                    	$field_options = new Flexible_Checkout_Fields_Field_Options( $field['option'] );
		                                    $new[ $key ][ $field['name'] ]['options'] = $field_options->get_options_as_array();
	                                    }
	                                }

	                                $custom_attributes = array();
		                            if ( isset( $new[$key][$field['name']]['custom_attributes'] ) ) {
		                            	$custom_attributes = $new[$key][$field['name']]['custom_attributes'];
		                            }
		                            if ( isset( $field['label'] ) ) {
			                            $custom_attributes['data-qa-id'] = $field['label'];
		                            }

	                                $new[$key][$field['name']]['custom_attributes'] = apply_filters( 'flexible_checkout_fields_custom_attributes', $custom_attributes, $field );
	                            }
                    		}
                        }
                    }
                }

                $new = $this->append_other_plugins_fields_to_checkout_fields( $settings, $fields, $new, $request_type );

                foreach ( $new as $type => $fields ) {
	                $priority = 0;
                	foreach ( $fields as $key => $field ) {
		                $priority = $priority + 10;
		                $new[$type][$key]['priority'] = $priority;
	                }
                }

                if ( $request_type == null ) {
                    if ( !empty($fields['account'] ) ) {
                        $new['account'] = $fields['account'];
                    }

	                $new = $this->restore_default_city_validation( $new, $_POST, 'billing' );
	                $new = $this->restore_default_city_validation( $new, $_POST, 'shipping' );

                    return $new;
                }
                else{
                	if ( isset( $new[$request_type] ) ) {
		                $new = $this->restore_default_city_validation( $new, $_POST, $request_type );
		                return $new[ $request_type ];
	                }
	                else {
                		return array();
	                }
                }
            }
            else {
                return $fields;
            }
        }

	    /**
	     * Restores the default validation for the city
	     *
	     * @param array $fields
	     * @param array $request
	     * @param string $request_type the type of shipping address (billing or shipping)
	     *
	     * @return array
	     */
	    private function restore_default_city_validation( array $fields, array $request, $request_type ) {

		    $city    = $request_type . '_city';
		    $country = $request_type . '_country';

		    if( isset( $fields[ $request_type ][ $city ]['required'] ) && isset( $request[ $country ] ) ) {
			    $slug       = $request[ $country ];
			    $countries  = new WC_Countries();
			    $locales    = $countries->get_country_locale();
			    if ( isset( $locales[ $slug ]['city']['required'] ) ) {
				    $required = $locales[ $slug ]['city']['required'];
				    if( !$required ) {
					    $fields[ $request_type ][ $city ]['required']    = 0;
					    $fields[ $request_type ][ $city ]['hidden']      = 1;
				    }
			    }
		    }
		    return $fields;
	    }

        public function getCheckoutUserFields($fields, $request_type = null) {
            $settings = $this->get_settings();

            $checkout_field_type = $this->get_fields();

            $priority = 0;

            if ( !empty($settings[$request_type] ) ) {
                foreach ( $settings[$request_type] as $key => $field ) {

                    if($field['visible'] == 0 || $field['name'] == 'billing_country' || $field['name'] == 'shipping_country' || ( isset($_GET['page']) && $_GET['page'] == 'inspire_checkout_fields_settings' && $field['visible'] == 1)){
                        if(!empty($fields[$key])){
                            $new[$key] = $fields[$key];
                        }

                        if($field['required'] == 1){
                            $new[$key]['required'] = true;
                        }
                        else{
                            $new[$key]['required'] = false;
                        }

	                    if ( isset( $field['label'] ) ) {
		                    $new[ $key ]['label'] = wpdesk__( $field['label'], 'flexible-checkout-fields' );
	                    }

                        if ( isset( $field['placeholder'] ) ) {
                            $new[$key]['placeholder'] = wpdesk__( $field['placeholder'], 'flexible-checkout-fields' );
                        }
                        else {
                        	$new[$key]['placeholder'] = '';
                        }

                        if(is_array($field['class'])){
                            $new[$key]['class'][0] = implode(' ', $field['class']);
                        }
                        else {
                            $new[$key]['class'][0] = $field['class'];
                        }

	                    if ( !empty( $field['name'] ) ) {
		                    if ( ( $field['name'] == 'billing_country' || $field['name'] == 'shipping_country' ) && $field['visible'] == 1 ) {
			                    $new[ $key ]['class'][1] = "inspire_checkout_fields_hide";
		                    }
	                    }

                        if(!empty($field['type'])){
                            $new[$key]['type'] = $field['type'];
                        }

                        if( isset( $field['type'] ) && ( !empty( $checkout_field_type[$field['type']]['has_options'] ) ) ) {
	                        $field_options = new Flexible_Checkout_Fields_Field_Options( $field['option'] );
	                        $new[ $key ]['options'] = $field_options->get_options_as_array();
                        }
                    }
                }

                /* added 02-02-2018 */
                foreach ( $fields as $field_key => $field ) {
                	if ( empty( $new[$field_key] ) ) {
		                $new[$field_key] = $field;
	                }
                }

                if ( count( $fields ) ) {
	                foreach ( $new as $key => $field ) {
		                if ( empty( $fields[$key] ) ) {
			                $new[$key]['custom_field'] = 1;
		                }
	                }
                }

                foreach ( $new as $key => $field ) {
	                $priority += 10;
	                $new[$key]['priority'] = $priority;
                }
                return $new;
            }
            else {
                return $fields;
            }
        }

        public function printCheckoutFields( $order, $request_type = null ) {

        	$settings = $this->getCheckoutFields( $this->get_settings() );

            $checkout_field_type = $this->get_fields();

            if( !empty( $settings ) ){
                foreach ($settings as $key => $type) {
                    if ( $request_type == null || $request_type == $key ) {
                        foreach ($type as $field) {
                            if ( 1 == 1
                                /* $field['visible'] == 0 */ /* probably temporary change ;) */ // TODO - remove in next version?
                            	&& ( ( isset( $field['custom_field'] ) && $field['custom_field'] == 1 ) || in_array( $field['name'], array('billing_phone', 'billing_email' ) ) )
                            	&& ( empty( $field['type'] ) || ( !empty( $checkout_field_type[$field['type']] ) && empty( $checkout_field_type[$field['type']]['exclude_in_admin'] ) ) )
                            	) {
                                if ( $value = wpdesk_get_order_meta( $order, '_'.$field['name'] , true ) ) {
                                    if ( isset( $field['type'] ) ) {
                                    	$value = apply_filters( 'flexible_checkout_fields_print_value', $value, $field );
                                        $return[] = '<b>'.stripslashes( wpdesk__( $field['label'], 'flexible-checkout-fields' ) ).'</b>: '.$value;
                                    }
                                	else{
                                        $return[] = '<b>'.stripslashes( wpdesk__( $field['label'], 'flexible-checkout-fields' ) ).'</b>: '.$value;
                                    }
                                }
                            }
                        }
                    }
                }

                if( !empty( $return ) ) {
                    echo '<div class="address_flexible_checkout_fields"><p class="form-field form-field-wide">' . implode( '<br />', $return ) . '</p></div>';
                }
            }
        }

        public function changeAdminLabelsCheckoutFields( $labels, $request_type ) {
            $settings = $this->get_settings();
            if( !empty( $settings ) && ( $request_type == null || !empty( $settings[$request_type] ) ) ) {
            	$new = array();
                foreach ($settings as $key => $type) {
                    if ( $request_type == null || $request_type == $key ) {
                        foreach ($type as $field) {
                            if ( $field['visible'] == 0 && ($request_type == null || strpos($field['name'], $request_type) === 0 )
                            	&& ( ( empty( $field['type'] ) || ( $field['type'] != 'heading' && $field['type'] != 'info' && $field['type'] != 'file' ) ) )
                            	) {
	                            $field_name = str_replace($request_type.'_', '', $field['name']);

	                            if ( isset( $labels[$field_name] ) ) {

	                                $new[$field_name] = $labels[$field_name];

	                                if(!empty($field['label'])){
	                                    $new[$field_name]['label'] = stripslashes($field['label']);

	                                }

	                                if(empty($new[$field_name]['label'])){
	                                    $new[$field_name]['label'] = $field['name'];
	                                }

	                                $new[$field_name]['type'] = 'text';
	                                if ( isset( $field['type'] ) ) {
	                                	$new[$field_name]['type'] = $field['type'];
	                            	}

	                            	$new[$field_name] = apply_filters( 'flexible_checkout_fields_admin_labels', $new[$field_name], $field, $field_name );

	                                if($field_name == 'country'){
	                                    $new[$field_name]['type'] = 'select';
	                                }

	                                if ( isset( $field['show'] ) ) {
		                                $new[ $field_name ]['show'] = $field['show'];
	                                }

		                            //$new[ $field_name ]['wrapper_class'] = 'form-field-wide';

                            	}
                            }
                        }
                    }
                }

                foreach ( $labels as $key=>$value ) {
                	if ( $request_type == null || $request_type == $key ) {
                		if ( empty( $new[$key] ) ) {
                			$new[$key] = $value;
                		}
                	}
                }

                return $new;
            }
            else{
                return $labels;
            }

        }


        public function changeCheckoutFields( $fields ) {
	        return $this->getCheckoutFields($fields);
        }

        public function changeShippingFields($fields) {
            return $this->getCheckoutFields($fields, 'shipping');
        }

        public function changeBillingFields($fields) {
            return $this->getCheckoutFields($fields, 'billing');
        }

        public function changeOrderFields($fields) {
            return $this->getCheckoutFields($fields, 'order');
        }

        public function changeAdminBillingFields($labels) {
            return $this->changeAdminLabelsCheckoutFields($labels, 'billing');
        }

        public function changeAdminShippingFields($labels) {
            return $this->changeAdminLabelsCheckoutFields($labels, 'shipping');
        }

        public function changeAdminOrderFields($labels) {
            return $this->changeAdminLabelsCheckoutFields($labels, 'order');
        }

        public function addCustomBillingFieldsToAdmin($order){
            $this->printCheckoutFields( $order, 'billing' );
        }

        public function addCustomShippingFieldsToAdmin($order){
            $this->printCheckoutFields( $order, 'shipping' );
        }

        public function addCustomOrderFieldsToAdmin($order){
            $this->printCheckoutFields( $order, 'order' );
        }

        public function addCustomFieldsBillingFields($fields) {
	        return $this->getCheckoutUserFields( $fields, 'billing');
        }

        public function addCustomFieldsShippingFields($fields) {
	        return $this->getCheckoutUserFields( $fields, 'shipping');
        }

        public function addCustomFieldsOrderFields($fields) {
            return $this->getCheckoutUserFields($fields, 'order');
        }

        function updateCheckoutFields( $order_id ) {
            $shippingNotOverwrite = array(
                'shipping_address_1',
                'shipping_address_2',
                'shipping_address_2',
                'shipping_city',
                'shipping_company',
                'shipping_country',
                'shipping_first_name',
                'shipping_last_name',
                'shipping_postcode',
                'shipping_state',
            );

            $settings = $this->get_settings();
            if ( !empty( $settings ) ) {
                $keys = array_flip(
                				array_merge(
                						array_keys(	isset( $settings['billing'] ) ? $settings['billing'] : array() ),
                						array_keys( isset( $settings['shipping'] ) ? $settings['shipping'] : array() ),
                						array_keys( isset( $settings['order'] ) ? $settings['order'] : array() )
                				)
                		);


                foreach ($_POST as $key => $value) {

                    $save = true;
                    if (empty($_POST['ship_to_different_address']))
                    {
                        $save = !in_array( $key, $shippingNotOverwrite );
                    }
                    if ($save)
                    {
                        if(array_key_exists($key, $keys)){
	                        update_post_meta( $order_id, '_' . $key, $value );
                        }
                    }
                }
            }

            do_action( 'flexible_checkout_fields_checkout_update_order_meta', $order_id );

        }

        /**
         * action_links function.
         *
         * @access public
         * @param mixed $links
         * @return void
         */
         public function links_filter( $links ) {
	         $docs_link = 'https://www.wpdesk.net/docs/flexible-checkout-fields-pro-woocommerce-docs/';
	         if ( get_locale() === 'pl_PL' ) {
	             $docs_link = 'https://www.wpdesk.pl/docs/woocommerce-checkout-fields-docs/';
	         }
	         $docs_link .= '?utm_source=wp-admin-plugins&utm_medium=quick-link&utm_campaign=flexible-checkout-fields-docs-link';

	         $plugin_links = array();
	         if ( defined( 'WC_VERSION' ) ) {
		         $plugin_links[] = '<a href="' . admin_url( 'admin.php?page=inspire_checkout_fields_settings') . '">' . __( 'Settings', 'flexible-checkout-fields' ) . '</a>';
	         }
	         $plugin_links[] = '<a href="' . $docs_link . '">' . __( 'Docs', 'flexible-checkout-fields' ) . '</a>';
	         $plugin_links[] = '<a href="https://wordpress.org/support/plugin/flexible-checkout-fields/">' . __( 'Support', 'flexible-checkout-fields' ) . '</a>';

	         $pro_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/sklep/woocommerce-checkout-fields/' : 'https://www.wpdesk.net/products/flexible-checkout-fields-pro-woocommerce/';
	         $utm = '?utm_source=wp-admin-plugins&utm_medium=link&utm_campaign=flexible-checkout-fields-plugins-upgrade-link';

	         if ( ! wpdesk_is_plugin_active( 'flexible-checkout-fields-pro/flexible-checkout-fields-pro.php' ) )
	             $plugin_links[] = '<a href="' . $pro_link . $utm . '" target="_blank" style="color:#d64e07;font-weight:bold;">' . __( 'Upgrade', 'flexible-checkout-fields' ) . '</a>';

	         return array_merge( $plugin_links, $links );
         }

         public static function flexible_checkout_fields_section_settings( $key, $settings ) {
         	echo 1;
         }

         public function flexible_checkout_fields_field_tabs( $tabs ) {
         	$tabs[] = array(
         			'hash' => 'advanced',
         			'title' => __( 'Advanced', 'flexible-checkout-fields' )
         	);
         	return $tabs;
         }

    	public function flexible_checkout_fields_field_tabs_content( $key, $name, $field, $settings ) {
    		include( 'views/settings-field-advanced.php' );
    	}


    	public function woocommerce_get_country_locale_default( $address_fields ) {
         	return $address_fields;
	    }

    }

	if ( !function_exists( 'is_flexible_checkout_fields_pro_active' ) ) {
		/**
		 * Get PRO plugin.
		 *
		 * @return Flexible_Checkout_Fields_Pro_Plugin|null
		 */
		function get_flexible_checkout_fields_pro_plugin() {
			if ( class_exists( '\WPDesk\PluginBuilder\Storage\StaticStorage' ) ) {
				$storage = new \WPDesk\PluginBuilder\Storage\StaticStorage();
				try {
					return $storage->get_from_storage( Flexible_Checkout_Fields_Pro_Plugin::class );
				} catch ( \WPDesk\PluginBuilder\Storage\Exception\ClassNotExists $e ) {
					return null;
				}
			}
			return null;
		}
	}


	if ( !function_exists( 'is_flexible_checkout_fields_pro_active' ) ) {
		/**
		 * Checks if Flexible Checkout Fields PRO is active
		 *
		 * @return bool
		 */
		function is_flexible_checkout_fields_pro_active() {
			$pro_plugin = get_flexible_checkout_fields_pro_plugin();
			if ( !empty( $pro_plugin ) && method_exists( $pro_plugin, 'get_plugin_is_active' ) ) {
				return $pro_plugin->get_plugin_is_active();
			} else {
				return wpdesk_is_plugin_active( 'flexible-checkout-fields-pro/flexible-checkout-fields-pro.php' );
			}
		}
	}

	if ( !function_exists( 'wpdesk__' ) ) {
		function wpdesk__( $text, $domain ) {
			if ( function_exists( 'icl_sw_filters_gettext' ) ) {
				return icl_sw_filters_gettext( $text, $text, $domain, $text );
			}
			if ( function_exists( 'pll__' ) ) {
				return pll__( $text );
			}
			return __( $text, $domain );
		}
	}

	if ( !function_exists( 'wpdesk__e' ) ) {
		function wpdesk__e( $text, $domain ) {
			echo wpdesk__( $text, $domain );
		}
	}

	add_action( 'plugins_loaded', 'flexible_chekout_fields_plugins_loaded', 9 );
	function flexible_chekout_fields_plugins_loaded() {
		if ( ! function_exists( 'should_enable_wpdesk_tracker' ) ) {
			function should_enable_wpdesk_tracker() {
				$tracker_enabled = true;
				if ( ! empty( $_SERVER['SERVER_ADDR'] ) && $_SERVER['SERVER_ADDR'] === '127.0.0.1' ) {
					$tracker_enabled = false;
				}

				return apply_filters( 'wpdesk_tracker_enabled', $tracker_enabled );
			}
		}

		$tracker_factory = new WPDesk_Tracker_Factory();
		$tracker_factory->create_tracker( basename( dirname( __FILE__ ) ) );
	}

	function flexible_checkout_fields() {
		global $flexible_checkout_fields;
		global $flexible_checkout_fields_plugin_data;
		if ( !isset( $flexible_checkout_fields ) ) {
			$flexible_checkout_fields = new Flexible_Checkout_Fields_Plugin( __FILE__, $flexible_checkout_fields_plugin_data );
		}
		return $flexible_checkout_fields;
	}

    $_GLOBALS['inspire_checkout_fields'] = flexible_checkout_fields();
