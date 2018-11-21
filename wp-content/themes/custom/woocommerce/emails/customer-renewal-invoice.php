<?php
/**
 * Customer renewal invoice email
 *
 * @author  Brent Shepherd
 * @package WooCommerce_Subscriptions/Templates/Emails
 * @version 1.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
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

<?php if ( 'pending' == $order->get_status()) : ?>
	<p>
		Your Newport Art Museum Membership has expired. You can renew your membership on our website, by logging into your account. 
	</p>
	<p>
		<?php if($email_address): ?>
			Your account email address is <?php printf($email_address); ?>
			<br>
		<?php endif; ?>
		<?php if($user_login): ?>
			Your username is <?php printf($user_login); ?>
		<?php endif; ?>
	</p>
	<?php if( $user_was_imported === 'yes' ): ?>
		<p>
			<strong>
				Please note: your account was automatically created from our membership database. To renew your membership, you will need to activate your account, if you have not already. To activate your account, follow these instructions:
			</strong>
		</p>
		<ol>
			<li>Go to <a href="https://newportartmuseum.org/my-account/lost-password">https://newportartmuseum.org/my-account/lost-password</a></li> 
			<li>Enter your email address</li>
			<li>You will then receive an email from hello@newportartmuseum</li>
			<li>Follow the link in that email to create a password</li>
			<li>Log in </li>
			<li>Once you are logged in, click on 'Memberships', click on your membership, then click on the button that says 'Renew Now'. </li>
		</ol>
		<p>
			<?php
			echo wp_kses( sprintf( _x( ' If you have already activated your account, %2$s', 'In customer renewal invoice email', 'woocommerce-subscriptions' ), esc_html( get_bloginfo( 'name' ) ), '<a href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . esc_html__( 'Click Here to Renew Now.', 'woocommerce-subscriptions' ) . '</a>' ), array( 'a' => array( 'href' => true ) ) );
			?>
		</p>
		<br>
		<?php else: ?>
			<p>
				<?php
			// translators: %1$s: name of the blog, %2$s: link to checkout payment url, note: no full stop due to url at the end
				echo wp_kses( sprintf( _x( '%2$s', 'In customer renewal invoice email', 'woocommerce-subscriptions' ), esc_html( get_bloginfo( 'name' ) ), '<a href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . esc_html__( 'Click Here to Renew Now.', 'woocommerce-subscriptions' ) . '</a>' ), array( 'a' => array( 'href' => true ) ) );
				?>
			</p>
			<br>
		<?php endif; //end user was imported else ?>

		<?php elseif ( 'failed' == $order->get_status() ) : ?>
			<p>
				<?php
				// translators: %1$s: name of the blog, %2$s: link to checkout payment url, note: no full stop due to url at the end
				echo wp_kses( sprintf( _x( 'The automatic payment to renew your subscription with %1$s has failed. To reactivate the subscription, please login and pay for the renewal from your account page: %2$s', 'In customer renewal invoice email', 'woocommerce-subscriptions' ), esc_html( get_bloginfo( 'name' ) ), '<a href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . esc_html__( 'Pay Now &raquo;', 'woocommerce-subscriptions' ) . '</a>' ), array( 'a' => array( 'href' => true ) ) ); ?>
			</p>
		<?php endif; ?>
		<?php do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email ); ?>
		<p>If you have any questions, please <a href="https://newportartmuseum.org/contact" target="_blank">contact us.</a></p>
		<p>Thank you.</p>	
		<br><br>
		<?php printf($user_id); ?>
		<?php printf($user_was_imported); ?>
		<?php do_action( 'woocommerce_email_footer', $email ); ?>
