<?php

/**
 * Handles Ajax file upload.
 *
 * Class Flexible_Checkout_Fields_Pro_File_Field_Ajax
 */
class Flexible_Checkout_Fields_Pro_File_Field_Ajax
	implements \WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \WPDesk\PluginBuilder\Plugin\PluginAccess;

	/**
	 * Checkout fields PRO.
	 *
	 * @var Flexible_Checkout_Fields_Pro
	 */
	protected $checkout_fields_pro;

	/**
	 * Flexible_Checkout_Fields_Pro_Field_Type constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro $checkout_fields_pro Checkout fields PRO.
	 */
	public function __construct( $checkout_fields_pro ) {
		$this->checkout_fields_pro = $checkout_fields_pro;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'wp_ajax_cf_upload', array( $this, 'ajax_upload' ) );
		add_action( 'wp_ajax_nopriv_cf_upload', array( $this, 'ajax_upload' ) );
	}

	/**
	 * Is doing tests?
	 *
	 * @return bool
	 */
	private function is_doing_tests() {
		if ( defined( 'DOING_TESTS' ) && true === DOING_TESTS ) {
			return true;
		}
		return false;
	}

	/**
	 * Is doing AJAX?
	 *
	 * @return bool
	 */
	private function is_doing_ajax() {
		if ( defined( 'DOING_AJAX' ) && true === DOING_AJAX ) {
			return true;
		}
		return false;
	}

	/**
	 * Handles file upload.
	 */
	public function ajax_upload() {
		$settings = $this->checkout_fields_pro->get_settings();
		$ret      = array(
			'status'  => 'error',
			'message' => '',
		);
		check_ajax_referer( 'inspire_upload_nonce', 'inspire_upload_nonce' );
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		if ( $_FILES ) {
			foreach ( $_FILES as $file => $file_data ) {
				if ( $_FILES[ $file ]['error'] !== UPLOAD_ERR_OK ) {
					$ret['message'] = $_FILES[ $file ]['error'];
					echo wp_json_encode( $ret );
					if ( $this->is_doing_ajax() ) {
						die();
					}
				}
				$checkout_field = false;
				foreach ( $settings as $key => $type ) {
					if ( is_array( $type ) ) {
						foreach ( $type as $field ) {
							if ( isset( $field['name'] ) && $field['name'] === $file ) {
								$checkout_field = $field;
							}
						}
					}
				}
				if ( false === $checkout_field ) {
					// Translators: file name.
					$ret['message'] = sprintf( __( 'There is no field with name %s', 'flexible-checkout-fields-pro' ), $file );
					echo wp_json_encode( $ret );
					if ( $this->is_doing_ajax() ) {
						die();
					}
					return;
				} else {
					if ( is_numeric( $checkout_field['file_size'] ) && $file_data['size'] > intval( $checkout_field['file_size'] ) * 1024 * 1024 ) {
						// Translators: filename.
						$ret['message'] = sprintf( __( 'File %s is to big!', 'flexible-checkout-fields-pro' ), $file_data['name'] );
						echo wp_json_encode( $ret );
						if ( $this->is_doing_ajax() ) {
							die();
						}
						return;
					}
					$allowed_extension = array();
					if ( ! empty( $checkout_field['file_types'] ) ) {
						$allowed_extension = explode( ',', $checkout_field['file_types'] );
					}
					$path_parts = pathinfo( $file_data['name'] );
					if ( ! in_array( $path_parts['extension'], $allowed_extension, true ) ) {
						$ret['message'] = sprintf(
							// Translators: file type and file name.
							__( 'Not allowed file type %1$s for file %2$s', 'flexible-checkout-fields-pro' ),
							$path_parts['extension'],
							$file_data['name']
						);
						echo wp_json_encode( $ret );
						if ( $this->is_doing_ajax() ) {
							die();
						}
						return;
					}
				}
				$upload_dir = new Flexible_Checkout_Fields_Pro_File_Upload_Dir();
				$upload_dir->add_filter();
				add_filter( 'intermediate_image_sizes_advanced', array( $this, 'intermediate_image_sizes_advanced' ) );
				if ( $this->is_doing_ajax() ) {
					$overrides = array( 'test_form' => false );
				} else {
					$_POST['action'] = 'fcf_upload_test';
					$overrides       = array(
						'test_form' => true,
						'action'    => 'fcf_upload_test',
					);
				}
				$attach_id = media_handle_upload( $file, 0, array(), $overrides );
				remove_filter( 'intermediate_image_sizes_advanced', array( $this, 'intermediate_image_sizes_advanced' ) );
				$upload_dir->remove_filter();
				if ( is_wp_error( $attach_id ) ) {
					$ret['message'] = $attach_id->get_error_message();
					echo wp_json_encode( $ret );
					if ( $this->is_doing_ajax() ) {
						die();
					}
					return;
				}
				$file_name = new Flexible_Checkout_Fields_Pro_File_File_Name( $attach_id );
				$file_name->update_file_name( $file_data['name'] );
				$session_data          = WC()->session->get( 'checkout-fields', array() );
				$session_data[ $file ] = $attach_id;
				WC()->session->set( 'checkout-fields', $session_data );

				$ret['status']  = 'ok';
				$ret['message'] = __( 'File uploaded', 'flexible-checkout-fields-pro' );

			}
		}

		echo wp_json_encode( $ret );

		if ( $this->is_doing_ajax() ) {
			die();
		}
	}

	/**
	 * Filter intermediate_image_sizes_advanced.
	 *
	 * @param array $sizes Sizes.
	 *
	 * @return array
	 */
	public function intermediate_image_sizes_advanced( $sizes ) {
		return array();
	}


}
