
<?php 
$timezone = 'America/New_York';
$timestamp = time();
$current_date = new DateTime("now", new DateTimeZone($timezone)); 
$current_date->setTimestamp($timestamp); 
$current_day = $current_date->format('l');
$current_time = $current_date->format('g:ia');
$current_time = strtotime($current_time);
$current_calendar_date = $current_date->format('F j, Y');
$museum_status = 'closed';
$holiday = false;
$override = false;

if( have_rows('holidays_settings','23') ): 
	while ( have_rows('holidays_settings','23') ): the_row(); 
		$holiday_date = get_sub_field('holiday_date');
		if( $holiday_date === $current_calendar_date ):
			$holiday = true;
			$museum_status_on_holiday = get_sub_field('status_on_holiday');
			if( $museum_status_on_holiday === 'closed' ):
				$closed_for_holiday = true;
			else:
				$open_for_holiday = true;
				$museum_open = get_sub_field('open');
				$museum_close = get_sub_field('close');
				if( $current_time > strtotime($museum_open) && $current_time < strtotime($museum_close) ):
					$museum_status = 'open';
				else:
					$museum_status = 'closed';
				endif;
			endif;
		endif; 
	endwhile;
endif; 

if( $holiday === false ):
	if( have_rows('hours_overrides','23') ): 
		while ( have_rows('hours_overrides','23') ): the_row(); 
			$override_date = get_sub_field('override_date');
			if( $override_date === $current_calendar_date ):
				$override = true;
				$museum_open = get_sub_field('open');
				$museum_close = get_sub_field('close'); 			
				if( $current_time > strtotime($museum_open) && $current_time < strtotime($museum_close) ):
					$museum_status = 'open';
				else:
					$museum_status = 'closed';
				endif;
			endif;
		endwhile;
	endif;
	if ( $override === false ):
		if( have_rows('hours_settings','23') ): 
			while ( have_rows('hours_settings','23') ): the_row(); 
				$museum_day = get_sub_field('days');
				if ( $museum_day === $current_day ): 		
					if( $museum_day_status !== 'closed' ):
						$museum_open = get_sub_field('open');
						$museum_close = get_sub_field('close'); 		
						if( $current_time > strtotime($museum_open) && $current_time < strtotime($museum_close) ):
							$museum_status = 'open';
						else:
							$museum_status = 'closed';
						endif;
					else:
						$museum_status = 'closed';
					endif;
				endif;
			endwhile;
		endif;
	endif;
endif; 
?>

The museum is currently <span class="ms-status ms-status-<?php echo $museum_status; ?>"><?php echo $museum_status; ?><?php if ( $closed_for_holiday ): ?> for a holiday<?php endif; ?>.</span>
<?php if( $museum_status === 'open' ): ?>
	<br>
	Our <?php if( $override ): echo '<i>special</i> '; endif; ?> hours today are <span class="ms-open"><?php echo $museum_open; ?></span> to <span class="ms-close"><?php echo $museum_close; ?></span>.
<?php endif; ?>
