
<nav id="nav" class="fixed">
	<div class="container-fluid">
		<div class="row">
			<div class="logo col-sm-3 col-xs-6">
				<a href="/">
					<?php get_template_part('partials/logo'); ?>
				</a>
			</div>
			<div class="col-sm-9 col-xs-6" id="nav-menu">
				<?php wp_nav_menu(); ?>
			</div>

		</div>
	</div>
</nav>
<div id="mobile-nav">
	<ul class="mobile-nav-items">
		<?php wp_nav_menu(); ?>
	</ul>
</div>
<div class="hamburger menu-toggle">
	<span class="hamburger-line hl-1"></span>
	<span class="hamburger-line hl-2"></span>
	<span class="hamburger-line hl-3"></span>
</div>

