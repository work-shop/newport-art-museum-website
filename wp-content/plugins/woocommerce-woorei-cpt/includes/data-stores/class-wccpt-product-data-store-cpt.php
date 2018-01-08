<?php

class WCCPT_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {
	
	/**
	 * Method to read a product from the database.
	 * @param WC_Product
	 */
	 
	public function read( &$product ) {
		
		$product->set_defaults();

		if ( ! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || ! ( ('product' === $post_object->post_type) ||  WC_CPT_List::is_active( $post_object->post_type ) ) ) {
			throw new Exception( __( 'Invalid product.', 'woocommerce' ) );
		}

		$id = $product->get_id();

		$product->set_props( array(
			'name'              => $post_object->post_title,
			'slug'              => $post_object->post_name,
			'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
			'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
			'status'            => $post_object->post_status,
			'description'       => $post_object->post_content,
			'short_description' => $post_object->post_excerpt,
			'parent_id'         => $post_object->post_parent,
			'menu_order'        => $post_object->menu_order,
			'reviews_allowed'   => 'open' === $post_object->comment_status,
		) );

		$this->read_attributes( $product );
		$this->read_downloads( $product );
		$this->read_visibility( $product );
		$this->read_product_data( $product );
		$this->read_extra_data( $product );
		$product->set_object_read( true );
	}
	
	/**
	 * Get the product type based on product ID.
	 *
	 * @since 3.0.0
	 * @param int $product_id
	 * @return bool|string
	 */
	public function get_product_type( $product_id ) {
		$post_type = get_post_type( $product_id );
		if ( 'product_variation' === $post_type ) {
			return 'variation';
		} elseif ( ( $post_type === 'product' ) || WC_CPT_List::is_active( $post_type ) ) {
			$terms = get_the_terms( $product_id, 'product_type' );
			return ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
		} else {
			return false;
		}
	}
}




/**
 * WC Grouped Product Data Store: Stored in CPT.
 *
 * @version  3.0.0
 * @category Class
 * @author   WooThemes
 */
class WCCPT_Product_Grouped_Data_Store_CPT extends WCCPT_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface {

	/**
	 * Helper method that updates all the post meta for a grouped product.
	 *
	 * @param WC_Product
	 * @param bool Force update. Used during create.
	 * @since 3.0.0
	 */
	protected function update_post_meta( &$product, $force = false ) {
		$meta_key_to_props = array(
			'_children' => 'children',
		);

		$props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $product, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value   = $product->{"get_$prop"}( 'edit' );
			$updated = update_post_meta( $product->get_id(), $meta_key, $value );
			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}

		parent::update_post_meta( $product, $force );
	}

	/**
	 * Handle updated meta props after updating meta data.
	 *
	 * @since  3.0.0
	 * @param  WC_Product $product
	 */
	protected function handle_updated_props( &$product ) {
		if ( in_array( 'children', $this->updated_props ) ) {
			$child_prices = array();
			foreach ( $product->get_children( 'edit' ) as $child_id ) {
				$child = wc_get_product( $child_id );
				if ( $child ) {
					$child_prices[] = $child->get_price();
				}
			}
			$child_prices = array_filter( $child_prices );
			delete_post_meta( $product->get_id(), '_price' );

			if ( ! empty( $child_prices ) ) {
				add_post_meta( $product->get_id(), '_price', min( $child_prices ) );
				add_post_meta( $product->get_id(), '_price', max( $child_prices ) );
			}
		}
		parent::handle_updated_props( $product );
	}

	/**
	 * Sync grouped product prices with children.
	 *
	 * @since 3.0.0
	 * @param WC_Product|int $product
	 */
	public function sync_price( &$product ) {
		global $wpdb;

		$children_ids = get_posts( array(
			'post_parent' => $product->get_id(),
			'fields'      => 'ids',
		) );
		$prices = $children_ids ? array_unique( $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_price' AND post_id IN ( " . implode( ',', array_map( 'absint', $children_ids ) ) . " )" ) ) : array();

		delete_post_meta( $product->get_id(), '_price' );
		delete_transient( 'wc_var_prices_' . $product->get_id() );

		if ( $prices ) {
			sort( $prices );
			// To allow sorting and filtering by multiple values, we have no choice but to store child prices in this manner.
			foreach ( $prices as $price ) {
				add_post_meta( $product->get_id(), '_price', $price, false );
			}
		}
	}
}



/**
 * WC Variable Product Data Store: Stored in CPT.
 *
 * @version  3.0.0
 * @category Class
 * @author   WooThemes
 */
class WCCPT_Product_Variable_Data_Store_CPT extends WCCPT_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface, WC_Product_Variable_Data_Store_Interface {

	/**
	 * Cached & hashed prices array for child variations.
	 *
	 * @var array
	 */
	protected $prices_array = array();

	/**
	 * Read product data.
	 *
	 * @since 3.0.0
	 */
	protected function read_product_data( &$product ) {
		parent::read_product_data( $product );

		// Set directly since individual data needs changed at the WC_Product_Variation level -- these datasets just pull.
		$children = $this->read_children( $product );
		$product->set_children( $children['all'] );
		$product->set_visible_children( $children['visible'] );
		$product->set_variation_attributes( $this->read_variation_attributes( $product ) );
	}

	/**
	 * Loads variation child IDs.
	 *
	 * @param  WC_Product
	 * @param  bool $force_read True to bypass the transient.
	 * @return array
	 */
	protected function read_children( &$product, $force_read = false ) {
		$children_transient_name = 'wc_product_children_' . $product->get_id();
		$children                = get_transient( $children_transient_name );

		if ( empty( $children ) || ! is_array( $children ) || ! isset( $children['all'] ) || ! isset( $children['visible'] ) || $force_read ) {
			$all_args = $visible_only_args = array(
				'post_parent' => $product->get_id(),
				'post_type'   => 'product_variation',
				'orderby'     => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
				'fields'      => 'ids',
				'post_status' => 'publish',
				'numberposts' => -1,
			);
			if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
				$visible_only_args['tax_query'][] = array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => 'outofstock',
					'operator' => 'NOT IN',
				);
			}
			$children['all']     = get_posts( apply_filters( 'woocommerce_variable_children_args', $all_args, $product, false ) );
			$children['visible'] = get_posts( apply_filters( 'woocommerce_variable_children_args', $visible_only_args, $product, true ) );

			set_transient( $children_transient_name, $children, DAY_IN_SECONDS * 30 );
		}

		$children['all']     = wp_parse_id_list( (array) $children['all'] );
		$children['visible'] = wp_parse_id_list( (array) $children['visible'] );

		return $children;
	}

	/**
	 * Loads an array of attributes used for variations, as well as their possible values.
	 *
	 * @param WC_Product
	 * @return array
	 */
	protected function read_variation_attributes( &$product ) {
		global $wpdb;

		$variation_attributes = array();
		$attributes           = $product->get_attributes();
		$child_ids            = $product->get_children();

		if ( ! empty( $child_ids ) && ! empty( $attributes ) ) {
			foreach ( $attributes as $attribute ) {
				if ( empty( $attribute['is_variation'] ) ) {
					continue;
				}

				// Get possible values for this attribute, for only visible variations.
				$values = array_unique( $wpdb->get_col( $wpdb->prepare(
					"SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN (" . implode( ',', array_map( 'absint', $child_ids ) ) . ")",
					wc_variation_attribute_name( $attribute['name'] )
				) ) );

				// Empty value indicates that all options for given attribute are available.
				if ( in_array( '', $values ) || empty( $values ) ) {
					$values = $attribute['is_taxonomy'] ? wc_get_object_terms( $product->get_id(), $attribute['name'], 'slug' ) : wc_get_text_attributes( $attribute['value'] );
				// Get custom attributes (non taxonomy) as defined.
				} elseif ( ! $attribute['is_taxonomy'] ) {
					$text_attributes          = wc_get_text_attributes( $attribute['value'] );
					$assigned_text_attributes = $values;
					$values                   = array();

					// Pre 2.4 handling where 'slugs' were saved instead of the full text attribute
					if ( version_compare( get_post_meta( $product->get_id(), '_product_version', true ), '2.4.0', '<' ) ) {
						$assigned_text_attributes = array_map( 'sanitize_title', $assigned_text_attributes );
						foreach ( $text_attributes as $text_attribute ) {
							if ( in_array( sanitize_title( $text_attribute ), $assigned_text_attributes ) ) {
								$values[] = $text_attribute;
							}
						}
					} else {
						foreach ( $text_attributes as $text_attribute ) {
							if ( in_array( $text_attribute, $assigned_text_attributes ) ) {
								$values[] = $text_attribute;
							}
						}
					}
				}
				$variation_attributes[ $attribute['name'] ] = array_unique( $values );
			}
		}

		return $variation_attributes;
	}

	/**
	 * Get an array of all sale and regular prices from all variations. This is used for example when displaying the price range at variable product level or seeing if the variable product is on sale.
	 *
	 * Can be filtered by plugins which modify costs, but otherwise will include the raw meta costs unlike get_price() which runs costs through the woocommerce_get_price filter.
	 * This is to ensure modified prices are not cached, unless intended.
	 *
	 * @since  3.0.0
	 * @param  WC_Product
	 * @param  bool $include_taxes If taxes should be calculated or not.
	 * @return array of prices
	 */
	public function read_price_data( &$product, $include_taxes = false ) {

		/**
		 * Transient name for storing prices for this product (note: Max transient length is 45)
		 * @since 2.5.0 a single transient is used per product for all prices, rather than many transients per product.
		 */
		$transient_name = 'wc_var_prices_' . $product->get_id();

		$price_hash = $this->get_price_hash( $product, $include_taxes );

		/**
		 * $this->prices_array is an array of values which may have been modified from what is stored in transients - this may not match $transient_cached_prices_array.
		 * If the value has already been generated, we don't need to grab the values again so just return them. They are already filtered.
		 */
		if ( empty( $this->prices_array[ $price_hash ] ) ) {
			$transient_cached_prices_array = array_filter( (array) json_decode( strval( get_transient( $transient_name ) ), true ) );

			// If the product version has changed since the transient was last saved, reset the transient cache.
			if ( empty( $transient_cached_prices_array['version'] ) || WC_Cache_Helper::get_transient_version( 'product' ) !== $transient_cached_prices_array['version'] ) {
				$transient_cached_prices_array = array( 'version' => WC_Cache_Helper::get_transient_version( 'product' ) );
			}

			// If the prices are not stored for this hash, generate them and add to the transient.
			if ( empty( $transient_cached_prices_array[ $price_hash ] ) ) {
				$prices         = array();
				$regular_prices = array();
				$sale_prices    = array();
				$variation_ids  = $product->get_visible_children();
				foreach ( $variation_ids as $variation_id ) {
					if ( $variation = wc_get_product( $variation_id ) ) {
						$price         = apply_filters( 'woocommerce_variation_prices_price', $variation->get_price( 'edit' ), $variation, $product );
						$regular_price = apply_filters( 'woocommerce_variation_prices_regular_price', $variation->get_regular_price( 'edit' ), $variation, $product );
						$sale_price    = apply_filters( 'woocommerce_variation_prices_sale_price', $variation->get_sale_price( 'edit' ), $variation, $product );

						// Skip empty prices
						if ( '' === $price ) {
							continue;
						}

						// If sale price does not equal price, the product is not yet on sale
						if ( $sale_price === $regular_price || $sale_price !== $price ) {
							$sale_price = $regular_price;
						}

						// If we are getting prices for display, we need to account for taxes
						if ( $include_taxes ) {
							if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
								$price         = '' === $price ? ''         : wc_get_price_including_tax( $variation, array( 'qty' => 1, 'price' => $price ) );
								$regular_price = '' === $regular_price ? '' : wc_get_price_including_tax( $variation, array( 'qty' => 1, 'price' => $regular_price ) );
								$sale_price    = '' === $sale_price ? ''    : wc_get_price_including_tax( $variation, array( 'qty' => 1, 'price' => $sale_price ) );
							} else {
								$price         = '' === $price ? ''         : wc_get_price_excluding_tax( $variation, array( 'qty' => 1, 'price' => $price ) );
								$regular_price = '' === $regular_price ? '' : wc_get_price_excluding_tax( $variation, array( 'qty' => 1, 'price' => $regular_price ) );
								$sale_price    = '' === $sale_price ? ''    : wc_get_price_excluding_tax( $variation, array( 'qty' => 1, 'price' => $sale_price ) );
							}
						}

						$prices[ $variation_id ]         = wc_format_decimal( $price, wc_get_price_decimals() );
						$regular_prices[ $variation_id ] = wc_format_decimal( $regular_price, wc_get_price_decimals() );
						$sale_prices[ $variation_id ]    = wc_format_decimal( $sale_price . '.00', wc_get_price_decimals() );
					}
				}

				$transient_cached_prices_array[ $price_hash ] = array(
					'price'         => $prices,
					'regular_price' => $regular_prices,
					'sale_price'    => $sale_prices,
				);

				set_transient( $transient_name, json_encode( $transient_cached_prices_array ), DAY_IN_SECONDS * 30 );
			}

			/**
			 * Give plugins one last chance to filter the variation prices array which has been generated and store locally to the class.
			 * This value may differ from the transient cache. It is filtered once before storing locally.
			 */
			$this->prices_array[ $price_hash ] = apply_filters( 'woocommerce_variation_prices', $transient_cached_prices_array[ $price_hash ], $product, $include_taxes );
		}
		return $this->prices_array[ $price_hash ];
	}

	/**
	 * Create unique cache key based on the tax location (affects displayed/cached prices), product version and active price filters.
	 * DEVELOPERS should filter this hash if offering conditonal pricing to keep it unique.
	 *
	 * @since  3.0.0
	 * @param  WC_Product
	 * @param  bool $include_taxes If taxes should be calculated or not.
	 * @return string
	 */
	protected function get_price_hash( &$product, $include_taxes = false ) {
		global $wp_filter;

		$price_hash   = $include_taxes ? array( get_option( 'woocommerce_tax_display_shop', 'excl' ), WC_Tax::get_rates() ) : array( false );
		$filter_names = array( 'woocommerce_variation_prices_price', 'woocommerce_variation_prices_regular_price', 'woocommerce_variation_prices_sale_price' );

		foreach ( $filter_names as $filter_name ) {
			if ( ! empty( $wp_filter[ $filter_name ] ) ) {
				$price_hash[ $filter_name ] = array();

				foreach ( $wp_filter[ $filter_name ] as $priority => $callbacks ) {
					$price_hash[ $filter_name ][] = array_values( wp_list_pluck( $callbacks, 'function' ) );
				}
			}
		}

		$price_hash[] = WC_Cache_Helper::get_transient_version( 'product' );
		$price_hash   = md5( json_encode( apply_filters( 'woocommerce_get_variation_prices_hash', $price_hash, $product, $include_taxes ) ) );

		return $price_hash;
	}

	/**
	 * Does a child have a weight set?
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 * @return boolean
	 */
	public function child_has_weight( $product ) {
		global $wpdb;
		$children = $product->get_visible_children();
		return $children ? null !== $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_weight' AND meta_value > 0 AND post_id IN ( " . implode( ',', array_map( 'absint', $children ) ) . " )" ) : false;
	}

	/**
	 * Does a child have dimensions set?
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 * @return boolean
	 */
	public function child_has_dimensions( $product ) {
		global $wpdb;
		$children = $product->get_visible_children();
		return $children ? null !== $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key IN ( '_length', '_width', '_height' ) AND meta_value > 0 AND post_id IN ( " . implode( ',', array_map( 'absint', $children ) ) . " )" ) : false;
	}

	/**
	 * Is a child in stock?
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 * @return boolean
	 */
	public function child_is_in_stock( $product ) {
		global $wpdb;
		$children            = $product->get_children();
		$oufofstock_children = $children ? $wpdb->get_var( "SELECT COUNT( post_id ) FROM $wpdb->postmeta WHERE meta_key = '_stock_status' AND meta_value = 'outofstock' AND post_id IN ( " . implode( ',', array_map( 'absint', $children ) ) . " )" ) : 0;
		return count( $children ) > $oufofstock_children;
	}

	/**
	 * Syncs all variation names if the parent name is changed.
	 *
	 * @param WC_Product $product
	 * @param string $previous_name
	 * @param string $new_name
	 * @since 3.0.0
	 */
	public function sync_variation_names( &$product, $previous_name = '', $new_name = '' ) {
		if ( $new_name !== $previous_name ) {
			global $wpdb;

			$wpdb->query( $wpdb->prepare(
				"
					UPDATE {$wpdb->posts}
					SET post_title = REPLACE( post_title, %s, %s )
					WHERE post_type = 'product_variation'
					AND post_parent = %d
				",
				$previous_name ? $previous_name : 'AUTO-DRAFT',
				$new_name,
				$product->get_id()
			 ) );
		}
	}

	/**
	 * Stock managed at the parent level - update children being managed by this product.
	 * This sync function syncs downwards (from parent to child) when the variable product is saved.
	 *
	 * @param WC_Product
	 * @since 3.0.0
	 */
	public function sync_managed_variation_stock_status( &$product ) {
		global $wpdb;

		if ( $product->get_manage_stock() ) {
			$status           = $product->get_stock_status();
			$children         = $product->get_children();
			$managed_children = $children ? array_unique( $wpdb->get_col( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_manage_stock' AND meta_value != 'yes' AND post_id IN ( " . implode( ',', array_map( 'absint', $children ) ) . " )" ) ) : array();
			$changed          = false;
			foreach ( $managed_children as $managed_child ) {
				if ( update_post_meta( $managed_child, '_stock_status', $status ) ) {
					$changed = true;
				}
			}
			if ( $changed ) {
				$children = $this->read_children( $product, true );
				$product->set_children( $children['all'] );
				$product->set_visible_children( $children['visible'] );
			}
		}
	}

	/**
	 * Sync variable product prices with children.
	 *
	 * @since 3.0.0
	 * @param WC_Product|int $product
	 */
	public function sync_price( &$product ) {
		global $wpdb;

		$children = $product->get_visible_children();
		$prices   = $children ? array_unique( $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '_price' AND post_id IN ( " . implode( ',', array_map( 'absint', $children ) ) . " )" ) ) : array();

		delete_post_meta( $product->get_id(), '_price' );

		if ( $prices ) {
			sort( $prices );
			// To allow sorting and filtering by multiple values, we have no choice but to store child prices in this manner.
			foreach ( $prices as $price ) {
				if ( is_null( $price ) || '' === $price ) {
					continue;
				}
				add_post_meta( $product->get_id(), '_price', $price, false );
			}
		}
	}

	/**
	 * Sync variable product stock status with children.
	 * Change does not persist unless saved by caller.
	 *
	 * @since 3.0.0
	 * @param WC_Product|int $product
	 */
	public function sync_stock_status( &$product ) {
		$product->set_stock_status( $product->child_is_in_stock() ? 'instock' : 'outofstock' );
	}

	/**
	 * Delete variations of a product.
	 *
	 * @since 3.0.0
	 * @param int $product_id
	 * @param $force_delete False to trash.
	 */
	public function delete_variations( $product_id, $force_delete = false ) {
		if ( ! is_numeric( $product_id ) || 0 >= $product_id ) {
			return;
		}

		$variation_ids = wp_parse_id_list( get_posts( array(
			'post_parent' => $product_id,
			'post_type'   => 'product_variation',
			'fields'      => 'ids',
			'post_status' => array( 'any', 'trash', 'auto-draft' ),
			'numberposts' => -1,
		) ) );

		if ( ! empty( $variation_ids ) ) {
			foreach ( $variation_ids as $variation_id ) {
				if ( $force_delete ) {
					wp_delete_post( $variation_id, true );
				} else {
					wp_trash_post( $variation_id );
				}
			}
		}

		delete_transient( 'wc_product_children_' . $product_id );
	}

	/**
	 * Untrash variations.
	 *
	 * @param int $product_id
	 */
	public function untrash_variations( $product_id ) {
		$variation_ids = wp_parse_id_list( get_posts( array(
			'post_parent' => $product_id,
			'post_type'   => 'product_variation',
			'fields'      => 'ids',
			'post_status' => 'trash',
			'numberposts' => -1,
		) ) );

		if ( ! empty( $variation_ids ) ) {
			foreach ( $variation_ids as $variation_id ) {
				wp_untrash_post( $variation_id );
			}
		}

		delete_transient( 'wc_product_children_' . $product_id );
	}
}




/**
 * WC Variation Product Data Store: Stored in CPT.
 *
 * @version  3.0.0
 * @category Class
 * @author   WooThemes
 */
class WCCPT_Product_Variation_Data_Store_CPT extends WCCPT_Product_Data_Store_CPT implements WC_Object_Data_Store_Interface {

	/**
	 * Callback to remove unwanted meta data.
	 *
	 * @param object $meta
	 * @return bool false if excluded.
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return ! in_array( $meta->meta_key, $this->internal_meta_keys ) && 0 !== stripos( $meta->meta_key, 'attribute_' ) && 0 !== stripos( $meta->meta_key, 'wp_' );
	}

	/*
	|--------------------------------------------------------------------------
	| CRUD Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Reads a product from the database and sets its data to the class.
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 */
	public function read( &$product ) {
		$product->set_defaults();

		if ( ! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || 'product_variation' !== $post_object->post_type ) {
			return;
		}

		$id = $product->get_id();
		$product->set_parent_id( $post_object->post_parent );
		$parent_id = $product->get_parent_id();

		// The post doesn't have a parent id, therefore its invalid and we should prevent this being created.
		if ( empty( $parent_id ) ) {
			throw new Exception( sprintf( 'No parent product set for variation #%d', $product->get_id() ), 422 );
		}

		// The post parent is not a valid variable product so we should prevent this being created.
		$post_type = get_post_type( $product->get_parent_id() );
		if (  ! ( ($post_type === 'product') || WC_CPT_List::is_active( $post_type ) ) ) {
			throw new Exception( sprintf( 'Invalid parent for variation #%d', $product->get_id() ), 422 );
		}

		$product->set_props( array(
			'name'            => $post_object->post_title,
			'slug'            => $post_object->post_name,
			'date_created'    => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
			'date_modified'   => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
			'status'          => $post_object->post_status,
			'menu_order'      => $post_object->menu_order,
			'reviews_allowed' => 'open' === $post_object->comment_status,
		) );

		$this->read_product_data( $product );
		$this->read_extra_data( $product );
		$product->set_attributes( wc_get_product_variation_attributes( $product->get_id() ) );

		/**
		 * If a variation title is not in sync with the parent e.g. saved prior to 3.0, or if the parent title has changed, detect here and update.
		 */
		if ( version_compare( get_post_meta( $product->get_id(), '_product_version', true ), '3.0', '<' ) && 0 !== strpos( $post_object->post_title, get_post_field( 'post_title', $product->get_parent_id() ) ) ) {
			global $wpdb;

			$new_title = $this->generate_product_title( $product );
			$product->set_name( $new_title );
			$wpdb->update( $wpdb->posts, array( 'post_title' => $new_title ), array( 'ID' => $product->get_id() ) );
			clean_post_cache( $product->get_id() );
		}

		// Set object_read true once all data is read.
		$product->set_object_read( true );
	}

	/**
	 * Create a new product.
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 */
	public function create( &$product ) {
		if ( ! $product->get_date_created() ) {
			$product->set_date_created( current_time( 'timestamp', true ) );
		}

		$id = wp_insert_post( apply_filters( 'woocommerce_new_product_variation_data', array(
			'post_type'      => 'product_variation',
			'post_status'    => $product->get_status() ? $product->get_status() : 'publish',
			'post_author'    => get_current_user_id(),
			'post_title'     => $this->generate_product_title( $product ),
			'post_content'   => '',
			'post_parent'    => $product->get_parent_id(),
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'menu_order'     => $product->get_menu_order(),
			'post_date'      => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getOffsetTimestamp() ),
			'post_date_gmt'  => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getTimestamp() ),
		) ), true );

		if ( $id && ! is_wp_error( $id ) ) {
			$product->set_id( $id );

			$this->update_post_meta( $product, true );
			$this->update_terms( $product, true );
			$this->update_attributes( $product, true );
			$this->handle_updated_props( $product );

			$product->save_meta_data();
			$product->apply_changes();

			$this->update_version_and_type( $product );

			$this->clear_caches( $product );

			do_action( 'woocommerce_new_product_variation', $id );
		}
	}

	/**
	 * Updates an existing product.
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 */
	public function update( &$product ) {
		$product->save_meta_data();
		$changes = $product->get_changes();
		$title   = $this->generate_product_title( $product );

		// Only update the post when the post data changes.
		if ( $title !== $product->get_name( 'edit' ) || array_intersect( array( 'parent_id', 'status', 'menu_order', 'date_created', 'date_modified' ), array_keys( $changes ) ) ) {
			wp_update_post( array(
				'ID'                => $product->get_id(),
				'post_title'        => $title,
				'post_parent'       => $product->get_parent_id( 'edit' ),
				'comment_status'    => 'closed',
				'post_status'       => $product->get_status( 'edit' ) ? $product->get_status( 'edit' ) : 'publish',
				'menu_order'        => $product->get_menu_order( 'edit' ),
				'post_date'         => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getOffsetTimestamp() ),
				'post_date_gmt'     => gmdate( 'Y-m-d H:i:s', $product->get_date_created( 'edit' )->getTimestamp() ),
				'post_modified'     => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $product->get_date_modified( 'edit' )->getOffsetTimestamp() ) : current_time( 'mysql' ),
				'post_modified_gmt' => isset( $changes['date_modified'] ) ? gmdate( 'Y-m-d H:i:s', $product->get_date_modified( 'edit' )->getTimestamp() ) : current_time( 'mysql', 1 ),
			) );
			$product->read_meta_data( true ); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.
		}

		$this->update_post_meta( $product );
		$this->update_terms( $product );
		$this->update_attributes( $product );
		$this->handle_updated_props( $product );

		$product->apply_changes();

		$this->update_version_and_type( $product );

		$this->clear_caches( $product );

		do_action( 'woocommerce_update_product_variation', $product->get_id() );
	}

	/*
	|--------------------------------------------------------------------------
	| Additional Methods
	|--------------------------------------------------------------------------
	*/

	/**
	 * Generates a title with attribute information for a variation.
	 * Products will get a title of the form "Name - Value, Value" or just "Name".
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 * @return string
	 */
	protected function generate_product_title( $product ) {
		$attributes = (array) $product->get_attributes();

		// Don't include attributes if the product has 3+ attributes.
		$should_include_attributes = count( $attributes ) < 3;

		// Don't include attributes if an attribute name has 2+ words.
		if ( $should_include_attributes ) {
			foreach ( $attributes as $name => $value ) {
				if ( false !== strpos( $name, '-' ) ) {
					$should_include_attributes = false;
					break;
				}
			}
		}

		$should_include_attributes = apply_filters( 'woocommerce_product_variation_title_include_attributes', $should_include_attributes, $product );
		$separator                 = apply_filters( 'woocommerce_product_variation_title_attributes_separator', ' - ', $product );
		$title_base                = get_post_field( 'post_title', $product->get_parent_id() );
		$title_suffix              = $should_include_attributes ? wc_get_formatted_variation( $product, true, false ) : '';

		return apply_filters( 'woocommerce_product_variation_title', rtrim( $title_base . $separator . $title_suffix, $separator ), $product, $title_base, $title_suffix );
	}

	/**
	 * Make sure we store the product version (to track data changes).
	 *
	 * @param WC_Product
	 * @since 3.0.0
	 */
	protected function update_version_and_type( &$product ) {
		update_post_meta( $product->get_id(), '_product_version', WC_VERSION );
	}

	/**
	 * Read post data.
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 */
	protected function read_product_data( &$product ) {
		$id = $product->get_id();
		$product->set_props( array(
			'description'       => get_post_meta( $id, '_variation_description', true ),
			'regular_price'     => get_post_meta( $id, '_regular_price', true ),
			'sale_price'        => get_post_meta( $id, '_sale_price', true ),
			'date_on_sale_from' => get_post_meta( $id, '_sale_price_dates_from', true ),
			'date_on_sale_to'   => get_post_meta( $id, '_sale_price_dates_to', true ),
			'manage_stock'      => get_post_meta( $id, '_manage_stock', true ),
			'stock_status'      => get_post_meta( $id, '_stock_status', true ),
			'shipping_class_id' => current( $this->get_term_ids( $id, 'product_shipping_class' ) ),
			'virtual'           => get_post_meta( $id, '_virtual', true ),
			'downloadable'      => get_post_meta( $id, '_downloadable', true ),
			'downloads'         => array_filter( (array) get_post_meta( $id, '_downloadable_files', true ) ),
			'gallery_image_ids' => array_filter( explode( ',', get_post_meta( $id, '_product_image_gallery', true ) ) ),
			'download_limit'    => get_post_meta( $id, '_download_limit', true ),
			'download_expiry'   => get_post_meta( $id, '_download_expiry', true ),
			'image_id'          => get_post_thumbnail_id( $id ),
			'backorders'        => get_post_meta( $id, '_backorders', true ),
			'sku'               => get_post_meta( $id, '_sku', true ),
			'stock_quantity'    => get_post_meta( $id, '_stock', true ),
			'weight'            => get_post_meta( $id, '_weight', true ),
			'length'            => get_post_meta( $id, '_length', true ),
			'width'             => get_post_meta( $id, '_width', true ),
			'height'            => get_post_meta( $id, '_height', true ),
			'tax_class'         => ! metadata_exists( 'post', $id, '_tax_class' ) ? 'parent' : get_post_meta( $id, '_tax_class', true ),
		) );

		if ( $product->is_on_sale( 'edit' ) ) {
			$product->set_price( $product->get_sale_price( 'edit' ) );
		} else {
			$product->set_price( $product->get_regular_price( 'edit' ) );
		}

		$parent_object = get_post( $product->get_parent_id() );
		$product->set_parent_data( array(
			'title'             => $parent_object->post_title,
			'sku'               => get_post_meta( $product->get_parent_id(), '_sku', true ),
			'manage_stock'      => get_post_meta( $product->get_parent_id(), '_manage_stock', true ),
			'backorders'        => get_post_meta( $product->get_parent_id(), '_backorders', true ),
			'stock_quantity'    => get_post_meta( $product->get_parent_id(), '_stock', true ),
			'weight'            => get_post_meta( $product->get_parent_id(), '_weight', true ),
			'length'            => get_post_meta( $product->get_parent_id(), '_length', true ),
			'width'             => get_post_meta( $product->get_parent_id(), '_width', true ),
			'height'            => get_post_meta( $product->get_parent_id(), '_height', true ),
			'tax_class'         => get_post_meta( $product->get_parent_id(), '_tax_class', true ),
			'shipping_class_id' => absint( current( $this->get_term_ids( $product->get_parent_id(), 'product_shipping_class' ) ) ),
			'image_id'          => get_post_thumbnail_id( $product->get_parent_id() ),
		) );

		// Pull data from the parent when there is no user-facing way to set props.
		$product->set_sold_individually( get_post_meta( $product->get_parent_id(), '_sold_individually', true ) );
		$product->set_tax_status( get_post_meta( $product->get_parent_id(), '_tax_status', true ) );
		$product->set_cross_sell_ids( get_post_meta( $product->get_parent_id(), '_crosssell_ids', true ) );
	}

	/**
	 * For all stored terms in all taxonomies, save them to the DB.
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 * @param bool Force update. Used during create.
	 */
	protected function update_terms( &$product, $force = false ) {
		$changes = $product->get_changes();

		if ( $force || array_key_exists( 'shipping_class_id', $changes ) ) {
			wp_set_post_terms( $product->get_id(), array( $product->get_shipping_class_id( 'edit' ) ), 'product_shipping_class', false );
		}
	}

	/**
	 * Update attribute meta values.
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 * @param bool Force update. Used during create.
	 */
	protected function update_attributes( &$product, $force = false ) {
		$changes = $product->get_changes();

		if ( $force || array_key_exists( 'attributes', $changes ) ) {
			global $wpdb;
			$attributes             = $product->get_attributes();
			$updated_attribute_keys = array();
			foreach ( $attributes as $key => $value ) {
				update_post_meta( $product->get_id(), 'attribute_' . $key, $value );
				$updated_attribute_keys[] = 'attribute_' . $key;
			}

			// Remove old taxonomies attributes so data is kept up to date - first get attribute key names.
			$delete_attribute_keys = $wpdb->get_col( $wpdb->prepare( "SELECT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE 'attribute_%%' AND meta_key NOT IN ( '" . implode( "','", array_map( 'esc_sql', $updated_attribute_keys ) ) . "' ) AND post_id = %d;", $product->get_id() ) );

			foreach ( $delete_attribute_keys as $key ) {
				delete_post_meta( $product->get_id(), $key );
			}
		}
	}

	/**
	 * Helper method that updates all the post meta for a product based on it's settings in the WC_Product class.
	 *
	 * @since 3.0.0
	 * @param WC_Product
	 * @param bool Force update. Used during create.
	 */
	public function update_post_meta( &$product, $force = false ) {
		$meta_key_to_props = array(
			'_variation_description' => 'description',
		);

		$props_to_update = $force ? $meta_key_to_props : $this->get_props_to_update( $product, $meta_key_to_props );

		foreach ( $props_to_update as $meta_key => $prop ) {
			$value   = $product->{"get_$prop"}( 'edit' );
			$updated = update_post_meta( $product->get_id(), $meta_key, $value );
			if ( $updated ) {
				$this->updated_props[] = $prop;
			}
		}

		parent::update_post_meta( $product, $force );
	}
}
