<section class="block vhm100" id="about-mission">
	<div class="row about-mission-row">
		<div class="col-md-6 col-lg-5 about-quote">
			<?php if( have_rows('about_page_quote') ): 
				while( have_rows('about_page_quote') ): the_row(); 
					$image = get_sub_field('quote_image');
					$text = get_sub_field('quote_text'); ?>
					<div class="block-background about-quote-image" style="background-image: url('<?php echo $image['sizes']['large']; ?>');">
					</div>
					<div class="about-quote-text">
						<h1 class="white serif m0">
							<?php echo $text; ?>
						</h1>
						<div class="nam-dash mt2 mb2"></div>
						<h4 class="white"><?php the_sub_field('quoted_person'); ?></h4>
						<h4 class="white"><?php the_sub_field('quoted_person_title'); ?></h4>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
		<div class="col-md-6 col-lg-7 bg-khaki">
			<div class="about-mission-vision">
				<div class="about-mission-content mb4">
					<h2 class="serif">
						Our Mission
					</h2>
					<h3>
						<?php the_field('mission'); ?>
					</h3>
				</div>
				<div class="about-vision-content">
					<h2 class="serif">
						Our Vision
					</h2>
					<h3>
						<?php the_field('vision'); ?>
					</h3>
				</div>
			</div>
		</div>
	</div>
</section>