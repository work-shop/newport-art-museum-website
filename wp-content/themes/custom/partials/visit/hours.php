<?php if( have_rows('hours_content','23') ): ?>
	<?php while ( have_rows('hours_content','23') ) : the_row(); ?>
		<div class="row">
			<div class="col-md-7">
				<h4 class="plan-your-visit-days">
					<?php the_sub_field('days'); ?>
				</h4>
			</div>
			<div class="col-md-5">
				<h4 class="plan-your-visit-hours">
					<?php the_sub_field('hours'); ?>
				</h4>
			</div>
		</div> 
	<?php endwhile; ?>
<?php endif; ?>