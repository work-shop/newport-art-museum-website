<?php
/**
 * This template renders a simple add-to-cart button for woocommerce,
 * including a quantity slider and and an "add-to-cart" button.
 */

$class_in_cart = NAM_Classes::has_class_in_cart();
$is_classes = get_post_type( get_the_ID() ) == 'classes';

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<div class="single-sidebar-add-to-cart">

    <?php if ( $class_in_cart && $is_classes ) : // we're on a class page and there's a class in the cart. ?>

        <div class="single-sidebar-middle">
            <div class="row">

                <div class="col-12 add-to-cart-price">

                    <?php if ( $class_in_cart->get_id() === $product->get_id() ) : ?>

                    You already have this class in your cart!

                    <?php else: ?>

                        You already have <span class="bold"><?php echo $class_in_cart->get_name(); ?></span> in your cart.

                    <?php endif; ?>

                </div>

            </div>
        </div>

    <?php else: // we're on a non-class page or there's no class in the cart.  ?>

        <form class="cart" action="<?php echo esc_url( get_permalink() ); ?>" method="post" enctype='multipart/form-data'>
            <?php
            do_action( 'woocommerce_before_add_to_cart_button' );
            do_action( 'woocommerce_before_add_to_cart_quantity' ); ?>
            <div class="single-sidebar-middle">
                <div class="row">
                    <div class="col-<?php if ( $is_classes ) : ?>12<?php else: ?>6<?php endif; ?> add-to-cart-price">
                        <h5 class="label bold">
                            Price
                        </h5>
                        <h5 class="price">
                            <?php $current_price = $product->get_price(); ;?>
                            <?php $membership_discount = NAM_Membership::get_membership_discount( $product->id ); ?>
                            <?php $is_member_or_has_membership_in_cart = NAM_Membership::is_member() || NAM_Membership::has_membership_in_cart(); ?>

                            <?php if ( $is_member_or_has_membership_in_cart && $current_price > 0 ) : ?>

                             <p class="members-price"><?php echo wc_price($current_price - $membership_discount); ?> Per person
                              <br>
                              <span class="price-label small">Member price <span class="icon" data-icon="%"></span></span>
                          </p>
                          <p class="non-members-price hidden">
                             <?php echo wc_price( $current_price ); ?> Per person
                             <br>
                             <span class="price-label small">Non-member price</span>
                         </p>

                         <?php elseif ( $current_price > 0 ): ?>

                             <p class="non-members-price">
                                 <?php echo wc_price( $current_price ); ?> Per person
                                 <br>
                                 <span class="price-label small">Non-member price</span>
                             </p>
                             <p class="members-price"><?php echo wc_price($current_price - $membership_discount); ?> Per person
                                 <br>
                                 <a class="price-label small modal-toggle" href="#" id="member-price-info" data-modal-target="modal-member-price-info">
                                    Member price <span class="icon" data-icon="?"></span>
                                </a> 
                            </p>

                            <?php else: ?>

                                Free

                            <?php endif; ?>
                        </h5>
                    </div>

                    <?php if ( !$is_classes ) : // if it's not classes, add the quantity button. ?>
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
                    <?php endif; ?>

                    <?php if ( $product instanceof WC_Product_Bundle ) : // if it's a bundle with fees, render the fees in their own section. ?>
                        <?php $bundled_items = $product->get_bundled_data_items(); ?>
                        <?php if ( count( $bundled_items ) > 0 ) : ?>

                            <div class="col-12 add-to-cart-fees">
                                <h5 class="label bold">
                                    Fees
                                </h5>
                                <?php foreach( $bundled_items as $fee ) : ?>

                                    <?php $fee_product = wc_get_product( $fee->get_product_id() ); ?>

                                    <p class="small"><?php echo wc_price( $fee_product->get_price() ); ?> <?php echo $fee_product->name; ?> Per person</p>

                                <?php endforeach; ?>
                            </div>

                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </div>
            <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button-full button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
            <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
        </form>

    <?php endif; ?>
</div>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

