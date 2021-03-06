<section class="block filters filters-<?php echo $GLOBALS['tree_slug']; ?> padded" id="filters">
	<div class="container-fluid container-fluid-stretch">
		<div class="row-broken clearfix">
			<div class="filter-primary filter-category mb2" id="filters-primary">
				<div class="row">
					<div class="col">
						<h4 class="medium filter-title">
							Filter by Category:
						</h4>
					</div>
				</div>
				<div class="row filter-content-row">
					<div class="col">
						<?php if( $GLOBALS['tree_slug'] === 'events' ):
							$type = 'events';
							$id = '74';
							$field1 = 'events_filtering_menu';
							$field2 = 'event_category';
						elseif( $GLOBALS['tree_slug'] === 'education'):
							$type = 'classes';
							$id = '78';
							$field1 = 'classes_filtering_menu';
							$field2 = 'class_category';
						endif;
						?>
						<?php if( have_rows($field1, $id) ): ?>
							<div class="filter-categories " id="filter-buttons">
								<?php  while ( have_rows($field1, $id) ) : the_row(); ?>
									<?php $term = get_sub_field($field2); ?>
									<button class="filter-button filter-button-category" data-target="filter-<?php echo $term->slug; ?>">
										<?php echo $term->name; ?>
									</button>
								<?php endwhile; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="filter-secondary mb2" id="filter-secondary">
				<?php if( $type === 'events' ): ?>
					<div class="row filter-dates">
						<div class="col col-sm-6">
							<div class="">
								<div>
							<h4 class="medium filter-title">
								Starting:
							</h4>
							<div class="filters-date-start mb2" id="filters-date-start">
								<input type="text" placeholder="Start Date" class="filter-date-input filter-date-start" />
							</div>
						</div>
						</div>
						</div>
						<div class="col col-sm-6">
							<div class="">
								<div>
							<h4 class="medium filter-title">
								Until:
							</h4>
							<div class="filters-date-end mb2" id="filters-date-end">
								<input type="text" placeholder="End Date" class="filter-date-input filter-date-end" />
							</div>
						</div>
						</div>
						</div>
					</div>
					<?php elseif( $type === 'classes'): ?>
						<div class="row filter-day">
							<div class="col">
								<h4 class="medium filter-title">
									Filter by Day:
								</h4>
							</div>
						</div>
						<div class="row">
							<div class="col">
								<div class="filter-days">
									<button class="filter-button filter-button-day" data-target="filter-monday">
										Mon											
									</button>
									<button class="filter-button filter-button-day" data-target="filter-tuesday">
										Tue											
									</button>
									<button class="filter-button filter-button-day" data-target="filter-wednesday">
										Wed											
									</button>
									<button class="filter-button filter-button-day" data-target="filter-thursday">
										Thu											
									</button>
									<button class="filter-button filter-button-day" data-target="filter-friday">
										Fri											
									</button>
									<button class="filter-button filter-button-day" data-target="filter-saturday">
										Sat											
									</button>
									<button class="filter-button filter-button-day" data-target="filter-sunday">
										Sun											
									</button>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div class="row" id="filter-messages">
				<div class="col">
					<div class="bg-error filter-message">
						<h4 class="filter-messages-text error centered">
							Sorry, we couldn't find any <?php echo $type; ?> that match your selection.
						</h4>
					</div>
				</div>
			</div>
		</div>
	</section>