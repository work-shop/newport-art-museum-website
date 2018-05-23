<?php $posts = NAM_Event::get_posts(); ?>

<section class="block padded" id="events">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<?php foreach ( $posts as $post ) : ?>
				<div class="col-md-4">
					<?php
					setup_postdata( $post );
					$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
					$card_size = 'medium'; //'wide', 'medium', 'small', 'menu'
					$card_type = 'event';  //'event', 'class', 'product', 'news' 
					NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null ); ?>
				</div>
			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>

