<?php
$the_query = new WP_Query( array(
	'post_type' => 'exhibitions',
	'tax_query' => array(
		array (
			'taxonomy' => 'exhibitions-categories',
			'field' => 'slug',
			'terms' => 'past',
		)
	),
) ); ?>
<?php if( $the_query->have_posts() ): ?>
<section class="block pb7" id="past-exhibitions">
	<div class="container-fluid container-fluid-stretch">
		<div class="row now-on-view-title">
			<div class="col-sm-12">
				<h2 class="serif mb1">Past Exhibitions</h2>
			</div>
		</div>
	</div>
	<div class="exhibition-slider ">
		<div class="slick slick-exhibitions">

			<?php while ( $the_query->have_posts() ) : ?>
				<?php $the_query->the_post(); ?>
				<?php 
				$card_layout = 'text_right'; //'text_right', 'text_bottom', 'text_top'
				$card_size = 'wide'; //'wide', 'medium', 'small', 'menu'
				$card_type = 'exhibition';  //'event', 'class', 'product', 'news' 
				NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null );
				?>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>