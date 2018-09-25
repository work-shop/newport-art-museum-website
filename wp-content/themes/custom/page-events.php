
<?php get_template_part('partials/header'); ?>

<?php get_template_part('partials/page/page_nav' ); ?>

<?php $show_events_page_temporary_message = get_field('show_events_page_temporary_message','74'); ?>
<?php if( $show_events_page_temporary_message ): ?>
	<?php $events_page_temporary_message = get_field('events_page_temporary_message','74'); ?>
	<section class="block padded" id="classes-page-temporary message">
		<div class="container-fluid container-fluid-stretch">
			<div class="row">
				<div class="col-lg-8">
					<div class="bg-error p2">
						<h4 class="error mb0 bold">
							<?php echo $events_page_temporary_message; ?>
						</h4>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php else: ?>
		<?php get_template_part('partials/filters' ); ?>
		<?php get_template_part('partials/events/events' ); ?>
<?php endif; ?>

<?php get_template_part('partials/page/page_end_info' ); ?>

<?php get_template_part('partials/footer' ); ?>
