<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Flexible_Checkout_Fields_User_Profile {

	protected $plugin;

	/**
	 * Flexible_Checkout_Fields_User_Profile constructor.
	 *
	 * @param inspireCheckoutFields $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	protected function hooks() {
		add_filter( 'woocommerce_customer_meta_fields', array( $this, 'woocommerce_customer_meta_fields' ) );

		add_action( 'show_user_profile', array( $this, 'addCustomUserFieldsAdmin'), 75 );
		add_action( 'edit_user_profile', array( $this, 'addCustomUserFieldsAdmin'), 75 );

		add_action( 'personal_options_update', array( $this, 'saveCustomUserFieldsAdmin') );
		add_action( 'edit_user_profile_update',  array( $this, 'saveCustomUserFieldsAdmin') );

	}

	public function woocommerce_customer_meta_fields( $fields ) {
		$billing_fields = WC()->countries->get_address_fields( '', 'billing_' );
		foreach ( $billing_fields as $key => $field ) {
			if ( empty( $fields['billing']['fields'][$key] ) ) {
				unset( $field['class'] );
				$field['class'] = '';
				$field['description'] = '';
				if ( empty( $field['type'] ) ) {
					$field['type'] = 'text';
				}
				if ( $field['type'] == 'inspirecheckbox' ) {
					$field['type'] = 'checkbox';
					$field['class'] = 'select';
					$field['type'] = 'checkbox';
				}
				if ( $field['type'] == 'inspireradio' ) {
					$field['type'] = 'select';
					$field['class'] = 'select';
					$field['description'] = '';
				}
				$fields['billing']['fields'][$key] = $field;
			}
		}
		$billing_fields = WC()->countries->get_address_fields( '', 'shipping_' );
		foreach ( $billing_fields as $key => $field ) {
			if ( empty( $fields['shipping']['fields'][$key] ) ) {
				unset( $field['class'] );
				$field['class'] = '';
				$field['description'] = '';
				if ( $field['type'] == 'inspirecheckbox' ) {
					$field['type'] = 'checkbox';
					$field['class'] = 'select';
					$field['type'] = 'checkbox';
				}
				if ( $field['type'] == 'inspireradio' ) {
					$field['type'] = 'select';
					$field['class'] = 'select';
					$field['type'] = 'select';
				}
				$fields['shipping']['fields'][$key] = $field;
			}
		}
		return $fields;
	}


	/**
	 * add custom fields to edit user admin /wp-admin/profile.php
	 *
	 * @access public
	 * @param mixed $user
	 * @return void
	 */
	public function addCustomUserFieldsAdmin( $user ) {
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

								switch ( $field['type'] ) {
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

	public function saveCustomUserFieldsAdmin($user_id) {
		if ( !current_user_can( 'edit_user', $user_id ) )
			return false;
		$settings = $this->plugin->get_settings();
		$sections = $this->plugin->sections;
		if (! empty( $settings ) ){
			foreach ( $settings as $key => $type ) {
				if ( empty( $sections[ 'woocommerce_checkout_' . $key] ) ) {
					continue;
				}
				foreach ( $type as $field ) {
					if ( $field['visible'] == 0 and $field['custom_field'] == 1 ){
						update_user_meta( $user_id, $field['name'], $_POST[$field['name']] );
					}
				}
			}
		}

	}

}
