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

?>
