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
    $show_individual_event_temporary_message = get_field('show_individual_event_temporary_message');
    $individual_event_temporary_message = get_field('individual_event_page_temporary_message','74');
else: 
    $type = 'class'; 
    $registration_required = true;
    $show_individual_class_temporary_message = get_field('show_individual_class_temporary_message','78');
endif; 

$sidebar_box_override_text = get_field('sidebar_box_override_text');
$sidebar_box_override_link = get_field('sidebar_box_override_link');

?>

<div class="sidebar single-sidebar single-sidebar-<?php echo $type; ?>">
    <div class="sidebar-inner">
        <div class="single-sidebar-top">
            <h4 class="bold sidebar-heading">

                <?php if( $sidebar_box_override_text ): ?>
                    <span class="sidebar-box-override-text m1 bold">
                        <?php echo $sidebar_box_override_text; ?>
                    </span>
                <?php else: //don't override sidebar text ?>

                    <?php if( $type === 'event'): ?>
                        <?php if( $show_individual_event_temporary_message ): ?>
                            <span class="error temporary-message bold">
                                <?php echo $individual_event_temporary_message; ?>
                            </span>
                        <?php else: //don't show individual event temporary message ?>
                            <?php if( $registration_required ): ?>
                                <?php if ( $is_in_stock ) : ?>
                                    <?php if ( !$current_price > 0 ): ?>
                                        This event is free, but tickets are limited and highly recommended.
                                        <?php else: ?>
                                            Event Registration
                                        <?php endif; //end if free ?>
                                    <?php else: //event is not in stock?>
                                        This Event is Sold Out.
                                    <?php endif; // is in stock ?>
                                <?php else: //no registration required ?>
                                    This Event is Free and Open to the Public, No Registration or Tickets Required.
                                <?php endif; //end registration required if ?>
                            <?php endif; //end temporary message if ?>

                            <?php elseif( $type === 'class'): ?>
                                <?php if( $show_individual_class_temporary_message ): ?>
                                    <span class="error temporary-message bold">
                                        <?php echo $individual_class_temporary_message; ?>
                                    </span>
                                <?php else: //dont show individual class temporary message ?>
                                    <?php if ( $is_in_stock ) : ?>
                                        Class Registration
                                        <?php else: ?>
                                            This Class is Full
                                        <?php endif; // is in stock ?>
                                    <?php endif; //end show individual class temporary message ?>
                                <?php endif; //end type class ?>

                            <?php endif; //end sidebar box override text if ?>

                        </h4><!-- .sidebar-heading -->
                    </div><!-- .single-sidebar-top -->

                    <?php if( $sidebar_box_override_link ): ?>
                        <a href="<?php echo $sidebar_box_override_link['url']; ?>" class="button button-full">
                            <?php echo $sidebar_box_override_link['title']; ?>
                        </a>
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

                            <?php endif; //end sidebar box override link ?>

                        </div><!-- .sidebar-inner -->
</div><!-- .sidebar -->