<div class="support-sidebar sidebar shadowed">
	<div class="contact-sidebar-content sidebar-box">
		<h4 class="bold sidebar-heading">
			<?php the_field('page_sidebar_heading'); ?>
		</h4>
		<h4 class="sidebar-text">
			<?php the_field('page_sidebar_text'); ?>
		</h4>
		<div class="support-sidebar-donation-tiers">
			<?php
			$the_query = new WP_Query( array(
				'post_type' => 'donation-tiers',
				'posts_per_page' => '-1'
			) ); ?>
			<?php if( $the_query->have_posts() ): ?>
				<?php while ( $the_query->have_posts() ) : ?>
					<?php $the_query->the_post(); ?>
					<?php 
					$id = $post->ID;
					$product = wc_get_product($id);
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
					<div class="button-donation-tier">
						<a href="<?php echo $add_to_cart_url; ?>" class="">
							$<?php echo $current_price; ?>
						</a>
					</div>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</div>
	<?php $link = get_field('page_sidebar_link');
	if( $link ): ?>
		<a class="button button-full" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a>
	<?php endif; ?>
</div>