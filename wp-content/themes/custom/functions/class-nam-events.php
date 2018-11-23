<?php

/**
 * This class encapsulates functionality related
 * to managing classes, and imposing constraints on
 * the classes checkout process.
 */
class NAM_Events {

	/**
	 * Adds custom meta keys or settings for WooCommerce products
	 * that shadow "classes" custom post types.
	 *
	 * @param int $product_id the id of the shadowing product.
	 */
	public static function get_ticket_levels($product) {

		$variations = $product->get_available_variations();

		$variations = array_map(function ($variation) use ($product) {

			$discount = get_post_meta($variation['variation_id'], '_nam_membership_discount', true);
			$ticket_level = get_term_by('slug', $variation['attributes']['attribute_pa_ticket_levels'], 'pa_ticket_levels');

			return array(
				'title' => $variation['attributes']['attribute_pa_ticket_levels'],
				'term' => $ticket_level,
				'price' => $variation['display_price'],
				'in_stock' => $variation['is_in_stock'],
				'max_qty' => $variation['max_qty'],
				'min_qty' => $variation['min_qty'],
				'id' => $variation['variation_id'],
				'product_id' => $product->id,
				'membership_discount' => (double) $discount,
				// 'variation' => $variation
			);

		}, $variations);

		return $variations;
	}

	public static function save_notices() {

		return $_GET['save_notices'] !== NULL || $_POST['save_notices'] !== NULL;

	}



}

?>
