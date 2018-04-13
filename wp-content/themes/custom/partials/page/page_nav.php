<?php //https://codepen.io/benfrain/pen/MppBYa ?>

<nav id="page-nav">

	<?php 

	function is_tree($pid) {  
		global $post;         
		if( is_page() && ( $post->post_parent == $pid || is_page($pid) ) ) {
			// we're at the page or at a sub page
			return true;
		}  else{
			return false;  
		} 
	};

	if( is_tree(23) ): $page_slug = 'visit';  endif;
	if( is_tree(40) ): $page_slug = 'exhibitions';  endif; 
	if( is_tree(42) ): $page_slug = 'collection';  endif;
	if( is_tree(46) ): $page_slug = 'about';  endif;
	if( is_tree(64) ): $page_slug = 'about';  endif;
	if( is_tree(76) ): $page_slug = 'education';  endif;
	if( is_tree(74) ): $page_slug = 'events';  endif;
	if( is_tree(70) ): $page_slug = 'join';  endif;
	if( is_tree(66) ): $page_slug = 'support';  endif;
	if( is_tree(4) ): $page_slug = 'shop';  endif;

	$GLOBALS['links'] = get_field( $page_slug . '_menu_links', 'option');
	$GLOBALS['links_additional'] = get_field( $page_slug . '_menu_additional_links', 'option');
	$GLOBALS['page_nav'] = true;
	$GLOBALS['page_title'] = ucfirst($page_slug);
	
	?>

	<?php get_template_part('partials/menus_links' ); ?>

</nav>