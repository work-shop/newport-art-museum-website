
<?php get_template_part('partials/header'); ?>

<?php get_template_part('partials/page/page_nav' ); ?>

<?php 
$show_classes_page_temporary_message = get_field('show_classes_page_temporary_message','78');
?>
<?php if( $show_classes_page_temporary_message ): ?>
	<?php $classes_page_temporary_message = get_field('classes_page_temporary_message','78'); ?>
	<section class="block padded" id="classes-page-temporary message">
		<div class="container-fluid container-fluid-stretch">
			<div class="row">
				<div class="col-lg-8">
					<div class="bg-error p2">
						<h4 class="error mb0 bold">
							<?php echo $classes_page_temporary_message; ?>
						</h4>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php else: ?>
	<?php get_template_part('partials/filters' ); ?>
	<?php get_template_part('partials/classes/classes' ); ?>
<?php endif; ?>

<?php get_template_part('partials/page/page_end_info' ); ?>

<?php get_template_part('partials/footer' ); ?>
