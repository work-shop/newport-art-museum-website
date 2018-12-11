<?php

/**
 * Grabs multi-select field from posted data.
 *
 * Class Flexible_Checkout_Fields_Pro_Order_Multi_Select_Metabox_Grabber
 */
class Flexible_Checkout_Fields_Pro_Order_Multi_Select_Metabox_Grabber {

	/**
	 * Field key.
	 *
	 * @var string
	 */
	private $field_key;

	/**
	 * Flexible_Checkout_Fields_Pro_Order_Multi_Select_Metabox_Grabber constructor.
	 *
	 * @param string $field_key Field key.
	 */
	public function __construct( $field_key ) {
		$this->field_key = $field_key;
	}

	/**
	 * Grab field.
	 *
	 * @return string
	 */
	public function grab() {
		if ( isset( $_POST[ $this->field_key ] ) ) {
			return json_encode( wp_unslash( $_POST[ $this->field_key ] ) ); // WPCS: CSRF, input var okay, sanitization.
		}
		return json_encode( array() );
	}


}
