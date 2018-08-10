<?php

$id = get_the_ID();
$product = wc_get_product( $id );

// woocommerce values
// In the woocommerge product API, replacing get_{field_name} with set_{field_name} allows you
// to update the field in some way based on an action. In general, these won't need to be called directly.
/** The full product API is available [here](https://docs.woocommerce.com/wc-apidocs/class-WC_Product.html) */
$current_price = $product->get_price();

$is_in_stock = $product->is_in_stock();

$event_date = get_field('event_date');
$event_start_time = get_field('event_start_time');
$event_end_time = get_field('event_end_time');
$event_location = get_field('event_location');
$event_short_description = get_field('short_description');

?>

<section class="block single-body" id="event-single-body">
    <div class="container-fluid-single container-fluid">
        <div class="row">
            <div class="col-md-8 single-body-left">
                <div class="single-body-left-link">
                    <a href="/events">Back To Events</a>
                </div>
                <div class="single-body-left-main">
                    <div class="event-single-introduction single-introduction">
                        <h1 class="serif event-single-title single-title">
                            <?php the_title(); ?>
                        </h1>
                        <h3 class="event-single-short-description mt1">
                            <?php echo $event_short_description; ?>
                        </h3>
                        <h4 class="event-single-info">
                            <span class="event-date mr2"><?php echo $event_date; ?></span>
                            <span class="event-time mr2"><?php echo $event_start_time; ?><?php if($event_end_time): echo ' - ' . $event_end_time; endif; ?></span>
                            <span class="event-location"><?php echo $event_location; ?></span>
                        </h4>
                        <div class="row">
                            <div class="col">
                                <div class="nam-dash nam-dash-desktop"></div>
                                <div class="single-ecommerce-sidebar-sm">
                                    <?php NAM_Helpers::print_notices(false); ?>
                                   <?php get_template_part('partials/single_ecommerce_sidebar'); ?>
                               </div>
                           </div>
                       </div>
                   </div>
                   <div class="single-body-left-content">
                    <?php get_template_part('partials/flexible_content/flexible_content'); ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 single-body-right single-body-right-desktop">
            <div class="single-body-right-content">
               <?php NAM_Helpers::print_notices(true); ?>
               <?php get_template_part('partials/single_ecommerce_sidebar'); ?>
           </div><!-- .single-body-right-content -->
       </div><!-- .single-body-right -->
   </div><!-- .row-->
</div><!-- .container-fluid -->
</section>




