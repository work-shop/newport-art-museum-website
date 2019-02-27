<?php

/**
 * Filter upload dir.
 *
 * Class Flexible_Checkout_Fields_Pro_File_Upload_Dir
 */
class Flexible_Checkout_Fields_Pro_File_Upload_Dir {

	/**
	 * Add filter.
	 */
	public function add_filter() {
		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
	}

	/**
	 * Remove filter.
	 */
	public function remove_filter() {
		remove_filter( 'upload_dir', array( $this, 'upload_dir' ) );
	}

	/**
	 * Handle upload_dir filter.
	 *
	 * @param string $path Path.
	 *
	 * @return string
	 */
	public function upload_dir( $path ) {
		$subdir         = '/woocommerce_uploads/checkout_fields/files';
		$path['subdir'] = $subdir;
		$path['path']   = $path['basedir'] . $subdir;
		$path['url']    = $path['baseurl'] . $subdir;
		return $path;
	}


}
