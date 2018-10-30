<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	//set_site_transient('update_plugins', null);

	if (!class_exists('inspire_Plugin4'))
	{

		/**
		 * Base plugin class for Inspire Labs plugins
		 *
		 * @author Krzysiek
		 *
		 */
	    abstract class inspire_Plugin4
	    {
			const VERSION = '4.0';
			const DEFAULT_TOKEN = '350299001f1cbf2c8f3af7ca3296f0a3';

	    	protected $_pluginNamespace = "";
	    	protected $__wpdeskUpdateUrl = 'http://wpdesk.pl/wp-content/';

	    	protected $_pluginPath;
	    	protected $_templatePath;
	    	protected $_pluginFilePath;
	    	protected $_pluginUrl;

	    	protected $_defaultViewArgs; // default args given to template

	    	public function __construct()
	    	{
	    		$this->initBaseVariables();
	    	}

	    	/**
	    	 *
	    	 * @return inspire_Plugin4
	    	 */
	    	public function getPlugin()
	    	{
	    		return $this;
	    	}

	    	public function loadPluginTextDomain()
	    	{
	    	    load_plugin_textdomain( 'inspire-plugin', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
	    	}

	    	/**
	    	 *
	    	 */
	    	public function initBaseVariables()
	    	{
	    		$reflection = new ReflectionClass($this);

	    		// Set Plugin Path
	    		$this->_pluginPath = dirname($reflection->getFileName());

	    		// Set Plugin URL
	    		$this->_pluginUrl = plugin_dir_url($reflection->getFileName());

	    		$this->_pluginFilePath = $reflection->getFileName();

	    		$this->_templatePath = '/' . $this->_pluginNamespace . '_templates';

	    		$this->_defaultViewArgs = array(
	    			'pluginUrl' => $this->getPluginUrl()
	    		);

	    		register_activation_hook( $this->_pluginFilePath, array( $this, 'activation' ) );
	    		register_deactivation_hook( $this->_pluginFilePath, array( $this, 'deactivation' ) );
	    		register_uninstall_hook( $this->_pluginFilePath, 'inspire_Plugin4::uninstall' );

	    		add_action( 'plugins_loaded', array($this, 'loadPluginTextDomain') );
	   			add_filter( 'plugin_action_links_' . plugin_basename( $this->getPluginFilePath() ), array( $this, 'linksFilter' ) );

	   			/*$this->_initPluginUpdates();
	   			echo '<pre style="margin-left: 170px;">';
	   			var_dump($this->_pluginPath);
	   			var_dump($this->_pluginUrl);
	   			var_dump($this->_pluginFilePath);
	   			var_dump($this->getTemplatePath());
	   			var_dump($this->getPluginAssetsUrl());

	   			echo '</pre>';*/

	    	}

	    	/**
	    	 * Plugin activation hook
	    	 */
	    	public function activation()
	    	{

	    	}

	    	/**
	    	 * Plugin deactivation hook
	    	 */
	    	public function deactivation()
	    	{

	    	}

	    	/**
	    	 * Plugin uninstall hook
	    	 */
	    	public static function uninstall()
	    	{

	    	}

	        /**
	         *
	         * @return string
	         */
	        public function getPluginUrl()
	        {
	        	return esc_url(trailingslashit($this->_pluginUrl));
	        }

	        public function getPluginAssetsUrl()
	        {
	            return esc_url(trailingslashit($this->getPluginUrl() . 'assets'));
	        }

	        /**
	         * @return string
	         */
	        public function getTemplatePath()
	        {
	        	return trailingslashit($this->_templatePath);
	        }

	        public function getPluginFilePath()
	        {
	        	 return $this->_pluginFilePath;
	        }

	        public function getNamespace()
	        {
	        	return $this->_pluginNamespace;
	        }

	        protected function _initPluginUpdates()
	        {
	        	add_filter('pre_set_site_transient_update_plugins', array($this, 'checkForPluginUpdate'));
	        	//add_filter('plugins_api', array($this, 'pluginApiCall'), 10, 3);
	        }

	        protected function getPluginUpdateName()
	        {
	        	return $this->getNamespace() . '/' . str_replace('woocommerce-', '', $this->getNamespace()) .'.php';
	        }

	        public function checkForPluginUpdate($checked_data)
	        {
	        	global $wp_version;

	        	var_dump('y');

	        	if (empty($checked_data->checked))
	        		return $checked_data;

	        	var_dump($checked_data);

	        	$args = array(
	        			'slug' => $this->getNamespace(),
	        			'version' => $checked_data->checked[$this->getPluginUpdateName()],
	        	);
	        	$request_string = array(
	        			'body' => array(
	        					'action' => 'basic_check',
	        					'request' => serialize($args),
	        					'site' => get_bloginfo('url'),
	        			        'token' => self::DEFAULT_TOKEN
	        			),
	        			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	        	);

	        	var_dump($request_string); die();

	        	// Start checking for an update
	        	$raw_response = wp_remote_post($this->_wpdeskUpdateUrl, $request_string);

	        	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
	        		$response = unserialize($raw_response['body']);

	        	if (is_object($response) && !empty($response)) // Feed the update data into WP updater
	        		$checked_data->response[$this->getNamespace() .'/'. $this->getNamespace() .'.php'] = $response;

	        	return $checked_data;
	        }

	        function pluginApiCall($def, $action, $args) {
	        	global $wp_version;

	        	if (!isset($args->slug) || ($args->slug != $plugin_slug))
	        		return false;

	        	// Get the current version
	        	$plugin_info = get_site_transient('update_plugins');
	        	$current_version = $plugin_info->checked[$this->getPluginUpdateName()];
	        	$args->version = $current_version;

	        	$request_string = array(
	        			'body' => array(
	        					'action' => $action,
	        					'request' => serialize($args),
	        					'api-key' => md5(get_bloginfo('url'))
	        			),
	        			'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	        	);

	        	$request = wp_remote_post($this->wpdeskServer, $request_string);

	        	if (is_wp_error($request)) {
	        		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', 'inspire-plugin'), $request->get_error_message());
	        	} else {
	        		$res = unserialize($request['body']);

	        		if ($res === false)
	        			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred', 'inspire-plugin'), $request['body']);
	        	}

	        	return $res;
	        }

	        /**
	         *
	         * @param string $message
	         * @param string $class
	         */
	        public function addAdminMessage($message, $class = 'updated')
	        {
	        	$messages = $this->_getAdminMessages();
	        	if (!is_array($messages))
	        	{
	        		$messages = array();
	        	}
	        	$messages[$class][] = $message;
	        	$this->_setAdminMessages($messages);
	        }

	        protected function _getAdminMessages()
	        {
	        	return get_option($this->getNamespace() . '_messages');
	        }

	        protected function _setAdminMessages($messages)
	        {
	        	update_option($this->getNamespace() . '_messages', $messages);
	        }

	        /**
	         *
	         * @param boolean $clean
	         */
	        public function getAdminMessagesHtml($clean = true)
	        {
	        	$messages = $this->_getAdminMessages();
	        	$str = '';

	        	if (is_array($messages) && !empty($messages))
	        	{
	        	    foreach ($messages as $class => $messagesArray)
	        	    {
    	        		$str .= '<div class="' . $class . '" id="message">';

    	        		foreach ($messagesArray as $message)
    	        		{
    	        			$str .= '<p>' . $message . '</p>';
    	        		}
    	        		$str .= '</div>';
	        	    }
	        	}

	        	if ($clean)
	        	{
	        		$this->_setAdminMessages('');
	        	}
	        	return $str;
	        }

	        public function addFrontMessage($message, $class = 'message')
	        {
	            $messages = $this->_getFrontMessages();
	            if (!is_array($messages))
	            {
	                $messages = array();
	            }
	            $messages[$class][] = $message;
	            $this->_setFrontMessages($messages);
	        }

	        protected function _getFrontMessages()
	        {
	            return $_SESSION['i-messages'];
	        }

	        protected function _setFrontMessages($messages)
	        {
	            $_SESSION['i-messages'] = $messages;
	        }

	        public function getFrontMessagesHtml($clean = true)
	        {
	            $messages = $this->_getFrontMessages();
	            $str = '';

	            if (is_array($messages) && !empty($messages))
	            {
	                foreach ($messages as $class => $messagesArray)
	                {
	                    foreach ($messagesArray as $message)
	                    {
	                        $str .= '<p class="woocommerce-' . $class . '"><strong>' . $message . '</strong></p>';
	                    }
	                }
	            }

	            if ($clean)
	            {
	                $this->_setFrontMessages('');
	            }
	            return $str;
	        }

	        /**
			 * Renders end returns selected template
			 *
			 * @param string $name name of the template
			 * @param string $path additional inner path to the template
			 * @param array $args args accesible from template
			 * @return string
			 */
			public function loadTemplate($name, $path = '', $args = array())
			{
				//$args = array_merge($this->_defaultViewArgs, array('textDomain', $this->_textDomain), $args);
				$path = trim($path, '/');

				if (file_exists($templateName = implode('/', array(get_template_directory(), $this->getTemplatePath(), $path, $name . '.php'))))
				{
				} else {
					$templateName = implode('/', array($this->_pluginPath, $this->getTemplatePath(), $path, $name . '.php'));
				}

				ob_start();
				include($templateName);
				return ob_get_clean();
			}

	        /**
	         * Gets setting value
	         *
	         * @param string $name
	         * @param string $default
	         * @return Ambigous <mixed, boolean>
	         */
	        public function getSettingValue($name, $default = null)
	        {
	        	return get_option($this->getNamespace() . '_' . $name, $default);
	        }

	        public function setSettingValue($name, $value)
	        {
	        	return update_option($this->getNamespace() . '_' . $name, $value);
	        }

	        public function isSettingValue($name)
	        {
	        	$option = get_option($this->getNamespace() . '_' . $name);
	        	return !empty($option);
	        }

	        /**
	         * action_links function.
	         *
	         * @access public
	         * @param mixed $links
	         * @return void
	         */
	        public function linksFilter( $links )
	        {

	        	$plugin_links = array(
	        			'<a href="' . admin_url( 'admin.php?page=' . $this->getNamespace() ) . '">' . __( 'Ustawienia', 'inspire-plugin' ) . '</a>',
	        			'<a href="http://www.wpdesk.pl/docs/' . str_replace('_', '-', $this->getNamespace()) . '_docs/">' . __( 'Dokumentacja', 'inspire-plugin' ) . '</a>',
	        			'<a href="http://www.wpdesk.pl/support/">' . __( 'Wsparcie', $this->getTextDomain() ) . '</a>',
	        	);

	        	return array_merge( $plugin_links, $links );
	        }

	        /**
	         *
	         * @param string $name
	         */
	        protected function _convertCamelCaseToPath($name)
	        {
	            return strtolower(preg_replace('/([a-z0-9])([A-Z])/', '$1_$2', $name));
	        }

	        public function createHelperClass($name)
	        {
	            require_once('pluginHelper4.php');
	            $file = $this->_convertCamelCaseToPath($name); ;
	            require_once( plugin_dir_path($this->getPluginFilePath()) . '/classes/' . $file . '.php' );

	            return new $name($this);
	        }

	        public function createDependant($name)
	        {
	            require_once('pluginDependant4.php');
	            $file = $this->_convertCamelCaseToPath($name); ;
	            require_once( plugin_dir_path($this->getPluginFilePath()) . '/classes/' . $file . '.php' );

	            return new $name($this);
	        }
	    }
	}
