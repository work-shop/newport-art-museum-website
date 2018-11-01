<?php 
$show_classes_page_temporary_message = get_field('show_classes_page_temporary_message','78');
?>
<?php if( $show_classes_page_temporary_message ): ?>
	<?php $classes_page_temporary_message = get_field('classes_page_temporary_message','78'); ?>
	<section class="block padded" id="classes-page-temporary message">
		<div class="container-fluid container-fluid-stretch">
			<div class="row">
				<div class="col-lg-8">
					<div class="bg-error p2">
						<h4 class="error mb0 bold">
							<?php echo $classes_page_temporary_message; ?>
						</h4>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php else: ?>
		<?php
		$the_query = new WP_Query( array(
			'post_type' => 'classes',
			'tax_query' => array(
				array (
					'taxonomy' => 'classes-categories',
					'field' => 'slug',
					'terms' => 'upcoming-classes',
				)
			),
		) ); ?>
		<?php if( $the_query->have_posts() ): ?>
			<section class="block pt4 pb7" id="upcoming-classes">
				<div class="container-fluid container-fluid-stretch">
					<div class="row now-on-view-title">
						<div class="col-sm-12">
							<h2 class="serif mb1">Upcoming Classes</h2>
						</div>
					</div>
				</div>
				<div class="classes-slider">
					<div class="slick slick-classes">
						<?php while ( $the_query->have_posts() ) : ?>
							<?php $the_query->the_post(); ?>
							<div class="slick-classes-slide">
								<?php 
						$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
						$card_size = 'medium'; //'wide', 'medium', 'small', 'menu'
						$card_type = 'class';  //'event', 'class', 'product', 'news' 
						NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null );
						?>
					</div>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	</section>
<?php endif; ?>


<?php endif; ?>