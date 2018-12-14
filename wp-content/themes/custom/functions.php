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
require_once( __ROOT__ . '/functions/library/csv_export_addons.php' );

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



add_action( 'profile_update', 'profile_update_notification', 10, 2 );

function profile_update_notification( $user_id, $old_user_data ) {

	$user = get_user_by('id', $user_id);

	if ( ! empty( $user ) ) {
		
		$old_first_name = $old_user_data->first_name;
		$old_last_name = $old_user_data->last_name;
		$old_email = $old_user_data->user_email;

		$old = '';
		$old .= get_user_meta( $user_id, 'billing_first_name', true );
		$old .= ' ';
		$old .= get_user_meta( $user_id, 'billing_last_name', true );
		$old .= '<br>';
		$old .= $old_email;
		$old .= '<br>';

		$new_first_name = $user->first_name;
		$new_last_name = $user->last_name;
		$new_email = $user->user_email;

		$new = '';
		$new .= $new_first_name;
		$new .= ' ';
		$new .= $new_last_name;
		$new .= '<br>';
		$new .= $new_email;

		$body = $new_first_name . ' ' . $new_last_name . ' just changed their account details on newportartmuseum.org'
		. '<br><br>' 
		. '<strong>OLD account details:</strong>' 
		. '<br>' 
		. $old
		. '<br><br>'
		. '<strong>NEW account details:</strong>' 
		. '<br>' 
		. $new
		. '<br><br>' 
		. 'Their billing address may have changed.' 
		. '<br><br>' 
		. 'View their account details <a href="https://newportartmuseum.org/wp-admin/user-edit.php?user_id=' . $user_id . '">here</a>.';

		$to = 'hello@newportartmuseum.org';
		$subject = $new_first_name . ' ' . $new_last_name . ' updated account details on newportartmuseum.org';
		$headers = array('Content-Type: text/html; charset=UTF-8');

		wp_mail( $to, $subject, $body, $headers );
	}

}



?>
