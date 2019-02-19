

<?php
$id = get_the_ID();
$product_id = get_field('managed_field_related_post', $id)[0];
$product = wc_get_product( $product_id );

// woocommerce values
// In the woocommerge product API, replacing get_{field_name} with set_{field_name} allows you
// to update the field in some way based on an action. In general, these won't need to be called directly.
/** The full product API is available [here](https://docs.woocommerce.com/wc-apidocs/class-WC_Product.html) */
$current_price = $product->get_price();
$is_in_stock = $product->is_in_stock();

$class_start_date = get_field('class_start_date');
$class_end_date = get_field('class_end_date');
$number_of_sessions = get_field('number_of_sessions');
$class_instructor_name = get_field('class_instructor_name');
$class_instructor_bio = get_field('class_instructor_bio');
$class_instructor_link_text = get_field('class_instructor_link_text');
$class_instructor_link_url = get_field('class_instructor_link_url');
$class_instructor_headshot = get_field('class_instructor_headshot');
$class_end_date = get_field('class_end_date');
$class_short_description = get_field('short_description');
?>

<section class="block single-body" id="class-single-body">
    <div class="container-fluid-single container-fluid">
        <div class="row">
            <div class="col-md-7 col-lg-7 single-body-left">
                <div class="single-body-left-link">
                    <a href="/education/classes">Back To Classes</a>
                </div>
                <div class="single-body-left-main">
                    <div class="event-single-introduction single-introduction">
                        <h1 class="serif class-single-title single-title">
                            <?php the_title(); ?>
                        </h1>
                        <h4 class="class-single-info mt2">
                            <?php if( get_field('class_start_date') ): ?>
                                <h4 class="bold">
                                    <?php the_field('class_start_date'); ?> - <?php the_field('class_end_date'); ?>
                                </h4>
                                <?php if( get_field('number_of_sessions') ): ?>
                                    <h4 class="bold">
                                        <span class="class-dates-sessions bold">
                                            <?php the_field('number_of_sessions'); ?>
                                        </span>
                                    </h4>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if( have_rows('class_days_and_times') ): ?>
                                <h4 class="class-days bold">
                                    <?php  while ( have_rows('class_days_and_times') ) : the_row(); ?>
                                        <?php the_sub_field('class_day'); ?> <?php the_sub_field('class_start_time'); ?> - <?php the_sub_field('class_end_time'); ?>
                                    <?php endwhile; ?>
                                </h4>
                            <?php endif; ?>
                            <?php if( get_field('class_instructor_name') ): ?>
                                <h4 class="class-instructor bold">
                                    Taught by
                                    <?php if( get_field('class_instructor_link_url') ): ?>
                                        <a href="<?php the_field('class_instructor_link_url'); ?>">
                                        <?php endif; ?>
                                        <?php the_field('class_instructor_name'); ?>
                                        <?php if( get_field('class_instructor_link_url') ): ?>
                                        </a>
                                    <?php endif; ?>
                                </h4>
                            <?php endif; ?>
                            <?php if( get_field('class_pricing_1') ): ?>
                                <!-- <div class="nam-dash"></div> -->
                                <h4 class="class-pricing-1 bold">
                                    <?php the_field('class_pricing_1'); ?>  
                                </h4>
                            <?php endif; ?>
                            <?php if( get_field('class_pricing_2') ): ?>
                                <h4 class="class-pricing-2 bold">
                                    <?php the_field('class_pricing_2'); ?>  
                                </h4>
                            <?php endif; ?>
                            <?php if( get_field('class_pricing_3') ): ?>
                                <h4 class="class-pricing-3 bold">
                                    <?php the_field('class_pricing_3'); ?>  
                                </h4>
                            <?php endif; ?>
                        </h4>
                        <?php if( $class_short_description ): ?>
                            <h3 class="class-single-short-description mt1">
                                <?php echo $class_short_description; ?>
                            </h3>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col">
                                <div class="nam-dash nam-dash-desktop"></div>
                                <div class="single-ecommerce-sidebar-sm">
                                    <?php // NAM_Helpers::print_notices(false); ?>
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
            <div class="col-md-5 col-lg-5 col-xl-4 offset-xl-1 single-body-right single-body-right-desktop">
                <div class="single-body-right-content">
                    <?php // NAM_Helpers::print_notices(true); ?>
                    <?php get_template_part('partials/single_ecommerce_sidebar'); ?>
                </div><!-- .single-body-right-content -->
            </div><!-- .single-body-right -->
        </div><!-- .row-->
    </div><!-- .container-fluid -->
</section>
