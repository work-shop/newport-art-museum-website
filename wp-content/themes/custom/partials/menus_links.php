<?php $links = $GLOBALS['links']; ?>
<?php $links_additional = $GLOBALS['links_additional']; ?>
<?php if( $links ): ?>
	<div class="container-fluid menu-links-container">
		<?php if ( $GLOBALS['page_nav'] ): ?>
			<div class="row">
				<div class="col-sm-12">
					<h2 class="serif page-nav-title">
						<?php echo $GLOBALS['page_title'] ?>
					</h2>
				</div>
			</div>
		<?php endif; ?>
		<div class="row">
			<div class="col-sm-12">
				<ul class="menu-links-list">
					<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
						<?php setup_postdata($post); ?>
						<li>
							<a href="<?php the_permalink(); ?>" class="<?php if ( $GLOBALS['page_nav'] ): echo 'page-nav-link'; endif; ?>"><?php the_title(); ?></a>
						</li>
					<?php endforeach; ?>
					<?php if( $links_additional ): ?>
						<?php foreach( $links_additional as $link): ?>
                            <?php if ( !empty( $link['link'] ) ) : ?>
    							<li>
                                    <?php //var_dump( $link ); ?>
    								<a href="<?php echo $link['link']['url']; ?>/" target="<?php echo $link['link']['target']; ?>" class="page-nav-link">
                                    	<?php echo $link['link']['title']; ?>
    								</a>
    							</li>
                            <?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
					<?php if( $GLOBALS['tree_slug'] === 'my_account' && is_user_logged_in() && $GLOBALS['page_nav']  ): ?>
						<li>
							<a href="<?php echo wp_logout_url('/my-account') ?>" class="page-nav-link">
								Logout
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
	<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
	<?php endif; ?>
