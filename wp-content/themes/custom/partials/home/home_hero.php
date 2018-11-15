<section class="block vh100" id="home-hero">
	<?php 
	$home_hero_image = get_field('home_hero_image');
	$home_hero_image = $home_hero_image['sizes']['page_hero'];
	$home_hero_heading = get_field('home_hero_heading');
	$home_hero_subheading = get_field('home_hero_subheading');
	$home_hero_text_color = get_field('home_hero_text_color');
	$home_hero_link_text = get_field('home_hero_link_text');
	$home_hero_link_url = get_field('home_hero_link_url');	
	$home_hero_link_color = get_field('home_hero_link_color');
	?>
	<div class="block-background mask mask-dark" style="background-image: url('<?php echo $home_hero_image; ?>');"></div>
	<?php if( get_field('home_hero_image_credit') ): ?>
		<div class="home-hero-image-credit">
			<span><?php the_field('home_hero_image_credit'); ?></span>
		</div>
	<?php endif; ?>
	<div class="vertical-center">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-9 col-lg-7">
					<?php if( $home_hero_subheading ): ?>
						<h1 class="home-hero-heading serif mt1 mb0 <?php echo $home_hero_text_color; ?>">
							<?php echo $home_hero_heading; ?>
						</h1>
						<div class="home-hero-separator nam-dash"></div>
					<?php endif; ?>
					<?php if( $home_hero_subheading ): ?>
						<h3 class="home-hero-subheading serif m0 <?php echo $home_hero_text_color; ?>">
							<?php echo $home_hero_subheading; ?>
						</h3>
					<?php endif; ?>
					<?php if( $home_hero_link_url && $home_hero_link_text ): ?>
						<div class="home-hero-link mt3 <?php echo $home_hero_link_color; ?>">
							<a href="<?php echo $home_hero_link_url; ?>" class="home-hero-link-button <?php echo $home_hero_link_color; ?>">
								<?php echo $home_hero_link_text; ?>
								<span class="icon" data-icon="‹"></span>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>