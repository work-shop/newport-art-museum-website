<?php

/**
 * Handles hooks for inspireradio field type.
 *
 * Class Flexible_Checkout_Fields_Pro_Inspire_Radio_Field_Type
 */
class Flexible_Checkout_Fields_Pro_Inspire_Radio_Field_Type extends Flexible_Checkout_Fields_Pro_Field_Type {

	const FIELD_TYPE_INSPIRERADIO = 'inspireradio';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'flexible_checkout_fields_print_value', array( $this, 'field_print_value' ), 10, 2 );
		add_filter( 'woocommerce_form_field_inspireradio', array( $this, 'form_field' ), 10, 4 );
	}

	/**
	 * Is radio field?
	 *
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	private function is_inspireradio( array $field ) {
		return $this->is_field_type_of( $field, self::FIELD_TYPE_INSPIRERADIO );
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
		if ( $this->is_inspireradio( $field ) ) {
			$options = $this->prepare_options_for_field( $field );
			if ( isset( $options[ $value ] ) ) {
				$value = $options[ $value ];
			}
		}
		return $value;
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
		$template_args = array(
			'args'  => $args,
			'key'   => $key,
			'value' => $value,
		);
		return $this->renderer->render( 'fields/inspireradio', $template_args );
	}

}
