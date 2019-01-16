<?php

//add custom column headers to CSV export
function wc_csv_export_modify_column_headers( $column_headers ) { 

	$new_headers = array(
		'item_category' => 'item_category',
		'paid_date' => 'paid_date',
		'item_membership_discount' => 'item_membership_discount'
		// add other column headers here in the format column_key => Column Name
	);

	return array_merge( $column_headers, $new_headers );
}
add_filter( 'wc_customer_order_csv_export_order_headers', 'wc_csv_export_modify_column_headers' );


//reorder columns based on what column they're adjacent to
function sv_wc_csv_export_reorder_columns( $column_headers ) {
	
	// // remove order total from the original set of column headers, otherwise it will be duplicated
	// unset( $column_headers['order_total'] );

	//turn off order date column to be replaced with paid_date
	unset( $column_headers['order_date'] );

	$new_column_headers = array();
	foreach ( $column_headers as $column_key => $column_name ) {
		$new_column_headers[ $column_key ] = $column_name;
		
		if ( 'item_name' == $column_key ) {
			// add item_category immediately after item_name
			$new_column_headers['item_category'] = 'item_category';
		}

		if ( 'order_id' == $column_key ) {
			// add item_category immediately after order_id
			$new_column_headers['paid_date'] = 'paid_date';
		}

		if ( 'item_subtotal' == $column_key ) {
			// add item_membership_discount immediately after item_subtotal
			$new_column_headers['item_membership_discount'] = 'item_membership_discount';
		}

		if ( 'item_subtotal' == $column_key ) {
			// add item_membership_discount immediately after item_subtotal
			$new_column_headers['item_total_after_membership_discount'] = 'item_total_after_membership_discount';
		}


	}
	return $new_column_headers;

}
add_filter( 'wc_customer_order_csv_export_order_headers', 'sv_wc_csv_export_reorder_columns' );



function sv_wc_csv_export_modify_line_item( $line_item, $item, $product, $order ) {

	$new_item_data = array();

	foreach ( $line_item as $key => $data ) {

		$new_item_data[ $key ] = $data;

		if ( 'sku' === $key ) {

			//make any modifications to line item in this block

			//set item product category
			$product_categories = get_the_terms( $product->id, 'product_cat' );
			$category_text = '';

			foreach ($product_categories as $category) {
				$category_slug = $category->slug;
				$category_text = $category_text . ' ' . $category_slug;
			}

			if (strpos($category_text, 'classes') !== false ) {
				$product_category = 'Classes';
			} elseif (strpos($category_text, 'events') !== false ) {
				$product_category = 'Events';
			} elseif (strpos($category_text, 'membership-tiers') !== false ) {
				$product_category = 'Memberships';
			} elseif (strpos($category_text, 'donation-tiers') !== false ) {
				$product_category = 'Donations';
			} elseif (strpos($category_text, 'fees') !== false ) {
				$product_category = 'Fees';
			} else {
				$product_category = 'Uncategorized';
			}

			$new_item_data['item_category'] = $product_category;


			//modify the date to be paid date instead of created date
			$date_paid = $order->get_date_paid();
			$date_paid = date_format($date_paid,"n/j/Y");	
			$new_item_data['paid_date'] = $date_paid;


			//set item membership discount
			$item_membership_discount = '0';
			$product_name = $product->name;
			$product_price = $product->get_price();
			$item_total_after_membership_discount = $product_price;

			$fees = $order->get_fees();

			foreach ($fees as $fee) {

				$fee_name = $fee['name'];
				$fee_total = $fee['total'];
				$fee_name = str_replace('Membership Discount: ', '', $fee_name);

				if( $product_name == $fee_name ){
					$item_membership_discount = $fee_total;
					$item_total_after_membership_discount = $product_price + $fee_total;
					break;
				}

			}

			$new_item_data['item_membership_discount'] = $item_membership_discount;
			$new_item_data['item_total_after_membership_discount'] = $item_total_after_membership_discount;

		}

	}

	return $new_item_data;
}
add_filter( 'wc_customer_order_csv_export_order_line_item', 'sv_wc_csv_export_modify_line_item', 10, 4 );





function sv_wc_csv_export_order_row_one_row_per_item_category( $order_data, $item ) {

	$order_data['item_category'] = $item['item_category'];
	$order_data['item_membership_discount'] = $item['item_membership_discount'];
	$order_data['item_total_after_membership_discount'] = $item['item_total_after_membership_discount'];
	$order_data['paid_date'] = $item['paid_date'];
	return $order_data;

}
add_filter( 'wc_customer_order_csv_export_order_row_one_row_per_item', 'sv_wc_csv_export_order_row_one_row_per_item_category', 10, 2 );




//If CSV export is set to one row per item format - use this helper function
if ( ! function_exists( 'sv_wc_csv_export_is_one_row' ) ) :

/**
 * Helper function to check the export format
 *
 * @param \WC_Customer_Order_CSV_Export_Generator $csv_generator the generator instance
 * @return bool - true if this is a one row per item format
 */
function sv_wc_csv_export_is_one_row( $csv_generator ) {

	$one_row_per_item = false;

	if ( version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ) {

		// pre 4.0 compatibility
		$one_row_per_item = ( 'default_one_row_per_item' === $csv_generator->order_format || 'legacy_one_row_per_item' === $csv_generator->order_format );

	} elseif ( isset( $csv_generator->format_definition ) ) {

		// post 4.0 (requires 4.0.3+)
		$one_row_per_item = 'item' === $csv_generator->format_definition['row_type'];
	}

	return $one_row_per_item;
}

endif;

