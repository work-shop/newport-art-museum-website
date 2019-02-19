<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Flexible_Checkout_Fields_Pro_Types {

	/**
	 * Flexible_Checkout_Fields_Pro_Types constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro_Plugin $plugin
	 */
	public function __construct( $plugin ) {

		$this->plugin = $plugin;

		add_filter('woocommerce_form_field_datepicker', array( $this, 'inspire_checkout_field_datepicker'), 999, 4);
		add_filter('woocommerce_form_field_colorpicker', array( $this, 'inspire_checkout_field_colorpicker'), 999, 4);
		add_filter('woocommerce_form_field_timepicker', array( $this, 'inspire_checkout_field_timepicker'), 999, 4);
		add_filter('woocommerce_form_field_heading', array( $this, 'inspire_checkout_field_heading'), 999, 4);
		add_filter('woocommerce_form_field_info', array( $this, 'inspire_checkout_field_info'), 999, 4);
		add_filter('woocommerce_form_field_inspirecheckbox', array( $this, 'inspire_checkout_field_inspirecheckbox'), 999, 4);
		add_filter('woocommerce_form_field_inspireradio', array( $this, 'inspire_checkout_field_inspireradio'), 999, 4);

		add_filter( 'woocommerce_form_field_file', array( $this, 'inspire_checkout_field_file'), 999, 4);

		add_action( 'template_redirect', array( $this, 'template_redirect_fields_file' ) );

		add_action( 'woocommerce_checkout_process', array($this, 'inspire_checkout_process_field_file'));
		add_action( 'woocommerce_checkout_update_order_meta', array($this, 'inspire_checkout_update_order_meta_field_file') );
		add_action( 'add_meta_boxes', array($this, 'add_meta_boxes_fields_file' ) );

		add_action( 'wp_ajax_cf_upload', array( $this, 'ajax_upload' ) );
		add_action( 'wp_ajax_nopriv_cf_upload', array( $this, 'ajax_upload' ) );


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

	public function inspire_checkout_field_inspireradio( $no_parameter, $key, $args, $value ) {
		$template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
		return $this->plugin->load_template( 'inspireradio', 'fields', $template_args );
	}

	public function inspire_checkout_field_heading($no_parameter, $key, $args, $value) {
		$template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
		return $this->plugin->load_template( 'heading', 'fields', $template_args );
	}

	public function inspire_checkout_field_info($no_parameter, $key, $args, $value) {
		$template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
		return $this->plugin->load_template( 'info', 'fields', $template_args );
	}

	public function inspire_checkout_field_file( $no_parameter, $key, $args, $value ) {
		$value = '';
		$template_args = array( 'args' => $args, 'key' => $key, 'value' => $value );
		return $this->plugin->load_template( 'file', 'fields', $template_args );
	}

	public function inspire_checkout_process_field_file() {
		$settings = $this->get_settings();
		$session_data = WC()->session->get( 'checkout-fields', array() );
		foreach ($settings as $key => $type) {
			if ( is_array( $type ) ) {
				foreach ($type as $field) {
					if (isset($field['type']) && $field['type'] == 'file') {
						if ($field['required'] == 1 && !isset($session_data[$field['name']])) {
							// TODO: test with all woocommerce versions
							//wc_add_notice(sprintf(__('<strong>%s</strong> is required field.', 'flexible-checkout-fields-pro'), $field['label']), 'error');
						}
					}
				}
			}
		}
	}

	public function inspire_checkout_update_order_meta_field_file( $order_id ) {
		$settings = $this->get_settings();
		$session_data = WC()->session->get( 'checkout-fields', array() );
		foreach ($settings as $key => $type) {
			if ( is_array( $type ) ) {
				foreach ($type as $field) {
					if ( isset( $field['name'] ) && isset( $_POST[$field['name']] ) && isset( $session_data[$field['name']] ) ) {
						$attachment = get_post($session_data[$field['name']]);
						$attachment->post_parent = $order_id;
						wp_update_post($attachment);
						wpdesk_update_order_meta($order_id, '_' . $field['name'], $attachment->ID);
						update_post_meta($attachment->ID, '_checkout_fields_field_file', $field);
					}
				}
			}
		}
		WC()->session->set( 'checkout-fields', array() );
	}


	function add_meta_boxes_fields_file() {
		add_meta_box(
			'inspire_checkout_fields_field_file',
			__( 'Attachments', 'flexible-checkout-fields-pro' ),
			array( $this, 'metabox_field_file' ),
			'shop_order',
			'side'
		);
	}

	function metabox_field_file( $post, $metabox ) {
        $attachments = get_posts( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'posts_per_page' => -1 ) );
		foreach ( $attachments as $attachment ) {
			$field = get_post_meta( $attachment->ID, '_checkout_fields_field_file', true );
			$attachment_file = get_attached_file( $attachment->ID );
			$file_name = basename( $attachment_file );
			if ( $field != '' ) {
				$url = add_query_arg( 'checkout_fields_get', $attachment->ID, site_url() );
				$url = add_query_arg( 'checkout_fields_nonce', wp_create_nonce( 'checkout_fields_nonce' ), $url );
				echo '<p><a target="_blank" href="' . $url . '">' . $field['label'] . ' (' . $file_name . ')' . '</a></p>';
			}
		}
	}

	function template_redirect_fields_file() {
		if ( isset( $_GET['checkout_fields_get'] ) && isset( $_GET['checkout_fields_nonce'] ) ) {
			if ( wp_verify_nonce( $_GET['checkout_fields_nonce'], 'checkout_fields_nonce' ) ) {
				$attachment_id = $_GET['checkout_fields_get'];
				$attachment = get_attached_file( $attachment_id );
				if ( $attachment ) {
					$finfo = new finfo();
					$mime_type = $finfo->file( $attachment, FILEINFO_MIME );
					$file_name = basename( $attachment );

					header("Content-type: ".$mime_type, true, 200);
					header("Content-Disposition: attachment; filename=" . $file_name );
					header("Pragma: no-cache");
					header("Expires: 0");
					readfile( $attachment );
					die();

				}
			}
		}
	}

	function ajax_upload() {
		$settings = $this->get_settings();
		//$ret = array( 'status' => 'error', 'message' => __( 'No files?', 'flexible0checkout-fields-pro' ) );
		$ret = array( 'status' => 'error', 'message' => '' );
		check_ajax_referer( 'inspire_upload_nonce', 'inspire_upload_nonce' );
		//require the needed files
		require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
		require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
		require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
		//then loop over the files that were sent and store them using  media_handle_upload();
		if ( $_FILES ) {
			foreach ( $_FILES as $file => $file_data ) {
				if ( $_FILES[$file]['error'] !== UPLOAD_ERR_OK ) {
					$ret['message'] = $_FILES[$file]['error'];
					echo json_encode( $ret );
					die();
				}
				$checkout_field = false;
				foreach ( $settings as $key => $type ) {
					if ( is_array( $type ) ) {
						foreach ($type as $field) {
							if (isset($field['name']) && $field['name'] == $file) {
								$checkout_field = $field;
							}
						}
					}
				}
				if ( $checkout_field === false ) {
					$ret['message'] = sprintf( __( 'There is no field with name %s', 'flexible-checkout-fields-pro' ), $file );
					echo json_encode( $ret );
					die();
				}
				else {
					if ( is_numeric( $checkout_field['file_size'] ) && $file_data['size'] > intval( $checkout_field['file_size'] ) * 1024 * 1024 ) {
						$ret['message'] = sprintf( __( 'File %s is to big!', 'flexible-checkout-fields-pro' ), $file_data['name'] );
						echo json_encode( $ret );
						die();
					}
					$allowed_extension = array();
					if ( !empty( $checkout_field['file_types'] ) ) {
						$allowed_extension = explode( ',', $checkout_field['file_types'] );
					}
					$path_parts = pathinfo( $file_data['name'] );
					if ( !in_array( $path_parts['extension'], $allowed_extension ) ) {
						$ret['message'] = sprintf( __( 'Not allowed file type %s for file %s', 'flexible-checkout-fields-pro' ), $path_parts['extension'], $file_data['name'] );
						echo json_encode( $ret );
						die();
					}
				}
				add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
				add_filter( 'intermediate_image_sizes_advanced', array( $this, 'intermediate_image_sizes_advanced' ) );
				$attach_id = media_handle_upload( $file, 0 );
				remove_filter( 'intermediate_image_sizes_advanced', array( $this, 'intermediate_image_sizes_advanced' ) );
				remove_filter( 'upload_dir', array( $this, 'upload_dir' ) );
				if ( is_wp_error( $attach_id ) ) {
					$ret['message'] = $attach_id->get_error_message();
					echo json_encode( $ret );
					die();
				}
				$session_data = WC()->session->get( 'checkout-fields', array() );
				$session_data[$file] = $attach_id;
				WC()->session->set( 'checkout-fields', $session_data );

				$ret['status'] = 'ok';
				$ret['message'] = __( 'File uploaded', 'flexible-checkout-fields-pro' );

			}
		}

		echo json_encode( $ret );

		die();
	}

	function upload_dir( $path ) {
		$subdir = '/woocommerce_uploads/checkout_fields/files';
		$path['subdir'] = $subdir;
		$path['path'] = $path['basedir'] . $subdir;
		$path['url'] = $path['baseurl'] . $subdir;
		return $path;
	}

	function intermediate_image_sizes_advanced( $sizes ) {
		return array();
	}

}
