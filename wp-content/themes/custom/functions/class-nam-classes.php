<?php

/**
 * This class encapsulates functionality related
 * to managing classes, and imposing constraints on
 * the classes checkout process.
 */
class NAM_Classes {

	public static $classes_category_slug = 'classes';

	public static $classes_custom_fields = array(
		'_order_student_first_name' => 'Student First Name',
		'_order_student_last_name' => 'Student Last Name',
		'_order_birthdate' => 'Birthdate',
		'_order_primary_phone_number' => 'Primary Phone',
		'_order_primary_phone_type' => 'Primary Phone Type',
		'_order_secondary_phone_number' => 'Secondary Phone',
		'_order_secondary_phone_type' => 'Secondary Phone Type',
		'_order_email_contact' => 'Contact Email',
		'_order_preferred_pronoun' => 'Preferred Pronoun',
	);

	/**
	 * Adds custom meta keys or settings for WooCommerce products
	 * that shadow "classes" custom post types.
	 *
	 * @param int $post_id the id of the parent post.
	 * @param int $product_id the id of the shadowing product.
	 */
	public static function do_creation_meta($post_id, $product_id) {

		if (get_post_type($post_id) != NAM_Class::$slug) {return;}

		update_post_meta($product_id, '_sold_individually', 'yes');

	}

	/**
	 * This routine checks the current user's cart to see if there's
	 * A class product in their cart. If there is a class in
	 * their cart, it returns an array with the class ID in it.
	 * If not, it returns false.
	 *
	 * @return bool||WC_Product returns the class product in the cart if there is one, or false.
	 *
	 */
	public static function has_class_in_cart() {

		foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {

			$product = $cart_item['data'];

			if (has_term(static::$classes_category_slug, 'product_cat', $product->id)) {
				return $product;
			}

		}

		return false;

	}

	public static function register_classes_heading_rewrite() {

		add_action('admin_init', function () {
			$called_class = get_called_class();
			add_filter('the_content', array($called_class, 'rewrite_customer_list_headings'), 99);
		});

	}

	public static function rewrite_customer_list_headings($content) {

		return str_replace('_order_student_first_name', 'Student First Name', $content);

		// foreach ( static::$classes_custom_fields as $field => $name ) {
		//
		//     $content = str_replace( $field, $name, $content );
		//
		// }
		//
		// return $content;

	}

	/**
	 * This routine checks to see if there's
	 * a class in the given order, and
	 * return true if there is.
	 */
	public static function has_class_in_order($order) {

		foreach ($order->get_items() as $item_id => $item_data) {

			$product = $item_data->get_product();

			if (has_term(static::$classes_category_slug, 'product_cat', $product->id)) {
				return true;
			}

		}

		return false;
	}

}

?>
