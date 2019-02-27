<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class Flexible_Checkout_Fields_Settings {


    	private $admin_notices = array();

	    /**
	     * Flexible_Checkout_Fields_Settings constructor.
	     *
	     * @param Flexible_Checkout_Fields_Plugin $plugin
	     */
        public function __construct( $plugin ) {

            $this->plugin = $plugin;

            add_action( 'admin_init', array($this, 'updateSettingsAction') );                        
            add_action( 'admin_menu', array($this, 'initAdminMenuAction'), 80);
            
            add_action( 'init', array($this, 'init_polylang') );
            add_action( 'admin_init', array($this, 'init_wpml') );
        }

	    public function getSettingValue( $name, $default = null ) {
        	return $this->plugin->getSettingValue( $name, $default );
	    }
        
        function init_polylang() {
        	if ( function_exists( 'pll_register_string' ) ) {
        		$settings = get_option('inspire_checkout_fields_settings', array() );
        		$checkout_field_type = $this->plugin->get_fields();
        		foreach ( $settings as $section ) {
        			if ( is_array( $section ) ) {
        				foreach ( $section as $field ) {
        					if ( isset( $field['label'] ) && $field['label'] != '' ) {
        						pll_register_string( $field['label'], $field['label'], __('Flexible Checkout Fields', 'flexible-checkout-fields' ) );
        					}
        					if ( isset( $field['placeholder'] ) && $field['placeholder'] != '' ) {
        						pll_register_string( $field['placeholder'], $field['placeholder'], __('Flexible Checkout Fields', 'flexible-checkout-fields' ) );
        					}
        					if ( isset( $field['type'] ) && isset( $checkout_field_type[$field['type']]['has_options'] ) && $checkout_field_type[$field['type']]['has_options'] ) {
        						$array_options = explode("\n", $field['option']);
        						if ( !empty( $array_options ) ){
        							foreach ( $array_options as $option ) {
        								$tmp = explode(':', $option, 2);
        								$option_label = trim( $tmp[1] );
        								pll_register_string( $option_label, $option_label, __('Flexible Checkout Fields', 'flexible-checkout-fields' ) );
        								unset($tmp);
        							}
        						}        						
        					}
        				}
        			}
        		}
        	}
        }
        
        function init_wpml() {
        	if ( function_exists( 'icl_register_string' ) ) {
        		$icl_language_code = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : get_bloginfo('language');
        		$settings = get_option('inspire_checkout_fields_settings', array() );
        		$checkout_field_type = $this->plugin->get_fields();
        		foreach ( $settings as $section ) {
        			if ( is_array( $section ) ) {
        				foreach ( $section as $field ) {
        					if ( isset( $field['label'] ) && $field['label'] != '' ) {        						
        						icl_register_string( 'flexible-checkout-fields', $field['label'], $field['label'], false, $icl_language_code );
        					}
        					if ( isset( $field['placeholder'] ) && $field['placeholder'] != '' ) {
        						icl_register_string( 'flexible-checkout-fields', $field['placeholder'], $field['placeholder'], false, $icl_language_code );
        					}
        					if ( isset( $field['type'] ) && isset( $checkout_field_type[$field['type']]['has_options'] ) && $checkout_field_type[$field['type']]['has_options'] ) {
        						$array_options = explode("\n", $field['option']);
        						if ( !empty( $array_options ) ){
        							foreach ( $array_options as $option ) {
        								$tmp = explode(':', $option, 2);
        								$option_label = trim( $tmp[1] );
        								icl_register_string( 'flexible-checkout-fields', $option_label, $option_label, false, $icl_language_code );
        								unset($tmp);
        							}
        						}        						
        					}
        				}
        			}
        		}        		
        	}
        }
        
        /**
         * add new menu to woocommerce function.
         *
         * @access public
         * @param none
         * @return void
         */

        public function initAdminMenuAction() {
            add_submenu_page( 'woocommerce', __( 'Checkout Fields Settings', 'flexible-checkout-fields' ),  __( 'Checkout Fields', 'flexible-checkout-fields' ) , 'manage_woocommerce', 'inspire_checkout_fields_settings', array( $this, 'renderInspireCheckoutFieldsSettingsPage') );
        }

        /**
         * wordpress action
         *
         * renders plugin submenu page
         */
        public function renderInspireCheckoutFieldsSettingsPage() {
            global $woocommerce;

            $settings = get_option( 'inspire_checkout_fields_settings' );

            $countries = new WC_Countries();
            $billing = $countries->get_address_fields($countries->get_base_country(), 'billing_');
            $shipping = $countries->get_address_fields($countries->get_base_country(), 'shipping_');
            
            if( empty( $settings ) || empty( $settings['order'] ) ) {        	
                $order = array(
                	'order_comments' => array(
                    'type'           => 'textarea',
                	'class'          => array('notes'),
                	'label'          => __( 'Order Notes', 'flexible-checkout-fields' ),
                	'placeholder'    => _x( 'Notes about your order, e.g. special notes for delivery.', 'placeholder', 'flexible-checkout-fields')
                	)
                );                
            }
            else {
           		$order = $settings['order'];
            }

            $checkout_fields = array_merge( array('billing' => $billing), array('shipping' => $shipping), array('order' => $order) );

        	foreach ( $this->plugin->sections as $custom_section => $custom_section_data ) {
        		if ( $custom_section_data['section'] == 'billing' || $custom_section_data['section'] == 'shipping' || $custom_section_data['section'] == 'order' ) continue;
        		if ( empty( $settings[$custom_section_data['section']] ) ) {
        			$checkout_fields[$custom_section_data['section']] = array();
        		}
        		else {
        			$checkout_fields[$custom_section_data['section']] = $settings[$custom_section_data['section']];
        		}

        	}
            $current_tab = ( empty( $_GET['tab'] ) ) ? 'fields_billing' : sanitize_text_field( urldecode( $_GET['tab'] ) );

            $args = array(
                    'current_tab' => $current_tab,
                    'tabs' => array(
                    		'settings'			=>	__( 'Settings', 'flexible-checkout-fields' ),
                    )
            );

            foreach ( $this->plugin->sections as $section => $section_data ) {
            	$args['tabs'][$section_data['tab']] = $section_data['tab_title'];
            }

            if ( !is_flexible_checkout_fields_pro_active() ) {
                $args['tabs']['pro'] = __( 'Custom Sections', 'flexible-checkout-fields' );
            }

	        $docs_link = 'https://www.wpdesk.net/docs/flexible-checkout-fields-pro-woocommerce-docs/';
	        if ( get_locale() === 'pl_PL' ) {
		        $docs_link = 'https://www.wpdesk.pl/docs/woocommerce-checkout-fields-docs/';
	        }
	        $docs_link .= '?utm_source=wp-admin-plugins&utm_medium=quick-link&utm_campaign=flexible-checkout-fields-docs-link';

	        include( 'views/settings-tabs.php' );

	        require_once('activation-tracker.php');
	        $activation_tracker = new Flexible_Checkout_Fields_Activation_Tracker( $this->plugin->plugin_namespace );

            switch ($current_tab) {
                case 'settings':

                	$args = array(
                		'plugin' => $this->plugin
                    );

                	include( 'views/settings-settings.php' );

                break;

                case 'checkboxes':
                    echo $this->loadTemplate('submenu_checkboxes', 'settings', array(
                            'plugin' => $this->plugin
                        )
                    );
                break;

                case 'pro':

                    include( 'views/settings-pro.php' );

                    break;

                default:

                	$args = array(
                            'plugin' 			=> $this->plugin,
                            'checkout_fields' 	=> $checkout_fields,
                            'show_ads' => $activation_tracker->is_activated_more_than_two_weeks()
                    );


                	include( 'views/settings-fields.php' );

                break;
            }

        }

        private function is_active_more_than_week() {
        	
        }


        public function validate_field_name( $name, $section ) {
        	if ( in_array( $section, array( 'billing' ) ) ) {
        		if ( in_array( $name, array(
        			'billing_address_1',
			        'billing_address_2',
			        'billing_address_index',
			        'billing_city',
			        'billing_company',
			        'billing_country',
			        'billing_email',
			        'billing_first_name',
			        'billing_last_name',
			        'billing_phone',
			        'billing_postcode'
		        ) ) ) {
        			return false;
		        }
	        }
	        if ( in_array( $section, array( 'shipping' ) ) ) {
		        if ( in_array( $name, array(
		        	'shipping_address_1',
			        'shipping_address_2',
			        'shipping_city',
			        'shipping_company',
			        'shipping_country',
			        'shipping_first_name',
			        'shipping_last_name',
			        'shipping_postcode',
			        'shipping_state'
		        ) ) ) {
			        return false;
		        }
	        }
        	return true;
        }

         /**
          * save settings function.
          *
          * @access public
          * @param none
          * @return void
          */

        public function updateSettingsAction(){

            if ( !empty( $_POST ) ) {
                if ( !empty($_POST['option_page']) && in_array( $_POST['option_page'], array('inspire_checkout_fields_settings', 'inspire_checkout_fields_checkboxes') ) ) {
                    if ( !empty( $_POST[$this->plugin->get_namespace()] ) ) {
                        foreach ( $_POST[$this->plugin->get_namespace()] as $name => $value ) {
                        	$settings = get_option( 'inspire_checkout_fields_' . $name, array() );
                        	if ( is_array( $value )) {
                        		foreach ( $value as $key => $val ) {
                        			$settings[$key] = $val;
                        			if ( isset( $_POST['reset_settings'] ) ) {
                        				unset( $settings[$key] );
                        			}
                        			else {
                        				$section_settings = array();
                        				if ( empty( $settings[$key] ) ) {
					                        $settings[$key] = array();
				                        }
                        				foreach ( $settings[ $key ] as $field_name => $field ) {
                        					if ( isset( $field['custom_field'] ) && $field['custom_field'] == '1' ) {
                        						if ( isset( $field['short_name'] ) && $field['short_name'] ) {
                        							$new_field_name = $key . '_' . $field['short_name'];
                        							if ( $new_field_name != $field_name ) {
								                        if ( $this->validate_field_name( $new_field_name, $key ) ) {
								                        	unset( $settings[$key][$field_name] );
									                        $field['name'] = $new_field_name;
									                        $field_name = $new_field_name;
								                        }
								                        else {
								                        	$this->add_admin_notice( sprintf( __( 'You cannot use this field name: %s, for field: %s.', 'flexible-checkout-fields' ), $field['short_name'], $field['label'] ), 'error' );
								                        }
							                        }
							                        else {
								                        $field['name'] = $new_field_name;
							                        }
						                        }
					                        }
                        					if ( is_array( $field ) ) {
						                        if ( empty( $field['label'] ) ) {
							                        $field['label'] = '';
						                        } else {
							                        $field['label'] = wp_unslash( $field['label'] );
						                        }
						                        if ( empty( $field['placeholder'] ) ) {
							                        $field['placeholder'] = '';
						                        } else {
							                        $field['placeholder'] = wp_unslash( $field['placeholder'] );
						                        }
					                        } else {
                        						$field = wp_unslash( $field );
					                        }
					                        $section_settings[$field_name] = $field;
				                        }
				                        $settings[$key] = $section_settings;
			                        }
                        		}
                        	}
                        	else {
                        		$settings = $value;
                        	}
                            update_option( 'inspire_checkout_fields_' . $name, $settings );
                            $settings = get_option( 'inspire_checkout_fields_' . $name, array() );
                            $this->plugin->init_sections();
                        }
                    }
                    elseif ( empty( $_POST[$this->plugin->get_namespace()] ) && $_POST['option_page'] == 'inspire_checkout_fields_checkboxes' ) {
                        update_option('inspire_checkout_fields_checkboxes', '');
                    }
                }
            }
        }

        public function add_admin_notice( $message, $class ) {
        	$this->admin_notices[] = array( 'message' => $message, 'class' => $class );
        }

        public function admin_notices() {
        	foreach ( $this->admin_notices as $admin_notice ) {
		        echo sprintf( '<div class="%s fade"><p>%s</p></div>', $admin_notice['class'], $admin_notice['message'] );
	        }
        }

    }
