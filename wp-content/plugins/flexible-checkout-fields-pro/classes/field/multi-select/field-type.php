<?php

/**
 * Handles hooks for multi-select field type.
 *
 * Class Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type
 */
class Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type extends Flexible_Checkout_Fields_Pro_Field_Type {

	const FIELD_TYPE_MULTISELECT = 'wpdeskmultiselect';

	/**
	 * Is multiselect field?
	 *
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	private function is_multiselect( array $field ) {
		return $this->is_field_type_of( $field, self::FIELD_TYPE_MULTISELECT );
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'flexible_checkout_fields_fields', array( $this, 'add_multi_select_to_fields' ) );
		add_filter( 'woocommerce_form_field_' . self::FIELD_TYPE_MULTISELECT, array( $this, 'form_field' ), 10, 4 );
		add_action( 'flexible_checkout_fields_checkout_update_order_meta', array( $this, 'save_order_fields' ) );
		add_filter( 'flexible_checkout_fields_print_value', array( $this, 'field_print_value' ), 10, 2 );
		add_filter( 'flexible_checkout_fields_user_meta_display_value', array( $this, 'field_print_value' ), 10, 2 );
		$settings = $this->checkout_fields_pro->get_settings();
		foreach ( $settings as $section ) {
			if ( is_array( $section ) ) {
				foreach ( $section as $key => $field ) {
					if ( $this->is_multiselect( $field ) ) {
						add_filter( 'woocommerce_process_myaccount_field_' . $key, array( $this, 'save_my_account_field' ) );
						add_filter( 'woocommerce_process_admin_order_field_' . $key, array( $this, 'save_admin_order_field' ) );
					}
				}
			}
		}
		add_filter( 'woocommerce_admin_order_field_' . self::FIELD_TYPE_MULTISELECT, array( $this, 'admin_order_field' ), 10, 4 );
	}

	/**
	 * Add multi select field to fields.
	 *
	 * @param array $fields Fields.
	 *
	 * @return array
	 */
	public function add_multi_select_to_fields( array $fields ) {
		$fields[ self::FIELD_TYPE_MULTISELECT ] = array(
			'name'                => __( 'Multi-select', 'flexible-checkout-fields-pro' ),
			'disable_placeholder' => true,
			'has_options'         => true,
		);
		return $fields;
	}

	/**
	 * Display field od checkout form.
	 *
	 * @param string $no_parameter No parameter.
	 * @param string $key Key.
	 * @param array  $args Args.
	 * @param string $value Value.
	 *
	 * @return string
	 */
	public function form_field( $no_parameter, $key, $args, $value ) {
		$args['name']  = $args['id'] . '[]';
		$template_args = array(
			'field' => $args,
			'key'   => $key,
			'value' => $value,
		);
		return $this->renderer->render( 'fields/multiselect', $template_args );
	}

	/**
	 * Save order field.
	 *
	 * @param int $order_id Order id.
	 */
	public function save_order_fields( $order_id ) {
		$order    = wc_get_order( $order_id );
		$settings = $this->checkout_fields_pro->get_settings();
		foreach ( $settings as $section => $section_fields ) {
			if ( isset( $section_fields ) && is_array( $section_fields ) ) {
				foreach ( $section_fields as $key => $field ) {
					if ( isset( $field[ self::TYPE ] )
						&& self::FIELD_TYPE_MULTISELECT === $field[ self::TYPE ]
					) {
						$grabber = new Flexible_Checkout_Fields_Pro_Order_Multi_Select_Metabox_Grabber( $key );
						$value   = $grabber->grab();
						$order->update_meta_data( '_' . $key, $value );
						$order->save();
					}
				}
			}
		}
	}

	/**
	 * Field print value.
	 *
	 * @param string $value Value.
	 * @param array  $field Field.
	 *
	 * @return string
	 */
	public function field_print_value( $value, $field ) {
		if ( $this->is_multiselect( $field ) ) {
			if ( ! is_array( $value ) ) {
				$selected_values = json_decode( $value );
				if ( ! is_array( $selected_values ) ) {
					$selected_values = array();
				}
			} else {
				$selected_values = $value;
			}
			$values  = array();
			$options = $this->prepare_options_for_field( $field );
			foreach ( $selected_values as $selected_value ) {
				$values[] = isset( $options[ $selected_value ] ) ? $options[ $selected_value ] : $selected_value;
			}
			$value = implode( ', ', $values );
		}
		return $value;
	}

	/**
	 * Save my account multiselect field.
	 *
	 * @param array $value Value.
	 *
	 * @return false|mixed|string
	 */
	public function save_my_account_field( $value ) {
		if ( is_array( $value ) ) {
			$value = wp_json_encode( $value );
		}
		return $value;
	}

}
