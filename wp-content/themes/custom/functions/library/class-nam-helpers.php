<?php

class NAM_Helpers{

	public static function is_tree($pid) {  
		global $post;         
		if( ( $post->post_parent == $pid || is_page($pid) || get_the_ID() === $pid ) ) {
			// we're at the page or at a sub page
			return true;
		}  else{
			return false;  
		} 
	}


	public static function menu_graphic_two_column( $heading, $subheading, $link, $image ){ ?>
		<div class="menu-dropdown-graphic-1">
			<?php if( $heading ): ?>
				<h3 class="serif menu-dropdown-graphic-heading"><?php echo $heading; ?></h3>
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
			<?php //$image = get_field('about_menu_image', 'option'); ?>
			<?php if( $image ): ?>
				<div class="menu-dropdown-graphic-background" style="background-image: url('<?php echo $image['sizes']['large']; ?>')">
				</div>
			<?php endif; ?>
		</div>
	<?php } 


	//CARDS

	public static function card( $card_layout, $card_size, $card_type, $card_link, $card_image, $card_title, $card_info ){ ?>

		<div class="card card-type-<?php echo $card_type; ?> card-size-<?php echo $card_size; ?> card-layout-<?php echo $card_layout; ?> ">
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
					<?php if( $card_type === 'news' ): ?>
						<h5 class="card-text-news-date">
							<?php the_field('news_story_date'); ?>
						</h5>
					<?php endif; ?>
					<div class="card-text-title-ellipsis">
						<div>
							<h3 class="serif card-text-title">
								<?php if( $card_title ): echo $card_title; else: the_title(); endif; ?>
							</h3>
						</div>
					</div>
					<div class="nam-dash"></div>
					<div class="card-text-info">
						<?php if( $card_type === 'exhibition' ): ?>
							<h5 class="card-text-exhibition-location bold mb0">
								<?php the_field('exhibition_location'); ?>
							</h5>
							<h5 class="card-text-exhibition-dates bold">
								<?php the_field('exhibition_start_date'); ?> <?php if( get_field('exhibition_start_date') && get_field('exhibition_end_date') ): echo ' - '; endif; ?> <?php the_field('exhibition_end_date'); ?> 
							</h5>
						<?php elseif( $card_type === 'event' ): ?>
							<h5 class="card-text-event-date bold mb0">
								<?php the_field('event_date'); ?>
							</h5>
						<?php elseif( $card_type === 'news' ): ?>
							<?php if( get_field('short_description') ): ?>
								<h5 class="card-text-news-short-description">
									<?php the_field('short_description'); ?>
								</h5>
							<?php endif; ?>
							<h5 class="card-text-news-link mb0">
								<a href="<?php the_permalink(); ?>">Read More</a>
							</h5>
						<?php elseif( $card_type === 'class' ): ?>
							<?php if( get_field('class_start_date') ): ?>
								<h5 class="card-text-class-dates bold">
									<?php the_field('class_start_date'); ?> - <?php the_field('class_end_date'); ?>
									<?php if( get_field('number_of_sessions') ): ?> 
										<br>
										<span class="card-text-class-dates-sessions bold">
											(<?php the_field('number_of_sessions'); ?> Sessions)
										</span>
									<?php endif; ?>
								</h5>
							<?php endif; ?>
								<?php if( have_rows('class_days_and_times') ): ?>
									<h5 class="card-text-class-days bold">
										<?php  while ( have_rows('class_days_and_times') ) : the_row(); ?>
											<?php the_sub_field('class_day'); ?> <?php the_sub_field('class_start_time'); ?> - <?php the_sub_field('class_end_time'); ?>
										<?php endwhile; ?>
									</h5>
								<?php endif; ?>
							<?php if( get_field('class_instructor_name') ): ?>
								<h5 class="card-text-class-instructor bold">
									Taught by <?php the_field('class_instructor_name'); ?>
								</h5>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			</a>
		</div>
	<?php }  


	public static function card_terms($taxonomy){
		$terms = get_the_terms( $post, $taxonomy );
		if( $terms ):
		foreach ($terms as $term) :
			echo 'filter-';
			echo $term->slug;
			echo ' ';
		endforeach;
		endif;
	}

	public static function print_notices( $clear_notices ){
         $all_notices  = WC()->session->get( 'wc_notices', array() );
         $notice_types = apply_filters( 'woocommerce_notice_types', array( 'error', 'success', 'notice' ) );
         foreach ( $notice_types as $notice_type ) {
            if ( wc_notice_count( $notice_type ) > 0 ) {
                wc_get_template( "notices/{$notice_type}.php", array(
                    'messages' => array_filter( $all_notices[ $notice_type ] ),
                ) );
            }
        }
        if( $clear_notices ):
        	wc_clear_notices();
        endif; 
	}

} ?>