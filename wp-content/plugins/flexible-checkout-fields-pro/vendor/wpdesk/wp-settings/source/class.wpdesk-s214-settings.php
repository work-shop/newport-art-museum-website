<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * WP Desk Section214 settings handler class
 *
 * @since       1.0.0
 */
class WPDesk_S214_Settings extends S214_Settings {

	const CUSTOM_ATTRIBUTES_KEY = 'custom_attributes';
	const CLASS_KEY = 'class';

	/**
	 * @var         string $version The settings class version
	 * @since       1.0.0
	 */
	public $version = '1.3';


	/**
	 * @var         string
	 */
	private $text_domain;


	/**
	 * Get things started
	 *
	 * @access      public
	 * @since       1.0.1
	 *
	 * @param       string $slug The plugin slug
	 * @param       string $default_tab The default settings tab to display
	 *
	 * @return      void
	 */
	public function __construct( $url_path, $slug = 'wpdesk-settings', $default_tab = 'general' ) {
		parent::__construct( $slug, $default_tab );
		$this->url_path = $url_path;
		global ${$this->func . '_options'};
		${$this->func . '_options'} = $this->get_settings();


	}

	/**
	 * Add settings pages
	 *
	 * @access      public
	 * @since       1.0.0
	 * @global      string ${this->func . '_settings_page'} The settings page slug
	 * @return      void
	 */
	public function add_settings_page() {
		global ${$this->func . '_settings_page'};

		$menu = apply_filters( $this->func . '_menu', array(
			'type'       => 'menu',
			'parent'     => 'options-general.php',
			'page_title' => __( 'Section214 Settings', 's214' ),
			'show_title' => false,
			'menu_title' => __( 'Section214 Settings', 's214' ),
			'capability' => 'manage_options',
			'icon'       => '',
			'position'   => null
		) );

		$this->show_title = $menu['show_title'];
		$this->page_title = $menu['page_title'];

		if ( $menu['type'] === 'submenu' ) {
			${$this->func . '_settings_page'} = add_submenu_page( $menu['parent'], $menu['page_title'],
				$menu['menu_title'], $menu['capability'], $this->slug . '-settings',
				array( $this, 'render_settings_page' ) );
		} else {
			${$this->func . '_settings_page'} = add_menu_page( $menu['page_title'], $menu['menu_title'],
				$menu['capability'], $this->slug . '-settings', array( $this, 'render_settings_page' ), $menu['icon'],
				$menu['position'] );
		}
	}


	/**
	 * Render settings page
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	public function render_settings_page() {
		$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'],
			$this->get_settings_tabs() ) ? $_GET['tab'] : $this->default_tab;
		$sections   = $registered_sections = $this->get_settings_tab_sections( $active_tab );
		$key        = 'main';

		if ( is_array( $sections ) ) {
			$key = key( $sections );
		}

		$section = isset( $_GET['section'] ) && ! empty( $registered_sections ) && array_key_exists( $_GET['section'],
			$registered_sections ) ? $_GET['section'] : $key;

		ob_start();
		?>
        <div class="wrap">
			<?php if ( $this->show_title ) { ?>
                <h2><?php echo $this->page_title; ?></h2>
			<?php } ?>
            <h2 class="nav-tab-wrapper">
				<?php
				foreach ( $this->get_settings_tabs() as $tab_id => $tab_name ) {
					$tab_url = add_query_arg( array(
						'settings-updated' => false,
						'tab'              => $tab_id
					) );

					// Remove the section from the tabs so we always end up at the main section
					$tab_url = remove_query_arg( 'section', $tab_url );

					$active = $active_tab === $tab_id ? ' nav-tab-active' : '';

					echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">' . esc_html( $tab_name ) . '</a>';
				}
				?>
            </h2>
			<?php
			$number_of_sections = count( $sections );
			$number             = 0;

			if ( $number_of_sections > 1 ) {
				echo '<div><ul class="subsubsub">';

				foreach ( $sections as $section_id => $section_name ) {
					echo '<li>';

					$number ++;
					$tab_url = add_query_arg( array(
						'settings-updated' => false,
						'tab'              => $active_tab,
						'section'          => $section_id
					) );
					$class   = '';

					if ( $section === $section_id ) {
						$class = 'current';
					}

					echo '<a class="' . $class . '" href="' . esc_url( $tab_url ) . '">' . $section_name . '</a>';

					if ( $number != $number_of_sections ) {
						echo ' | ';
					}

					echo '</li>';
				}

				echo '</ul></div>';
			}
			?>
            <div id="tab_container">
                <form method="post" action="options.php">
                    <table class="form-table">
						<?php
						settings_fields( $this->func . '_settings' );

						do_action( $this->func . '_settings_tab_top_' . $active_tab . '_' . $section );
						do_settings_sections( $this->func . '_settings_' . $active_tab . '_' . $section );
						do_action( $this->func . '_settings_tab_bottom_' . $active_tab . '_' . $section );
						?>
                    </table>
					<?php
					if ( ! in_array( $active_tab, apply_filters( $this->func . '_unsavable_tabs', array() ) ) ) {
						submit_button();
					}
					?>
                </form>
            </div>
        </div>
		<?php
		echo ob_get_clean();
	}


	/**
	 * Retrieve the settings tabs
	 *
	 * @access      private
	 * @since       1.0.0
	 * @return      array $tabs The registered tabs for this plugin
	 */
	private function get_settings_tabs() {
		return apply_filters( $this->func . '_settings_tabs', array() );
	}

	/**
	 * Retrieve an option
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       string $key The key to retrieve
	 * @param       mixed $default The default value if key doesn't exist
	 *
	 * @return      mixed $value The value to return
	 */
	public function get_option( $key = '', $default = false ) {
		$option = $this->get_global_options();

		$value = ! empty( $option[ $key ] ) ? $option[ $key ] : $default;
		$value = apply_filters( $this->func . '_get_option', $value, $key, $default );

		return apply_filters( $this->func . '_get_option_' . $key, $value, $key, $default );
	}

	/**
	 * Get shared options
	 *
	 * @global array ${$this->func . '_options'} The plugin options
	 * @return mixed
	 */
	private function get_global_options() {
		global ${$this->func . '_options'};

		return ${$this->func . '_options'};
	}

	/**
	 * Update an option
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       string $key The key to update
	 * @param       mixed $value The value to set key to
	 *
	 * @return      bool true if updated, false otherwise
	 */
	public function update_option( $key = '', $value = false ) {
		// Bail if no key is set
		if ( empty( $key ) ) {
			return false;
		}

		if ( empty( $value ) ) {
			$remove_option = $this->delete_option( $key );

			return $remove_option;
		}

		// Fetch a clean copy of the options array
		$options = get_option( $this->func . '_settings' );

		// Allow devs to modify the value
		$value = apply_filters( $this->func . '_update_option', $value, $key );

		// Try to update the option
		$options[ $key ] = $value;
		$did_update      = update_option( $this->func . '_settings', $options );

		// Update the global
		if ( $did_update ) {
			$global_option         = $this->get_global_options();
			$global_option[ $key ] = $value;
			$this->set_global_options( $global_option );
		}

		return $did_update;
	}


	/**
	 * Delete an option
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       string $key The key to delete
	 *
	 * @return      bool true if deleted, false otherwise
	 */
	public function delete_option( $key = '' ) {
		// Bail if no key is set
		if ( empty( $key ) ) {
			return false;
		}

		// Fetch a clean copy of the options array
		$options = get_option( $this->func . '_settings' );

		// Try to unset the option
		if ( isset( $options[ $key ] ) ) {
			unset( $options[ $key ] );
		}

		$did_update = update_option( $this->func . '_settings', $options );

		// Update the global
		if ( $did_update ) {
			$this->set_global_options( $options );
		}

		return $did_update;
	}

	/**
	 * @param array $option
	 *
	 * @global array ${$this->func . '_options'} The plugin options
	 */
	private function set_global_options( $option ) {
		global ${$this->func . '_options'};
		${$this->func . '_options'} = $option;
	}

	/**
	 * Retrieve all options
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      array $settings The options array
	 */
	public function get_settings() {
		$settings = get_option( $this->func . '_settings' );

		if ( empty( $settings ) ) {
			$settings = array();

			update_option( $this->func . '_settings', $settings );
		}

		return apply_filters( $this->func . '_get_settings', $settings );
	}

	/**
	 * Add settings sections and fields
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      void
	 */
	function register_settings() {
		if ( get_option( $this->func . '_settings' ) === false ) {
			add_option( $this->func . '_settings' );
		}

		foreach ( $this->get_registered_settings() as $tab => $sections ) {
			foreach ( $sections as $section => $settings ) {
				// Check for backwards compatibility
				$section_tabs = $this->get_settings_tab_sections( $tab );

				if ( ! is_array( $section_tabs ) || ! array_key_exists( $section, $section_tabs ) ) {
					$section  = 'main';
					$settings = $sections;
				}

				add_settings_section(
					$this->func . '_settings_' . $tab . '_' . $section,
					__return_null(),
					'__return_false',
					$this->func . '_settings_' . $tab . '_' . $section
				);

				foreach ( $settings as $option ) {
					// For backwards compatibility
					if ( empty( $option['id'] ) ) {
						continue;
					}

					$name = isset( $option['name'] ) ? $option['name'] : '';

					add_settings_field(
						$this->func . '_settings[' . $option['id'] . ']',
						$name,
						function_exists( $this->func . '_' . $option['type'] . '_callback' ) ? $this->func . '_' . $option['type'] . '_callback' : ( method_exists( $this,
							$option['type'] . '_callback' ) ? array(
							$this,
							$option['type'] . '_callback'
						) : array( $this, 'missing_callback' ) ),
						$this->func . '_settings_' . $tab . '_' . $section,
						$this->func . '_settings_' . $tab . '_' . $section,
						$this->prepare_setting_args( $section, $option )
					);
				}
			}
		}

		register_setting( $this->func . '_settings', $this->func . '_settings', array( $this, 'settings_sanitize' ) );
	}

	/**
	 * Retrieve the plugin settings
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      array $settings The plugin settings
	 */
	public function get_registered_settings() {
		return apply_filters( $this->func . '_registered_settings', array() );
	}

	/**
	 * Parses html parameters from $option
	 *
	 * @param string $section
	 * @param array $option
	 *
	 * @return array
	 */
	private function prepare_setting_args( $section, array $option ) {
		$args = array(
			'section'       => $section,
			'id'            => isset( $option['id'] ) ? $option['id'] : null,
			'desc'          => ! empty( $option['desc'] ) ? $option['desc'] : '',
			'name'          => isset( $option['name'] ) ? $option['name'] : null,
			'size'          => isset( $option['size'] ) ? $option['size'] : null,
			'options'       => isset( $option['options'] ) ? $option['options'] : '',
			'std'           => isset( $option['std'] ) ? $option['std'] : '',
			'min'           => isset( $option['min'] ) ? $option['min'] : null,
			'max'           => isset( $option['max'] ) ? $option['max'] : null,
			'step'          => isset( $option['step'] ) ? $option['step'] : null,
			'select2'       => isset( $option['select2'] ) ? $option['select2'] : null,
			'placeholder'   => isset( $option['placeholder'] ) ? $option['placeholder'] : null,
			'multiple'      => isset( $option['multiple'] ) ? $option['multiple'] : null,
			'allow_blank'   => isset( $option['allow_blank'] ) ? $option['allow_blank'] : true,
			'readonly'      => isset( $option['readonly'] ) ? $option['readonly'] : false,
			'buttons'       => isset( $option['buttons'] ) ? $option['buttons'] : null,
			'wpautop'       => isset( $option['wpautop'] ) ? $option['wpautop'] : null,
			'teeny'         => isset( $option['teeny'] ) ? $option['teeny'] : null,
			'tab'           => isset( $option['tab'] ) ? $option['tab'] : null,
			'tooltip_title' => isset( $option['tooltip_title'] ) ? $option['tooltip_title'] : false,
			'tooltip_desc'  => isset( $option['tooltip_desc'] ) ? $option['tooltip_desc'] : false,

			'available_header' => isset( $option['available_header'] ) ? $option['available_header'] : null,
			'selected_header'  => isset( $option['selected_header'] ) ? $option['selected_header'] : null,
		);

		if ( isset( $option[ self::CUSTOM_ATTRIBUTES_KEY ] ) ) {
			$args[ self::CUSTOM_ATTRIBUTES_KEY ] = $option[ self::CUSTOM_ATTRIBUTES_KEY ];
		}

		if ( isset( $option[ self::CLASS_KEY ] ) ) {
			$args[ self::CLASS_KEY ] = $this->prepare_class_arg( $option[ self::CLASS_KEY ], $args['id'] );
		}

		return $args;
	}

	/**
	 * Prepare class arg.
	 *
	 * @param string|array $class_option Class option.
	 * @param null|string $id id.
	 *
	 * @return string
	 */
	private function prepare_class_arg( $class_option, $id ) {
		$class_arg = '';
		if ( $id != null ) {
			$class_arg .= $id . ' ';
		}
		if ( is_array( $class_option ) ) {
			$class_arg .= implode( ' ', $class_option );
		} else {
			$class_arg .= $class_option;
		}

		return trim( $class_arg );
	}

	/**
	 * Checkbox callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function checkbox_callback( $args ) {
		$options                = $this->get_global_options();
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args );

		$name    = ' name="' . $this->func . '_settings[' . $args['id'] . ']"';
		$checked = isset( $options[ $args['id'] ] ) ? checked( 1, $options[ $args['id'] ], false ) : '';

		$html = '<input type="hidden"' . $name . ' value="-1" />';
		$html .= '<input ' . $custom_attributes_html . $class_html . ' type="checkbox" id="' . $this->func . '_settings[' . $args['id'] . ']"' . $name . ' value="1" ' . $checked . '/>&nbsp;';
		$html = $this->append_description_html( $html, $args );
		echo $this->apply_after_setting_output( $html, $args );
	}

	/**
	 * Get custom attributes.
	 *
	 * @param  array $args Field data.
	 *
	 * @return string
	 *
	 * @see https://github.com/woocommerce/woocommerce/blob/master/includes/abstracts/abstract-wc-settings-api.php
	 */
	private function get_custom_attribute_html( $args ) {
		$custom_attributes = array();
		if ( ! empty( $args[ self::CUSTOM_ATTRIBUTES_KEY ] ) && is_array( $args[ self::CUSTOM_ATTRIBUTES_KEY ] ) ) {
			foreach ( $args[ self::CUSTOM_ATTRIBUTES_KEY ] as $attribute => $attribute_value ) {

				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		return implode( ' ', $custom_attributes );
	}


	/**
	 * Get class attribute.
	 *
	 * @param array $args Field data.
	 * @param string $additional_class Additional class.
	 *
	 * @return string
	 */
	private function get_class_html( $args, $additional_class = '' ) {
		$class_attribute_value = '';
		if ( ! empty( $args[ self::CLASS_KEY ] ) ) {
			$class_attribute_value = $args[ self::CLASS_KEY ];
		}
		if ( $additional_class !== '' ) {
			$class_attribute_value .= ' ' . $additional_class;
		}
		if ( $class_attribute_value !== '' ) {
			return ' class="' . trim( $class_attribute_value ) . '" ';
		} else {
			return '';
		}
	}

	/**
	 * @param string $html
	 * @param array $args
	 *
	 * @return string
	 */
	private function append_description_html( $html, $args ) {
		return $html . '<span class="description"><label for="' . $this->func . '_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';
	}

	/**
	 * @param string $html
	 * @param array $args
	 *
	 * @return string
	 */
	private function apply_after_setting_output( $html, $args ) {
		return apply_filters( $this->func . '_after_setting_output', $html, $args );
	}

	/**
	 * Color callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the settings
	 *
	 * @return      void
	 */
	public function color_callback( $args ) {
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args );
		$value                  = $this->get_std_input_value( $args );

		$default = isset( $args['std'] ) ? $args['std'] : '';

		$html = '<input ' . $custom_attributes_html . $class_html . ' type="text" class="s214-color-picker" id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />&nbsp;';
		$html .= '<span class="s214-color-picker-label description"><label for="' . $this->func . '_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

		echo $this->apply_after_setting_output( $html, $args );
	}

	/**
	 * @param array $args
	 *
	 * @return string
	 */
	private function get_std_input_value( $args ) {
		$options = $this->get_global_options();
		if ( isset( $options[ $args['id'] ] ) ) {
			return $options[ $args['id'] ];
		} else {
			return isset( $args['std'] ) ? $args['std'] : '';
		}
	}

	/**
	 * Descriptive text callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function descriptive_text_callback( $args ) {
		$html = wp_kses_post( $args['desc'] );

		echo $this->apply_after_setting_output( $html, $args );
	}

	/**
	 * HTML callback
	 *
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function html_callback( $args ) {
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args, 'large-text s214-html' );
		$value                  = $this->get_std_input_value( $args );

		$html = '<textarea ' . $custom_attributes_html . $class_html . ' cols="50" rows="5" id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>&nbsp;';
		$html = $this->append_description_html( $html, $args );
		echo $this->apply_after_setting_output( $html, $args );
	}

	/**
	 * Multicheck callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function multicheck_callback( $args ) {
		$options = $this->get_global_options();

		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args );

		if ( ! empty( $args['options'] ) ) {
			$html = '';

			foreach ( $args['options'] as $key => $option ) {
				if ( isset( $options[ $args['id'] ][ $key ] ) ) {
					$enabled = $option;
				} else {
					$enabled = isset( $args['std'][ $key ] ) ? $args['std'][ $key ] : null;
				}

				$html .= '<input ' . $custom_attributes_html . $class_html . ' name="' . $this->func . '_settings[' . $args['id'] . '][' . $key . ']" id="' . $this->func . '_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option,
						$enabled, false ) . ' />&nbsp;';
				$html .= '<label for="' . $this->func . '_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
			}
			$html .= '<p class="description">' . $args['desc'] . '</p>';

			echo $this->apply_after_setting_output( $html, $args );
		}
	}

	/**
	 * Number callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function number_callback( $args ) {
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args );
		$value                  = $this->get_std_input_value( $args );

		$name     = ' name="' . $this->func . '_settings[' . $args['id'] . ']"';
		$max      = isset( $args['max'] ) ? $args['max'] : 999999;
		$min      = isset( $args['min'] ) ? $args['min'] : 0;
		$step     = isset( $args['step'] ) ? $args['step'] : 1;
		$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';

		$html = '<input ' . $custom_attributes_html . $class_html . ' type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="' . $this->func . '_settings[' . $args['id'] . ']"' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . '/>&nbsp;';
		$html = $this->append_description_html( $html, $args );
		echo $this->apply_after_setting_output( $html, $args );
	}

	/**
	 * Password callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the settings
	 *
	 * @return      void
	 */
	public function password_callback( $args ) {
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$size                   = $this->get_size_attr( $args );
		$class_html             = $this->get_class_html( $args, $size . '-text' );
		$value                  = $this->get_std_input_value( $args );

		$html = '<input ' . $custom_attributes_html . $class_html . ' type="password" id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" />&nbsp;';
		$html = $this->append_description_html( $html, $args );
		echo $this->apply_after_setting_output( $html, $args );
	}

	/**
	 * @param array $args
	 *
	 * @return string
	 */
	private function get_size_attr( $args ) {
		return ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	}

	/**
	 * Radio callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function radio_callback( $args ) {
		$options = $this->get_global_options();

		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args );

		if ( ! empty( $args['options'] ) ) {
			$html = '';

			foreach ( $args['options'] as $key => $option ) {
				$checked = false;

				if ( isset( $options[ $args['id'] ] ) && $options[ $args['id'] ] === $key ) {
					$checked = true;
				} elseif ( isset( $args['std'] ) && $args['std'] === $key && ! isset( $options[ $args['id'] ] ) ) {
					$checked = true;
				}

				$html .= '<input ' . $custom_attributes_html . $class_html . ' name="' . $this->func . '_settings[' . $args['id'] . ']" id="' . $this->func . '_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true,
						$checked, false ) . '/>&nbsp;';
				$html .= '<label for="' . $this->func . '_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
			}

			$html .= '<p class="description">' . $args['desc'] . '</p>';

			echo $this->apply_after_setting_output( $html, $args );
		}
	}


	/**
	 * Select callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function select_callback( $args ) {
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args );
		$value                  = $this->get_std_input_value( $args );

		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
		$select2     = isset( $args['select2'] ) ? ' class="s214-select2"' : '';
		$width       = isset( $args['size'] ) ? ' style="width: ' . $args['size'] . '"' : '';

		if ( isset( $args['multiple'] ) && $args['multiple'] === true ) {
			$html = '<input type="hidden" name="' . $this->func . '_settings[' . $args['id'] . '][]" value="" />';
			$html .= '<select ' . $custom_attributes_html . $class_html . ' id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . '][]"' . $select2 . ' data-placeholder="' . $placeholder . '" multiple="multiple"' . $width . ' />';
		} else {
			$html = '<select ' . $custom_attributes_html . $class_html . ' id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . ']"' . $select2 . ' data-placeholder="' . $placeholder . '"' . $width . ' />';
		}

		foreach ( $args['options'] as $option => $name ) {
			if ( isset( $args['multiple'] ) && $args['multiple'] === true ) {
				if ( is_array( $value ) ) {
					$selected = ( in_array( $option, $value ) ? 'selected="selected"' : '' );
				} else {
					$selected = '';
				}
			} else {
				if ( is_string( $value ) ) {
					$selected = selected( $option, $value, false );
				} else {
					$selected = '';
				}
			}

			$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
		}

		$html .= '</select>&nbsp;';
		$html = $this->append_description_html( $html, $args );
		echo $this->apply_after_setting_output( $html, $args );
	}


	/**
	 * Sysinfo callback
	 *
	 * @since       1.1.0
	 *
	 * @param       array $args Arguements passed by the settings
	 *
	 * @return      void
	 */
	public function sysinfo_callback( $args ) {
		$options = $this->get_global_options();

		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args );

		if ( ! isset( $options[ $args['tab'] ] ) || ( isset( $options[ $args['tab'] ] ) && isset( $_GET['tab'] ) && $_GET['tab'] === $options[ $args['tab'] ] ) ) {
			$html = '<textarea ' . $custom_attributes_html . $class_html . ' readonly="readonly" onclick="this.focus(); this.select()" id="system-info-textarea" name="' . $this->func . '-system-info" title="' . __( 'To copy the system info, click below then press Ctrl + C (PC) or Cmd + C (Mac).',
					's214-settings' ) . '">' . $this->sysinfo->get_system_info() . '</textarea>';
			$html .= '<p class="submit">';
			$html .= '<input type="hidden" name="' . $this->slug . '-settings-action" value="download_system_info" />';
			$html .= '<a class="button button-primary" href="' . add_query_arg( $this->slug . '-settings-action',
					'download_system_info' ) . '">' . __( 'Download System Info File', 's214-settings' ) . '</a>';
			$html .= '</p>';

			echo $this->apply_after_setting_output( $html, $args );
		}
	}


	/**
	 * Text callback
	 *
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function text_callback( $args ) {
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$value                  = $this->get_std_input_value( $args );

		$name       = ' name="' . $this->func . '_settings[' . $args['id'] . ']"';
		$readonly   = $args['readonly'] === true ? ' readonly="readonly"' : '';
		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$class_html = $this->get_class_html( $args, $size . '-text' );

		$html = '<input ' . $custom_attributes_html . $class_html . ' type="text" id="' . $this->func . '_settings[' . $args['id'] . ']"' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . '/>&nbsp;';
		$html = $this->append_description_html( $html, $args );
		echo $this->apply_after_setting_output( $html, $args );
	}


	/**
	 * Textarea callback
	 *
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function textarea_callback( $args ) {
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args, 'large-text' );
		$value                  = $this->get_std_input_value( $args );

		$html = '<textarea ' . $custom_attributes_html . $class_html . ' cols="50" rows="5" id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>&nbsp;';
		$html = $this->append_description_html( $html, $args );
		echo $this->apply_after_setting_output( $html, $args );
	}


	/**
	 * Upload callback
	 *
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function upload_callback( $args ) {
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$value                  = $this->get_std_input_value( $args );
		$size                   = $this->get_size_attr( $args );
		$class_html             = $this->get_class_html( $args, $size . '-text' );

		$html = '<input ' . $custom_attributes_html . $class_html . ' type="text" id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />&nbsp;';
		$html .= '<span><input type="button" class="' . $this->func . '_settings_upload_button button-secondary" value="' . __( 'Upload File',
				's214-settings' ) . '" /></span>&nbsp;';
		$html = $this->append_description_html( $html, $args );
		echo $this->apply_after_setting_output( $html, $args );
	}


	/**
	 * License field callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @return      void
	 */
	public function license_key_callback( $args ) {
		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$value                  = $this->get_std_input_value( $args );
		$size                   = $this->get_size_attr( $args );
		$class_html             = $this->get_class_html( $args, $size . '-text' );

		$html = '<input ' . $custom_attributes_html . $class_html . ' type="text" id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" />&nbsp;';

		if ( get_option( $args['options']['is_valid_license_option'] ) ) {
			$html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . __( 'Deactivate License',
					's214-settings' ) . '"/>';
		}
		$html .= '<span class="description"><label for="' . $this->func . '_settings[' . $args['id'] . ']"> ' . $args['desc'] . '</label></span>';

		wp_nonce_field( $args['id'] . '-nonce', $args['id'] . '-nonce' );

		echo $this->apply_after_setting_output( $html, $args );
	}


	/**
	 * Check if we should load admin scripts
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       string $hook The hook for the current page
	 *
	 * @return      bool true if we should load scripts, false otherwise
	 */
	public function load_scripts( $hook ) {
		global ${$this->func . '_settings_page'};

		$ret   = false;
		$pages = apply_filters( $this->func . '_admin_pages', array( ${$this->func . '_settings_page'} ) );

		if ( in_array( $hook, $pages ) ) {
			$ret = true;
		}

		return (bool) apply_filters( $this->func . 'load_scripts', $ret );
	}

	/**
	 * Add tooltips
	 *
	 * @access      public
	 * @since       1.2.0
	 *
	 * @param       string $html The current field HTML
	 * @param       array $args Arguments passed to the field
	 *
	 * @return      string $html The updated field HTML
	 */
	function add_setting_tooltip( $html, $args ) {
		if ( ! empty( $args['tooltip_title'] ) && ! empty( $args['tooltip_desc'] ) ) {
			$tooltip = '<span alt="f223" class="s214-help-tip dashicons dashicons-editor-help" title="<strong>' . $args['tooltip_title'] . '</strong>: ' . $args['tooltip_desc'] . '"></span>';
			$html    .= $tooltip;
		}

		return $html;
	}

	private $url_path;


	public function enqueue_scripts( $hook ) {
		if ( ! apply_filters( $this->func . '_load_admin_scripts', $this->load_scripts( $hook ), $hook ) ) {
			return;
		}

		global $wp_scripts;

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		//$url_path    = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, dirname( __FILE__ ) );
		//$url_path    = $this->plugin->get_plugin_url() . 'classes/wpdesk';
		$url_path       = $this->url_path . '/vendor/wpdesk/wp-settings/source';
		$select2_cdn    = 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/';
		$cm_cdn         = 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.14.2/';
		$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'jquery-ui-tooltip' );
		wp_enqueue_media();
		wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui' . $suffix . '.css', array(), $jquery_version );

		wp_enqueue_script( 'media-upload' );
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_style( 's14-select2', $select2_cdn . 'css/select2' . $suffix . '.css', array(), '4.0.5' );
		wp_enqueue_script( 's14-select2', $select2_cdn . 'js/select2' . $suffix . '.js', array( 'jquery' ), '4.0.5' );

		wp_enqueue_style( $this->slug . '-cm', $cm_cdn . 'codemirror.css', array(), '5.10' );
		wp_enqueue_script( $this->slug . '-cm', $cm_cdn . 'codemirror.js', array( 'jquery' ), '5.14.2' );
		wp_enqueue_script( $this->slug . '-cm-html', $cm_cdn . 'mode/htmlmixed/htmlmixed.js', array(
			'jquery',
			$this->slug . '-cm'
		), '5.14.2' );
		wp_enqueue_script( $this->slug . '-cm-xml', $cm_cdn . 'mode/xml/xml.js', array(
			'jquery',
			$this->slug . '-cm'
		), '5.14.2' );
		wp_enqueue_script( $this->slug . '-cm-js', $cm_cdn . 'mode/javascript/javascript.js', array(
			'jquery',
			$this->slug . '-cm'
		), '5.14.2' );
		wp_enqueue_script( $this->slug . '-cm-css', $cm_cdn . 'mode/css/css.js', array(
			'jquery',
			$this->slug . '-cm'
		), '5.14.2' );
		wp_enqueue_script( $this->slug . '-cm-php', $cm_cdn . 'mode/php/php.js', array(
			'jquery',
			$this->slug . '-cm'
		), '5.14.2' );
		wp_enqueue_script( $this->slug . '-cm-clike', $cm_cdn . 'mode/clike/clike.js', array(
			'jquery',
			$this->slug . '-cm'
		), '5.14.2' );

		wp_enqueue_style( $this->slug, $url_path . '/assets/css/admin' . $suffix . '.css', array(), $this->version );
		wp_enqueue_style( $this->slug . '-settings', $url_path . '/assets/css/admin-settings' . $suffix . '.css', array(), $this->version );
		wp_enqueue_script( $this->slug . '-js', $url_path . '/assets/js/admin-settings' . $suffix . '.js', array( 'jquery' ), $this->version );
		wp_localize_script( $this->slug . '-js', 's214_settings_vars', apply_filters( $this->func . 'localize_script', array(
			'func'               => $this->func,
			'image_media_button' => __( 'Insert Image', 'wpdesk-plugin' ),
			'image_media_title'  => __( 'Select Image', 'wpdesk-plugin' ),
		) ) );
	}

	/**
	 * Date callback
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @global      array ${$this->func . '_options'} The Beacon options
	 * @return      void
	 */
	public function date_callback( $args ) {
		global ${$this->func . '_options'};

		$custom_attributes_html = $this->get_custom_attribute_html( $args );

		if ( isset( ${$this->func . '_options'}[ $args['id'] ] ) ) {
			$value = ${$this->func . '_options'}[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$name       = ' name="' . $this->func . '_settings[' . $args['id'] . ']"';
		$max        = isset( $args['max'] ) ? $args['max'] : 999999;
		$min        = isset( $args['min'] ) ? $args['min'] : 0;
		$step       = isset( $args['step'] ) ? $args['step'] : 1;
		$size       = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'date';
		$readonly   = $args['readonly'] === true ? ' readonly="readonly"' : '';
		$class_html = $this->get_class_html( $args, $size . '-text' );

		$html = '<input ' . $custom_attributes_html . $class_html . ' type="date" ' . '" id="' . $this->func . '_settings[' . $args['id'] . ']"' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . '/>&nbsp;';
		$html .= '<span class="description"><label for="' . $this->func . '_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

		echo apply_filters( $this->func . '_after_setting_output', $html, $args );
	}


	/**
	 * select 2 columns
	 *
	 * @access      public
	 * @since       1.0.0
	 *
	 * @param       array $args Arguments passed by the setting
	 *
	 * @global      array ${$this->func . '_options'} The Beacon options
	 * @return      void
	 */
	public function select_2_columns_callback( $args ) {
		global ${$this->func . '_options'};

		$custom_attributes_html = $this->get_custom_attribute_html( $args );
		$class_html             = $this->get_class_html( $args, 'select-2-columns' );

		if ( isset( ${$this->func . '_options'}[ $args['id'] ] ) ) {
			$value = ${$this->func . '_options'}[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$value_array = explode( ',', $value );

		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';
		$width       = isset( $args['size'] ) ? ' style="width: ' . $args['size'] . '"' : '';

		$available_header = isset( $args['available_header'] ) ? $args['available_header'] : '';

		$selected_header = isset( $args['selected_header'] ) ? $args['selected_header'] : '';

		$html = '<input type="hidden" value="' . esc_attr( $value ) . '" id="' . $this->func . '_settings[' . $args['id'] . ']" name="' . $this->func . '_settings[' . $args['id'] . ']"' . ' />';

		$html_list_available = '<div class="available-column">';
		$html_list_available .= '<strong>' . $available_header . '</strong>';
		$html_list_available .= '<ul id="' . $this->func . '_settings[' . $args['id'] . '][available]" class="available connectedSortable">';

		$html_list_selected = '<div class="selected-column">';
		$html_list_selected .= '<strong>' . $selected_header . '</strong>';
		$html_list_selected .= '<ul id="' . $this->func . '_settings[' . $args['id'] . '][selected]" class="selected connectedSortable">';

		foreach ( $value_array as $value ) {
			if ( isset( $args['options'][ $value ] ) ) {
				$option             = $value;
				$name               = $args['options'][ $value ];
				$html_list_selected .= '<li data-value="' . esc_attr( $option ) . '">' . $name . '</li>';
			}
		}

		foreach ( $args['options'] as $option => $name ) {
			if ( ! is_array( $value_array ) || ! in_array( $option, $value_array ) ) {
				$html_list_available .= '<li data-value="' . esc_attr( $option ) . '">' . $name . '</li>';
			}
		}

		$html_list_available .= '</ul></div>';
		$html_list_selected  .= '</ul></div>';

		$html .= '<div ' . $custom_attributes_html . $class_html . '>';
		$html .= $html_list_available;
		$html .= $html_list_selected;

		$html .= '<div style="clear:both;">';

		$html .= '<script type="text/javascript">';
		$html .= "\n";
		$html .= '
					 jQuery( function() {
					 	jQuery( "#' . $this->func . '_settings\\\\[' . $args['id'] . '\\\\]\\\\[available\\\\], #' . $this->func . '_settings\\\\[' . $args['id'] . '\\\\]\\\\[selected\\\\]" ).sortable({
							connectWith: ".connectedSortable",
					 		deactivate: function( event, ui ) {
					 			jQuery("#' . $this->func . '_settings\\\\[' . $args['id'] . '\\\\]").val("");
					 			var val = "";
					 			jQuery("#' . $this->func . '_settings\\\\[' . $args['id'] . '\\\\]\\\\[selected\\\\] > li").each( function () {
					 				if ( val != "" ) {
					 					val = val + ",";
					 				}
					 				val = val + jQuery(this).attr("data-value");
					 			});
					 			jQuery("#' . $this->func . '_settings\\\\[' . $args['id'] . '\\\\]").val(val);
       						}
       					}).disableSelection();
       				 } );
					 ';
		$html .= "\n";
		$html .= '</script>';

		$html .= '</div>';

		$html .= '<span class="description"><label for="' . $this->func . '_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

		echo apply_filters( $this->func . '_after_setting_output', $html, $args );
	}

	public function set_text_domain( $text_domain ) {
		$this->text_domain = $text_domain;
	}

	public function get_text_domain() {
		return $this->text_domain;
	}

}

