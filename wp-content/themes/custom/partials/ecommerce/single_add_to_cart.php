<?php

$class_in_cart = NAM_Classes::has_class_in_cart();
$is_classes = get_post_type( get_the_ID() ) == 'classes';

do_action( 'woocommerce_before_add_to_cart_form' );

?>

<?php if ( $class_in_cart && $is_classes ) : // we're on a class page and there's a class in the cart ?>

    <div class="sidebar-middle single-sidebar-middle">
        <div class="row add-to-cart-price">
            <div class="col-12">
                <p class="mb0">
                    <?php if ( $class_in_cart->get_id() === $product->get_id() ) : ?>
                    You already have this class in your <a href="/cart" class="underline">cart.</a>
                    <?php else: ?>
                        You already have <span class="bold"><?php echo $class_in_cart->get_name(); ?></span> in your <a href="/cart" class="underline">cart.</a>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="row add-to-cart-limit">
            <div class="col-12">
                <p class="small mb0">
                    <span class="icon icon-alert" data-icon="="></span>
                    <?php the_field('class_registration_limit_explanation','option'); ?>
                </p>
            </div>
        </div>
    </div>

<?php else: // we're on a non-class page or there's no class in the cart.  ?>

    <form class="cart" action="<?php echo esc_url( get_permalink() ); ?>" method="post" enctype='multipart/form-data'>
        <?php
        do_action( 'woocommerce_before_add_to_cart_button' );
        do_action( 'woocommerce_before_add_to_cart_quantity' ); 
        ?>
        <div class="sidebar-middle single-sidebar-middle">

            <?php for ($i=0; $i < 2; $i++) {  ?>

                <?php if( $is_classes && $i == 1 ): break; endif; ?>

                <div class="row add-to-cart-header">
                    <div class="col">
                        <h5 class="label bold">
                            <?php if( $is_classes ): ?>
                                Tuition
                                <?php else: ?>
                                    Tickets
                                <?php endif; ?>
                            </h5>
                        </div>
                        <div class="col">
                            <h5 class="label bold righted">
                                Quantity
                            </h5>
                        </div>
                    </div>
                    <div class="row add-to-cart-price">
                        <div class="col">
                            <div class="price">
                                <?php $current_price = $product->get_price(); ;?>
                                <?php $membership_discount = NAM_Membership::get_membership_discount( $product->id ); ?>
                                <?php $is_member_or_has_membership_in_cart = NAM_Membership::is_member() || NAM_Membership::has_membership_in_cart(); ?>
                                <?php //getting membership discount ?>

                                <?php if ( $is_member_or_has_membership_in_cart && $current_price > 0 ) : ?>

                                    <div class="row class-price-row mb1">
                                        <div class="class-price-col-first">
                                            <span class="icon" data-icon="%"></span>
                                        </div>
                                        <div class="class-price-col-second">
                                            <h5 class="bold mb0">
                                                Member price
                                            </h5> 
                                            <p class="members-price m0">
                                                <?php echo wc_price($current_price - $membership_discount); ?> Per person
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row class-price-row">
                                        <div class="class-price-col-first">
                                        </div>
                                        <div class="class-price-col-second">
                                            <h5 class="bold m0 faded">
                                                Non-member price
                                            </h5>
                                            <p class="non-members-price m0 faded">
                                                <?php echo wc_price( $current_price ); ?> Per person
                                            </p>
                                        </div>
                                    </div>

                                    <?php //not getting membership discount ?>
                                    <?php elseif ( $current_price > 0 ): ?>

                                        <div class="row class-price-row mb1">
                                            <div class="class-price-col-first">
                                            </div>
                                            <div class="class-price-col-second">
                                                <a class="h5 bold modal-toggle" href="#" id="member-price-info" data-modal-target="modal-member-price-info">
                                                    <span class="faded bold">Member price </span><span class="icon" data-icon="?"></span>
                                                </a> 
                                                <p class="members-price faded m0">
                                                    <?php echo wc_price($current_price - $membership_discount); ?> Per person
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row class-price-row">
                                            <div class="class-price-col-first">
                                                <span class="icon" data-icon="%"></span>
                                            </div>
                                            <div class="class-price-col-second">
                                                <h5 class="bold m0">
                                                    Non-member price
                                                </h5>
                                                <p class="non-members-price m0">
                                                    <?php echo wc_price( $current_price ); ?> Per person
                                                </p>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                            <p>
                                                Free
                                            </p>
                                        <?php endif; ?>
                                    </div><!-- .price-->
                                </div>

                                <?php //if ( !$is_classes ) : // if it's not classes, add the quantity button. ?>
                                <div class="col add-to-cart-quantity">
                                    <?php
                                    woocommerce_quantity_input( array(
                                        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
                                        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
                                        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity(),
                                    ) );
                                    ?>
                                </div>
                                <?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>
                                <?php // endif; ?>

                            </div><!-- .row.add-to-cart-price -->

                            <?php if( $i < 1 ) :?>
                                <?php if ( $product instanceof WC_Product_Bundle ) : // if it's a bundle with fees, render the fees in their own section. ?>
                                    <?php $bundled_items = $product->get_bundled_data_items(); ?>
                                    <?php if ( count( $bundled_items ) > 0 ) : ?>
                                        <div class="row add-to-cart-fees">
                                            <div class="col-12 add-to-cart-fees-broken">
                                                <h5 class="label bold">
                                                    Fees
                                                </h5>
                                                <?php foreach( $bundled_items as $fee ) : ?>
                                                    <?php $fee_product = wc_get_product( $fee->get_product_id() ); ?>
                                                    <p class="mb0">
                                                        <?php echo wc_price( $fee_product->get_price() ); ?> <?php echo $fee_product->name; ?> Per person
                                                    </p>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ( $is_classes ) : ?>
                                    <div class="row add-to-cart-limit">
                                        <div class="col-12 add-to-cart-limit-broken">
                                            <p class="small mb0">
                                                <span class="icon icon-alert" data-icon="="></span>
                                                <?php the_field('class_registration_limit_explanation','option'); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php } ?>

                    </div><!-- .single-sidebar-middle -->

                    <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button-full button alt">
                        <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                    </button>
                    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
                </form>

            <?php endif; ?>

            <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

