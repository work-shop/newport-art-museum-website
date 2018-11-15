<?php get_template_part('partials/header'); ?>

<?php 
if( is_user_logged_in() ): 
	get_template_part('partials/page/page_nav');
endif; 
?>

<?php get_template_part('partials/ecommerce/checkout'); ?>

<?php get_template_part('partials/footer' ); ?>
