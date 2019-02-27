<?php
/**
 * WC_PB_Coupon class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Product Bundles
 * @since    5.8.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Bundle Coupon functions and filters.
 *
 * @class    WC_PB_Coupon
 * @version  5.8.0
 */
class WC_PB_Coupon {

	/*
	 * Initilize.
	 */
	public static function init() {

		// Coupons - inherit bundled item coupon validity from parent.
		add_filter( 'woocommerce_coupon_is_valid_for_product', array( __CLASS__, 'coupon_is_valid_for_product' ), 10, 4 );
	}

	/**
	 * Inherit coupon validity from parent:
	 *
	 * - Coupon is invalid for bundled item if parent is excluded.
	 * - Coupon is valid for bundled item if valid for parent, unless bundled item is excluded.
	 *
	 * @since  5.8.0
	 *
	 * @param  bool        $valid
	 * @param  WC_Product  $product
	 * @param  WC_Coupon   $coupon
	 * @param  array       $item
	 * @return boolean
	 */
	public static function coupon_is_valid_for_product(  $valid, $product, $coupon, $item  ) {

		if ( is_a( $item, 'WC_Order_Item_Product' ) ) {

			if ( $container_cart_item = wc_pb_get_bundled_order_item_container( $item ) ) {

				$bundle    = $container_cart_item->get_product();
				$bundle_id = $container_cart_item[ 'product_id' ];
			}

		} elseif ( ! empty( WC()->cart ) ) {

			if ( $container_cart_item = wc_pb_get_bundled_cart_item_container( $item ) ) {

				$bundle    = $container_cart_item[ 'data' ];
				$bundle_id = $container_cart_item[ 'product_id' ];
			}
		}

		if ( ! isset( $bundle, $bundle_id ) || empty( $container_cart_item ) ) {
			return $valid;
		}

		/**
		 * 'woocommerce_bundles_inherit_coupon_validity' filter.
		 *
		 * Uset this to prevent coupon valididty inheritance for bundled products.
		 *
		 * @param  boolean     $inherit
		 * @param  WC_Product  $product
		 * @param  WC_Coupon   $coupon
		 * @param  array       $cart_item
		 * @param  array       $container_cart_item
		 */
		if ( apply_filters( 'woocommerce_bundles_inherit_coupon_validity', true, $product, $coupon, $item, $container_cart_item ) ) {

			$product_id = $product->get_id();
			$parent_id  = $product->get_parent_id();

			$excluded_product_ids        = $coupon->get_excluded_product_ids();
			$excluded_product_categories = $coupon->get_excluded_product_categories();
			$excludes_sale_items         = $coupon->get_exclude_sale_items();

			if ( $valid ) {

				$parent_excluded = false;

				// Parent ID excluded from the discount.
				if ( sizeof( $excluded_product_ids ) > 0 ) {
					if ( in_array( $bundle_id, $excluded_product_ids ) ) {
						$parent_excluded = true;
					}
				}

				// Parent category excluded from the discount.
				if ( sizeof( $excluded_product_categories ) > 0 ) {

					$product_cats = wc_get_product_cat_ids( $bundle_id );

					if ( sizeof( array_intersect( $product_cats, $excluded_product_categories ) ) > 0 ) {
						$parent_excluded = true;
					}
				}

				// Sale Items excluded from discount and parent on sale.
				if ( $excludes_sale_items ) {

					$product_ids_on_sale = wc_get_product_ids_on_sale();

					if ( in_array( $bundle_id, $product_ids_on_sale, true ) ) {
						$parent_excluded = true;
					}
				}

				if ( $parent_excluded ) {
					$valid = false;
				}

			} else {

				$bundled_product_excluded = false;

				// Bundled product ID excluded from the discount.
				if ( sizeof( $excluded_product_ids ) > 0 ) {
					if ( in_array( $product_id, $excluded_product_ids ) || ( $parent_id && in_array( $parent_id, $excluded_product_ids ) ) ) {
						$bundled_product_excluded = true;
					}
				}

				// Bundled product category excluded from the discount.
				if ( sizeof( $excluded_product_categories ) > 0 ) {

					$product_cats = $parent_id ? wc_get_product_cat_ids( $parent_id ) : wc_get_product_cat_ids( $product_id );

					if ( sizeof( array_intersect( $product_cats, $excluded_product_categories ) ) > 0 ) {
						$bundled_product_excluded = true;
					}
				}

				// Bundled product on sale and sale items excluded from discount.
				if ( $excludes_sale_items ) {

					$product_ids_on_sale = wc_get_product_ids_on_sale();

					if ( in_array( $product_id, $product_ids_on_sale ) || ( $parent_id && in_array( $parent_id, $product_ids_on_sale ) ) ) {
						$bundled_product_excluded = true;
					}
				}

				if ( ! $bundled_product_excluded && $coupon->is_valid_for_product( $bundle, $container_cart_item ) ) {
					$valid = true;
				}
			}
		}

		return $valid;
	}
}

WC_PB_Coupon::init();
