<?php

class Flexible_Checkout_Fields_Conditional_Logic implements \WPDesk\PluginBuilder\Plugin\HookablePluginDependant {

	use \WPDesk\PluginBuilder\Plugin\PluginAccess;

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'flexible_checkout_fields_field_tabs', array( $this, 'flexible_checkout_fields_field_tabs' ), 9 );

		add_action( 'flexible_checkout_fields_field_tabs_content', array( $this, 'flexible_checkout_fields_field_tabs_content'), 9, 4 );

		add_action( 'flexible_checkout_fields_field_tabs_content_js', array( $this, 'flexible_checkout_fields_field_tabs_content_js' ) );

		add_action( 'flexible_checkout_fields_java_script', array( $this, 'flexible_checkout_fields_java_script' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_filter( 'woocommerce_screen_ids', array ( $this, 'woocommerce_screen_ids' ) );

		add_filter( 'flexible_checkout_fields_condition', array( $this, 'flexible_checkout_fields_condition_in_checkout_page' ), 10, 2 );

		add_action( 'wp_ajax_flexible_checkout_fields_ajax', array( $this, 'wp_ajax_flexible_checkout_fields_ajax' ) );
	}
	
	public function woocommerce_screen_ids( $screen_ids ) {

		$screen_ids[] = 'woocommerce_page_inspire_checkout_fields_settings';

		return $screen_ids;
	}
	
	
	public function admin_enqueue_scripts() {
		$current_screen = get_current_screen();
		if ( $current_screen->id == 'woocommerce_page_inspire_checkout_fields_settings' ) {
			wp_enqueue_script( 'select2' );
			wp_enqueue_script( 'wc-enhanced-select' );
			wp_enqueue_style(
				'inspire_checkout_fields_pro',
				trailingslashit( $this->plugin->get_plugin_assets_url() ) . 'css/admin.css',
				array(),
				$this->plugin->get_script_version()
			);

		}
	}
	
	public function products_in_cart( $products ) {
		if ( !empty( WC()->cart ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				if ( $_product->is_type( 'variation' ) ) {
					if ( in_array( wpdesk_get_variation_parent_id( $_product ), $products ) ) {
						return true;
					}
					if ( in_array( wpdesk_get_variation_id( $_product ), $products ) ) {
						return true;
					}
				} else {
					if ( in_array( wpdesk_get_product_id( $_product ), $products ) ) {
						return true;
					}
				}
			}
		}
		return false;
	}
	
	public function categories_in_cart( $categories ) {
		if ( !empty( WC()->cart ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				if ( $_product->is_type( 'variation' ) ) {
					$_categories = get_the_terms( wpdesk_get_variation_parent_id( $_product ), 'product_cat' );
				} else {
					$_categories = get_the_terms( wpdesk_get_product_id( $_product ), 'product_cat' );
				}
				if ( is_array( $_categories ) ) {
					foreach ( $_categories as $_category ) {
						if ( in_array( $_category->term_id, $categories ) ) {
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	
	public function flexible_checkout_fields_condition_in_checkout_page( $return, $field ) {
		if ( !is_checkout() ) {
			return $return;
		}
		if ( isset( $field['conditional_logic'] ) && $field['conditional_logic'] == '1' ) {
			$rules_match = array();
			if ( isset( $field['conditional_logic_rules'] ) ) {				
				foreach ( $field['conditional_logic_rules'] as $rule_id => $rule ) {
					$rule_match = 0;
					if ( $rule['condition'] == 'cart_contains' ) {
						if ( $rule['what'] == 'product' ) {
							if ( isset( $rule['products'] ) ) {
								if ( !is_array( $rule['products'] ) ) {
									$rule['products'] = explode( ',', $rule['products'] );
								}
								if ( $this->products_in_cart( $rule['products'] ) ) {
									$rule_match = 1;
								}
							}
						}
					}
					if ( $rule['condition'] == 'cart_contains' ) {
						if ( $rule['what'] == 'product_category' ) {
							if ( isset( $rule['product_categories'] ) && $rule['product_categories'] != '' ) {
								if ( $this->categories_in_cart( explode( ',', $rule['product_categories'] ) ) ) {
									$rule_match = 1;
								}
							}
						}
					}
					$rules_match[$rule_id] = $rule_match;
				}
			}			
			if ( isset( $field['conditional_logic_action'] ) ) {
				$return = false;
				if ( $field['conditional_logic_operator'] == 'and' ) {
					$return = true;
					if ( count( $rules_match ) ) {
						foreach ( $rules_match as $rule_match ) {
							if ( $rule_match == 1 ) {
								$return = $return && true;
							}
							else {
								$return = $return && false;
							}
						}
					}
					else {
						$return = false;
					}
				}
				if ( $field['conditional_logic_operator'] == 'or' ) {
					$return = false;
					foreach ( $rules_match as $rule_match ) {
						if ( $rule_match == 1 ) {
							$return = $return || true;
						}
						else {
							$return = $return || false;
						}
					}
				}
				if ( $field['conditional_logic_action'] == 'hide' ) {
					$return = !$return;
				}
			}
		}
		return $return;
	}
	
	public function flexible_checkout_fields_field_tabs( $tabs ) {

		$flexible_checkout_fields = flexible_checkout_fields();

		remove_filter( 'flexible_checkout_fields_field_tabs', array( $flexible_checkout_fields, 'flexible_checkout_fields_field_tabs' ) );
		
		$tabs[] = array( 
				'hash' => 'advanced', 
				'title' => __( 'Advanced', 'flexible-checkout-fields-pro' ) 			
		);				
		return $tabs;
	}
	
	public function init_select_options( $settings ) {
		global $flexible_checkout_fields_all_product_categories;
		if ( empty( $flexible_checkout_fields_all_product_categories ) ) {
			$flexible_checkout_fields_all_product_categories = array();
				
			$taxonomy     = 'product_cat';
			$orderby      = 'name';
			$show_count   = 0;      // 1 for yes, 0 for no
			$pad_counts   = 0;      // 1 for yes, 0 for no
			$hierarchical = 0;      // 1 for yes, 0 for no
			$title        = '';
			$empty        = 0;
			$args = array(
					'taxonomy'     => $taxonomy,
					'orderby'      => $orderby,
					'show_count'   => $show_count,
					'pad_counts'   => $pad_counts,
					'hierarchical' => $hierarchical,
					'title_li'     => $title,
					'hide_empty'   => $empty
			);
			$all_categories = get_categories( $args );
			foreach ( $all_categories as $category ) {
				$flexible_checkout_fields_all_product_categories[$category->term_id] = $category->name;
			}
		}

        global $flexible_checkout_fields_all_field_options;
        global $flexible_checkout_fields_all_field_values;
		global $flexible_checkout_fields_all_conditions;

		$flexible_checkout_fields = flexible_checkout_fields();

        if ( empty( $flexible_checkout_fields_all_field_options ) ) {
            $flexible_checkout_fields_all_field_options = array();
            $flexible_checkout_fields_all_field_values = array();
	        $flexible_checkout_fields_all_conditions = array();
            $all_sections = $flexible_checkout_fields->all_sections;
            $sections = $flexible_checkout_fields->sections;
            if ( is_array( $settings ) ) {
                foreach ( $settings as $group => $fields ) {
                    if ( is_array( $fields ) ) {
                        foreach ( $fields as $key => $field ) {
                            if (isset($field['type']) && ($field['type'] == 'select' || $field['type'] == 'inspireradio' || $field['type'] == 'inspirecheckbox' || $field['type'] == 'checkbox')) {
                                $section = array( 'tab_title' => __( 'Disabled section:' , 'flexible-checkout-fields' ) . ' ' . $group );
                                foreach ( $all_sections as $section1 ) {
                                    if ( $section1['section'] == $group ) {
                                        $section = $section1;
                                        $disabled = true;
                                        foreach ( $sections as $section2 ) {
                                            if ( $section2['section'] == $group ) {
                                                $disabled = false;
                                            }
                                        }
                                        if ( $disabled ) {
                                            $section['tab_title'] = __( 'Disabled section:' , 'flexible-checkout-fields' ) . ' ' . $section['tab_title'];
                                        }
                                        break;
                                    }
                                }
                                $flexible_checkout_fields_all_field_options[$field['name']] = $field['label'] . ' [' . $section['tab_title'] . ']';
                                $flexible_checkout_fields_all_field_values[$field['name']] = array();
                                if ($field['type'] == 'select' || $field['type'] == 'inspireradio') {
                                    $array_options = explode("\n", $field['option']);
                                    if (!empty($array_options)) {
                                        $options = array();
                                        foreach ($array_options as $option) {
                                            $tmp = explode(':', $option);
                                            $option_value = trim($tmp[0]);
                                            $option_label = $option_value;
                                            if ( isset( $tmp[1] ) ) {
                                            	$option_label = $tmp[1];
                                            }
                                            $options[$option_value] = htmlspecialchars( wp_unslash( trim( $option_label ) ) );
                                            unset($tmp);
                                        }
                                    }
                                    $flexible_checkout_fields_all_field_values[$field['name']] = $options;
                                }
                                if ( $field['type'] == 'inspirecheckbox' || $field['type'] == 'checkbox' ) {
                                    $flexible_checkout_fields_all_field_values[$field['name']] = array(
                                        'checked' => __('checked', 'flexible-checkout-fields-pro'),
                                        'unchecked' => __('unchecked', 'flexible-checkout-fields-pro')
                                    );
                                }
                            }
                            if ( isset( $field['conditional_logic_fields_rules'] ) ) {
                            	foreach ( $field['conditional_logic_fields_rules'] as $rule ) {
                            		if ( isset( $rule['field'] ) ) {
			                            if ( ! isset( $flexible_checkout_fields_all_conditions[ $rule['field'] ] ) ) {
				                            $flexible_checkout_fields_all_conditions[ $rule['field'] ] = array();
			                            }
			                            if ( ! isset( $flexible_checkout_fields_all_conditions[ $rule['field'] ][ $group ] ) ) {
				                            $flexible_checkout_fields_all_conditions[ $rule['field'] ][ $group ] = array();
			                            }
			                            $flexible_checkout_fields_all_conditions[ $rule['field'] ][ $group ][] = $field['label'];
		                            }
	                            }
                            }
                        }
                    }
                }
            }
        }

    }
	
	public function flexible_checkout_fields_field_tabs_content( $key, $name, $field, $settings ) {
		$flexible_checkout_fields = flexible_checkout_fields();
		remove_action( 'flexible_checkout_fields_field_tabs_content', array( $flexible_checkout_fields, 'flexible_checkout_fields_field_tabs_content'), 10, 4 );
		$this->init_select_options( $settings );
		//global $flexible_checkout_fields_all_products;
		global $flexible_checkout_fields_all_product_categories;
        global $flexible_checkout_fields_all_field_options;
        global $flexible_checkout_fields_all_field_values;
		global $flexible_checkout_fields_all_conditions;
		$count = 0;
		include( 'views/settings-field-advanced.php' );
	}
	
	public function flexible_checkout_fields_field_tabs_content_js() {
		$count = 0;
		include( 'views/settings-field-advanced-js.php' );
	}
	
	public function flexible_checkout_fields_java_script( $settings ) {
		$this->init_select_options( $settings );
		global $flexible_checkout_fields_all_product_categories;
        global $flexible_checkout_fields_all_field_options;
        global $flexible_checkout_fields_all_field_values;
		global $flexible_checkout_fields_all_conditions;
		include( 'views/settings-field-java-script.php' );
	}
	
	public function wp_ajax_flexible_checkout_fields_ajax() {
		$data = array();
		$q = $_POST['q'];
		$data_type = $_POST['data_type'];
		if ( $data_type = 'products' ) {
			$args = array(
					'post_type' 		=> 'product',
					'posts_per_page' 	=> -1,
					's'					=> $q
			);
			$posts = get_posts( $args );
			foreach ( $posts as $post ) {
				$data[] = array( 'id' => $post->ID, 'text' => $post->post_title );
			}				
		}
		if ( $data_type = 'categories' ) {
			$taxonomy     = 'product_cat';
			$orderby      = 'name';
			$show_count   = 0;      // 1 for yes, 0 for no
			$pad_counts   = 0;      // 1 for yes, 0 for no
			$hierarchical = 0;      // 1 for yes, 0 for no
			$title        = '';
			$empty        = 0;
			$args = array(
					'taxonomy'     => $taxonomy,
					'orderby'      => $orderby,
					'show_count'   => $show_count,
					'pad_counts'   => $pad_counts,
					'hierarchical' => $hierarchical,
					'title_li'     => $title,
					'hide_empty'   => $empty,
					'search'	   => $q
			);
			$all_categories = get_categories( $args );
			foreach ( $all_categories as $category ) {
				$data[] = array( 'id' => $category->term_id, 'text' => $category->name );
			}
		}
		echo json_encode( $data );
	}
	
}
