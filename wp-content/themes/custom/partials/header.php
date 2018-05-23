<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<title>
		<?php
		if( is_front_page() ){
			bloginfo( 'name' ); echo ' | ';  bloginfo( 'description' );
		} else{
			wp_title( false ); echo ' | '; bloginfo( 'name' );
		}
		?>
	</title>

	<!-- typekit for freightdisp pro -->
	<link rel="stylesheet" href="https://use.typekit.net/reg3qbo.css">

	<link rel="icon" type="image/png" sizes="16x16" href="<?php bloginfo('template_directory'); ?>/images/favicon-16x16.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php bloginfo('template_directory'); ?>/images/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php bloginfo('template_directory'); ?>//images/favicon-96x96.png">
	<link rel="apple-touch-icon" href="<?php bloginfo('template_directory'); ?>/images/apple-icon.png">

	<meta name="description" content="<?php bloginfo('description'); ?>">
	<meta name="author" content="Work-Shop">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<?php wp_head(); ?>

</head>

<?php 
if( NAM_Helpers::is_tree(23) ): $GLOBALS['tree_slug'] = 'visit'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(40) || is_tax( 'exhibitions-categories' ) ): $GLOBALS['tree_slug'] = 'exhibitions'; $GLOBALS['include_page_nav'] = true; endif; 
if( NAM_Helpers::is_tree(42) ): $GLOBALS['tree_slug'] = 'collection'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(46) ): $GLOBALS['tree_slug'] = 'about'; $GLOBALS['include_page_nav'] = true; endif;
if( is_post_type_archive( 'news' ) || is_tax( 'news-categories' ) ): $GLOBALS['tree_slug'] = 'about'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(76) || is_tax( 'classes-categories' ) ): $GLOBALS['tree_slug'] = 'education'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(74) || is_tax( 'events-categories' ) ): $GLOBALS['tree_slug'] = 'events'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(70) ): $GLOBALS['tree_slug'] = 'join'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(66) ): $GLOBALS['tree_slug'] = 'support'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(4) || is_tax( 'products-categories' ) ): $GLOBALS['tree_slug'] = 'shop'; $GLOBALS['include_page_nav'] = true; endif;
?>

<?php 
$sitewide_alert_on = get_field('show_sitewide_alert', 'option'); 
if( $sitewide_alert_on === true ): 
	if( !isset($_COOKIE['nam_show_sitewide_alert']) || $_COOKIE['nam_show_sitewide_alert'] === false ):
		$sitewide_alert_class = 'sitewide-alert-on'; 
		$show_sitewide_alert = true;
	endif;
endif;
?>

<body <?php body_class( ' loading before-scroll modal-off menu-closed dropdown-off ' . $sitewide_alert_class . ' ' ); ?>>

	<?php if( $show_sitewide_alert ): get_template_part('partials/sitewide_alert'); endif; ?>
	<?php get_template_part('partials/nav'); ?>
	<?php get_template_part('partials/menus'); ?>

    <?php get_template_part('partials/notices'); ?>

	<main id="content">
