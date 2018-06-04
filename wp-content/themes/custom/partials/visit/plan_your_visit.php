<div class="plan-your-visit">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<div class="plan-your-visit-left col-lg-8">
				<div class="row">
					<div class="col-xs-12">
						<h2 class="serif plan-your-visit-title">Plan Your Visit</h2>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5 col-sm-5 mb3">
						<h4 class="plan-your-visit-heading mb2">
							Hours
						</h4>
						<h4 class="plan-your-visit-currently museum-status mb2">
							<?php get_template_part('partials/visit/museum_status'); ?>
						</h4>	
						<div class="plan-your-visit-hours hours">
							<?php get_template_part('partials/visit/hours'); ?>
						</div>
					</div>
					<div class="admissions col-md-6 offset-md-1 col-sm-6 offset-sm-1">
						<h4 class="plan-your-visit-heading mb2">
							Admission
						</h4>
						<h4 class="plan-your-visit-admissions">
							<?php
							if( have_rows('admission','23') ):
								while ( have_rows('admission','23') ) : the_row(); ?>
									<div class="row">
										<div class="col-md-8 col-xs-8">
											<h4 class="plan-your-visit-admission-type key">
												<?php the_sub_field('admission_type'); ?>
											</h4>
										</div>
										<div class="col-md-4 col-xs-4">
											<h4 class="plan-your-visit-admission-cost value">
												<?php the_sub_field('admission_cost'); ?>
											</h4>
										</div>
									</div> 
									<?php 
								endwhile;
							endif;
							?>
						</h4>		
					</div>
				</div>
			</div>
			<div class="plan-your-visit-right col-lg-4">
				<div class="plan-your-visit-3">
					<?php $google_maps_api_key = 'AIzaSyCh9fjCJw8vxVBIvC6_IAtMQ050t4iYxjg'; ?>
					<iframe class="plan-your-visit-map" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=Newport%20Art%20Museum&key=AIzaSyCh9fjCJw8vxVBIvC6_IAtMQ050t4iYxjg" allowfullscreen></iframe>
				</div>		
			</div>
		</div>
	</div>
</div>