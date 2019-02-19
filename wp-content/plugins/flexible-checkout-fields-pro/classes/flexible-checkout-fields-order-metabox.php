<?php

/**
 * Handles order metabox for custom checkout fields.
 *
 * Class Flexible_Checkout_Fields_Order_Metabox
 */
class Flexible_Checkout_Fields_Order_Metabox implements \WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \WPDesk\PluginBuilder\Plugin\PluginAccess;

	const NONCE_ACTION = 'fcf_pro_metabox';
	const NONCE_NAME   = 'fcf_pro_metabox';

	/**
	 * Plugin.
	 *
	 * @var Flexible_Checkout_Fields_Pro_Plugin
	 */
	private $plugin;

	/**
	 * Checkout fields PRO.
	 *
	 * @var Flexible_Checkout_Fields_Pro
	 */
	private $checkout_fields_pro;

	/**
	 * Flexible checkout fields plugin.
	 *
	 * @var Flexible_Checkout_Fields_Plugin
	 */
	private $flexible_checkout_fields_plugin;

	/**
	 * Flexible_Checkout_Fields_Order_Metabox constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro    $checkout_fields_pro Checkout fields PRO.
	 * @param Flexible_Checkout_Fields_Plugin $flexible_checkout_fields_plugin Flexible checkout fields plugin.
	 */
	public function __construct( Flexible_Checkout_Fields_Pro $checkout_fields_pro, Flexible_Checkout_Fields_Plugin $flexible_checkout_fields_plugin ) {
		$this->checkout_fields_pro             = $checkout_fields_pro;
		$this->flexible_checkout_fields_plugin = $flexible_checkout_fields_plugin;
	}

	/**
	 * Get supported field types.
	 *
	 * @return array
	 */
	private function get_supported_field_types() {
		$fields = $this->flexible_checkout_fields_plugin->get_fields();

		unset( $fields['file'] );
		unset( $fields['heading'] );
		unset( $fields['info'] );

		$supported_field_types = array_keys( $fields );
		return $supported_field_types;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'add_meta_boxes', array( $this, 'add_order_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox_data' ), 10, 2 );
	}

	/**
	 * Add order metabox.
	 */
	public function add_order_metabox() {
		add_meta_box(
			'checkout_fields_fields_editor',
			__( 'Flexible Checkout Fields', 'flexible-checkout-fields-pro' ),
			array( $this, 'metabox_content' ),
			'shop_order',
			'advanced'
		);
	}

	/**
	 * Is custom field?
	 *
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	private function is_custom_field( array $field ) {
		if ( isset( $field['custom_field'] ) && 1 === intval( $field['custom_field'] ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Has sections custom fields?
	 *
	 * @param string $section Section.
	 * @param array  $fields Fields.
	 *
	 * @return bool
	 */
	private function has_section_custom_fields( $section, array $fields ) {
		if ( isset( $fields[ $section ] ) ) {
			foreach ( $fields[ $section ] as $field ) {
				if ( $this->is_custom_field( $field ) && $this->is_supported_type( $field ) ) {
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * Display metabox content.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function metabox_content( WP_Post $post ) {
		$order = wc_get_order( $post->ID );
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
		$flexible_checkout_fields_plugin = $this->flexible_checkout_fields_plugin;
		$sections                        = $flexible_checkout_fields_plugin->sections;
		$fields                          = $flexible_checkout_fields_plugin->getCheckoutFields( array() );
		foreach ( $sections as $section => $section_data ) {
			$fields_section = $section_data['section'];
			if ( $this->has_section_custom_fields( $fields_section, $fields ) ) {
				$this->section_content( $section_data, $fields[ $fields_section ], $order );
			}
		}
		include 'views/metabox-script.php';
	}

	/**
	 * Section content.
	 *
	 * @param array    $section_data   Section data.
	 * @param array    $section_fields Fields.
	 * @param WC_Order $order Order.
	 */
	private function section_content( array $section_data, array $section_fields, WC_Order $order ) {
		$section_title = $section_data['tab_title'];
		include 'views/metabox-section-header.php';
		foreach ( $section_fields as $field_id => $field ) {
			$this->field_content( $field_id, $field, $order );
		}
		include 'views/metabox-section-footer.php';
	}

	/**
	 * Is supported type?
	 *
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	private function is_supported_type( array $field ) {
		$supported_field_types = $this->get_supported_field_types();
		if ( isset( $field['type'] )
			&& in_array( $field['type'], $supported_field_types, true )
		) {
			return true;
		}
		return false;
	}

	/**
	 * Field content.
	 *
	 * @param string   $field_id Field ID.
	 * @param array    $field Field.
	 * @param WC_Order $order Order.
	 */
	private function field_content( $field_id, array $field, WC_Order $order ) {
		if ( $this->is_custom_field( $field ) && $this->is_supported_type( $field ) ) {
			$field['id']   = '_' . $field_id;
			$field['name'] = '_' . $field_id;
			$value         = $order->get_meta( $field['id'] );
			include 'views/metabox-field.php';
		}
	}

	/**
	 * Is valid request with metabox data.
	 *
	 * @param int    $post_id Post ID.
	 * @param object $post Post.
	 * @return bool
	 */
	private function is_valid_request( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) // input var okay.
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION ) ) { // input var okay.
			return false;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return false;
		}

		if ( empty( $_POST['post_ID'] ) || intval( $_POST['post_ID'] ) !== $post_id ) { // input var okay.
			return false;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
	}


	/**
	 * Save order field data.
	 *
	 * @param WC_Order $order Order.
	 * @param string   $field_id $field id.
	 * @param array    $field Field.
	 */
	private function update_order_field_data( WC_Order $order, $field_id, array $field ) {
		$field_key = '_' . $field_id;
		$value     = '';
		if ( isset( $_POST[ $field_key ] ) ) { // WPCS: CSRF, input var okay.
			if ( Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type::FIELD_TYPE_MULTISELECT === $field['type'] ) {
				$grabber = new Flexible_Checkout_Fields_Pro_Order_Multi_Select_Metabox_Grabber( $field_key );
				$value   = $grabber->grab();
			} else {
				$value = wp_unslash( $_POST[ $field_key ] ); // WPCS: CSRF, input var okay, sanitization.
			}
		}
		$order->update_meta_data( $field_key, $value );
	}

	/**
	 * Save section data.
	 *
	 * @param string   $section Section.
	 * @param array    $fields Fields.
	 * @param WC_Order $order Order.
	 */
	private function save_section_data( $section, array $fields, WC_Order $order ) {
		$section_fields = $fields[ $section ];
		foreach ( $section_fields as $field_id => $field ) {
			if ( $this->is_custom_field( $field ) && $this->is_supported_type( $field ) ) {
				$this->update_order_field_data( $order, $field_id, $field );
			}
		}
	}

	/**
	 * Save metabox post data.
	 *
	 * @param int    $post_id Post ID.
	 * @param object $post Post.
	 * @return bool
	 */
	public function save_metabox_data( $post_id, $post ) {
		if ( $this->is_valid_request( $post_id, $post ) ) {
			$order = wc_get_order( $post_id );

			$flexible_checkout_fields_plugin = $this->flexible_checkout_fields_plugin;
			$sections                        = $flexible_checkout_fields_plugin->sections;
			$fields                          = $flexible_checkout_fields_plugin->getCheckoutFields( array() );
			foreach ( $sections as $section => $section_data ) {
				$fields_section = $section_data['section'];
				if ( isset( $fields[ $fields_section ] ) ) {
					$this->save_section_data( $fields_section, $fields, $order );
				}
			}
			$order->save();
			return true;
		}
		return false;
	}

}
