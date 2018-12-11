
<section class="block join-membership-levels padded" id="join-membership-levels">
	<div class="container-fluid container-fluid-stretch">
		<?php wc_print_notices(); ?>
		<div class="row">
			<div class="col-md-8 col-lg-7 join-content">
				<?php
				$the_query = new WP_Query( array(
					'post_type' => 'membership-tier',
					'posts_per_page' => '-1'
				) ); ?>
				<?php if( $the_query->have_posts() ): ?>

					<h2 class="serif mb1">Membership Levels</h2>
					<div data-accordion-group>
						<?php while ( $the_query->have_posts() ) : ?>
							<?php $the_query->the_post(); ?>
							<?php
							$id = $post->ID;
							$product_id = get_field('managed_field_related_post', $id)[0];
							$product = wc_get_product( $product_id );
							$current_price = $product->get_price();
							$sale_price = $product->get_sale_price();
							$regular_price = $product->get_regular_price();
							$sale_start_date = $product->get_date_on_sale_from();
							$sale_end_date = $product->get_date_on_sale_to();
							$is_on_sale = $product->is_on_sale();
							// woocommerce plumbing
							$add_to_cart_url = $product->add_to_cart_url();
							$add_to_cart_button_text = $product->add_to_cart_url();
							?>
							<div class="accordion multi-collapse" data-accordion>
								<div class="accordion-label" data-control>
									<h4 class="accordion-title">
										<span class="bold mr2"><?php the_title(); ?></span> <span class="membership-level-price">$<?php echo $current_price; ?></span>
									</h4>
									<span class="icon" data-icon="”"></span>
								</div>
								<div class="accordion-body" data-content>
									<div class="accordion-content-inner">
										<div class="wysiwyg">
											<?php the_field('membership_level_description'); ?>
										</div>
										<?php if( !is_user_logged_in() && NAM_Membership::has_membership_in_cart() ): ?>
										<div class="bg-error p1 mt2">
											<h5 class="bold m0">
												You already have a membership in your <a href="/cart" class="underline">cart.</a> Memberships are limited to one per customer.
											</h5>
										</div>
										<?php else: ?>
											<div class="accordion-link membership-link-button">
												<a href="<?php echo $add_to_cart_url; ?>" class="">
													Purchase New Membership
												</a>
												<a href="/renew-your-membership" class="ml1">
													Renew Your Membership
												</a>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="col-md-4 offset-lg-1 join-sidebar">
				<?php get_template_part('partials/sidebar' ); ?>
			</div>
		</div>
	</div>
</section>
