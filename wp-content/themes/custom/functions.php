<?php

/** Theme-specific global constants for NAM */
define( '__ROOT__', dirname( __FILE__ ) );

require_once( __ROOT__ . '/functions/class-nam-abstract-taxonomy.php' );
require_once( __ROOT__ . '/functions/class-nam-abstract-custom-post-type.php' );
require_once( __ROOT__ . '/functions/class-nam-abstract-shadowed-post-type.php' );

require_once( __ROOT__ . '/functions/library/class-ws-cdn-url.php');

require_once( __ROOT__ . '/functions/post-types/class-post-types-archive-mapping.php');

require_once( __ROOT__ . '/functions/taxonomies/groups/class-nam-group.php');
require_once( __ROOT__ . '/functions/taxonomies/classes-categories/class-nam-classes-category.php');
require_once( __ROOT__ . '/functions/taxonomies/classes-categories/class-nam-classes-days.php');
require_once( __ROOT__ . '/functions/taxonomies/events-categories/class-nam-events-category.php');
require_once( __ROOT__ . '/functions/taxonomies/news-categories/class-nam-news-category.php');
require_once( __ROOT__ . '/functions/taxonomies/exhibitions-categories/class-nam-exhibitions-category.php');

require_once( __ROOT__ . '/functions/post-types/shop-products/class-nam-shop-product.php');
require_once( __ROOT__ . '/functions/post-types/donation-tiers/class-nam-donation-tier.php');
require_once( __ROOT__ . '/functions/post-types/membership-tiers/class-nam-membership-tier.php');
require_once( __ROOT__ . '/functions/post-types/exhibitions/class-nam-exhibition.php');
require_once( __ROOT__ . '/functions/post-types/classes/class-nam-class.php');
require_once( __ROOT__ . '/functions/post-types/events/class-nam-event.php');
require_once( __ROOT__ . '/functions/post-types/news/class-nam-news.php');
require_once( __ROOT__ . '/functions/post-types/fees/class-nam-fee.php');

require_once( __ROOT__ . '/functions/class-nam-membership.php' );
require_once( __ROOT__ . '/functions/class-nam-classes.php' );
require_once( __ROOT__ . '/functions/class-nam-events.php' );
require_once( __ROOT__ . '/functions/class-nam-cart.php' );
require_once( __ROOT__ . '/functions/class-nam-site-admin.php' );
require_once( __ROOT__ . '/functions/class-nam-site-init.php' );
require_once( __ROOT__ . '/functions/class-nam-membership-creator.php' );

require_once( __ROOT__ . '/functions/library/class-nam-helpers.php' );
require_once( __ROOT__ . '/functions/library/class-ws-flexible-content.php' );

require_once( __ROOT__ . '/functions/library/museum_status.php' );
require_once( __ROOT__ . '/functions/library/member_checker_api.php' );

new NAM_Site();
new NAM_Site_Admin();



//MISCELLANEOUS ADDITIONS

//remove woocommerce CSS
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );


//rename subscriber role to member
$val = get_option( 'wp_user_roles' );
$val['subscriber']['name'] = 'Member';
update_option( 'wp_user_roles', $val );


//rename order status
add_filter( 'wc_order_statuses', 'rename_order_statuses', 20, 1 );
function rename_order_statuses( $order_statuses ) {
	$order_statuses['wc-processing'] = _x( 'Paid', 'Order status', 'woocommerce' );
	return $order_statuses;
}

add_filter( 'bulk_actions-edit-shop_order', 'custom_dropdown_bulk_actions_shop_order', 20, 1 );
function custom_dropdown_bulk_actions_shop_order( $actions ) {
	$actions['mark_processing'] = __( 'Mark Paid', 'woocommerce' );
	return $actions;
}

foreach( array( 'post', 'shop_order' ) as $hook )
	add_filter( "views_edit-$hook", 'shop_order_modified_views' );

function shop_order_modified_views( $views ){

	if( isset( $views['wc-processing'] ) )
		$views['wc-processing'] = str_replace( 'Processing', __( 'Paid', 'woocommerce'), $views['wc-processing'] );

	return $views;
}


//hide woocommerce notices in admin that suggest connecting store to woocommerce
add_filter( 'woocommerce_helper_suppress_admin_notices', '__return_true' );


/**
 * Function adds a BCC header to emails that match our array
 *
 * @param string $headers The default headers being used
 * @param string $object  The email type/object that is being processed
 */
function add_bcc_to_certain_emails( $headers, $object ) {
	// email types/objects to add bcc to
	$add_bcc_to = array(
		'customer_renewal_invoice'		// Renewal invoice from WooCommerce Subscriptions
	);
	// if our email object is in our array
	if ( in_array( $object, $add_bcc_to ) ) {
		// change our headers
		$headers = array(
			$headers,
			'Bcc: info+nam-orders@workshop.co' ."\r\n",
		);
	}
	return $headers;
}
add_filter( 'woocommerce_email_headers', 'add_bcc_to_certain_emails', 10, 2 );


//hide the 'product has been added to your cart notice'
add_filter( 'wc_add_to_cart_message_html', '__return_false' );


//add capabilities to user roles
// $role_object = get_role( 'shop_manager' );
// // add $cap capability to this role object
// $role_object->add_cap( 'edit_users' );
// $role_object->add_cap( 'add_users' );
// $role_object->add_cap( 'create_users' );
// $role_object->add_cap( 'list_users' );

// function edit_shop_manager() {
//     // Get custom role
//     $shop_manager = get_role('shop_manager');
//     $shop_manager->add_cap('create_users');
//     $shop_manager->add_cap('edit_users');
//     $shop_manager->add_cap('manage_network_users');
//     $shop_manager->add_cap('delete_users');
//     $shop_manager->add_cap('list_users');
//     $shop_manager->add_cap('remove_users');
//     $shop_manager->add_cap('promote_users');
// }
// add_action( 'init', 'edit_shop_manager', 1000 );

/**
 * Modify the list of editable roles to prevent non-admin adding admin users.
 * @param  array $roles
 * @return array
 */
// function override_wc_modify_editable_roles( $roles ) {
// 	return false;
// 	// if ( ! current_user_can( 'administrator' ) ) {
// 	// 	//unset( $roles['administrator'] );
// 	// }
// 	// return $roles;
// }
// add_filter( 'editable_roles', 'override_wc_modify_editable_roles', 1 );

//automatically log user in after password reset
function action_woocommerce_reset_password( $user ) {
	$login=$_POST['reset_login'];
	$pass=$_POST['password_1'];
	$creds = array(
		'user_login' => $login,
		'user_password' => $pass,
		'remember' => true
	);

	wp_signon( $creds, false );
};
add_action( 'woocommerce_customer_reset_password', 'action_woocommerce_reset_password', 10, 1 );


/*
* Add columns to events post list
*/
function add_acf_columns ( $columns ) {
	return array_merge ( $columns, array ( 
		'event_date' => __ ( 'Event Date' )
	) );
}
add_filter ( 'manage_events_posts_columns', 'add_acf_columns' );

/*
* Add column content to events post list
*/
function event_custom_column ( $column, $post_id ) {
	switch ( $column ) {
		case 'event_date':
		the_field('event_date',$post_id);
		break;
	}
}
add_action ( 'manage_events_posts_custom_column', 'event_custom_column', 10, 2 );








//add custom column headers to CSV export
// function wc_csv_export_modify_column_headers( $column_headers ) { 

// 	$new_headers = array(
// 		'item_category' => 'item_category'
// 		// add other column headers here in the format column_key => Column Name
// 	);

// 	return array_merge( $column_headers, $new_headers );
// }
// add_filter( 'wc_customer_order_csv_export_order_headers', 'wc_csv_export_modify_column_headers' );



function sv_wc_csv_export_reorder_columns( $column_headers ) {
	// // remove order total from the original set of column headers, otherwise it will be duplicated
	// unset( $column_headers['item_category'] );
	$new_column_headers = array();
	foreach ( $column_headers as $column_key => $column_name ) {
		$new_column_headers[ $column_key ] = $column_name;
		if ( 'item_name' == $column_key ) {
			// add order total immediately after order_number
			$new_column_headers['item_category'] = 'item_category';
		}
	}
	return $new_column_headers;
}
add_filter( 'wc_customer_order_csv_export_order_headers', 'sv_wc_csv_export_reorder_columns' );



function sv_wc_csv_export_add_category_to_line_item( $line_item, $item, $product, $order ) {

	$new_item_data = array();

	foreach ( $line_item as $key => $data ) {

		$new_item_data[ $key ] = $data;

		if ( 'sku' === $key ) {
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

			if( $order->status === 'processing' ){ //this evaluates properly, but I can't set status in next line
				$new_item_data['status'] = 'Paid';
			}
			
		}

	}

	return $new_item_data;
}
add_filter( 'wc_customer_order_csv_export_order_line_item', 'sv_wc_csv_export_add_category_to_line_item', 10, 4 );




/**
 * Add the item_weight column data for the Default - One Row per Item format
 *
 * @param array $order_data the original order data
 * @param array $item       the item for this row
 * @return array - the updated order data
 */
function sv_wc_csv_export_order_row_one_row_per_item_category( $order_data, $item ) {

	$order_data['item_category'] = $item['item_category'];
	return $order_data;
}
add_filter( 'wc_customer_order_csv_export_order_row_one_row_per_item', 'sv_wc_csv_export_order_row_one_row_per_item_category', 10, 2 );



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




?>
