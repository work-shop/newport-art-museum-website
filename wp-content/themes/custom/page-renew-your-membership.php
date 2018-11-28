<?php if (is_user_logged_in() ) {
	wp_redirect ( '/my-account/subscriptions' );
	exit;
}
?>
<?php get_template_part('partials/header'); ?>

<?php get_template_part('partials/renew/renew' ); ?>

<?php get_template_part('partials/footer' ); ?>