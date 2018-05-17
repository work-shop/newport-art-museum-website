<div id="menus">
	
	<menu id="menu-visit" class="menu-dropdown off" data-dropdown="visit" >
		<div class="menu-dropdown-links">
			<?php $GLOBALS['links'] = get_field('visit_menu_links', 'option'); ?>
			<?php $GLOBALS['links_additional'] = get_field('visit_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			<?php get_template_part('partials/visit/plan_your_visit' ); ?>
		</div>
	</menu>

	<menu id="menu-about" class="menu-dropdown off" data-dropdown="about" >
		<div class="menu-dropdown-links">
			<?php $GLOBALS['links'] = get_field('about_menu_links', 'option'); ?>
			<?php $GLOBALS['links_additional'] = get_field('about_menu_additional_links', 'option'); ?>		
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic menu-dropdown-graphic-two-column">
			<?php 
			$heading = get_field('about_menu_heading', 'option'); 
			$subheading = get_field('about_menu_subheading', 'option'); 
			$link = get_field('about_menu_link', 'option');
			$image = get_field('about_menu_image', 'option');
			NAM_Helpers::menu_graphic_two_column( $heading, $subheading, $link, $image );
			?>
		</div>
	</menu>

	<menu id="menu-exhibitions" class="menu-dropdown off" data-dropdown="exhibitions" >
		<div class="menu-dropdown-links">
			<?php $GLOBALS['links'] = get_field('exhibitions_menu_links', 'option'); ?>
			<?php $GLOBALS['links_additional'] = get_field('exhibitions_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic menu-dropdown-graphic-cards menu-dropdown-graphic-cards-two">
			<div class="container-fluid contrainer-fluid-stretch">
				<div class="row menu-dropdown-cards-upper">
					<div class="col-md-6">
						<h3 class="serif">Now on view</h3>
					</div>
					<div class="col-md-6">
						<div class="menu-dropdown-graphic-link righted">
							<a href="/exhibitions">See All Exhibitions 
								<span class="menu-dropdown-graphic-link-arrow">-></span>
							</a>
						</div>
					</div>
				</div>
				<div class="row menu-dropdown-cards-lower">
					<?php
					$posts = get_field('exhibitions_menu_featured_exhibitions','option'); 
					if ($posts) : ?>
						<?php foreach( $posts as $post): // variable must be called $post (IMPORTANT) ?>
							<div class="col-md-6">
								<?php 
								$card_layout = 'text_right'; //'text_right', 'text_bottom', 'text_top'
								$card_size = 'menu'; //'wide', 'medium', 'small', 'menu'
								$card_type = 'exhibition';  //'event', 'class', 'product', 'news' 
								NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null );
								?>							
							</div>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</menu>

	<menu id="menu-collection" class="menu-dropdown off" data-dropdown="collection" >
		<div class="menu-dropdown-links">
			<?php $GLOBALS['links'] = get_field('collection_menu_links', 'option'); ?>
			<?php $GLOBALS['links_additional'] = get_field('collection_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic menu-dropdown-graphic-cards menu-dropdown-graphic-cards-three">
			<div class="container-fluid contrainer-fluid-stretch">
				<div class="row menu-dropdown-cards-upper">
					<div class="col-md-6">
						<h3 class="serif">Collection</h3>
					</div>
					<div class="col-md-6">
						<div class="menu-dropdown-graphic-link righted">
							<a href="/exhibitions">Learn About the Collection
								<span class="menu-dropdown-graphic-link-arrow">-></span>
							</a>
						</div>
					</div>
				</div>
				<div class="row menu-dropdown-cards-lower">
					<?php if( have_rows('collection_menu_featured_artworks','option') ): ?>
						<?php  while ( have_rows('collection_menu_featured_artworks','option') ) : the_row(); ?>
							<div class="col-md-4">
								<?php 
								$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
								$card_size = 'menu'; //'wide', 'medium', 'small', 'menu'
								$card_type = 'generic';  //'event', 'class', 'product', 'news', 'generic' 
								$card_link = '/collection';
								$card_image = get_sub_field('image');
								$card_title = get_sub_field('title');
								$card_info = get_sub_field('artist');
								NAM_Helpers::card( $card_layout, $card_size, $card_type, $card_link, $card_image, $card_title, $card_info );
								?>							
							</div>
						<?php endwhile; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</menu>

	<menu id="menu-events" class="menu-dropdown off" data-dropdown="events" >
		<div class="menu-dropdown-links">
			<?php $GLOBALS['links'] = get_field('events_menu_links', 'option'); ?>
			<?php $GLOBALS['links_additional'] = get_field('events_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			events Graphic
		</div>
	</menu>

	<menu id="menu-education" class="menu-dropdown off" data-dropdown="education" >
		<div class="menu-dropdown-links">
			<?php $GLOBALS['links'] = get_field('education_menu_links', 'option'); ?>
			<?php $GLOBALS['links_additional'] = get_field('education_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">

		</div>
	</menu>

	<menu id="menu-join" class="menu-dropdown off" data-dropdown="join" >
		<div class="menu-dropdown-links">
			<?php $GLOBALS['links'] = get_field('join_menu_links', 'option'); ?>
			<?php $GLOBALS['links_additional'] = get_field('join_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic menu-dropdown-graphic-two-column">
			<?php 
			$heading = get_field('join_menu_heading', 'option'); 
			$subheading = get_field('join_menu_subheading', 'option'); 
			$link = get_field('join_menu_link', 'option');
			$image = get_field('join_menu_image', 'option');
			NAM_Helpers::menu_graphic_two_column( $heading, $subheading, $link, $image );
			?>
		</div>
	</menu>

	<menu id="menu-support" class="menu-dropdown off" data-dropdown="support" >
		<div class="menu-dropdown-links">
			<?php $GLOBALS['links'] = get_field('support_menu_links', 'option'); ?>
			<?php $GLOBALS['links_additional'] = get_field('support_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic menu-dropdown-graphic-two-column">
			<?php 
			$heading = get_field('support_menu_heading', 'option'); 
			$subheading = get_field('support_menu_subheading', 'option'); 
			$link = get_field('support_menu_link', 'option');
			$image = get_field('support_menu_image', 'option');
			NAM_Helpers::menu_graphic_two_column( $heading, $subheading, $link, $image );
			?>
		</div>
	</menu>

	<div id="blanket-dropdown" class="dropdown-close"></div>

</div>

