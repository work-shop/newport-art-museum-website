<?php
/**
 * Post Types Admin
 *
 * @author   WooThemes
 * @category Admin
 * @package  WooCommerce/Admin
 * @version  2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WCCPT_Admin_Post_Types' ) ) :

/**
 * WCCPT_Admin_Post_Types Class.
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 */
class WCCPT_Admin_Post_Types {

	
	
	/**
	 * Constructor.
	 */
	
	public function __construct() {
		
		add_action( 'init', array( $this, 'init'), 99999 );
		add_action( 'init', array( $this, 'woocommerce_register_post_type' ), 5 );
		
		add_filter( 'woocommerce_product_class', array( $this, 'wccpt_product_class' ), 10, 4 );
	}
	
	public function wccpt_product_class( $classname, $product_type, $post_type, $product_id ) {
		
		if ( ( $post_type !== 'product' ) && WC_CPT_List::is_active( $post_type ) ) {
			$_product_type = get_the_terms( $product_id, 'product_type' );
			if ($_product_type[0]) {
				$product_type = $_product_type[0]->slug;
				$classname = 'WC_Product_' . implode( '_', array_map( 'ucfirst', explode( '-', $product_type ) ) );
			}
		}
		return $classname;
	}
	
	public function init( ){
		$cpt_list = WC_CPT_List::get( );
		foreach( $cpt_list as $cpt ) {
			if ( $cpt['active'] ) {
				new WCCPT_Admin_Post_Types_Helper( $cpt['slug'] );
			}
		}
	}
	
	public function woocommerce_register_post_type() {
		$cpt_list = array();
		$cpts = get_option( 'woorei_cpt_list' );
		if ( is_array( $cpts ) ) {
			foreach ( $cpts as $key => $post_type ) {
				$args = array(
					'labels'              => array(
							'name'                  => __( $post_type['menu_name'], 'woocommerce' ),
							'singular_name'         => __( $post_type['name'], 'woocommerce' ),
							'menu_name'             => _x( $post_type['menu_name'], 'Admin menu name', 'woocommerce' ),
							'add_new'               => __( 'Add ' . $post_type['name'], 'woocommerce' ),
							'add_new_item'          => __( 'Add New ' . $post_type['name'], 'woocommerce' ),
							'edit'                  => __( 'Edit', 'woocommerce' ),
							'edit_item'             => __( 'Edit ' . $post_type['name'], 'woocommerce' ),
							'new_item'              => __( 'New ' . $post_type['name'], 'woocommerce' ),
							'view'                  => __( 'View ' . $post_type['name'], 'woocommerce' ),
							'view_item'             => __( 'View ' . $post_type['name'], 'woocommerce' ),
							'search_items'          => __( 'Search ' . $post_type['menu_name'] . '', 'woocommerce' ),
							'not_found'             => __( 'No ' . $post_type['menu_name'] . ' found', 'woocommerce' ),
							'not_found_in_trash'    => __( 'No ' . $post_type['menu_name'] . ' found in trash', 'woocommerce' ),
							'parent'                => __( 'Parent ' . $post_type['name'], 'woocommerce' ),
							'featured_image'        => __( $post_type['name'] . ' Image', 'woocommerce' ),
							'set_featured_image'    => __( 'Set ' . $post_type['name'] . ' image', 'woocommerce' ),
							'remove_featured_image' => __( 'Remove ' . $post_type['name'] . ' image', 'woocommerce' ),
							'use_featured_image'    => __( 'Use as ' . $post_type['name'] . ' image', 'woocommerce' ),
						),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'product',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
					//'rewrite'             => $product_permalink ? array( 'slug' => untrailingslashit( $product_permalink ), 'with_front' => false, 'feeds' => true ) : false,
					'query_var'           => true,
					'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
					'has_archive'         => ( $shop_page_id = wc_get_page_id( $key ) ) && WC_CPT_List::is_active( $key ) && get_post( $shop_page_id ) ? get_page_uri( $shop_page_id ) : $key,
					'show_in_nav_menus'   => true
				);
				register_post_type( $key, apply_filters( 'wccpt_register_post_type_' . $key, $args ) );
			}
		}
	}
}

class WCCPT_Admin_Post_Types_Helper {
	
	private $cpt_slug;
	
	public function __construct( $cpt_slug = '' ){
		
		if ( empty($cpt_slug) ) return;
		
		$this->cpt_slug = $cpt_slug;
		
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// WP List table columns. Defined here so they are always available for events such as inline editing.
		add_filter( 'manage_' . $cpt_slug . '_posts_columns', array( $this, 'product_columns' ) );

		add_action( 'manage_' . $cpt_slug . '_posts_custom_column', array( $this, 'render_product_columns' ), 2 );

		add_filter( 'manage_edit-' . $cpt_slug . '_sortable_columns', array( $this, 'product_sortable_columns' ) );

		add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 2, 100 );

		// Views
		add_filter( 'views_edit-' . $cpt_slug, array( $this, 'product_sorting_link' ) );

		// Bulk / quick edit
		add_action( 'bulk_edit_custom_box', array( $this, 'bulk_edit' ), 10, 2 );
		add_action( 'quick_edit_custom_box',  array( $this, 'quick_edit' ), 10, 2 );
		add_action( 'save_post', array( $this, 'bulk_and_quick_edit_save_post' ), 10, 2 );
		add_action( 'load-edit.php', array( $this, 'bulk_action' ) );

		// Filters
		add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
		add_filter( 'request', array( $this, 'request_query' ) );
		add_filter( 'parse_query', array( $this, 'product_filters_query' ) );
		add_filter( 'posts_search', array( $this, 'product_search' ) );

		// Status transitions
		add_action( 'delete_post', array( $this, 'delete_post' ) );
		add_action( 'wp_trash_post', array( $this, 'trash_post' ) );
		add_action( 'untrash_post', array( $this, 'untrash_post' ) );

		// Edit post screens
		add_filter( 'media_view_strings', array( $this, 'change_insert_into_post' ) );
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );
		add_action( 'post_submitbox_misc_actions', array( $this, 'product_data_visibility' ) );

		// Uploads
		add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
		add_action( 'media_upload_downloadable_product', array( $this, 'media_upload_downloadable_product' ) );


		// Meta-Box Class
		// add product meta box to enabled Custom Post Types add/edit screens.
		require_once WCCPTM()->plugin_path() . '/includes/admin/custom-post-type-meta-box.php';

		// Download permissions
		add_action( 'woocommerce_process_product_file_download_paths', array( $this, 'process_product_file_download_paths' ), 10, 3 );

		// Disable DFW feature pointer
		add_action( 'admin_footer', array( $this, 'disable_dfw_feature_pointer' ) );

	}
	
	/**
	 * Change messages when a post type is updated.
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;
		
		if ( ! WC_CPT_List::is_active( $post->post_type ) ) return $messages;
		
		$post_type = get_post_type_object( $post->post_type );
		$label = $post_type->labels->singular_name;

		$messages[$post->post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( '%2$s updated. <a href="%1$s">View %2$s</a>', 'woocommerce' ), esc_url( get_permalink( $post_ID ) ), $label ),
			2 => __( 'Custom field updated.', 'woocommerce' ),
			3 => __( 'Custom field deleted.', 'woocommerce' ),
			4 => sprintf( __( '%s updated.', 'woocommerce' ), $label ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( '%2$s restored to revision from %1$s', 'woocommerce' ), wp_post_revision_title( (int) $_GET['revision'], false ), $label ) : false,
			6 => sprintf( __( '%2$s published. <a href="%1$s">View %2$s</a>', 'woocommerce' ), esc_url( get_permalink( $post_ID ) ), $label ),
			7 => sprintf( __( '%s saved.', 'woocommerce' ), $label ),
			8 => sprintf( __( '%2$s submitted. <a target="_blank" href="%1$s">Preview %2$s</a>', 'woocommerce' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), $label ),
			9 => sprintf( __( '%3$s scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview %3$s</a>', 'woocommerce' ),
			  date_i18n( __( 'M j, Y @ G:i', 'woocommerce' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ), $label ),
			10 => sprintf( __( '%2$s draft updated. <a target="_blank" href="%1$s">Preview %2$s</a>', 'woocommerce' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), $label ),
		);



		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 * @param  array $bulk_messages
	 * @param  array $bulk_counts
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
		global $post;
		
		if (empty($post)) {	return; }
		
		if ( ! WC_CPT_List::is_active( $post->post_type ) ) return $bulk_messages;
		
		$post_type = get_post_type_object( $post->post_type );
		$label = $post_type->labels->singular_name;

		$bulk_messages[$post->post_type] = array(
			'updated'   => _n( '%s ' . $label . ' updated.', '%s ' . $label . 's updated.', $bulk_counts['updated'], 'woocommerce' ),
			'locked'    => _n( '%s ' . $label . ' not updated, somebody is editing it.', '%s ' . $label . 's not updated, somebody is editing them.', $bulk_counts['locked'], 'woocommerce' ),
			'deleted'   => _n( '%s ' . $label . ' permanently deleted.', '%s ' . $label . 's permanently deleted.', $bulk_counts['deleted'], 'woocommerce' ),
			'trashed'   => _n( '%s ' . $label . ' moved to the Trash.', '%s ' . $label . 's moved to the Trash.', $bulk_counts['trashed'], 'woocommerce' ),
			'untrashed' => _n( '%s ' . $label . ' restored from the Trash.', '%s ' . $label . 's restored from the Trash.', $bulk_counts['untrashed'], 'woocommerce' ),
		);

		return $bulk_messages;
	}

	/**
	 * Define custom columns for products.
	 * @param  array $existing_columns
	 * @return array
	 */
	public function product_columns( $existing_columns ) {
		
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}

		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns          = array();
		$columns['cb']    = '<input type="checkbox" />';
		$columns['thumb'] = '<span class="wc-image tips" data-tip="' . esc_attr__( 'Image', 'woocommerce' ) . '">' . __( 'Image', 'woocommerce' ) . '</span>';
		$columns['name']  = __( 'Name', 'woocommerce' );

		if ( wc_product_sku_enabled() ) {
			$columns['sku'] = __( 'SKU', 'woocommerce' );
		}

		if ( 'yes' == get_option( 'woocommerce_manage_stock' ) ) {
			$columns['is_in_stock'] = __( 'Stock', 'woocommerce' );
		}

		$columns['price']        = __( 'Price', 'woocommerce' );
		$columns['product_cat']  = __( 'Categories', 'woocommerce' );
		$columns['product_tag']  = __( 'Tags', 'woocommerce' );
		$columns['featured']     = '<span class="wc-featured parent-tips" data-tip="' . esc_attr__( 'Featured', 'woocommerce' ) . '">' . __( 'Featured', 'woocommerce' ) . '</span>';
		$columns['product_type'] = '<span class="wc-type parent-tips" data-tip="' . esc_attr__( 'Type', 'woocommerce' ) . '">' . __( 'Type', 'woocommerce' ) . '</span>';
		$columns['date']         = __( 'Date', 'woocommerce' );

		return array_merge( $columns, $existing_columns );

	}

	/**
	 * Ouput custom columns for products.
	 *
	 * @param string $column
	 */
	public function render_product_columns( $column ) {
		global $post, $the_product;

		if ( empty( $the_product ) || $the_product->get_id() != $post->ID ) {
			$the_product = wc_get_product( $post );
		}
		if ( empty( $the_product ) ) {
			$the_product = new WC_Product( $post->ID );
		}

		switch ( $column ) {
			case 'thumb' :
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . $the_product->get_image( 'thumbnail' ) . '</a>';
				break;
			case 'name' :
				$edit_link = get_edit_post_link( $post->ID );
				$title     = _draft_or_post_title();

				echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

				_post_states( $post );

				echo '</strong>';

				if ( $post->post_parent > 0 ) {
					echo '&nbsp;&nbsp;&larr; <a href="' . get_edit_post_link( $post->post_parent ) . '">' . get_the_title( $post->post_parent ) . '</a>';
				}

				// Excerpt view
				if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
					echo apply_filters( 'the_excerpt', $post->post_excerpt );
				}

				get_inline_data( $post );

				/* Custom inline data for woocommerce. */
				echo '
					<div class="hidden" id="woocommerce_inline_' . absint( $post->ID ) . '">
						<div class="menu_order">' . absint( $the_product->get_menu_order() ) . '</div>
						<div class="sku">' . esc_html( $the_product->get_sku() ) . '</div>
						<div class="regular_price">' . esc_html( $the_product->get_regular_price() ) . '</div>
						<div class="sale_price">' . esc_html( $the_product->get_sale_price() ) . '</div>
						<div class="weight">' . esc_html( $the_product->get_weight() ) . '</div>
						<div class="length">' . esc_html( $the_product->get_length() ) . '</div>
						<div class="width">' . esc_html( $the_product->get_width() ) . '</div>
						<div class="height">' . esc_html( $the_product->get_height() ) . '</div>
						<div class="shipping_class">' . esc_html( $the_product->get_shipping_class() ) . '</div>
						<div class="visibility">' . esc_html( $the_product->get_catalog_visibility() ) . '</div>
						<div class="stock_status">' . esc_html( $the_product->get_stock_status() ) . '</div>
						<div class="stock">' . esc_html( $the_product->get_stock_quantity() ) . '</div>
						<div class="manage_stock">' . esc_html( wc_bool_to_string( $the_product->get_manage_stock() ) ) . '</div>
						<div class="featured">' . esc_html( wc_bool_to_string( $the_product->get_featured() ) ) . '</div>
						<div class="product_type">' . esc_html( $the_product->get_type() ) . '</div>
						<div class="product_is_virtual">' . esc_html( wc_bool_to_string( $the_product->get_virtual() ) ) . '</div>
						<div class="tax_status">' . esc_html( $the_product->get_tax_status() ) . '</div>
						<div class="tax_class">' . esc_html( $the_product->get_tax_class() ) . '</div>
						<div class="backorders">' . esc_html( $the_product->get_backorders() ) . '</div>
					</div>
				';

			break;
			case 'sku' :
				echo $the_product->get_sku() ? esc_html( $the_product->get_sku() ) : '<span class="na">&ndash;</span>';
				break;
			case 'product_type' :
				if ( $the_product->is_type( 'grouped' ) ) {
					echo '<span class="product-type tips grouped" data-tip="' . esc_attr__( 'Grouped', 'woocommerce' ) . '"></span>';
				} elseif ( $the_product->is_type( 'external' ) ) {
					echo '<span class="product-type tips external" data-tip="' . esc_attr__( 'External/Affiliate', 'woocommerce' ) . '"></span>';
				} elseif ( $the_product->is_type( 'simple' ) ) {

					if ( $the_product->is_virtual() ) {
						echo '<span class="product-type tips virtual" data-tip="' . esc_attr__( 'Virtual', 'woocommerce' ) . '"></span>';
					} elseif ( $the_product->is_downloadable() ) {
						echo '<span class="product-type tips downloadable" data-tip="' . esc_attr__( 'Downloadable', 'woocommerce' ) . '"></span>';
					} else {
						echo '<span class="product-type tips simple" data-tip="' . esc_attr__( 'Simple', 'woocommerce' ) . '"></span>';
					}
				} elseif ( $the_product->is_type( 'variable' ) ) {
					echo '<span class="product-type tips variable" data-tip="' . esc_attr__( 'Variable', 'woocommerce' ) . '"></span>';
				} else {
					// Assuming that we have other types in future
					echo '<span class="product-type tips ' . esc_attr( sanitize_html_class( $the_product->get_type() ) ) . '" data-tip="' . esc_attr( ucfirst( $the_product->get_type() ) ) . '"></span>';
				}
				break;
			case 'price' :
				echo $the_product->get_price_html() ? $the_product->get_price_html() : '<span class="na">&ndash;</span>';
				break;
			case 'product_cat' :
			case 'product_tag' :
				if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					$termlist = array();
					foreach ( $terms as $term ) {
						$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=product' ) . ' ">' . $term->name . '</a>';
					}

					echo implode( ', ', $termlist );
				}
				break;
			case 'featured' :
				$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_feature_product&product_id=' . $post->ID ), 'woocommerce-feature-product' );
				echo '<a href="' . esc_url( $url ) . '" aria-label="' . __( 'Toggle featured', 'woocommerce' ) . '">';
				if ( $the_product->is_featured() ) {
					echo '<span class="wc-featured tips" data-tip="' . esc_attr__( 'Yes', 'woocommerce' ) . '">' . __( 'Yes', 'woocommerce' ) . '</span>';
				} else {
					echo '<span class="wc-featured not-featured tips" data-tip="' . esc_attr__( 'No', 'woocommerce' ) . '">' . __( 'No', 'woocommerce' ) . '</span>';
				}
				echo '</a>';
				break;
			case 'is_in_stock' :

				if ( $the_product->is_in_stock() ) {
					$stock_html = '<mark class="instock">' . __( 'In stock', 'woocommerce' ) . '</mark>';
				} else {
					$stock_html = '<mark class="outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</mark>';
				}

				if ( $the_product->managing_stock() ) {
					$stock_html .= ' (' . wc_stock_amount( $the_product->get_stock_quantity() ) . ')';
				}

				echo apply_filters( 'woocommerce_admin_stock_html', $stock_html, $the_product );

				break;
			default :
				break;
		}
	}

	/**
	 * Render product row actions for old version of WordPress.
	 * Since WordPress 4.3 we don't have to build the row actions.
	 *
	 * @param WP_Post $post
	 * @param string $title
	 */
	private function _render_product_row_actions( $post, $title ) {
		global $wp_version;

		if ( version_compare( $wp_version, '4.3-beta', '>=' ) ) {
			return;
		}

		$post_type_object = get_post_type_object( $post->post_type );
		$can_edit_post    = current_user_can( $post_type_object->cap->edit_post, $post->ID );

		// Get actions
		$actions = array();

		$actions['id'] = 'ID: ' . $post->ID;

		if ( $can_edit_post && 'trash' != $post->post_status ) {
			$actions['edit'] = '<a href="' . get_edit_post_link( $post->ID, true ) . '" title="' . esc_attr( __( 'Edit this item', 'woocommerce' ) ) . '">' . __( 'Edit', 'woocommerce' ) . '</a>';
			$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline', 'woocommerce' ) ) . '">' . __( 'Quick&nbsp;Edit', 'woocommerce' ) . '</a>';
		}
		if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
			if ( 'trash' == $post->post_status ) {
				$actions['untrash'] = '<a title="' . esc_attr( __( 'Restore this item from the Trash', 'woocommerce' ) ) . '" href="' . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . '">' . __( 'Restore', 'woocommerce' ) . '</a>';
			} elseif ( EMPTY_TRASH_DAYS ) {
				$actions['trash'] = '<a class="submitdelete" title="' . esc_attr( __( 'Move this item to the Trash', 'woocommerce' ) ) . '" href="' . get_delete_post_link( $post->ID ) . '">' . __( 'Trash', 'woocommerce' ) . '</a>';
			}

			if ( 'trash' == $post->post_status || ! EMPTY_TRASH_DAYS ) {
				$actions['delete'] = '<a class="submitdelete" title="' . esc_attr( __( 'Delete this item permanently', 'woocommerce' ) ) . '" href="' . get_delete_post_link( $post->ID, '', true ) . '">' . __( 'Delete Permanently', 'woocommerce' ) . '</a>';
			}
		}
		if ( $post_type_object->public ) {
			if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
				if ( $can_edit_post )
					$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'woocommerce' ), $title ) ) . '" rel="permalink">' . __( 'Preview', 'woocommerce' ) . '</a>';
			} elseif ( 'trash' != $post->post_status ) {
				$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'woocommerce' ), $title ) ) . '" rel="permalink">' . __( 'View', 'woocommerce' ) . '</a>';
			}
		}

		$actions = apply_filters( 'post_row_actions', $actions, $post );

		echo '<div class="row-actions">';

		$i = 0;
		$action_count = sizeof( $actions );

		foreach ( $actions as $action => $link ) {
			++$i;
			( $i == $action_count ) ? $sep = '' : $sep = ' | ';
			echo '<span class="' . $action . '">' . $link . $sep . '</span>';
		}
		echo '</div>';
	}

	/**
	 * Make columns sortable - https://gist.github.com/906872.
	 *
	 * @param array $columns
	 * @return array
	 */
	public function product_sortable_columns( $columns ) {
		$custom = array(
			'price'    => 'price',
			'featured' => 'featured',
			'sku'      => 'sku',
			'name'     => 'title'
		);
		return wp_parse_args( $custom, $columns );
	}

	/**
	 * Set list table primary column for products and orders.
	 * Support for WordPress 4.3.
	 *
	 * @param  string $default
	 * @param  string $screen_id
	 *
	 * @return string
	 */
	public function list_table_primary_column( $default, $screen_id ) {

		if ( 'edit-' . $this->cpt_slug === $screen_id ) {
			return 'name';
		}

		return $default;
	}

	/**
	 * Set row actions for products and orders.
	 *
	 * @param  array $actions
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	public function row_actions( $actions, $post ) {
		if ( $this->cpt_slug === $post->post_type ) {
			return array_merge( array( 'id' => 'ID: ' . $post->ID ), $actions );
		}

		return $actions;
	}

	/**
	 * Product sorting link.
	 *
	 * Based on Simple Page Ordering by 10up (http://wordpress.org/extend/plugins/simple-page-ordering/).
	 *
	 * @param array $views
	 * @return array
	 */
	public function product_sorting_link( $views ) {
		global $post_type, $wp_query;

		if ( ! current_user_can('edit_others_pages') ) {
			return $views;
		}

		$class            = ( isset( $wp_query->query['orderby'] ) && $wp_query->query['orderby'] == 'menu_order title' ) ? 'current' : '';
		$query_string     = remove_query_arg(array( 'orderby', 'order' ));
		$query_string     = add_query_arg( 'orderby', urlencode('menu_order title'), $query_string );
		$query_string     = add_query_arg( 'order', urlencode('ASC'), $query_string );
		$views['byorder'] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $class ) . '">' . __( 'Sort Products', 'woocommerce' ) . '</a>';

		return $views;
	}

	/**
	 * Custom bulk edit - form.
	 *
	 * @param mixed $column_name
	 * @param mixed $post_type
	 */
	public function bulk_edit( $column_name, $post_type ) {

		if ( 'price' != $column_name || $this->cpt_slug != $post_type ) {
			return;
		}

		$shipping_class = get_terms( 'product_shipping_class', array(
			'hide_empty' => false,
		) );

		include( WC()->plugin_path() . '/includes/admin/views/html-bulk-edit-product.php' );
	}

	/**
	 * Custom quick edit - form.
	 *
	 * @param mixed $column_name
	 * @param mixed $post_type
	 */
	public function quick_edit( $column_name, $post_type ) {

		if ( 'price' != $column_name || $this->cpt_slug != $post_type ) {
			return;
		}

		$shipping_class = get_terms( 'product_shipping_class', array(
			'hide_empty' => false,
		) );

		include( WC()->plugin_path() . '/includes/admin/views/html-quick-edit-product.php' );
	}

	/**
	 * Quick and bulk edit saving.
	 *
	 * @param int $post_id
	 * @param WP_Post $post
	 * @return int
	 */
	public function bulk_and_quick_edit_save_post( $post_id, $post ) {

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Don't save revisions and autosaves
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return $post_id;
		}

		// Check post type is product
		if ( $this->cpt_slug != $post->post_type ) {
			return $post_id;
		}

		// Check user permission
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check nonces
		if ( ! isset( $_REQUEST['woocommerce_quick_edit_nonce'] ) && ! isset( $_REQUEST['woocommerce_bulk_edit_nonce'] ) ) {
			return $post_id;
		}
		if ( isset( $_REQUEST['woocommerce_quick_edit_nonce'] ) && ! wp_verify_nonce( $_REQUEST['woocommerce_quick_edit_nonce'], 'woocommerce_quick_edit_nonce' ) ) {
			return $post_id;
		}
		if ( isset( $_REQUEST['woocommerce_bulk_edit_nonce'] ) && ! wp_verify_nonce( $_REQUEST['woocommerce_bulk_edit_nonce'], 'woocommerce_bulk_edit_nonce' ) ) {
			return $post_id;
		}

		// Get the product and save
		$product = wc_get_product( $post );

		if ( ! empty( $_REQUEST['woocommerce_quick_edit'] ) ) {
			$this->quick_edit_save( $post_id, $product );
		} else {
			$this->bulk_edit_save( $post_id, $product );
		}

		// Clear transient
		wc_delete_product_transients( $post_id );

		return $post_id;
	}

	/**
	 * Quick edit.
	 *
	 * @param integer $post_id
	 * @param WC_Product $product
	 */
	private function quick_edit_save( $post_id, $product ) {
		global $wpdb;

		$old_regular_price = $product->regular_price;
		$old_sale_price    = $product->sale_price;

		// Save fields
		if ( isset( $_REQUEST['_sku'] ) ) {
			$sku     = get_post_meta( $post_id, '_sku', true );
			$new_sku = wc_clean( $_REQUEST['_sku'] );

			if ( $new_sku !== $sku ) {
				if ( ! empty( $new_sku ) ) {
					$unique_sku = wc_product_has_unique_sku( $post_id, $new_sku );
					if ( $unique_sku ) {
						update_post_meta( $post_id, '_sku', $new_sku );
					}
				} else {
					update_post_meta( $post_id, '_sku', '' );
				}
			}
		}

		if ( isset( $_REQUEST['_weight'] ) ) {
			update_post_meta( $post_id, '_weight', wc_clean( $_REQUEST['_weight'] ) );
		}

		if ( isset( $_REQUEST['_length'] ) ) {
			update_post_meta( $post_id, '_length', wc_clean( $_REQUEST['_length'] ) );
		}

		if ( isset( $_REQUEST['_width'] ) ) {
			update_post_meta( $post_id, '_width', wc_clean( $_REQUEST['_width'] ) );
		}

		if ( isset( $_REQUEST['_height'] ) ) {
			update_post_meta( $post_id, '_height', wc_clean( $_REQUEST['_height'] ) );
		}

		if ( ! empty( $_REQUEST['_shipping_class'] ) ) {
			$shipping_class = '_no_shipping_class' == $_REQUEST['_shipping_class'] ? '' : wc_clean( $_REQUEST['_shipping_class'] );

			wp_set_object_terms( $post_id, $shipping_class, 'product_shipping_class' );
		}

		if ( isset( $_REQUEST['_visibility'] ) ) {
			if ( update_post_meta( $post_id, '_visibility', wc_clean( $_REQUEST['_visibility'] ) ) ) {
				do_action( 'woocommerce_product_set_visibility', $post_id, wc_clean( $_REQUEST['_visibility'] ) );
			}
		}

		if ( isset( $_REQUEST['_featured'] ) ) {
			if ( update_post_meta( $post_id, '_featured', 'yes' ) ) {
				delete_transient( 'wc_featured_products' );
			}
		} else {
			if ( update_post_meta( $post_id, '_featured', 'no' ) ) {
				delete_transient( 'wc_featured_products' );
			}
		}

		if ( isset( $_REQUEST['_tax_status'] ) ) {
			update_post_meta( $post_id, '_tax_status', wc_clean( $_REQUEST['_tax_status'] ) );
		}

		if ( isset( $_REQUEST['_tax_class'] ) ) {
			update_post_meta( $post_id, '_tax_class', wc_clean( $_REQUEST['_tax_class'] ) );
		}

		if ( $product->is_type('simple') || $product->is_type('external') ) {

			if ( isset( $_REQUEST['_regular_price'] ) ) {
				$new_regular_price = $_REQUEST['_regular_price'] === '' ? '' : wc_format_decimal( $_REQUEST['_regular_price'] );
				update_post_meta( $post_id, '_regular_price', $new_regular_price );
			} else {
				$new_regular_price = null;
			}
			if ( isset( $_REQUEST['_sale_price'] ) ) {
				$new_sale_price = $_REQUEST['_sale_price'] === '' ? '' : wc_format_decimal( $_REQUEST['_sale_price'] );
				update_post_meta( $post_id, '_sale_price', $new_sale_price );
			} else {
				$new_sale_price = null;
			}

			// Handle price - remove dates and set to lowest
			$price_changed = false;

			if ( ! is_null( $new_regular_price ) && $new_regular_price != $old_regular_price ) {
				$price_changed = true;
			} elseif ( ! is_null( $new_sale_price ) && $new_sale_price != $old_sale_price ) {
				$price_changed = true;
			}

			if ( $price_changed ) {
				update_post_meta( $post_id, '_sale_price_dates_from', '' );
				update_post_meta( $post_id, '_sale_price_dates_to', '' );

				if ( ! is_null( $new_sale_price ) && $new_sale_price !== '' ) {
					update_post_meta( $post_id, '_price', $new_sale_price );
				} else {
					update_post_meta( $post_id, '_price', $new_regular_price );
				}
			}
		}

		// Handle stock status
		if ( isset( $_REQUEST['_stock_status'] ) ) {
			$stock_status = wc_clean( $_REQUEST['_stock_status'] );

			if ( $product->is_type( 'variable' ) ) {
				foreach ( $product->get_children() as $child_id ) {
					if ( 'yes' !== get_post_meta( $child_id, '_manage_stock', true ) ) {
						wc_update_product_stock_status( $child_id, $stock_status );
					}
				}

				WC_Product_Variable::sync_stock_status( $post_id );
			} else {
				wc_update_product_stock_status( $post_id, $stock_status );
			}
		}

		// Handle stock
		if ( ! $product->is_type('grouped') ) {
			if ( isset( $_REQUEST['_manage_stock'] ) ) {
				update_post_meta( $post_id, '_manage_stock', 'yes' );
				wc_update_product_stock( $post_id, wc_stock_amount( $_REQUEST['_stock'] ) );
			} else {
				update_post_meta( $post_id, '_manage_stock', 'no' );
				wc_update_product_stock( $post_id, 0 );
			}

			if ( ! empty( $_REQUEST['_backorders'] ) ) {
				update_post_meta( $post_id, '_backorders', wc_clean( $_REQUEST['_backorders'] ) );
			}
		}

		do_action( 'woocommerce_' . $this->cpt_slug . '_quick_edit_save', $product );
	}

	/**
	 * Bulk edit.
	 * @param integer $post_id
	 * @param WC_Product $product
	 */
	public function bulk_edit_save( $post_id, $product ) {

		$old_regular_price = $product->regular_price;
		$old_sale_price    = $product->sale_price;

		// Save fields
		if ( ! empty( $_REQUEST['change_weight'] ) && isset( $_REQUEST['_weight'] ) ) {
			update_post_meta( $post_id, '_weight', wc_clean( stripslashes( $_REQUEST['_weight'] ) ) );
		}

		if ( ! empty( $_REQUEST['change_dimensions'] ) ) {
			if ( isset( $_REQUEST['_length'] ) ) {
				update_post_meta( $post_id, '_length', wc_clean( stripslashes( $_REQUEST['_length'] ) ) );
			}
			if ( isset( $_REQUEST['_width'] ) ) {
				update_post_meta( $post_id, '_width', wc_clean( stripslashes( $_REQUEST['_width'] ) ) );
			}
			if ( isset( $_REQUEST['_height'] ) ) {
				update_post_meta( $post_id, '_height', wc_clean( stripslashes( $_REQUEST['_height'] ) ) );
			}
		}

		if ( ! empty( $_REQUEST['_tax_status'] ) ) {
			update_post_meta( $post_id, '_tax_status', wc_clean( $_REQUEST['_tax_status'] ) );
		}

		if ( ! empty( $_REQUEST['_tax_class'] ) ) {
			$tax_class = wc_clean( $_REQUEST['_tax_class'] );
			if ( 'standard' == $tax_class ) {
				$tax_class = '';
			}
			update_post_meta( $post_id, '_tax_class', $tax_class );
		}

		if ( ! empty( $_REQUEST['_stock_status'] ) ) {
			$stock_status = wc_clean( $_REQUEST['_stock_status'] );

			if ( $product->is_type( 'variable' ) ) {
				foreach ( $product->get_children() as $child_id ) {
					if ( 'yes' !== get_post_meta( $child_id, '_manage_stock', true ) ) {
						wc_update_product_stock_status( $child_id, $stock_status );
					}
				}

				WC_Product_Variable::sync_stock_status( $post_id );
			} else {
				wc_update_product_stock_status( $post_id, $stock_status );
			}
		}

		if ( ! empty( $_REQUEST['_shipping_class'] ) ) {
			$shipping_class = '_no_shipping_class' == $_REQUEST['_shipping_class'] ? '' : wc_clean( $_REQUEST['_shipping_class'] );

			wp_set_object_terms( $post_id, $shipping_class, 'product_shipping_class' );
		}

		if ( ! empty( $_REQUEST['_visibility'] ) ) {
			if ( update_post_meta( $post_id, '_visibility', wc_clean( $_REQUEST['_visibility'] ) ) ) {
				do_action( 'woocommerce_product_set_visibility', $post_id, wc_clean( $_REQUEST['_visibility'] ) );
			}
		}

		if ( ! empty( $_REQUEST['_featured'] ) ) {
			if ( update_post_meta( $post_id, '_featured', stripslashes( $_REQUEST['_featured'] ) ) ) {
				delete_transient( 'wc_featured_products' );
			}
		}

		// Sold Individually
		if ( ! empty( $_REQUEST['_sold_individually'] ) ) {
			if ( $_REQUEST['_sold_individually'] == 'yes' ) {
				update_post_meta( $post_id, '_sold_individually', 'yes' );
			}
			else {
				update_post_meta( $post_id, '_sold_individually', '' );
			}
		}

		// Handle price - remove dates and set to lowest
		$change_price_product_types = apply_filters( 'woocommerce_bulk_edit_save_price_product_types', array( 'simple', 'external' ) );
		$can_product_type_change_price = false;
		foreach ( $change_price_product_types as $product_type ) {
			if ( $product->is_type( $product_type ) ) {
				$can_product_type_change_price = true;
				break;
			}
		}

		if ( $can_product_type_change_price ) {

			$price_changed = false;

			if ( ! empty( $_REQUEST['change_regular_price'] ) ) {

				$change_regular_price = absint( $_REQUEST['change_regular_price'] );
				$regular_price = esc_attr( stripslashes( $_REQUEST['_regular_price'] ) );

				switch ( $change_regular_price ) {
					case 1 :
						$new_price = $regular_price;
						break;
					case 2 :
						if ( strstr( $regular_price, '%' ) ) {
							$percent = str_replace( '%', '', $regular_price ) / 100;
							$new_price = $old_regular_price + ( round( $old_regular_price * $percent, wc_get_price_decimals() ) );
						} else {
							$new_price = $old_regular_price + $regular_price;
						}
						break;
					case 3 :
						if ( strstr( $regular_price, '%' ) ) {
							$percent = str_replace( '%', '', $regular_price ) / 100;
							$new_price = max( 0, $old_regular_price - ( round ( $old_regular_price * $percent, wc_get_price_decimals() ) ) );
						} else {
							$new_price = max( 0, $old_regular_price - $regular_price );
						}
						break;

					default :
						break;
				}

				if ( isset( $new_price ) && $new_price != $old_regular_price ) {
					$price_changed = true;
					$new_price = round( $new_price, wc_get_price_decimals() );
					update_post_meta( $post_id, '_regular_price', $new_price );
					$product->regular_price = $new_price;
				}
			}

			if ( ! empty( $_REQUEST['change_sale_price'] ) ) {

				$change_sale_price = absint( $_REQUEST['change_sale_price'] );
				$sale_price        = esc_attr( stripslashes( $_REQUEST['_sale_price'] ) );

				switch ( $change_sale_price ) {
					case 1 :
						$new_price = $sale_price;
						break;
					case 2 :
						if ( strstr( $sale_price, '%' ) ) {
							$percent = str_replace( '%', '', $sale_price ) / 100;
							$new_price = $old_sale_price + ( $old_sale_price * $percent );
						} else {
							$new_price = $old_sale_price + $sale_price;
						}
						break;
					case 3 :
						if ( strstr( $sale_price, '%' ) ) {
							$percent = str_replace( '%', '', $sale_price ) / 100;
							$new_price = max( 0, $old_sale_price - ( $old_sale_price * $percent ) );
						} else {
							$new_price = max( 0, $old_sale_price - $sale_price );
						}
						break;
					case 4 :
						if ( strstr( $sale_price, '%' ) ) {
							$percent = str_replace( '%', '', $sale_price ) / 100;
							$new_price = max( 0, $product->regular_price - ( $product->regular_price * $percent ) );
						} else {
							$new_price = max( 0, $product->regular_price - $sale_price );
						}
						break;

					default :
						break;
				}

				if ( isset( $new_price ) && $new_price != $old_sale_price ) {
					$price_changed = true;
					$new_price = ! empty( $new_price ) || '0' === $new_price ? round( $new_price, wc_get_price_decimals() ) : '';
					update_post_meta( $post_id, '_sale_price', $new_price );
					$product->sale_price = $new_price;
				}
			}

			if ( $price_changed ) {
				update_post_meta( $post_id, '_sale_price_dates_from', '' );
				update_post_meta( $post_id, '_sale_price_dates_to', '' );

				if ( $product->regular_price < $product->sale_price ) {
					$product->sale_price = '';
					update_post_meta( $post_id, '_sale_price', '' );
				}

				if ( $product->sale_price ) {
					update_post_meta( $post_id, '_price', $product->sale_price );
				} else {
					update_post_meta( $post_id, '_price', $product->regular_price );
				}
			}
		}

		// Handle stock
		if ( ! $product->is_type( 'grouped' ) ) {

			if ( ! empty( $_REQUEST['change_stock'] ) ) {
				update_post_meta( $post_id, '_manage_stock', 'yes' );
				wc_update_product_stock( $post_id, wc_stock_amount( $_REQUEST['_stock'] ) );
			}

			if ( ! empty( $_REQUEST['_manage_stock'] ) ) {

				if ( $_REQUEST['_manage_stock'] == 'yes' ) {
					update_post_meta( $post_id, '_manage_stock', 'yes' );
				} else {
					update_post_meta( $post_id, '_manage_stock', 'no' );
					wc_update_product_stock( $post_id, 0 );
				}
			}

			if ( ! empty( $_REQUEST['_backorders'] ) ) {
				update_post_meta( $post_id, '_backorders', wc_clean( $_REQUEST['_backorders'] ) );
			}

		}

		do_action( 'woocommerce_' . $this->cpt_slug . '_bulk_edit_save', $product );
	}


	/**
	 * Process the new bulk actions for changing order status.
	 */
	public function bulk_action() {
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
		$action        = $wp_list_table->current_action();

		// Bail out if this is not a status-changing action
		if ( strpos( $action, 'mark_' ) === false ) {
			return;
		}

		$order_statuses = wc_get_order_statuses();

		$new_status    = substr( $action, 5 ); // get the status name from action
		$report_action = 'marked_' . $new_status;

		// Sanity check: bail out if this is actually not a status, or is
		// not a registered status
		if ( ! isset( $order_statuses[ 'wc-' . $new_status ] ) ) {
			return;
		}

		$changed = 0;

		$post_ids = array_map( 'absint', (array) $_REQUEST['post'] );

		foreach ( $post_ids as $post_id ) {
			$order = wc_get_order( $post_id );
			$order->update_status( $new_status, __( 'Order status changed by bulk edit:', 'woocommerce' ), true );
			do_action( 'woocommerce_order_edit_status', $post_id, $new_status );
			$changed++;
		}

		$sendback = add_query_arg( array( 'post_type' => 'shop_order', $report_action => true, 'changed' => $changed, 'ids' => join( ',', $post_ids ) ), '' );

		if ( isset( $_GET['post_status'] ) ) {
			$sendback = add_query_arg( 'post_status', sanitize_text_field( $_GET['post_status'] ), $sendback );
		}

		wp_redirect( esc_url_raw( $sendback ) );
		exit();
	}


	/**
	 * Filters for post types.
	 */
	public function restrict_manage_posts() {
		global $typenow;

		if ( $this->cpt_slug == $typenow ) {
			$this->product_filters();
		} 
	}

	/**
	 * Show a category filter box.
	 */
	public function product_filters() {
		global $wp_query;

		// Category Filtering
		wc_product_dropdown_categories();

		// Type filtering
		$terms   = get_terms( 'product_type' );
		$output  = '<select name="product_type" id="dropdown_product_type">';
		$output .= '<option value="">' . __( 'Show all product types', 'woocommerce' ) . '</option>';

		foreach ( $terms as $term ) {
			$output .= '<option value="' . sanitize_title( $term->name ) . '" ';

			if ( isset( $wp_query->query['product_type'] ) ) {
				$output .= selected( $term->slug, $wp_query->query['product_type'], false );
			}

			$output .= '>';

			switch ( $term->name ) {
				case 'grouped' :
					$output .= __( 'Grouped product', 'woocommerce' );
					break;
				case 'external' :
					$output .= __( 'External/Affiliate product', 'woocommerce' );
					break;
				case 'variable' :
					$output .= __( 'Variable product', 'woocommerce' );
					break;
				case 'simple' :
					$output .= __( 'Simple product', 'woocommerce' );
					break;
				default :
					// Assuming that we have other types in future
					$output .= ucfirst( $term->name );
					break;
			}

			$output .= '</option>';

			if ( 'simple' == $term->name ) {

				$output .= '<option value="downloadable" ';

				if ( isset( $wp_query->query['product_type'] ) ) {
					$output .= selected( 'downloadable', $wp_query->query['product_type'], false );
				}

				$output .= '> &rarr; ' . __( 'Downloadable', 'woocommerce' ) . '</option>';

				$output .= '<option value="virtual" ';

				if ( isset( $wp_query->query['product_type'] ) ) {
					$output .= selected( 'virtual', $wp_query->query['product_type'], false );
				}

				$output .= '> &rarr;  ' . __( 'Virtual', 'woocommerce' ) . '</option>';
			}
		}

		$output .= '</select>';

		echo apply_filters( 'woocommerce_product_filters', $output );
	}

	/**
	 * Filters and sorting handler.
	 *
	 * @param  array $vars
	 * @return array
	 */
	public function request_query( $vars ) {
		global $typenow, $wp_query, $wp_post_statuses;

		if ( $this->cpt_slug === $typenow ) {
			// Sorting
			if ( isset( $vars['orderby'] ) ) {
				if ( 'price' == $vars['orderby'] ) {
					$vars = array_merge( $vars, array(
						'meta_key'  => '_price',
						'orderby'   => 'meta_value_num'
					) );
				}
				if ( 'featured' == $vars['orderby'] ) {
					$vars = array_merge( $vars, array(
						'meta_key'  => '_featured',
						'orderby'   => 'meta_value'
					) );
				}
				if ( 'sku' == $vars['orderby'] ) {
					$vars = array_merge( $vars, array(
						'meta_key'  => '_sku',
						'orderby'   => 'meta_value'
					) );
				}
			}

		}

		return $vars;
	}

	/**
	 * Filter the products in admin based on options.
	 *
	 * @param mixed $query
	 */
	public function product_filters_query( $query ) {
		global $typenow, $wp_query;

		if ( $this->cpt_slug == $typenow ) {

			if ( isset( $query->query_vars['product_type'] ) ) {
				// Subtypes
				if ( 'downloadable' == $query->query_vars['product_type'] ) {
					$query->query_vars['product_type']  = '';
					$query->is_tax = false;
					$query->query_vars['meta_value']    = 'yes';
					$query->query_vars['meta_key']      = '_downloadable';
				} elseif ( 'virtual' == $query->query_vars['product_type'] ) {
					$query->query_vars['product_type']  = '';
					$query->is_tax = false;
					$query->query_vars['meta_value']    = 'yes';
					$query->query_vars['meta_key']      = '_virtual';
				}
			}

			// Categories
			if ( isset( $_GET['product_cat'] ) && '0' == $_GET['product_cat'] ) {
				$query->query_vars['tax_query'][] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'id',
					'terms'    => get_terms( 'product_cat', array( 'fields' => 'ids' ) ),
					'operator' => 'NOT IN'
				);
			}
		}
	}

	/**
	 * Search by SKU or ID for products.
	 *
	 * @param string $where
	 * @return string
	 */
	public function product_search( $where ) {
		global $pagenow, $wpdb, $wp;

		if ( 'edit.php' != $pagenow || ! is_search() || ! isset( $wp->query_vars['s'] ) || $this->cpt_slug != $wp->query_vars['post_type'] ) {
			return $where;
		}

		$search_ids = array();
		$terms      = explode( ',', $wp->query_vars['s'] );

		foreach ( $terms as $term ) {
			if ( is_numeric( $term ) ) {
				$search_ids[] = $term;
			}

			// Attempt to get a SKU
			$sku_to_id = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_parent FROM {$wpdb->posts} LEFT JOIN {$wpdb->postmeta} ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id WHERE meta_key='_sku' AND meta_value LIKE %s;", '%' . $wpdb->esc_like( wc_clean( $term ) ) . '%' ) );
			$sku_to_id = array_merge( wp_list_pluck( $sku_to_id, 'ID' ), wp_list_pluck( $sku_to_id, 'post_parent' ) );

			if ( $sku_to_id && sizeof( $sku_to_id ) > 0 ) {
				$search_ids = array_merge( $search_ids, $sku_to_id );
			}
		}

		$search_ids = array_filter( array_unique( array_map( 'absint', $search_ids ) ) );

		if ( sizeof( $search_ids ) > 0 ) {
			$where = str_replace( 'AND (((', "AND ( ({$wpdb->posts}.ID IN (" . implode( ',', $search_ids ) . ")) OR ((", $where );
		}

		return $where;
	}

	/**
	 * Removes variations etc belonging to a deleted post, and clears transients.
	 *
	 * @param mixed $id ID of post being deleted
	 */
	public function delete_post( $id ) {
		global $woocommerce, $wpdb;

		if ( ! current_user_can( 'delete_posts' ) ) {
			return;
		}

		if ( $id > 0 ) {

			$post_type = get_post_type( $id );

			switch ( $post_type ) {
				case $this->cpt_slug :

					$child_product_variations = get_children( 'post_parent=' . $id . '&post_type=product_variation' );

					if ( ! empty( $child_product_variations ) ) {
						foreach ( $child_product_variations as $child ) {
							wp_delete_post( $child->ID, true );
						}
					}

					$child_products = get_children( 'post_parent=' . $id . '&post_type=product' );

					if ( ! empty( $child_products ) ) {
						foreach ( $child_products as $child ) {
							$child_post                = array();
							$child_post['ID']          = $child->ID;
							$child_post['post_parent'] = 0;
							wp_update_post( $child_post );
						}
					}

					if ( $parent_id = wp_get_post_parent_id( $id ) ) {
						wc_delete_product_transients( $parent_id );
					}

				break;
			}
		}
	}


	/**
	 * Change label for insert buttons.
	 * @param  array $strings
	 * @return array
	 */
	public function change_insert_into_post( $strings ) {
		global $post_type;

		if ( in_array( $post_type, array( $this->cpt_slug ) ) ) {
			$obj = get_post_type_object( $post_type );

			$strings['insertIntoPost']     = sprintf( __( 'Insert into %s', 'woocommerce' ), $obj->labels->singular_name );
			$strings['uploadedToThisPost'] = sprintf( __( 'Uploaded to this %s', 'woocommerce' ), $obj->labels->singular_name );
		}

		return $strings;
	}

	/**
	 * Hidden default Meta-Boxes.
	 * @param  array  $hidden
	 * @param  object $screen
	 * @return array
	 */
	public function hidden_meta_boxes( $hidden, $screen ) {
		if ( $this->cpt_slug === $screen->post_type && 'post' === $screen->base ) {
			$hidden = array_merge( $hidden, array( 'postcustom' ) );
		}

		return $hidden;
	}

	/**
	 * Output product visibility options.
	 */
	public function product_data_visibility() {
		global $post;
		
		if ( $this->cpt_slug != $post->post_type ) {
			return;
		}

		$current_visibility = ( $current_visibility = get_post_meta( $post->ID, '_visibility', true ) ) ? $current_visibility : apply_filters( 'woocommerce_' . $this->cpt_slug . '_visibility_default' , 'visible' );
		$current_featured   = ( $current_featured = get_post_meta( $post->ID, '_featured', true ) ) ? $current_featured : 'no';

		$visibility_options = apply_filters( 'woocommerce_' . $this->cpt_slug . '_visibility_options', array(
			'visible' => __( 'Catalog/search', 'woocommerce' ),
			'catalog' => __( 'Catalog', 'woocommerce' ),
			'search'  => __( 'Search', 'woocommerce' ),
			'hidden'  => __( 'Hidden', 'woocommerce' )
		) );
		?>
		<div class="misc-pub-section" id="catalog-visibility">
			<?php _e( 'Catalog visibility:', 'woocommerce' ); ?> <strong id="catalog-visibility-display"><?php
				echo isset( $visibility_options[ $current_visibility ]  ) ? esc_html( $visibility_options[ $current_visibility ] ) : esc_html( $current_visibility );

				if ( 'yes' == $current_featured ) {
					echo ', ' . __( 'Featured', 'woocommerce' );
				}
			?></strong>

			<a href="#catalog-visibility" class="edit-catalog-visibility hide-if-no-js"><?php _e( 'Edit', 'woocommerce' ); ?></a>

			<div id="catalog-visibility-select" class="hide-if-js">

				<input type="hidden" name="current_visibility" id="current_visibility" value="<?php echo esc_attr( $current_visibility ); ?>" />
				<input type="hidden" name="current_featured" id="current_featured" value="<?php echo esc_attr( $current_featured ); ?>" />

				<?php
					echo '<p>' . __( 'Choose where this product should be displayed in your catalog. The product will always be accessible directly.', 'woocommerce' ) . '</p>';

					foreach ( $visibility_options as $name => $label ) {
						echo '<input type="radio" name="_visibility" id="_visibility_' . esc_attr( $name ) . '" value="' . esc_attr( $name ) . '" ' . checked( $current_visibility, $name, false ) . ' data-label="' . esc_attr( $label ) . '" /> <label for="_visibility_' . esc_attr( $name ) . '" class="selectit">' . esc_html( $label ) . '</label><br />';
					}

					echo '<p>' . __( 'Enable this option to feature this product.', 'woocommerce' ) . '</p>';

					echo '<input type="checkbox" name="_featured" id="_featured" ' . checked( $current_featured, 'yes', false ) . ' /> <label for="_featured">' . __( 'Featured Product', 'woocommerce' ) . '</label><br />';
				?>
				<p>
					<a href="#catalog-visibility" class="save-post-visibility hide-if-no-js button"><?php _e( 'OK', 'woocommerce' ); ?></a>
					<a href="#catalog-visibility" class="cancel-post-visibility hide-if-no-js"><?php _e( 'Cancel', 'woocommerce' ); ?></a>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Filter the directory for uploads.
	 *
	 * @param array $pathdata
	 * @return array
	 */
	public function upload_dir( $pathdata ) {

		// Change upload dir for downloadable files
		if ( isset( $_POST['type'] ) && 'downloadable_product' == $_POST['type'] ) {

			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/woocommerce_uploads';
				$pathdata['url']    = $pathdata['url']. '/woocommerce_uploads';
				$pathdata['subdir'] = '/woocommerce_uploads';
			} else {
				$new_subdir = '/woocommerce_uploads' . $pathdata['subdir'];

				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['subdir'] );
			}
		}

		return $pathdata;
	}

	/**
	 * Run a filter when uploading a downloadable product.
	 */
	public function woocommerce_media_upload_downloadable_product() {
		do_action( 'media_upload_file' );
	}

	/**
	 * Grant downloadable file access to any newly added files on any existing.
	 * orders for this product that have previously been granted downloadable file access.
	 *
	 * @param int $product_id product identifier
	 * @param int $variation_id optional product variation identifier
	 * @param array $downloadable_files newly set files
	 */
	public function process_product_file_download_paths( $product_id, $variation_id, $downloadable_files ) {
		global $wpdb;

		if ( $variation_id ) {
			$product_id = $variation_id;
		}

		$product               = wc_get_product( $product_id );
		$existing_download_ids = array_keys( (array) $product->get_files() );
		$updated_download_ids  = array_keys( (array) $downloadable_files );

		$new_download_ids      = array_filter( array_diff( $updated_download_ids, $existing_download_ids ) );
		$removed_download_ids  = array_filter( array_diff( $existing_download_ids, $updated_download_ids ) );

		if ( ! empty( $new_download_ids ) || ! empty( $removed_download_ids ) ) {
			// determine whether downloadable file access has been granted via the typical order completion, or via the admin ajax method
			$existing_permissions = $wpdb->get_results( $wpdb->prepare( "SELECT * from {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE product_id = %d GROUP BY order_id", $product_id ) );

			foreach ( $existing_permissions as $existing_permission ) {
				$order = wc_get_order( $existing_permission->order_id );

				if ( ! empty( $order->id ) ) {
					// Remove permissions
					if ( ! empty( $removed_download_ids ) ) {
						foreach ( $removed_download_ids as $download_id ) {
							if ( apply_filters( 'woocommerce_process_' . $this->cpt_slug . '_file_download_paths_remove_access_to_old_file', true, $download_id, $product_id, $order ) ) {
								$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", $order->id, $product_id, $download_id ) );
							}
						}
					}
					// Add permissions
					if ( ! empty( $new_download_ids ) ) {

						foreach ( $new_download_ids as $download_id ) {

							if ( apply_filters( 'woocommerce_process_' . $this->cpt_slug . '_file_download_paths_grant_access_to_new_file', true, $download_id, $product_id, $order ) ) {
								// grant permission if it doesn't already exist
								if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT 1=1 FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d AND product_id = %d AND download_id = %s", $order->id, $product_id, $download_id ) ) ) {
									wc_downloadable_file_permission( $download_id, $product_id, $order );
								}
							}
						}
					}
				}
			}
		}
	}


	/**
	 * Disable DFW feature pointer.
	 */
	public function disable_dfw_feature_pointer() {
		$screen = get_current_screen();

		if ( $screen && ( $this->cpt_slug === $screen->id ) && ( 'post' === $screen->base ) ) {
			remove_action( 'admin_print_footer_scripts', array( 'WP_Internal_Pointers', 'pointer_wp410_dfw' ) );
		}
	}
}

endif;

new WCCPT_Admin_Post_Types();
