<?php

/**
 * Class Flexible_Checkout_Fields_Field
 */
class Flexible_Checkout_Fields_Field {

	const FIELD_TYPE         = 'type';
	const FIELD_CUSTOM_FIELD = 'custom_field';
	const FIELD_VISIBLE      = 'visible';

	const FIELD_TYPE_EXCLUDE_IN_ADMIN = 'exclude_in_admin';
	const FIELD_TYPE_EXCLUDE_FOR_USER = 'exclude_for_user';

	/**
	 * Field data.
	 *
	 * @var array
	 */
	private $field_data;

	/**
	 * Plugin.
	 *
	 * @var Flexible_Checkout_Fields_Plugin
	 */
	private $plugin;

	/**
	 * Flexible_Checkout_Fields_Field constructor.
	 *
	 * @param array                           $field_data Field data.
	 * @param Flexible_Checkout_Fields_Plugin $plugin Plugin.
	 */
	public function __construct( array $field_data, $plugin ) {
		$this->plugin     = $plugin;
		$this->field_data = $field_data;
	}

	/**
	 * Get field types from plugin.
	 *
	 * @return array
	 */
	private function get_field_types_from_plugin() {
		return $this->plugin->get_fields();
	}

	/**
	 * Get field type settings.
	 *
	 * @return array
	 */
	private function get_field_type_settings() {
		$default_values = array(
			self::FIELD_TYPE_EXCLUDE_IN_ADMIN => false,
			self::FIELD_TYPE_EXCLUDE_FOR_USER => false,
		);
		$field_types    = $this->get_field_types_from_plugin();
		if ( isset( $this->field_data[ self::FIELD_TYPE ] ) && isset( $field_types[ $this->field_data[ self::FIELD_TYPE ] ] ) ) {
			$field_type_settings = $field_types[ $this->field_data[ self::FIELD_TYPE ] ];
			$field_type_settings = array_merge( $default_values, $field_type_settings );
			return $field_type_settings;
		}
		return $default_values;
	}

	/**
	 * Is visible?
	 *
	 * @return bool
	 */
	public function is_custom_field() {
		if ( isset( $this->field_data[ self::FIELD_CUSTOM_FIELD ] ) && 1 === intval( $this->field_data[ self::FIELD_CUSTOM_FIELD ] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Is visible?
	 *
	 * @return bool
	 */
	public function is_visible() {
		if ( isset( $this->field_data[ self::FIELD_VISIBLE ] ) && 0 === intval( $this->field_data[ self::FIELD_VISIBLE ] ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Is field excluded for user?
	 * Field is excluded from user when is custom field and is not visible or field type is excluded for user.
	 *
	 * @return bool
	 */
	public function is_field_excluded_for_user() {
		if ( ! $this->is_custom_field() ) {
			return false;
		}
		$field_type_settings = $this->get_field_type_settings();
		if ( true === $field_type_settings[ self::FIELD_TYPE_EXCLUDE_FOR_USER ] ) {
			return true;
		}
		return false;
	}

	/**
	 * Is field excluded in admin?
	 *
	 * @return bool
	 */
	public function is_field_excluded_in_admin() {
		if ( ! $this->is_custom_field() ) {
			return false;
		}
		$field_type_settings = $this->get_field_type_settings();
		if ( true === $field_type_settings[ self::FIELD_TYPE_EXCLUDE_IN_ADMIN ] ) {
			return true;
		}
		return false;
	}


}
