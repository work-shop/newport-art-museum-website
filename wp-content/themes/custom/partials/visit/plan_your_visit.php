<div class="plan-your-visit">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<div class="col-12">
				<h2 class="serif plan-your-visit-title">Plan Your Visit</h2>
			</div>
		</div>
		<div class="row">
			<div class="col-md-7">
				<h3 class="plan-your-visit-currently museum-status">
					<?php get_template_part('partials/visit/museum_status'); ?>
				</h3>	
			</div>
			<div class="col-md-5 plan-your-visit-menu-link">
				<div class="menu-dropdown-graphic-link righted">
					<a href="/visit">
						Plan Your Visit ->
					</a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 col-xl-3 mb3">
				<h4 class="plan-your-visit-heading mb2">
					Location
				</h4>
				<div class="plan-your-visit-address address mb1">
					<?php get_template_part('partials/visit/address'); ?>
				</div>
				<div class="plan-your-visit-location-links">
					<a href="/visit/getting-here" class="button button-bordered mb1 plan-your-visit-getting-here">
						Getting here
					</a>
					<br>
					<a href="/visit/nearby-attractions" class="button button-bordered mb1 plan-your-visit-nearby-attractions" >
						Nearby Attractions
					</a>
				</div>
			</div>
			<div class="admissions col-md-6 col-xl-4 mb3">
				<h4 class="plan-your-visit-heading mb2">
					Admission
				</h4>
				<h4 class="plan-your-visit-admissions">
					<?php
					if( have_rows('admission','23') ):
						while ( have_rows('admission','23') ) : the_row(); ?>
							<div class="row">
								<div class="col-md-8 col-8">
									<h4 class="plan-your-visit-admission-type key">
										<?php the_sub_field('admission_type'); ?>
									</h4>
								</div>
								<div class="col-md-4 col-4">
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
			<div class="col-md-6 col-xl-5">
				<h4 class="plan-your-visit-heading mb2">
					Hours
				</h4>
				<div class="plan-your-visit-hours hours mb4">
					<?php get_template_part('partials/visit/hours'); ?>
				</div>
			</div>
			<div class="col-md-6 col-xl-5 offset-xl-7">
				<div class="plan-your-visit-holidays">
					<h4 class="plan-your-visit-heading mb2">
						Holidays
					</h4>
					<h4 class="holidays">
						<?php the_field('holidays_content','23'); ?>
					</h4>
				</div>
			</div>
		</div>
	</div>
</div>