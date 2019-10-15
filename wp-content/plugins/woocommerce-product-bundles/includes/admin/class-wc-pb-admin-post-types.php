<?php
/**
 * WC_PB_Admin_Post_Types class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add hooks to the edit posts view for the 'product' post type.
 *
 * @class    WC_PB_Admin_Post_Types
 * @version  5.9.0
 */
class WC_PB_Admin_Post_Types {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Add details to admin product stock info when the bundled stock is insufficient.
		add_filter( 'woocommerce_admin_stock_html', array( __CLASS__, 'admin_stock_html' ), 10, 2 );
	}

	/**
	 * Add details to admin stock info when contents stock is insufficient.
	 *
	 * @param  string      $stock_status
	 * @param  WC_Product  $product
	 * @return string
	 */
	public static function admin_stock_html( $stock_status, $product ) {

		if ( 'bundle' === $product->get_type() ) {
			if ( $product->is_parent_in_stock() && $product->contains( 'out_of_stock_strict' ) ) {

				ob_start();

				?><mark class="outofstock insufficient_stock"><?php _e( 'Insufficient stock', 'woocommerce-product-bundles' ); ?></mark>
				<div class="row-actions">
					<span class="view"><a href="<?php echo admin_url( 'admin.php?page=wc-reports&tab=stock&report=insufficient_stock&bundle_id=' . $product->get_id() ) ?>" rel="bookmark" aria-label="<?php _e( 'View Report', 'woocommerce-product-bundles' ); ?>"><?php _e( 'View Report', 'woocommerce-product-bundles' ); ?></a></span>
				</div><?php

				$stock_status = ob_get_clean();
			}
		}

		return $stock_status;
	}
}

WC_PB_Admin_Post_Types::init();
