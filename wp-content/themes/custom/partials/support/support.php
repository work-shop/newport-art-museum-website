
<section class="block padded bg-light" id="support-content">
	<div class="container-fluid container-fluid-stretch">
		<?php wc_print_notices(); ?>
		<div class="row">
			<div class="col-md-6 col-lg-7 col-xl-7 support-content">
				<div class="wysiwyg support-letter">
					<?php the_field('support_page_letter'); ?>
				</div>
			</div>
			<div class="col-md-6 col-lg-5 col-xl-4 offset-xl-1 support-sidebar">
				<?php get_template_part('partials/sidebar' ); ?>
			</div>
		</div>
	</div>
</section>