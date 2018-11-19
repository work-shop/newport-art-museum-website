<footer id="footer" class="block">
	<div class="footer-message bg-medium">
		<div class="container-fluid">
			<h4 class="white museum-status"></h4>
		</div>
	</div>
	<div class="footer-body bg-khaki pt4 pb6">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-2 col-lg-2 col-3 footer-col">
					<a href="/" id="footer-logo">
						<?php get_template_part('partials/logo'); ?>
					</a>
				</div>
				<div class="col-md-4 col-lg-4 col-9 footer-col">
					<div class="footer-address address">
						<?php get_template_part('partials/visit/address'); ?>
					</div>
					<div class="footer-hours hours hidden-sm hidden-xs">
						<?php get_template_part('partials/visit/hours'); ?>
					</div>
				</div>
				<div class="col-md-6 col-lg-5 offset-lg-1 col-xs-12 footer-col footer-menus">
					<div class="row">
						<div class="col-md-6">
							<?php if( have_rows('footer_menu_links_column_1','option') ): ?>
								<ul>
									<?php while( have_rows('footer_menu_links_column_1','option') ): the_row(); ?>
										<?php $link = get_sub_field('link'); ?>
										<li>
											<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
												<?php echo $link['title']; ?>
											</a>
										</li>
									<?php endwhile; ?>
									<li class="footer-social-items desktop">
										<a href="https://www.facebook.com/NewportArtMuseum/" target="_blank">
											<img src="<?php bloginfo( 'template_directory' );?>/images/facebook.png" class="social-icon">
										</a> 
										<a href="https://twitter.com/NewportArtMuse" target="_blank">
											<img src="<?php bloginfo( 'template_directory' );?>/images/twitter.png" class="social-icon">
										</a>
										<a href="https://www.instagram.com/newportartmuseum/" target="_blank">
											<img src="<?php bloginfo( 'template_directory' );?>/images/instagram.png" class="social-icon">
										</a>
									</li>
								</ul>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<?php if( have_rows('footer_menu_links_column_2','option') ): ?>
								<ul>
									<?php while( have_rows('footer_menu_links_column_2','option') ): the_row(); ?>
										<?php $link = get_sub_field('link'); ?>
										<li>
											<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
												<?php echo $link['title']; ?>
											</a>
										</li>
									<?php endwhile; ?>
									<li class="footer-social-items mobile">
										<a href="https://www.facebook.com/NewportArtMuseum/" target="_blank">
											<img src="<?php bloginfo( 'template_directory' );?>/images/facebook.png" class="social-icon">
										</a> 
										<a href="https://twitter.com/NewportArtMuse" target="_blank">
											<img src="<?php bloginfo( 'template_directory' );?>/images/twitter.png" class="social-icon">
										</a>
										<a href="https://www.instagram.com/newportartmuseum/" target="_blank">
											<img src="<?php bloginfo( 'template_directory' );?>/images/instagram.png" class="social-icon">
										</a>
									</li>
								</ul>
							<?php endif; ?>
						</div>
					</div>
				</div>						
			</div>
			<div class="site-credit">
				<a href="http://workshop.co" target="_blank">
					<h4 class="medium">Site by Work-Shop Design Studio</h4>
				</a>
			</div>
		</div>
	</div>
</footer>