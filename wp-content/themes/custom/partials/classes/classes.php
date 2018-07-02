<?php $posts = NAM_Class::get_posts(); ?>

<section class="block pb6" id="classes">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<?php foreach ( $posts as $post ) : ?>
				<div class="col-md-4 col-xl-3 filter-target filter-class <?php NAM_Helpers::card_terms('classes-categories'); ?> <?php NAM_Helpers::card_terms('classes-days'); ?>" data-date="<?php $class_start_date = get_field('class_start_date'); $class_start_date = strtotime($class_start_date); $class_start_date = date('m/d/Y', $class_start_date); echo $class_start_date; ?>">
					<?php
					setup_postdata( $post );
					$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
					$card_size = 'medium'; //'wide', 'medium', 'small', 'menu'
					$card_type = 'class';  //'event', 'class', 'product', 'news' 
					NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null ); ?>
				</div>
			<?php endforeach; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>

