<?php
/**
 * Variable product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}
/**  @var WC_Product_Gift_Card $product */
global $product;
// Enqueue variation scripts
do_action ( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="gift-cards_form cart" method="post" enctype='multipart/form-data'
      data-product_id="<?php echo absint ( yit_get_prop ( $product, 'id' ) ); ?>">
	<?php do_action ( 'woocommerce_before_gift-cards_form' ); ?>
	<?php if ( ! $product->is_purchasable () ) : ?>
		<p class="gift-card-not-valid">
			<?php _e ( "This product cannot be purchased", "ywqc" ); ?>
		</p>
	<?php else : ?>
		<table class="gift-cards-list" cellspacing="0">
			<tbody>
			<tr>
				<td class="label"><label
						for="gift_amounts"><?php echo __ ( "Choose amount", 'yith-woocommerce-gift-cards' ); ?></label>
				</td>
				<td class="value">
					<?php
					$selected = isset( $_REQUEST['gift_amounts'] ) ? wc_clean ( $_REQUEST['gift_amounts'] ) : '';
					
					echo '<select id="gift_amounts" name="gift_amounts">';
					echo '<option value="">' . __ ( "Choose an amount", 'yith-woocommerce-gift-cards' ) . '</option>';
					
					foreach ( $product->get_gift_card_amounts () as $amount ) {
						
						echo '<option value="' . $amount . '" ' . selected ( $amount, $selected, false ) . '>' . wc_price ( $amount ) . '</option>';
					}
					
					echo '</select>';
					
					?>
				</td>
			</tr>
			</tbody>
		</table>
		
		<?php do_action ( 'woocommerce_before_add_to_cart_button' ); ?>
		
		<div class="single_variation_wrap" style="display:none;">
			<?php
			/**
			 * woocommerce_before_single_variation Hook
			 */
			do_action ( 'woocommerce_before_single_variation' );
			
			/**
			 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
			 * @since  2.4.0
			 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
			 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action ( 'woocommerce_single_variation' );
			
			/**
			 * woocommerce_after_single_variation Hook
			 */
			do_action ( 'woocommerce_after_single_variation' );
			?>
		</div>
		
		<?php do_action ( 'woocommerce_after_add_to_cart_button' ); ?>
	<?php endif; ?>
	
	<?php do_action ( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action ( 'woocommerce_after_add_to_cart_form' ); ?>
