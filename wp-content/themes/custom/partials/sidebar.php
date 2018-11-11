<div class="sidebar">
    <div class="sidebar-inner">
        <form class="cart" action="/support" method="post" enctype='multipart/form-data'>
            <div class="contact-sidebar-content sidebar-content sidebar-box">
                <h4 class="bold sidebar-heading">
                    <?php the_field('page_sidebar_heading'); ?>
                </h4>
                <?php if( is_page('66') ): ?>

                    <?php
                    $the_query = new WP_Query( array(
                        'post_type' => 'donation-tiers',
                        'posts_per_page' => '-1'
                    ) ); ?>

                    <?php if( $the_query->have_posts() ): ?>
                        <?php $countSet = false; ?>
                        <?php $count = 0; ?>
                        <?php $name_your_price_html = ''; ?>
                        <?php $donation_tiers_html = ''; ?>
                        <?php $name_your_price_id = ''; ?>

                        <?php while ( $the_query->have_posts() ) : ?>
                            <?php $the_query->the_post(); ?>
                            <?php
                            $id = $post->ID;
                            $product_id = get_field('managed_field_related_post', $id)[0];
                            $name_your_price = get_field('name_your_price_product', $id);
                            ?>
                            
                            <?php if ( $name_your_price) :

                                $name_your_price_id = $product_id->ID;
                                $min_price = get_field('minimum_price', $id);
                                $show_min_price = false;
                                $suggested_price = get_field('suggested_price', $id);
                                ob_start();
                                ?>

                                <?php // this is the NYP input for the Name Your Price product. All values are currently hardcoded. ?>
                                <div class="nyp hidden" id="nyp-fields" data-price="<?php echo $suggested_price; ?>" data-minimum-error="Please enter at least <?php echo $min_price; ?>." data-hide-minimum="1" data-hide-minimum-error="Please enter a higher amount." data-max-price="" data-maximum-error="Please enter less than or equal to %%MAXIMUM%%." data-min-price="<?php echo $min_price; ?>">
                                    <input id="nyp" name="nyp" type="text" value="<?php echo $suggested_price ?>" title="nyp" class="input-text amount nyp-input text" placeholder="Enter an amount to donate" />
                                    <?php if( $min_price && $show_min_price ): ?>
                                        <p class="small">The minimum donation is $<?php echo $min_price ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php $name_your_price_html = ob_get_clean(); ?>

                                <?php elseif ( $product_id) : ?>
                                    <?php 
                                    if( $countSet ===  false ): 
                                        $countSet = true;
                                        $count = 0;
                                    endif;
                                    $product = wc_get_product($product_id);
                                    $current_price = $product->get_price();
                                    $sale_price = $product->get_sale_price();
                                    $regular_price = $product->get_regular_price();
                                    $sale_start_date = $product->get_date_on_sale_from();
                                    $sale_end_date = $product->get_date_on_sale_to();
                                    $is_on_sale = $product->is_on_sale();
                                    $add_to_cart_url = $product->add_to_cart_url();
                                    $add_to_cart_button_text = $product->add_to_cart_url();
                                    ob_start();
                                    ?>
                                    <button class="button-donation-tier button-donation-select <?php if($count === 0): $currentDonationUrl = $add_to_cart_url; echo 'active'; endif; ?>" data-cart-url="<?php echo $add_to_cart_url; ?>">
                                        $<?php echo $current_price; ?>
                                    </button>
                                    <?php $donation_tiers_html .= ob_get_clean(); ?>

                                <?php endif; ?>
                                <?php $count++; ?>

                            <?php endwhile; //have posts ?>

                            <?php wp_reset_postdata(); ?>

                            <div class="support-sidebar-donation-tiers">
                                <?php echo $donation_tiers_html; ?>
                                <button class="button-donation-tier button-donation-toggle">Other Amount</button>
                            </div>
                        <?php endif; //have posts ?>
                        <div class="support-sidebar-write-in-donation">
                            <?php // this is the NYP input for the Name Your Price product. All values are currently hardcoded. ?>
                            <?php echo $name_your_price_html; ?>
                        </div>
                    <?php endif; ?>
                    <h4 class="sidebar-text">
                        <?php the_field('page_sidebar_text'); ?>
                    </h4>
                </div>
                <?php if( is_page('66') ): ?>

                    <?php // This is the submit button for the write-in donation field ?>
                    <button type="submit" name="add-to-cart" id="nyp-button" value="<?php echo $name_your_price_id; ?>" class="single_add_to_cart_button button-full button alt hidden">Donate</button>

                    <a class="button button-full" href="<?php echo $currentDonationUrl; ?>" id="sidebar-donate-button">Donate</a>

                    <?php else: ?>
                        <?php $link = get_field('page_sidebar_link');
                        if( $link ): ?>
                            <a class="button button-full" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </form>
            </div>
        </div>
