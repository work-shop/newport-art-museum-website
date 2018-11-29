<section class="block single-body" id="exhibition-single-body">
	<div class="container-fluid-single container-fluid">
		<div class="row">
			<div class="col-md-8 single-body-left">
				<div class="single-body-left-link">
					<a href="/exhibitions">Back To Exhibitions</a>
				</div>
				<div class="single-body-left-main">
					<div class="exhibition-single-introduction single-introduction">
						<h1 class="serif exhibition-single-title single-title">
							<?php the_title(); ?>
						</h1>
						<h3 class="exhibition-single-short-description">
							<?php the_field('short_description'); ?>
						</h3>
						<div class="row">
							<?php if( get_field('exhibition_start_date') || get_field('exhibition_end_date') ): ?>
							<div class="col-md-6">
								<h4 class="bold">
									<?php the_field('exhibition_start_date'); ?> <?php if( get_field('exhibition_start_date') && get_field('exhibition_end_date') ): echo ' - '; endif; ?> <?php the_field('exhibition_end_date'); ?> 
								</h4>
							</div>
						<?php endif; ?>
						<div class="col-md-6">
							<h4>
								<?php the_field('exhibition_location'); ?>
							</h4>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<div class="nam-dash">
							</div>
						</div>
					</div>
				</div>
				<div class="single-body-left-content">
					<?php get_template_part('partials/flexible_content/flexible_content'); ?>
				</div>
			</div>
		</div>
		<div class="col-md-4 single-body-right">
			<div class="single-body-right-content">
				<?php if( get_field('page_sidebar_heading') || get_field('page_sidebar_text') ): ?>
				<div class="sidebar">
					<div class="sidebar-inner">
						<div class="exhibition-sidebar-content sidebar-content sidebar-box">
							<?php if( get_field('page_sidebar_heading') ): ?>
								<h4 class="bold sidebar-heading">
									<?php the_field('page_sidebar_heading'); ?>
								</h4>
							<?php endif; ?>
							<?php if( get_field('page_sidebar_text') ): ?>
								<h4 class="sidebar-text">
									<?php the_field('page_sidebar_text'); ?>
								</h4>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php $link = get_field('page_sidebar_link'); ?>
			<?php if( $link ): ?>
				<a class="button button-full" href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
					<?php echo $link['title']; ?>
				</a>
			<?php endif; ?>
		</div>
	</section>