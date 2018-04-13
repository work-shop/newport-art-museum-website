
<?php get_template_part('partials/header'); ?>

<?php global $post;
//check if this is a child page of the collections page
//this is a hacky workaround to catch all the child pages instead of making their own templates
if ( $post->post_parent === 42 ) { ?>

	<?php get_template_part('partials/page/page_nav'); ?>		

	<?php get_template_part('partials/collection/collection_gallery'); ?>

	<?php get_template_part('partials/flexible_content/flexible_content' ); ?>

	<?php get_template_part('partials/footer' ); ?>

<?php } else { ?>


	<?php get_template_part('partials/page/page_hero' ); ?>

	<?php get_template_part('partials/page/page_intro' ); ?>

	<?php get_template_part('partials/flexible_content/flexible_content' ); ?>

	<?php get_template_part('partials/page/page_end_info' ); ?>

	<?php get_template_part('partials/footer' ); ?>

<?php } ?>
