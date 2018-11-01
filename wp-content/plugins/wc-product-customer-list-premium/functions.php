<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.7.3
 */

// WooCommerce version check

if( ! function_exists('woocommerce_version_check') ) {
	function woocommerce_version_check( $version = '3.0' ) {
		if ( class_exists( 'WooCommerce' ) ) {
			global $woocommerce;
			if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
				return true;
			}
		}
		return false;
	}
}

// Admin notice

if( ! function_exists('wpcl_admin_message') ) {
	function wpcl_admin_message() {
		echo '<div class="error"><p>' . __('Woocommerce Product Customer List is enabled but not effective. It requires WooCommerce 2.2+ in order to work.', 'wc-product-customer-list') . '</p></div>';
	}
}

// Localize plugin

if( ! function_exists('wpcl_load_textdomain') ) {
	function wpcl_load_textdomain() {
		load_plugin_textdomain( 'wc-product-customer-list', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
	}
	add_action('plugins_loaded', 'wpcl_load_textdomain');
}

if ( wpcl_activation()->is__premium_only() ) {
	// Get true product sales (Premium)
	if( ! function_exists('wpcl_product_sales') ) {
		function wpcl_product_sales($product, $status) {
			$order_statuses = array_map( 'esc_sql', (array) $status );
			$order_statuses_string = "'" . implode( "', '", $order_statuses ) . "'";
			$productcount = array();
			global $wpdb;
			$item_sales = $wpdb->get_results( $wpdb->prepare(
				"SELECT o.ID as order_id, oi.order_item_id FROM
				{$wpdb->prefix}woocommerce_order_itemmeta oim
				INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
				ON oim.order_item_id = oi.order_item_id
				INNER JOIN $wpdb->posts o
				ON oi.order_id = o.ID
				WHERE oim.meta_key = %s
				AND oim.meta_value IN ( $product )
				AND o.post_status IN ( $order_statuses_string )
				AND o.post_type NOT IN ('shop_order_refund')
				ORDER BY o.ID DESC",
				'_product_id'
			));
			foreach( $item_sales as $sale ) {
				$order = wc_get_order( $sale->order_id );

				$refunded_qty = 0;
				$items = $order->get_items();
				foreach ($items as $item_id => $item) {
					if($item['product_id'] == $product) {
						$refunded_qty += $order->get_qty_refunded_for_item($item_id);
					}
				}
				$quantity = wc_get_order_item_meta( $sale->order_item_id, '_qty', true );
				$quantity += $refunded_qty;
				// Check for partially refunded orders
				if($quantity == 0) {
				// Order has been partially refunded
				} else {
					$productcount[] = $quantity;
				}
			}
			return array_sum($productcount);
		}
	}
}