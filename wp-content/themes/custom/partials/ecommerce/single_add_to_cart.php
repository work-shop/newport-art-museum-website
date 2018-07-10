<?php
/**
 * This template renders a simple add-to-cart button for woocommerce,
 * including a quantity slider and and an "add-to-cart" button.
 */

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<div class="single-sidebar-add-to-cart">
    <form class="cart" action="<?php echo esc_url( get_permalink() ); ?>" method="post" enctype='multipart/form-data'>
        <?php
        do_action( 'woocommerce_before_add_to_cart_button' );
        do_action( 'woocommerce_before_add_to_cart_quantity' ); ?>
        <div class="single-sidebar-middle">
            <div class="row"> 
                <div class="col-6 add-to-cart-price">
                    <h5 class="label bold">
                        Price
                    </h5>
                    <h5 class="price">
                       <?php $current_price = $product->get_price(); ;?>
                       <?php if ( $current_price > 0 ): ?>
                           $<?php echo $current_price; ?> Per Person
                           <?php else: ?>
                            Free
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="col-6 add-to-cart-quantity">
                    <h5 class="label bold righted">
                        Quantity
                    </h5>
                    <?php 
                    woocommerce_quantity_input( array(
                        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity(),
                    ) );
                    ?>
                </div>
                <?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>
            </div>
        </div>
        <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button-full button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
        <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
    </form>
</div>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
