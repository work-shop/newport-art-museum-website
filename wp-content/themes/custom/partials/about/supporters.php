<?php if( have_rows('supporters','60') ): ?>
	<section class="block padded" id="about-supporters">
		<div class="container-fluid">
			<div class="row supporters mb5">
				<div class="col-12 mb3">
					<h2 class="serif centered">Supporters</h2>
				</div>
				<div class="col-12">
					<div class="slick slick-sponsors">
						<?php  while ( have_rows('supporters','60') ) : the_row(); ?>
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
		<?php endif; ?>
		<?php if( have_rows('affiliates','60') ): ?>
			<div class="row affiliations">
				<div class="col-12 mb3">
					<h2 class="serif centered">Affiliations</h2>
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