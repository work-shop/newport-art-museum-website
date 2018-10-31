<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.7.7
 */

function wpcl_add_section( $sections ) {
	$sections['wpcl'] = __( 'Product Customer List', 'wc-product-customer-list' );
	return $sections;
}
add_filter( 'woocommerce_get_sections_products', 'wpcl_add_section' );


if ( wpcl_activation()->is__premium_only() ) {
	// Pro

	// Get order custom fields
	function wpcl_order_meta_keys(){
	    global $wpdb;
	    $post_type = 'shop_order';
	    $query = "
	        SELECT DISTINCT($wpdb->postmeta.meta_key)
	        FROM $wpdb->posts
	        LEFT JOIN $wpdb->postmeta
	        ON $wpdb->posts.ID = $wpdb->postmeta.post_id
	        WHERE $wpdb->posts.post_type = '%s'
	        AND $wpdb->postmeta.meta_key != ''
	        /*AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)'
	        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'*/
	    ";
	    $meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));
	    $custom_fields = array();
	    foreach($meta_keys as $meta_key) {
	    	$custom_fields[$meta_key] = $meta_key;
	    }
	    return $custom_fields;
	}

	// Get order product fields (Rightpress)
	function wpcl_rightpress_product_fields(){
		// Query Arguments
		$args = array(
			'post_type' => array('wccf_product_field'),
			'post_status' => array('publish'),
			'posts_per_page' => -1,
			'nopaging' => true,
			'order' => 'DESC',
			'orderby' => 'none',
		);

		// The Query
		$query = new WP_Query( $args );

		// The Loop
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$rightpress_label = get_post_meta( get_the_ID(), 'label', true);
				//$rightpres_label = $rightpres_label[0];
				$rightpress_key = get_post_meta( get_the_ID(), 'key', true);
				//$rightpres_key = $rightpres_key[0];
				$rightpress_product_fields[$rightpress_key] = $rightpress_label;
					//$rightpress_product_fields[] = get_the_title();
				//$rightpress_product_fields[] = get_post_custom($post->ID);
			}
		} else {
			// no posts found
		}
		return $rightpress_product_fields;
		/* Restore original Post Data */
		wp_reset_postdata();
	}

	// Get order checkout fields (Rightpress)
	function wpcl_rightpress_checkout_fields(){
		// Query Arguments
		$args = array(
			'post_type' => array('wccf_checkout_field'),
			'post_status' => array('publish'),
			'posts_per_page' => -1,
			'nopaging' => true,
			'order' => 'DESC',
			'orderby' => 'none',
		);

		// The Query
		$query = new WP_Query( $args );

		// The Loop
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$rightpress_checkout_label = get_post_meta( get_the_ID(), 'label', true);
				//$rightpres_label = $rightpres_label[0];
				$rightpress_checkout_key = get_post_meta( get_the_ID(), 'key', true);
				//$rightpres_key = $rightpres_key[0];
				$rightpress_checkout_fields[$rightpress_checkout_key] = $rightpress_checkout_label;
					//$rightpress_product_fields[] = get_the_title();
				//$rightpress_product_fields[] = get_post_custom($post->ID);
			}
		} else {
			// no posts found
		}
		return $rightpress_checkout_fields;
		/* Restore original Post Data */
		wp_reset_postdata();
	}

}

function wpcl_all_settings( $settings, $current_section ) {

	// Enqueue admin stylesheet

	wp_register_style( 'wpcl-settings-css', plugin_dir_url( __FILE__ ) . '../admin/assets/settings.css', false, '2.7.4' );
	wp_enqueue_style( 'wpcl-settings-css' );

	// Get all available statuses
	$statuses = array();
	foreach ( get_post_stati( array( 'show_in_admin_status_list' => true ), 'objects' ) as $status ) {
		if ( ! in_array( $status->name, array( 'publish', 'draft', 'pending', 'trash', 'future', 'private', 'auto-draft' ) ) ) {

			$statuses[$status->name] = $status->label;
		}
	}
	if ( $current_section == 'wpcl' ) {
		$settings_wpcl = array();
		$settings_wpcl[] = array( 'name' => __( 'Product Customer List for WooCommerce', 'wc-product-customer-list' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure Product Customer List for WooCommerce', 'wc-product-customer-list' ), 'id' => 'wpcl-settings' );
		$settings_wpcl[] = array(
			'name'    => __( 'Order status', 'wc-product-customer-list' ),
			'desc'    => __( 'Select one or multiple order statuses for which you will display the customers.', 'wc-product-customer-list' ),
			'id'      => 'wpcl_order_status_select',
			'css'     => 'min-width:300px;',
			'default' => array('wc-completed'),
			'type'    => 'multiselect',
			'options' => $statuses,
			'desc_tip' =>  true,
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Partial refunds', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_partial_refunds',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Hide partially refunded orders', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order number column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_number',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order number column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order date column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_date',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order date column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order status column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_status',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order status column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order quantity column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_qty',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order quantity column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order total column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_total',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order total column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Payment method column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_payment',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable payment method column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping method column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_shipping',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping method column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Coupons used column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_coupon',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable coupons used column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Variations column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_variations',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable variations column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Customer message column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_customer_message',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable customer message column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Customer ID', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_customer_ID',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable customer ID column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Customer username', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_customer_username',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable customer username column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Customer display name', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_customer_display_name',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable customer display name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing first name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_first_name',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing first name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing last name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_last_name',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing last name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing company column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_company',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing company column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing e-mail column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_email',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing e-mail column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing phone column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_phone',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing phone column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing address 1 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_address_1',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing address 1 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing address 2 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_address_2',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing address 2 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing city column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_city',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing city column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing state column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_state',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing state column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing Postal Code / Zip column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_postalcode',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing postal code / Zip column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing country column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_country',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc' 		=> __( 'Enable billing country column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping first name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_first_name',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping first name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping last name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_last_name',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping last name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping company column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_company',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping company column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping address 1 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_address_1',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping address 1 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping address 2 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_address_2',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping address 2 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping city column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_city',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping city column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping state column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_state',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping state column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping Postal Code / Zip column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_postalcode',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping postal code / Zip column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping country column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_country',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping country column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'PDF orientation', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_export_pdf_orientation',
			'css'		=> 'min-width:300px;',
			'default'	=> array('portrait'),
			'type'		=> 'select',
			'options'	=> array(
				'portrait' 		=> __( 'Portrait', 'wc-product-customer-list' ),
				'landscape'		=> __( 'Landscape', 'wc-product-customer-list' ),
			),
			'desc_tip'	=>  false,
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'PDF page size', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_export_pdf_pagesize',
			'css'		=> 'min-width:300px;',
			'default'	=> array('letter'),
			'type'		=> 'select',
			'options'	=> array(
				'LETTER'	=> __( 'US Letter', 'wc-product-customer-list' ),
				'LEGAL'		=> __( 'US Legal', 'wc-product-customer-list' ),
				'A3'		=> __( 'A3', 'wc-product-customer-list' ),
				'A4'		=> __( 'A4', 'wc-product-customer-list' ),
				'A5'		=> __( 'A5', 'wc-product-customer-list' ),
			),
			'desc_tip' =>  false,
		);

		$settings_wpcl[] = array( 'type' => 'sectionend', 'id' => 'wpcl-settings' );

		if ( wpcl_activation()->is__premium_only() ) {

			// Pro

			$settings_wpcl[] = array( 'name' => __( 'Product Customer List for WooCommerce Premium', 'wc-product-customer-list' ), 'type' => 'title', 'id' => 'wpclpro' );

			// Custom fields

			$settings_wpcl[] = array(
				'name'    => __( 'Checkout custom fields', 'wc-product-customer-list' ),
				'desc'    => __( 'Select one or multiple custom fields to display in the table.', 'wc-product-customer-list' ),
				'id'      => 'wpcl_custom_fields',
				'css'     => 'min-width:300px;',
				'type'    => 'multiselect',
				'options' => wpcl_order_meta_keys(),
				'desc_tip' =>  true,
			);

			// Default column order

			$settings_wpcl[] = array(
				'name'    => __( 'Index of column for default order', 'wc-product-customer-list' ),
				'desc'    => __( 'Select which column index the data should be sorted by (Numbers only, 0 = first column)', 'wc-product-customer-list' ),
				'id'      => 'wpcl_column_order_index',
				'default' => '0',
				'css'     => 'min-width:300px;',
				'type'    => 'number',
				'desc_tip' =>  true,
			);

			// Default sort (ASC/DESC)

			$settings_wpcl[] = array(
				'name'    => __( 'Direction of default order', 'wc-product-customer-list' ),
				'desc'    => __( 'Select one or multiple custom fields to display in the table.', 'wc-product-customer-list' ),
				'id'      => 'wpcl_column_order_direction',
				'css'     => 'min-width:300px;',
				'default'	=> array('asc'),
				'type'		=> 'select',
				'options'	=> array(
					'asc'		=> __( 'Ascending', 'wc-product-customer-list' ),
					'desc'	=> __( 'Descending', 'wc-product-customer-list' ),
				),
				'desc_tip' =>  true,
			);

			// State save

			$settings_wpcl[] = array(
				'name'		=> __( 'State save', 'wc-product-customer-list' ),
				'id'		=> 'wpcl_state_save',
				'default'	=> 'yes',
				'type' 		=> 'checkbox',
				'css' 		=> 'min-width:300px;',
				'desc'		=> __( 'Enable state save', 'wc-product-customer-list' ),
			);

			// Wootours/Wooevents

			if ( function_exists( 'wt_get_plugin_url' ) || function_exists( 'we_get_plugin_url' ) ) {
				$settings_wpcl[] = array(
					'name'		=> __( 'WooTours/WooEvents column', 'wc-product-customer-list' ),
					'id'		=> 'wpcl_wootours',
					'default'	=> 'no',
					'type'		=> 'checkbox',
					'css'		=> 'min-width:300px;',
					'desc'		=> __( 'Activate WooTours/WooEvents column', 'wc-product-customer-list' ),
				);
			}

			// WooCommerce Custom Fields (Rightpress) - Product Fields

			if (class_exists('WCCF')) {
				$settings_wpcl[] = array(
					'name'    => __( 'Product fields (Rightpress)', 'woocommerce' ),
					'desc'    => __( 'Select one or multiple custom fields to display in the table.', 'wc-product-customer-list' ),
					'id'      => 'wpcl_rightpress_custom_fields',
					'css'     => 'min-width:300px;',
					'type'    => 'multiselect',
					'options' => wpcl_rightpress_product_fields(),
					'desc_tip' =>  true,
				);
			}

			if (class_exists('WCCF')) {
				$settings_wpcl[] = array(
					'name'    => __( 'Checkout fields (Rightpress)', 'woocommerce' ),
					'desc'    => __( 'Select one or multiple custom fields to display in the table.', 'wc-product-customer-list' ),
					'id'      => 'wpcl_rightpress_checkout_fields',
					'css'     => 'min-width:300px;',
					'type'    => 'multiselect',
					'options' => wpcl_rightpress_checkout_fields(),
					'desc_tip' =>  true,
				);
			}

			if (class_exists('WCCF')) {
				$settings_wpcl[] = array(
					'name'		=> __( 'Split rows by quantity', 'wc-product-customer-list' ),
					'id'		=> 'wpcl_split_rows',
					'default'	=> 'no',
					'type'		=> 'checkbox',
					'css'		=> 'min-width:300px;',
					'desc'		=> __( 'One row per product', 'wc-product-customer-list' ),
				);
			}

			$settings_wpcl[] = array( 'type' => 'sectionend', 'id' => 'wpclpro' );

		}

		$settings_wpcl = apply_filters( 'wpcl_settings_filter', $settings_wpcl );

		return $settings_wpcl;

	} else {

		return $settings;
	}
}

add_filter( 'woocommerce_get_settings_products', 'wpcl_all_settings', 10, 2 );