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

	public static function card( $card_layout, $card_size, $card_type, $card_link, $card_image, $card_title, $card_info ){ ?>

		<div class="card card-type-<?php echo $card_type; ?> card-size-<?php echo $card_size; ?> card-layout-<?php echo $card_layout; ?>">
			<a href="<?php if( $card_link ): echo $card_link; else: the_permalink(); endif; ?>">
				<div class="card-image">
					<?php if( $card_image ): ?>
						<img src="<?php echo $card_image['sizes']['card_medium']; ?>">
						<?php else: ?>
							<?php if( $card_size === 'wide' ): 
								$size = 'card_wide';
							else:
								$size = 'card_medium'; ?>
							<?php endif; ?>
							<?php the_post_thumbnail($size); ?>
						<?php endif; ?>
					</div>
					<div class="card-text">
						<h3 class="serif card-text-title">
							<?php if( $card_title ): echo $card_title; else: the_title(); endif; ?>
						</h3>
						<div class="nam-dash"></div>
						<div class="card-text-info">
							<?php if( $card_type === 'exhibition' ): ?>
								<h4 class="card-text-exhibition-location mb0">
									<?php the_field('exhibition_location'); ?>
								</h4>
								<h4 class="card-text-exhibition-dates">
									<?php the_field('exhibition_start_date'); ?> - <?php the_field('exhibition_end_date'); ?> 
								</h4>
							<?php endif; ?>
						</div>
					</div>
				</a>
			</div>

		<?php }  



	} ?>