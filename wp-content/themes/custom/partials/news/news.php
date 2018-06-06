
<?php
$the_query = new WP_Query( array(
	'post_type' => 'news',
	'posts_per_page' => '10'
)
); ?>

<section class="block padded" id="news">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<div class="col-md-8 col-lg-7">
				<?php if( $the_query->have_posts() ): ?>
					<?php while ( $the_query->have_posts() ) : ?>
						<?php $the_query->the_post(); ?>
						<?php
						$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
						$card_size = 'wide'; //'wide', 'medium', 'small', 'menu'
						$card_type = 'news';  //'event', 'class', 'product', 'news' 
						$card_link = null;
						$card_image = null;
						$card_title = null;
						$card_info = null;
						NAM_Helpers::card( $card_layout, $card_size, $card_type, $card_link, $card_image, $card_title, $card_info ); 
						?>
					<?php endwhile; ?>
					<?php wp_reset_postdata(); ?>
				<?php endif; ?>
			</div>
			<div class="col-md-4 offset-lg-1">
				<?php get_template_part('partials/mailchimp_form'); ?>
			</div>
		</div>
	</div>
</section>