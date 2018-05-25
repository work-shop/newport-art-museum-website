<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
	
	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<div class="row cart-headings">
		<div class="col-md-6">
			<h4 class="bold cart-heading">Product</h4>
		</div>
		<div class="col-md">
			<h4 class="bold cart-heading">Price</h4>
		</div>
		<div class="col-md">
			<h4 class="bold cart-heading">Quantity</h4>
		</div>
		<div class="col-md">
			<h4 class="bold cart-heading righted">Total</h4>
		</div>
		<div class="col-md-1 product-remove-heading">
		</div>
	</div>

	<div class="cart-contents">

		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ):
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
			$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		?>
		<div class="row cart-row woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

			<div class="product-name col-md-6" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>"><?php
			if ( ! $product_permalink ) {
				echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;';
			} else {
				echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key );
			}

								// Meta data.
			echo wc_get_formatted_cart_item_data( $cart_item );

								// Backorder notification.
			if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
				echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
			}
			?></div>

			<div class="product-price col-md" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
				<?php
				echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				?>
			</div>

			<div class="product-quantity col-md" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>"><?php
			if ( $_product->is_sold_individually() ) {
				$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			} else {
				$product_quantity = woocommerce_quantity_input( array(
					'input_name'    => "cart[{$cart_item_key}][qty]",
					'input_value'   => $cart_item['quantity'],
					'max_value'     => $_product->get_max_purchase_quantity(),
					'min_value'     => '0',
					'product_name'  => $_product->get_name(),
				), $_product, false );
			}

			echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
			?></div>

			<div class="product-subtotal col-md righted" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
				<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
			</div>

			<div class="col-md-1 product-remove">
				<?php
							// @codingStandardsIgnoreLine
				echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
					'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
					esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
					__( 'Remove this item', 'woocommerce' ),
					esc_attr( $product_id ),
					esc_attr( $_product->get_sku() )
				), $cart_item_key );
				?>
			</div>

		</div>
	<?php endif; ?>
<?php endforeach; ?>
		<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ):
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
			$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		?>
		<div class="row cart-row woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

			<div class="product-name col-md-6" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>"><?php
			if ( ! $product_permalink ) {
				echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;';
			} else {
				echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key );
			}

								// Meta data.
			echo wc_get_formatted_cart_item_data( $cart_item );

								// Backorder notification.
			if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
				echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
			}
			?></div>

			<div class="product-price col-md" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
				<?php
				echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				?>
			</div>

			<div class="product-quantity col-md" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>"><?php
			if ( $_product->is_sold_individually() ) {
				$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			} else {
				$product_quantity = woocommerce_quantity_input( array(
					'input_name'    => "cart[{$cart_item_key}][qty]",
					'input_value'   => $cart_item['quantity'],
					'max_value'     => $_product->get_max_purchase_quantity(),
					'min_value'     => '0',
					'product_name'  => $_product->get_name(),
				), $_product, false );
			}

			echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
			?></div>

			<div class="product-subtotal col-md righted" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
				<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
			</div>

			<div class="col-md-1 product-remove">
				<?php
							// @codingStandardsIgnoreLine
				echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
					'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
					esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
					__( 'Remove this item', 'woocommerce' ),
					esc_attr( $product_id ),
					esc_attr( $_product->get_sku() )
				), $cart_item_key );
				?>
			</div>

		</div>
	<?php endif; ?>
<?php endforeach; ?>
		<?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ):
		$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

		if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) :
			$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		?>
		<div class="row cart-row woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

			<div class="product-name col-md-6" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>"><?php
			if ( ! $product_permalink ) {
				echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;';
			} else {
				echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key );
			}

								// Meta data.
			echo wc_get_formatted_cart_item_data( $cart_item );

								// Backorder notification.
			if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
				echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
			}
			?></div>

			<div class="product-price col-md" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
				<?php
				echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				?>
			</div>

			<div class="product-quantity col-md" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>"><?php
			if ( $_product->is_sold_individually() ) {
				$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
			} else {
				$product_quantity = woocommerce_quantity_input( array(
					'input_name'    => "cart[{$cart_item_key}][qty]",
					'input_value'   => $cart_item['quantity'],
					'max_value'     => $_product->get_max_purchase_quantity(),
					'min_value'     => '0',
					'product_name'  => $_product->get_name(),
				), $_product, false );
			}

			echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
			?></div>

			<div class="product-subtotal col-md righted" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
				<?php echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); ?>
			</div>

			<div class="col-md-1 product-remove">
				<?php
							// @codingStandardsIgnoreLine
				echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
					'<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s">&times;</a>',
					esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
					__( 'Remove this item', 'woocommerce' ),
					esc_attr( $product_id ),
					esc_attr( $_product->get_sku() )
				), $cart_item_key );
				?>
			</div>

		</div>
	<?php endif; ?>
<?php endforeach; ?>

</div>

<?php do_action( 'woocommerce_cart_contents' ); ?>

<div class="row cart-discount cart-row bg-light">
	<?php if ( wc_coupons_enabled() ) { ?>
		<!-- <div class="coupon"> -->
			<div class="col-md-6">
				<h4 class="bold">
					Have a Discount Code?
				</h4>
			</div>
			<div class="d-flex col-md-6 col-discount-code justify-content-end">
				<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Discount Code', 'woocommerce' ); ?>" /> <input type="submit" class="button button-small ml1" name="apply_coupon" value="<?php esc_attr_e( 'Apply Discount', 'woocommerce' ); ?>" />
			</div>
<!-- 			<div class="d-flex col-apply-discount justify-content-end">
				
			</div> -->
			<?php do_action( 'woocommerce_cart_coupon' ); ?>
	<!-- 	</div> -->
	<?php } ?>
</div>
<div class="row cart-update">
	<div class="col-md-12">
		<button type="submit" class="button button-centered" id="update-cart-button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>">
			<?php esc_html_e( 'Update cart', 'woocommerce' ); ?>
		</button>
	</div>
</div>

<?php do_action( 'woocommerce_cart_actions' ); ?>

<?php wp_nonce_field( 'woocommerce-cart' ); ?>
</div>

<?php do_action( 'woocommerce_after_cart_contents' ); ?>
<?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>


<div class="cart-collaterals">
	<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action( 'woocommerce_cart_collaterals' );
		?>
	</div>

	<?php do_action( 'woocommerce_after_cart' ); ?>
