<?php
/**
 * Development Helper Function Formatting
 */

if ( !function_exists('nam_debug_formatting') ) {
    function nam_debug_formatting( $tag, $meta_key, $meta_value, $classes = '' ) {
        echo '<' . $tag . ' class="'. $classes . '">' . $meta_key . ' <span class="ml2 bold">' . $meta_value . '</span></' . $tag . '>';
    }
}

?>

<?php

$id = get_the_ID();
$product = wc_get_product( $id );

// woocommerce values
// In the woocommerge product API, replacing get_{field_name} with set_{field_name} allows you
// to update the field in some way based on an action. In general, these won't need to be called directly.
/** The full product API is available [here](https://docs.woocommerce.com/wc-apidocs/class-WC_Product.html) */
$name = $product->get_name();
$current_price = $product->get_price();
$sale_price = $product->get_sale_price();
$regular_price = $product->get_regular_price();
$sale_start_date = $product->get_date_on_sale_from();
$sale_end_date = $product->get_date_on_sale_to();
$is_on_sale = $product->is_on_sale();

$is_in_stock = $product->is_in_stock();
$stock_quantity = $product->get_stock_quantity();

$event_date = get_field('event_date');
$event_start_time = get_field('event_start_time');
$event_end_time = get_field('event_end_time');
$event_location = get_field('event_location');
$event_short_description = get_field('short_description');

// woocommerce plumbing
$add_to_cart_url = $product->add_to_cart_url();
$add_to_cart_button_text = $product->add_to_cart_url();
$add_to_cart_url = $product->add_to_cart_url();

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
                                <div class="sidebar single-sidebar shadowed sidebar-responsive">
                                    <div class="single-sidebar-top">
                                        <h4 class="bold sidebar-heading">
                                            <?php if( get_field('registration_required') ): ?>
                                                <?php if ( $is_in_stock ) : ?>
                                                    Tickets
                                                    <?php if ( !$current_price > 0 ): ?>
                                                        <br>
                                                        <span class="free-registration-text h5 mt1">
                                                            This Event is Free, but Requires Registration. 
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
                                                </h4>
                                            </div>
                                            <?php if ( $is_in_stock ) : ?>
                                                <?php include( locate_template('partials/ecommerce/single_add_to_cart.php') ); ?>
                                            <?php endif; ?>
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
                            <div class="sidebar single-sidebar shadowed">
                                <div class="single-sidebar-top">
                                    <h4 class="bold sidebar-heading">
                                        <?php if( get_field('registration_required') ): ?>
                                            <?php if ( $is_in_stock ) : ?>
                                                Tickets
                                                <?php if ( !$current_price > 0 ): ?>
                                                    <br>
                                                    <span class="free-registration-text h5 mt1">
                                                        This Event is Free, but Requires Registration. 
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
                                            </h4>
                                        </div>
                                        <?php if ( $is_in_stock ) : ?>
                                            <?php include( locate_template('partials/ecommerce/single_add_to_cart.php') ); ?>
                                        <?php endif; ?>
                                    </div><!-- .sidebar -->
                                </div><!-- .single-body-right-content -->
                            </div><!-- .single-body-right -->
                        </div><!-- .container-fluid -->
                    </div><!-- .row-->
                </section>




