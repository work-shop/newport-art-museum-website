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
							<div class="col-md-6">
								<h4 class="bold">
									<?php the_field('exhibition_start_date'); ?> - <?php the_field('exhibition_end_date'); ?>
								</h4>
							</div>
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
				
				</div>
			</div>
		</div>
	</div>
</section>