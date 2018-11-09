<div id="blanket" class="modal-close">
</div>

<div class="modal modal-small-broken modal-medium bg-khaki off scroll" id="modal-member-price-info">
	<div class="modal-inner">
		<div class="row">
			<div class="col-10">
				<h3 class="bold mb2">
					<?php the_field('membership_discount_popup_heading','option'); ?>
				</h3>
			</div>
			<div class="col-2 d-flex justify-content-end">
				<a href="#" class="modal-close righted"><span class="icon" data-icon="x"></span></a>
			</div>
		</div>
		<?php // login_with_ajax(); ?>
		<p class="mb2">
			<?php the_field('membership_discount_popup_intro','option'); ?>
		</p>
		<?php if( have_rows('membership_discount_popup_explanation_content','option') ): ?>
			<?php  while ( have_rows('membership_discount_popup_explanation_content','option') ) : the_row(); ?>
				<div class="membership-discount-explaination-row mb2">
					<?php if( get_sub_field('heading') || get_sub_field('instructions') ): ?>
					<p class="mb0">
						<?php if( get_sub_field('heading') ): ?>
							<b><?php the_sub_field('heading'); ?></b>
						<?php endif; ?>
						<?php if( get_sub_field('instructions') ): ?>
							<br>
							<?php the_sub_field('instructions'); ?>
						<?php endif; ?>
					</p>
				<?php endif; ?>
				<?php $link = get_sub_field('link'); ?>
				<?php if( $link['url'] && $link['title']): ?>
					<a href="<?php echo $link['url']; ?>" target="<?php echo $link['target']; ?>" class="button button-bordered button-small mt1">
						<?php echo $link['title']; ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endwhile; ?>
	<?php endif; ?>
</div>
</div>