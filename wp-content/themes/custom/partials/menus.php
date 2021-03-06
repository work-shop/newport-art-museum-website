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
			<?php $heading = get_field('exhibitions_menu_heading', 'option'); ?>
			<div class="container-fluid contrainer-fluid-stretch">
				<div class="row menu-dropdown-cards-upper">
					<div class="col-md-6">
						<h3 class="serif menu-dropdown-graphic-heading"><?php echo $heading; ?></h3>
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
					$items = get_field('exhibitions_menu_featured_exhibitions','option'); 
					if ($items) : ?>
						<?php $count = 0; ?>
						<?php foreach( $items as $post): // variable must be called $post (IMPORTANT) ?>
							<?php setup_postdata($post); ?>
							<div class="col-md-4 <?php // if( $count === 1 ): echo ' offset-md-2'; endif ?>">
								<?php 
								$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
								$card_size = 'menu'; //'wide', 'medium', 'small', 'menu'
								$card_type = 'exhibition';  //'event', 'class', 'product', 'news' 
								NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null );
								?>							
							</div>
							<?php $count++; ?>
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
		<div class="menu-dropdown-graphic menu-dropdown-graphic-two-column">
			<?php 
			$heading = get_field('collection_menu_heading', 'option'); 
			$subheading = get_field('collection_menu_subheading', 'option'); 
			$link = get_field('collection_menu_link', 'option');
			$image = get_field('collection_menu_image', 'option');
			NAM_Helpers::menu_graphic_two_column( $heading, $subheading, $link, $image );
			?>
		</div>
	</menu>

	<menu id="menu-events" class="menu-dropdown off" data-dropdown="events" >
		<div class="menu-dropdown-links">
			<?php $GLOBALS['links'] = get_field('events_menu_links', 'option'); ?>
			<?php $GLOBALS['links_additional'] = get_field('events_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic menu-dropdown-graphic-cards menu-dropdown-graphic-cards-three">
			<?php $show_events_page_temporary_message = get_field('show_events_page_temporary_message','74'); ?>
			<?php if( $show_events_page_temporary_message ): ?>
				<?php $events_page_temporary_message = get_field('events_page_temporary_message','74'); ?>
				<div class="container-fluid container-fluid-stretch">
					<div class="row mt3">
						<div class="col-lg-10">
							<div class="bg-error p2">
								<h4 class="error mb0 bold">
									<?php echo $events_page_temporary_message; ?>
								</h4>
							</div>
						</div>
					</div>
				</div>
				<?php else: ?>
					<div class="container-fluid contrainer-fluid-stretch">
						<div class="row menu-dropdown-cards-upper">
							<div class="col-md-6">
								<?php $heading = get_field('events_menu_heading', 'option'); ?>
								<h3 class="serif menu-dropdown-graphic-heading"><?php echo $heading; ?></h3>
							</div>
							<div class="col-md-6">
								<div class="menu-dropdown-graphic-link righted">
									<a href="/events">See All Events 
										<span class="menu-dropdown-graphic-link-arrow">-></span>
									</a>
								</div>
							</div>
						</div>
						<div class="row menu-dropdown-cards-lower">
							<?php
							$items = get_field('events_menu_featured_events','option'); 
							if ($items) : ?>
								<?php $count = 0; ?>
								<?php foreach( $items as $post): // variable must be called $post (IMPORTANT) ?>
									<?php setup_postdata($post); ?>
									<div class="col-md-4">
										<?php 
								$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
								$card_size = 'menu'; //'wide', 'medium', 'small', 'menu'
								$card_type = 'event';  //'event', 'class', 'product', 'news' 
								NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null );
								?>							
							</div>
							<?php $count++; ?>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; //temporary message ?>
	</div>
</menu>

<menu id="menu-education" class="menu-dropdown off" data-dropdown="education" >
	<div class="menu-dropdown-links">
		<?php $GLOBALS['links'] = get_field('education_menu_links', 'option'); ?>
		<?php $GLOBALS['links_additional'] = get_field('education_menu_additional_links', 'option'); ?>
		<?php get_template_part('partials/menus_links' ); ?>
	</div>
	<div class="menu-dropdown-graphic menu-dropdown-graphic-cards menu-dropdown-graphic-cards-three">
		<?php $show_classes_page_temporary_message = get_field('show_classes_page_temporary_message','78'); ?>
		<?php if( $show_classes_page_temporary_message ): ?>
			<?php $classes_page_temporary_message = get_field('classes_page_temporary_message','78'); ?>
			<div class="container-fluid container-fluid-stretch">
				<div class="row mt3">
					<div class="col-lg-10">
						<div class="bg-error p2">
							<h4 class="error mb0 bold">
								<?php echo $classes_page_temporary_message; ?>
							</h4>
						</div>
					</div>
				</div>
			</div>
			<?php else: ?>
				<div class="container-fluid contrainer-fluid-stretch">
					<div class="row menu-dropdown-cards-upper">
						<div class="col-md-6">
							<?php $heading = get_field('education_menu_heading', 'option'); ?>
							<h3 class="serif menu-dropdown-graphic-heading"><?php echo $heading; ?></h3>
						</div>
						<div class="col-md-6">
							<div class="menu-dropdown-graphic-link righted">
								<a href="/education/classes">See All Classes 
									<span class="menu-dropdown-graphic-link-arrow">-></span>
								</a>
							</div>
						</div>
					</div>
					<div class="row menu-dropdown-cards-lower">
						<?php
						$items = get_field('education_menu_featured_classes','option'); 
						if ($items) : ?>
							<?php $count = 0; ?>
							<?php foreach( $items as $post): // variable must be called $post (IMPORTANT) ?>
								<?php setup_postdata($post); ?>
								<div class="col-md-4">
									<?php 
								$card_layout = 'text_bottom'; //'text_right', 'text_bottom', 'text_top'
								$card_size = 'menu'; //'wide', 'medium', 'small', 'menu'
								$card_type = 'class';  //'event', 'class', 'product', 'news' 
								NAM_Helpers::card( $card_layout, $card_size, $card_type, null, null, null, null );
								?>							
							</div>
							<?php $count++; ?>
						<?php endforeach; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
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

