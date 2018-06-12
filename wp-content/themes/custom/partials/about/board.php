<section class="block padded" id="board">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<div class="col-md-8 col-lg-7 board-content">
				<div class="staff-group">
					<?php if( have_rows('board_1') ): ?>
						<h2 class="serif mb1">Board Officers</h2>
						<div class="staff-list">
							<?php  while ( have_rows('board_1') ) : the_row(); ?>
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
					<?php if( have_rows('board_2') ): ?>
						<h2 class="serif mb1">Board Members</h2>
						<div class="staff-list">
							<?php  while ( have_rows('board_2') ) : the_row(); ?>
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
				<?php get_template_part('partials/contact_sidebar' ); ?>
			</div>
		</div>
	</div>
</section>