
<section class="block join-membership-levels padded" id="join-membership-levels">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<div class="col-md-8 col-lg-7 join-content">
				<?php
				$the_query = new WP_Query( array(
					'post_type' => 'membership-tier',
					'posts_per_page' => '-1'
				) ); ?>
				<?php if( $the_query->have_posts() ): ?>
					<h2 class="serif mb1">Membership Levels</h2>
					<div data-accordion-group>
						<?php while ( $the_query->have_posts() ) : ?>
							<?php $the_query->the_post(); ?>
							<?php $membership_for_sale = true; ?>
							<div class="accordion multi-collapse" data-accordion>
								<div class="accordion-label" data-control>
									<h4 class="accordion-title">
										<?php the_title(); ?>
									</h4>
									<span class="icon" data-icon="”"></span>
								</div>
								<div class="accordion-body" data-content>
									<div class="accordion-content-inner">
										<div class="wysiwyg">
											<?php the_field('membership_level_description'); ?>
										</div>
										<?php if( $membership_for_sale ): ?>
											<div class="accordion-link membership-link-button">
												<a href="#" class="">
													Join or Renew Now
												</a>
											</div>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					</div>	
				<?php endif; ?>
			</div>
			<div class="col-md-4 offset-lg-1 join-sidebar">
				<?php get_template_part('partials/join/join_sidebar' ); ?>
			</div>
		</div>
	</div>
</section>