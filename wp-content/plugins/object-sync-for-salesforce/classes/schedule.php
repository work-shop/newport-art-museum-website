<?php
/**
 * Class file for the Object_Sync_Sf_Schedule class. Extend the WP_Background_Process class for the purposes of Object Sync for Salesforce.
 *
 * @file
 */

if ( ! class_exists( 'Object_Sync_Salesforce' ) ) {
	die();
}

/**
 * Schedule events in a queue in WordPress
 */
class Object_Sync_Sf_Schedule extends WP_Background_Process {

	protected $wpdb;
	protected $version;
	protected $login_credentials;
	protected $slug;
	protected $wordpress;
	protected $salesforce;
	protected $mappings;
	protected $schedule_name;
	protected $logging;

	/**
	* Constructor which sets up schedule and handler for when schedule runs
	*
	* @param object $wpdb
	* @param string $version
	* @param array $login_credentials
	* @param string $slug
	* @param object $wordpress
	* @param object $salesforce
	* @param object $mappings
	* @param string $schedule_name
	* @throws \Exception
	*/

	public function __construct( $wpdb, $version, $login_credentials, $slug, $wordpress, $salesforce, $mappings, $schedule_name, $logging, $schedulable_classes ) {

		$this->wpdb = $wpdb;
		$this->version = $version;
		$this->login_credentials = $login_credentials;
		$this->slug = $slug;
		$this->wordpress = $wordpress;
		$this->salesforce = $salesforce;
		$this->mappings = $mappings;
		$this->schedule_name = $schedule_name;
		$this->logging = $logging;
		$this->schedulable_classes = $schedulable_classes;

		$this->identifier = $this->schedule_name;

		$this->add_filters();
		add_action( $this->schedule_name, array( $this, 'maybe_handle' ) ); // run the handle method

	}

	/**
	* Create the filters we need to run
	*
	*/
	public function add_filters() {
		add_filter( 'cron_schedules', array( $this, 'set_schedule_frequency' ) );
	}

	/**
	* Convert the schedule frequency from the admin settings into an array
	* interval must be in seconds for the class to use it
	*
	*/
	public function set_schedule_frequency( $schedules ) {

		// create an option in the core schedules array for each one the plugin defines
		foreach ( $this->schedulable_classes as $key => $value ) {
			$schedule_number = absint( get_option( 'object_sync_for_salesforce_' . $key . '_schedule_number', '' ) );
			$schedule_unit = get_option( 'object_sync_for_salesforce_' . $key . '_schedule_unit', '' );

			switch ( $schedule_unit ) {
				case 'minutes':
					$seconds = 60;
					break;
				case 'hours':
					$seconds = 3600;
					break;
				case 'days':
					$seconds = 86400;
					break;
				default:
					$seconds = 0;
			}

			$key = $schedule_unit . '_' . $schedule_number;

			$schedules[ $key ] = array(
				'interval' => $seconds * $schedule_number,
				'display' => 'Every ' . $schedule_number . ' ' . $schedule_unit,
			);

			$this->schedule_frequency = $key;

		}

		return $schedules;

	}

	/**
	* Convert the schedule frequency from the admin settings into an array
	* interval must be in seconds for the class to use it
	*
	*/
	public function get_schedule_frequency_key( $name = '' ) {

		$schedule_number = get_option( 'object_sync_for_salesforce_' . $name . '_schedule_number', '' );
		$schedule_unit = get_option( 'object_sync_for_salesforce_' . $name . '_schedule_unit', '' );

		switch ( $schedule_unit ) {
			case 'minutes':
				$seconds = 60;
				break;
			case 'hours':
				$seconds = 3600;
				break;
			case 'days':
				$seconds = 86400;
				break;
			default:
				$seconds = 0;
		}

		$key = $schedule_unit . '_' . $schedule_number;

		return $key;

	}

	/**
	* Convert the schedule frequency from the admin settings into seconds
	*
	*/
	public function get_schedule_frequency_seconds( $name = '' ) {

		$schedule_number = get_option( 'object_sync_for_salesforce_' . $name . '_schedule_number', '' );
		$schedule_unit = get_option( 'object_sync_for_salesforce_' . $name . '_schedule_unit', '' );

		switch ( $schedule_unit ) {
			case 'minutes':
				$seconds = 60;
				break;
			case 'hours':
				$seconds = 3600;
				break;
			case 'days':
				$seconds = 86400;
				break;
			default:
				$seconds = 0;
		}

		$total = $seconds * $schedule_number;

		return $total;

	}

	/**
	 * Schedule function
	 * This creates and manages the scheduling of the task
	 *
	 * @return void
	 */
	public function use_schedule( $name = '' ) {

		if ( '' !== $name ) {
			$schedule_name = $name;
		} else {
			$schedule_name = $this->schedule_name;
		}

		$schedule_frequency = $this->get_schedule_frequency_key( $name );

		if ( ! wp_next_scheduled( $schedule_name ) ) {
			wp_schedule_event( time(), $schedule_frequency, $schedule_name );
		}

	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on the
	 * queue data. Return the modified data for further processing
	 * in the next pass through. Or, return false to remove the
	 * data from the queue.
	 *
	 * @param mixed $data Queue data to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $data ) {
		if ( is_array( $this->schedulable_classes[ $this->schedule_name ] ) ) {
			$schedule = $this->schedulable_classes[ $this->schedule_name ];
			if ( isset( $schedule['class'] ) ) {
				$class = new $schedule['class']( $this->wpdb, $this->version, $this->login_credentials, $this->slug, $this->wordpress, $this->salesforce, $this->mappings, $this->logging, $this->schedulable_classes );
				$method = $schedule['callback'];
				$task = $class->$method( $data['object_type'], $data['object'], $data['mapping'], $data['sf_sync_trigger'] );
			}
		}
		return false;
	}

	/**
	 * Check for data
	 *
	 * This method is new to the extension. It allows a scheduled method to do nothing but call the
	 * callback parameter of its calling class.
	 * This is useful for running the salesforce_pull event to check for updates in Salesforce
	 *
	 * @return $data
	 */
	protected function check_for_data() {
		if ( is_array( $this->schedulable_classes[ $this->schedule_name ] ) ) {
			$schedule = $this->schedulable_classes[ $this->schedule_name ];
			if ( isset( $schedule['class'] ) ) {
				$class = new $schedule['class']( $this->wpdb, $this->version, $this->login_credentials, $this->slug, $this->wordpress, $this->salesforce, $this->mappings, $this->logging, $this->schedulable_classes );
				$method = $schedule['initializer'];
				$task = $class->$method();
			}
		}
		// we have checked for data and it's in the queue if it exists
		// now run maybe_handle again to see if it nees to be processed
		$this->maybe_handle( true );
	}

	/**
	 * Maybe process queue
	 *
	 * Checks whether data exists within the queue and that
	 * the process is not already running.
	 */
	public function maybe_handle( $already_checked = false, $ajax = false ) {
		if ( $this->is_process_running() ) {
			// Background process already running.
			wp_die();
		}

		// if we need to check for data first, run that method
		// it should call its corresponding class method that saves data to the queue
		// it should then run maybe_handle() again

		$check_for_data_first = isset( $this->schedulable_classes[ $this->schedule_name ]['initializer'] ) ? true : false;

		if ( false === $already_checked && true === $check_for_data_first ) {
			$this->check_for_data();
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			wp_die();
		}

		if ( true === $ajax ) {
			check_ajax_referer( $this->identifier, 'nonce' );
		}

		$this->handle();
		wp_die();
	}

	/**
	 * Method to cancel a specific queue by its name
	 *
	 * This is modeled off the cancel_process method in wp-background-process but that one doesn't seem to work when we need to specify the queue name
	 */
	public function cancel_by_name( $name ) {
		if ( ! isset( $name ) ) {
			$name = $this->identifier . '_cron';
		}
		if ( ! $this->is_queue_empty() ) {
			while ( $this->count_queue_items() > 0 ) {
				$batch = $this->get_batch();
				$this->delete( $batch->key );
				wp_clear_scheduled_hook( $name );
			}
		}
	}

	/**
	 * How many items are in this queue?
	 * Based on is_queue_empty from base library
	 *
	 * @return bool
	 */
	public function count_queue_items( $schedule_name = '' ) {
		$wpdb = $this->wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		if ( '' === $schedule_name ) {
			$key = $this->identifier . '_batch_%';
		} else {
			$key = $schedule_name . '_batch_%';
		}

		$count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM {$table}
			WHERE {$column} LIKE %s
		", $key ) );

		return $count;
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();
		// we could log something here, or show something to admin user, etc.
	}

}
