<?php

/**
 * Handles hooks for multi-select field type.
 *
 * Class Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type
 */
class Flexible_Checkout_Fields_Pro_Select_Field_Type extends Flexible_Checkout_Fields_Pro_Field_Type {

	const FIELD_TYPE_SELECT = 'select';

	/**
	 * Is select field?
	 *
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	private function is_select( array $field ) {
		return $this->is_field_type_of( $field, self::FIELD_TYPE_SELECT );
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'flexible_checkout_fields_print_value', array( $this, 'field_print_value' ), 10, 2 );
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
		if ( $this->is_select( $field ) ) {
			$options = $this->prepare_options_for_field( $field );
			if ( isset( $options[ $value ] ) ) {
				$value = $options[ $value ];
			}
		}
		return $value;
	}

}
