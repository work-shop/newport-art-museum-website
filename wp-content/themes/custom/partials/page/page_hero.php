<?php if( get_field('show_hero') ): ?>
	<?php if( is_single() ): ?>
		<div class="single-has-hero">
	<?php endif; ?>
	<?php $hero_image = get_field('hero_image');
	$hero_image = $hero_image['sizes']['page_hero'];
	$hero_text = get_field('hero_text'); ?>
	<section class="block page-hero" id="page-hero">
		<div class="block-background page-hero-image" style="background-image: url('<?php echo $hero_image; ?>');">
		</div>
		<?php if( $hero_text ): ?>
			<div class="page-hero-text">
				<h1 class="khaki serif">
					<?php echo $hero_text; ?>
				</h1>
			</div>
		<?php endif; ?>
	</section>
<?php endif; ?>