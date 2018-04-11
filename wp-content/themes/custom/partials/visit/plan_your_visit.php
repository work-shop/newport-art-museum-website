<div class="plan-your-visit clearfix" >
	<div class="plan-your-visit-container">
		<div class="plan-your-visit-left">
			<div class="row">
				<div class="col-xs-12">
					<h2 class="serif plan-your-visit-title">Plan Your Visit</h2>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<h4 class="plan-your-visit-heading mb2">
						Hours
					</h4>
					<h4 class="plan-your-visit-currently museum-status mb2">
						<?php get_template_part('partials/visit/museum_status'); ?>
					</h4>	
					<h4 class="plan-your-visit-hours">
						<?php
						if( have_rows('hours_content','23') ):
							while ( have_rows('hours_content','23') ) : the_row(); ?>
							<div class="row">
								<div class="col-md-6">
									<h4 class="plan-your-visit-days">
										<?php the_sub_field('days'); ?>
									</h4>
								</div>
								<div class="col-md-6">
									<h4 class="plan-your-visit-hours">
										<?php the_sub_field('hours'); ?>
									</h4>
								</div>
							</div> 
							<?php 
						endwhile;
					endif;
					?>
				</h4>			
			</div>
			<div class="col-md-6">
				<h4 class="plan-your-visit-heading mb2">
					Admission
				</h4>
				<h4 class="plan-your-visit-admissions">
					<?php
					if( have_rows('admission','23') ):
						while ( have_rows('admission','23') ) : the_row(); ?>
						<div class="row">
							<div class="col-md-6">
								<h4 class="plan-your-visit-admission-type">
									<?php the_sub_field('admission_type'); ?>
								</h4>
							</div>
							<div class="col-md-6">
								<h4 class="plan-your-visit-admission-cost">
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
<div class="plan-your-visit-right">
	<div class="plan-your-visit-3">
		<?php $google_maps_api_key = 'AIzaSyCh9fjCJw8vxVBIvC6_IAtMQ050t4iYxjg'; ?>
		<iframe class="plan-your-visit-map" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=Newport%20Art%20Museum&key=AIzaSyCh9fjCJw8vxVBIvC6_IAtMQ050t4iYxjg" allowfullscreen></iframe>
	</div>		
</div>
</div>
</div>