<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Class Flexible_Checkout_Fields_Conditional_Logic_Filter
 */
abstract class Flexible_Checkout_Fields_Conditional_Logic_Filter {

	/**
	 * Plugin.
	 *
	 * @var Flexible_Checkout_Fields_Pro_Plugin.
	 */
	protected $plugin = null;

	/**
	 * Flexible_Checkout_Fields_Conditional_Logic_Checkout constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro_Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Check that products are in cart/order.
	 * Should be overwritten.
	 *
	 * @param array $products Products ID array.
	 *
	 * @return bool
	 */
	abstract protected function are_products_meet( array $products );

	/**
	 * Check that categories are in cart/order.
	 * Should be overwritten.
	 *
	 * @param array $categories Categories ID array.
	 *
	 * @return bool
	 */
	abstract protected function are_categories_meet( array $categories );

	/**
	 * Get field settings.
	 *
	 * @param string $field_name Field name.
	 *
	 * @return array|null
	 */
	protected function get_field_settings( $field_name ) {
		$field_definition = null;
		$fields           = $this->plugin->get_flexible_checkout_fields_plugin()->get_settings();
		foreach ( $fields as $section ) {
			foreach ( $section as $section_field_name => $field ) {
				if ( $field_name === $section_field_name ) {
					$field_definition = $field;
				}
			}
		}
		return $field_definition;
	}

	/**
	 * Conditional logic rule match products.
	 *
	 * @param string|array $products Products IDs.
	 *
	 * @return int
	 */
	private function conditional_logic_rule_match_products( $products ) {
		$rule_match = 0;
		if ( ! is_array( $products ) ) {
			$products = explode( ',', $products );
		}
		if ( $this->are_products_meet( $products ) ) {
			$rule_match = 1;
		}
		return $rule_match;
	}

	/**
	 * Conditional logic rule match categories.
	 *
	 * @param string|array $categories Categories IDs.
	 *
	 * @return int
	 */
	private function conditional_logic_rule_match_categories( $categories ) {
		$rule_match = 0;
		if ( ! is_array( $categories ) ) {
			$categories = explode( ',', $categories );
		}
		if ( $this->are_categories_meet( $categories ) ) {
			$rule_match = 1;
		}
		return $rule_match;
	}

	/**
	 * Conditional logic rule match.
	 *
	 * @param array $rule Rule.
	 *
	 * @return int
	 */
	protected function conditional_logic_rule_match( $rule ) {
		$rule_match = 0;
		if ( 'cart_contains' === $rule['condition'] ) {
			if ( 'product' === $rule['what'] ) {
				if ( isset( $rule['products'] ) ) {
					$rule_match = $this->conditional_logic_rule_match_products( $rule['products'] );
				}
			} elseif ( 'product_category' === $rule['what'] ) {
				if ( isset( $rule['product_categories'] ) ) {
					$rule_match = $this->conditional_logic_rule_match_categories( $rule['product_categories'] );
				}
			}
		}
		return $rule_match;
	}

	/**
	 * Compute AND operator.
	 *
	 * @param array $rules_match Rules match.
	 *
	 * @return bool
	 */
	private function compute_and_operator( array $rules_match ) {
		$show_field = true;
		if ( count( $rules_match ) ) {
			foreach ( $rules_match as $rule_match ) {
				if ( 1 !== $rule_match ) {
					$show_field = $show_field && false;
				}
			}
		} else {
			$show_field = false;
		}
		return $show_field;
	}


	/**
	 * Compute OR operator.
	 *
	 * @param array $rules_match Rules match.
	 *
	 * @return bool
	 */
	private function compute_or_operator( array $rules_match ) {
		$show_field = false;
		foreach ( $rules_match as $rule_match ) {
			if ( 1 === $rule_match ) {
				$show_field = $show_field || true;
			}
		}
		return $show_field;
	}

	/**
	 * Show field?
	 *
	 * @param array $rules_match Rules match.
	 * @param string $operator Operator.
	 * @param string $action Action.
	 *
	 * @return bool
	 */
	protected function compute_show_field( array $rules_match, $operator, $action ) {
		$show_field = false;
		if ( 'and' === $operator ) {
			$show_field = $this->compute_and_operator( $rules_match );
		} elseif ( 'or' === $operator ) {
			$show_field = $this->compute_or_operator( $rules_match );
		}
		if ( 'hide' === $action ) {
			$show_field = ! $show_field;
		}
		return $show_field;
	}

	/**
	 * Show field - product and category logic rules.
	 *
	 * @param bool $show_field Show field.
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	protected function show_field_product_and_category_rules( $show_field, $field ) {
		if ( isset( $field['conditional_logic'] ) && '1' === $field['conditional_logic'] ) {
			$rules_match = array();
			if ( isset( $field['conditional_logic_rules'] ) ) {
				foreach ( $field['conditional_logic_rules'] as $rule_id => $rule ) {
					$rules_match[ $rule_id ] = $this->conditional_logic_rule_match( $rule );
				}
			}
			if ( isset( $field['conditional_logic_action'] ) ) {
				$show_field = $this->compute_show_field( $rules_match, $field['conditional_logic_operator'], $field['conditional_logic_action'] );
			}
		}
		return $show_field;
	}
}
