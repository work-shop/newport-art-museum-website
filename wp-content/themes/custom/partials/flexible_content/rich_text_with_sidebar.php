<?php
$fc = get_field('page_flexible_content');
$fc_row = $fc[$GLOBALS['fc_index']]; 
$section_heading = $fc_row['section_heading'];
$rich_text = $fc_row['rich_text'];
$sidebar_heading = $fc_row['sidebar_heading'];
$sidebar_text = $fc_row['sidebar_text'];
$sidebar_button = $fc_row['sidebar_button'];
?>
<section class="block flexible-content fc fc-rich-text fc-rich-text-with-sidebar">
	<div class="container-fc">
		<?php if( $section_heading ): ?>
			<div class="row fc-section-heading fc-row-primary">
				<div class="col-sm-12 fc-col-primary">
					<h2 class="serif fc-section-heading-text">
						<?php echo $section_heading; ?>
					</h2>
				</div>
			</div>
		<?php endif; ?>
		<div class="row fc-row-primary">
			<div class="col-xl-8 col-lg-9 col-md-9 col-sm-12 fc-col-primary fc-rich-text-with-sidebar-main mb3">
				<div class="rich-text fc-rich-text-container wysiwyg">
					<?php echo $rich_text; ?>
				</div>
			</div>
			<div class="col-xl-4 col-lg-3 col-md-3 col-sm-12 fc-rich-text-with-sidebar-sidebar">
				<?php if( get_field('page_sidebar_heading') || get_field('page_sidebar_text') ): ?>
				<div class="sidebar">
					<div class="sidebar-inner">
						<div class="fc-sidebar-content sidebar-content sidebar-box">
							<?php if($sidebar_heading){ ?>
								<h4 class="bold sidebar-heading">
									<?php echo $sidebar_heading; ?>
								</h4>
							<?php endif; ?>
							<?php if($sidebar_text){ ?>
								<h4 class="sidebar-text">
									<?php echo $sidebar_text; ?>
								</h4>
							<?php endif; ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<?php if( $sidebar_button ): ?>
				<div class="link-container">
					<a href="<?php echo $sidebar_button['url']; ?>" target="<?php echo $sidebar_button['target']; ?>" class="button">
						<?php echo $sidebar_button['title']; ?>
					</a>
				</div>	
			<?php endif; ?>
		</div>
	</div>
</div>
</section>