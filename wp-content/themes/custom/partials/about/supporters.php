<?php if( have_rows('supporters','60') ): ?>
	<section class="block pt6 pb0 bg-light" id="about-supporters-foundation">
		<div class="container-fluid">
			<div class="row supporters">
				<div class="col-12 mb1">
					<h2 class="serif">Foundation Support</h2>
				</div>
				<div class="supporters-list col-12">
					<?php  while ( have_rows('supporters','60') ) : the_row(); ?>
						<div class="supporter mb2">
							<?php 
							$link = get_sub_field('sponsor_link'); 
							?>
							<?php if( $link ): ?>
								<a href="<?php echo $link; ?>">
								<?php endif; ?>
								<h3 class="supporter-title">
									<?php the_sub_field('sponsoring_organization'); ?>
								</h3>
								<?php if( $link ): ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>
<?php if( have_rows('supporters_2','60') ): ?>
	<section class="block pt2 pb4 bg-light" id="about-supporters-corporate">
		<div class="container-fluid">
			<div class="row supporters">
				<div class="col-12 mb1">
					<h2 class="serif">Corporate Support</h2>
				</div>
				<div class="supporters-list col-12">
					<?php  while ( have_rows('supporters_2','60') ) : the_row(); ?>
						<div class="supporter mb2">
							<?php 
							$link = get_sub_field('sponsor_link'); 
							?>
							<?php if( $link ): ?>
								<a href="<?php echo $link; ?>">
								<?php endif; ?>
								<h3 class="supporter-title">
									<?php the_sub_field('sponsoring_organization'); ?>
								</h3>
								<?php if( $link ): ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>
<?php if( have_rows('affiliates','60') ): ?>
	<section class="block padded" id="about-afilliations">
		<div class="container-fluid">
			<div class="row affiliations">
				<div class="col-12 mb3">
					<h2 class="serif">Affiliations</h2>
				</div>
				<div class="col-12">
					<div class="slick slick-sponsors">
						<?php  while ( have_rows('affiliates','60') ) : the_row(); ?>
							<div class="slick-sponsor-slide">
								<?php 
								$image = get_sub_field('sponsor_logo'); 
								$link = get_sub_field('sponsor_link'); 
								?>
								<?php if( $link ): ?>
									<a href="<?php echo $link; ?>">
									<?php endif; ?>
									<img src="<?php echo $image['sizes']['medium']; ?>">
									<?php if( $link ): ?>
									</a>
								<?php endif; ?>
							</div>
						<?php endwhile; ?>

					</div>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>