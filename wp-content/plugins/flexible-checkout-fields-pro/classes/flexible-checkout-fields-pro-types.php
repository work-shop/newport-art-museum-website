<?php

/**
 * Class Flexible_Checkout_Fields_Pro_Types
 */
class Flexible_Checkout_Fields_Pro_Types {

	/**
	 * Plugin.
	 *
	 * @var Flexible_Checkout_Fields_Pro_Plugin
	 */
	private $plugin;

	/**
	 * Flexible_Checkout_Fields_Pro_Types constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro_Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;

		add_filter( 'woocommerce_form_field_datepicker', array( $this, 'inspire_checkout_field_datepicker' ), 999, 4 );
		add_filter( 'woocommerce_form_field_colorpicker', array( $this, 'inspire_checkout_field_colorpicker' ), 999, 4 );
		add_filter( 'woocommerce_form_field_timepicker', array( $this, 'inspire_checkout_field_timepicker' ), 999, 4 );
		add_filter( 'woocommerce_form_field_heading', array( $this, 'inspire_checkout_field_heading' ), 999, 4 );
		add_filter( 'woocommerce_form_field_info', array( $this, 'inspire_checkout_field_info' ), 999, 4 );
		add_filter( 'woocommerce_form_field_inspirecheckbox', array( $this, 'inspire_checkout_field_inspirecheckbox' ), 999, 4 );

	}

	public function get_settings() {
		$default = array();
		$settings = get_option('inspire_checkout_fields_settings', $default );
		return $settings;
	}

	public function inspire_checkout_field_datepicker( $no_parameter, $key, $args, $value ) {
        $template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
	    return $this->plugin->load_template( 'datepicker', 'fields', $template_args );
	}

	public function inspire_checkout_field_colorpicker( $no_parameter, $key, $args, $value ) {
		$template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
		return $this->plugin->load_template( 'colorpicker', 'fields', $template_args );
	}

	public function inspire_checkout_field_timepicker( $no_parameter, $key, $args, $value ) {
		$template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
		return $this->plugin->load_template( 'timepicker', 'fields', $template_args );
	}

	public function inspire_checkout_field_inspirecheckbox( $no_parameter, $key, $args, $value ) {
		$template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
		return $this->plugin->load_template( 'inspirecheckbox', 'fields', $template_args );
	}

	public function inspire_checkout_field_heading($no_parameter, $key, $args, $value) {
		$template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
		return $this->plugin->load_template( 'heading', 'fields', $template_args );
	}

	public function inspire_checkout_field_info($no_parameter, $key, $args, $value) {
		$template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
		return $this->plugin->load_template( 'info', 'fields', $template_args );
	}


}
