<?php
/**
 * WP Desk Tracker
 *
 * @class        WPDESK_Tracker
 * @version        1.3.2
 * @package        WPDESK/Helper
 * @category    Class
 * @author        WP Desk
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPDesk_Tracker_Data_Provider_Orders_Country' ) ) {

	/**
	 * Class WPDesk_Tracker_Data_Provider_Orders_Country
	 */
	class WPDesk_Tracker_Data_Provider_Orders_Country implements WPDesk_Tracker_Data_Provider {

		/**
		 * Info about shipping coutry per order.
		 *
		 * @return array Data provided to tracker.
		 */
		public function get_data() {
			global $wpdb;
			$query                              = $wpdb->get_results( "
            	SELECT m.meta_value AS shipping_country, p.post_status AS post_status , COUNT(p.ID) AS orders
            	FROM {$wpdb->postmeta} m, {$wpdb->posts} p
            	WHERE p.ID = m.post_id
            	AND m.meta_key = '_shipping_country'
            	GROUP BY shipping_country, post_status ORDER BY orders DESC"
			);
			$data['shipping_country_per_order'] = array();
			if ( $query ) {
				foreach ( $query as $row ) {
					if ( ! isset( $data['shipping_country_per_order'][ $row->shipping_country ] ) ) {
						$data['shipping_country_per_order'][ $row->shipping_country ] = array();
					}
					$data['shipping_country_per_order'][ $row->shipping_country ][ $row->post_status ] = $row->orders;
				}
			}

			return $data;
		}

	}

}

