<section class="block" id="contact-introduction">
	<div class="row row-full">
		<div class="col-md-6 bg-brand contact-intro-address">
			<div class="row">
				<div class="col-md-6 mb2">
					<?php get_template_part('partials/visit/address'); ?>
					<a href="https://goo.gl/maps/Cg9iTD1eg3q" class="button button-white-bordered contact-directions-link">
						Get Directions
					</a>
				</div>
				<div class="col-md-6">
					<?php get_template_part('partials/visit/address_school'); ?>
					<a href="https://www.google.com/maps/dir//26+Liberty+Street,+Newport,+RI/@41.4851435,-71.342355,13z/data=!3m1!4b1!4m9!4m8!1m0!1m5!1m1!1s0x89e5af433c512e67:0x34dde6fcc2e834ff!2m2!1d-71.3073356!2d41.4850845!3e0" class="button button-white-bordered contact-directions-link">
						Get Directions
					</a>
				</div>
			</div>
		</div>
		<div class="col-md-6  contact-map">
			<?php $google_maps_api_key = 'AIzaSyCh9fjCJw8vxVBIvC6_IAtMQ050t4iYxjg'; ?>
			<iframe class="" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=Newport%20Art%20Museum&key=AIzaSyCh9fjCJw8vxVBIvC6_IAtMQ050t4iYxjg" allowfullscreen></iframe>
		</div>

	</div>
</section>