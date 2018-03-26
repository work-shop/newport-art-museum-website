<?php if( $posts ): ?>
	<div class="container-fluid-broken">
		<div class="col-sm-12-broken">
			<ul>
				<?php foreach( $posts as $post): // variable must be called $post (IMPORTANT) ?>
					<?php setup_postdata($post); ?>
					<li>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</li>
				<?php endforeach; ?>
				<?php if( $links ): ?>
					<?php foreach( $links as $link): ?>
						<li>
							<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>"><?php echo $link['title']; ?></a>
						</li>
					<?php endforeach; ?>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
<?php endif; ?>