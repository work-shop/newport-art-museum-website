
<?php
$sitewide_alert_on = get_field('show_sitewide_alert', 'option');
if( $sitewide_alert_on === true ):
	if( !isset($_COOKIE['nam_show_sitewide_alert']) || $_COOKIE['nam_show_sitewide_alert'] === false ):
		$sitewide_alert_class = 'sitewide-alert-on';
		$show_sitewide_alert = true;
	endif;
endif;
?>