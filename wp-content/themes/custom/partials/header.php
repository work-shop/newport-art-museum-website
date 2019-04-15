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

	<?php 
	if( get_field('social_media_title') ):
		$social_title = get_field('social_media_title'); 
	else:
		$social_title = get_bloginfo( 'name' );
	endif;
	if( get_field('social_media_description') ):
		$social_description = get_field('social_media_description');
	else:
		$social_description = '';
	endif;
	if( get_field('social_media_url') ):
		$social_url = get_field('social_media_url'); 
	else: 
		$social_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	endif;
	if( get_field('social_media_image') ):
		$social_image_array = get_field('social_media_image');
		$social_image = $social_image_array['sizes']['fb'];
	else:
		$social_image = '';
	endif;

	?>

	<!-- Facebook Open Graph data -->
	<meta property="og:title" content="<?php echo $social_title; ?>" />
	<meta property="og:description" content="<?php echo $social_description; ?>" />
	<meta property="og:image" content="<?php echo $social_image; ?>" />
	<meta property="og:url" content="<?php echo $social_url; ?>" />
	<meta property="og:type" content="website" />

	<!-- Twitter Card data -->
	<meta name="twitter:card" value="<?php echo $social_description; ?>">

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

	<!-- Facebook Pixel Code -->
	<script>
		!function(f,b,e,v,n,t,s)
		{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
			n.callMethod.apply(n,arguments):n.queue.push(arguments)};
			if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
			n.queue=[];t=b.createElement(e);t.async=!0;
			t.src=v;s=b.getElementsByTagName(e)[0];
			s.parentNode.insertBefore(t,s)}(window,document,'script',
				'https://connect.facebook.net/en_US/fbevents.js');
			fbq('init', '1201212850080371'); 
			fbq('track', 'PageView');
		</script>
		<noscript>
			<img height="1" width="1" 
			src="https://www.facebook.com/tr?id=1201212850080371&ev=PageView
			&noscript=1"/>
		</noscript>
		<!-- End Facebook Pixel Code -->

	</head>

	<?php get_template_part('partials/setup_slug'); ?>
	<?php
	$sitewide_alert_on = get_field('show_sitewide_alert', 'option');
	if( $sitewide_alert_on === true ):
		if( !isset($_COOKIE['nam_show_sitewide_alert_2']) || $_COOKIE['nam_show_sitewide_alert_2'] === false ):
			$sitewide_alert_class = 'sitewide-alert-on';
			$show_sitewide_alert = true;
		endif;
	endif;
	?>

	<body <?php body_class( ' loading before-scroll modal-off menu-closed dropdown-off ' . $sitewide_alert_class . ' ' ); ?>>

		<?php if( $show_sitewide_alert ): get_template_part('partials/sitewide_alert'); endif; ?>
		<?php get_template_part('partials/nav'); ?>
		<?php get_template_part('partials/menus'); ?>

		<main id="content">
			
			<?php // get_template_part('partials/notices'); ?>