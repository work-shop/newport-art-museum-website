<?php

function nam_breakpoint( $var ) {
    var_dump( $var );
    throw new Exception('NAM Debugging Breakpoint');
}

abstract class NAM_Shadowed_Post_Type extends NAM_Custom_Post_Type {

	public static $field_keys = array(
		// Shadowed Post Field
		'managed_field_related_post' => 'field_5b687e3ad7e8e',

		// Price Fields
		'price' => 'field_5b647341dca78',
		'sale_price' => 'field_5b6473c0dca7a',
		'sale_from' => 'field_5b64742edca7b',
		'sale_to' => 'field_5b647490dca7c',

		// Stock Management Price
		'manage_stock' => 'field_5b685c94d33c6',
		'stock_quantity' => 'field_5b685cdbd33c7',

		'fees' => 'field_5b7470acd3e33',

		'name_your_price_product' => 'field_5bd76c14ec5b8',
		'minimum_price' => 'field_5bd76f19ec5b9',
		'suggested_price' => 'field_5bd76f94ec5bb',

		// Discounts
		'membership_discount_type' => 'field_5bdc683e3d11b',
		'membership_percentage_discount' => 'field_5bdc68863d11c',
		'membership_fixed_discount' => 'field_5bdc68f33d11d',

		// Events
		'number_of_ticket_levels' => 'field_5bef14b2974bf',
		'ticket_levels' => 'field_5bef20c467cb5',

		// One per Customer
		'one_per_order' => 'field_5bf5af2b2c46f',



		// Fees and Surcharges
	);

	/**
	 * The register static method is used to register the instance post type
	 * in WordPress
	 */
	public static function register() {

		parent::register();

		static::register_shadowed_post_actions();

		static::register_stock_actions();

	}

	/**
	 * This routine registers specific actions for this post-type
	 * that create and delete shadowed woocommerce posts
	 * that implement e-commerce functionality programmatically.
	 */
	public static function register_shadowed_post_actions() {
		$called_class = get_called_class();
		add_action('acf/save_post', array($called_class, 'do_product_management_actions'), 20);
		add_action('mtphr_post_duplicator_created', array($called_class, 'do_duplicate_post_product_management_actions'), 10, 2);
	}

	/**
	 * This routine removes specific actions for this post-type
	 * that create and delete shadowed woocommerce posts
	 * that implement e-commerce functionality programmatically.
	 *
	 * NOTE: this is used to prevent "save_post" loops from ocurring when woocommerce
	 *       objects are created.
	 */
	public static function deregister_shadowed_post_actions() {
		$called_class = get_called_class();
		remove_action('acf/save_post', array($called_class, 'do_product_management_actions'), 20);
		remove_action('mtphr_post_duplicator_created', array($called_class, 'do_duplicate_post_product_management_actions'), 10, 2);
	}

	/**
	 * This routine is called when a post is duplicated,
	 * and handles creating a new shadowing product
	 * for the duplicate.
	 */
	public static function do_duplicate_post_product_management_actions($post_id, $duplicate_id) {

		static::do_product_management_actions($duplicate_id, true);

	}

	/**
	 * This function dispatches calls to create_shadow_post and
	 * update_shadow_post depending on the context in which the
	 * action is triggered.
	 *
	 * @param int $post_id  the id of the post being saved.
     * @param boolean $duplicate true if the product is a duplicate.
	 */
	public static function do_product_management_actions($post_id, $duplicate = false) {

		$post_id = (int) $post_id;

		if (get_post_type($post_id) != static::$slug) {return;}

		static::deregister_shadowed_post_actions();

		$updated_post = get_post($post_id);
		$shadow_post = get_field(static::$field_keys['managed_field_related_post'], $post_id);

		if (!$shadow_post) {

			static::create_shadowing_product($post_id, $updated_post);

		} else {

			if (count($shadow_post) > 1) {

				throw new Exception('Shadowing Post Error – multiple products associated with this post.');

			} else {

				if ($duplicate) {

					delete_field(static::$field_keys['managed_field_related_post'], $post_id);
                    static::delete_event_variation_ids( $post_id );
					static::create_shadowing_product($post_id, $updated_post);

				} else {

					static::update_shadowing_product($post_id, $updated_post, $shadow_post[0]);

				}

			}

		}

		static::register_shadowed_post_actions();

	}

	/**
	 * This function creates a new shadowed woocommerce post when
	 * The authoring post is saved in the database, and associates it
	 * with the authoring post.
	 *
	 * @param int $post_id the idea of the post being created.
	 * @param WP_Post $post the post object being updated.
	 */
	public static function create_shadowing_product($post_id, $updated_post, $copy = false) {

		$product_id = (int) wp_insert_post(array(
			'post_title' => $updated_post->post_title,
			'post_content' => '',
			'post_status' => $updated_post->post_status,
			'post_type' => 'product',
		));

		static::set_shadowing_product_object_terms($updated_post->post_title, $post_id, $product_id);
		static::set_shadowing_product_categories($updated_post->post_title, $post_id, $product_id);
		static::set_shadowing_product_custom_trackers($updated_post->post_title, $post_id, $product_id);

		$result = update_field(static::$field_keys['managed_field_related_post'], array($product_id), (int) $post_id);

	}

	/**
	 * This function creates a new shadowed woocommerce post when
	 * The authoring post is saved in the database, and associates it
	 * with the authoring post.
	 *
	 * @param int $post_id the idea of the post being created.
	 * @param WP_Post $post the post object being updated.
	 * @param WP_Post $post the shadowed product object being attached.
	 */
	public static function update_shadowing_product($post_id, $updated_post, $shadowing_post) {

		wp_update_post(array(
			'ID' => $shadowing_post->ID,
			'post_title' => $updated_post->post_title,
			'post_status' => $updated_post->post_status,
			'post_name' => '',
		));

		static::set_shadowing_product_object_terms($updated_post->post_title, $post_id, $shadowing_post->ID);
		static::set_shadowing_product_categories($updated_post->post_title, $post_id, $shadowing_post->ID);
		static::set_shadowing_product_custom_trackers($updated_post->post_title, $post_id, $shadowing_post->ID);

		$result = update_field(static::$field_keys['managed_field_related_post'], array($shadowing_post->ID), (int) $post_id);

		//throw new Exception('Testing Product Updating');

	}

	/**
	 * This function removes the shadowed post associated with a given
	 * post, in the case that it's parent is being deleted.
	 *
	 * NOTE: This function DOES NOT delete the product record associated
	 * with the post, it just unlinks the shadowing post.
	 *
	 * @param int $post_id the id of the post being created.
	 */
	public static function remove_shadowing_product($post_id) {

		delete_field(static::$field_keys['managed_field_related_post'], (int) $post_id);

	}

	/**
	 * This routine sets all the required object terms on a given shadow product,
	 * based on the relevant parameters set in the Sales Information box on the
	 * the custom post.
	 *
	 * @param string title the Title of this post.
	 * @param int $post_id the id of the post that owns this custom product
	 * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
	 */
	public static function set_shadowing_product_object_terms($title, $post_id, $product_id) {

		static::set_product_meta($title, $post_id, $product_id);
		static::set_product_type($title, $post_id, $product_id);

	}

	/**
	 * This function dispatches particular configuration actions based on
	 * The type of product being added. If the product has registered fees,
	 * This routine creates a bundled product which wraps the fees with
	 * the specified base product. Otherwise, the product is set to simple.
	 *
	 * @param string title the Title of this post.
	 * @param int $post_id the id of the post that owns this custom product
	 * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
	 */
	public static function set_product_type($title, $post_id, $product_id) {

		$fees = static::get_product_fees($post_id);

		if ($fees) {

			wp_set_object_terms($product_id, 'bundle', 'product_type');

			$product = new WC_Product_Bundle($product_id);

			static::set_product_fees($product, $fees);

		} else if (static::$slug == 'membership-tier') {

			wp_set_object_terms($product_id, 'subscription', 'product_type');

			static::set_subscription_meta($post_id, $product_id);

		} else if (static::$slug == 'events') {

			static::create_event_meta($title, $post_id, $product_id);

		} else {

			wp_set_object_terms($product_id, 'simple', 'product_type');

		}

	}

/**
 */

	/**
	 * This routine constructs the bundle items for a given bundled product.
	 * @see This routine is build off of `WC_PB_Meta_Box_Product_Data::process_bundle_data`
	 *
	 * @param WC_Product $product the product to bundle.
	 * @param array $fees an array of wp_post
	 */
	public static function set_product_fees($product, $fees) {

		$data_items = array_map(function ($fee, $i) {

			return array(
				'product_id' => $fee->get_id(),
				'meta_data' => array(
					'priced_individually' => 'yes',
					'shipped_individually' => 'no',
					'quantity_min' => 1,
					'quantity_max' => 1,
					'order_visibility' => 'visible',
					'discount' => 0,
					'order_price_visibility' => 'visible',
					'override_title' => 'no',
				),
			);

		}, $fees, array_keys($fees));

		$product->set_bundled_data_items($data_items);
		$product->save();

	}

	/**
	 * Given a post_id and shadowing product id representing
	 * a membership, set the appropriate subscription meta data
	 *
	 * @param int $post_id the ID of the parent post
	 * @param int $product_id the ID of the shadowing subscription product.
	 */
	public static function set_subscription_meta($post_id, $product_id) {

		$price = get_field(static::$field_keys['price'], $post_id);

		update_post_meta($product_id, '_subscription_price', (double) $price);
		update_post_meta($product_id, '_subscription_sign_up_fee', 0);
		update_post_meta($product_id, '_subscription_period', 'year');
		update_post_meta($product_id, '_subscription_period_interval', 1);
		update_post_meta($product_id, '_subscription_length', 0);
		update_post_meta($product_id, '_subscription_trial_period', '');
		update_post_meta($product_id, '_subscription_limit', 'no');
		update_post_meta($product_id, '_subscription_one_time_shipping', 'no');

	}

	/**
	 * Given a master post id, get the set of Woocommerce Products
	 * associated with that post.
	 *
	 * @param int $post_id the id of the master CPT to get woocommerce fees for.
	 * @return array || boolean false if the object has no fees, array of WC_Products representing fees otherwise.
	 */
	public static function get_product_fees($post_id) {

		$fees = get_field(static::$field_keys['fees'], $post_id);

		if ($fees) {

			$fees = array_map(function ($fee) {

				$product = get_field(static::$field_keys['managed_field_related_post'], $fee->ID);

				return ($product[0]) ? wc_get_product($product[0]->ID) : false;

			}, $fees);

			$fees = array_filter($fees); // NOTE: No callback, means all elements == false are dropped.

		}

		return ($fees && count($fees) > 0) ? $fees : false;

	}

    /**
     * Delete the existing variation_ids from a duplicated
     * post to ensure that new variations are created for this
     * product.
     *
     * @param int $post_id the id of the post to remove meta for.
     */
    public static function delete_event_variation_ids( $post_id ) {
        if ( 'events' != static::$slug ) { return; }

        $ticket_levels = get_field(static::$field_keys['ticket_levels'], $post_id);

        foreach ( $ticket_levels as $i => $ticket_level ) {

            delete_sub_field( array( 'ticket_levels', ($i + 1),  'ticket_level_variation_id' ), $post_id );

        }

    }

	/**
	 * Create the relevant metadata required to create
	 * a variable product that can represent the
	 * different ticket levels in an event.
	 *
	 */
	public static function create_event_meta($title, $post_id, $product_id) {

		//$multiple_ticket_levels = get_field(static::$field_keys['number_of_ticket_levels'], $post_id);

		$ticket_levels = get_field(static::$field_keys['ticket_levels'], $post_id);

		$available_attributes = array('ticket_levels');

		$variations = static::create_ticket_level_variations($post_id, $ticket_levels);


        static::delete_old_variations( $variations, $product_id );

		static::insert_product_attributes($product_id, $ticket_levels);

        static::modify_product_variations($title, $post_id, $product_id, $variations);

	}

    public static function delete_old_variations( $variations, $product_id ) {

        $variation_ids = array_map( function( $v ) { return $v['variation_id']; }, $variations );

        $children = get_posts(array(
            'post_parent' => $product_id,
            'post_type' => 'product_variation',
        ));

        foreach ( $children as $child ) {

            if ( !in_array( $child->ID, $variation_ids )) {
                // NOTE: It would be extra safe to implement a method here to
                //       Check if the variation ID has ever been purchased:
                //       - if yes, don't delete the variation for records-keeping purposes.
                wp_delete_post($child->ID);
            }

        }

    }

    /**
     * Create a valid set of importable variations
     * from a given set of ticket levels. These will
     * be used to update existing variations
     *
     */
    public static function create_ticket_level_variations($post_id, $ticket_levels = array()) {

        $variations = array();

        foreach ($ticket_levels as $i => $ticket_level) {

            $needs_default = empty($ticket_level['ticket_level_name']) || empty($ticket_level['ticket_level_price']);
            $name = (!empty($ticket_level['ticket_level_name'])) ? $ticket_level['ticket_level_name'] : 'Ticket Level ' . ($i + 1);
            $price = (!empty($ticket_level['ticket_level_price'])) ? $ticket_level['ticket_level_price'] : 0;
            $variation_id = (!empty($ticket_level['ticket_level_variation_id'])) ? (int) $ticket_level['ticket_level_variation_id'] : false;

            if ( $needs_default ) {
                static::set_default_ticket_level_data( $post_id, $name, $price, $i, $variation_id );
            }

            $variations[] = array(
                'attributes' => array(
                    'ticket_levels' => $name,
                ),
                'price' => $price,
                'variation_id' => $variation_id
            );

        }

        return $variations;

    }

    public static function  set_default_ticket_level_data( $post_id, $name, $price, $i, $variation_id ) {

        update_sub_field( array( 'ticket_levels', $i + 1, 'ticket_level_name' ), $name, $post_id );
        update_sub_field( array( 'ticket_levels', $i + 1, 'ticket_level_price' ), $price, $post_id );
        //update_field( array( 'ticket_levels', $i + 1, 'ticket_level_variation_id' ), $variation_id, $post_id );

    }


    public function modify_product_variations( $title, $post_id, $product_id, $variations ) {

        $sold_individually = get_field(static::$field_keys['one_per_order'], $post_id);

        foreach ($variations as $index => $variation) {

            $product_exists = wc_get_product( $variation['variation_id'] );

            if ( $variation['variation_id'] && $product_exists ) {
                // NOTE: In this case, we have a pre-existing variation to update.
                //       Just update the existing variation with the new Meta.

                static::set_product_variation_meta( $title, $post_id, $product_id, $variation, $sold_individually  );

            } else {
                // NOTE: This ticket level does not have an existing variation,
                //       or the pre-existing variation was deleted. We need to
                //       create a new variation to manage this ticket level.

                $variation['variation_id'] = static::create_product_variation( $title, $post_id, $product_id, $variation );
                static::set_product_variation_meta( $title, $post_id, $product_id, $variation, $sold_individually  );

                update_sub_field(
                    array('ticket_levels', $index + 1, 'ticket_level_variation_id' ),
                    $variation['variation_id'],
                    $post_id
                );

            }

        }

    }

    public static function create_product_variation( $title, $post_id, $product_id, $variation ) {

        $variation_type_name = $variation['attributes']['ticket_levels'];

        $variation_name = 'ticket-level-' . $index . '-for-event-product-' . $product_id . '-for-event-' . $post_id;

        $variation_post = array(
            'post_title' => $variation_type_name . ' Ticket for ' . $title,
            'post_status' => 'publish',
            'post_parent' => $product_id,
            'post_type' => 'product_variation',
        );

        $variation_id = wp_insert_post($variation_post);

        return $variation_id;

    }


    public static function set_product_variation_meta( $title, $post_id, $product_id, $variation, $sold_individually ) {

        $variation_id = $variation['variation_id'];
        $variation_type_name = $variation['attributes']['ticket_levels'];

        $variation_name = 'ticket-level-' . $index . '-for-event-product-' . $product_id . '-for-event-' . $post_id;
        $variation_price = $variation['price'];
        $discount = static::get_product_membership_discount($post_id, $variation_price);

        $attribute_term = get_term_by('name', $variation_type_name, 'pa_ticket_levels');

        //nam_breakpoint( $attribute_term );

        update_post_meta($variation_id, 'attribute_pa_ticket_levels', $attribute_term->slug);

        update_post_meta($variation_id, '_virtual', 'yes');
        update_post_meta($variation_id, '_sku', $variation_name);

        update_post_meta($variation_id, '_price', $variation_price);
        update_post_meta($variation_id, '_regular_price', $variation_price);
        update_post_meta($variation_id, '_nam_membership_discount', $discount);
        update_post_meta($variation_id, '_nam_variation_type', $variation_type_name );
        update_post_meta($variation_id, '_wc_min_qty_product', 0);

        update_post_meta($variation_id, '_manage_stock', 'no');
        update_post_meta($variation_id, '_backorders', 'no');
        update_post_meta($variation_id, '_stock', 0);
        update_post_meta($variation_id, '_stock_status', 'instock');
        update_post_meta($variation_id, '_default_attributes', array());
        update_post_meta($variation_id, '_variation_description', $variation_type_name);

        if ($sold_individually) {
            update_post_meta($variation_id, '_sold_individually', 'yes');
        }

        wc_delete_product_transients($variation['variation_id']);

    }


	public static function delete_existing_variations($product_id) {
		$children = get_posts(array(
			'post_parent' => $product_id,
			'post_type' => 'product_variation',
		));

		if (is_array($children) && count($children) > 0) {

			foreach ($children as $child) {

				wp_delete_post($child->ID);

			}

		}

	}

	public static function insert_product_attributes($product_id, $ticket_levels) {

		$values = array_map(function ($ticket_level) {return $ticket_level['ticket_level_name'];}, $ticket_levels);

		$product_attributes_data = array(
			'pa_ticket_levels' => array(
				'name' => 'pa_ticket_levels',
				'value' => '',
				'position' => 0,
				'is_visible' => 1,
				'is_variation' => 1,
				'is_taxonomy' => 1,
			),
		);

		wp_set_object_terms($product_id, 'variable', 'product_type');
		update_post_meta($product_id, '_product_attributes', $product_attributes_data);
		wp_set_object_terms($product_id, $values, 'pa_ticket_levels');

	}

	public static function get_product_membership_discount($post_id, $price) {

		$membership_discount_type = get_field(static::$field_keys['membership_discount_type'], $post_id);
		$membership_percentage_discount = get_field(static::$field_keys['membership_percentage_discount'], $post_id);
		$membership_fixed_discount = get_field(static::$field_keys['membership_fixed_discount'], $post_id);

		if ($membership_discount_type && $membership_discount_type !== 'no-discount') {
			if ($membership_discount_type === 'percentage-discount') {

				$percentage = ((double) $membership_percentage_discount) / 100;
				return (double) $price * $percentage;

			} else if ($membership_discount_type === 'fixed-discount') {

				return (double) $membership_fixed_discount;

			}
		}

		return 0.0;

	}

	public static function insert_product_variations($title, $post_id, $product_id, $variations) {

		$sold_individually = get_field(static::$field_keys['one_per_order'], $post_id);

		foreach ($variations as $index => $variation) {

			$variation_type_name = $variation['attributes']['ticket_levels'];

			$variation_name = 'ticket-level-' . $index . '-for-event-product-' . $product_id . '-for-event-' . $post_id;
			$variation_price = $variation['price'];
			$discount = static::get_product_membership_discount($post_id, $variation_price);

			$variation_post = array(
				'post_title' => $variation_type_name . ' Ticket for ' . $title,
				//'post_name' => $variation_name,
				'post_status' => 'publish',
				'post_parent' => $product_id,
				'post_type' => 'product_variation',
			);

			$variation_id = wp_insert_post($variation_post);

			$attribute_term = get_term_by('name', $variation_type_name, 'pa_ticket_levels');

			update_post_meta($variation_id, 'attribute_pa_ticket_levels', $attribute_term->slug);

			update_post_meta($variation_id, '_virtual', 'yes');
			update_post_meta($variation_id, '_sku', $variation_name);

			update_post_meta($variation_id, '_price', $variation_price);
			update_post_meta($variation_id, '_regular_price', $variation_price);
			update_post_meta($variation_id, '_nam_membership_discount', $discount);
			update_post_meta($variation_id, '_nam_variation_type', $variation_type_name );
			update_post_meta($variation_id, '_wc_min_qty_product', 0);

			update_post_meta($variation_id, '_manage_stock', 'no');
			update_post_meta($variation_id, '_backorders', 'no');
			update_post_meta($variation_id, '_stock', 0);
			update_post_meta($variation_id, '_stock_status', 'instock');
			update_post_meta($variation_id, '_default_attributes', array());
			update_post_meta($variation_id, '_variation_description', $variation_type_name);

			if ($sold_individually) {
				update_post_meta($variation_id, '_sold_individually', 'yes');
			}

            update_sub_field(
                array('ticket_levels', $index + 1, 'ticket_level_variation_id' ),
                $variation_id,
                $post_id
            );

		}

	}

	/**
	 * This routine determines the appropriate product type for this custom post type.
	 *
	 * @param string title the Title of this post.
	 * @param int $post_id the id of the post that owns this custom product
	 * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
	 */
	public static function set_product_meta($title, $post_id, $product_id) {

		$called_class = get_called_class();

		wc_delete_product_transients($product_id);

		$price = get_field(static::$field_keys['price'], $post_id);
		$sale_price = get_field(static::$field_keys['sale_price'], $post_id);
		$sale_from = get_field(static::$field_keys['sale_from'], $post_id);
		$sale_to = get_field(static::$field_keys['sale_to'], $post_id);
		$manage_stock = get_field(static::$field_keys['manage_stock'], $post_id);
		$stock_quantity = get_field(static::$field_keys['stock_quantity'], $post_id);
		$name_your_price = get_field(static::$field_keys['name_your_price_product'], $post_id);
		$nyp_minumum_price = get_field(static::$field_keys['minimum_price'], $post_id);
		$nyp_suggested_price = get_field(static::$field_keys['suggested_price'], $post_id);

		$membership_discount_type = get_field(static::$field_keys['membership_discount_type'], $post_id);
		$membership_percentage_discount = get_field(static::$field_keys['membership_percentage_discount'], $post_id);
		$membership_fixed_discount = get_field(static::$field_keys['membership_fixed_discount'], $post_id);
		$sold_individually = get_field(static::$field_keys['one_per_order'], $post_id);

		update_post_meta($product_id, '_downloadable', 'no');
		update_post_meta($product_id, '_virtual', 'yes'); // NOTE: once shop products are launched, we'll need to make this non-constant

		update_post_meta($product_id, '_price', (double) $price);
		update_post_meta($product_id, '_regular_price', (double) $price);
		update_post_meta($product_id, '_wc_pb_base_regular_price', (double) $price);
		update_post_meta($product_id, '_wc_pb_base_price', (double) $price);

		if ($sale_price) {
			update_post_meta($product_id, '_sale_price', (double) $sale_price);
			update_post_meta($product_id, '_sale_price_dates_from', $sale_from);
			update_post_meta($product_id, '_sale_price_dates_to', $sale_to);
			update_post_meta($product_id, '_wc_pb_base_sale_price', (double) $sale_price);
		} else {
			update_post_meta($product_id, '_sale_price', '');
			update_post_meta($product_id, '_sale_price_dates_from', '');
			update_post_meta($product_id, '_sale_price_dates_to', '');
			update_post_meta($product_id, '_wc_pb_base_sale_price', '');
		}

		if ($name_your_price) {
			update_post_meta($product_id, '_nyp', 'yes');
			update_post_meta($product_id, '_minimum_price', (double) $nyp_minumum_price);
			update_post_meta($product_id, '_suggested_price', (double) $nyp_suggested_price);
		} else {
			update_post_meta($product_id, '_nyp', 'no');
			update_post_meta($product_id, '_minimum_price', (double) $price);
			update_post_meta($product_id, '_suggested_price', (double) $price);
		}

		if ($membership_discount_type && $membership_discount_type !== 'no-discount') {
			if ($membership_discount_type === 'percentage-discount') {
				$percentage = ((double) $membership_percentage_discount) / 100;
				$discount = (double) $price * $percentage;
				update_post_meta($product_id, '_nam_membership_discount', $discount);
			} else if ($membership_discount_type === 'fixed-discount') {
				$discount = (double) $membership_fixed_discount;
				update_post_meta($product_id, '_nam_membership_discount', $discount);
			} else {
				update_post_meta($product_id, '_nam_membership_discount', 0);
			}
		} else {
			update_post_meta($product_id, '_nam_membership_discount', 0);
		}

		update_post_meta($product_id, '_purchase_note', '');
		update_post_meta($product_id, '_featured', 'no');
		update_post_meta($product_id, '_weight', '');
		update_post_meta($product_id, '_length', '');
		update_post_meta($product_id, '_width', '');
		update_post_meta($product_id, '_height', '');
		update_post_meta($product_id, '_product_attributes', array());

		$possible_sku = get_post_meta($product_id, '_sku', true);
		if (!$possible_sku) {
			update_post_meta($product_id, '_sku', static::$slug . '-' . $post_id );
		}

		if ($manage_stock) {

			update_post_meta($product_id, '_manage_stock', 'yes');
			update_post_meta($product_id, '_backorders', 'no');
			update_post_meta($product_id, '_stock', $stock_quantity);
			update_post_meta($product_id, '_stock_status', ($stock_quantity > 0) ? 'instock' : 'outofstock');

		} else {

			update_post_meta($product_id, '_manage_stock', 'no');
			update_post_meta($product_id, '_backorders', 'no');
			update_post_meta($product_id, '_stock', '');
			update_post_meta($product_id, '_stock_status', 'instock');

		}

		if ($sold_individually) {

			update_post_meta($product_id, '_sold_individually', 'yes');

		}

		NAM_Classes::do_creation_meta($post_id, $product_id);
		NAM_Membership_Tier::do_creation_meta($post_id, $product_id);

	}

	/**
	 * For a given shadowing product, get the parent custom post
	 * for the id. Essentially a reverse lookup on the shadowing product.
	 *
	 * @param int $product_id the id of the product to look up.
	 * @param Array<int> an array of post ids.
	 */
	public static function get_parent_posts($product_id) {
		$parents = get_posts(array(
			'post_type' => static::$slug,
			'meta_query' => array(
				array(
					'key' => 'managed_field_related_post', // name of custom field
					'value' => '"' . $product_id . '"', // matches exactly "123", not just 123. This prevents a match for "1234"
					'compare' => 'LIKE',
				),
			),
		));

		return array_map(function ($p) {return $p->ID;}, $parents);
	}

	/**
	 * This routine sets all the required product taxonomy terms for reporting
	 * purposes.
	 *
	 * @param int $post_id the id of the post that owns this custom product
	 * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
	 */
	public static function set_shadowing_product_custom_trackers($title, $post_id, $product_id) {

	}

	/**
	 * This routine sets all the required product taxonomy terms for reporting
	 * purposes.
	 *
	 * @param int $post_id the id of the post that owns this custom product
	 * @param int $product_id the id of the product that implements ecommerce functionality for the Custom Post.
	 */
	public static function set_shadowing_product_categories($title, $post_id, $product_id) {

		$existing_categories = get_the_terms($product_id, 'product_cat');

		if ($existing_categories) {

			$existing_categories = array_map(function ($term) {return $term->name;}, $existing_categories);

		} else {

			$existing_categories = array();

		}

		$categories = static::get_product_categories($post_id);

		$categories = array_unique(array_merge($categories, $existing_categories));

		$term_ids = array();

		foreach ($categories as $category) {

			if ($term_object = get_term_by('name', $category, 'product_cat')) {

				array_push($term_ids, (int) $term_object->term_id);

			} else {

				$term = wp_insert_term(
					$category,
					'product_cat',
					array(
						'description' => '',
						'slug' => sanitize_title_with_dashes($category),
					)
				);

				array_push($term_ids, (int) $term['term_id']);

			}

		}

		wp_set_object_terms($product_id, $term_ids, 'product_cat');

	}

	/**
	 * This routine is implemented by subclasses and is responsible for
	 * returning the taxonomies that are relevant to the executing CPT.
	 *
	 * @param int post_id the post id of the post being processed.
	 * @return array an array of category names derived from the categories associated with this Custom Post.
	 */
	public static abstract function get_product_categories($post_id);

	// STOCK ACTIONS ---

    /**
     * This function handles registering actions to take
     * to keep stock levels between products and their parent posts
     * consistent. Stock actions happen when:
     *
     * - A product is purchased.
     * - A refund is processed.
     * - stock is manually edited on the product.
     */
	public static function register_stock_actions() {
		$called_class = get_called_class();
		//add_action('woocommerce_reduce_order_stock', array($called_class, 'reduce_parent_post_stock'));
        add_action('woocommerce_variation_set_stock', array( $called_class, 'reduce_parent_post_stock'));
        add_action('woocommerce_product_set_stock', array( $called_class, 'reduce_parent_post_stock'));
	}

    /**
     * Given a product whose stock has just been adjusted,
     * set the parent post's stock to match the product's stock.
     *
     * @param WC_Product $product the product whose stock has just been updated.
     */
    public static function reduce_parent_post_stock( $product ) {

        $parent_post = static::get_parent_posts( $product->id );

        if ( $parent_post ) {

            $parent_post_id = $parent_post[0];
            $manage_stock = get_field(static::$field_keys['manage_stock'], $parent_post_id);

            if ( $manage_stock ) {

                $stock_quantity = $product->get_stock_quantity();
                update_field(static::$field_keys['stock_quantity'], $stock_quantity, $parent_post_id);

            }

        }

    }

}
