<?php

/** Theme-specific global constants for NAM */
define( '__ROOT__', dirname( __FILE__ ) );

require_once( __ROOT__ . '/functions/class-nam-abstract-taxonomy.php' );
require_once( __ROOT__ . '/functions/class-nam-abstract-custom-post-type.php' );
require_once( __ROOT__ . '/functions/class-nam-abstract-shadowed-post-type.php' );

require_once( __ROOT__ . '/functions/library/class-ws-cdn-url.php');

require_once( __ROOT__ . '/functions/post-types/class-post-types-archive-mapping.php');

require_once( __ROOT__ . '/functions/taxonomies/groups/class-nam-group.php');
require_once( __ROOT__ . '/functions/taxonomies/classes-categories/class-nam-classes-category.php');
require_once( __ROOT__ . '/functions/taxonomies/classes-categories/class-nam-classes-days.php');
require_once( __ROOT__ . '/functions/taxonomies/events-categories/class-nam-events-category.php');
require_once( __ROOT__ . '/functions/taxonomies/news-categories/class-nam-news-category.php');
require_once( __ROOT__ . '/functions/taxonomies/exhibitions-categories/class-nam-exhibitions-category.php');

require_once( __ROOT__ . '/functions/post-types/shop-products/class-nam-shop-product.php');
require_once( __ROOT__ . '/functions/post-types/donation-tiers/class-nam-donation-tier.php');
require_once( __ROOT__ . '/functions/post-types/membership-tiers/class-nam-membership-tier.php');
require_once( __ROOT__ . '/functions/post-types/exhibitions/class-nam-exhibition.php');
require_once( __ROOT__ . '/functions/post-types/classes/class-nam-class.php');
require_once( __ROOT__ . '/functions/post-types/events/class-nam-event.php');
require_once( __ROOT__ . '/functions/post-types/news/class-nam-news.php');
require_once( __ROOT__ . '/functions/post-types/fees/class-nam-fee.php');

require_once( __ROOT__ . '/functions/class-nam-membership.php' );
require_once( __ROOT__ . '/functions/class-nam-classes.php' );
require_once( __ROOT__ . '/functions/class-nam-site-admin.php' );
require_once( __ROOT__ . '/functions/class-nam-site-init.php' );
require_once( __ROOT__ . '/functions/class-nam-membership-creator.php' );

require_once( __ROOT__ . '/functions/library/class-nam-helpers.php' );
require_once( __ROOT__ . '/functions/library/class-ws-flexible-content.php' );

new NAM_Site();
new NAM_Site_Admin();

// if ( ! function_exists('rid_remove_jqmigrate_console_log') ) {
// 	function rid_remove_jqmigrate_console_log( $scripts ) {
// 		if ( ! empty( $scripts->registered['jquery'] ) ) {
// 			$scripts->registered['jquery']->deps = array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) );
// 		}
// 	}
// 	add_action( 'wp_default_scripts', 'rid_remove_jqmigrate_console_log' );
// }

function museum_status( $data ){

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

ob_start(); ?>

The museum is currently <span class="ms-status ms-status-<?php echo $museum_status; ?>"><?php echo $museum_status; ?><?php if ( $closed_for_holiday ): ?> for a holiday<?php endif; ?>.</span>
<?php if( $museum_status === 'open' ): ?>
	<br>
	Our <?php if( $override ): echo '<i>special</i> '; endif; ?> hours today are <span class="ms-open"><?php echo $museum_open; ?></span> to <span class="ms-close"><?php echo $museum_close; ?></span>.<?php endif; ?><?php 
	$status = ob_get_clean();
	//ob_end_flush();

	return $status;

}


add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_action( 'rest_api_init', function () {
	register_rest_route( 'museum-status/v1', '/status', array(
		'methods' => WP_REST_Server::ALLMETHODS,
		'callback' => 'museum_status',
	) );
} );

$val = get_option( 'wp_user_roles' );
$val['subscriber']['name'] = 'Member';
update_option( 'wp_user_roles', $val );

add_filter( 'wc_order_statuses', 'rename_order_statuses', 20, 1 );
function rename_order_statuses( $order_statuses ) {
    $order_statuses['wc-processing'] = _x( 'Paid', 'Order status', 'woocommerce' );

    return $order_statuses;
}

add_filter( 'bulk_actions-edit-shop_order', 'custom_dropdown_bulk_actions_shop_order', 20, 1 );
function custom_dropdown_bulk_actions_shop_order( $actions ) {
    $actions['mark_processing'] = __( 'Mark Paid', 'woocommerce' );

    return $actions;
}

foreach( array( 'post', 'shop_order' ) as $hook )
    add_filter( "views_edit-$hook", 'shop_order_modified_views' );

function shop_order_modified_views( $views ){

    if( isset( $views['wc-processing'] ) )
        $views['wc-processing'] = str_replace( 'Processing', __( 'Paid', 'woocommerce'), $views['wc-processing'] );

    return $views;
}

add_filter( 'woocommerce_helper_suppress_admin_notices', '__return_true' );



?>
