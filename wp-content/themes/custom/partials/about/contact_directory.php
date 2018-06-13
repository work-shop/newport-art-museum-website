<section class="block padded" id="contact-directory">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<div class="col-12">
				<h2 class="serif mb1">
					Directory
				</h2>
			</div>
		</div>
		<?php if( have_rows('contact_directory') ): ?>
			<div class="contact-directory-list">
				<?php  while ( have_rows('contact_directory') ) : the_row(); ?>
					<div class="row contact-directory-item">
						<div class="col-md-4 contact-title">
							<h4 class="bold"><?php the_sub_field('contact_title'); ?></h4>
						</div>
						<div class="col-md-3 contact-person">
							<h4><?php the_sub_field('contact_person'); ?></h4>
						</div>
						<div class="col-md-2 contact-phone-number">
							<h5><?php the_sub_field('contact_phone_number'); ?></h5>
						</div>
						<div class="col-md-3 contact-email-address">
							<h5><?php the_sub_field('contact_email_address'); ?></h5>
						</div>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>