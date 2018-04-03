<div id="menus">

	<menu id="menu-visit" class="menu-dropdown off" data-dropdown="visit" >
		<div class="menu-dropdown-links">
			<?php $posts = get_field('visit_menu_links', 'option'); ?>
			<?php $links = get_field('visit_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			<?php get_template_part('partials/visit/plan_your_visit' ); ?>
		</div>
	</menu>

	<menu id="menu-about" class="menu-dropdown off" data-dropdown="about" >
		<div class="menu-dropdown-links">
			<?php $posts = get_field('about_menu_links', 'option'); ?>
			<?php $links = get_field('about_menu_additional_links', 'option'); ?>		
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			ABOUT Graphic
		</div>
	</menu>

	<menu id="menu-exhibitions" class="menu-dropdown off" data-dropdown="exhibitions" >
		<div class="menu-dropdown-links">
			<?php $posts = get_field('exhibitions_menu_links', 'option'); ?>
			<?php $links = get_field('exhibitions_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			Exhibitions Graphic
		</div>
	</menu>

	<menu id="menu-collection" class="menu-dropdown off" data-dropdown="collection" >
		<div class="menu-dropdown-links">
			<?php $posts = get_field('collection_menu_links', 'option'); ?>
			<?php $links = get_field('collection_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			Collection Graphic
		</div>
	</menu>

	<menu id="menu-events" class="menu-dropdown off" data-dropdown="events" >
		<div class="menu-dropdown-links">
			<?php $posts = get_field('events_menu_links', 'option'); ?>
			<?php $links = get_field('events_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			events Graphic
		</div>
	</menu>

	<menu id="menu-education" class="menu-dropdown off" data-dropdown="education" >
		<div class="menu-dropdown-links">
			<?php $posts = get_field('education_menu_links', 'option'); ?>
			<?php $links = get_field('education_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			education Graphic
		</div>
	</menu>

	<menu id="menu-join" class="menu-dropdown off" data-dropdown="join" >
		<div class="menu-dropdown-links">
			<?php $posts = get_field('join_menu_links', 'option'); ?>
			<?php $links = get_field('join_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			join Graphic
		</div>
	</menu>

	<menu id="menu-support" class="menu-dropdown off" data-dropdown="support" >
		<div class="menu-dropdown-links">
			<?php $posts = get_field('support_menu_links', 'option'); ?>
			<?php $links = get_field('support_menu_additional_links', 'option'); ?>
			<?php get_template_part('partials/menus_links' ); ?>
		</div>
		<div class="menu-dropdown-graphic">
			support Graphic
		</div>
	</menu>

	<div id="blanket-dropdown" class="dropdown-close"></div>

</div>

