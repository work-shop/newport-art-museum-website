<footer id="footer" class="block">
	<div class="footer-message bg-medium">
		<div class="container-fluid">
			<h4 class="white"><?php get_template_part('partials/visit/museum_status'); ?></h4>
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
								<?php while( have_rows('footer_menu_links_column_1','option') ): the_row(); ?>
									<?php $link = get_sub_field('link'); ?>
									<ul>
										<li>
											<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
												<?php echo $link['title']; ?>
											</a>
										</li>
									</ul>
								<?php endwhile; ?>
							<?php endif; ?>
						</div>
						<div class="col-md-6">
							<?php if( have_rows('footer_menu_links_column_2','option') ): ?>
								<?php while( have_rows('footer_menu_links_column_2','option') ): the_row(); ?>
									<?php $link = get_sub_field('link'); ?>
									<ul>
										<li>
											<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>">
												<?php echo $link['title']; ?>
											</a>
										</li>
									</ul>
								<?php endwhile; ?>
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