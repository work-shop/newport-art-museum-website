<?php

/**
 * Class Flexible_Checkout_Fields_Pro_File_File_Name
 */
class Flexible_Checkout_Fields_Pro_File_File_Name {

	const META_FILE_NAME = '_fcf_file_name';

	/**
	 * Attachment ID.
	 *
	 * @var int
	 */
	private $attachment_id;

	/**
	 * Flexible_Checkout_Fields_Pro_File_File_Name constructor.
	 *
	 * @param int $attachment_id Attachment ID.
	 */
	public function __construct( $attachment_id ) {
		$this->attachment_id = $attachment_id;
	}

	/**
	 * Update file name.
	 *
	 * @param string $file_name File name.
	 */
	public function update_file_name( $file_name ) {
		update_post_meta( $this->attachment_id, self::META_FILE_NAME, $file_name );
	}

	/**
	 * Get file name.
	 *
	 * @param string $default_name Default name.
	 *
	 * @return string
	 */
	public function get_file_name( $default_name ) {
		$original_file_name = get_post_meta( $this->attachment_id, self::META_FILE_NAME, true );
		if ( '' !== $original_file_name ) {
			return $original_file_name;
		}
		return $default_name;
	}

}
