<?php

$class_in_cart = NAM_Classes::has_class_in_cart();
$is_member_or_has_membership_in_cart = NAM_Membership::is_member() || NAM_Membership::has_membership_in_cart();
$is_classes = get_post_type( get_the_ID() ) == 'classes';
$is_events = get_post_type( get_the_ID() ) == 'events';

do_action( 'woocommerce_before_add_to_cart_form' );

?>

    <?php if ( $class_in_cart && $is_classes ) : // we're on a class page and there's a class in the cart ?>

        <div class="sidebar-middle single-sidebar-middle">

            <div class="row add-to-cart-message">
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
                    <h5 class="add-to-cart-label">
                        Registration Limit
                    </h5>
                    <p class="small mb0">
                        <span class="icon icon-alert" data-icon="="></span>
                        <?php the_field('class_registration_limit_explanation','option'); ?>
                    </p>
                </div>
            </div>
        </div>

    <?php else: // we're on a non-class page or there's no class in the cart.  ?>

    <?php

    // NOTE: Consider adding this.
    // NOTE: https://diviengine.com/woocommerce-add-cart-ajax-single-variable-products-improve-ux/

    if ( $is_events ) {

        $purchase_options = NAM_Events::get_ticket_levels( $product );

    } else {

        $purchase_options = array( $product );

    }

    ?>

    <?php foreach ( $purchase_options as $i => $purchase_option ) {  ?>

    <form id='form-<?php echo $i; ?>' class="cart" action="<?php echo esc_url( get_permalink() ); ?>" method="post" enctype='multipart/form-data'>
        <?php

            do_action( 'woocommerce_before_add_to_cart_button' );
            do_action( 'woocommerce_before_add_to_cart_quantity' );

            ?>
            <div class="sidebar-middle single-sidebar-middle">

                <div class="row add-to-cart-header">
                    <div class="col">
                        <h5 class="add-to-cart-label">
                            <?php if( $is_classes ): ?>
                                Tuition
                            <?php elseif ( $is_events ): ?>
                              <?php echo $purchase_option['term']->name; ?> Tickets
                            <?php else: ?>
                                
                            <?php endif; ?>
                        </h5>
                    </div>
                    <div class="col hidden">
                        <h5 class="add-to-cart-label righted">
                            Quantity
                        </h5>
                    </div>
                </div>
                <div class="row add-to-cart-price">
                    <div class="col">
                        <div class="price">

                            <?php
                            if ( $is_events ) {

                                $current_price = $purchase_option['price'];
                                $membership_discount = $purchase_option['membership_discount'];

                            } else {

                                $current_price = $purchase_option->get_price();
                                $membership_discount = NAM_Membership::get_membership_discount( $purchase_option->id );

                            }


                            ?>

                            <?php //MEMBER PRICE ?>
                            <?php if ( $is_member_or_has_membership_in_cart && $current_price > 0 ) : ?>

                                <div class="row class-price-row">
                                    <div class="class-price-col-first">
                                        <span class="icon" data-icon="%"></span>
                                    </div>
                                    <div class="class-price-col-second">
                                        <h5 class="bold mb0 price-discount-label">
                                            <span class="icon member-check" data-icon="%"></span>Member price
                                        </h5>
                                        <p class="members-price m0">
                                            <?php echo wc_price($current_price - $membership_discount); ?> Per person
                                        </p>
                                    </div>
                                </div>

                                <?php //NON MEMBER PRICE ?>
                            <?php elseif ( $current_price > 0 ): ?>

                                <div class="row class-price-row mb1">
                                    <div class="class-price-col-first">
                                        <span class="icon" data-icon="%"></span>
                                    </div>
                                    <div class="class-price-col-second">
                                        <h5 class="bold mb0 price-discount-label">
                                            <span class="icon member-check" data-icon="%"></span>Member price
                                        </h5>
                                        <p class="members-price m0">
                                            <?php echo wc_price($current_price - $membership_discount); ?> Per person
                                        </p>
                                    </div>
                                </div>

                                <?php //NON MEMBER PRICE ?>
                            <?php elseif ( $current_price > 0 ): ?>

                                <div class="row class-price-row mb1">
                                    <div class="class-price-col-first">
                                        <span class="icon" data-icon="%"></span>
                                    </div>
                                    <div class="class-price-col-second">
                                        <h5 class="bold m0 price-discount-label">
                                            Non-member price
                                        </h5>
                                        <p class="non-members-price m0">
                                            <?php echo wc_price( $current_price ); ?> Per person
                                        </p>
                                    </div>
                                </div>
                                <div class="row class-price-row">
                                    <div class="col">
                                        <a class="h5 bold price-discount-label modal-toggle" href="#" id="member-price-info" data-modal-target="modal-member-price-info">
                                            <span class="faded bold">Member price <?php echo wc_price($current_price - $membership_discount); ?> Per person</span><span class="icon" data-icon="?"></span>
                                        </a>
                                    </div>
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


                    <?php // endif; ?>
                    <?php if ( $is_events ) : ?>

                        <div class="col-12 add-to-cart-quantity">
                            <?php
                            woocommerce_quantity_input( array(
                                'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $purchase_option['min_qty'], $product ),
                                'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $purchase_option['max_qty'], $product ),
                                'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity(),
                            ) );
                            ?>
                        </div>
                        <input type="hidden" name="variation_id" value="<?php echo $purchase_option['id'] ?>" />
                        <input type="hidden" name="attribute_ticket_levels" value="<?php echo $purchase_option['term']->slug; ?>">

                    <?php else: ?>

                        <div class="col-12 add-to-cart-quantity">
                            <?php
                            woocommerce_quantity_input( array(
                                'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $purchase_option->get_min_purchase_quantity(), $purchase_option ),
                                'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $purchase_option->get_max_purchase_quantity(), $purchase_option ),
                                'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $purchase_option->get_min_purchase_quantity(),
                            ) );
                            ?>
                        </div>

                    <?php endif; ?>
                    <?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>



                </div><!-- .row.add-to-cart-price -->


                <?php if ( $product instanceof WC_Product_Bundle ) : // if it's a bundle with fees, render the fees in their own section. ?>
                    <?php $bundled_items = $product->get_bundled_data_items(); ?>
                        <?php if ( count( $bundled_items ) > 0 ) : ?>
                            <div class="row add-to-cart-fees">
                                <div class="col-12">
                                    <h5 class="add-to-cart-label bold uppercase tracked-less">
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
                            <div class="col-12">
                                <h5 class="add-to-cart-label">
                                    Registration Limit
                                </h5>
                                <p class="small mb0">
                                    <span class="icon icon-alert" data-icon="="></span>
                                    <?php the_field('class_registration_limit_explanation','option'); ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>



                </div><!-- .single-sidebar-middle -->

                    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />
                    <?php if ( !$is_events ) : ?>
                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button-full button alt">
                            <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                        </button>
                    <?php endif; ?>
                    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
                </form>
            <?php } ?>

            <?php if ( $is_events ) : ?>
                <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="events_add_to_cart_button single_add_to_cart_button button-full button alt">
                    <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                </button>
            <?php endif; ?>

            <?php endif; ?>
            <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
