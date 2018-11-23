<?php 


add_action( 'rest_api_init', function () {
	register_rest_route( 'members/v1', '/status', array(
		'methods' => WP_REST_Server::ALLMETHODS,
		'callback' => 'check_member_by_email',
	) );
} );


function check_member_by_email(){
	$arr = array(
		'has_account' => false,
		'has_subscription' => false, 
		'subscription_is_active' => false
	);

	$email = $_GET['email'];
	$user = get_user_by('email', $email); 
	$user_id = $user->ID;
	if( $user_id ): 
		$arr['has_account'] = true;
		$has_subscription = wcs_user_has_subscription( $user_id );
		if( $has_subscription ):
			$subscriptions = wcs_get_users_subscriptions( $user_id );
			$active = wcs_user_has_subscription( $user_id, '', 'active' );
			$arr['has_subscription'] = true;
			$arr['subscription_is_active'] = $active;

		endif;
	endif;

	$response = json_encode($arr);
	return $response;

}


?>