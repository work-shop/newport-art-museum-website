<?php

/**
 * Handles hooks for file field type.
 *
 * Class Flexible_Checkout_Fields_Pro_File_Field_Type
 */
class Flexible_Checkout_Fields_Pro_File_Field_Type extends Flexible_Checkout_Fields_Pro_Field_Type {

	const FIELD_TYPE_FILE = 'file';

	const META_CHECKOUT_FIELDS_FIELD_FILE = '_checkout_fields_field_file';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'woocommerce_form_field_file', array( $this, 'form_field' ), 10, 4 );
		add_filter( 'flexible_checkout_fields_print_value', array( $this, 'field_print_value' ), 10, 2 );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta_on_checkout' ), 10, 2 );
		add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'update_user_meta_on_checkout' ), 10, 2 );
	}

	/**
	 * Is file field?
	 *
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	private function is_file( array $field ) {
		return $this->is_field_type_of( $field, self::FIELD_TYPE_FILE );
	}

	/**
	 * Field print value.
	 *
	 * @param string $value Value.
	 * @param array  $field Field.
	 *
	 * @return string
	 */
	public function field_print_value( $value, $field ) {
		if ( $this->is_file( $field ) ) {
			$attachment_file = get_attached_file( $value );
			if ( $attachment_file ) {
				$file_name = new Flexible_Checkout_Fields_Pro_File_File_Name( $value );
				$value     = $file_name->get_file_name( basename( $attachment_file ) );
			}
		}
		return $value;
	}

	/**
	 * Get field type definition.
	 *
	 * @return array
	 */
	public function get_field_type_definition() {
		$upload_dir = new Flexible_Checkout_Fields_Pro_File_Upload_Dir();
		$upload_dir->add_filter();
		$wp_upload_dir = wp_upload_dir();
		$upload_dir->remove_filter();
		$upload_folder = substr( $wp_upload_dir['path'], strlen( ABSPATH ) );
		return array(
			'name'                => __( 'File Upload', 'flexible-checkout-fields-pro' ),
			// Translators: upload folder.
			'description'         => sprintf( __( 'Files will be saved to: %s', 'flexible-checkout-fields-pro' ), '<br/>' . $upload_folder ),
			'disable_placeholder' => true,
			'exclude_in_admin'    => true,
			'exclude_for_user'    => true,
		);
	}

	/**
	 * Display field od checkout form.
	 *
	 * @param string $no_parameter No parameter.
	 * @param string $key Key.
	 * @param array  $args Args.
	 * @param string $value Value.
	 *
	 * @return string
	 */
	public function form_field( $no_parameter, $key, $args, $value ) {
		$value         = '';
		$template_args = array(
			'args'  => $args,
			'key'   => $key,
			'value' => $value,
		);
		return $this->renderer->render( 'fields/file', $template_args );
	}

	/**
	 * Maybe update order meta for field.
	 *
	 * @param array    $field Field.
	 * @param array    $data Data.
	 * @param array    $session_data Session data.
	 * @param WC_Order $order Order.
	 * @return array
	 */
	private function maybe_update_order_meta_for_field( array $field, array $data, array $session_data, $order ) {
		if ( $this->is_file( $field ) ) {
			$name = 'name';
			if ( isset( $field[ $name ] ) && isset( $data[ $field[ $name ] ] ) && isset( $session_data[ $field[ $name ] ] ) ) {
				$attachment              = get_post( $session_data[ $field['name'] ] );
				$attachment->post_parent = $order->get_id();
				wp_update_post( $attachment );
				$order->update_meta_data( '_' . $field['name'], $attachment->ID );
				update_post_meta( $attachment->ID, self::META_CHECKOUT_FIELDS_FIELD_FILE, $field );
				unset( $session_data[ $field['name'] ] );
				$order->save();
			}
		}
		return $session_data;
	}

	/**
	 * Update order meta on checkout.
	 *
	 * @param int   $order_id Order id.
	 * @param array $data Posted data.
	 */
	public function update_order_meta_on_checkout( $order_id, $data ) {
		$order        = wc_get_order( $order_id );
		$settings     = $this->checkout_fields_pro->get_settings();
		$session_data = WC()->session->get( 'checkout-fields', array() );
		foreach ( $settings as $key => $section_fields ) {
			if ( is_array( $section_fields ) ) {
				foreach ( $section_fields as $field ) {
					$session_data = $this->maybe_update_order_meta_for_field( $field, $data, $session_data, $order );
				}
			}
		}
		WC()->session->set( 'checkout-fields', $session_data );
	}

	/**
	 * Maybe delete customer meta for field.
	 *
	 * @param array       $field Field.
	 * @param WC_Customer $customer Customer.
	 */
	private function maybe_delete_customer_meta_for_field( array $field, WC_Customer $customer ) {
		if ( $this->is_file( $field ) ) {
			$customer->delete_meta_data( $field['name'] );
			$customer->save();
		}
	}

	/**
	 * Update user meta on checkout.
	 *
	 * @param int   $user_id Customer id.
	 * @param array $data Posted data.
	 */
	public function update_user_meta_on_checkout( $user_id, $data ) {
		if ( $user_id ) {
			try {
				$customer = new WC_Customer( $user_id );
				$settings = $this->checkout_fields_pro->get_settings();
				foreach ( $settings as $key => $section_fields ) {
					if ( is_array( $section_fields ) ) {
						foreach ( $section_fields as $field ) {
							$this->maybe_delete_customer_meta_for_field( $field, $customer );
						}
					}
				}
			} catch ( Exception $e ) {
				// Do nothing.
			}
		}
	}

}
