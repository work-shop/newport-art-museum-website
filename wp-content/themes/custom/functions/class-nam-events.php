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
	public static function get_ticket_levels( $post_id, $product ) {

        $ticket_levels = get_field( NAM_Event::$field_keys['ticket_levels'], $post_id );

        $variations = array();

        foreach ($ticket_levels as $ticket_level) {

            if ( !empty($ticket_level['ticket_level_variation_id']) ) {

                $variation = wc_get_product( (int) $ticket_level['ticket_level_variation_id'] );

                $discount = get_post_meta($variation->get_id(), '_nam_membership_discount', true);
                $ticket_level_term = get_term_by('name', $ticket_level['ticket_level_name'], 'pa_ticket_levels');

                $variations[] = array(
                    'title' => $ticket_level['ticket_level_name'],
                    'term' => $ticket_level_term,
                    'price' => $variation->get_price(),
                    'in_stock' => $variation->is_in_stock(),
                    'max_qty' => $variation->get_stock_quantity(),
                    'min_qty' => 1,
                    'id' => $variation->get_id(),
                    'product_id' => $product->get_id(),
                    'membership_discount' => (double) $discount
                );

            }

        }

		return $variations;

	}

	public static function save_notices() {

		return $_GET['save_notices'] !== NULL || $_POST['save_notices'] !== NULL;

	}



}

?>
