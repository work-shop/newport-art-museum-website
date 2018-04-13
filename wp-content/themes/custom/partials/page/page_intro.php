<?php if( get_field('show_page_introduction_section') && get_field('page_introduction_text') ): ?>
	<?php $page_introduction_text = get_field('page_introduction_text'); ?>
	<section class="block page-introduction">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 col-md-10">
					<div class="page-introduction-text">
						<h3 class="serif">
							<?php echo $page_introduction_text; ?>
						</h3>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>