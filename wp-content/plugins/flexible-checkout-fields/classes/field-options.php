<?php

/**
 * Field options.
 *
 * Class Flexible_Checkout_Fields_Field_Options
 */
class Flexible_Checkout_Fields_Field_Options {

	const ALLOWED_HTML_TAGS_IN_OPTION = '<img><a><strong><em><br>';

	/**
	 * Options in string.
	 *
	 * @var strind
	 */
	private $options_string;

	/**
	 * Flexible_Checkout_Fields_Field_Options constructor.
	 *
	 * @param string $options_string Options in string.
	 */
	public function __construct( $options_string ) {
		$this->options_string = $options_string;
	}

	/**
	 * Get options as array.
	 *
	 * @return array
	 */
	public function get_options_as_array() {
		$options           = array();
		$tmp_options_array = explode( "\n", $this->options_string );
		foreach ( $tmp_options_array as $option_row ) {
			$option_array = explode( ':', $option_row, 2 );
			$option_value = trim( $option_array[0] );
			$option_label = $option_value;
			if ( isset( $option_array[1] ) ) {
				$option_label = wp_unslash( strip_tags( trim( $option_array[1] ), self::ALLOWED_HTML_TAGS_IN_OPTION ) );
			}
			$options[ $option_value ] = wpdesk__( $option_label, 'flexible-checkout-fields' );
			unset( $option_array );
		}
		unset( $tmp_options_array );
		return $options;
	}

}
