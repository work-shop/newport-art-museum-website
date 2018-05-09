<?php if( get_field('show_page_end_info') ): ?>
	<?php 
	$page_end_info_image = get_field('page_end_info_image');
	$page_end_info_image = $page_end_info_image['sizes']['large'];
	$page_end_info_heading = get_field('page_end_info_heading'); ?>
	<section class="block page-end-info bg-khaki row row-equal-height">
		<div class="page-end-info-1 col-md-4 col-lg-5">
			<div class="block-background" style="background-image: url('<?php echo $page_end_info_image; ?>');">
			</div>
		</div>
		<div class="page-end-info-2 col-md-8 col-lg-7">
			<?php if( $page_end_info_heading ): ?>
				<h2 class="serif mb2">
					<?php echo $page_end_info_heading; ?>
				</h2>
			<?php endif; ?>
			<div class="page-end-info-links row mb4">
				<?php if( have_rows('page_end_info_links_column_1') ): ?>
					<div class="page-end-info-links-1 col-md-6 col-lg-4">
						<ul class="">
							<?php while ( have_rows('page_end_info_links_column_1') ) : the_row(); ?>
								<?php $link = get_sub_field('link'); ?>
								<li>
									<a href="<?php echo $link['url']; ?>" target= "<?php echo $link['target']; ?>">
										<?php echo $link['title']; ?>
									</a>
								</li>
							<?php endwhile; ?>
						</ul>
					</div>
				<?php endif; ?>
				<?php if( have_rows('page_end_info_links_column_2') ): ?>
					<div class="page-end-info-links-2 col-md-6 col-lg-4">
						<ul class="">
							<?php while ( have_rows('page_end_info_links_column_2') ) : the_row(); ?>
								<?php $link = get_sub_field('link'); ?>
								<li>
									<a href="<?php echo $link['url']; ?>" target= "<?php echo $link['target']; ?>">
										<?php echo $link['title']; ?>
									</a>
								</li>
							<?php endwhile; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
			<h4 class="">Want to contact us? Call us at 401-848-8200, <br>
				or email us at <a href="mailto:info@newportartmuseum.org" target="_blank">info@newportartmuseum.org</a>
			</h4>
		</div>
	</section>
	<?php endif; ?>