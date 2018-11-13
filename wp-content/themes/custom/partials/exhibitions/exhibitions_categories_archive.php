
<?php if( have_posts() ): ?>
	<section class="block pt4 pb7" id="exhibitions-categories-archive">
		<div class="container-fluid container-fluid-stretch">
			<div class="row now-on-view-title">
				<div class="col-sm-12">
					<h2 class="serif mb1"><?php single_term_title(); ?></h2>
				</div>
			</div>
		</div>
		<div class="exhibitions">
			<div class="container-fluid container-fluid-stretch">
				<div class="row">
					<?php while ( have_posts() ) : ?>
						<?php the_post(); ?>
						<div class="col-md-4 col-sm-6 col-9">
							<?php 
							$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
							$card_size = 'medium'; //'wide', 'medium', 'small', 'menu'
							$card_type = 'exhibition';  //'event', 'class', 'product', 'news' 
							NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null ); ?>
						</div>
					<?php endwhile; ?>
				</div>
				<div class="row">
					<div class="col-12">
						<?php the_posts_pagination( array(
							'mid_size'  => 2,
							'prev_text' => __( '‘', 'textdomain' ),
							'next_text' => __( '—', 'textdomain' ),
						) ); ?>
					</div>
				</div>
			</div>
		</section>
<?php endif; ?>