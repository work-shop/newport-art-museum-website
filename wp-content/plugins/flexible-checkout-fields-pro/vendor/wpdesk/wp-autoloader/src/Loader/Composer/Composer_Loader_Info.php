<?php

class WPDesk_Composer_Loader_Info {
	/** @var int */
	private $load_priority;

	/** @var \SplFileInfo */
	private $autoload_file;

	/** @var \SplFileInfo */
	private $creation_file;

	/** @var WPDesk_Plugin_Info */
	private $plugin_info;

	/**
	 * @param int $load_priority
	 */
	public function set_load_priority( $load_priority ) {
		$this->load_priority = $load_priority;
	}

	/**
	 * @param SplFileInfo $autoload_file
	 */
	public function set_autoload_file( $autoload_file ) {
		$this->autoload_file = $autoload_file;
	}

	/**
	 * @param SplFileInfo $creation_file
	 */
	public function set_creation_file( $creation_file ) {
		$this->creation_file = $creation_file;
	}

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return $this->load_priority;
	}

	/**
	 * @return SplFileInfo
	 */
	public function get_autoload_file() {
		return $this->autoload_file;
	}

	/**
	 * @return SplFileInfo
	 */
	public function get_creation_file() {
		return $this->creation_file;
	}

	/**
	 * @return WPDesk_Plugin_Info
	 */
	public function get_plugin_info() {
		return $this->plugin_info;
	}

	/**
	 * @param WPDesk_Plugin_Info $plugin_info
	 */
	public function set_plugin_info( $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

}