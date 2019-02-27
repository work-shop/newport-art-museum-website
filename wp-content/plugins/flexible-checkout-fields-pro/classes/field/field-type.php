<?php

/**
 * Class Flexible_Checkout_Fields_Pro_Field_Type
 */
class Flexible_Checkout_Fields_Pro_Field_Type
	implements \WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \WPDesk\PluginBuilder\Plugin\PluginAccess;

	const TYPE    = 'type';
	const OPTIONS = 'options';

	const ALLOWED_HTML_TAGS_IN_OPTION = '<img><a><strong><em><br>';

	/**
	 * Renderer.
	 *
	 * @var WPDesk\View\Renderer\Renderer
	 */
	protected $renderer;

	/**
	 * Checkout fields PRO.
	 *
	 * @var Flexible_Checkout_Fields_Pro
	 */
	protected $checkout_fields_pro;

	/**
	 * Flexible_Checkout_Fields_Pro_Field_Type constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro   $checkout_fields_pro Checkout fields PRO.
	 * @param \WPDesk\View\Renderer\Renderer $renderer Renderer.
	 */
	public function __construct( $checkout_fields_pro, WPDesk\View\Renderer\Renderer $renderer ) {
		$this->renderer            = $renderer;
		$this->checkout_fields_pro = $checkout_fields_pro;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
	}

	/**
	 * Is field type of?
	 *
	 * @param array  $field Field.
	 * @param string $type Type.
	 * @return bool
	 */
	protected function is_field_type_of( array $field, $type ) {
		if ( isset( $field[ self::TYPE ] ) && $type === $field[ self::TYPE ] ) {
			return true;
		}
		return false;
	}

	/**
	 * Prepare options for field.
	 * Prepares options array from option string.
	 *
	 * @param array $field Field.
	 *
	 * @return array
	 */
	protected function prepare_options_for_field( array $field ) {
		$options = array();
		if ( class_exists( 'Flexible_Checkout_Fields_Field_Options' ) ) {
			$field_options = new Flexible_Checkout_Fields_Field_Options( $field['option'] );
			$options       = $field_options->get_options_as_array();
		} else {
			// Only for compability with older FCF Free plugin.
			$options_row = explode( "\n", $field['option'] );
			foreach ( $options_row as $option ) {
				$tmp          = explode( ':', $option, 2 );
				$option_value = trim( $tmp[0] );
				if ( isset( $tmp[1] ) ) {
					$option_label = wp_unslash( strip_tags( $tmp[1], self::ALLOWED_HTML_TAGS_IN_OPTION ) );
				} else {
					$option_label = $option_value;
				}
				$options[ $option_value ] = wpdesk__( $option_label, 'flexible-checkout-fields' );
				unset( $tmp );
			}
			unset( $options_array );
		}
		return $options;
	}


}
