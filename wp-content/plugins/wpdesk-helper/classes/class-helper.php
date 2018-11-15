<?php

/**
 * Class WPDesk_Helper
 */
class WPDesk_Helper extends \WPDesk\PluginBuilder\Plugin\AbstractPlugin {

	use \WPDesk\PluginBuilder\Plugin\HookableParent;
	use \WPDesk\PluginBuilder\Plugin\TemplateLoad;

	const PRIORITY_LAST = 9999999;

	/**
	 * Scripts version.
	 *
	 * @var string
	 */
	private $scripts_version = '2';

	/**
	 * Upgrade URL.
	 *
	 * @var string
	 */
	protected $upgrade_url = 'https://www.wpdesk.pl';

	/**
	 * Upgrade URL .pl.
	 *
	 * @var string
	 */
	protected $upgrade_url_pl = 'https://www.wpdesk.pl';

	/**
	 * Upgrade URL .net.
	 *
	 * @var string
	 */
	protected $upgrade_url_net = 'https://www.wpdesk.net';

	/**
	 * Updater.
	 *
	 * @var WPDesk_Update_API_Check
	 */
	protected $updater = null;

	/**
	 * WPDesk_Helper constructor.
	 *
	 * @param WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		$this->plugin_info = $plugin_info;
		parent::__construct( $this->plugin_info );
		$this->wpdesk_helper_updater();
	}

	/**
	 * Init dependencies.
	 */
	private function init_dependencies() {
		$this->add_hookable( new WPDesk_Helper_Debug_Log() );
	}

	/**
	 * Init.
	 */
	public function init() {
		$this->init_base_variables();
		$this->init_dependencies();
		$this->hooks();
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		parent::hooks();

		add_action( 'plugins_loaded', [ $this, 'init_helper_plugins' ], self::PRIORITY_LAST );

		add_action( 'admin_menu', [ $this, 'admin_menu' ], 1 );
		add_action( 'admin_notices', [ $this, 'wpdesk_message' ] );
		add_action( 'admin_head', [ $this, 'admin_head' ], 999 );
		add_action( 'wp_ajax_wpdesk_api_hide_message', [ &$this, 'api_hide_message' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'css_scripts' ], 100 );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
		add_filter( 'wpdesk_tracker_notice_screens', [ $this, 'wpdesk_tracker_notice_screens' ] );

		$this->hooks_on_hookable_objects();
	}

	/**
	 * Init base variables for plugin
	 */
	public function init_base_variables() {
		$this->plugin_url = $this->plugin_info->get_plugin_url();

		$this->plugin_path   = $this->plugin_info->get_plugin_dir();
		$this->template_path = $this->plugin_info->get_text_domain();

		$this->plugin_text_domain   = $this->plugin_info->get_text_domain();
		$this->plugin_namespace     = $this->plugin_info->get_text_domain();
		$this->template_path        = $this->plugin_info->get_text_domain();
		$this->default_settings_tab = 'main';

		$this->settings_url = admin_url( 'admin.php?page=flexible-shipping-settings' );
		$this->docs_url     = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/' : 'https://www.wpdesk.net/docs/flexible-shipping-pro-woocommerce-docs/';

		$this->default_view_args = array(
			'plugin_url' => $this->get_plugin_url()
		);

	}

	/**
	 * Init helper plugins.
	 */
	public function init_helper_plugins() {
		global $wpdesk_helper_plugins;

		if ( ! isset( $wpdesk_helper_plugins ) ) {
			$wpdesk_helper_plugins = [];
		}

		foreach ( $wpdesk_helper_plugins as $key => $wpdesk_helper_plugin ) {
			$config_uri = null;
			if ( isset( $wpdesk_helper_plugin['config_uri'] ) ) {
				$config_uri = $wpdesk_helper_plugin['config_uri'];
			}
			$menu_title = $wpdesk_helper_plugin['product_id'];
			if ( isset( $wpdesk_helper_plugin['title'] ) ) {
				$menu_title = $wpdesk_helper_plugin['title'];
			}
			$wpdesk_helper_plugins[ $key ]['api_manager'] = new WPDesk_API_Manager(
				$upgrade_url = $this->upgrade_url,
				$version = $wpdesk_helper_plugin['version'],
				$name = $wpdesk_helper_plugin['plugin'],
				$product_id = $wpdesk_helper_plugin['product_id'],
				$menu_title,
				$title = $menu_title,
				$plugin_file = basename( $wpdesk_helper_plugin['plugin'] ),
				$plugin_dir = dirname( $wpdesk_helper_plugin['plugin'] ),
				$config_uri
			);
			$wpdesk_helper_plugins[ $key ]['activation_status'] = get_option(
				$wpdesk_helper_plugins[ $key ]['api_manager']->activated_key,
				'Deactivated'
			);
		}
	}

	/**
	 * @param mixed $links
	 *
	 * @return array
	 */
	public function links_filter( $links ) {
		$docs_link    = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/docs/licencje-wtyczek/' : 'https://www.wpdesk.net/docs/activate-wp-desk-plugin-licenses/';
		$support_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/support/' : 'https://www.wpdesk.net/support';

		$plugin_links = [
			'<a href="' . admin_url( 'admin.php?page=wpdesk-licenses' ) . '">' . __( 'Settings',
				'wpdesk-helper' ) . '</a>',
			'<a href="' . $docs_link . '">' . __( 'Docs', 'wpdesk-helper' ) . '</a>',
			'<a href="' . $support_link . '">' . __( 'Support', 'wpdesk-helper' ) . '</a>',
		];

		return array_merge( $plugin_links, $links );
	}

	public function admin_menu() {
		global $wpdesk_helper_plugins;

		$counter     = '';
		$wpdesk_data = [];
		try {
			$wpdesk_data = $this->wpdesk_api_get_plugins();
		} catch ( Exception $e ) {
			WPDesk_Logger_Factory::log_exception( $e );
		}
		if ( isset( $wpdesk_data['message'] ) && $wpdesk_data['message'] != '' ) {
			$wpdesk_api_message_close = get_option( 'wpdesk_api_message_close', '0' );
			if ( md5( $wpdesk_data['message'] ) != $wpdesk_api_message_close ) {
				$counter = " <span id='wpdesk_helper_message_counter' class='wpdesk-update-plugins update-plugins count-1' title=''><span class='update-count'>1</span></span>";
			}
		}
		$wpdesk_data            = [];
		$wpdesk_data['plugins'] = [];
		add_menu_page( 'WP Desk', 'WP Desk' . $counter, 'manage_options', 'wpdesk-helper', [ $this, 'wpdesk_page' ],
			'dashicons-controls-play', 99.99941337 );
		add_submenu_page( 'wpdesk-helper',
			__( 'Licenses', 'wpdesk-helper' ),
			__( 'Licenses', 'wpdesk-helper' ),
			'manage_options',
			'wpdesk-licenses',
			[ $this, 'wpdesk_licenses' ]
		);
		add_submenu_page( 'wpdesk-helper',
			__( 'Settings', 'wpdesk-helper' ),
			__( 'Settings', 'wpdesk-helper' ),
			'manage_options',
			'wpdesk-helper-settings',
			[ $this, 'wpdesk_helper_settings' ]
		);
	}

	public function admin_init() {

		register_setting( 'wpdesk_helper_options', 'wpdesk_helper_options' );
		add_settings_section( 'wpdesk_helper_debug', __( 'Debug', 'wpdesk-helper' ), null, 'wpdesk_helper' );
		add_settings_field( 'debug_log', __( 'WP Desk Debug Log', 'wpdesk-helper' ),
			[ $this, 'wpdesk_helper_debug_log' ], 'wpdesk_helper', 'wpdesk_helper_debug' );

		if ( should_enable_wpdesk_tracker() ) {
			add_settings_section( 'wpdesk_helper_tracking', __( 'Plugin usage tracking', 'wpdesk-helper' ), null,
				'wpdesk_helper' );
			add_settings_field( 'wpdesk_tracker_agree',
				__( 'Allow WP Desk to track plugin usage', 'wpdesk-helper' ), [
					$this,
					'wpdesk_helper_wpdesk_tracker_agree'
				], 'wpdesk_helper', 'wpdesk_helper_tracking' );
		}

	}

	function wpdesk_helper_wpdesk_tracker_agree() {
		$options = get_option( 'wpdesk_helper_options', [] );
		if ( ! is_array( $options ) ) {
			$options = [];
		}
		if ( empty( $options['wpdesk_tracker_agree'] ) ) {
			$options['wpdesk_tracker_agree'] = '0';
		}
		?>
		<input type="checkbox" id="wpdesk_helper_options[wpdesk_tracker_agree]"
		       name="wpdesk_helper_options[wpdesk_tracker_agree]" value="1" <?php checked( 1,
			$options['wpdesk_tracker_agree'], true ); ?>>
		<label for="wpdesk_helper_options[wpdesk_tracker_agree]"><?php _e( 'Enable', 'wpdesk-helper' ); ?></label>
		<p class="description" id="admin-email-description">
			<?php
			$terms_url = get_locale() == 'pl_PL' ? 'https://www.wpdesk.pl/dane-uzytkowania/' : 'https://www.wpdesk.net/usage-tracking/';
			printf( __( 'No sensitive data is tracked, %sread more%s.', 'wpdesk-helper' ),
				'<a target="_blank" href="' . $terms_url . '">', '</a>' );
			?>
		</p>
		<?php
	}

	function wpdesk_helper_debug_log() {
		$options = get_option( 'wpdesk_helper_options', [] );
		if ( ! is_array( $options ) ) {
			$options = [];
		}
		if ( empty( $options['debug_log'] ) ) {
			$options['debug_log'] = '0';
		}
		?>
		<input type="checkbox" id="wpdesk_helper_options[debug_log]" name="wpdesk_helper_options[debug_log]"
		       value="1" <?php checked( 1, $options['debug_log'], true ); ?>>
		<label for="wpdesk_helper_options[debug_log]"><?php _e( 'Enable', 'wpdesk-helper' ); ?></label>
		<p class="description" id="admin-email-description">
			<?php echo sprintf( __( 'Writes error log to %s.', 'wpdesk-helper' ),
				'<a target="_blank" href="' . content_url( 'uploads/wpdesk-logs/wpdesk_debug.log' ) . '">' . content_url( 'uploads/wpdesk-logs/wpdesk_debug.log' ) . '</a>' ); ?>
		</p>
		<?php
	}

	public function wpdesk_helper_settings() {
		?>
		<div class="wrap">
			<h1><?php _e( 'WP Desk Helper Settings', 'wpdesk-helper' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'wpdesk_helper_options' );
				do_settings_sections( 'wpdesk_helper' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * @return array|bool|mixed|object
	 * @throws Exception
	 */
	public function wpdesk_api_get_plugins() {
		if ( get_locale() != 'pl_PL' ) {
			$this->upgrade_url = $this->upgrade_url_net;
		}
		$transient_name = 'wpdesk_api_response' . get_locale();
		$ret            = get_transient( $transient_name );
		if ( isset( $_GET['refresh'] ) ) {
			delete_transient( $transient_name );
			delete_option( 'wpdesk_api_message_close' );
			$ret = false;
		}
		if ( $ret === false ) {
			$url      = trailingslashit( $this->upgrade_url ) . '?wpdesk_api=1&t=1';
			$response = wp_remote_get( $url, [ 'timeout' => 30, 'sslverify' => false ] );
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}
			if ( $response['response']['code'] != 200 ) {
				throw new Exception( sprintf( __( 'Invalid response from WP Desk server: %s', 'wpdesk-helper' ),
					$response['response']['code'] . ' - ' . $response['response']['message'] ) );
			}
			// uncomment bellow to throw error
			//$response['body'] = '<html><body>error</body></html>';
			$ret = json_decode( $response['body'], true );
			if ( ! $ret ) {
				throw new Exception( sprintf(
					__( '%sInvalid response: <pre>%s</pre>', 'wpdesk-helper' ),
					'<br/>',
					htmlentities( $response['body'] )
				) );
			}
			set_transient( $transient_name, $ret, DAY_IN_SECONDS );
		}

		return $ret;
	}

	public function wpdesk_page() {
		global $wpdesk_installed_plugins;
		try {
			$wpdesk_plugins         = $this->wpdesk_api_get_plugins();
			$wp_plugins             = get_plugins();
			$wpdesk_plugins_plugins = [];
			foreach ( $wpdesk_plugins['plugins'] as $key => $plugin ) {
				$found = false;
				foreach ( $wp_plugins as $wp_key => $wp_plugin ) {
					if ( $wp_plugin['PluginURI'] == $plugin['url'] ) {
						$found = true;
						break;
					}
				}
				if ( $found ) {
					$wpdesk_plugins_plugins[] = $plugin;
					unset( $wpdesk_plugins['plugins'][ $key ] );
				}
			}
			foreach ( $wpdesk_plugins_plugins as $plugin ) {
				$wpdesk_plugins['plugins'][] = $plugin;
			}
		} catch ( Exception $e ) {
			WPDesk_Logger_Factory::log_exception( $e );
			$wpdesk_plugins = $e->getMessage();
			$url = trailingslashit( $this->upgrade_url ) . '?wpdesk_api=1&t=1';
		}

		include 'views/wpdesk-page.php';
	}

	public function wpdesk_licenses() {
		global $wpdesk_helper_plugins;
		if ( ! isset( $wpdesk_helper_plugins ) ) {
			$wpdesk_helper_plugins = [];
		}

		if ( isset( $_POST['plugin'] ) ) {
			$plugin = false;
			foreach ( $wpdesk_helper_plugins as $plugin_key => $wpdesk_helper_plugin ) {
				if ( $wpdesk_helper_plugin['plugin'] == $_POST['plugin'] ) {
					$plugin = $wpdesk_helper_plugin;
				}
			}
			if ( $plugin ) {
				if ( $_POST['action'] == 'activate' ) {
					$activation_email = $_POST['activation_email'];
					$api_key          = $_POST['api_key'];
					$args             = [
						'email'       => $activation_email,
						'licence_key' => $api_key,
					];

					$plugin['api_manager']->upgrade_url = $this->upgrade_url_pl;
					$activate_results                   = json_decode( $plugin['api_manager']->key()->activate( $args ),
						true );
					$activated                          = false;
					if ( $activate_results['activated'] === true ) {
						add_settings_error( 'activate_text', 'activate_msg',
							__( 'Plugin activated. ', 'wpdesk-helper' ) . "{$activate_results['message']}.",
							'updated' );
						update_option( $plugin['api_manager']->activated_key, 'Activated' );

						$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]          = $api_key;
						$plugin['api_manager']->options[ $plugin['api_manager']->activation_email ] = $activation_email;
						update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
						$activated = true;
						update_option( $plugin['api_manager']->upgrade_url_key,
							$plugin['api_manager']->upgrade_url );
					} else {
						$this->upgrade_url                  = $this->upgrade_url_net;
						$plugin['api_manager']->upgrade_url = $this->upgrade_url_net;

						$activate_results = json_decode( $plugin['api_manager']->key()->activate( $args ), true );
						if ( $activate_results['activated'] === true ) {
							add_settings_error( 'activate_text', 'activate_msg',
								__( 'Plugin activated. ', 'wpdesk-helper' ) . "{$activate_results['message']}.",
								'updated' );
							update_option( $plugin['api_manager']->activated_key, 'Activated' );

							$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]          = $api_key;
							$plugin['api_manager']->options[ $plugin['api_manager']->activation_email ] = $activation_email;
							update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
							$activated = true;
							update_option( $plugin['api_manager']->upgrade_url_key,
								$plugin['api_manager']->upgrade_url );
						}
					}

					if ( $activate_results == false ) {
						add_settings_error( 'api_key_check_text', 'api_key_check_error',
							__( 'Connection failed to the License Key API server. Try again later.',
								'wpdesk-helper' ), 'error' );
						$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]          = '';
						$plugin['api_manager']->options[ $plugin['api_manager']->activation_email ] = '';
						update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
						update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
					}

					if ( ! $activated && isset( $activate_results['code'] ) ) {

						if ( ! isset( $activate_results['additional info'] ) ) {
							$activate_results['additional info'] = '';
						}

						switch ( $activate_results['code'] ) {
							case '100':
								add_settings_error( 'api_email_text', 'api_email_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$plugin['api_manager']->options[ $plugin['api_manager']->activation_email ] = '';
								$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]          = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '101':
								add_settings_error( 'api_key_text', 'api_key_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$plugin['api_manager']->options[ $plugin['api_manager']->activation_email ] = '';
								$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]          = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '102':
								add_settings_error( 'api_key_purchase_incomplete_text',
									'api_key_purchase_incomplete_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$plugin['api_manager']->options[ $plugin['api_manager']->activation_email ] = '';
								$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]          = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '103':
								add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$plugin['a_manager']->options[ $plugin['api_manager']->activation_email ] = '';
								$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]        = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '104':
								add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$plugin['api_manager']->options[ $plugin['api_manager']->activation_email ] = '';
								$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]          = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '105':
								add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$plugin['api_manager']->options[ $plugin['api_manager']->activation_email ] = '';
								$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]          = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '106':
								add_settings_error( 'sub_not_active_text', 'sub_not_active_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$plugin['api_manager']->options[ $plugin['api_manager']->activation_email ] = '';
								$plugin['api_manager']->options[ $plugin['api_manager']->api_key ]          = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
						}
					}

					$this->init_helper_plugins();

				}

				if ( $_POST['action'] == 'deactivate' ) {

					$args             = [
						'email'       => $plugin['api_manager']->options[ $plugin['api_manager']->activation_email ],
						'licence_key' => $plugin['api_manager']->options[ $plugin['api_manager']->api_key ],
					];
					$activate_results = json_decode( $plugin['api_manager']->key()->deactivate( $args ), true );
					// Used to display results for development
					//print_r($activate_results); exit();
					$deactivated = false;
					if ( $activate_results['deactivated'] === true ) {
						$update = [
							$plugin['api_manager']->api_key          => '',
							$plugin['api_manager']->activation_email => ''
						];

						$merge_options = array_merge( $plugin['api_manager']->options, $update );

						update_option( $plugin['api_manager']->data_key, $merge_options );

						update_option( $plugin['api_manager']->activated_key, 'Deactivated' );

						delete_option( $plugin['api_manager']->upgrade_url_key );

						add_settings_error( 'wc_am_deactivate_text', 'deactivate_msg',
							__( 'Plugin license deactivated. ',
								'wpdesk-helper' ) . "{$activate_results['activations_remaining']}.", 'updated' );

						$deactivated = true;

					}

					if ( ! $deactivated && isset( $activate_results['code'] ) ) {

						switch ( $activate_results['code'] ) {
							case '100':
								add_settings_error( 'api_email_text', 'api_email_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$options[ $plugin['api_manager']->activation_email ] = '';
								$options[ $plugin['api_manager']->api_key ]          = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '101':
								add_settings_error( 'api_key_text', 'api_key_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$options[ $plugin['api_manager']->api_key ]          = '';
								$options[ $plugin['api_manager']->activation_email ] = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '102':
								add_settings_error( 'api_key_purchase_incomplete_text',
									'api_key_purchase_incomplete_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$options[ $plugin['api_manager']->api_key ]          = '';
								$options[ $plugin['api_manager']->activation_email ] = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '103':
								add_settings_error( 'api_key_exceeded_text', 'api_key_exceeded_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$options[ $plugin['api_manager']->api_key ]          = '';
								$options[ $plugin['api_manager']->activation_email ] = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '104':
								add_settings_error( 'api_key_not_activated_text', 'api_key_not_activated_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$options[ $plugin['api_manager']->api_key ]          = '';
								$options[ $plugin['api_manager']->activation_email ] = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '105':
								add_settings_error( 'api_key_invalid_text', 'api_key_invalid_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$options[ $plugin['api_manager']->api_key ]          = '';
								$options[ $plugin['api_manager']->activation_email ] = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
							case '106':
								add_settings_error( 'sub_not_active_text', 'sub_not_active_error',
									"{$activate_results['error']}. {$activate_results['additional info']}",
									'error' );
								$options[ $plugin['api_manager']->api_key ]          = '';
								$options[ $plugin['api_manager']->activation_email ] = '';
								update_option( $plugin['api_manager']->data_key, $plugin['api_manager']->options );
								update_option( $plugin['api_manager']->activated_key, 'Deactivated' );
								break;
						}

					}

					$this->init_helper_plugins();
				}
			}
		}

		require_once( 'class-wpdesk-helper-list-table.php' );

		include 'views/licenses.php';

		//$args = [ 'wpdesk_helper_plugins' => $wpdesk_helper_plugins ];
		//echo $this->load_template( 'licenses', '', $args );
	}

	function wpdesk_message() {
		$currentScreen = get_current_screen();
		if ( $currentScreen->id == 'toplevel_page_wpdesk-helper' ) {
			try {
				$wpdesk_data = $this->wpdesk_api_get_plugins();
			} catch ( Exception $e ) {
				WPDesk_Logger_Factory::log_exception( $e );
				$wpdesk_data = [];
			}
			if ( isset( $wpdesk_data['message'] ) && $wpdesk_data['message'] != '' ) {
				$wpdesk_api_message_close = get_option( 'wpdesk_api_message_close', '0' );
				if ( md5( $wpdesk_data['message'] ) != $wpdesk_api_message_close ) {
					?>
					<div id="wpdesk-dismiss" class="updated notice is-dismissible">
						<p><?php echo $wpdesk_data['message']; ?></p>
						<span id="wpdesk-api-ajax-notification-nonce"
						      class="hidden"><?php echo wp_create_nonce( 'wpdesk-api-ajax-notification-nonce' ); ?></span>
					</div>
					<script type="text/javascript">
                        jQuery(document).ready(function () {
                            jQuery('#wpdesk-dismiss.is-dismissible').on('click', '.notice-dismiss', function (event) {
                                jQuery.post(ajaxurl, {
                                        action: 'wpdesk_api_hide_message',
                                        nonce: jQuery.trim(jQuery('#wpdesk-api-ajax-notification-nonce').text()),
                                        value: '<?php echo md5( $wpdesk_data['message'] ); ?>'
                                    },
                                    function (response) {
                                        if (response == 1) {
                                        }
                                    });
                                jQuery('#wpdesk_helper_message_counter').css('display', 'none');
                            });
                        });
					</script>
					<?php
				}
			}
		}

	}

	function api_hide_message() {
		if ( wp_verify_nonce( $_REQUEST['nonce'], 'wpdesk-api-ajax-notification-nonce' ) ) {
			if ( update_option( 'wpdesk_api_message_close', $_REQUEST['value'] ) ) {
				die( '1' );
			} else {
				die( '0' );
			}
		}
	}

	function admin_head() {
		?>
		<style>
			li.wp-first-item .wpdesk-update-plugins {
				display: none !important;
			}
		</style>
		<?php
	}

	public function wpdesk_helper_updater() {
		require_once( 'class-wc-plugin-update.php' );
		$this->updater = new WPDesk_Update_API_Check(
			null,
			$this->upgrade_url,
			'wpdesk-helper/wpdesk-helper.php',
			'WPDesk Helper',
			$api_key = null,
			$activation_email = null,
			$renew_license_url = null,
			$instance = null,
			$domain = null,
			WPDesk_Helper,
			$plugin_or_theme = 'plugin',
			$text_domain = null,
			$extra = null,
			$free = true
		);
	}

	// Loads admin style sheets
	public function css_scripts() {
		$screen = get_current_screen();
		if ( $screen->base == 'toplevel_page_wpdesk-helper' || $screen->base == 'wp-desk_page_wpdesk-licenses' || $screen->base == 'wp-desk-1_page_wpdesk-licenses' ) {
			wp_register_style( 'wpdesk-helper', plugins_url( 'wpdesk-helper/assets/css/admin-settings.css' ), [],
				$this->scripts_version, 'all' );
			wp_enqueue_style( 'wpdesk-helper' );
		}
	}

	public static function wpdesk_tracker_notice_screens( $screens ) {
		$screens[] = 'toplevel_page_wpdesk-helper';
		$screens[] = 'wp-desk_page_wpdesk-licenses';
		$screens[] = 'wp-desk_page_wpdesk-helper-settings';

		return $screens;
	}
}
