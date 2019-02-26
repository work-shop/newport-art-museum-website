<?php

/**
 * User profile hooks.
 *
 * Class Flexible_Checkout_Fields_User_Profile
 */
class Flexible_Checkout_Fields_User_Profile {

	const FIELD_TYPE        = 'type';
	const FIELD_TYPE_SELECT = 'select';

	/**
	 * Plugin.
	 *
	 * @var Flexible_Checkout_Fields_Plugin
	 */
	protected $plugin;

	/**
	 * Flexible_Checkout_Fields_User_Profile constructor.
	 *
	 * @param Flexible_Checkout_Fields_Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'woocommerce_customer_meta_fields', array( $this, 'add_customer_meta_fields' ) );

		add_action( 'show_user_profile', array( $this, 'add_custom_user_fields_admin' ), 75 );
		add_action( 'edit_user_profile', array( $this, 'add_custom_user_fields_admin' ), 75 );

		add_action( 'personal_options_update', array( $this, 'save_custom_user_fields_admin' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_custom_user_fields_admin' ) );

	}

	/**
	 * Prepare fields.
	 *
	 * @param array $fields Fields.
	 *
	 * @return array
	 */
	private function prepare_fields( array $fields ) {
		foreach ( $fields as $key => $field ) {
			$fcf_field = new Flexible_Checkout_Fields_Field( $field, $this->plugin );
			if ( $fcf_field->is_field_excluded_for_user() ) {
				unset( $fields[ $key ] );
			} else {
				$field['class']       = '';
				$field['description'] = '';
				if ( empty( $field[ self::FIELD_TYPE ] ) ) {
					$field[ self::FIELD_TYPE ] = 'text';
				}
				if ( 'inspirecheckbox' === $field[ self::FIELD_TYPE ] ) {
					$field['class']            = self::FIELD_TYPE_SELECT;
					$field[ self::FIELD_TYPE ] = 'checkbox';
				}
				if ( 'inspireradio' === $field[ self::FIELD_TYPE ] ) {
					$field[ self::FIELD_TYPE ] = self::FIELD_TYPE_SELECT;
					$field['class']            = self::FIELD_TYPE_SELECT;
				}
				$fields[ $key ] = $field;
			}
		}
		return $fields;
	}

	/**
	 * Add customer billing and shipping fields.
	 *
	 * @param array $fields Fields.
	 *
	 * @return array mixed
	 */
	public function add_customer_meta_fields( array $fields ) {
		$fields['billing']['fields']  = $this->prepare_fields( WC()->countries->get_address_fields( '', 'billing_' ) );
		$fields['shipping']['fields'] = $this->prepare_fields( WC()->countries->get_address_fields( '', 'shipping_' ) );
		return $fields;
	}


	/**
	 * add custom fields to edit user admin /wp-admin/profile.php
	 *
	 * @access public
	 * @param mixed $user
	 * @return void
	 */
	public function add_custom_user_fields_admin( $user ) {
		$settings = $this->plugin->get_settings();
		$sections = $this->plugin->sections;
		if ( !empty($settings ) ) {
			foreach ( $settings as $key => $type ) {
				if ( in_array( $key, array( 'shipping', 'billing' ) ) ) {
					continue;
				}
				if ( empty( $sections[ 'woocommerce_checkout_' . $key] ) ) {
					continue;
				}
				if ( is_array( $type ) ) {
					foreach ( $type as $field ) {
						if ( isset( $field['visible'] ) && $field['visible'] == 0 && ( isset( $field['custom_field'] ) && $field['custom_field'] == 1 ) ) {
							$return = false;

							$return = apply_filters( 'flexible_checkout_fields_user_fields', $return, $field, $user );

							if ( $return === false ) {

								switch ( $field[ self::FIELD_TYPE ] ) {
									case 'textarea':
										$fields[] = '
		                                        <tr>
		                                            <th><label for="' . $field['name'] . '">' . $field['label'] . '</label></th>
		                                            <td>
		                                                <textarea name="' . $field['name'] . '" id="' . $field['name'] . '" class="regular-text" rows="5" cols="30">' . esc_attr( get_the_author_meta( $field['name'], $user->ID ) ) . '</textarea><br /><span class="description"></span>
		                                            </td>
		                                        </tr>
		                                    ';
										break;

									default:
										$fields[] = '
		                                        <tr>
		                                            <th><label for="' . $field['name'] . '">' . $field['label'] . '</label></th>
		                                            <td>
		                                                <input type="text" name="' . $field['name'] . '" id="' . $field['name'] . '" value="' . esc_attr( get_the_author_meta( $field['name'], $user->ID ) ) . '" class="regular-text" /><br /><span class="description"></span>
		                                            </td>
		                                        </tr>
		                                    ';
										break;
								}
							} else {
								if ( $return != '' ) {
									$fields[] = $return;
								}
							}
						}
					}
				}
			}
			if ( isset( $fields ) ) {
				echo '<h3>' . __( 'Additional Information', 'flexible-checkout-fields' ) . '</h3>';
				echo '<table class="form-table">';
				echo implode( '', $fields );
				echo '</table>';
			}
		}
	}

	/**
	 * Save custom user fields in admin.
	 *
	 * @param int $user_id User ID.
	 */
	public function save_custom_user_fields_admin( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}
		if ( wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
			$settings    = $this->plugin->get_settings();
			$sections    = $this->plugin->sections;
			$field_types = $this->plugin->get_fields();
			if ( ! empty( $settings ) ) {
				foreach ( $settings as $key => $type ) {
					if ( empty( $sections[ 'woocommerce_checkout_' . $key ] ) ) {
						continue;
					}
					foreach ( $type as $field ) {
						$field_name = $field['name'];
						$fcf_field  = new Flexible_Checkout_Fields_Field( $field, $this->plugin );
						if ( ! $fcf_field->is_field_excluded_for_user() ) {
							$value = '';
							if ( isset( $_POST[ $field_name ] ) ) {
								$value = wp_unslash( $_POST[ $field_name ] );
							}
							update_user_meta( $user_id, $field_name, $value );
						}
					}
				}
			}
		}
	}

}
