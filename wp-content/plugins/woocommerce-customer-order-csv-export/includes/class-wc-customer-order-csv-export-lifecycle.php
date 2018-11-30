<?php
/**
 * WooCommerce Customer/Order CSV Export
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order CSV Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order CSV Export for your
 * needs please refer to http://docs.woocommerce.com/document/ordercustomer-csv-exporter/
 *
 * @package     WC-Customer-Order-CSV-Export
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2018, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order CSV Export Lifecycle Class
 *
 * Static class that handles installation and upgrades
 *
 * @since 4.5.0
 */
class WC_Customer_Order_CSV_Export_Lifecycle {


	/**
	 * Runs install scripts.
	 *
	 * @since 4.5.0
	 */
	public static function install() {

		require_once( wc_customer_order_csv_export()->get_plugin_path() . '/includes/data-stores/class-wc-customer-order-csv-export-data-store-factory.php' );

		self::install_default_settings();
		self::install_default_custom_format_builder_settings();
		self::install_data_stores();
	}


	/**
	 * Performs upgrades from older versions.
	 *
	 * @since 4.5.0
	 *
	 * @param string $from_version current installed version
	 */
	public static function upgrade( $from_version ) {

		$plugin       = wc_customer_order_csv_export();
		$upgrade_path = array(
			'3.0.4'        => 'upgrade_to_3_0_4',
			'3.4.0'        => 'upgrade_to_3_4_0',
			'3.12.0'       => 'upgrade_to_3_12_0',
			'4.0.0'        => 'upgrade_to_4_0_0',
			'4.5.0'        => 'upgrade_to_4_5_0',
			'4.6.0'        => 'upgrade_to_4_6_0',
		);

		foreach ( $upgrade_path as $upgrade_to_version => $upgrade_script ) {

			if ( version_compare ( $from_version, $upgrade_to_version, '<' ) && method_exists( __CLASS__, $upgrade_script ) ) {

				$plugin->log( "Begin upgrading to version {$upgrade_to_version}..." );

				self::$upgrade_script();

				$plugin->log( "Upgrade to version {$upgrade_to_version} complete" );
			}
		}
	}


	/**
	 * Installs the database and filesystem data stores.
	 *
	 * @since 4.5.0
	 */
	public static function install_data_stores() {

		// database
		self::create_tables();

		// filesystem
		self::create_files();
	}


	/**
	 * Creates new database tables.
	 *
	 * @since 4.5.0
	 */
	public static function create_tables() {
		global $wpdb;

		WC_Customer_Order_CSV_Export_Data_Store_Factory::includes( 'database' );

		// nothing to create if we're already there
		if ( self::validate_table() ) {
			return;
		}

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( WC_Customer_Order_CSV_Export_Data_Store_Database::get_table_schema() );
	}


	/**
	 * Create files/directories
	 *
	 * Based on WC_Install::create_files()
	 *
	 * @since 4.5.0
	 */
	private static function create_files() {

		WC_Customer_Order_CSV_Export_Data_Store_Factory::includes( 'filesystem' );

		// Install files and folders for exported files and prevent hotlinking
		$upload_dir      = WC_Customer_Order_CSV_Export_Data_Store_Filesystem::get_exports_directory();
		$download_method = get_option( 'woocommerce_file_download_method', 'force' );

		$files = array(
			array(
				'base'    => $upload_dir,
				'file'    => 'index.html',
				'content' => ''
			),
		);

		if ( 'redirect' !== $download_method ) {
			$files[] = array(
				'base'    => $upload_dir,
				'file'    => '.htaccess',
				'content' => 'deny from all'
			);
		}

		foreach ( $files as $file ) {

			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {

				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {

					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}


	/**
	 * Validates that the table required by Customer/Order CSV Export is present in the database.
	 *
	 * @since 4.5.0
	 *
	 * @return bool true if all are found, false if not
	 */
	public static function validate_table() {
		global $wpdb;

		$table_name = WC_Customer_Order_CSV_Export_Data_Store_Database::get_table_name();

		return $table_name === $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" );
	}


	/**
	 * Installs default plugin settings.
	 *
	 * @since 4.5.0
	 *
	 * @param string|null $section_id settings section to install defaults for
	 */
	private static function install_default_settings( $section_id = null ) {

		$plugin = wc_customer_order_csv_export();

		require_once( $plugin->get_plugin_path() . '/includes/admin/class-wc-customer-order-csv-export-admin-settings.php' );

		foreach ( WC_Customer_Order_CSV_Export_Admin_Settings::get_settings( $section_id ) as $section => $settings ) {

			foreach ( $settings as $setting ) {

				if ( isset( $setting['default'] ) ) {

					update_option( $setting['id'], $setting['default'] );
				}
			}
		}
	}


	/**
	 * Installs default custom format builder settings.
	 *
	 * @since 4.5.0
	 */
	private static function install_default_custom_format_builder_settings() {

		$plugin = wc_customer_order_csv_export();

		require_once( $plugin->get_plugin_path() . '/includes/admin/class-wc-customer-order-csv-export-admin-custom-format-builder.php' );

		foreach ( WC_Customer_Order_CSV_Export_Admin_Custom_Format_Builder::get_settings() as $section => $settings ) {

			foreach ( $settings as $setting ) {

				if ( isset( $setting['default'] ) ) {

					update_option( $setting['id'], $setting['default'] );
				}
			}
		}
	}


	/** Upgrade Routines ******************************************************/


	/**
	 * Upgrades the plugin to version 3.0.4
	 *
	 * @since 4.5.0
	 */
	private static function upgrade_to_3_0_4() {

		// wc_customer_order_csv_export_passive_mode > wc_customer_order_csv_export_ftp_passive_mode
		update_option( 'wc_customer_order_csv_export_ftp_passive_mode', get_option( 'wc_customer_order_csv_export_passive_mode' ) );
		delete_option( 'wc_customer_order_csv_export_passive_mode' );
	}


	/**
	 * Upgrades the plugin to version 3.4.0
	 *
	 * @since 4.5.0
	 */
	private static function upgrade_to_3_4_0() {

		// update order statuses for 2.2+
		$order_status_options = array( 'wc_customer_order_csv_export_statuses', 'wc_customer_order_csv_export_auto_export_statuses' );

		foreach ( $order_status_options as $option ) {

			$order_statuses     = (array) get_option( $option );
			$new_order_statuses = array();

			foreach ( $order_statuses as $status ) {
				$new_order_statuses[] = 'wc-' . $status;
			}

			update_option( $option, $new_order_statuses );
		}
	}


	/**
	 * Upgrades the plugin to version 3.12.0
	 *
	 * @since 4.5.0
	 */
	private static function upgrade_to_3_12_0() {

		if ( 'import' === get_option( 'wc_customer_order_csv_export_order_format' ) ) {
			update_option( 'wc_customer_order_csv_export_order_format', 'legacy_import' );
		}
	}


	/**
	 * Upgrades the plugin to version 4.0.0
	 *
	 * @since 4.5.0
	 */
	private static function upgrade_to_4_0_0() {

		$plugin = wc_customer_order_csv_export();

		// install defaults for customer auto-export settings, this must be done before
		// updating renamed options, otherwise defaults will override the previously set options
		self::install_default_settings( 'customers' );

		self::create_files();

		// install defaults for new settings
		update_option( 'wc_customer_order_csv_export_orders_add_note', 'yes' );
		update_option( 'wc_customer_order_csv_export_orders_auto_export_trigger', 'schedule' );

		// rename settings
		$renamed_options = array(
			'wc_customer_order_csv_export_order_format'           => 'wc_customer_order_csv_export_orders_format',
			'wc_customer_order_csv_export_order_filename'         => 'wc_customer_order_csv_export_orders_filename',
			'wc_customer_order_csv_export_customer_format'        => 'wc_customer_order_csv_export_customers_format',
			'wc_customer_order_csv_export_customer_filename'      => 'wc_customer_order_csv_export_customers_filename',
			'wc_customer_order_csv_export_auto_export_method'     => 'wc_customer_order_csv_export_orders_auto_export_method',
			'wc_customer_order_csv_export_auto_export_start_time' => 'wc_customer_order_csv_export_orders_auto_export_start_time',
			'wc_customer_order_csv_export_auto_export_interval'   => 'wc_customer_order_csv_export_orders_auto_export_interval',
			'wc_customer_order_csv_export_auto_export_statuses'   => 'wc_customer_order_csv_export_orders_auto_export_statuses',
			'wc_customer_order_csv_export_ftp_server'             => 'wc_customer_order_csv_export_orders_ftp_server',
			'wc_customer_order_csv_export_ftp_username'           => 'wc_customer_order_csv_export_orders_ftp_username',
			'wc_customer_order_csv_export_ftp_password'           => 'wc_customer_order_csv_export_orders_ftp_password',
			'wc_customer_order_csv_export_ftp_port'               => 'wc_customer_order_csv_export_orders_ftp_port',
			'wc_customer_order_csv_export_ftp_path'               => 'wc_customer_order_csv_export_orders_ftp_path',
			'wc_customer_order_csv_export_ftp_security'           => 'wc_customer_order_csv_export_orders_ftp_security',
			'wc_customer_order_csv_export_ftp_passive_mode'       => 'wc_customer_order_csv_export_orders_ftp_passive_mode',
			'wc_customer_order_csv_export_http_post_url'          => 'wc_customer_order_csv_export_orders_http_post_url',
			'wc_customer_order_csv_export_email_recipients'       => 'wc_customer_order_csv_export_orders_email_recipients',
			'wc_customer_order_csv_export_email_subject'          => 'wc_customer_order_csv_export_orders_email_subject',
		);

		foreach ( $renamed_options as $old => $new ) {

			update_option( $new, get_option( $old ) );
			delete_option( $old );
		}

		// install default custom field mapping settings
		self::install_default_custom_format_builder_settings();

		// maintain backwards compatibility with previous `default` and
		// `default_one_row_per_item` formats for those who use it by creating a custom
		// format based on the previous version
		$orders_format = get_option( 'wc_customer_order_csv_export_orders_format' );

		if ( in_array( $orders_format, array( 'default', 'default_one_row_per_item' ), true ) ) {

			$custom_format = $plugin->get_formats_instance()->get_format( 'orders', $orders_format );

			// keep order_number backwards-compatible and remove refunds key
			$custom_format['columns']['order_number_formatted'] = 'order_number';
			unset( $custom_format['columns']['order_number'], $custom_format['columns']['refunds'] );

			if ( 'default_one_row_per_item' === $orders_format ) {

				// rename 'total_tax' back to 'tax'
				$custom_format['columns']['total_tax'] = 'tax';

				// remove item-specific keys that weren't present in the old default format
				unset(
					$custom_format['columns']['item_id'],
					$custom_format['columns']['item_product_id'],
					$custom_format['columns']['subtotal'],
					$custom_format['columns']['subtotal_tax']
				);

				update_option( 'wc_customer_order_csv_export_orders_custom_format_row_type', 'item' );

			} else {

				update_option( 'wc_customer_order_csv_export_orders_custom_format_row_type', 'order' );
			}

			$mapping = array();

			foreach ( $custom_format['columns'] as $column => $name ) {
				$mapping[] = array( 'source' => $column, 'name' => $name );
			}

			update_option( 'wc_customer_order_csv_export_orders_custom_format_delimiter', ',' );
			update_option( 'wc_customer_order_csv_export_orders_custom_format_mapping', $mapping );

			// set the current orders export format as `custom`
			update_option( 'wc_customer_order_csv_export_orders_format', 'custom' );
		}

		// handle renamed cron schedule
		if ( $start_timestamp = wp_next_scheduled( 'wc_customer_order_csv_export_auto_export_interval' ) ) {

			wp_clear_scheduled_hook( 'wc_customer_order_csv_export_auto_export_interval' );

			wp_schedule_event( $start_timestamp, 'wc_customer_order_csv_export_orders_auto_export_interval', 'wc_customer_order_csv_export_auto_export_orders' );
		}
	}


	/**
	 * Upgrades the plugin to version 4.5.0
	 *
	 * @since 4.5.0
	 */
	private static function upgrade_to_4_5_0() {

		self::create_tables();
	}


	/**
	 * Upgrades the plugin to version 4.6.0.
	 *
	 * @since 4.6.0
	 */
	private static function upgrade_to_4_6_0() {

		$plugin = wc_customer_order_csv_export();

		// set default settings for coupons export
		// in older versions of WC these settings aren't included, so we need grab the coupon settings specifically
		// this is yuno use self::install_default_settings()
		require_once( $plugin->get_plugin_path() . '/includes/admin/class-wc-customer-order-csv-export-admin-settings.php' );

		// pass an empty array as a starting point so we only get coupon settings
		foreach ( WC_Customer_Order_CSV_Export_Admin_Settings::get_coupon_export_settings( array() ) as $section => $settings ) {

			foreach ( $settings as $setting ) {

				if ( isset( $setting['default'] ) ) {
					update_option( $setting['id'], $setting['default'] );
				}
			}
		}

		// set default mapping for custom coupons export
		$custom_format = $plugin->get_formats_instance()->get_format( 'coupons', 'default' );

		$mapping = array();

		foreach ( $custom_format['columns'] as $column => $name ) {
			$mapping[] = array( 'source' => $column, 'name' => $name );
		}

		update_option( 'wc_customer_order_csv_export_coupons_custom_format_mapping', $mapping );
	}

}
