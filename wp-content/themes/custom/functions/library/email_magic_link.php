<?php 


function email_magic_link( $data ){
	// load the mailer class
	$mailer = WC()->mailer();
	//format the email
	$recipient = "someone@somewhere.com";
	$subject = __("Hi! Here is a custom notification from us!", 'theme_name');
	$content = get_custom_email_html( $order, $subject, $mailer );
	$headers = "Content-Type: text/html\r\n";
	//send the email through wordpress
	$mailer->send( $recipient, $subject, $content, $headers );

	return true;

}

add_action( 'rest_api_init', function () {
	register_rest_route( 'email/v1', '/magic', array(
		'methods' => WP_REST_Server::ALLMETHODS,
		'callback' => 'email_magic_link',
	) );
} );


function get_custom_email_html( $order, $heading = false, $mailer ) {
	$template = 'emails/my-custom-email-i-want-to-send.php';
	return wc_get_template_html( $template, array(
		'order'         => $order,
		'email_heading' => $heading,
		'sent_to_admin' => false,
		'plain_text'    => false,
		'email'         => $mailer
	) );
}


?>