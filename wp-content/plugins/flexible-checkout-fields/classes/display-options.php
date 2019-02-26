<?php

class Flexible_Checkout_Fields_Disaplay_Options {

	const HOOK_PRIORITY_LAST = 999999;

	const DISPLAY_ON_ADDRESS = 'display_on_address';
	const DISPLAY_ON_THANK_YOU = 'display_on_thank_you';
	const DISPLAY_ON_ORDER = 'display_on_order';
	const DISPLAY_ON_EMAILS = 'display_on_emails';

	protected $plugin;

	protected $current_address_type = 'shipping';

	protected $in_email_address = false;

	/**
	 * Flexible_Checkout_Fields_Disaplay_Options constructor.
	 *
	 * @param Flexible_Checkout_Fields_Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 *
	 */
	protected function hooks() {
		add_filter( 'woocommerce_localisation_address_formats', array( $this, 'woocommerce_localisation_address_formats_filter' ), self::HOOK_PRIORITY_LAST );
		add_filter( 'woocommerce_formatted_address_replacements', array( $this, 'woocommerce_formatted_address_replacements' ), self::HOOK_PRIORITY_LAST, 2 );
		add_filter( 'woocommerce_order_formatted_billing_address', array( $this, 'woocommerce_order_formatted_billing_address' ), self::HOOK_PRIORITY_LAST, 2 );
		add_filter( 'woocommerce_order_formatted_shipping_address', array( $this, 'woocommerce_order_formatted_shipping_address' ), self::HOOK_PRIORITY_LAST, 2 );

		// addresses in my account
		add_filter( 'woocommerce_my_account_my_address_formatted_address', array( $this, 'woocommerce_my_account_my_address_formatted_address' ), 10, 3 );

		add_action( 'woocommerce_billing_fields', array($this, 'woocommerce_billing_fields'), 19999 );
		add_action( 'woocommerce_shipping_fields', array($this, 'woocommerce_shipping_fields'), 19999 );

		add_action( 'woocommerce_email_customer_details', array( $this, 'woocommerce_email_customer_details_start' ), 10 );

		add_action( 'woocommerce_email_customer_details', array( $this, 'woocommerce_email_customer_details_end' ), 10000 );

		// additional fields
		add_action( 'woocommerce_thankyou', array( $this, 'additional_information_fields' ), 75 );
		add_action( 'woocommerce_email_order_meta', array( $this, 'email_additional_information_fields' ), 195 );
		add_action( 'woocommerce_view_order', array( $this, 'additional_information_fields' ), 195 );

		add_action( 'woocommerce_edit_account_form', array( $this, 'woocommerce_edit_account_form' ) );
	}

	public function woocommerce_edit_account_form() {
	}

	public function email_additional_information_fields( $order ) {
		$this->in_email_address = true;
		$this->additional_information_fields( wpdesk_get_order_id( $order ) );
		$this->in_email_address = false;
	}

	/**
	 * Displays additional fields.
	 *
	 * @param int $order_id Order id.
	 */
	public function additional_information_fields( $order_id ) {

		$settings = $this->plugin->getCheckoutFields( $this->plugin->get_settings() );

		$checkout_field_type = $this->plugin->get_fields();

		if ( ! empty( $settings ) && is_array( $settings ) ) {
			$return = array();
			foreach ( $settings as $key => $type ) {
				if ( in_array( $key, array( 'billing', 'shipping' ), true ) ) {
					continue;
				}
				if ( isset( $type ) && is_array( $type ) ) {
					foreach ( $type as $field ) {
						if ( isset( $field['visible'] ) && 0 === intval( $field['visible'] ) && isset( $field['custom_field'] ) && 1 === intval( $field['custom_field'] ) ) {
							$value = wpdesk_get_order_meta( $order_id, '_' . $field['name'], true );
							if ( $this->is_field_displayable( $field ) && '' !== $value ) {
								if ( ! empty( $checkout_field_type[ $field['type'] ]['has_options'] ) ) {
									$options = $field['options'];
									if ( isset( $options[ $value ] ) ) {
										$value = $options[ $value ];
									}
								}
								$value = apply_filters( 'flexible_checkout_fields_print_value', $value, $field );
								if ( '' !== $value ) {
									$return[] = stripslashes( strip_tags( wpdesk__( $field['label'], 'flexible-checkout-fields' ) ) ) . ': ' . $value;
								}
							}
						}
					}
				}
			}
			if ( count( $return ) > 0 ) {
				echo '<div class="inspire_checkout_fields_additional_information">';
				echo '<h3>' . __( 'Additional Information', 'flexible-checkout-fields' ) . '</h3>';
				echo '<p>' . implode( '<br />', $return ) . '</p>';
				echo '</div>';
			}
		}
	}


	public function woocommerce_email_customer_details_start() {
		$this->in_email_address = true;
	}

	public function woocommerce_email_customer_details_end() {
		$this->in_email_address = false;
	}

	public function woocommerce_my_account_my_address_formatted_address( $address, $customer_id, $address_type ) {
		$checkout_field_type = $this->plugin->get_fields();
		$this->current_address_type = $address_type;
		WC()->countries->address_formats = '';
		$cf_fields = $this->getCheckoutFields( array(), $address_type );
		foreach ( $cf_fields as $field_key => $field ) {
			if ( !isset( $address[$field['name']] ) ) {
				$val = '';
				if ( isset( $field['custom_field'] ) && $field['custom_field'] == 1 ) {
					$val .= strip_tags( wpdesk__( $field['label'], 'flexible-checkout-fields' ) ) . ': ';
				}

				$meta_value = get_user_meta( $customer_id, $field_key, true );
				if ( !empty( $field['type'] ) && !empty( $checkout_field_type[$field['type']]['has_options'] ) ) {
					$array_options = explode("\n", $field['option']);
					$options = array();
					if( !empty( $array_options ) ) {
						foreach ( $array_options as $option ) {
							$tmp = explode(':', $option , 2 );
							$options[trim($tmp[0])] = trim($tmp[0]);
							if ( isset( $tmp[1] ) ) {
								$options[ trim( $tmp[0] ) ] = wpdesk__( trim( $tmp[1] ), 'flexible-checkout-fields' );
							}
							unset( $tmp );
						}
					}
					if ( isset( $options[$meta_value] ) ) {
						$meta_value = $options[$meta_value];
					}
				}

				$meta_value = apply_filters( 'flexible_checkout_fields_user_meta_display_value', $meta_value, $field );

				$val .= $meta_value;
				$address[$field['name']] = $val;
				$address[str_replace(  $address_type . '_', '', $field['name'] )] = $val;
			}
		}
		return $address;
	}

	public function getCheckoutFields( $fields, $request_type = null ) {
		return $this->plugin->getCheckoutFields( $fields, $request_type );
	}

	/**
	 * Append field to address format.
	 *
	 * @param string $format
	 * @param string $field_key
	 * @param array $field
	 *
	 * @return string
	 */
	private function append_field_to_address_format( $format, $field_key, $field ) {
		if ( ( $this->is_thankyou_page() || $this->is_in_email() || $this->is_order_page() )
		     && in_array( $field_key, array( 'billing_phone', 'billing_email' ) )
		) {
			return $format;
		}
		if ( isset( $field['type'] ) && in_array( $field['type'], array( 'heading', 'info' ) ) ) {
			return $format;
		}
		if ( $this->is_field_displayable( $field ) ) {
			if ( $format != '' ) {
				$format .= "\n";
			}
			$format .= '{' . str_replace( $this->current_address_type . '_', '',  $field['name'] . '}' );
		}
		return $format;
	}

	/**
	 * Localisation address formats - woocommerce filter.
	 *
	 * @param array $formats
	 *
	 * @return array
	 */
	public function woocommerce_localisation_address_formats_filter( $formats ) {
		$fields = $this->getCheckoutFields( array(), $this->current_address_type );
		if ( empty( $fields ) ) {
			return $formats;
		}
		$default_format = $formats['default'];
		if ( $this->is_edit_address_page()
		     || $this->is_order_page()
		     || $this->is_in_email()
		     || $this->is_thankyou_page()
		) {
			$default_format = '';
			foreach ( $fields as $field_key => $field ) {
				$default_format = $this->append_field_to_address_format( $default_format, $field_key, $field );
			}
		}
		$new_formats = array( 'default' => $default_format );
		return $new_formats;
	}

	private function is_field_displayable( $field ) {
		$displayable = true;
		$fcf_field = new Flexible_Checkout_Fields_Field( $field, $this->plugin );
		if ( $this->is_edit_address_page() ) {
			$displayable = !isset( $field[self::DISPLAY_ON_ADDRESS] ) || $field[self::DISPLAY_ON_ADDRESS] == '1';
			if ( $fcf_field->is_field_excluded_for_user() ) {
				$displayable = false;
			}
		}
		if ( $this->is_order_page() ) {
			$displayable = !isset( $field[self::DISPLAY_ON_ORDER] ) || $field[self::DISPLAY_ON_ORDER] == '1';
		}
		if ( $this->is_in_email() ) {
			$displayable = !isset( $field[self::DISPLAY_ON_EMAILS] ) || $field[self::DISPLAY_ON_EMAILS] == '1';
		}
		if ( $this->is_thankyou_page() ) {
			$displayable = !isset( $field[self::DISPLAY_ON_THANK_YOU] ) || $field[self::DISPLAY_ON_THANK_YOU] == '1';
		}
		return $displayable;
	}

	public function is_admin_edit_order() {
		$admin_edit_order = false;
		if ( is_admin() ) {
			$admin_edit_order = true;
		}
		return $admin_edit_order;
	}

	public function is_edit_address_page() {
		global $wp;
		$edit_address_page = false;
		if ( is_account_page() ) {
			if ( isset( $wp->query_vars['edit-address'] ) ) {
				$edit_address_page = true;
			}
		}
		return $edit_address_page;
	}

	public function is_order_page() {
		global $wp;
		$order_page = false;
		if ( is_account_page() ) {
			if ( isset( $wp->query_vars['view-order'] ) ) {
				$order_page = true;
			}
		}
		return $order_page;
	}

	public function is_in_email() {
		$in_email = false;
		if ( $this->in_email_address ) {
			$in_email = true;
		}
		return $in_email;
	}

	public function is_thankyou_page() {
		global $wp;
		$thankyou_page = false;
		if ( is_checkout() ) {
			if ( isset( $wp->query_vars['order-received'] ) ) {
				$thankyou_page = true;
			}
		}
		return $thankyou_page;
	}

	public function woocommerce_formatted_address_replacements( $fields, $args ) {
		foreach ( $args as $arg_key => $arg ) {
			if ( !isset( $fields['{' . $arg_key . '}'] ) ) {
				$fields['{' . $arg_key . '}'] = $arg;
			}
		}
		return $fields;
	}

	public function woocommerce_order_formatted_billing_address( $fields, $order ) {
		$this->current_address_type = 'billing';
		WC()->countries->address_formats = '';
		return $this->woocommerce_order_formatted_address( $fields, $order, 'billing' );
	}

	/**
	 * @param array $fields
	 * @param WC_Order $order
	 * @param string $address_type
	 *
	 * @return mixed
	 */
	public function woocommerce_order_formatted_address( $fields, $order, $address_type ) {
		$cf_fields = $this->getCheckoutFields( array(), $address_type );
		$checkout_field_type = $this->plugin->get_fields();

		foreach ( $cf_fields as $field_key => $field ) {
			$val = wpdesk_get_order_meta( $order, '_' . $field_key, true );
			if ( isset( $field['custom_field'] ) && $field['custom_field'] == '1' ) {

				$val = strip_tags( wpdesk__( $field['label'], 'flexible-checkout-fields' ) ) . ': ';

				$meta_value = wpdesk_get_order_meta( $order, '_' . $field_key, true );

				if ( !empty( $checkout_field_type[$field['type']]['has_options'] ) ) {
					$array_options = explode("\n", $field['option']);
					$options = array();
					if( !empty( $array_options ) ) {
						foreach ( $array_options as $option ) {
							$tmp = explode(':', $option , 2 );
							$options[trim($tmp[0])] = trim($tmp[0]);
							if ( isset( $tmp[1] ) ) {
								$options[ trim( $tmp[0] ) ] = wpdesk__( trim( $tmp[1] ), 'flexible-checkout-fields' );
							}
							unset( $tmp );
						}
					}
					if ( isset( $options[$meta_value] ) ) {
						$meta_value = $options[$meta_value];
					}
				}

				$meta_value = apply_filters( 'flexible_checkout_fields_print_value', $meta_value, $field );

				$val .= $meta_value;

			}
			$fields[$field['name']] = $val;
			$fields[str_replace(  $address_type . '_', '', $field['name'] )] = $val;
		}
		return $fields;

	}

	public function woocommerce_order_formatted_shipping_address( $fields, $order ) {
		$this->current_address_type = 'shipping';
		WC()->countries->address_formats = '';
		return $this->woocommerce_order_formatted_address( $fields, $order, 'shipping' );
	}

	public function woocommerce_billing_fields( $fields ) {
		return $this->woocommerce_fields( $fields, 'billing' );
	}

	public function woocommerce_shipping_fields( $fields ) {
		return $this->woocommerce_fields( $fields, 'shipping' );
	}

	protected function woocommerce_fields( $fields, $section ) {
		global $wp;
		$cf_fields = $this->getCheckoutFields( array(), $section );
		$edit_address_page = false;
		if ( is_account_page() ) {
			if ( isset( $wp->query_vars['edit-address'] ) ) {
				$edit_address_page = true;
			}
		}
		foreach ( $cf_fields as $cf_field_key => $cf_field ) {
			$remove_field = false;
			if ( $edit_address_page ) {
				$remove_field = true;
				if ( !isset( $cf_field[self::DISPLAY_ON_ADDRESS] ) || $cf_field[self::DISPLAY_ON_ADDRESS] == '1' ) {
					$remove_field = false;
				}
			}
			if ( $remove_field ) {
				unset( $fields[$cf_field_key] );
			}
		}
		return $fields;
	}

}
