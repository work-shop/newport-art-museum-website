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

// custom fields
$show_hero = get_field('show_hero');
$hero_image = get_field('hero_image');
$hero_text = get_field('hero_text');

$event_date = get_field('event_date');
$event_time = get_field('event_time');
$event_location = get_field('event_location');
$event_short_description = get_field('short_description');

$flexible_content = get_field('flexible_content_sections');

// woocommerce plumbing
$add_to_cart_url = $product->add_to_cart_url();
$add_to_cart_button_text = $product->add_to_cart_url();
$add_to_cart_url = $product->add_to_cart_url();

?>

<section class="container-fluid">
    <div class="row mt2 mb2">
        <div class="col-md-6">
            <div class="mb2">
                <?php nam_debug_formatting('h3', 'Name', $name ); ?>
            </div>
            <div class="mb2">
                <?php nam_debug_formatting('h5', 'Current Price', $current_price ); ?>
                <?php nam_debug_formatting('h5', 'Sale Price', $sale_price ); ?>
                <?php nam_debug_formatting('h5', 'Regular Price', $regular_price ); ?>
                <?php nam_debug_formatting('h5', 'Sale Start Date', $sale_start_date ); ?>
                <?php nam_debug_formatting('h5', 'Sale End Date', $sale_end_date ); ?>
                <?php nam_debug_formatting('h5', 'On Sale?', $is_on_sale ); ?>
            </div>
            <div class="mb2">
                <?php nam_debug_formatting('h5', 'In Stock?', $is_in_stock ); ?>
                <?php nam_debug_formatting('h5', 'Stock Quantity', $stock_quantity ); ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="mb2">
                <p class="mb1">Page Hero Metadata</p>
                <?php nam_debug_formatting( 'h6', 'Show Hero?', $show_hero); ?>
                <?php nam_debug_formatting( 'h6', 'Hero Image', $hero_image['url'] ); ?>
                <?php nam_debug_formatting( 'h6', 'Show Text', $hero_text ); ?>
            </div>

            <div class="mb2">
                <p class="mb1">Event Details</p>
                <?php nam_debug_formatting( 'h6', 'Event Date', $event_date ); ?>
                <?php nam_debug_formatting( 'h6', 'Event Time',$event_time ); ?>
                <?php nam_debug_formatting( 'h6', 'Event Location', $event_location['address'] ); ?>
                <?php nam_debug_formatting( 'h6', 'Event Short Description', $event_short_description ); ?>
            </div>
        </div>
    </div>


    <?php if ( $is_in_stock ) : ?>

    <div class="row">
        <div class="col-md-6">

            <p class="mb1">Add to Cart Form</p>

            <?php include( locate_template('partials/ecommerce/add_to_cart.php') ); ?>

        </div>
    </div>

    <?php endif; ?>

</section>
