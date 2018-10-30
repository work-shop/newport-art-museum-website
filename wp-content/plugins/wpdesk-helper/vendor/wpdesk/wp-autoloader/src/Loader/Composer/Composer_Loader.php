<?php

class WPDesk_Composer_Loader implements WPDesk_Loader {
	/** @var WPDesk_Composer_Loader_Info */
	private $loader_info;

	/** @var bool  */
	private $autoload_loaded = false;

	/** @var bool  */
	private $plugin_loaded = false;

	/** @var WPDesk_Loader_Manager|null */
	private $manager;

	/**
	 * WPDesk_Composer_Loader constructor.
	 *
	 * @param WPDesk_Composer_Loader_Info $loader_info
	 */
	public function __construct( WPDesk_Composer_Loader_Info $loader_info) {
		$this->loader_info = $loader_info;
	}

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return $this->loader_info->get_load_priority();
	}

	/**
	 * @return int
	 */
	public function get_create_priority() {
		return $this->loader_info->get_load_priority();
	}

	/**
	 * Load composer autoload file
	 *
	 * @return void
	 */
	public function notify_can_autoload() {
		if (!$this->autoload_loaded) {
			$this->autoload_loaded = true;
			require_once( $this->loader_info->get_autoload_file()->getPathname() );
		}
	}

	/**
	 * Load creation file
	 *
	 * @return void
	 */
	public function notify_can_create_plugin() {
		if (!$this->plugin_loaded) {
			$this->plugin_loaded = true;
			/** @noinspection PhpUnusedLocalVariableInspection */
			$plugin_info = $this->loader_info->get_plugin_info();
			require_once( $this->loader_info->get_creation_file()->getPathname() );
		}
	}

	/**
	 * Well that's life. Do nothing.
	 *
	 * @return void
	 */
	public function notify_cannot_autoload() {
	}

	/**
	 * Thanks for notification
	 *
	 * @return void
	 */
	public function notify_all_loaded() {
	}

	/**
	 * @param WPDesk_Loader_Manager $manager
	 * @return void
	 */
	public function update_manager( WPDesk_Loader_Manager $manager ) {
		$this->manager = $manager;
	}


}