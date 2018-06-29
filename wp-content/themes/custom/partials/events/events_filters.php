<section class="block filters events-filters padded" id="events-filters">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<div class="col-md-9 col-lg-8 col-xl-7 filter-category mb2" id="events-filters-category">
				<div class="row">
					<div class="col">
						<h4 class="medium filter-title">
							Filter by Program Category:
						</h4>
					</div>
				</div>
				<div class="row filter-content-row">
					<div class="col">
						<?php
						$terms = get_terms( array(
							'taxonomy' => 'events-categories',
							'orderby' => 'name',
							'order' => 'ASC',
							'hide_empty' => false,
						) ); 
						?>
						<?php if( $terms ): //var_dump($terms) ?>
							<div class="filter-categories " id="filter-buttons">
								<?php foreach ($terms as $term): ?>
									<button class="filter-button" data-target="<?php echo $term->slug; ?>">
										<?php echo $term->name; ?>
									</button>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-lg-4 col-xl-5 filter-date" id="filter-dates">
				<div class="row">
					<div class="col-xl-6">
						<h4 class="medium filter-title">
							Starting from:
						</h4>
						<div class="filters-date-start mb2" id="events-filters-date-start">
							<input type="text" class="filter-date-input filter-date-start" />
						</div>
					</div>
					<div class="col-xl-6">
						<h4 class="medium filter-title">
							Until:
						</h4>
						<div class="filters-date-end mb2" id="events-filters-date-end">
							<input type="text" class="filter-date-input filter-date-end" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="filter-messages">
			<div class="col">
				<div class="bg-error filter-message">
					<h4 class="filter-messages-text error centered">
						Sorry, we couldn't find any events that match your selection.
					</h4>
				</div>
			</div>
		</div>
	</div>
</section>