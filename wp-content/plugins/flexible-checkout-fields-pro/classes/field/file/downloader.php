<?php

/**
 * Handles file download.
 *
 * Class Flexible_Checkout_Fields_Pro_File_Field_Downloader
 */
class Flexible_Checkout_Fields_Pro_File_Field_Downloader
	implements \WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \WPDesk\PluginBuilder\Plugin\PluginAccess;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'template_redirect', array( $this, 'download_attachment_file' ) );
	}

	/**
	 * Handle download attachment request.
	 */
	public function download_attachment_file() {
		$nonce_name = Flexible_Checkout_Fields_Pro_File_Field_Order_Metabox::CHECKOUT_FIELDS_NONCE;
		if ( isset( $_GET['checkout_fields_get'] ) && isset( $_GET[ $nonce_name ] ) ) {
			if ( wp_verify_nonce( $_GET[ $nonce_name ], $nonce_name ) ) {
				$attachment_id = $_GET['checkout_fields_get'];
				$attachment    = get_attached_file( $attachment_id );
				if ( $attachment ) {
					$finfo     = new finfo();
					$mime_type = $finfo->file( $attachment, FILEINFO_MIME );

					$file_file_name = new Flexible_Checkout_Fields_Pro_File_File_Name( $attachment_id );
					$file_name      = $file_file_name->get_file_name( basename( $attachment ) );


					header( 'Content-type: ' . $mime_type, true, 200 );
					header( 'Content-Disposition: attachment; filename=' . $file_name );
					header( 'Pragma: no-cache' );
					header( 'Expires: 0' );
					readfile( $attachment );
					die();
				}
			}
		}
	}


}
