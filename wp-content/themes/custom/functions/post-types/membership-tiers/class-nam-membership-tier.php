<?php

class NAM_Membership_Tier extends NAM_Shadowed_Post_Type {

	public static $slug = 'membership-tier';

	public static $singular_name = 'Membership Tier';

	public static $plural_name = 'Membership Tiers';

	public static $post_options = array(
		'menu_icon' => 'dashicons-clipboard',
		'hierarchical' => false,
		'has_archive' => true,
		'menu_position' => 3,
		'supports' => array(
			'title',
			'revisions',
		),
		'rewrite' => array(
			'slug' => 'membership-tier',
			'with_front' => false,
			'feeds' => true,
			'pages' => true,
		),
		'taxonomies' => array(),

	);

	public static $query_options = array(

	);

	/**
	 * ==== Instance Members and Methods ====
	 */

	public function __construct($id) {

		$this->id = $id;

	}

	public function validate() {

	}

	public function create() {

	}

	/**
	 * This routine sets all the required product taxonomy terms for reporting
	 * purposes.
	 *
	 * @param int $post_id the id of the post that owns this custom product
	 * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
	 * @return array category names
	 */
	public static function get_product_categories($post_id) {

		return array(self::$plural_name);

	}

	public static function do_creation_meta($post_id, $product_id) {

		if (get_post_type($post_id) != static::$slug) {return;}

		$discount_multiplier = get_field(NAM_Membership::$field_keys['membership_discount_eligibility'], $post_id);

		update_post_meta($product_id, '_nam_discount_multiplier', $discount_multiplier);
        update_post_meta( $product_id, '_sold_individually', 'yes');

	}

}

?>
