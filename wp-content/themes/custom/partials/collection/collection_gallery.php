<section class="block collection-gallery">
	<?php if( have_rows('collection_highlights_gallery') ): ?>
		<div class="slick slick-collection">
			<?php $count = 0; ?>
			<?php  while ( have_rows('collection_highlights_gallery') ) : the_row(); ?>
				<div class="slick-collection-slide closed" id="artwork-<?php echo $count; ?>">
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
					<div class="artwork-content" >
						<div class="artwork-content-upper">
							<div class="row row-full artwork-content-upper-controls">
								<div class="col-9 p0">
									<h6 class="white uppercase bold tracked-less">
										Highlight
									</h6>
								</div>
								<div class="col-3 p0">
									<a href="#" class="collection-gallery-more righted h5 display-block" data-target="#artwork-<?php echo $count; ?>">More Info <span class="icon collection-gallery-more-icon" data-icon="ﬁ"></span></a>
								</div>
							</div>
							<div class="row row-full">
								<h5 class="artwork-title white">
									<span class="bold">
										<?php echo $artwork_artist; ?></span><?php if( $artwork_title ): ?>, 
									<span class="artwork-title-title italic">
										<?php echo $artwork_title; ?>
									</span>
								<?php endif; ?>
							</h5>
						</div>
					</div>
					<div class="artwork-content-lower">
						<h5 class="white artwork-description">
							<?php echo $artwork_description; ?>
						</h5>
					</div>
				</div>
				<div class="artwork-content-bottom">
				</div>
			</div>
			<?php $count++; ?>
		<?php endwhile; ?>
	</div>
<?php endif; ?>
</section>

