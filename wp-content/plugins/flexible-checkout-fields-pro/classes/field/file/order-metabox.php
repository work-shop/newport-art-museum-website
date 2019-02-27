<?php

/**
 * Order metabox.
 *
 * Class Flexible_Checkout_Fields_Pro_File_Field_Order_Metabox
 */
class Flexible_Checkout_Fields_Pro_File_Field_Order_Metabox
	implements \WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \WPDesk\PluginBuilder\Plugin\PluginAccess;

	const CHECKOUT_FIELDS_NONCE = 'checkout_fields_nonce';

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
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Add metabox.
	 */
	public function add_meta_box() {
		add_meta_box(
			'inspire_checkout_fields_field_file',
			__( 'Attachments', 'flexible-checkout-fields-pro' ),
			array( $this, 'metabox_field_file' ),
			'shop_order',
			'side'
		);
	}

	/**
	 * Metabox handler.
	 *
	 * @param WP_Post $post Post.
	 */
	public function metabox_field_file( $post ) {
		$attachments = get_posts(
			array(
				'post_parent'    => $post->ID,
				'post_type'      => 'attachment',
				'posts_per_page' => -1,
			)
		);
		foreach ( $attachments as $attachment ) {
			$field = get_post_meta( $attachment->ID, Flexible_Checkout_Fields_Pro_File_Field_Type::META_CHECKOUT_FIELDS_FIELD_FILE, true );
			if ( '' !== $field ) {
				$attachment_file = get_attached_file( $attachment->ID );
				$file_file_name  = new Flexible_Checkout_Fields_Pro_File_File_Name( $attachment->ID );
				$file_name       = $file_file_name->get_file_name( basename( $attachment_file ) );

				$url = add_query_arg( 'checkout_fields_get', $attachment->ID, site_url() );
				$url = add_query_arg( self::CHECKOUT_FIELDS_NONCE, wp_create_nonce( self::CHECKOUT_FIELDS_NONCE ), $url );
				echo '<p><a target="_blank" href="' . $url . '">' . $field['label'] . ' (' . $file_name . ')' . '</a></p>';
			}
		}
	}

}
