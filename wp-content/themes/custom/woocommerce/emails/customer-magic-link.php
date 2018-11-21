<?php
/**
 * Customer Reset Password email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-reset-password.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates/Emails
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<?php 
$user = get_user_by('login',$user_login); 
$first_name = $user->user_firstname; 
$email_address = $user->user_email; 
$user_id = $user->ID;
$user_was_imported = get_user_meta( $user_id, '_nam_imported_member_user', true );
?>
<p>
	Hello<?php if( $first_name ): ?><?php printf(' '); printf( $first_name ); ?><?php endif; ?>,
</p>
<p>
	Click the link below to log in to your account. 
	<br>
	<br>
	<a href="#">Log In</a>
</p>
<?php if( $user_was_imported === 'yes' ): ?>
	<p>
		<strong>
			Please note: your account was automatically created from our membership database.
		</strong>
	</p>
<?php endif; ?>

<p>If you didn't make this request, ignore this email.</p>
<br>
<br>
<p>If you have any questions, please <a href="https://newportartmuseum.org/contact" target="_blank">contact us.</a></p>
<p>Thank you.</p>

<?php do_action( 'woocommerce_email_footer', $email ); ?>
