<?php $links = $GLOBALS['links']; ?>
<?php $links_additional = $GLOBALS['links_additional']; ?>
<?php if( $links ): ?>
	<div class="container-fluid">
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
				<ul>
					<?php foreach( $links as $post): // variable must be called $post (IMPORTANT) ?>
						<?php setup_postdata($post); ?>
						<li>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</li>
					<?php endforeach; ?>
					<?php if( $links_additional ): ?>
						<?php foreach( $links_additional as $link): ?>
							<li>
								<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a>
							</li>
						<?php endforeach; ?>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</div>
	<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
<?php endif; ?>