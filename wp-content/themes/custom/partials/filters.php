<section class="block filters filters-<?php echo $GLOBALS['tree_slug']; ?> padded" id="filters">
	<div class="container-fluid container-fluid-stretch">
		<div class="row">
			<div class="col-md-9 col-lg-8 col-xl-7 filter-category mb2" id="filters-primary">
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
							$taxonomy = 'events-categories';
							$type = 'events';
						elseif( $GLOBALS['tree_slug'] === 'education'):
							$taxonomy = 'classes-categories';
							$type = 'classes';
						endif;
						$terms = get_terms( array(
							'taxonomy' => $taxonomy,
							'orderby' => 'name',
							'order' => 'ASC',
							'hide_empty' => false,
						) ); 
						?>
						<?php if( $terms ): ?>
							<div class="filter-categories " id="filter-buttons">
								<?php foreach ($terms as $term): ?>
									<button class="filter-button filter-button-category" data-target="<?php echo $term->slug; ?>">
										<?php echo $term->name; ?>
									</button>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="col-md-3 col-lg-4 col-xl-5 mb2" id="filter-secondary">
				<?php if( $type === 'events' ): ?>
					<div class="row filter-dates">
						<div class="col-xl-6">
							<h4 class="medium filter-title">
								Starting:
							</h4>
							<div class="filters-date-start mb2" id="filters-date-start">
								<input type="text" placeholder="Start Date" class="filter-date-input filter-date-start" />
							</div>
						</div>
						<div class="col-xl-6">
							<h4 class="medium filter-title">
								Until:
							</h4>
							<div class="filters-date-end mb2" id="filters-date-end">
								<input type="text" placeholder="End Date" class="filter-date-input filter-date-end" />
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
									<button class="filter-button filter-button-day" data-target="monday">
										Mon											
									</button>
									<button class="filter-button filter-button-day" data-target="tuesday">
										Tue											
									</button>
									<button class="filter-button filter-button-day" data-target="wednesday">
										Wed											
									</button>
									<button class="filter-button filter-button-day" data-target="thursday">
										Thu											
									</button>
									<button class="filter-button filter-button-day" data-target="friday">
										Fri											
									</button>
									<button class="filter-button filter-button-day" data-target="saturday">
										Sat											
									</button>
									<button class="filter-button filter-button-day" data-target="sunday">
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