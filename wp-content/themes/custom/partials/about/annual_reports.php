<?php  if ( have_rows('annual_reports','46') ) : ?>
	<section class="block padded bg-light" id="about-annual-reports">
		<div class="container-fluid container-fluid-stretch">
			<div class="row annual reports">
				<div class="col-6 col-md-4 mb3">
					<h2 class="serif">Annual Reports</h2>
				</div>
				<?php  while ( have_rows('annual_reports','46') ) : the_row(); ?>
					<div class="col-6 col-md-4 mb3">
						<div class="bg-brand ">
							<?php $file = get_sub_field('annual_report_file'); ?>
							<a href="<?php echo $file['url']; ?>" class="p2 d-flex align-items-center justify-content-center">
								<h4 class="annual-report-title white mb0">
									<?php the_sub_field('annual_report_title'); ?>
								</h4>
							</a>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>