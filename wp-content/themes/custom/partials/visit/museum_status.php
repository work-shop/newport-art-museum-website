
<?php 
$timezone = 'America/New_York';
$timestamp = time();
$current_date = new DateTime("now", new DateTimeZone($timezone)); 
$current_date->setTimestamp($timestamp); 
$current_day = $current_date->format('l');
$current_time = $current_date->format('g:ia');
$museum_status = 'closed';
?>

<?php if( have_rows('hours_settings','23') ): 
	while ( have_rows('hours_settings','23') ): the_row(); 
		$museum_day = get_sub_field('days');
		if ( $museum_day === $current_day ): 		
			if( $museum_day_status !== 'closed' ):
				$museum_open = get_sub_field('open');
				$museum_close = get_sub_field('close'); 			
				if ( $current_time > $museum_open && $current_time < $museum_close ):
   					$museum_status = 'open';
   				else:
   					$museum_status = 'closed';
   				endif;
			else:
				$museum_status = 'closed';
			endif;
		endif;
	endwhile;
endif; ?>

The museum is currently <span class="ms-status"><?php echo $museum_status; ?></span>. 
<br>
Our hours today are <span class="ms-open"><?php echo $museum_open; ?> to <span class="ms-close"><?php echo $museum_close; ?>.