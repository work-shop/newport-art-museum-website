<div class="sidebar shadowed">
	<div class="contact-sidebar-content sidebar-box">
		<h4 class="bold sidebar-heading">
			<?php the_field('page_sidebar_heading'); ?>
		</h4>
		<?php if( is_page('66') ): ?>
			<div class="support-sidebar-donation-tiers">
				<?php
				$the_query = new WP_Query( array(
					'post_type' => 'donation-tiers',
					'posts_per_page' => '-1'
				) ); ?>
				<?php if( $the_query->have_posts() ): ?>
					<?php $count = 0; ?>
					<?php while ( $the_query->have_posts() ) : ?>
						<?php $the_query->the_post(); ?>
						<?php
						$id = $post->ID;
                        $product_id = get_field('managed_field_related_post', $id)[0];
                        if ( $product_id ) :
    						$product = wc_get_product($product_id);
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
    						<button class="button-donation-tier <?php if($count == 0): $currentDonationUrl = $add_to_cart_url; echo 'active'; endif; ?>" data-cart-url="<?php echo $add_to_cart_url; ?>">
    							$<?php echo $current_price; ?>
    						</button>
						<?php
                        endif;
                        $count++;
                        ?>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<h4 class="sidebar-text">
			<?php the_field('page_sidebar_text'); ?>
		</h4>
	</div>
	<?php if( is_page('66') ): ?>
		<a class="button button-full" href="<?php echo $currentDonationUrl; ?>" id="sidebar-donate-button">Donate</a>
		<?php else: ?>
			<?php $link = get_field('page_sidebar_link');
			if( $link ): ?>
				<a class="button button-full" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
