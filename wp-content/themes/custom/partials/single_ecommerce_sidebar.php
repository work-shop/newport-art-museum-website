<?php

$id = get_the_ID();
$product_id = get_field('managed_field_related_post', $id);
$product = wc_get_product( $product_id );
$current_price = $product->get_price();
$is_in_stock = $product->is_in_stock();
if(is_singular('event')): $type = 'event'; else: $type = 'class'; endif; ?>

<div class="sidebar single-sidebar shadowed single-sidebar-<?php echo $type; ?>">
    <div class="single-sidebar-top">
        <h4 class="bold sidebar-heading">
            <?php if( $type === 'event'): ?>
                <?php if( get_field('registration_required') ): ?>
                    <?php if ( $is_in_stock ) : ?>
                        Tickets
                        <?php if ( !$current_price > 0 ): ?>
                            <br>
                            <span class="free-registration-text h5 mt1">
                                This event is free, but tickets are limited and highly recommended.
                            </span>
                        <?php endif; ?>
                        <?php else: ?>
                            <span class="error bold m1 display-block">This Event is Sold Out.</span>
                        <?php endif; ?>
                        <?php else: ?>
                            <span class="bold display-block m1">
                                This Event is Free and Open to the Public, No Registration or Tickets Required.
                            </span>
                        <?php endif; ?>
                        <?php //end type event ?>
                        <?php elseif( $type === 'class'): ?>
                            <?php if ( $is_in_stock ) : ?>
                                Registration
                                <?php else: ?>
                                    <span class="error bold m1 display-block">This Class is Full.</span>
                                <?php endif; ?>
                            <?php endif; //end type class ?>
                        </h4>
                    </div>
                    <?php if ( $is_in_stock ) : ?>
                        <?php include( locate_template('partials/ecommerce/single_add_to_cart.php') ); ?>
                    <?php endif; ?>
</div><!-- .sidebar -->
