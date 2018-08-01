
<nav id="nav" class="fixed before closed">
	<div id="logo" class="logo">
		<a href="/" title="Home">
			<?php get_template_part('partials/logo'); ?>
			<?php get_template_part('partials/logo_small'); ?>
		</a>
	</div>
	<div id="nav-menus">
		<div id="nav-menu-upper">
			<ul>
				<li id="nav-link-shop" class="hidden"><a href="/shop">Shop</a></li>

				<li id="nav-link-login">
					<a href="/my-account">
						<?php
						if( is_user_logged_in() ) {
							//$user = wp_get_current_user();
							//$user_name = $user->display_name;
							//echo $user_name;
							echo 'My Account';
						} else{
							echo 'Login';
						}
						?>
					</a>
				</li>
				<li id="nav-link-cart">
					<a class="cart-customlocation" title="View Your Shopping Cart" href="<?php echo wc_get_cart_url(); ?>">
						<span class="icon" data-icon="i"></span>
						<span id="cart-number"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
					</a>
				</li>			
			</ul>
		</div>
		<div id="nav-menu-primary">
			<ul>
				<li class="sub-menu-closed has-sub-menu ">
					<a href="/visit" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'visit' ): echo ' nav-current '; endif; ?>" id="nav-link-visit" data-dropdown-target="visit">Visit</a>
					<ul class="sub-menu">
						<?php $links = get_field('visit_menu_links','option'); ?>
						<?php $links_additional = get_field('visit_menu_additional_links','option'); ?>
						<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
							<?php setup_postdata($post); ?>
							<li>
								<a href="<?php the_permalink(); ?>" class="<?php if ( $GLOBALS['page_nav'] ): echo 'page-nav-link'; endif; ?>"><?php the_title(); ?></a>
							</li>
						<?php endforeach; ?>
						<?php if( $links_additional ): ?>
							<?php foreach( $links_additional as $link): ?>
								<li>
									<a href="<?php echo $link['link']['url']; ?>/" target="<?php echo $link['link']['target']; ?>" class="page-nav-link">
										<?php echo $link['link']['title']; ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					</ul>
				</li>
				<li class="sub-menu-closed has-sub-menu ">
					<a href="/about" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'about' ): echo ' nav-current '; endif; ?>" id="nav-link-about" data-dropdown-target="about">About</a>
					<ul class="sub-menu">
						<?php $links = get_field('about_menu_links','option'); ?>
						<?php $links_additional = get_field('about_menu_additional_links','option'); ?>
						<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
							<?php setup_postdata($post); ?>
							<li>
								<a href="<?php the_permalink(); ?>" class="<?php if ( $GLOBALS['page_nav'] ): echo 'page-nav-link'; endif; ?>"><?php the_title(); ?></a>
							</li>
						<?php endforeach; ?>
						<?php if( $links_additional ): ?>
							<?php foreach( $links_additional as $link): ?>
								<li>
									<a href="<?php echo $link['link']['url']; ?>/" target="<?php echo $link['link']['target']; ?>" class="page-nav-link">
										<?php echo $link['link']['title']; ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					</ul>
				</li>
				<li class="sub-menu-closed has-sub-menu ">
					<a href="/exhibitions" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'exhibitions' ): echo ' nav-current '; endif; ?>" id="nav-link-exhibitions" data-dropdown-target="exhibitions">Exhibitions</a>
					<ul class="sub-menu">
						<?php $links = get_field('exhibitions_menu_links','option'); ?>
						<?php $links_additional = get_field('exhibitions_menu_additional_links','option'); ?>
						<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
							<?php setup_postdata($post); ?>
							<li>
								<a href="<?php the_permalink(); ?>" class="<?php if ( $GLOBALS['page_nav'] ): echo 'page-nav-link'; endif; ?>"><?php the_title(); ?></a>
							</li>
						<?php endforeach; ?>
						<?php if( $links_additional ): ?>
							<?php foreach( $links_additional as $link): ?>
								<li>
									<a href="<?php echo $link['link']['url']; ?>/" target="<?php echo $link['link']['target']; ?>" class="page-nav-link">
										<?php echo $link['link']['title']; ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					</ul>
				</li>
				<li class="sub-menu-closed has-sub-menu ">
					<a href="/collection" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'collection' ): echo ' nav-current '; endif; ?>" id="nav-link-collection" data-dropdown-target="collection">Collection</a>
					<ul class="sub-menu">
						<?php $links = get_field('collection_menu_links','option'); ?>
						<?php $links_additional = get_field('collection_menu_additional_links','option'); ?>
						<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
							<?php setup_postdata($post); ?>
							<li>
								<a href="<?php the_permalink(); ?>" class="<?php if ( $GLOBALS['page_nav'] ): echo 'page-nav-link'; endif; ?>"><?php the_title(); ?></a>
							</li>
						<?php endforeach; ?>
						<?php if( $links_additional ): ?>
							<?php foreach( $links_additional as $link): ?>
								<li>
									<a href="<?php echo $link['link']['url']; ?>/" target="<?php echo $link['link']['target']; ?>" class="page-nav-link">
										<?php echo $link['link']['title']; ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					</ul>
				</li>
				<li class="sub-menu-closed has-sub-menu ">
					<a href="/events" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'events' ): echo ' nav-current '; endif; ?>" id="nav-link-events" data-dropdown-target="events">Events</a>
					<ul class="sub-menu">
						<?php $links = get_field('events_menu_links','option'); ?>
						<?php $links_additional = get_field('events_menu_additional_links','option'); ?>
						<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
							<?php setup_postdata($post); ?>
							<li>
								<a href="<?php the_permalink(); ?>" class="<?php if ( $GLOBALS['page_nav'] ): echo 'page-nav-link'; endif; ?>"><?php the_title(); ?></a>
							</li>
						<?php endforeach; ?>
						<?php if( $links_additional ): ?>
							<?php foreach( $links_additional as $link): ?>
								<li>
									<a href="<?php echo $link['link']['url']; ?>/" target="<?php echo $link['link']['target']; ?>" class="page-nav-link">
										<?php echo $link['link']['title']; ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					</ul>
				</li>
				<li class="sub-menu-closed has-sub-menu ">
					<a href="/education" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'education' ): echo ' nav-current '; endif; ?>" id="nav-link-education" data-dropdown-target="education">Education</a>
					<ul class="sub-menu">
						<?php $links = get_field('education_menu_links','option'); ?>
						<?php $links_additional = get_field('education_menu_additional_links','option'); ?>
						<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
							<?php setup_postdata($post); ?>
							<li>
								<a href="<?php the_permalink(); ?>" class="<?php if ( $GLOBALS['page_nav'] ): echo 'page-nav-link'; endif; ?>"><?php the_title(); ?></a>
							</li>
						<?php endforeach; ?>
						<?php if( $links_additional ): ?>
							<?php foreach( $links_additional as $link): ?>
								<li>
									<a href="<?php echo $link['link']['url']; ?>/" target="<?php echo $link['link']['target']; ?>" class="page-nav-link">
										<?php echo $link['link']['title']; ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					</ul>
				</li>
				<li class="sub-menu-closed has-sub-menu ">
					<a href="/join" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'join' ): echo ' nav-current '; endif; ?>" id="nav-link-join" data-dropdown-target="join">Join</a>
					<ul class="sub-menu">
						<?php $links = get_field('join_menu_links','option'); ?>
						<?php $links_additional = get_field('join_menu_additional_links','option'); ?>
						<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
							<?php setup_postdata($post); ?>
							<li>
								<a href="<?php the_permalink(); ?>" class="<?php if ( $GLOBALS['page_nav'] ): echo 'page-nav-link'; endif; ?>"><?php the_title(); ?></a>
							</li>
						<?php endforeach; ?>
						<?php if( $links_additional ): ?>
							<?php foreach( $links_additional as $link): ?>
								<li>
									<a href="<?php echo $link['link']['url']; ?>/" target="<?php echo $link['link']['target']; ?>" class="page-nav-link">
										<?php echo $link['link']['title']; ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					</ul>
				</li>
				<li class="sub-menu-closed has-sub-menu ">
					<a href="/support" class="dropdown-link <?php if( $GLOBALS['tree_slug'] === 'support' ): echo ' nav-current '; endif; ?>" id="nav-link-support" data-dropdown-target="support">Support</a>
					<ul class="sub-menu">
						<?php $links = get_field('support_menu_links','option'); ?>
						<?php $links_additional = get_field('support_menu_additional_links','option'); ?>
						<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
							<?php setup_postdata($post); ?>
							<li>
								<a href="<?php the_permalink(); ?>" class="<?php if ( $GLOBALS['page_nav'] ): echo 'page-nav-link'; endif; ?>"><?php the_title(); ?></a>
							</li>
						<?php endforeach; ?>
						<?php if( $links_additional ): ?>
							<?php foreach( $links_additional as $link): ?>
								<li>
									<a href="<?php echo $link['link']['url']; ?>/" target="<?php echo $link['link']['target']; ?>" class="page-nav-link">
										<?php echo $link['link']['title']; ?>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>
<div class="hamburger menu-toggle">
	<span class="hamburger-line hl-1"></span>
	<span class="hamburger-line hl-2"></span>
</div>

