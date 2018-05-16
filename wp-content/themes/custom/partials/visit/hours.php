<?php if( have_rows('hours_content','23') ): ?>
	<?php while ( have_rows('hours_content','23') ) : the_row(); ?>
		<div class="row hours">
			<div class="col-md-5 col-xs-5">
				<h4 class="plan-your-visit-days key">
					<?php the_sub_field('days'); ?>
				</h4>
			</div>
			<div class="col-md-7 col-xs-7">
				<h4 class="plan-your-visit-hours value">
					<?php the_sub_field('hours'); ?>
				</h4>
			</div>
		</div> 
	<?php endwhile; ?>
<?php endif; ?>