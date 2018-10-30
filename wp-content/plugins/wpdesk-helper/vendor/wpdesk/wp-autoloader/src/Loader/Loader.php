<?php

/**
 * Interface for all loaders that can be manager by manager
 */
interface WPDesk_Loader {
	/**@return int */
	public function get_load_priority();

	/**@return int */
	public function get_create_priority();

	/** @return bool */
	public function notify_can_autoload();

	/** @return void */
	public function notify_can_create_plugin();

	/** @return bool */
	public function notify_cannot_autoload();

	/** @return bool */
	public function notify_all_loaded();

    /**
     * @param WPDesk_Loader_Manager $manager Observed manager
     *
     * @return void
     */
	public function update_manager(WPDesk_Loader_Manager $manager);
}