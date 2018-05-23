
<nav id="nav" class="fixed before">
	<div id="logo" class="logo">
		<a href="/" title="Home">
			<?php get_template_part('partials/logo'); ?>
		</a>
	</div>
	<div id="nav-menus">
		<div id="nav-menu-upper">
			<ul>
				<li id="nav-link-shop"><a href="/shop">Shop</a></li>
				<li id="nav-link-login"><a href="/login">Login</a></li>
				<li id="nav-link-cart">
					<a class="cart-customlocation" title="View Your Shopping Cart" href="<?php echo wc_get_cart_url(); ?>">
						<span class="icon" data-icon="i"></span>
						<span id="cart-number"><?php echo WC()->cart->get_cart_contents_count(); ?>
					</a>
				</li>			
				</ul>
		</div>
		<div id="nav-menu-primary">
			<ul>
				<li><a href="/visit" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'visit' ): echo ' nav-current '; endif; ?>" id="nav-link-visit" data-dropdown-target="visit">Visit</a></li>
				<li><a href="/about" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'about' ): echo ' nav-current '; endif; ?>" id="nav-link-about" data-dropdown-target="about">About</a></li>
				<li><a href="/exhibitions" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'exhibitions' ): echo ' nav-current '; endif; ?>" id="nav-link-exhibitions" data-dropdown-target="exhibitions">Exhibitions</a></li>
				<li><a href="/collection" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'collection' ): echo ' nav-current '; endif; ?>" id="nav-link-collection" data-dropdown-target="collection">Collection</a></li>
				<li><a href="/events" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'events' ): echo ' nav-current '; endif; ?>" id="nav-link-events" data-dropdown-target="events">Events</a></li>
				<li><a href="/education" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'education' ): echo ' nav-current '; endif; ?>" id="nav-link-education" data-dropdown-target="education">Education</a></li>
				<li><a href="/join" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'join' ): echo ' nav-current '; endif; ?>" id="nav-link-join" data-dropdown-target="join">Join</a></li>
				<li><a href="/support" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'support' ): echo ' nav-current '; endif; ?>" id="nav-link-support" data-dropdown-target="support">Support</a></li>
			</ul>
		</div>
	</div>
</nav>
<nav id="mobile-nav">
	<ul class="mobile-nav-items">
		<?php wp_nav_menu(); ?>
	</ul>
</nav>
<div class="hamburger menu-toggle">
	<span class="hamburger-line hl-1"></span>
	<span class="hamburger-line hl-2"></span>
	<span class="hamburger-line hl-3"></span>
</div>

