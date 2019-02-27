<?php

/**
 * Handles hooks for HTML field type.
 *
 * Class Flexible_Checkout_Fields_Pro_HTML_Field_Type
 */
class Flexible_Checkout_Fields_Pro_HTML_Field_Type extends Flexible_Checkout_Fields_Pro_Field_Type {

	const FIELD_TYPE_HTML = 'info';

	/**
	 * Is html field?
	 *
	 * @param array $args .
	 *
	 * @return bool
	 */
	private function is_html( array $args ) {
		return $this->is_field_type_of( $args, self::FIELD_TYPE_HTML );
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'woocommerce_form_field', array( $this, 'woocommerce_form_field_info' ), 10, 3 );
	}

	/**
	 * Change field wrapper from <p> to </div>
	 *
	 * @param string $field HTML Output.
	 * @param string $key   Field key.
	 * @param array  $args  Field arguments.
	 *
	 * @return string
	 */
	public function woocommerce_form_field_info( $field, $key, array $args ) {
		if ( $this->is_html( $args ) ) {
			$field = preg_replace( '/<\s*?p\b[^>]*>(.*?)<\/p\b[^>]*>\s*?$/s', '<div class="form-row">$1</div>', $field, 1 );
		}
		return $field;
	}

}