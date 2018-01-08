<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WCCPT_Admin_Meta_Boxes.
 */
class WCCPT_Admin_Meta_Boxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors  = array();
	
	private $cpt;
	
	/**
	 * Constructor.
	 */
	public function __construct() {
		
		add_action( 'current_screen', array( $this, 'current_screen' ) );
		
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );


		// Save Product Meta Boxes
		add_action( 'woocommerce_process_product_meta', 'WC_Meta_Box_Product_Data::save', 10, 2 );
		add_action( 'woocommerce_process_product_meta', 'WC_Meta_Box_Product_Images::save', 20, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}
	
	public function current_screen( $current_screen ) {
		$this->cpt = WC_CPT_List::get( $current_screen->post_type );
	}
	
	/**
	 * Add an error message.
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'woocommerce_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'woocommerce_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="woocommerce_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'woocommerce_meta_box_errors' );
		}
	}

	/**
	 * Add WC Meta boxes.
	 */
	public function add_meta_boxes() {
		$screen = get_current_screen();
		
		if ( empty( $this->cpt ) || ! WC_CPT_List::is_active( $this->cpt ) ) return;
		
		$post_type = get_post_type_object( $screen->id );
		$label = $post_type->labels->singular_name;
		
		// Products
		add_meta_box( 'postexcerpt', __( $label . ' Short Description', 'woocommerce' ), 'WC_Meta_Box_Product_Short_Description::output', $this->cpt, 'normal' );
		add_meta_box( 'woocommerce-product-data', __( $label . ' Data', 'woocommerce' ), 'WC_Meta_Box_Product_Data::output', $this->cpt, 'normal', 'high' );
		add_meta_box( 'woocommerce-product-images', __( $label . ' Gallery', 'woocommerce' ), 'WC_Meta_Box_Product_Images::output', $this->cpt, 'side', 'low' );

		// Reviews
		if ( $screen && 'comment' === $screen->id && isset( $_GET['c'] ) ) {
			if ( get_comment_meta( intval( $_GET['c'] ), 'rating', true ) ) {
				add_meta_box( 'woocommerce-rating', __( 'Rating', 'woocommerce' ), 'WC_Meta_Box_Order_Reviews::output', 'comment', 'normal', 'high' );
			}
		}
	}

	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		
		if ( empty( $this->cpt ) || ! WC_CPT_List::is_active( $this->cpt ) ) return;
		
		remove_meta_box( 'postexcerpt', $this->cpt, 'normal' );
		remove_meta_box( 'product_shipping_classdiv', $this->cpt, 'side' );
		remove_meta_box( 'pageparentdiv', $this->cpt, 'side' );
		remove_meta_box( 'commentstatusdiv', $this->cpt, 'normal' );
		remove_meta_box( 'commentstatusdiv', $this->cpt, 'side' );
	}

	/**
	 * Rename core meta boxes.
	 */
	public function rename_meta_boxes() {
		global $post;

		if ( empty( $this->cpt ) || ! WC_CPT_List::is_active( $this->cpt ) ) return;
		
		// Comments/Reviews
		if ( isset( $post ) && ( 'publish' == $post->post_status || 'private' == $post->post_status ) ) {
			remove_meta_box( 'commentsdiv', $this->cpt, 'normal' );

			add_meta_box( 'commentsdiv', __( 'Reviews', 'woocommerce' ), 'post_comment_meta_box', $this->cpt, 'normal' );
		}
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		//	remove_action( current_filter(), __METHOD__ );
		// But cannot be used due to https://github.com/woothemes/woocommerce/issues/6485
		// When that is patched in core we can use the above. For now:
		self::$saved_meta_boxes = true;

		// Check the post type
		if ( in_array( $post->post_type, array( $this->cpt ) ) ) {
			do_action( 'woocommerce_process_product_meta', $post_id, $post );
		}
	}

}

new WCCPT_Admin_Meta_Boxes();
