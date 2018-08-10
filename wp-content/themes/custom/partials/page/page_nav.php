<?php //https://codepen.io/benfrain/pen/MppBYa ?>

<?php if( $GLOBALS['include_page_nav'] ): ?>
	<nav id="page-nav" class="present">
		<?php 
		$GLOBALS['links'] = get_field( $GLOBALS['tree_slug'] . '_menu_links', 'option');
		$GLOBALS['links_additional'] = get_field( $GLOBALS['tree_slug'] . '_menu_additional_links', 'option');
		$GLOBALS['page_nav'] = true;
		if($GLOBALS['tree_slug'] === 'my_account'): 
			$user = wp_get_current_user();
			$name = $user->user_firstname;
			$GLOBALS['page_title'] = 'Welcome Back, ' . $name;
		else:
			$GLOBALS['page_title'] = ucfirst( $GLOBALS['tree_slug'] );
		endif; 
		get_template_part('partials/menus_links' ); 
		?>
	</nav>
<?php endif; ?>
