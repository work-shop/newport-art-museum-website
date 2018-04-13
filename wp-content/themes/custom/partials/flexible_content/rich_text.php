<section class="block flexible-content fc fc-rich-text">
	<?php
	$fc = get_field('page_flexible_content');
	$fc_row = $fc[$GLOBALS['fc_index']]; 
	$section_heading = $fc_row['section_heading'];
	$rich_text = $fc_row['rich_text'];
	?>
	<div class="container-fc">
		<?php if( $section_heading ): ?>
			<div class="row fc-section-heading">
				<div class="col-sm-12">
					<h2 class="serif fc-section-heading-text">
						<?php echo $section_heading; ?>
					</h2>
				</div>
			</div>
		<?php endif; ?>
		<div class="row">
			<div class="col-lg-7 col-md-9">
				<div class="rich-text fc-rich-text-container">
					<?php echo $rich_text; ?>
				</div>
			</div>
		</div>
	</div>

</section>