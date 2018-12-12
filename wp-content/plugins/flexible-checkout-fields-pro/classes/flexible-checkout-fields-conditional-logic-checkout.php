<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once 'flexible-checkout-fields-conditional-logic-filter.php';

/**
 * Class Flexible_Checkout_Fields_Conditional_Logic_Checkout
 */
class Flexible_Checkout_Fields_Conditional_Logic_Checkout extends Flexible_Checkout_Fields_Conditional_Logic_Filter {

	/**
	 * Constants.
	 */
	const TERM_PRODUCT_CAT = 'product_cat';

	const PRODUCT_TYPE_VARIATION = 'variation';

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'flexible_checkout_fields_condition', array( $this, 'show_field_on_checkout_page' ), 10, 2 );
	}

	/**
	 * Check that products are in cart/order.
	 *
	 * @param array $products Products ID array.
	 *
	 * @return bool
	 */
	protected function are_products_meet( array $products ) {
		return $this->are_products_in_cart( $products );
	}

	/**
	 * Check that categories are in cart/order.
	 *
	 * @param array $categories Categories ID array.
	 *
	 * @return bool
	 */
	protected function are_categories_meet( array $categories ) {
		return $this->are_categories_in_cart( $categories );
	}

	/**
	 * Are products in cart?
	 *
	 * @param array $products Products array.
	 *
	 * @return bool
	 */
	public function are_products_in_cart( array $products ) {
		if ( ! empty( WC()->cart ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				/**
				 * Type hint for IDE.
				 *
				 * @var WC_Product $_product
				 */
				$_product = $values['data'];
				if ( $_product->is_type( self::PRODUCT_TYPE_VARIATION ) ) {
					if ( in_array( (string) wpdesk_get_variation_parent_id( $_product ), $products, true ) ) {
						return true;
					}
					if ( in_array( (string) wpdesk_get_variation_id( $_product ), $products, true ) ) {
						return true;
					}
				} else {
					if ( in_array( (string) wpdesk_get_product_id( $_product ), $products, true ) ) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Are categories in cart?
	 *
	 * @param array $categories Categories array.
	 *
	 * @return bool
	 */
	public function are_categories_in_cart( $categories ) {
		if ( ! empty( WC()->cart ) ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				/**
				 * Type hint for IDE.
				 *
				 * @var WC_Product $_product
				 */
				$_product = $values['data'];
				if ( $_product->is_type( self::PRODUCT_TYPE_VARIATION ) ) {
					$_categories = get_the_terms( wpdesk_get_variation_parent_id( $_product ), self::TERM_PRODUCT_CAT );
				} else {
					$_categories = get_the_terms( wpdesk_get_product_id( $_product ), self::TERM_PRODUCT_CAT );
				}
				if ( is_array( $_categories ) ) {
					foreach ( $_categories as $_category ) {
						if ( in_array( (string) $_category->term_id, $categories, true ) ) {
							return true;
						}
					}
				}
			}
		}
		return false;
	}

	/**
	 * Show field on checkout page?
	 *
	 * @param bool  $show_field Show field.
	 * @param array $field Field.
	 *
	 * @return bool
	 */
	public function show_field_on_checkout_page( $show_field, $field ) {
		if ( ! is_checkout() ) {
			return $show_field;
		}
		return $this->show_field_product_and_category_rules( $show_field, $field );
	}

}
