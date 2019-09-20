<?php $posts = NAM_Event::get_posts(); ?>

<section class="block pb6" id="events">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<?php foreach ( $posts as $post ) : ?>
				<?php setup_postdata( $post ); ?>
				<?php if( has_term( 'private', 'events-categories' ) === false ) : ?>
					<div class="col-md-4 col-xl-3 filter-target filter-event <?php NAM_Helpers::card_terms('events-categories'); ?>" data-date="<?php $event_date = get_field('event_date'); $event_date = strtotime($event_date); $event_date = date('m/d/Y', $event_date); echo $event_date; ?>">
						<?php
					$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
					$card_size = 'medium'; //'wide', 'medium', 'small', 'menu'
					$card_type = 'event';  //'event', 'class', 'product', 'news' 
					NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null ); ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</div>
</section>

