<?php

if ( ! class_exists( 'WPDesk_Loader' ) ) {
	require_once 'Loader.php';
}

/**
 * Manages loaders that can load/autoload and create resources
 */
class WPDesk_Loader_Manager {
	const DEFAULT_LOADER_PRIORITY = - 10;

	const HOOK_TO_LOAD_LOADERS = 'plugins_loaded';

	const HOOK_BEFORE_LOAD_AUTOLOADERS = 'wp_autoloader_loader_manager_before_load_autoloaders';
	const HOOK_ALL_LOADERS_LOADED = 'wp_autoloader_loader_manager_all_autoloaders_loaded';
	const HOOK_ALL_LOADERS_LOADED_AND_NOTIFIED = 'wp_autoloader_loader_manager_all_autoloaders_loaded_notified';
	const HOOK_BEFORE_CREATE = 'wp_autoloader_loader_manager_before_create';
	const HOOK_ALL_LOADERS_CREATED = 'wp_autoloader_loader_manager_all_created';

	const FILTER_IF_LOAD_LOADER = 'wp_autoloader_loader_should_load';
	const FILTER_LOADERS_TO_LOAD = 'wp_autoloader_loader_loaders_to_load';
	const FILTER_LOADERS_TO_CREATE = 'wp_autoloader_loader_loaders_to_create';

	/** @var array */
	protected static $loaders = [];

	/** @var bool */
	protected static $load_hook_added = false;

	/** @var array */
	protected static $exceptions = [];

	/** @var \Psr\Log\LoggerInterface */
	protected static $logger = null;

	/** @var string */
	protected $manager_hook = self::HOOK_TO_LOAD_LOADERS;

	/** @var int */
	protected $manager_hook_priority = self::DEFAULT_LOADER_PRIORITY;

	/** @return array */
	public function get_loaders() {
		return static::$loaders;
	}

	/**
	 * @param \Psr\Log\LoggerInterface|WC_Logger_Interface $logger
	 */
	public function set_logger( $logger ) {
		static::$logger = $logger;
	}

	/**
	 * Attach loader to load
	 *
	 * @param WPDesk_Loader $loader
	 */
	public function attach_loader( WPDesk_Loader $loader ) {
		$loader->update_manager( $this );
		static::$loaders[] = $loader;
	}

	/**
	 * Detach loader from load list
	 *
	 * @param WPDesk_Loader $loader
	 */
	public function detach_loader( WPDesk_Loader $loader ) {
		foreach ( static::$loaders as $okey => $oval ) {
			if ( $oval == $loader ) {
				unset( static::$loaders[ $okey ] );
			}
		}
	}

	/**
	 * Attach loader to WP hook if it's not already attached
	 *
	 * @param string $hook
	 * @param int $priority
	 */
	public function attach_autoload_hook_once(
		$hook = self::HOOK_TO_LOAD_LOADERS,
		$priority = self::DEFAULT_LOADER_PRIORITY
	) {
		$this->manager_hook          = $hook;
		$this->manager_hook_priority = $priority;
		if ( ! static::$load_hook_added ) {
			static::$load_hook_added = add_action( $hook, [ $this, 'notify_loaders_action' ], $priority );
		}
	}

	/**
	 * Detach loader from WP hook
	 */
	public function detach_autoload_hook() {
		remove_action( $this->manager_hook, [ $this, 'notify_loaders' ], $this->manager_hook_priority );
	}

	public function notify_loaders_action() {
		do_action( self::HOOK_BEFORE_LOAD_AUTOLOADERS );
		$notified_loaders = $this->notify_loaders_can_load( static::$loaders );
		do_action( self::HOOK_ALL_LOADERS_LOADED_AND_NOTIFIED );

		do_action( self::HOOK_BEFORE_CREATE );
		$this->notify_loaders_can_create( $notified_loaders );
		do_action( self::HOOK_ALL_LOADERS_CREATED );

		$this->throw_exception_if_any();
	}

	/**
	 * Notify all loaders about event can_autoload, cannot_autoload and all_loaded
	 *
	 * @param array $loaders
	 *
	 * @return array
	 */
	private function notify_loaders_can_load( $loaders ) {
		$loaders        = $this->sort_loaders_load_priority_desc( $loaders );
		$loaders        = apply_filters( self::FILTER_LOADERS_TO_LOAD, $loaders );
		$loaders_loaded = [];

		foreach ( $loaders as $loadable ) {
			$should_load = apply_filters( self::FILTER_IF_LOAD_LOADER, true, $loadable );

			if ( $should_load ) {
				$this->notify_can_autoload( $loadable );
				$loaders_loaded[] = $loadable;
			} else {
				$this->notify_cannot_autoload( $loadable );
			}
		}
		$this->notify_loaders_all_loaded( $loaders );

		return $loaders_loaded;
	}

	/**
	 * Sort loaders list by load priority
	 *
	 * @param array $loaders
	 *
	 * @return array
	 */
	private function sort_loaders_load_priority_desc( $loaders ) {
		usort( $loaders, function ( WPDesk_Loader $a, WPDesk_Loader $b ) {
			return $b->get_load_priority() - $a->get_load_priority();
		} );

		return $loaders;
	}

	/**
	 * Notify loader that now it can add his autoloader
	 *
	 * @param WPDesk_Loader $loadable
	 *
	 * @return bool
	 */
	protected function notify_can_autoload( WPDesk_Loader $loadable ) {
		try {
			return $loadable->notify_can_autoload();
		} catch ( Exception $e ) {
			$this->log_exception( $e );

			return false;
		}
	}

	/**
	 * Log exception if occured to static list and to logs
	 *
	 * @param Exception $e
	 */
	protected function log_exception( Exception $e ) {
		static::$exceptions[] = $e;
		if ( ! empty( static::$logger ) ) {
			static::$logger->critical( "Load manager exception: {$e->getCode()}: {$e->getMessage()}. {$e->getTraceAsString()}" );
		}
	}

	/**
	 * Notify loader that he should not add hit autoloader
	 *
	 * @param WPDesk_Loader $loadable
	 *
	 * @return bool
	 */
	protected function notify_cannot_autoload( WPDesk_Loader $loadable ) {
		try {
			return $loadable->notify_cannot_autoload();
		} catch ( Exception $e ) {
			$this->log_exception( $e );

			return false;
		}
	}

	/**
	 * Notify group of loaders that all loaders have added their autoloaders
	 *
	 * @param array $loaders
	 */
	private function notify_loaders_all_loaded( $loaders ) {
		foreach ( $loaders as $loadable ) {
			$this->notify_all_loaded( $loadable );
		}
		do_action( self::HOOK_ALL_LOADERS_LOADED );
	}

	/**
	 * Notify loader that that all loaders have added their autoloaders
	 *
	 * @param WPDesk_Loader $loadable
	 */
	protected function notify_all_loaded( WPDesk_Loader $loadable ) {
		try {
			$loadable->notify_all_loaded();
		} catch ( Exception $e ) {
			$this->log_exception( $e );
		}
	}

	/**
	 * Notify group of loaders that that now they can create/instantiate autoloaded resources
	 *
	 * @param array $loaders
	 */
	private function notify_loaders_can_create( $loaders ) {
		$loaders = $this->sort_loaders_create_priority_desc( $loaders );
		$loaders = apply_filters( self::FILTER_LOADERS_TO_CREATE, $loaders );

		foreach ( $loaders as $loadable ) {
			$this->notify_can_create_plugin( $loadable );
		}
	}

	/**
	 * Sort loaders list by create priority
	 *
	 * @param array $loaders
	 *
	 * @return array
	 */
	private function sort_loaders_create_priority_desc( $loaders ) {
		usort( $loaders, function ( WPDesk_Loader $a, WPDesk_Loader $b ) {
			return $b->get_create_priority() - $a->get_create_priority();
		} );

		return $loaders;
	}

	/**
	 * Notify loader that that now it can create/instantiate autoloaded resources
	 *
	 * @param WPDesk_Loader $loadable
	 */
	protected function notify_can_create_plugin( WPDesk_Loader $loadable ) {
		try {
			$loadable->notify_can_create_plugin();
		} catch ( Exception $e ) {
			$this->log_exception( $e );
		}
	}

	/**
	 * If any exception was thrown through execution throw first of them now.
	 */
	protected function throw_exception_if_any() {
		if ( ! empty( $this->exceptions ) ) {
			throw reset( $this->exceptions );
		}
	}
}