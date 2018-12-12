<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Flexible_Checkout_Fields_Pro {

    protected $plugin = null;

	/**
	 * Flexible_Checkout_Fields_Pro constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro_Plugin $plugin
	 */
	public function __construct( Flexible_Checkout_Fields_Pro_Plugin $plugin ) {

		$this->plugin = $plugin;

		add_action( 'init', array( $this, 'init' ) );

		add_action( 'flexible_checkout_fields_fields', array( $this, 'flexible_checkout_fields_fields' ) );

		add_action( 'flexible_checkout_fields_settings_html', array( $this, 'flexible_checkout_fields_settings_html' ), 10, 3 );

		add_action( 'flexible_checkout_fields_settings_js_html', array( $this, 'flexible_checkout_fields_settings_js_html' ) );

		add_action( 'flexible_checkout_fields_settings_js_options', array( $this, 'flexible_checkout_fields_settings_js_options' ) );

		add_action( 'flexible_checkout_fields_settings_js_change', array( $this, 'flexible_checkout_fields_settings_js_change' ) );

		add_filter( 'flexible_checkout_fields_print_value', array( $this, 'flexible_checkout_fields_print_value' ), 10, 2 );

		add_filter( 'flexible_checkout_fields_admin_labels', array( $this, 'flexible_checkout_fields_admin_labels' ), 10, 3 );

		add_filter( 'flexible_checkout_fields_user_fields', array( $this, 'flexible_checkout_fields_user_fields' ), 10, 3 );

		add_filter( 'flexible_checkout_fields_custom_attributes', array( $this, 'flexible_checkout_fields_custom_attributes' ), 10, 2 );

		add_filter( 'flexible_checkout_fields_sections', array( $this, 'flexible_checkout_fields_sections' ), 10, 2 );

        add_filter( 'flexible_checkout_fields_all_sections', array( $this, 'flexible_checkout_fields_all_sections' ), 10, 2 );

		add_action( 'flexible_checkout_fields_settings', array( $this, 'flexible_checkout_fields_settings' ) );

		add_action( 'flexible_checkout_fields_section_settings', array( $this, 'flexible_checkout_fields_section_settings' ), 10, 2 );

		add_action( 'init', array( $this, 'init_sections' ) );

		add_action( 'flexible_checkout_fields_checkout_update_order_meta', array( $this, 'flexible_checkout_fields_checkout_update_order_meta' ) );

		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'woocommerce_admin_order_data_after_shipping_address' ), 9999999 );


		add_filter( 'woocommerce_checkout_fields', array( $this, 'woocommerce_checkout_fields'), 99999999, 1 );

		add_action( 'wp_footer', array( $this, 'wp_footer' ) );

	}

	public function get_settings() {
		$default = array(
		);
		$settings = get_option('inspire_checkout_fields_settings', $default );
		return $settings;
	}

	public function get_section_settings() {
		$default = array(
		);
		$settings = get_option('inspire_checkout_fields_section_settings', $default );
		return $settings;
	}

	public function init() {
		$settings = $this->get_settings();
		if ( get_option( 'flexible_checkout_fields_init_checkboxes', '0' ) == '0' ) {
			$checkboxes = get_option( 'inspire_checkout_fields_checkboxes', array() );
			foreach ( $checkboxes as $checkbox ) {
				if ( empty( $settings['after_customer_details'] ) ) {
					$settings['after_customer_details'] = array();
				}
				$settings['review_order_before_submit'][$checkbox['name']] = array(
						'custom_field' 	=> '1',
						'name'			=> $checkbox['name'],
						'visible'		=> '0',
						'required'		=> $checkbox['required'],
						'label'			=> $checkbox['label'],
						'placeholder'	=> __( 'Yes', 'flexible-checkout-fields-pro' ),
						'class'			=> $checkbox['class'],
						'type'			=> 'inspirecheckbox',
						'file_types'	=> '',
						'file_size'		=> '',
						'date_format'	=> 'dd.mm.yy',
						'days_before'	=> '0',
						'days_after'	=> '',
				);
				update_option( 'inspire_checkout_fields_settings', $settings );
				update_option( 'inspire_checkout_fields_review_order_before_submit', '1' );
			}
			update_option( 'flexible_checkout_fields_init_checkboxes', '1' );
		}
	}

	public function woocommerce_admin_order_data_after_shipping_address( $order ) {

		$additional_fields = false;

		$sections = $this->flexible_checkout_fields_sections( array(), false );
		$settings = $this->get_settings();

		$flexible_checkout_fields = flexible_checkout_fields();
		$checkout_field_type = $flexible_checkout_fields->get_fields();

		foreach ( $sections as $section => $section_data ) {
			if ( isset( $settings[$section_data['section']] ) && is_array( $settings[$section_data['section']] ) ) {
				foreach ( $settings[$section_data['section']] as $key => $field ) {
					$value = wpdesk_get_order_meta( $order, '_'.$key, true );
					$additional_fields = true;
				}
			}
		}

		if ( $additional_fields != false ) {
			include( 'views/order-additional-fields.php' );
		}
	}

	public function flexible_checkout_fields_checkout_update_order_meta( $order_id ) {
		$sections = $this->flexible_checkout_fields_sections( array(), false );
		$settings = $this->get_settings();
		foreach ( $sections as $section => $section_data ) {
			if ( isset( $settings[$section_data['section']] ) && is_array( $settings[$section_data['section']] ) ) {
				foreach ( $settings[$section_data['section']] as $key=>$field ) {
					if ( isset( $_POST[$key]) ) {
						$value = $_POST[$key];
						wpdesk_update_order_meta( $order_id, '_'.$key, esc_attr( $value ) );
					}
				}
			}
		}
	}

	public function checkout_form_action() {

		$sections = $this->flexible_checkout_fields_sections( array(), false );

		$settings = $this->get_settings();

		$section_settings = $this->get_section_settings();

		$current_filter = current_filter();

		$fields = array();

		$checkout = WC()->checkout();

		if ( empty( $section_settings[$sections[$current_filter]['section']] ) ) {
			$section_settings = array();
		}
		else {
			$section_settings = $section_settings[$sections[$current_filter]['section']];
		}

		if ( isset( $settings[$sections[$current_filter]['section']] ) ) {
			$fields = apply_filters( 'flexible_chekout_fields_fields', $settings[$sections[$current_filter]['section']], $sections[$current_filter]['section'] );
		}

		if ( !empty( $fields ) && is_array( $fields ) ) {
		    $args = array(
			    'fields' 	=> $fields,
			    'checkout'	=> $checkout,
			    'section_settings'	=> $section_settings
		    );
		    echo $this->plugin->load_template( $sections[$current_filter]['section'], 'checkout/flexible-checkout-fields', $args );
		}

	}

	public function init_sections( $checkout ) {
		$sections = $this->flexible_checkout_fields_sections( array(), false );

		foreach ( $sections as $section => $section_data ) {
			add_action( $section, array( $this, 'checkout_form_action' ), 100 );
		}
	}

	public function flexible_checkout_fields_section_settings( $section, $settings ) {

		$section_settings = $this->get_section_settings();

		$sections = $this->flexible_checkout_fields_sections( array(), true );
		foreach ( $sections as $section_data ) {
			if ( $section_data['section'] == $section ) {
				include( 'views/settings-settings-section-settings.php' );
			}
		}
	}

	public function flexible_checkout_fields_settings() {

		$sections = $this->flexible_checkout_fields_sections( array(), true );
		include( 'views/settings-settings.php' );

	}

	public function flexible_checkout_fields_all_sections( $sections, $get_disabled = true ) {
	    return $this->flexible_checkout_fields_sections( $sections, $get_disabled );
    }

	public function flexible_checkout_fields_sections( $sections, $get_disabled = false ) {

		$sections_add = array();

		$sections_add['woocommerce_checkout_before_customer_details'] = array(
				'section'			=> 'before_customer_details',
				'tab'				=> 'fields_before_customer_details',
				'tab_title'			=> __( 'Before Customer Details', 'flexible-checkout-fields-pro' ),
				'title' 			=> __( 'Before Customer Details Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_checkout_after_customer_details'] = array(
				'section'			=> 'after_customer_details',
				'tab'				=> 'fields_after_customer_details',
				'tab_title'			=> __( 'After Customer Details', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'After Customer Details Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_before_checkout_billing_form'] = array(
				'section'			=> 'before_checkout_billing_form',
				'tab'				=> 'fields_before_checkout_billing_form',
				'tab_title'			=> __( 'Before Billing Form', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'Before Billing Form Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_after_checkout_billing_form'] = array(
				'section'			=> 'after_checkout_billing_form',
				'tab'				=> 'fields_after_checkout_billing_form',
				'tab_title'			=> __( 'After Billing Form', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'After Billing Form Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_before_checkout_shipping_form'] = array(
				'section'			=> 'before_checkout_shipping_form',
				'tab'				=> 'fields_before_checkout_shipping_form',
				'tab_title'			=> __( 'Before Shipping Form', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'Before Shipping Form Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_after_checkout_shipping_form'] = array(
				'section'			=> 'after_checkout_shipping_form',
				'tab'				=> 'fields_after_checkout_shipping_form',
				'tab_title'			=> __( 'After Shipping Form', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'After Shipping Form Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_before_checkout_registration_form'] = array(
				'section'			=> 'before_checkout_registration_form',
				'tab'				=> 'fields_before_checkout_registration_form',
				'tab_title'			=> __( 'Before Registration Form', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'Before Registration Form Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_after_checkout_registration_form'] = array(
				'section'			=> 'after_checkout_registration_form',
				'tab'				=> 'fields_after_checkout_registration_form',
				'tab_title'			=> __( 'After Registration Form', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'After Registration Form Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_before_order_notes'] = array(
				'section'			=> 'before_order_notes',
				'tab'				=> 'fields_before_order_notes',
				'tab_title'			=> __( 'Before Order Notes', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'Before Order Notes Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_after_order_notes'] = array(
				'section'			=> 'after_order_notes',
				'tab'				=> 'fields_after_order_notes',
				'tab_title'			=> __( 'After Order Notes', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'After Order Notes Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_review_order_before_submit'] = array(
				'section'			=> 'review_order_before_submit',
				'tab'				=> 'fields_review_order_before_submit',
				'tab_title'			=> __( 'Before Submit', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'Before Submit Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		$sections_add['woocommerce_review_order_after_submit'] = array(
				'section'			=> 'review_order_after_submit',
				'tab'				=> 'fields_review_order_after_submit',
				'tab_title'			=> __( 'After Submit', 'flexible-checkout-fields-pro' ),
				'title'				=> __( 'After Submit Fields', 'flexible-checkout-fields-pro' ),
				'custom_section' 	=> true
		);

		foreach ( $sections_add as $section => $section_data ) {
			if ( $get_disabled || get_option( 'inspire_checkout_fields_' . $section_data['section'] , '0' ) == '1' ) {
				$sections[$section] = $section_data;
			}

		}

		return $sections;
	}

	public function flexible_checkout_fields_custom_attributes( $attributes, $field ) {
		if ( isset( $field['type'] ) && $field['type'] == 'datepicker' ) {
			$attributes['date_format'] = '';
			if ( isset( $field['date_format'] ) ) {
				$attributes['date_format'] = $field['date_format'];
			}
			if ( isset( $field['days_before'] ) ) {
				$attributes['days_before'] = $field['days_before'];
				if ( $field['days_before'] === '0' ) {
					$attributes['days_before'] = '0.0';
                }
			}
			if ( isset( $field['days_after'] ) ) {
				$attributes['days_after'] = $field['days_after'];
				if ( $field['days_after'] === '0' ) {
					$attributes['days_after'] = '0.0';
				}
			}
		}
		return $attributes;
	}

	public function flexible_checkout_fields_user_fields( $return, $field, $user ) {
		switch ( $field['type'] ) {

			case 'heading':
				$return = '';
				break;

			case 'info':
				$return = '';
				break;

			case 'timepicker':
				$return = '
                                        <tr>
                                            <th><label for="'.$field['name'].'">'.stripslashes($field['label']).'</label></th>
                                            <td>
                                                <input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.esc_attr( get_the_author_meta( $field['name'], $user->ID ) ).'" class="regular-text load-timepicker" /><br /><span class="description"></span>
                                            </td>
                                        </tr>
                                    ';
				break;

			case 'colorpicker':
				$return = '
                                        <tr>
                                            <th><label for="'.$field['name'].'">'.$field['label'].'</label></th>
                                            <td>
                                                <input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.esc_attr( get_the_author_meta( $field['name'], $user->ID ) ).'" class="regular-text load-colorpicker" /><br /><span class="description"></span>
                                            </td>
                                        </tr>
                                    ';
				break;

			case 'datepicker':
				$return = '
                                        <tr>
                                            <th><label for="'.$field['name'].'">'.$field['label'].'</label></th>
                                            <td>
                                                <input type="text" name="'.$field['name'].'" id="'.$field['name'].'" value="'.esc_attr( get_the_author_meta( $field['name'], $user->ID ) ).'" class="regular-text load-datepicker" /><br /><span class="description"></span>
                                            </td>
                                        </tr>
                                    ';
				break;

			case 'select':
				$array_options = explode("\n", $field['option']);
				if(!empty($array_options)){
					foreach ($array_options as $option) {
						$tmp = explode(':', $option);
						$options[trim($tmp[0])] = trim($tmp[0]);
						if ( isset( $tmp[1] ) ) {
							$options[ trim( $tmp[0] ) ] = trim( $tmp[1] );
						}
						unset($tmp);
					}
				}

				$return = '<tr><th><label for="'.$field['name'].'">'.$field['label'].'</label></th><td><select name="'.$field['name'].'" id="'.$field['name'].'" class="regular-text" />';
				foreach ($options as $okey => $option) {
					$selected = '';
					if($okey == esc_attr( get_the_author_meta( $field['name'], $user->ID ) )) $selected = " selected";
					$return .= '<option value="'.$okey.'"'.$selected.'>'.$option.'</option>';
					unset($selected);
				}

				$return .= '</select><br /><span class="description"></span></td></tr>';

				unset($options);
				break;

			case 'inspireradio':
				$array_options = explode("\n", $field['option']);
				if(!empty($array_options)){
					foreach ($array_options as $option) {
						$tmp = explode(':', $option);
						$options[trim($tmp[0])] = trim($tmp[1]);
						unset($tmp);
					}
				}

				$return = '<tr><th>'.$field['label'].'</th><td>';
				foreach ($options as $okey => $option) {
				    $checked = '';
					if($okey == esc_attr( get_the_author_meta( $field['name'], $user->ID ) )) $checked = " checked";
					$return .= '<input type="radio" name="'.$field['name'].'" id="'.$field['name'].$okey.'" value="'.$okey.'"'.$checked.'><label for="'.$field['name'].$okey.'">'.$option.'</label><br />';
					unset($checked);
				}

				$return .= '<br /><span class="description"></span></td></tr>';

				unset($options);
				break;

			case 'inspirecheckbox':
			    $checked = '';
				$return = '<tr><th><label for="'.$field['name'].'">'.$field['label'].'</label></th><td>';
				if($field['placeholder'] == esc_attr( get_the_author_meta( $field['name'], $user->ID ) )) $checked = " checked";
				$return .= '<input type="checkbox" name="'.$field['name'].'" id="'.$field['name'].'" value="'.$field['placeholder'].'"'.$checked.'><label for="'.$field['name'].'">'.$field['label'].'</label><br />';
				unset($checked);

				$return .= '<br /><span class="description"></span></td></tr>';

				unset($options);
				break;

			case 'file':
				$return = '';
				break;

		}

		return $return;
	}

	public function flexible_checkout_fields_admin_labels( $new, $field, $field_name ) {

		if ( isset( $field['type'] ) && $field['type'] == 'inspireradio' ) {
			$new['type'] = "select";
		}

		if ( isset( $field['type'] ) && $field['type'] == 'inspirecheckbox' ) {
			$new['type'] = "select";
			$new['options'] = array(
					__( 'Missing Value', 'flexible-checkout-fields-pro' ) =>  __( 'Missing Value', 'flexible-checkout-fields-pro' ),
					$field['placeholder'] => $field['placeholder']
			);
		}

		if ( isset( $field['type'] ) && ( $field['type'] == 'select' || $field['type'] == 'inspireradio' ) ) {
			$array_options = explode("\n", $field['option']);
			if ( !empty($array_options ) ) {
				foreach ( $array_options as $option ) {
					$tmp = explode( ':', $option );
					$options[trim( $tmp[0] )] = trim( $tmp[1] );
					unset( $tmp );
				}
				$new['options'] = $options;
				unset( $options );
			}
		}

		if ( $new['type'] == 'select' ) {
			$new['class'] .= ' js_field-country select';
		}

		return $new;

	}

	public function flexible_checkout_fields_print_value( $value, $field ) {
		if ( $field['type'] == 'select' || $field['type'] == 'inspireradio' ) {
			$array_options = explode("\n", $field['option'] );
            if ( !empty( $array_options ) ) {
            	foreach ($array_options as $option) {
            		$tmp = explode(':', $option);
                    $options[trim($tmp[0])] = trim($tmp[1]);
                    unset($tmp);
                }
                if ( isset( $options[$value] ) ) {
                	$value = $options[$value];
                }
                unset($options);
            }
		}
		return $value;
	}

	public function flexible_checkout_fields_settings_js_change( ) {
		include( 'views/settings-js-change.php' );
	}

	public function flexible_checkout_fields_settings_js_options( ) {
		include( 'views/settings-js-options.php' );
	}

	public function flexible_checkout_fields_settings_js_html( ) {
		include( 'views/settings-js-html.php' );
	}

	public function flexible_checkout_fields_settings_html( $key, $name, $settings ) {
		if ( $settings[$key][$name]['type'] == 'file' ) {
			include( 'views/settings-html-file.php' );
		}
		if ( $settings[$key][$name]['type'] == 'datepicker' ) {
			include( 'views/settings-html-datepicker.php' );
		}
	}

	public function flexible_checkout_fields_fields( $fields ) {

		$add_fields = array();

		$add_fields['inspirecheckbox'] = array(
				'name' 				=> __( 'Checkbox', 'flexible-checkout-fields-pro' ),
				'placeholder_label'	=> __( 'Value', 'flexible-checkout-fields-pro' ),
				'label_is_required'	=> true,
		);

		$add_fields['inspireradio'] = array(
				'name' 					=> __( 'Radio button', 'flexible-checkout-fields-pro' ),
				'disable_placeholder' 	=> true,
				'has_options'			=> true
		);

		$add_fields['select'] = array(
				'name' 					=> __( 'Select (Drop Down)', 'flexible-checkout-fields-pro' ),
				'disable_placeholder' 	=> true,
				'has_options'			=> true
		);

		$add_fields['datepicker'] = array(
				'name' 					=> __( 'Date', 'flexible-checkout-fields-pro' )
		);

		$add_fields['timepicker'] = array(
				'name' 					=> __( 'Time', 'flexible-checkout-fields-pro')
		);

		$add_fields['colorpicker'] = array(
				'name' 					=> __( 'Color Picker', 'flexible-checkout-fields-pro' )
		);

		$add_fields['heading'] = array(
				'name' 					=> __( 'Headline', 'flexible-checkout-fields-pro' ),
				'has_required'			=> false,
				'disable_placeholder' 	=> true,
				'exclude_in_admin'		=> true,
		);

		$add_fields['info'] = array(
				'name' 					=> __( 'HTML', 'flexible-checkout-fields-pro' ),
				'has_required'			=> false,
				'disable_placeholder' 	=> true,
				'exclude_in_admin'		=> true,
		);


		$upload_folder = '/wp-content/uploads/woocommerce_uploads/checkout_fields';
		add_filter( 'upload_dir', array( $this->plugin->pro_types, 'upload_dir' ) );
		$wp_upload_dir = wp_upload_dir();
		remove_filter( 'upload_dir', array( $this->plugin->pro_types, 'upload_dir' ) );
		$upload_folder = substr( $wp_upload_dir['path'], strlen( ABSPATH ) );
		$add_fields['file'] = array(
				'name' 					=> __( 'File Upload', 'flexible-checkout-fields-pro' ),
				'description'			=> sprintf( __( 'Files will be saved to: %s', 'flexible-checkout-fields-pro' ), '<br/>' . $upload_folder ),
				'disable_placeholder' 	=> true,
				'exclude_in_admin'		=> true,
		);

		foreach ( $add_fields as $key => $field ) {
			$fields[$key] = $field;
		}

		return $fields;
	}

	public function wp_footer() {
	    if ( is_checkout() ) {
	        $flexible_checkout_fields = flexible_checkout_fields();
            $fcf_conditions = array();
            $sections = $flexible_checkout_fields->sections;
            $settings = $this->get_settings();
            foreach ( $sections as $section => $section_data ) {
                if ( isset( $settings[$section_data['section']] ) && is_array( $settings[$section_data['section']] ) ) {
                    foreach ( $settings[$section_data['section']] as $key => $field ) {
                        if ( isset( $field['conditional_logic_fields'] ) && $field['conditional_logic_fields'] == '1' ) {
                            $fcf_conditions[$key] = array(
                                'conditional_logic_fields'              => $field['conditional_logic_fields'],
                                'conditional_logic_fields_action'       => $field['conditional_logic_fields_action'],
                                'conditional_logic_fields_operator'     => $field['conditional_logic_fields_operator'],
                                'conditional_logic_fields_rules'        => isset( $field['conditional_logic_fields_rules'] ) ? $field['conditional_logic_fields_rules'] : array(),
                            );
                        }
                    }
                }
            }
	        ?>
            <script type="text/javascript">
                /* FCF PRO */
                <?php /* ?>
                var fcf_sections = <?php echo json_encode( $sections, JSON_PRETTY_PRINT );  ?>;
                <?php */ ?>
                var fcf_conditions = <?php echo json_encode( $fcf_conditions );  ?>;
            </script>
            <?php
        }
    }

    public function woocommerce_checkout_fields( $checkout_fields ) {
        if ( is_checkout() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
	        return $checkout_fields;
        }
        $hidden_fields = array();
	    $flexible_checkout_fields = flexible_checkout_fields();
        $sections = $flexible_checkout_fields->sections;
        $settings = $this->get_settings();
        foreach ( $sections as $section => $section_data ) {
            if ( isset( $settings[$section_data['section']] ) && is_array( $settings[$section_data['section']] ) ) {
                foreach ( $settings[$section_data['section']] as $key => $field ) {
                    if ( isset( $field['conditional_logic_fields'] ) && $field['conditional_logic_fields'] == '1' ) {
                        $matched = true;
                        if ( $field['conditional_logic_fields_operator'] == 'or' ) {
                            $matched = false;
                        }
                        $hidden = false;
                        foreach ( $field['conditional_logic_fields_rules'] as $rule_field)  {
                            $field_value = '';
                            if ( isset( $rule_field['field'] ) && isset( $_POST[$rule_field['field']] ) ) {
                                $field_value = $_POST[$rule_field['field']];
                            }
                            if ( isset( $rule_field['value'] ) && $rule_field['value'] == 'unchecked' && $field_value == '' ) {
                                $field_value = 'unchecked';
                            }
                            if ( isset( $rule_field['value'] ) && $rule_field['value'] == 'checked' && $field_value != '' ) {
                                $field_value = 'checked';
                            }
                            if ( $field['conditional_logic_fields_operator'] == 'and' ) {
                                if ( isset( $rule_field['value'] ) && $rule_field['value'] == $field_value ) {
                                    $matched = $matched && true;
                                }
                                else {
                                    $matched = $matched && false;
                                }
                            }
                            if ( $field['conditional_logic_fields_operator'] == 'or' ) {
                                if ( $rule_field['value'] == $field_value ) {
                                    $matched = $matched || true;
                                }
                                else {
                                    $matched = $matched || false;
                                }
                            }
                        }
                        if ( $matched ) {
                            if ( $field['conditional_logic_fields_action'] == 'show' ) {
                                $hidden = false;
                            }
                            if ( $field['conditional_logic_fields_action'] == 'hide' ) {
                                $hidden = true;
                            }
                        }
                        else {
                            if ( $field['conditional_logic_fields_action'] == 'show' ) {
                                $hidden = true;
                            }
                            if ( $field['conditional_logic_fields_action'] == 'hide' ) {
                                $hidden = false;
                            }
                        }
                        if ( $hidden ) {
                            $hidden_fields[] = $key;
                        }
                    }
                }
            }
        }
        foreach ( $checkout_fields as $section => $fields ) {
            foreach ( $fields as $field_name => $field ) {
                if ( in_array( $field_name, $hidden_fields )) {
                    $checkout_fields[$section][$field_name]['required'] = false;
                }
            }
        }
	    return $checkout_fields;
    }

}
