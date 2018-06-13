<section class="block collection-gallery">
	<?php if( have_rows('collection_highlights_gallery') ): ?>
		<div class="slick slick-collection">
			<?php  while ( have_rows('collection_highlights_gallery') ) : the_row(); ?>
				<div class="slick-collection-slide">
					<?php 
					$image = get_sub_field('artwork_image'); 
					$artwork_title = get_sub_field('artwork_title'); 
					$artwork_artist = get_sub_field('artwork_artist'); 
					$artwork_medium = get_sub_field('artwork_medium'); 
					$artwork_description = get_sub_field('artwork_description'); 
					?>
					<div class="artwork-image">
						<img src="<?php echo $image['sizes']['page_hero']; ?>">
					</div>
					<div class="artwork-content">
						<div class="artwork-content-upper">
							<div class="row row-full">
								<div class="col-9">
									<h5 class="white uppercase">
										Highlight
									</h5>
									<h4 class="artwork-title white">
										<span class="bold"><?php echo $artwork_artist; ?></span>, <?php echo $artwork_title; ?>
									</h4>
								</div>
								<div class="col-3">
									<a href="#" class="collection-gallery-more">More Info <span class="icon" data-icon="”"></span></a>
								</div>
							</div>
						</div>
						<div class="artwork-content-lower">
							<h4 class="white artwork-description">
								<?php echo $artwork_description; ?>
							</h4>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
	<?php endif; ?>
</section>