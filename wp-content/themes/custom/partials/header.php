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

<?php get_template_part('partials/setup_slug'); ?>
<?php get_template_part('partials/setup_sitewide_alert'); ?>

<body <?php body_class( ' loading before-scroll modal-off menu-closed dropdown-off ' . $sitewide_alert_class . ' ' ); ?>>

	<?php if( $show_sitewide_alert ): get_template_part('partials/sitewide_alert'); endif; ?>
	<?php get_template_part('partials/nav'); ?>
	<?php get_template_part('partials/menus'); ?>

	<main id="content">
		
		<?php // get_template_part('partials/notices'); ?>
		

		<?php 
		// wc_add_notice('This is a success notice', 'success');
		// wc_add_notice('This is a regular notice', 'notice');
		// wc_add_notice('This is an error notice', 'error');
		?>