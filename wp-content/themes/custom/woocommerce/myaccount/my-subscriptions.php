<?php
/**
 * My Subscriptions section on the My Account page
 *
 * @author 		Prospress
 * @category 	WooCommerce Subscriptions/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php 
// TRUE if the user was imported via the backend tools.
$user_was_imported = NAM_Membership::user_was_imported();
?>

<div class="woocommerce_account_subscriptions">

	<?php if ( WC_Subscriptions::is_woocommerce_pre( '2.6' ) ) : ?>
		<h2><?php esc_html_e( 'My Memberships', 'woocommerce-subscriptions' ); ?></h2>
	<?php endif; ?>

	<?php if ( ! empty( $subscriptions ) ) : ?>
		<table class="shop_table shop_table_responsive my_account_subscriptions my_account_orders">
			<thead class="memberships-table-header">
				<tr>
					<th class="subscription-id order-number"><span class="nobr"><?php esc_html_e( 'Membership', 'woocommerce-subscriptions' ); ?></span></th>
					<th class="subscription-status order-status"><span class="nobr"><?php esc_html_e( 'Status', 'woocommerce-subscriptions' ); ?></span></th>
					<th class="subscription-next-payment order-date"><span class="nobr"><?php echo esc_html_x( 'Expiration Date', 'table heading', 'woocommerce-subscriptions' ); ?></span></th>
					<?php if( $user_was_imported === false ): ?>
						<th class="subscription-total order-total"><span class="nobr"><?php echo esc_html_x( 'Total', 'table heading', 'woocommerce-subscriptions' ); ?></span></th>
					<?php endif; ?>
					<th class="subscription-actions order-actions hidden-xs">&nbsp;</th>
				</tr>
			</thead>

			<tbody>
				<?php /** @var WC_Subscription $subscription */ ?>
				<?php foreach ( $subscriptions as $subscription_id => $subscription ) : ?>
					<?php
					// the Membership Tier post represented by the product in this subscription
					$membership = NAM_Membership::get_membership_for_subscription( $subscription );
					?>
					<tr class="order">
						<td class="subscription-id order-number" data-title="<?php esc_attr_e( 'ID', 'woocommerce-subscriptions' ); ?>">
							<?php //get subscription type here ?>
							<a href="<?php echo esc_url( $subscription->get_view_order_url() ); ?>">
								<?php 
								if ( sizeof( $subscription_items = $subscription->get_items() ) > 0 ) {
									foreach ( $subscription_items as $item_id => $item ) {
										$_product  = apply_filters( 'woocommerce_subscriptions_order_item_product', $subscription->get_product_from_item( $item ), $item );
										if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
											echo $item['name'];
											$membership_name = $item['name'];
										}
									}
								}
								?>
							</a>
							<?php do_action( 'woocommerce_my_subscriptions_after_subscription_id', $subscription ); ?>
						</td>
						<td class="subscription-status order-status" data-title="<?php esc_attr_e( 'Status', 'woocommerce-subscriptions' ); ?>">
							<?php $status = wcs_get_subscription_status_name( $subscription->get_status() ); ?>
							<?php if ( $status === 'On hold'): echo 'Expired'; else: echo $status; endif; ?>
							<?php //echo esc_attr( wcs_get_subscription_status_name( $subscription->get_status() ) ); ?>
						</td>
						<td class="subscription-next-payment order-date" data-title="<?php echo esc_attr_x( 'Next Payment', 'table heading', 'woocommerce-subscriptions' ); ?>">
							<?php if( $membership_name == 'Lifetime Membership' ): ?>
								Never
								<?php else: ?>
									<?php if ( $subscription->get_status() === 'on-hold' ): ?>
										<?php echo $subscription->get_date_to_display( 'last_order_date_created' ); ?>
										<?php else: ?>
											<?php echo esc_attr( $subscription->get_date_to_display( 'next_payment' ) ); ?>
											<?php if ( ! $subscription->is_manual() && $subscription->has_status( 'active' ) && $subscription->get_time( 'next_payment' ) > 0 ) : ?>
											<?php
											// translators: placeholder is the display name of a payment gateway a subscription was paid by
											$payment_method_to_display = sprintf( __( 'Via %s', 'woocommerce-subscriptions' ), $subscription->get_payment_method_to_display() );
											$payment_method_to_display = apply_filters( 'woocommerce_my_subscriptions_payment_method', $payment_method_to_display, $subscription );
											?>
										<?php endif; ?>
										<br/><small><?php echo esc_attr( $payment_method_to_display ); ?></small>
									<?php endif; ?>
								<?php endif; ?>
							</td>
							<?php if( $user_was_imported === false ): ?>
								<td class="subscription-total order-total" data-title="<?php echo esc_attr_x( 'Total', 'Used in data attribute. Escaped', 'woocommerce-subscriptions' ); ?>">
									<?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?>
								</td>
							<?php endif; ?>
							<td class="subscription-actions order-actions">
								<a href="<?php echo esc_url( $subscription->get_view_order_url() ) ?>" class="button view"><?php echo esc_html_x( 'View', 'view a subscription', 'woocommerce-subscriptions' ); ?></a>
								<?php do_action( 'woocommerce_my_subscriptions_actions', $subscription ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>

			</table>
			<?php else : ?>

				<div class="row notice">
					<div class="col">
						<h4 class="no_subscriptions bold">
							You have no memberships with this account. &nbsp; &nbsp;<a href="/join" class="button button-small">Purchase a membership</a>
							<?php
			// translators: placeholders are opening and closing link tags to take to the shop page
			//printf( esc_html__( 'You have no active subscriptions. Find your first subscription in the %sstore%s.', 'woocommerce-subscriptions' ), '<a href="' . esc_url( apply_filters( 'woocommerce_subscriptions_message_store_url', get_permalink( wc_get_page_id( 'shop' ) ) ) ) . '">', '</a>' );
							?>
						</h4>
					</div>
				</div>

			<?php endif; ?>

		</div>

		<?php
