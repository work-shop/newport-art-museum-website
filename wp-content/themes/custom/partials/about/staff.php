<section class="block padded" id="staff">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<div class="col-md-8 col-lg-7 staff-content">
				<div class="staff-group">
					<?php if( have_rows('staff_1') ): ?>
						<h2 class="serif mb1">Museum Staff</h2>
						<div class="staff-list">
							<?php  while ( have_rows('staff_1') ) : the_row(); ?>
								<div class="staff-person">
									<h4 class="staff-name m0">
										<?php the_sub_field('name'); ?>
									</h4>
									<h5 class="staff-title medium m0">
										<?php the_sub_field('title'); ?>
									</h5>
								</div>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="staff-group">
					<?php if( have_rows('staff_2') ): ?>
						<h2 class="serif mb1">Museum Experience Staff</h2>
						<div class="staff-list">
							<?php  while ( have_rows('staff_2') ) : the_row(); ?>
								<div class="staff-person">
									<h4 class="staff-name m0">
										<?php the_sub_field('name'); ?>
									</h4>
									<h5 class="staff-title medium m0">
										<?php the_sub_field('title'); ?>
									</h5>
								</div>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="staff-group">
					<?php if( have_rows('staff_3') ): ?>
						<h2 class="serif mb1">Gallery Guides</h2>
						<div class="staff-list">
							<?php  while ( have_rows('staff_3') ) : the_row(); ?>
								<div class="staff-person">
									<h4 class="staff-name m0">
										<?php the_sub_field('name'); ?>
									</h4>
									<h5 class="staff-title medium m0">
										<?php the_sub_field('title'); ?>
									</h5>
								</div>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="staff-group">
					<?php if( have_rows('staff_4') ): ?>
						<h2 class="serif mb1">Faculty</h2>
						<div class="staff-list">
							<?php  while ( have_rows('staff_4') ) : the_row(); ?>
								<div class="staff-person">
									<h4 class="staff-name m0">
										<?php the_sub_field('name'); ?>
									</h4>
									<h5 class="staff-title medium m0">
										<?php the_sub_field('title'); ?>
									</h5>
								</div>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-md-4 offset-lg-1 staff-sidebar">
				<?php get_template_part('partials/sidebar' ); ?>
			</div>
		</div>
	</div>
</section>