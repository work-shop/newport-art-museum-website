<section class="block single-body" id="news-single-body">
	<div class="container-fluid-single container-fluid">
		<div class="row">
			<div class="col-md-8 single-body-left">
				<div class="single-body-left-link">
					<a href="/news">Back To News</a>
				</div>
				<div class="single-body-left-main">
					<div class="news-single-introduction single-introduction">
						<?php if( get_field('news_story_date') ): ?>
							<h4 class="news-single-date medium">
								<?php the_field('news_story_date'); ?><?php if( get_field('news_story_author') ): ?> &nbsp;&nbsp;|&nbsp;&nbsp; By <?php the_field('news_story_author'); ?><?php endif; ?>
							</h4>
						<?php endif; ?>
						<h1 class="serif news-single-title single-title">
							<?php the_title(); ?>
						</h1>
						<div class="row">
							<div class="col">
								<div class="nam-dash">
								</div>
							</div>
						</div>
					</div>
					<div class="single-body-left-content">
						<?php get_template_part('partials/flexible_content/flexible_content'); ?>
					</div>
				</div>
			</div>
			<div class="col-md-4 single-body-right">
				<div class="single-body-right-content">
					<?php get_template_part('partials/mailchimp_form'); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if( get_field('show_hero') ): ?>
	<?php if( is_single() ): ?>
		</div><!--end .single-has-hero-->
	<?php endif; ?>
<?php endif; ?>