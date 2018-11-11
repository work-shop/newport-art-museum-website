<?php

$id = get_the_ID();
$product_id = get_field('managed_field_related_post', $id)[0];
if ( $product_id )  {
    $product = wc_get_product( $product_id );
    $current_price = $product->get_price();
    $is_in_stock = $product->is_in_stock();
    $membership_discount = NAM_Membership::get_membership_discount( $product_id );
}

if( get_post_type() == 'events' ): 
    $type = 'event'; 
    $registration_required = get_field('registration_required');
else: 
    $type = 'class'; 
    $registration_required = true;
endif; ?>

<div class="sidebar single-sidebar single-sidebar-<?php echo $type; ?>">
    <div class="sidebar-inner">
        <div class="single-sidebar-top">
            <h4 class="bold sidebar-heading">
                <?php if( $type === 'event'): ?>
                    <?php if( get_field('sidebar_box_override_text') ): ?>
                        <span class="sidebar-box-override-text m1 bold">
                            <?php the_field('sidebar_box_override_text'); ?>
                        </span>
                    <?php else: //don't override sidebar text?>
                        <?php $show_individual_event_temporary_message = get_field('show_individual_event_temporary_message'); ?>
                        <?php if( $show_individual_event_temporary_message ): ?>
                            <?php $individual_event_temporary_message = get_field('individual_event_page_temporary_message','74'); ?>
                            <div class="message error temporary-message">
                                <span class="error bold">
                                    <?php echo $individual_event_temporary_message; ?>
                                </span>
                            </div>
                            <?php else: ?>
                                <?php if( $registration_required  ): ?>
                                    <?php if(false): //temporarily don't display registration details ?>
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
                                        <?php endif; ?>

                                        <?php else: ?>
                                            <span class="bold display-block m1">
                                                This Event is Free and Open to the Public, No Registration or Tickets Required.
                                            </span>
                                        <?php endif; //end temporary message if ?>
                                    <?php endif; //end sidebar box override text if ?>
                                <?php endif; //end type event ?>

                                <?php elseif( $type === 'class'): ?>
                                    <?php $show_individual_class_temporary_message = get_field('show_individual_class_temporary_message','78'); ?>
                                    <?php if( $show_individual_class_temporary_message ): ?>
                                        <?php $individual_class_temporary_message = get_field('individual_class_temporary_message','78'); ?>
                                        <div class="message error">
                                            <span class="error bold">
                                                <?php echo $individual_class_temporary_message; ?>
                                            </span>
                                        </div>
                                        <?php else: ?>
                                            <?php if ( $is_in_stock ) : ?>
                                                Class Registration
                                                <?php else: ?>
                                                    <span class="error bold m1 display-block">This Class is Full.</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        <?php endif; //end type class ?>
                                    </h4>
                                </div>

                                <?php $sidebar_box_override_link = get_field('sidebar_box_override_link'); ?>
                                <?php if( $sidebar_box_override_link ): ?>
                                    <a href="<?php echo $sidebar_box_override_link['url']; ?>" class="button button-full"><?php echo $sidebar_box_override_link['title']; ?></a>
                                <?php else: //end sidebar box override link ?>
                                    <?php if( $show_individual_class_temporary_message ): ?>
                                        <?php if ( get_field('temporary_registration_link') ) : ?>
                                         <a href="<?php the_field('temporary_registration_link'); ?>" class="button button-full">Register on ASAP Connected</a>
                                     <?php endif; ?>
                                     <?php elseif( $show_individual_event_temporary_message ): ?>
                                        <?php if ( get_field('temporary_registration_link') ) : ?>
                                         <a href="<?php the_field('temporary_registration_link'); ?>" class="button button-full">Register</a>
                                     <?php endif; ?>
                                     <?php else: ?>
                                        <?php if ( $is_in_stock && $registration_required ) : ?>
                                            <?php include( locate_template('partials/ecommerce/single_add_to_cart.php') ); ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; //end sidebar box override link else ?>

                            </div><!-- .sidebar-inner -->
                        </div><!-- .sidebar -->
