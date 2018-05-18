<?php //https://codepen.io/benfrain/pen/MppBYa ?>

<nav id="page-nav" class="present">
	<?php 
	if( $GLOBALS['include_page_nav'] ):
		$GLOBALS['links'] = get_field( $GLOBALS['tree_slug'] . '_menu_links', 'option');
		$GLOBALS['links_additional'] = get_field( $GLOBALS['tree_slug'] . '_menu_additional_links', 'option');
		$GLOBALS['page_nav'] = true;
		$GLOBALS['page_title'] = ucfirst( $GLOBALS['tree_slug'] );
		get_template_part('partials/menus_links' ); 
	endif; ?>
</nav>