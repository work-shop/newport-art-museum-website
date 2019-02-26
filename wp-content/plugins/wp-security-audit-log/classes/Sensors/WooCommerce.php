<?php
/**
 * Sensor: WooCommerce
 *
 * WooCommerce sensor file.
 *
 * @package Wsal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Support for WooCommerce Plugin.
 *
 * 9000 User created a new product
 * 9001 User published a product
 * 9002 User created a new product category
 * 9003 User changed the category of a product
 * 9004 User modified the short description of a product
 * 9005 User modified the text of a product
 * 9006 User changed the URL of a product
 * 9007 User changed the Product Data of a product
 * 9008 User changed the date of a product
 * 9009 User changed the visibility of a product
 * 9010 User modified the product
 * 9012 User moved a product to trash
 * 9013 User permanently deleted a product
 * 9014 User restored a product from the trash
 * 9015 User changed status of a product
 * 9016 User changed type of a price
 * 9017 User changed the SKU of a product
 * 9018 User changed the stock status of a product
 * 9019 User changed the stock quantity
 * 9020 User set a product type
 * 9021 User changed the weight of a product
 * 9022 User changed the dimensions of a product
 * 9023 User added the Downloadable File to a product
 * 9024 User Removed the Downloadable File from a product
 * 9025 User changed the name of a Downloadable File in a product
 * 9026 User changed the URL of the Downloadable File in a product
 * 9027 User changed the Weight Unit
 * 9028 User changed the Dimensions Unit
 * 9029 User changed the Base Location
 * 9030 User Enabled/Disabled taxes
 * 9031 User changed the currency
 * 9032 User Enabled/Disabled the use of coupons during checkout
 * 9033 User Enabled/Disabled guest checkout
 *
 * @package Wsal
 * @subpackage Sensors
 */
class WSAL_Sensors_WooCommerce extends WSAL_AbstractSensor {

	/**
	 * Old Post.
	 *
	 * @var WP_Post
	 */
	protected $_old_post = null;

	/**
	 * Old Post Link.
	 *
	 * @var string
	 */
	protected $_old_link = null;

	/**
	 * Old Post Categories.
	 *
	 * @var array
	 */
	protected $_old_cats = null;

	/**
	 * Old Product Data.
	 *
	 * @var array
	 */
	protected $_old_data = null;

	/**
	 * Old Product Stock Quantity.
	 *
	 * @var int
	 */
	protected $_old_stock = null;

	/**
	 * Old Product Stock Status.
	 *
	 * @var string
	 */
	protected $_old_stock_status = null;

	/**
	 * Old Product File Names.
	 *
	 * @var array
	 */
	protected $_old_file_names = array();

	/**
	 * Old Product File URLs.
	 *
	 * @var array
	 */
	protected $_old_file_urls = array();

	/**
	 * Old Attribute Data.
	 *
	 * @since 3.3.1
	 *
	 * @var stdClass
	 */
	private $old_attr_data;

	/**
	 * Coupon Meta Data Keys.
	 *
	 * @since 3.3.1
	 *
	 * @var array
	 */
	private $coupon_meta = array(
		'discount_type',
		'coupon_amount',
		'individual_use',
		'product_ids',
		'exclude_product_ids',
		'usage_limit',
		'usage_limit_per_user',
		'limit_usage_to_x_items',
		'usage_count',
		'date_expires',
		'expiry_date',
		'free_shipping',
		'product_categories',
		'exclude_product_categories',
		'exclude_sale_items',
		'minimum_amount',
		'maximum_amount',
		'customer_email',
	);

	/**
	 * Is Event 9067 Logged?
	 *
	 * @since 3.3.1
	 *
	 * @var boolean
	 */
	private $is_9067_logged = false;

	/**
	 * Is Event 9068 Logged?
	 *
	 * @since 3.3.1
	 *
	 * @var boolean
	 */
	private $is_9068_logged = false;

	/**
	 * Listening to events using WP hooks.
	 */
	public function HookEvents() {
		if ( current_user_can( 'edit_posts' ) ) {
			add_action( 'admin_init', array( $this, 'EventAdminInit' ) );
		}
		add_action( 'post_updated', array( $this, 'EventChanged' ), 10, 3 );
		add_action( 'delete_post', array( $this, 'EventDeleted' ), 10, 1 );
		add_action( 'wp_trash_post', array( $this, 'EventTrashed' ), 10, 1 );
		add_action( 'untrash_post', array( $this, 'EventUntrashed' ) );
		add_action( 'wp_head', array( $this, 'viewing_product' ), 10 );
		add_action( 'create_product_cat', array( $this, 'EventCategoryCreation' ), 10, 1 );
		add_filter( 'post_edit_form_tag', array( $this, 'editing_product' ), 10, 1 );
		add_action( 'woocommerce_order_status_changed', array( $this, 'event_order_status_changed' ), 10, 4 );
		add_action( 'woocommerce_order_refunded', array( $this, 'event_order_refunded' ), 10, 2 );
		add_action( 'woocommerce_bulk_action_ids', array( $this, 'event_bulk_order_actions' ), 10, 2 );
		add_action( 'woocommerce_attribute_added', array( $this, 'event_attribute_added' ), 10, 2 );
		add_action( 'woocommerce_before_attribute_delete', array( $this, 'event_attribute_deleted' ), 10, 3 );
		add_action( 'woocommerce_attribute_updated', array( $this, 'event_attribute_updated' ), 10, 3 );
		add_action( 'wp_update_term_data', array( $this, 'event_product_cat_updated' ), 10, 4 );
		add_action( 'update_term_meta', array( $this, 'event_cat_display_updated' ), 10, 4 );
		add_action( 'delete_product_cat', array( $this, 'event_product_cat_deleted' ), 10, 4 );
		add_action( 'wsal_before_post_meta_create_event', array( $this, 'log_coupon_meta_created_event' ), 10, 4 );
		add_action( 'wsal_before_post_meta_update_event', array( $this, 'log_coupon_meta_update_events' ), 10, 5 );
		add_action( 'wsal_before_post_meta_delete_event', array( $this, 'log_coupon_meta_delete_event' ), 10, 4 );
	}

	/**
	 * Triggered when a user accesses the admin area.
	 */
	public function EventAdminInit() {
		// Load old data, if applicable.
		$this->RetrieveOldData();
		$this->CheckSettingsChange();
		$this->retrieve_attribute_data();
		$this->check_wc_ajax_change_events();
	}

	/**
	 * Retrieve Old data.
	 *
	 * @global mixed $_POST post data
	 */
	protected function RetrieveOldData() {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if ( isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& ! wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] ) ) {
			return false;
		}

		if ( isset( $post_array ) && isset( $post_array['post_ID'] )
			&& ! ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			&& ! ( isset( $post_array['action'] ) && 'autosave' == $post_array['action'] )
		) {
			$post_id                 = intval( $post_array['post_ID'] );
			$this->_old_post         = get_post( $post_id );
			$this->_old_link         = get_post_permalink( $post_id, false, true );
			$this->_old_cats         = $this->GetProductCategories( $this->_old_post );
			$this->_old_data         = $this->GetProductData( $this->_old_post );
			$this->_old_stock        = get_post_meta( $post_id, '_stock', true );
			$this->_old_stock_status = get_post_meta( $post_id, '_stock_status', true );

			$old_downloadable_files = get_post_meta( $post_id, '_downloadable_files', true );
			if ( ! empty( $old_downloadable_files ) ) {
				foreach ( $old_downloadable_files as $file ) {
					array_push( $this->_old_file_names, $file['name'] );
					array_push( $this->_old_file_urls, $file['file'] );
				}
			}
		}
	}

	/**
	 * Return Coupon Event Data.
	 *
	 * @since 3.3.1
	 *
	 * @param WP_Post $coupon - Coupon event data.
	 * @return array
	 */
	private function get_coupon_event_data( $coupon ) {
		if ( empty( $coupon ) || ! $coupon instanceof WP_Post ) {
			return array();
		}
		return array(
			'CouponID'      => $coupon->ID,
			'CouponName'    => $coupon->post_title,
			'CouponStatus'  => $coupon->post_status,
			'CouponExcerpt' => $coupon->post_excerpt,
		);
	}

	/**
	 * WooCommerce Product Updated.
	 *
	 * @param integer  $post_id - Post ID.
	 * @param stdClass $newpost - The new post.
	 * @param stdClass $oldpost - The old post.
	 */
	public function EventChanged( $post_id, $newpost, $oldpost ) {
		// Global variable which returns current page.
		global $pagenow;

		if ( 'post.php' === $pagenow && $this->CheckWooCommerce( $oldpost ) && is_admin() ) {
			$changes = 0 + $this->EventCreation( $oldpost, $newpost );
			if ( ! $changes ) {
				// Change Categories.
				$changes = $this->CheckCategoriesChange( $this->_old_cats, $this->GetProductCategories( $newpost ), $oldpost, $newpost );
			}
			if ( ! $changes ) {
				// Change Short description, Text, URL, Product Data, Date, Visibility, etc.
				$changes = 0
					+ $this->CheckShortDescriptionChange( $oldpost, $newpost )
					+ $this->CheckTextChange( $oldpost, $newpost )
					+ $this->CheckProductDataChange( $this->_old_data, $newpost )
					+ $this->CheckDateChange( $oldpost, $newpost )
					+ $this->CheckVisibilityChange( $oldpost )
					+ $this->check_catalog_visibility_change( $oldpost )
					+ $this->check_featured_product( $oldpost )
					+ $this->CheckStatusChange( $oldpost, $newpost )
					+ $this->CheckPriceChange( $oldpost )
					+ $this->CheckSKUChange( $oldpost )
					+ $this->CheckStockStatusChange( $oldpost )
					+ $this->CheckStockQuantityChange( $oldpost )
					+ $this->CheckTypeChange( $oldpost, $newpost )
					+ $this->CheckWeightChange( $oldpost )
					+ $this->CheckDimensionsChange( $oldpost )
					+ $this->CheckDownloadableFileChange( $oldpost )
					+ $this->check_backorders_setting( $oldpost )
					+ $this->check_upsells_change( $oldpost )
					+ $this->check_cross_sell_change( $oldpost )
					+ $this->check_attributes_change( $oldpost )
					+ $this->check_title_change( $oldpost, $newpost );
			}
			if ( ! $changes ) {
				// Change Permalink.
				$changes = $this->CheckPermalinkChange( $this->_old_link, get_post_permalink( $post_id, false, true ), $newpost );
				if ( ! $changes ) {
					// If no one of the above changes happen.
					$this->CheckModifyChange( $oldpost, $newpost );
				}
			}
		} elseif ( 'post.php' === $pagenow && 'shop_order' === $oldpost->post_type && is_admin() ) {
			// Check order events.
			$this->check_order_modify_change( $post_id, $oldpost, $newpost );
		} elseif ( 'post.php' === $pagenow && 'shop_coupon' === $oldpost->post_type && is_admin() ) {
			// Check coupon events.
			$changes = 0 + $this->EventCreation( $oldpost, $newpost );

			if ( ! $changes ) {
				$this->CheckShortDescriptionChange( $oldpost, $newpost );
				$this->CheckStatusChange( $oldpost, $newpost );
				$this->check_title_change( $oldpost, $newpost );
			}
		}
	}

	/**
	 * WooCommerce Product/Coupon Created.
	 *
	 * Trigger events 9000, 9001, 9063.
	 *
	 * @param object $old_post - Old Post.
	 * @param object $new_post - New Post.
	 */
	private function EventCreation( $old_post, $new_post ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		// @codingStandardsIgnoreStart
		$wpnonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : false;
		$post_id = isset( $_POST['post_ID'] ) ? sanitize_text_field( wp_unslash( $_POST['post_ID'] ) ) : false;
		// @codingStandardsIgnoreEnd

		if ( ! empty( $post_id ) && ! empty( $wpnonce ) && wp_verify_nonce( $wpnonce, 'update-post_' . $post_id ) ) {
			// Original post status.
			$original = isset( $_POST['original_post_status'] ) ? sanitize_text_field( wp_unslash( $_POST['original_post_status'] ) ) : '';

			// Ignore if original or new post type is draft.
			if ( 'draft' === $original && 'draft' === $new_post->post_status ) {
				return 0;
			}

			if ( 'draft' === $old_post->post_status || 'auto-draft' === $original ) {
				if ( 'product' === $old_post->post_type ) {
					$editor_link = $this->GetEditorLink( $new_post );
					if ( 'draft' === $new_post->post_status ) {
						$this->plugin->alerts->Trigger( 9000, array(
							'ProductTitle'       => $new_post->post_title,
							$editor_link['name'] => $editor_link['value'],
						) );
						return 1;
					} elseif ( 'publish' === $new_post->post_status ) {
						$this->plugin->alerts->Trigger( 9001, array(
							'ProductTitle'       => $new_post->post_title,
							'ProductUrl'         => get_post_permalink( $new_post->ID ),
							$editor_link['name'] => $editor_link['value'],
						) );
						return 1;
					}
				} elseif ( 'shop_coupon' === $old_post->post_type && 'publish' === $new_post->post_status ) {
					$coupon_data = $this->get_coupon_event_data( $new_post );
					$this->plugin->alerts->Trigger( 9063, $coupon_data );
					return 1;
				}
			}
		}
		return 0;
	}

	/**
	 * Trigger events 9002
	 *
	 * @param int|WP_Term $term_id - Term ID.
	 */
	public function EventCategoryCreation( $term_id = null ) {
		$term = get_term( $term_id );
		if ( ! empty( $term ) ) {
			$this->plugin->alerts->Trigger(
				9002, array(
					'CategoryName' => $term->name,
					'Slug'         => $term->slug,
				)
			);
		}
	}

	/**
	 * Trigger events 9003
	 *
	 * @param array  $old_cats - Old Categories.
	 * @param array  $new_cats - New Categories.
	 * @param object $oldpost  - Old product object.
	 * @param object $newpost  - New product object.
	 * @return int
	 */
	protected function CheckCategoriesChange( $old_cats, $new_cats, $oldpost, $newpost ) {
		if ( 'trash' === $newpost->post_status || 'trash' === $oldpost->post_status ) {
			return 0;
		}

		$old_cats = is_array( $old_cats ) ? implode( ', ', $old_cats ) : $old_cats;
		$new_cats = is_array( $new_cats ) ? implode( ', ', $new_cats ) : $new_cats;

		if ( $old_cats != $new_cats ) {
			$editor_link = $this->GetEditorLink( $newpost );
			$this->plugin->alerts->Trigger(
				9003, array(
					'ProductTitle'       => $newpost->post_title,
					'ProductStatus'      => $newpost->post_status,
					'OldCategories'      => $old_cats ? $old_cats : 'no categories',
					'NewCategories'      => $new_cats ? $new_cats : 'no categories',
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9004
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckShortDescriptionChange( $oldpost, $newpost ) {
		if ( 'auto-draft' === $oldpost->post_status ) {
			return 0;
		}

		if ( $oldpost->post_excerpt !== $newpost->post_excerpt ) {
			if ( 'product' === $newpost->post_type ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9004, array(
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'OldDescription'     => $oldpost->post_excerpt,
						'NewDescription'     => $newpost->post_excerpt,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			} elseif ( 'shop_coupon' === $newpost->post_type ) {
				$coupon_data = $this->get_coupon_event_data( $newpost );
				$this->plugin->alerts->Trigger( 9069, $coupon_data );
				return 1;
			}
		}
		return 0;
	}

	/**
	 * Trigger events 9005
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckTextChange( $oldpost, $newpost ) {
		if ( 'auto-draft' === $oldpost->post_status ) {
			return 0;
		}
		if ( $oldpost->post_content != $newpost->post_content ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9005, array(
					'ProductTitle'       => $oldpost->post_title,
					'ProductStatus'      => $oldpost->post_status,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9006
	 *
	 * @param string $old_link - Old product link.
	 * @param string $new_link - New product link.
	 * @param object $post     - Product object.
	 * @return int
	 */
	protected function CheckPermalinkChange( $old_link, $new_link, $post ) {
		if ( ( $old_link && $new_link ) && ( $old_link != $new_link ) ) {
			$editor_link = $this->GetEditorLink( $post );
			$this->plugin->alerts->Trigger(
				9006, array(
					'ProductTitle'       => $post->post_title,
					'ProductStatus'      => $post->post_status,
					'OldUrl'             => $old_link,
					'NewUrl'             => $new_link,
					$editor_link['name'] => $editor_link['value'],
					'ReportText'         => '"' . $old_link . '"|"' . $new_link . '"',
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9007
	 *
	 * @param array  $old_data - Product Data.
	 * @param object $post     - Product object.
	 * @return int
	 */
	protected function CheckProductDataChange( $old_data, $post ) {
		// Check nonce.
		if (
			isset( $_POST['post_ID'] )
			&& isset( $_POST['_wpnonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-post_' . sanitize_text_field( wp_unslash( $_POST['post_ID'] ) ) )
		) {
			if ( isset( $_POST['product-type'] ) ) {
				$old_data = is_array( $old_data ) ? implode( ', ', $old_data ) : $old_data;
				$new_data = sanitize_text_field( wp_unslash( $_POST['product-type'] ) );

				if ( $old_data !== $new_data ) {
					$editor_link = $this->GetEditorLink( $post );
					$this->plugin->alerts->Trigger(
						9007, array(
							'ProductTitle'       => $post->post_title,
							'ProductStatus'      => $post->post_status,
							'OldType'            => $old_data,
							'NewType'            => $new_data,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					return 1;
				}
			}
			return 0;
		}
		return false;
	}

	/**
	 * Trigger events 9008
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckDateChange( $oldpost, $newpost ) {
		if ( 'draft' === $oldpost->post_status || 'auto-draft' === $oldpost->post_status ) {
			return 0;
		}

		$from = strtotime( $oldpost->post_date );
		$to   = strtotime( $newpost->post_date );

		if ( $from != $to ) {
			$editor_link = $this->GetEditorLink( $oldpost );
			$this->plugin->alerts->Trigger(
				9008, array(
					'ProductTitle'       => $oldpost->post_title,
					'ProductStatus'      => $oldpost->post_status,
					'OldDate'            => $oldpost->post_date,
					'NewDate'            => $newpost->post_date,
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9009
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckVisibilityChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if (
			isset( $post_array['post_ID'] ) && isset( $post_array['_wpnonce'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] )
		) {
			$old_visibility = isset( $post_array['hidden_post_visibility'] ) ? $post_array['hidden_post_visibility'] : null;
			$new_visibility = isset( $post_array['visibility'] ) ? $post_array['visibility'] : null;

			if ( 'password' === $old_visibility ) {
				$old_visibility = __( 'Password Protected', 'wp-security-audit-log' );
			} else {
				$old_visibility = ucfirst( $old_visibility );
			}

			if ( 'password' === $new_visibility ) {
				$new_visibility = __( 'Password Protected', 'wp-security-audit-log' );
			} else {
				$new_visibility = ucfirst( $new_visibility );
			}

			if ( ( $old_visibility && $new_visibility ) && ( $old_visibility != $new_visibility ) ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9009, array(
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'OldVisibility'      => $old_visibility,
						'NewVisibility'      => $new_visibility,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
			return 0;
		}
		return false;
	}

	/**
	 * Check Title Change.
	 *
	 * Trigger event 9071.
	 *
	 * @since 3.3.1
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function check_title_change( $oldpost, $newpost ) {
		if ( 'auto-draft' === $oldpost->post_status ) {
			return 0;
		}

		if ( 'shop_coupon' === $newpost->post_type && $oldpost->post_title !== $newpost->post_title ) {
			// Get coupon event data.
			$coupon_data = $this->get_coupon_event_data( $newpost );

			// Set old and new titles.
			$coupon_data['OldName'] = $oldpost->post_title;
			$coupon_data['NewName'] = $newpost->post_title;

			// Log the event.
			$this->plugin->alerts->Trigger( 9071, $coupon_data );
			return 1;
		} elseif ( 'product' === $newpost->post_type && $oldpost->post_title !== $newpost->post_title ) {
			// Get editor link.
			$editor_link = $this->GetEditorLink( $newpost );

			// Log the event.
			$this->plugin->alerts->Trigger(
				9077, array(
					'PostID'             => $newpost->ID,
					'PostType'           => $newpost->post_type,
					'ProductStatus'      => $newpost->post_status,
					'ProductTitle'       => $newpost->post_title,
					'OldTitle'           => $oldpost->post_title,
					'NewTitle'           => $newpost->post_title,
					'ProductUrl'         => get_permalink( $newpost->ID ),
					$editor_link['name'] => $editor_link['value'],
				)
			);
			return 1;
		}
		return 0;
	}

	/**
	 * Trigger events 9042
	 *
	 * @since 3.3.1
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function check_catalog_visibility_change( $oldpost ) {
		if (
			isset( $_POST['post_ID'] ) && isset( $_POST['_wpnonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-post_' . sanitize_text_field( wp_unslash( $_POST['post_ID'] ) ) )
		) {
			// Get product data.
			$product_object = new WC_Product( $oldpost->ID );
			$old_visibility = $product_object->get_catalog_visibility();
			$new_visibility = isset( $_POST['_visibility'] ) ? sanitize_text_field( wp_unslash( $_POST['_visibility'] ) ) : false;

			// Get WooCommerce visibility options.
			$visibility_options = wc_get_product_visibility_options();

			if ( ( $old_visibility && $new_visibility ) && ( $old_visibility !== $new_visibility ) ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9042, array(
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'OldVisibility'      => isset( $visibility_options[ $old_visibility ] ) ? $visibility_options[ $old_visibility ] : false,
						'NewVisibility'      => isset( $visibility_options[ $new_visibility ] ) ? $visibility_options[ $new_visibility ] : false,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
			return 0;
		}
		return false;
	}

	/**
	 * Trigger events 9043
	 *
	 * @since 3.3.1
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function check_featured_product( $oldpost ) {
		if (
			isset( $_POST['post_ID'] ) && isset( $_POST['_wpnonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-post_' . sanitize_text_field( wp_unslash( $_POST['post_ID'] ) ) )
		) {
			// Get product data.
			$product_object = new WC_Product( $oldpost->ID );
			$old_featured   = $product_object->get_featured();
			$new_featured   = isset( $_POST['_featured'] );

			if ( $old_featured !== $new_featured ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9043, array(
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'Status'             => ( $new_featured ) ? 'Enabled' : 'Disabled',
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
			return 0;
		}
		return false;
	}

	/**
	 * Trigger events 9044
	 *
	 * @since 3.3.1
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function check_backorders_setting( $oldpost ) {
		if (
			isset( $_POST['post_ID'] ) && isset( $_POST['_wpnonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-post_' . sanitize_text_field( wp_unslash( $_POST['post_ID'] ) ) )
		) {
			// Get product data.
			$old_backorder = get_post_meta( $oldpost->ID, '_backorders', true );
			$new_backorder = isset( $_POST['_backorders'] ) ? sanitize_text_field( wp_unslash( $_POST['_backorders'] ) ) : false;

			if ( $old_backorder !== $new_backorder ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9044, array(
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'OldStatus'          => $old_backorder,
						'NewStatus'          => $new_backorder,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
			return 0;
		}
		return false;
	}

	/**
	 * Trigger events 9045
	 *
	 * @since 3.3.1
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function check_upsells_change( $oldpost ) {
		if (
			isset( $_POST['post_ID'] ) && isset( $_POST['_wpnonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-post_' . sanitize_text_field( wp_unslash( $_POST['post_ID'] ) ) )
		) {
			// Get product data.
			$old_upsell_ids = get_post_meta( $oldpost->ID, '_upsell_ids', true );
			$new_upsell_ids = isset( $_POST['upsell_ids'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['upsell_ids'] ) ) : false;

			// Compute the difference.
			$added_upsells   = array();
			$removed_upsells = array();
			if ( is_array( $new_upsell_ids ) && is_array( $old_upsell_ids ) ) {
				$added_upsells   = array_diff( $new_upsell_ids, $old_upsell_ids );
				$removed_upsells = array_diff( $old_upsell_ids, $new_upsell_ids );
			}

			// Get editor link.
			$editor_link = $this->GetEditorLink( $oldpost );

			// Return.
			$return = 0;

			// Added upsell products.
			if ( ! empty( $added_upsells ) && is_array( $added_upsells ) ) {
				foreach ( $added_upsells as $added_upsell ) {
					$upsell_title = get_the_title( $added_upsell );
					$this->plugin->alerts->Trigger(
						9045, array(
							'Status'             => 'Added',
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							'UpsellTitle'        => $upsell_title,
							'UpsellID'           => $added_upsell,
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
				$return = 1;
			}

			// Removed upsell products.
			if ( ! empty( $removed_upsells ) && is_array( $removed_upsells ) ) {
				foreach ( $removed_upsells as $removed_upsell ) {
					$upsell_title = get_the_title( $removed_upsell );
					$this->plugin->alerts->Trigger(
						9045, array(
							'Status'             => 'Removed',
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							'UpsellTitle'        => $upsell_title,
							'UpsellID'           => $removed_upsell,
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
				$return = 1;
			}
			return $return;
		}
		return false;
	}

	/**
	 * Trigger events 9046
	 *
	 * @since 3.3.1
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function check_cross_sell_change( $oldpost ) {
		if (
			isset( $_POST['post_ID'] ) && isset( $_POST['_wpnonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-post_' . sanitize_text_field( wp_unslash( $_POST['post_ID'] ) ) )
		) {
			// Get product data.
			$old_cross_sell_ids = get_post_meta( $oldpost->ID, '_crosssell_ids', true );
			$new_cross_sell_ids = isset( $_POST['crosssell_ids'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['crosssell_ids'] ) ) : false;

			// Compute the difference.
			$added_cross_sells   = array();
			$removed_cross_sells = array();
			if ( is_array( $new_cross_sell_ids ) && is_array( $old_cross_sell_ids ) ) {
				$added_cross_sells   = array_diff( $new_cross_sell_ids, $old_cross_sell_ids );
				$removed_cross_sells = array_diff( $old_cross_sell_ids, $new_cross_sell_ids );
			}

			// Get editor link.
			$editor_link = $this->GetEditorLink( $oldpost );

			// Return.
			$return = 0;

			// Added cross-sell products.
			if ( ! empty( $added_cross_sells ) && is_array( $added_cross_sells ) ) {
				foreach ( $added_cross_sells as $added_cross_sell ) {
					$cross_sell_title = get_the_title( $added_cross_sell );
					$this->plugin->alerts->Trigger(
						9046, array(
							'Status'             => 'Added',
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							'CrossSellTitle'     => $cross_sell_title,
							'CrossSellID'        => $added_cross_sell,
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
				$return = 1;
			}

			// Removed cross-sell products.
			if ( ! empty( $removed_cross_sells ) && is_array( $removed_cross_sells ) ) {
				foreach ( $removed_cross_sells as $removed_cross_sell ) {
					$cross_sell_title = get_the_title( $removed_cross_sell );
					$this->plugin->alerts->Trigger(
						9046, array(
							'Status'             => 'Removed',
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							'CrossSellTitle'     => $cross_sell_title,
							'CrossSellID'        => $removed_cross_sell,
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
				$return = 1;
			}
			return $return;
		}
		return false;
	}

	/**
	 * Trigger events 9010.
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckModifyChange( $oldpost, $newpost ) {
		if ( 'trash' === $oldpost->post_status || 'trash' === $newpost->post_status ) {
			return 0;
		}

		// Get Yoast alerts.
		$yoast_alerts = $this->plugin->alerts->get_alerts_by_sub_category( 'Yoast SEO' );

		// Check all alerts.
		foreach ( $yoast_alerts as $alert_code => $alert ) {
			if ( $this->plugin->alerts->WillOrHasTriggered( $alert_code ) ) {
				return 0; // Return if any Yoast alert has or will trigger.
			}
		}

		$editor_link = $this->GetEditorLink( $oldpost );
		$this->plugin->alerts->Trigger(
			9010, array(
				'ProductTitle'       => $oldpost->post_title,
				'ProductStatus'      => $oldpost->post_status,
				'ProductUrl'         => get_post_permalink( $oldpost->ID ),
				$editor_link['name'] => $editor_link['value'],
			)
		);
	}

	/**
	 * Moved to Trash 9012, 9037.
	 *
	 * @param int $post_id - Product/Order ID.
	 */
	public function EventTrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( empty( $post ) || ! $post instanceof WP_Post ) {
			return;
		}

		if ( 'product' === $post->post_type ) {
			$this->plugin->alerts->Trigger( 9012, array(
				'ProductTitle'  => $post->post_title,
				'ProductStatus' => $post->post_status,
				'ProductUrl'    => get_post_permalink( $post->ID ),
			) );
		} elseif ( 'shop_order' === $post->post_type ) {
			$this->plugin->alerts->Trigger( 9037, array(
				'OrderID'     => $post->ID,
				'OrderTitle'  => $this->get_order_title( $post->ID ),
				'OrderStatus' => $post->post_status,
			) );
		}
	}

	/**
	 * Permanently deleted 9013 or 9039.
	 *
	 * @param int $post_id - Product/Order ID.
	 */
	public function EventDeleted( $post_id ) {
		$post = get_post( $post_id );
		if ( empty( $post ) || ! $post instanceof WP_Post ) {
			return;
		}

		if ( 'product' === $post->post_type ) {
			$this->plugin->alerts->Trigger( 9013, array( 'ProductTitle' => $post->post_title ) );
		} elseif ( 'shop_order' === $post->post_type ) {
			$this->plugin->alerts->Trigger( 9039, array( 'OrderTitle' => $this->get_order_title( $post_id ) ) );
		}
	}

	/**
	 * Restored from Trash 9014
	 *
	 * @param int $post_id - Product ID.
	 */
	public function EventUntrashed( $post_id ) {
		$post = get_post( $post_id );
		if ( empty( $post ) || ! $post instanceof WP_Post ) {
			return;
		}

		if ( 'product' === $post->post_type ) {
			$editor_link = $this->GetEditorLink( $post );
			$this->plugin->alerts->Trigger( 9014, array(
				'ProductTitle'       => $post->post_title,
				$editor_link['name'] => $editor_link['value'],
			) );
		} elseif ( 'shop_order' === $post->post_type ) {
			$editor_link = $this->GetEditorLink( $post );
			$this->plugin->alerts->Trigger( 9038, array(
				'OrderID'            => $post->ID,
				'OrderTitle'         => $this->get_order_title( $post_id ),
				'OrderStatus'        => $post->post_status,
				$editor_link['name'] => $editor_link['value'],
			) );
		}
	}

	/**
	 * Viewing Product Event.
	 *
	 * Alerts for viewing of product post type for WooCommerce.
	 */
	public function viewing_product() {
		// Retrieve the current post object.
		$product = get_queried_object();

		// Check product post type.
		if ( ! empty( $product ) && $product instanceof WP_Post && 'product' !== $product->post_type ) {
			return $product;
		}

		if ( is_user_logged_in() && ! is_admin() ) {
			$current_path = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : false;
			if (
				! empty( $_SERVER['HTTP_REFERER'] )
				&& ! empty( $current_path )
				&& false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), $current_path )
			) {
				// Ignore this if we were on the same page so we avoid double audit entries.
				return;
			}

			if ( ! empty( $product->post_title ) ) {
				$editor_link = $this->GetEditorLink( $product );
				$this->plugin->alerts->Trigger(
					9073, array(
						'PostID'             => $product->ID,
						'PostType'           => $product->post_type,
						'ProductStatus'      => $product->post_status,
						'ProductTitle'       => $product->post_title,
						'ProductUrl'         => get_permalink( $product->ID ),
						$editor_link['name'] => $editor_link['value'],
					)
				);
			}
		}
	}

	/**
	 * Trigger events 9015
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckStatusChange( $oldpost, $newpost ) {
		if ( 'draft' === $oldpost->post_status || 'auto-draft' === $oldpost->post_status ) {
			return 0;
		}
		if ( $oldpost->post_status !== $newpost->post_status ) {
			if ( 'trash' !== $oldpost->post_status && 'trash' !== $newpost->post_status ) {
				if ( 'product' === $newpost->post_type ) {
					$editor_link = $this->GetEditorLink( $oldpost );
					$this->plugin->alerts->Trigger(
						9015, array(
							'ProductTitle'       => $oldpost->post_title,
							'OldStatus'          => $oldpost->post_status,
							'NewStatus'          => $newpost->post_status,
							$editor_link['name'] => $editor_link['value'],
						)
					);
					return 1;
				} elseif ( 'shop_coupon' === $newpost->post_type ) {
					// Get coupon data.
					$coupon_data = $this->get_coupon_event_data( $newpost );

					// Set status event data.
					$coupon_data['OldStatus'] = $oldpost->post_status;
					$coupon_data['NewStatus'] = $newpost->post_status;

					// Log the event.
					$this->plugin->alerts->Trigger( 9070, $coupon_data );
					return 1;
				}
			}
		}
		return 0;
	}

	/**
	 * Trigger events 9016
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckPriceChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if (
			isset( $post_array['post_ID'] ) && isset( $post_array['_wpnonce'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] )
		) {
			$result         = 0;
			$old_price      = get_post_meta( $oldpost->ID, '_regular_price', true );
			$old_sale_price = get_post_meta( $oldpost->ID, '_sale_price', true );
			$new_price      = isset( $post_array['_regular_price'] ) ? $post_array['_regular_price'] : null;
			$new_sale_price = isset( $post_array['_sale_price'] ) ? $post_array['_sale_price'] : null;

			if ( ( $new_price ) && ( $old_price != $new_price ) ) {
				$result = $this->EventPrice( $oldpost, 'Regular price', $old_price, $new_price );
			}
			if ( ( $new_sale_price ) && ( $old_sale_price != $new_sale_price ) ) {
				$result = $this->EventPrice( $oldpost, 'Sale price', $old_sale_price, $new_sale_price );
			}
			return $result;
		}
		return false;
	}

	/**
	 * Group the Price changes in one function
	 *
	 * @param object $oldpost   - Old Product Object.
	 * @param string $type      - Price Type.
	 * @param int    $old_price - Old Product Price.
	 * @param int    $new_price - New Product Price.
	 * @return int
	 */
	private function EventPrice( $oldpost, $type, $old_price, $new_price ) {
		$currency    = $this->GetCurrencySymbol( $this->GetConfig( 'currency' ) );
		$editor_link = $this->GetEditorLink( $oldpost );
		$this->plugin->alerts->Trigger(
			9016, array(
				'ProductTitle'       => $oldpost->post_title,
				'ProductStatus'      => $oldpost->post_status,
				'PriceType'          => $type,
				'OldPrice'           => ( ! empty( $old_price ) ? $currency . $old_price : 0 ),
				'NewPrice'           => $currency . $new_price,
				$editor_link['name'] => $editor_link['value'],
			)
		);
		return 1;
	}

	/**
	 * Trigger events 9017
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckSKUChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if (
			isset( $post_array['post_ID'] ) && isset( $post_array['_wpnonce'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] )
		) {
			$old_sku = get_post_meta( $oldpost->ID, '_sku', true );
			$new_sku = isset( $post_array['_sku'] ) ? $post_array['_sku'] : null;

			if ( ( $new_sku ) && ( $old_sku != $new_sku ) ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9017, array(
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'OldSku'             => ( ! empty( $old_sku ) ? $old_sku : 0 ),
						'NewSku'             => $new_sku,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
			return 0;
		}
		return false;
	}

	/**
	 * Trigger events 9018
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckStockStatusChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if (
			isset( $post_array['post_ID'] ) && isset( $post_array['_wpnonce'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] )
		) {
			$old_status = $this->_old_stock_status;
			$new_status = isset( $post_array['_stock_status'] ) ? $post_array['_stock_status'] : null;

			if ( ( $old_status && $new_status ) && ( $old_status != $new_status ) ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9018, array(
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'OldStatus'          => $this->GetStockStatusName( $old_status ),
						'NewStatus'          => $this->GetStockStatusName( $new_status ),
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
			return 0;
		}
		return false;
	}

	/**
	 * Trigger events 9019
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckStockQuantityChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if (
			isset( $post_array['post_ID'] )
			&& isset( $post_array['_wpnonce'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] )
		) {
			$old_value = (int) get_post_meta( $oldpost->ID, '_stock', true );
			$new_value = isset( $post_array['_stock'] ) ? (int) $post_array['_stock'] : null;

			if ( $new_value && ( $old_value !== $new_value ) ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9019, array(
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'OldValue'           => ( ! empty( $old_value ) ? $old_value : 0 ),
						'NewValue'           => $new_value,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
		}
		return 0;
	}

	/**
	 * Trigger events 9020
	 *
	 * @param object $oldpost - Old product object.
	 * @param object $newpost - New product object.
	 * @return int
	 */
	protected function CheckTypeChange( $oldpost, $newpost ) {
		if (
			isset( $_POST['post_ID'] ) && isset( $_POST['_wpnonce'] )
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-post_' . sanitize_text_field( wp_unslash( $_POST['post_ID'] ) ) )
		) {
			if ( 'trash' !== $oldpost->post_status && 'trash' !== $newpost->post_status ) {
				// Get simple product virtual data.
				$old_virtual = get_post_meta( $oldpost->ID, '_virtual', true );
				$new_virtual = isset( $_POST['_virtual'] ) ? 'yes' : 'no';

				// Get simple product downloadable data.
				$old_download = get_post_meta( $oldpost->ID, '_downloadable', true );
				$new_download = isset( $_POST['_downloadable'] ) ? 'yes' : 'no';

				// Return variable.
				$result = 0;

				if ( $old_virtual && $new_virtual && $old_virtual !== $new_virtual ) {
					$editor_link = $this->GetEditorLink( $oldpost );
					$this->plugin->alerts->Trigger(
						9020, array(
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							'OldType'            => ( 'yes' === $old_virtual ) ? 'Virtual' : 'Non-Virtual',
							'NewType'            => ( 'yes' === $new_virtual ) ? 'Virtual' : 'Non-Virtual',
							$editor_link['name'] => $editor_link['value'],
						)
					);
					$result = 1;
				}

				if ( $old_download && $new_download && $old_download !== $new_download ) {
					$editor_link = $this->GetEditorLink( $oldpost );
					$this->plugin->alerts->Trigger(
						9020, array(
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							'OldType'            => ( 'yes' === $old_download ) ? 'Downloadable' : 'Non-Downloadable',
							'NewType'            => ( 'yes' === $new_download ) ? 'Downloadable' : 'Non-Downloadable',
							$editor_link['name'] => $editor_link['value'],
						)
					);
					$result = 1;
				}
				return $result;
			}
			return 0;
		}
		return false;
	}

	/**
	 * Group the Type changes in one function.
	 *
	 * @deprecated 3.3.1
	 *
	 * @param object $oldpost - Old product object.
	 * @param string $type    - Product Type.
	 * @return int
	 */
	private function EventType( $oldpost, $type ) {
		$editor_link = $this->GetEditorLink( $oldpost );
		$this->plugin->alerts->Trigger(
			9020, array(
				'ProductTitle'       => $oldpost->post_title,
				'Type'               => $type,
				$editor_link['name'] => $editor_link['value'],
			)
		);
		return 1;
	}

	/**
	 * Trigger events 9021
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckWeightChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if (
			isset( $post_array['post_ID'] ) && isset( $post_array['_wpnonce'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] )
		) {
			$old_weight = get_post_meta( $oldpost->ID, '_weight', true );
			$new_weight = isset( $post_array['_weight'] ) ? $post_array['_weight'] : null;

			if ( ( $new_weight ) && ( $old_weight != $new_weight ) ) {
				$editor_link = $this->GetEditorLink( $oldpost );
				$this->plugin->alerts->Trigger(
					9021, array(
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'OldWeight'          => ( ! empty( $old_weight ) ? $old_weight : 0 ),
						'NewWeight'          => $new_weight,
						$editor_link['name'] => $editor_link['value'],
					)
				);
				return 1;
			}
			return 0;
		}
		return false;
	}

	/**
	 * Trigger events 9022
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckDimensionsChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if (
			isset( $post_array['post_ID'] ) && isset( $post_array['_wpnonce'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] )
		) {
			$result     = 0;
			$old_length = get_post_meta( $oldpost->ID, '_length', true );
			$new_length = isset( $post_array['_length'] ) ? $post_array['_length'] : null;
			$old_width  = get_post_meta( $oldpost->ID, '_width', true );
			$new_width  = isset( $post_array['_width'] ) ? $post_array['_width'] : null;
			$old_height = get_post_meta( $oldpost->ID, '_height', true );
			$new_height = isset( $post_array['_height'] ) ? $post_array['_height'] : null;

			if ( ( $new_length ) && ( $old_length != $new_length ) ) {
				$result = $this->EventDimension( $oldpost, 'Length', $old_length, $new_length );
			}
			if ( ( $new_width ) && ( $old_width != $new_width ) ) {
				$result = $this->EventDimension( $oldpost, 'Width', $old_width, $new_width );
			}
			if ( ( $new_height ) && ( $old_height != $new_height ) ) {
				$result = $this->EventDimension( $oldpost, 'Height', $old_height, $new_height );
			}
			return $result;
		}
		return false;
	}

	/**
	 * Group the Dimension changes in one function.
	 *
	 * @param object $oldpost - Old Product object.
	 * @param string $type - Dimension type.
	 * @param string $old_dimension - Old dimension.
	 * @param string $new_dimension - New dimension.
	 * @return int
	 */
	private function EventDimension( $oldpost, $type, $old_dimension, $new_dimension ) {
		$dimension_unit = $this->GetConfig( 'dimension_unit' );
		$editor_link    = $this->GetEditorLink( $oldpost );
		$this->plugin->alerts->Trigger(
			9022, array(
				'ProductTitle'       => $oldpost->post_title,
				'ProductStatus'      => $oldpost->post_status,
				'DimensionType'      => $type,
				'OldDimension'       => ( ! empty( $old_dimension ) ? $dimension_unit . ' ' . $old_dimension : 0 ),
				'NewDimension'       => $dimension_unit . ' ' . $new_dimension,
				$editor_link['name'] => $editor_link['value'],
			)
		);
		return 1;
	}

	/**
	 * Trigger events 9023, 9024, 9025, 9026
	 *
	 * @param object $oldpost - Old product object.
	 * @return int
	 */
	protected function CheckDownloadableFileChange( $oldpost ) {
		// Filter POST global array.
		$post_array = filter_input_array( INPUT_POST );

		if (
			isset( $post_array['post_ID'] ) && isset( $post_array['_wpnonce'] )
			&& wp_verify_nonce( $post_array['_wpnonce'], 'update-post_' . $post_array['post_ID'] )
		) {
			$result          = 0;
			$is_url_changed  = false;
			$is_name_changed = false;
			$new_file_names  = ! empty( $post_array['_wc_file_names'] ) ? $post_array['_wc_file_names'] : array();
			$new_file_urls   = ! empty( $post_array['_wc_file_urls'] ) ? $post_array['_wc_file_urls'] : array();
			$editor_link     = $this->GetEditorLink( $oldpost );
			$added_urls      = array_diff( $new_file_urls, $this->_old_file_urls );

			// Added files to the product.
			if ( count( $added_urls ) > 0 ) {
				// If the file has only changed URL.
				if ( count( $new_file_urls ) == count( $this->_old_file_urls ) ) {
					$is_url_changed = true;
				} else {
					foreach ( $added_urls as $key => $url ) {
						$this->plugin->alerts->Trigger(
							9023, array(
								'ProductTitle'       => $oldpost->post_title,
								'ProductStatus'      => $oldpost->post_status,
								'FileName'           => $new_file_names[ $key ],
								'FileUrl'            => $url,
								$editor_link['name'] => $editor_link['value'],
							)
						);
					}
					$result = 1;
				}
			}

			$removed_urls = array_diff( $this->_old_file_urls, $new_file_urls );
			// Removed files from the product.
			if ( count( $removed_urls ) > 0 ) {
				// If the file has only changed URL.
				if ( count( $new_file_urls ) == count( $this->_old_file_urls ) ) {
					$is_url_changed = true;
				} else {
					foreach ( $removed_urls as $key => $url ) {
						$this->plugin->alerts->Trigger(
							9024, array(
								'ProductTitle'       => $oldpost->post_title,
								'ProductStatus'      => $oldpost->post_status,
								'FileName'           => $this->_old_file_names[ $key ],
								'FileUrl'            => $url,
								$editor_link['name'] => $editor_link['value'],
							)
						);
					}
					$result = 1;
				}
			}

			$added_names = array_diff( $new_file_names, $this->_old_file_names );
			if ( count( $added_names ) > 0 ) {
				// If the file has only changed Name.
				if ( count( $new_file_names ) == count( $this->_old_file_names ) ) {
					foreach ( $added_names as $key => $name ) {
						$this->plugin->alerts->Trigger(
							9025, array(
								'ProductTitle'       => $oldpost->post_title,
								'ProductStatus'      => $oldpost->post_status,
								'OldName'            => $this->_old_file_names[ $key ],
								'NewName'            => $name,
								$editor_link['name'] => $editor_link['value'],
							)
						);
					}
					$result = 1;
				}
			}

			if ( $is_url_changed ) {
				foreach ( $added_urls as $key => $url ) {
					$this->plugin->alerts->Trigger(
						9026, array(
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							'FileName'           => $new_file_names[ $key ],
							'OldUrl'             => $removed_urls[ $key ],
							'NewUrl'             => $url,
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
				$result = 1;
			}
			return $result;
		}
		return false;
	}

	/**
	 * Trigger events Settings: 9027, 9028, 9029, 9030, 9031, 9032, 9033
	 */
	protected function CheckSettingsChange() {
		// Verify WooCommerce settings page nonce.
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'woocommerce-settings' ) ) {
			// Verify settings page via $_GET array.
			if ( isset( $_GET['page'] ) && 'wc-settings' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) {
				if ( isset( $_GET['tab'] ) && 'products' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) {
					// Check weight unit event.
					if ( isset( $_POST['woocommerce_weight_unit'] ) ) {
						$old_unit = $this->GetConfig( 'weight_unit' );
						$new_unit = sanitize_text_field( wp_unslash( $_POST['woocommerce_weight_unit'] ) );
						if ( $old_unit !== $new_unit ) {
							$this->plugin->alerts->Trigger( 9027, array(
								'OldUnit' => $old_unit,
								'NewUnit' => $new_unit,
							) );
						}
					}

					// Check dimension unit event.
					if ( isset( $_POST['woocommerce_dimension_unit'] ) ) {
						$old_unit = $this->GetConfig( 'dimension_unit' );
						$new_unit = sanitize_text_field( wp_unslash( $_POST['woocommerce_dimension_unit'] ) );
						if ( $old_unit !== $new_unit ) {
							$this->plugin->alerts->Trigger( 9028, array(
								'OldUnit' => $old_unit,
								'NewUnit' => $new_unit,
							) );
						}
					}
				} elseif ( isset( $_GET['tab'] ) && 'account' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) {
					// Guest Checkout event.
					$old_enable_guest_checkout = $this->GetConfig( 'enable_guest_checkout' );
					$new_enable_guest_checkout = isset( $_POST['woocommerce_enable_guest_checkout'] ) ? 'yes' : 'no';
					if ( $old_enable_guest_checkout !== $new_enable_guest_checkout ) {
						$status = 'yes' === $new_enable_guest_checkout ? 'Enabled' : 'Disabled';
						$this->plugin->alerts->Trigger( 9033, array( 'Status' => $status ) );
					}
				} elseif ( isset( $_GET['tab'] ) && 'checkout' === sanitize_text_field( wp_unslash( $_GET['tab'] ) ) ) {
					// Get payment method.
					$gateway = isset( $_GET['section'] ) ? sanitize_text_field( wp_unslash( $_GET['section'] ) ) : false;

					// Set to true if payment gateways are enabled or disabled.
					$status_change = false;

					// Check Cash On Delivery section.
					if ( $gateway && 'cod' === sanitize_text_field( wp_unslash( $gateway ) ) ) {
						// Check COD event.
						$old_cash_on_delivery = $this->GetConfig( 'cod_settings' );
						$old_cash_on_delivery = isset( $old_cash_on_delivery['enabled'] ) ? $old_cash_on_delivery['enabled'] : '';
						$new_cash_on_delivery = isset( $_POST['woocommerce_cod_enabled'] ) ? 'yes' : 'no';

						// Check status change.
						if ( $old_cash_on_delivery !== $new_cash_on_delivery ) {
							$status = ( 'yes' === $new_cash_on_delivery ) ? 'Enabled' : 'Disabled';
							$this->plugin->alerts->Trigger( 9034, array( 'Status' => $status ) );
							$status_change = true;
						}
					} elseif ( $gateway ) {
						// Get old gateway settings.
						$gateway_settings   = $this->GetConfig( $gateway . '_settings' );
						$old_gateway_status = isset( $gateway_settings['enabled'] ) ? $gateway_settings['enabled'] : false;
						$new_gateway_status = isset( $_POST[ 'woocommerce_' . $gateway . '_enabled' ] ) ? 'yes' : 'no';

						// Check status change.
						if ( $old_gateway_status !== $new_gateway_status ) {
							if ( 'yes' === $new_gateway_status ) {
								// Gateway enabled.
								$this->plugin->alerts->Trigger( 9074, array(
									'GatewayID'   => $gateway,
									'GatewayName' => isset( $gateway_settings['title'] ) ? $gateway_settings['title'] : false,
								) );
							} else {
								// Gateway disabled.
								$this->plugin->alerts->Trigger( 9075, array(
									'GatewayID'   => $gateway,
									'GatewayName' => isset( $gateway_settings['title'] ) ? $gateway_settings['title'] : false,
								) );
							}
							$status_change = true;
						}
					}

					if ( $gateway && ! $status_change ) {
						$gateway_settings = $this->GetConfig( $gateway . '_settings' );
						$this->plugin->alerts->Trigger( 9076, array(
							'GatewayID'   => $gateway,
							'GatewayName' => isset( $gateway_settings['title'] ) ? $gateway_settings['title'] : false,
						) );
					}
				} else {
					// "Enable Coupon" event.
					$old_enable_coupons = $this->GetConfig( 'enable_coupons' );
					$new_enable_coupons = isset( $_POST['woocommerce_enable_coupons'] ) ? 'yes' : 'no';
					if ( $old_enable_coupons !== $new_enable_coupons ) {
						$status = 'yes' === $new_enable_coupons ? 'Enabled' : 'Disabled';
						$this->plugin->alerts->Trigger( 9032, array( 'Status' => $status ) );
					}

					if ( isset( $_POST['woocommerce_default_country'] ) ) {
						// Default country event.
						$old_location = $this->GetConfig( 'default_country' );
						$new_location = sanitize_text_field( wp_unslash( $_POST['woocommerce_default_country'] ) );
						if ( $old_location !== $new_location ) {
							$this->plugin->alerts->Trigger( 9029, array(
								'OldLocation' => $old_location,
								'NewLocation' => $new_location,
							) );
						}

						// Calculate taxes event.
						$old_calc_taxes = $this->GetConfig( 'calc_taxes' );
						$new_calc_taxes = isset( $_POST['woocommerce_calc_taxes'] ) ? 'yes' : 'no';
						if ( $old_calc_taxes !== $new_calc_taxes ) {
							$status = ( 'yes' == $new_calc_taxes ) ? 'Enabled' : 'Disabled';
							$this->plugin->alerts->Trigger( 9030, array( 'Status' => $status ) );
						}
					}

					// Store current event.
					if ( isset( $_POST['woocommerce_currency'] ) ) {
						$old_currency = $this->GetConfig( 'currency' );
						$new_currency = sanitize_text_field( wp_unslash( $_POST['woocommerce_currency'] ) );
						if ( $old_currency !== $new_currency ) {
							$this->plugin->alerts->Trigger( 9031, array(
								'OldCurrency' => $old_currency,
								'NewCurrency' => $new_currency,
							) );
						}
					}
				}
			}
		}

		// Verify nonce for payment gateways.
		if ( isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'woocommerce-toggle-payment-gateway-enabled' ) ) {
			if ( isset( $_POST['action'] ) && 'woocommerce_toggle_gateway_enabled' === sanitize_text_field( wp_unslash( $_POST['action'] ) ) ) {
				// Get payment gateways.
				$payment_gateways = WC()->payment_gateways->payment_gateways();

				if ( ! empty( $payment_gateways ) ) {
					foreach ( $payment_gateways as $gateway ) {
						// Get gateway ID.
						$gateway_id = isset( $_POST['gateway_id'] ) ? sanitize_text_field( wp_unslash( $_POST['gateway_id'] ) ) : false;

						// Check if the gateway id matches any available gateway ids.
						if ( ! in_array( $gateway_id, array( $gateway->id, sanitize_title( get_class( $gateway ) ) ), true ) ) {
							continue;
						}
						$enabled = $gateway->get_option( 'enabled', 'no' );

						if ( 'cod' === $gateway->id ) {
							if ( ! wc_string_to_bool( $enabled ) ) {
								$status = 'Enabled';
							} else {
								$status = 'Disabled';
							}
							$this->plugin->alerts->Trigger( 9034, array( 'Status' => $status ) );
						} else {
							if ( ! wc_string_to_bool( $enabled ) ) {
								// Gateway enabled.
								$this->plugin->alerts->Trigger( 9074, array(
									'GatewayID'   => $gateway->id,
									'GatewayName' => $gateway->title,
								) );
							} else {
								// Gateway disabled.
								$this->plugin->alerts->Trigger( 9075, array(
									'GatewayID'   => $gateway->id,
									'GatewayName' => $gateway->title,
								) );
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Get Stock Status Name.
	 *
	 * @param string $slug - Stock slug.
	 * @return string
	 */
	private function GetStockStatusName( $slug ) {
		if ( 'instock' === $slug ) {
			return __( 'In stock', 'wp-security-audit-log' );
		} elseif ( 'outofstock' === $slug ) {
			return __( 'Out of stock', 'wp-security-audit-log' );
		} elseif ( 'onbackorder' === $slug ) {
			return __( 'On backorder', 'wp-security-audit-log' );
		}
	}

	/**
	 * Return: Product Categories.
	 *
	 * @param object $post - Product post object.
	 * @return array
	 */
	protected function GetProductCategories( $post ) {
		return wp_get_post_terms(
			$post->ID, 'product_cat', array(
				'fields' => 'names',
			)
		);
	}

	/**
	 * Return: Product Data.
	 *
	 * @param object $post - Product post object.
	 * @return array
	 */
	protected function GetProductData( $post ) {
		return wp_get_post_terms(
			$post->ID, 'product_type', array(
				'fields' => 'names',
			)
		);
	}

	/**
	 * Get the config setting
	 *
	 * @param string $option_name - Option Name.
	 * @return string
	 */
	private function GetConfig( $option_name ) {
		$fn = $this->IsMultisite() ? 'get_site_option' : 'get_option';
		return $fn( 'woocommerce_' . $option_name );
	}

	/**
	 * Check post type.
	 *
	 * @param stdClass $post - Post.
	 * @return bool
	 */
	private function CheckWooCommerce( $post ) {
		switch ( $post->post_type ) {
			case 'product':
				return true;
			default:
				return false;
		}
	}

	/**
	 * Get editor link.
	 *
	 * @param stdClass $post      - The post.
	 * @return array $editor_link - Name and value link.
	 */
	private function GetEditorLink( $post ) {
		// Meta value key.
		if ( 'shop_order' === $post->post_type ) {
			$name = 'EditorLinkOrder';
		} else {
			$name = 'EditorLinkProduct';
		}

		// Get editor post link URL.
		$value = get_edit_post_link( $post->ID );

		// If the URL is not empty then set values.
		if ( ! empty( $value ) ) {
			$editor_link = array(
				'name'  => $name, // Meta key.
				'value' => $value, // Meta value.
			);
		} else {
			// Get post object.
			$post = get_post( $post->ID );

			// Set URL action.
			if ( 'revision' === $post->post_type ) {
				$action = '';
			} else {
				$action = '&action=edit';
			}

			// Get and check post type object.
			$post_type_object = get_post_type_object( $post->post_type );
			if ( ! $post_type_object ) {
				return;
			}

			// Set editor link manually.
			if ( $post_type_object->_edit_link ) {
				$link = admin_url( sprintf( $post_type_object->_edit_link . $action, $post->ID ) );
			} else {
				$link = '';
			}

			$editor_link = array(
				'name'  => $name, // Meta key.
				'value' => $link, // Meta value.
			);
		}

		return $editor_link;
	}

	/**
	 * Alerts for editing of product post type for WooCommerce.
	 *
	 * @param WP_Post $product - Product post type.
	 */
	public function editing_product( $product ) {
		// Check product post type.
		if ( 'product' !== $product->post_type ) {
			return $product;
		}

		if ( is_user_logged_in() && is_admin() ) {
			// Filter $_SERVER array for security.
			$server_array = filter_input_array( INPUT_SERVER );

			$current_path = isset( $server_array['SCRIPT_NAME'] ) ? $server_array['SCRIPT_NAME'] . '?post=' . $product->ID : false;
			if ( ! empty( $server_array['HTTP_REFERER'] )
				&& strpos( $server_array['HTTP_REFERER'], $current_path ) !== false ) {
				// Ignore this if we were on the same page so we avoid double audit entries.
				return $product;
			}
			if ( ! empty( $product->post_title ) ) {
				$event = 9072;
				if ( ! $this->WasTriggered( $event ) ) {
					$editor_link = $this->GetEditorLink( $product );
					$this->plugin->alerts->Trigger(
						$event, array(
							'PostID'             => $product->ID,
							'PostType'           => $product->post_type,
							'ProductStatus'      => $product->post_status,
							'ProductTitle'       => $product->post_title,
							'ProductUrl'         => get_permalink( $product->ID ),
							$editor_link['name'] => $editor_link['value'],
						)
					);
				}
			}
		}
		return $product;
	}

	/**
	 * Check if the alert was triggered.
	 *
	 * @param integer $alert_id - Alert code.
	 * @return boolean
	 */
	private function WasTriggered( $alert_id ) {
		$query = new WSAL_Models_OccurrenceQuery();
		$query->addOrderBy( 'created_on', true );
		$query->setLimit( 1 );
		$last_occurence = $query->getAdapter()->Execute( $query );
		if ( ! empty( $last_occurence ) ) {
			if ( $last_occurence[0]->alert_id === $alert_id ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get Currency symbol.
	 *
	 * @param string $currency - Currency (default: '').
	 * @return string
	 */
	private function GetCurrencySymbol( $currency = '' ) {
		$symbols         = array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => '&fnof;',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x10da;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'Kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => 'KZT',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'L',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRO' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => '&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/.',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#x434;&#x438;&#x43d;.',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STD' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'Fr',
			'XCD' => '&#36;',
			'XOF' => 'Fr',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);
		$currency_symbol = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';

		return $currency_symbol;
	}

	/**
	 * Formulate Order Title as done by WooCommerce.
	 *
	 * @since 3.3.1
	 *
	 * @param int|WC_Order $order - Order id or WC Order object.
	 * @return string
	 */
	private function get_order_title( $order ) {
		if ( ! $order ) {
			return false;
		}
		if ( is_integer( $order ) ) {
			$order = new WC_Order( $order );
		}
		if ( ! $order instanceof WC_Order ) {
			return false;
		}

		$buyer = '';
		if ( $order->get_billing_first_name() || $order->get_billing_last_name() ) {
			$buyer = trim( sprintf( '%1$s %2$s', $order->get_billing_first_name(), $order->get_billing_last_name() ) );
		} elseif ( $order->get_billing_company() ) {
			$buyer = trim( $order->get_billing_company() );
		} elseif ( $order->get_customer_id() ) {
			$user  = get_user_by( 'id', $order->get_customer_id() );
			$buyer = ucwords( $user->display_name );
		}
		return '#' . $order->get_order_number() . ' ' . $buyer;
	}

	/**
	 * WooCommerce Order Status Changed Event.
	 *
	 * @since 3.3.1
	 *
	 * @param integer  $order_id    – Order ID.
	 * @param string   $status_from – Status changing from.
	 * @param string   $status_to   – Status changing to.
	 * @param WC_Order $order       – WooCommerce order object.
	 */
	public function event_order_status_changed( $order_id, $status_from, $status_to, $order ) {
		$order_post  = get_post( $order_id ); // Get order post object.
		$order_title = ( null !== $order_post && $order_post instanceof WP_Post ) ? $order_post->post_title : false;
		$order_post  = get_post( $order_id );
		$edit_link   = $this->GetEditorLink( $order_post );
		$event_data  = array(
			'OrderID'          => $order_id,
			'OrderTitle'       => $this->get_order_title( $order ),
			'OrderStatus'      => $status_to,
			$edit_link['name'] => $edit_link['value'],
		);
		$this->plugin->alerts->TriggerIf( 9036, $event_data, array( $this, 'must_not_contain_refund' ) );
	}

	/**
	 * Checks if event 9041 has triggered or if it will
	 * trigger.
	 *
	 * @since 3.3.1.1
	 *
	 * @param WSAL_AlertManager $manager - Alert manager instance.
	 * @return boolean
	 */
	public function must_not_contain_refund( WSAL_AlertManager $manager ) {
		if ( $manager->WillOrHasTriggered( 9041 ) ) {
			return false;
		}
		return true;
	}

	/**
	 * WooCommerce Order Modified.
	 *
	 * @since 3.3.1
	 *
	 * @param integer $order_id – Order id.
	 * @param WP_Post $oldorder – Old order WP_Post object.
	 * @param WP_Post $neworder – New order WP_Post object.
	 */
	private function check_order_modify_change( $order_id, $oldorder, $neworder ) {
		if ( 'trash' === $neworder->post_status ) {
			return 0;
		}

		// Get editor link.
		$edit_link = $this->GetEditorLink( $oldorder );

		// Set event data.
		$event_data = array(
			'OrderID'          => $order_id,
			'OrderTitle'       => $this->get_order_title( $order_id ),
			'OrderStatus'      => $neworder->post_status,
			$edit_link['name'] => $edit_link['value'],
		);

		// Log event.
		$this->plugin->alerts->TriggerIf( 9040, $event_data, array( $this, 'must_not_contain_refund' ) );
	}

	/**
	 * WooCommerce Bulk Order Modified.
	 *
	 * @since 3.3.1
	 *
	 * @param array  $order_ids – Bulk Order ids.
	 * @param string $action    – Bulk action to perform.
	 * @return array
	 */
	public function event_bulk_order_actions( $order_ids, $action ) {
		// Check for remove personal data action.
		if ( 'remove_personal_data' === $action ) {
			foreach ( $order_ids as $order_id ) {
				$order_post = get_post( $order_id );

				// Get editor link.
				$edit_link = $this->GetEditorLink( $order_post );

				// Log event.
				$this->plugin->alerts->Trigger( 9040, array(
					'OrderID'          => $order_id,
					'OrderTitle'       => $this->get_order_title( $order_id ),
					'OrderStatus'      => $order_post->post_status,
					$edit_link['name'] => $edit_link['value'],
				) );
			}
		}
		return $order_ids;
	}

	/**
	 * WooCommerce Order Refunded.
	 *
	 * @since 3.3.1
	 *
	 * @param integer $order_id  – Order ID.
	 * @param integer $refund_id – Refund ID.
	 */
	public function event_order_refunded( $order_id, $refund_id ) {
		// Get order post object.
		$order_obj = get_post( $order_id );
		$edit_link = $this->GetEditorLink( $order_obj );

		$this->plugin->alerts->Trigger( 9041, array(
			'OrderID'          => $order_id,
			'RefundID'         => $refund_id,
			'OrderTitle'       => $this->get_order_title( $order_id ),
			'OrderStatus'      => $order_obj->post_status,
			$edit_link['name'] => $edit_link['value'],
		) );
	}

	/**
	 * WooCommerce New Attribute Event.
	 *
	 * @since 3.3.1
	 *
	 * @param int   $attr_id   - Attribute ID.
	 * @param array $attr_data - Attribute data array.
	 */
	public function event_attribute_added( $attr_id, $attr_data ) {
		if ( $attr_id && is_array( $attr_data ) ) {
			$this->plugin->alerts->Trigger( 9057, $this->get_attribute_event_data( $attr_id, $attr_data ) );
		}
	}

	/**
	 * WooCommerce Attribute Deleted Event.
	 *
	 * @since 3.3.1
	 *
	 * @param int    $id       - Attribute ID.
	 * @param string $name     - Attribute name.
	 * @param string $taxonomy - Attribute taxonomy name.
	 */
	public function event_attribute_deleted( $id, $name, $taxonomy ) {
		// Get the attribute.
		$attribute = wc_get_attribute( $id );

		// Check id and attribute object.
		if ( $id && ! is_null( $attribute ) ) {
			$this->plugin->alerts->Trigger( 9058, array(
				'AttributeID'      => $id,
				'AttributeName'    => isset( $attribute->name ) ? $attribute->name : false,
				'AttributeSlug'    => isset( $attribute->slug ) ? str_replace( 'pa_', '', $attribute->slug ) : false,
				'AttributeType'    => isset( $attribute->type ) ? $attribute->type : false,
				'AttributeOrderby' => isset( $attribute->order_by ) ? $attribute->order_by : false,
				'AttributePublic'  => isset( $attribute->has_archives ) ? $attribute->has_archives : '0',
				'Taxonomy'         => $taxonomy,
			) );
		}
	}

	/**
	 * Retrieve Attribute Data before editing.
	 *
	 * @since 3.3.1
	 */
	private function retrieve_attribute_data() {
		// @codingStandardsIgnoreStart
		$save_attribute = isset( $_POST['save_attribute'] ) ? true : false;
		$post_type      = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : false;
		$page           = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : false;
		$attribute_id   = isset( $_GET['edit'] ) ? absint( sanitize_text_field( wp_unslash( $_GET['edit'] ) ) ) : false;
		// @codingStandardsIgnoreEnd

		if ( $save_attribute && ! empty( $post_type ) && ! empty( $page ) && ! empty( $attribute_id ) && 'product' === $post_type && 'product_attributes' === $page ) {
			// Verify nonce.
			check_admin_referer( 'woocommerce-save-attribute_' . $attribute_id );

			// Get attribute data.
			$this->old_attr_data = wc_get_attribute( $attribute_id );
		}
	}

	/**
	 * WooCommerce Attribute Updated Events.
	 *
	 * @since 3.3.1
	 *
	 * @param int    $id       - Added attribute ID.
	 * @param array  $data     - Attribute data.
	 * @param string $old_slug - Attribute old name.
	 */
	public function event_attribute_updated( $id, $data, $old_slug ) {
		// Check the attribute slug.
		if ( isset( $data['attribute_name'] ) && $data['attribute_name'] !== $old_slug ) {
			$attr_event            = $this->get_attribute_event_data( $id, $data );
			$attr_event['OldSlug'] = $old_slug;
			$attr_event['NewSlug'] = $data['attribute_name'];
			$this->plugin->alerts->Trigger( 9059, $attr_event );
		}

		// Check the attribute name.
		if ( isset( $data['attribute_label'] ) && isset( $this->old_attr_data->name ) && $data['attribute_label'] !== $this->old_attr_data->name ) {
			$attr_event            = $this->get_attribute_event_data( $id, $data );
			$attr_event['OldName'] = $this->old_attr_data->name;
			$attr_event['NewName'] = $data['attribute_label'];
			$this->plugin->alerts->Trigger( 9060, $attr_event );
		}

		// Check the attribute orderby.
		if ( isset( $data['attribute_orderby'] ) && isset( $this->old_attr_data->order_by ) && $data['attribute_orderby'] !== $this->old_attr_data->order_by ) {
			$attr_event                 = $this->get_attribute_event_data( $id, $data );
			$attr_event['OldSortOrder'] = $this->old_attr_data->order_by;
			$attr_event['NewSortOrder'] = $data['attribute_orderby'];
			$this->plugin->alerts->Trigger( 9061, $attr_event );
		}

		// Check the attribute archives.
		if ( isset( $data['attribute_public'] ) && isset( $this->old_attr_data->has_archives ) && $data['attribute_public'] !== (int) $this->old_attr_data->has_archives ) {
			$attr_event                   = $this->get_attribute_event_data( $id, $data );
			$attr_event['ArchivesStatus'] = 1 === $data['attribute_public'] ? 'Enabled' : 'Disabled';
			$this->plugin->alerts->Trigger( 9062, $attr_event );
		}
	}

	/**
	 * Return Attribute Events Data.
	 *
	 * @since 3.3.1
	 *
	 * @param int   $attr_id - Added attribute ID.
	 * @param array $data    - Attribute data.
	 * @return array
	 */
	private function get_attribute_event_data( $attr_id, $data ) {
		return array(
			'AttributeID'      => $attr_id,
			'AttributeName'    => isset( $data['attribute_label'] ) ? $data['attribute_label'] : false,
			'AttributeSlug'    => isset( $data['attribute_name'] ) ? $data['attribute_name'] : false,
			'AttributeType'    => isset( $data['attribute_type'] ) ? $data['attribute_type'] : false,
			'AttributeOrderby' => isset( $data['attribute_orderby'] ) ? $data['attribute_orderby'] : false,
			'AttributePublic'  => isset( $data['attribute_public'] ) ? $data['attribute_public'] : '0',
		);
	}

	/**
	 * Check AJAX changes for WooCommerce.
	 *
	 * @since 3.3.1
	 */
	private function check_wc_ajax_change_events() {
		// @codingStandardsIgnoreStart
		$action  = isset( $_POST['action'] ) ? sanitize_text_field( wp_unslash( $_POST['action'] ) ) : false;
		$is_data = isset( $_POST['data'] ) ? true : false;
		// @codingStandardsIgnoreEnd

		// WooCommerce order actions.
		$wc_order_actions = array(
			'woocommerce_add_order_item',
			'woocommerce_save_order_items',
			'woocommerce_remove_order_item',
			'woocommerce_add_coupon_discount',
			'woocommerce_remove_order_coupon',
		);

		// Check for save attributes action.
		if ( $is_data && 'woocommerce_save_attributes' === $action ) {
			// Check nonce.
			check_ajax_referer( 'save-attributes', 'security' );

			$post_id = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : false;
			if ( ! $post_id ) {
				return;
			}

			$post = get_post( $post_id );
			if ( ! $post ) {
				return;
			}

			// Get the attributes data.
			parse_str( $_POST['data'], $data );
			$this->check_attributes_change( $post, $data );
		} elseif ( in_array( $action, $wc_order_actions, true ) ) {
			// Check nonce.
			check_ajax_referer( 'order-item', 'security' );

			// Get order ID.
			$order_id = isset( $_POST['order_id'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['order_id'] ) ) ) : false;
			if ( ! $order_id ) {
				return;
			}

			// Get order post.
			$order = get_post( $order_id );

			// Get editor link.
			$edit_link = $this->GetEditorLink( $order );

			// Log event.
			$this->plugin->alerts->Trigger( 9040, array(
				'OrderID'          => $order_id,
				'OrderTitle'       => $this->get_order_title( $order_id ),
				'OrderStatus'      => isset( $order->post_status ) ? $order->post_status : false,
				$edit_link['name'] => $edit_link['value'],
			) );
		}
	}

	/**
	 * Check Product Attributes Change.
	 *
	 * @since 3.3.1
	 *
	 * @param WP_Post $oldpost - WP Post type object.
	 * @param array   $data    - Data array.
	 * @return int
	 */
	private function check_attributes_change( $oldpost, $data = false ) {
		$post_attributes = get_post_meta( $oldpost->ID, '_product_attributes', true ); // Get post attribute meta.
		$post_attributes = ! $post_attributes ? array() : $post_attributes;

		if ( ! $data ) {
			// @codingStandardsIgnoreStart
			$data = $_POST;
			// @codingStandardsIgnoreEnd
		}

		$attribute_names      = isset( $data['attribute_names'] ) ? array_map( 'sanitize_text_field', wp_unslash( $data['attribute_names'] ) ) : false;
		$attribute_position   = isset( $data['attribute_position'] ) ? array_map( 'sanitize_text_field', wp_unslash( $data['attribute_position'] ) ) : false;
		$attribute_visibility = isset( $data['attribute_visibility'] ) ? array_map( 'sanitize_text_field', wp_unslash( $data['attribute_visibility'] ) ) : false;
		$attribute_values     = isset( $data['attribute_values'] ) ? $data['attribute_values'] : false;

		if ( ! empty( $attribute_names ) && ! empty( $attribute_values ) ) {
			$new_attributes = array();
			foreach ( $attribute_names as $key => $name ) {
				$attr_key                    = $this->get_attribute_key( $name );
				$new_attributes[ $attr_key ] = array(
					'name'       => $name,
					'value'      => isset( $attribute_values[ $key ] ) ? $this->get_string_attribute_value( $attribute_values[ $key ] ) : false,
					'position'   => isset( $attribute_position[ $key ] ) ? $attribute_position[ $key ] : false,
					'is_visible' => isset( $attribute_visibility[ $key ] ) ? $attribute_visibility[ $key ] : false,
				);
			}

			// Compare old and new attributes.
			$added_attributes   = array_diff_key( $new_attributes, $post_attributes );
			$deleted_attributes = array_diff_key( $post_attributes, $new_attributes );

			// Get product editor link.
			$editor_link = $this->GetEditorLink( $oldpost );

			// Result.
			$result = 0;

			// Event 9047.
			if ( ! empty( $added_attributes ) ) {
				foreach ( $added_attributes as $added_attribute ) {
					if ( $added_attribute && ! empty( $added_attribute['name'] ) ) {
						$this->plugin->alerts->Trigger( 9047, array(
							'AttributeName'      => $added_attribute['name'],
							'AttributeValue'     => $added_attribute['value'],
							'ProductID'          => $oldpost->ID,
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							$editor_link['name'] => $editor_link['value'],
						) );
						$result = 1;
					}
				}
			}

			// Event 9050.
			if ( ! empty( $deleted_attributes ) ) {
				foreach ( $deleted_attributes as $deleted_attribute ) {
					$this->plugin->alerts->Trigger( 9050, array(
						'AttributeName'      => $deleted_attribute['name'],
						'AttributeValue'     => $deleted_attribute['value'],
						'ProductID'          => $oldpost->ID,
						'ProductTitle'       => $oldpost->post_title,
						'ProductStatus'      => $oldpost->post_status,
						'ProductUrl'         => get_permalink( $oldpost->ID ),
						$editor_link['name'] => $editor_link['value'],
					) );
					$result = 1;
				}
			}

			// Event 9048, 9049 & 9051.
			if ( ! empty( $new_attributes ) ) {
				foreach ( $new_attributes as $attr_key => $new_attr ) {
					// Get old attribute value.
					$old_value = '';
					if ( false !== strpos( $attr_key, 'pa_' ) ) {
						$old_value = $this->get_wc_product_attributes( $oldpost, $attr_key );
					} else {
						$old_value = isset( $post_attributes[ $attr_key ]['value'] ) ? $post_attributes[ $attr_key ]['value'] : false;
					}
					$new_value = isset( $new_attr['value'] ) ? $new_attr['value'] : false; // Get new attribute value.

					// Get old and new attribute names.
					$old_name = isset( $post_attributes[ $attr_key ]['name'] ) ? $post_attributes[ $attr_key ]['name'] : false;
					$new_name = isset( $new_attr['name'] ) ? $new_attr['name'] : false;

					// Get old and new attribute visibility.
					$old_visible = isset( $post_attributes[ $attr_key ]['is_visible'] ) ? (int) $post_attributes[ $attr_key ]['is_visible'] : false;
					$new_visible = isset( $new_attr['is_visible'] ) ? (int) $new_attr['is_visible'] : false;

					// Value change.
					if ( $old_value && $new_value && $old_value !== $new_value ) {
						$this->plugin->alerts->Trigger( 9048, array(
							'AttributeName'      => $new_attr['name'],
							'OldValue'           => $old_value,
							'NewValue'           => $new_value,
							'ProductID'          => $oldpost->ID,
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							$editor_link['name'] => $editor_link['value'],
						) );
						$result = 1;
					}

					// Name change.
					if ( $old_name && $new_name && $old_name !== $new_name ) {
						$this->plugin->alerts->Trigger( 9049, array(
							'AttributeName'      => $new_attr['name'],
							'OldValue'           => $old_name,
							'NewValue'           => $new_name,
							'ProductID'          => $oldpost->ID,
							'ProductTitle'       => $oldpost->post_title,
							'ProductStatus'      => $oldpost->post_status,
							'ProductUrl'         => get_permalink( $oldpost->ID ),
							$editor_link['name'] => $editor_link['value'],
						) );
						$result = 1;
					}

					// Visibility change.
					if ( ! empty( $new_attr['name'] ) && $old_visible !== $new_visible ) {
						$this->plugin->alerts->Trigger( 9051, array(
							'AttributeName'       => $new_attr['name'],
							'AttributeVisiblilty' => 1 === $new_visible ? __( 'Visible', 'wp-security-audit-log' ) : __( 'Non-Visible', 'wp-security-audit-log' ),
							'ProductID'           => $oldpost->ID,
							'ProductTitle'        => $oldpost->post_title,
							'ProductStatus'       => $oldpost->post_status,
							$editor_link['name']  => $editor_link['value'],
						) );
						$result = 1;
					}
				}
			}
			return $result;
		}
		return 0;
	}

	/**
	 * Returns the attribute key using attribute name.
	 *
	 * @since 3.3.1
	 *
	 * @param string $attribute_name - Attribute name.
	 * @return string
	 */
	private function get_attribute_key( $attribute_name = '' ) {
		return str_replace( ' ', '-', strtolower( $attribute_name ) );
	}

	/**
	 * Return the attribute value string using WooCommerce attribute value field.
	 *
	 * @since 3.3.1
	 *
	 * @param string $attribute_value - WooCommerce attribute value field.
	 * @return string
	 */
	private function get_string_attribute_value( $attribute_value = '' ) {
		// New attribute value.
		$new_attr_value = '';

		// Check if incoming value is string.
		if ( is_string( $attribute_value ) ) {
			$new_attr_value = sanitize_text_field( wp_unslash( $attribute_value ) );
		} elseif ( is_array( $attribute_value ) ) {
			// If the incoming value is an array, it is an array of term ids.
			$term_attr_values = array_map( 'sanitize_text_field', wp_unslash( $attribute_value ) );

			$new_attr_value = array();
			foreach ( $term_attr_values as $term_id ) {
				$term = get_term( $term_id ); // Get term.
				if ( null !== $term && ! is_wp_error( $term ) ) {
					$new_attr_value[] = $term->name;
				}
			}
			$new_attr_value = implode( ' | ', $new_attr_value );
		}
		return $new_attr_value;
	}

	/**
	 * Return product attribute value.
	 *
	 * @since 3.3.1
	 *
	 * @param WP_Post $product  - Product post object.
	 * @param string  $taxonomy - Taxonomy.
	 * @return string
	 */
	private function get_wc_product_attributes( $product, $taxonomy ) {
		$attribute_terms = wp_get_post_terms( $product->ID, $taxonomy );
		$product_attrs   = array();

		if ( ! is_wp_error( $attribute_terms ) ) {
			foreach ( $attribute_terms as $single_term ) {
				$product_attrs[] = $single_term->term_id;
			}
		}
		return $this->get_string_attribute_value( $product_attrs );
	}

	/**
	 * Check Product Category Updated Events.
	 *
	 * @since 3.3.1
	 *
	 * @param array  $data     - Term data to be updated.
	 * @param int    $term_id  - Term ID.
	 * @param string $taxonomy - Taxonomy slug.
	 * @param array  $args     - Arguments passed to wp_update_term().
	 */
	public function event_product_cat_updated( $data, $term_id, $taxonomy, $args ) {
		// Check if the taxonomy is `product_cat`.
		if ( 'product_cat' === $taxonomy ) {
			// Get term data.
			$new_name      = isset( $data['name'] ) ? $data['name'] : false;
			$new_slug      = isset( $data['slug'] ) ? $data['slug'] : false;
			$new_parent_id = isset( $args['parent'] ) ? $args['parent'] : false;

			// New parent category.
			$new_parent_cat = false;
			if ( 0 !== $new_parent_id ) {
				$new_parent_cat = get_term( $new_parent_id, $taxonomy );
			}

			// Get old data.
			$term     = get_term( $term_id, $taxonomy );
			$old_name = $term->name;
			$old_slug = $term->slug;

			// Old parent category.
			$old_parent_cat = false;
			if ( $term->parent ) {
				$old_parent_cat = get_term( $term->parent, $taxonomy );
			}

			// Update if both slugs are not same.
			if ( $old_slug !== $new_slug ) {
				$this->plugin->alerts->Trigger( 9053, array(
					'CategoryID'   => $term_id,
					'CategoryName' => $new_name,
					'OldSlug'      => $old_slug,
					'NewSlug'      => $new_slug,
				) );
			}

			// Update if both parent categories are not same.
			if ( $term->parent !== $new_parent_id ) {
				$this->plugin->alerts->Trigger( 9054, array(
					'CategoryID'   => $term_id,
					'CategoryName' => $new_name,
					'OldParentID'  => isset( $old_parent_cat->term_id ) ? $old_parent_cat->term_id : false,
					'OldParentCat' => isset( $old_parent_cat->name ) ? $old_parent_cat->name : false,
					'NewParentID'  => isset( $new_parent_cat->term_id ) ? $new_parent_cat->term_id : false,
					'NewParentCat' => isset( $new_parent_cat->name ) ? $new_parent_cat->name : false,
				) );
			}

			// Update if both names are not same.
			if ( $old_name !== $new_name ) {
				$this->plugin->alerts->Trigger( 9056, array(
					'CategoryID'   => $term_id,
					'CategoryName' => $new_name,
					'OldName'      => $old_name,
					'NewName'      => $new_name,
				) );
			}
		}
		return $data;
	}

	/**
	 * Check Product Category Display Type Meta Event.
	 *
	 * @since 3.3.1
	 *
	 * @param int    $meta_id    - ID of the metadata entry to update.
	 * @param int    $object_id  - Object ID.
	 * @param string $meta_key   - Meta key.
	 * @param mixed  $meta_value - Meta value.
	 */
	public function event_cat_display_updated( $meta_id, $object_id, $meta_key, $meta_value ) {
		// Check `display_type` meta key.
		if ( 'display_type' !== $meta_key ) {
			return;
		}

		// Get previous value.
		$old_display = get_term_meta( $object_id, $meta_key, true );

		// Get term.
		$term = get_term( $object_id, 'product_cat' );

		// Check if display type changed.
		if ( $meta_value !== $old_display ) {
			$this->plugin->alerts->Trigger( 9055, array(
				'CategoryID'     => $object_id,
				'CategoryName'   => $term->name,
				'OldDisplayType' => $old_display,
				'NewDisplayType' => $meta_value,
			) );
		}
	}

	/**
	 * Check Product Category Deletion Event.
	 *
	 * @since 3.3.1
	 *
	 * @param int   $term_id      - Term ID.
	 * @param int   $tt_id        - Term taxonomy ID.
	 * @param mixed $deleted_term - Copy of the already-deleted term, in the form specified by the parent function. WP_Error otherwise.
	 * @param array $object_ids   - List of term object IDs.
	 */
	public function event_product_cat_deleted( $term_id, $tt_id, $deleted_term, $object_ids ) {
		if ( 'product_cat' === $deleted_term->taxonomy ) {
			$this->plugin->alerts->Trigger( 9052, array(
				'CategoryID'   => $deleted_term->term_id,
				'CategoryName' => $deleted_term->name,
				'CategorySlug' => $deleted_term->slug,
			) );
		}
	}

	/**
	 * Check Created Events for Coupon Meta.
	 *
	 * @since 3.3.1
	 *
	 * @param bool    $log_event  - True if log event 2053 for coupon meta, false if not.
	 * @param string  $meta_key   - Meta key.
	 * @param mixed   $meta_value - Meta value.
	 * @param WP_Post $coupon     - Coupon CPT object.
	 * @return bool
	 */
	public function log_coupon_meta_created_event( $log_event, $meta_key, $meta_value, $coupon ) {
		if ( ! empty( $meta_key ) && 'shop_coupon' === $coupon->post_type && in_array( $meta_key, $this->coupon_meta, true ) ) {
			return false;
		}
		return $log_event;
	}

	/**
	 * Check Updated Events for Coupon Meta.
	 *
	 * @since 3.3.1
	 *
	 * @param bool     $log_meta_event - True if log meta events 2054 or 2062, false if not.
	 * @param string   $meta_key       - Meta key.
	 * @param mixed    $meta_value     - Meta value.
	 * @param stdClass $old_meta_obj   - Old meta value and key object.
	 * @param WP_Post  $coupon         - Coupon CPT object.
	 * @return bool
	 */
	public function log_coupon_meta_update_events( $log_meta_event, $meta_key, $meta_value, $old_meta_obj, $coupon ) {
		// If meta key does not match with any coupon meta key, then return.
		if ( ! empty( $meta_key ) && ( ! in_array( $meta_key, $this->coupon_meta, true ) || 'shop_coupon' !== $coupon->post_type ) ) {
			return $log_meta_event;
		}

		$ignore_coupon_meta     = array( 'date_expires', 'usage_count', 'free_shipping' ); // Ignore these meta keys.
		$usage_restriction_meta = array( 'individual_use', 'product_ids', 'exclude_product_ids', 'product_categories', 'exclude_product_categories', 'exclude_sale_items', 'minimum_amount', 'maximum_amount', 'customer_email' ); // Event 9067.
		$usage_limits_meta      = array( 'usage_limit', 'usage_limit_per_user', 'limit_usage_to_x_items' ); // Event 9068.

		if ( in_array( $meta_key, $ignore_coupon_meta, true ) && $meta_value !== $old_meta_obj->val ) {
			return false;
		} elseif ( $meta_value !== $old_meta_obj->val ) {
			// Event id.
			$event_id = false;

			// Get coupon event data.
			$coupon_data = $this->get_coupon_event_data( $coupon );

			if ( 'discount_type' === $meta_key ) {
				// Set coupon discount type data.
				$coupon_data['OldDiscountType'] = isset( $old_meta_obj->val ) ? $old_meta_obj->val : false;
				$coupon_data['NewDiscountType'] = $meta_value;

				// Set event id.
				$event_id = 9064;
			} elseif ( 'coupon_amount' === $meta_key ) {
				// Set coupon amount data.
				$coupon_data['OldAmount'] = isset( $old_meta_obj->val ) ? $old_meta_obj->val : false;
				$coupon_data['NewAmount'] = $meta_value;

				// Set event id.
				$event_id = 9065;
			} elseif ( 'expiry_date' === $meta_key ) {
				// Set coupon expiry date data.
				$coupon_data['OldDate'] = isset( $old_meta_obj->val ) ? $old_meta_obj->val : false;
				$coupon_data['NewDate'] = $meta_value;

				// Set event id.
				$event_id = 9066;
			} elseif ( in_array( $meta_key, $usage_restriction_meta, true ) ) {
				// Set usage restriction meta data.
				$coupon_data['MetaKey']      = $meta_key;
				$coupon_data['OldMetaValue'] = isset( $old_meta_obj->val ) ? $old_meta_obj->val : false;
				$coupon_data['NewMetaValue'] = $meta_value;

				if ( false === $this->is_9067_logged ) {
					// Set event id.
					$event_id             = 9067;
					$this->is_9067_logged = true;
				}
			} elseif ( in_array( $meta_key, $usage_limits_meta, true ) ) {
				// Set usage limits meta data.
				$coupon_data['MetaKey']      = $meta_key;
				$coupon_data['OldMetaValue'] = isset( $old_meta_obj->val ) ? $old_meta_obj->val : false;
				$coupon_data['NewMetaValue'] = $meta_value;

				if ( false === $this->is_9068_logged ) {
					// Set event id.
					$event_id             = 9068;
					$this->is_9068_logged = true;
				}
			}

			if ( $event_id && ! empty( $coupon_data ) ) {
				// Log the event.
				$this->plugin->alerts->Trigger( $event_id, $coupon_data );
			}
		}
		return false;
	}

	/**
	 * Check Created Events for Coupon Meta.
	 *
	 * @since 3.3.1
	 *
	 * @param bool    $log_event  - True if log event 2055 for coupon meta, false if not.
	 * @param string  $meta_key   - Meta key.
	 * @param mixed   $meta_value - Meta value.
	 * @param WP_Post $coupon     - Coupon CPT object.
	 * @return bool
	 */
	public function log_coupon_meta_delete_event( $log_event, $meta_key, $meta_value, $coupon ) {
		if ( ! empty( $meta_key ) && 'shop_coupon' === $coupon->post_type && in_array( $meta_key, $this->coupon_meta, true ) ) {
			return false;
		}
		return $log_event;
	}
}
