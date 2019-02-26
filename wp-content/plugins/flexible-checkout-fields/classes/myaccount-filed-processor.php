<?php

/**
 * Class Flexible_Checkout_Fields_Myaccount_Field_Processor
 */
class Flexible_Checkout_Fields_Myaccount_Field_Processor {

	/**
	 * @var Flexible_Checkout_Fields_Plugin
	 */
	protected $plugin;

	/**
	 * Flexible_Checkout_Fields_Myaccount_Field_Processor constructor.
	 *
	 * @param Flexible_Checkout_Fields_Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Is custom field?
	 *
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	private function is_custom_field( $field ) {
		if ( isset( $field['custom_field'] ) && 1 === intval( $field['custom_field'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		$settings = $this->plugin->get_settings();
		foreach ( $settings as $section ) {
			if ( is_array( $section ) ) {
				foreach ( $section as $key => $field ) {
					if ( $this->is_custom_field( $field ) ) {
						add_filter( 'woocommerce_process_myaccount_field_' . $key, array( $this, 'wp_unslash_field_value' ) );
					}
				}
			}
		}
	}

	/**
	 * Do wp_unslash on field.
	 *
	 * @param array|string $value Value.
	 *
	 * @return array|string
	 */
	public function wp_unslash_field_value( $value ) {
		return wp_unslash( $value );
	}

}
