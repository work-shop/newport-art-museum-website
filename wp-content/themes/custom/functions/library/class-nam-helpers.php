<?php

class NAM_Helpers{


	public static function menu_graphic_two_column( $heading, $subheading, $link, $image ){ ?>
		<div class="menu-dropdown-graphic-1">
			<?php if( $heading ): ?>
				<h3 class="serif"><?php echo $heading; ?></h3>
			<?php endif; ?>
			<?php if( $subheading ): ?>
				<p class="serif"><?php echo $subheading; ?></p>
			<?php endif; ?>
			<?php if( $link ): ?>
				<div class="menu-dropdown-graphic-link">
					<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
						<?php echo $link['title']; ?>
						<span class="menu-dropdown-graphic-link-arrow">-></span>
					</a>
				</div>	
			<?php endif; ?>
		</div>
		<div class="menu-dropdown-graphic-2">
			<?php $image = get_field('about_menu_image', 'option'); ?>
			<?php if( $image ): ?>
				<div class="menu-dropdown-graphic-background" style="background-image: url('<?php echo $image['sizes']['large']; ?>')">
				</div>
			<?php endif; ?>
		</div>
	<?php } 


	//CARDS

	public static function card( $card_layout, $card_size, $card_type ){ ?>

		<div class="card card-<?php echo $card_type; ?> card-<?php echo $card_size; ?> card-layout-<?php echo $card_layout; ?>">
			<div class="card-image">
				<?php the_post_thumbnail('card_wide'); ?>
			</div>
			<div class="card-text">
				<?php the_title(); ?>
			</div>
		</div>

	<?php } 



} ?>