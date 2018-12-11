<?php

/**
 * Renders multi-select field in metabox.
 *
 * Class Flexible_Checkout_Fields_Pro_Order_Multi_Select_Metabox_Renderer
 */
class Flexible_Checkout_Fields_Pro_Order_Multi_Select_Metabox_Renderer {

	const KEY_VALUE = 'value';

	/**
	 * Field.
	 *
	 * @var array
	 */
	private $field;

	/**
	 * Flexible_Checkout_Fields_Pro_Order_Multi_Select_Metabox_Renderer constructor.
	 *
	 * @param array $field Field.
	 */
	public function __construct( array $field ) {
		$this->field = $field;
	}

	/**
	 * Render field.
	 */
	public function render() {
		$field = $this->field;
		if ( ! empty( $field[ self::KEY_VALUE ] ) ) {
			$field_value = $field[ self::KEY_VALUE ];
			if ( is_array( $field_value ) ) {
				$field_values = $field_value;
			} else {
				$field_values = json_decode( $field_value );
			}
		} else {
			$field_values = array();
		}
		include 'views/order-field.php';
	}


}
