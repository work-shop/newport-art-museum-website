<?php

/**
 * @package WC_Product_Customer_List
 * @version 2.7.7
 */
// Display cell
if ( !function_exists( 'wpcl_display_cell' ) ) {
    function wpcl_display_cell( $option_name, $data )
    {
        echo  '<td><p>' ;
        switch ( $option_name ) {
            case 'wpcl_order_number':
                echo  '<a href="' . admin_url( 'post.php' ) . '?post=' . $data . '&action=edit" target="_blank">' . $data . '</a>' ;
                break;
            case 'wpcl_billing_email':
                echo  '<a href="mailto:' . $data . '">' . $data . '</a>' ;
                break;
            case 'wpcl_billing_phone':
                echo  '<a href="tel:' . $data . '">' . $data . '</a>' ;
                break;
            case 'wpcl_customer_id':
                if ( $data ) {
                    echo  '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $data . '" target="_blank">' . $data . '</a>' ;
                }
                break;
            case 'wpcl_variations':
                echo  '<span style="max-height: 50px; overflow-y: auto; display: block;">' ;
                foreach ( $data->get_meta_data() as $itemvariation ) {
                    if ( !is_array( $itemvariation->value ) ) {
                        echo  '<strong>' . wc_attribute_label( $itemvariation->key ) . '</strong>: &nbsp;' . wc_attribute_label( $itemvariation->value ) . '<br />' ;
                    }
                }
                echo  '</span>' ;
                break;
            default:
                echo  $data ;
                break;
        }
        echo  '</p></td>' ;
    }

}
if ( !function_exists( 'wpcl_count_rightpress_entries' ) ) {
    function wpcl_count_rightpress_entries( $display_values )
    {
        $found_keys = array();
        $highest_count = 0;
        foreach ( $display_values as $display_value ) {
            
            if ( !isset( $found_keys[$display_value['key']] ) ) {
                $found_keys[$display_value['key']] = 1;
            } else {
                $found_keys[$display_value['key']]++;
            }
            
            $highest_count = ( $found_keys[$display_value['key']] > $highest_count ? $found_keys[$display_value['key']] : $highest_count );
        }
        return $highest_count;
    }

}
// Load metabox at bottom of product admin screen

if ( !function_exists( 'wpcl_post_meta_boxes_setup' ) ) {
    add_action( 'load-post.php', 'wpcl_post_meta_boxes_setup' );
    function wpcl_post_meta_boxes_setup()
    {
        add_action( 'add_meta_boxes', 'wpcl_add_post_meta_boxes' );
    }

}

// Set metabox defaults
if ( !function_exists( 'wpcl_add_post_meta_boxes' ) ) {
    function wpcl_add_post_meta_boxes()
    {
        add_meta_box(
            'customer-bought',
            esc_html__( 'Customers who bought this product', 'wc-product-customer-list' ),
            'wpcl_post_class_meta_box',
            'product',
            'normal',
            'default'
        );
    }

}
// Output customer list inside metabox
if ( !function_exists( 'wpcl_post_class_meta_box' ) ) {
    function wpcl_post_class_meta_box( $object, $box )
    {
        global  $sitepress, $post, $wpdb ;
        // Get product ID
        
        if ( !function_exists( 'yith_wcp_premium_init' ) ) {
            $post_id = $post->ID;
        } else {
            // Fix for YITH Composite Products Premium Bug
            $post_id = intval( $_GET['post'] );
        }
        
        if ( get_option( 'wpcl_split_rows' ) == 'yes' ) {
            $split_rows = 'true';
        }
        //$split_rows = ! empty( $_REQUEST['wpcl_split_rows'] ) && $_REQUEST['wpcl_split_rows'] == 'true';
        $all_information = wpcl_gather_data( $post_id, $split_rows );
        ?>

		<div class="wpcl-init"></div>
		<div id="postcustomstuff" class="wpcl">
			<?php 
        
        if ( !empty($all_information['data']) ) {
            ?>
				<table id="list-table" style="width:100%">
					<thead>
					<tr>
						<?php 
            foreach ( $all_information['columns'] as $column ) {
                ?>
							<th>
								<strong><?php 
                echo  $column ;
                ?></strong>
							</th>
						<?php 
            }
            ?>
						<?php 
            // Add wpcl_admin_add_column_head action
            do_action( 'wpcl_admin_add_column_head', $all_information['columns'] );
            ?>
					</tr>
					</thead>
					<tbody>
					<?php 
            foreach ( $all_information['data'] as $data_row ) {
                ?>
						<tr>
							<?php 
                foreach ( $all_information['columns'] as $column_key => $column_name ) {
                    
                    if ( isset( $data_row[$column_key] ) ) {
                        wpcl_display_cell( $column_key, $data_row[$column_key] );
                    } else {
                        wpcl_display_cell( $column_key, '' );
                    }
                
                }
                do_action(
                    'wpcl_admin_add_row',
                    $data_row['order'],
                    $data_row['product'],
                    $data_row['sale']
                );
                ?>
						</tr>
					<?php 
            }
            ?>


					</tbody>
				</table>
				<?php 
            
            if ( get_option( 'wpcl_order_qty' ) == 'yes' ) {
                ?>
					<p class="total">
						<?php 
                echo  '<strong>' . __( 'Total', 'wc-product-customer-list' ) . ' : </strong>' . $all_information['product_count'] ;
                ?>
					</p>
				<?php 
            }
            
            ?>
				<a href="mailto:?bcc=<?php 
            echo  $all_information['email_list'] ;
            ?>" class="button"><?php 
            _e( 'Email all customers', 'wc-product-customer-list' );
            ?></a>
				<a href="#" class="button" id="email-selected" disabled><?php 
            _e( 'Email selected customers', 'wc-product-customer-list' );
            ?></a>
				<?php 
            do_action( 'wpcl_after_email_button', $all_information['email_list'] );
            /*if ( wpcl_activation()->is__premium_only() ) {
            			if ( $split_rows ) {
            				?>
            				<a href="<?php echo add_query_arg( 'wpcl_split_rows', 'false' );; ?>" class="button" id="wpcl-split-yes-no"><?php _e( 'Unsplit rows for Rightpress', 'wc-product-customer-list' ); ?></a>
            				<?php
            			} else {
            				?>
            				<a href="<?php echo add_query_arg( 'wpcl_split_rows', 'true' );; ?>" class="button" id="wpcl-split-yes-no"><?php _e( 'Split rows for Rightpress', 'wc-product-customer-list' ); ?></a><?php
            			}
            		}*/
        } else {
            _e( 'This product currently has no customers', 'wc-product-customer-list' );
        }
        
        ?>
		</div>
		<?php 
    }

}
if ( !function_exists( 'wpcl_gather_data' ) ) {
    function wpcl_gather_data( $post_id, $split_rows = false )
    {
        global  $sitepress, $post, $wpdb ;
        $productcount = array();
        $all_information = array();
        // get the adjusted post if WPML is active
        
        if ( isset( $sitepress ) ) {
            $trid = $sitepress->get_element_trid( $post_id, 'post_product' );
            $translations = $sitepress->get_element_translations( $trid, 'product' );
            $post_id = array();
            foreach ( $translations as $lang => $translation ) {
                $post_id[] = $translation->element_id;
            }
        }
        
        // Query the orders related to the product
        $order_statuses = array_map( 'esc_sql', (array) get_option( 'wpcl_order_status_select', array( 'wc-completed' ) ) );
        $order_statuses_string = "'" . implode( "', '", $order_statuses ) . "'";
        $post_id = array_map( 'esc_sql', (array) $post_id );
        $post_string = "'" . implode( "', '", $post_id ) . "'";
        $item_sales = $wpdb->get_results( $wpdb->prepare( "SELECT o.ID as order_id, oi.order_item_id FROM\n\t\t\t{$wpdb->prefix}woocommerce_order_itemmeta oim\n\t\t\tINNER JOIN {$wpdb->prefix}woocommerce_order_items oi\n\t\t\tON oim.order_item_id = oi.order_item_id\n\t\t\tINNER JOIN {$wpdb->posts} o\n\t\t\tON oi.order_id = o.ID\n\t\t\tWHERE oim.meta_key = %s\n\t\t\tAND oim.meta_value IN ( {$post_string} )\n\t\t\tAND o.post_status IN ( {$order_statuses_string} )\n\t\t\tAND o.post_type NOT IN ('shop_order_refund')\n\t\t\tORDER BY o.ID DESC", '_product_id' ) );
        // Get selected columns from the options page
        $product = WC()->product_factory->get_product( $post );
        $fields = array(
            'wpcl_order_number'          => array(
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Order', 'wc-product-customer-list' ),
            'object'             => 'sale',
            'property'           => 'order_id',
            'method'             => false,
            'format'             => false,
        ),
            'wpcl_order_date'            => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Date', 'wc-product-customer-list' ),
            'object'             => 'order',
            'property'           => false,
            'method'             => 'get_date_created',
            'format'             => "date_format( %someplaceholder, 'Y-m-d' ')",
        ),
            'wpcl_billing_first_name'    => array(
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Billing First name', 'wc-product-customer-list' ),
            'object'             => 'order',
            'property'           => false,
            'method'             => 'get_billing_first_name',
            'format'             => false,
        ),
            'wpcl_billing_last_name'     => array(
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Billing Last name', 'wc-product-customer-list' ),
        ),
            'wpcl_billing_company'       => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Company', 'wc-product-customer-list' ),
        ),
            'wpcl_billing_email'         => array(
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Billing E-mail', 'wc-product-customer-list' ),
        ),
            'wpcl_billing_phone'         => array(
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Billing Phone', 'wc-product-customer-list' ),
        ),
            'wpcl_billing_address_1'     => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Address 1', 'wc-product-customer-list' ),
        ),
            'wpcl_billing_address_2'     => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Address 2', 'wc-product-customer-list' ),
        ),
            'wpcl_billing_city'          => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing City', 'wc-product-customer-list' ),
        ),
            'wpcl_billing_state'         => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing State', 'wc-product-customer-list' ),
        ),
            'wpcl_billing_postalcode'    => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Postal Code / Zip', 'wc-product-customer-list' ),
        ),
            'wpcl_billing_country'       => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Billing Country', 'wc-product-customer-list' ),
        ),
            'wpcl_shipping_first_name'   => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping First name', 'wc-product-customer-list' ),
        ),
            'wpcl_shipping_last_name'    => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Last name', 'wc-product-customer-list' ),
        ),
            'wpcl_shipping_company'      => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Company', 'wc-product-customer-list' ),
        ),
            'wpcl_shipping_address_1'    => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Address 1', 'wc-product-customer-list' ),
        ),
            'wpcl_shipping_address_2'    => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Address 2', 'wc-product-customer-list' ),
        ),
            'wpcl_shipping_city'         => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping City', 'wc-product-customer-list' ),
        ),
            'wpcl_shipping_state'        => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping State', 'wc-product-customer-list' ),
        ),
            'wpcl_shipping_postalcode'   => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Postal Code / Zip', 'wc-product-customer-list' ),
        ),
            'wpcl_shipping_country'      => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping Country', 'wc-product-customer-list' ),
        ),
            'wpcl_customer_message'      => array(
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Customer Message', 'wc-product-customer-list' ),
        ),
            'wpcl_customer_id'           => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Customer ID', 'wc-product-customer-list' ),
        ),
            'wpcl_customer_username'     => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Customer username', 'wc-product-customer-list' ),
        ),
            'wpcl_customer_display_name' => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Customer display name', 'wc-product-customer-list' ),
        ),
            'wpcl_order_status'          => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Order Status', 'wc-product-customer-list' ),
        ),
            'wpcl_order_payment'         => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Payment method', 'wc-product-customer-list' ),
        ),
            'wpcl_order_shipping'        => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Shipping method', 'wc-product-customer-list' ),
        ),
            'wpcl_order_coupon'          => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Coupons used', 'wc-product-customer-list' ),
        ),
            'wpcl_variations'            => array(
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Variation', 'wc-product-customer-list' ),
        ),
            'wpcl_order_total'           => array(
            'default_value'      => 'no',
            'column_pretty_name' => __( 'Order total', 'wc-product-customer-list' ),
        ),
            'wpcl_order_qty'             => array(
            'default_value'      => 'yes',
            'column_pretty_name' => __( 'Qty', 'wc-product-customer-list' ),
        ),
        );
        foreach ( $fields as $option_name => $option_values ) {
            if ( get_option( $option_name, $option_values['default_value'] ) == 'yes' ) {
                $columns[$option_name] = $option_values['column_pretty_name'];
            }
        }
        foreach ( $item_sales as $sale ) {
            $order = wc_get_order( $sale->order_id );
            $formatted_total = $order->get_formatted_order_total();
            // Get quantity
            $refunded_qty = 0;
            $items = $order->get_items();
            foreach ( $items as $item_id => $item ) {
                if ( $item['product_id'] == $post->ID ) {
                    $refunded_qty += $order->get_qty_refunded_for_item( $item_id );
                }
            }
            // Only one product per line if rows are split
            $quantity = wc_get_order_item_meta( $sale->order_item_id, '_qty', true );
            $quantity += $refunded_qty;
            // Check for partially refunded orders
            if ( $quantity == 0 && get_option( 'wpcl_order_partial_refunds', 'no' ) == 'yes' ) {
                // Order has been partially refunded
                continue;
            }
            $current_row = array();
            $current_row['billing_email'] = $order->get_billing_email();
            $current_row['order'] = $order;
            $current_row['product'] = $product;
            $current_row['sale'] = $sale;
            if ( isset( $columns['wpcl_order_number'] ) ) {
                $current_row['wpcl_order_number'] = $sale->order_id;
            }
            if ( isset( $columns['wpcl_order_date'] ) ) {
                $current_row['wpcl_order_date'] = date_format( $order->get_date_created(), 'Y-m-d' );
            }
            if ( isset( $columns['wpcl_billing_first_name'] ) ) {
                $current_row['wpcl_billing_first_name'] = $order->get_billing_first_name();
            }
            if ( isset( $columns['wpcl_billing_last_name'] ) ) {
                $current_row['wpcl_billing_last_name'] = $order->get_billing_last_name();
            }
            if ( isset( $columns['wpcl_billing_company'] ) ) {
                $current_row['wpcl_billing_company'] = $order->get_billing_company();
            }
            if ( isset( $columns['wpcl_billing_email'] ) ) {
                $current_row['wpcl_billing_email'] = $order->get_billing_email();
            }
            if ( isset( $columns['wpcl_billing_phone'] ) ) {
                $current_row['wpcl_billing_phone'] = $order->get_billing_phone();
            }
            if ( isset( $columns['wpcl_billing_address_1'] ) ) {
                $current_row['wpcl_billing_address_1'] = $order->get_billing_address_1();
            }
            if ( isset( $columns['wpcl_billing_address_2'] ) ) {
                $current_row['wpcl_billing_address_2'] = $order->get_billing_address_2();
            }
            if ( isset( $columns['wpcl_billing_city'] ) ) {
                $current_row['wpcl_billing_city'] = $order->get_billing_city();
            }
            if ( isset( $columns['wpcl_billing_state'] ) ) {
                $current_row['wpcl_billing_state'] = $order->get_billing_state();
            }
            if ( isset( $columns['wpcl_billing_postalcode'] ) ) {
                $current_row['wpcl_billing_postalcode'] = $order->get_billing_postcode();
            }
            if ( isset( $columns['wpcl_billing_country'] ) ) {
                $current_row['wpcl_billing_country'] = $order->get_billing_country();
            }
            if ( isset( $columns['wpcl_shipping_first_name'] ) ) {
                $current_row['wpcl_shipping_first_name'] = $order->get_shipping_first_name();
            }
            if ( isset( $columns['wpcl_shipping_last_name'] ) ) {
                $current_row['wpcl_shipping_last_name'] = $order->get_shipping_last_name();
            }
            if ( isset( $columns['wpcl_shipping_company'] ) ) {
                $current_row['wpcl_shipping_company'] = $order->get_shipping_company();
            }
            if ( isset( $columns['wpcl_shipping_address_1'] ) ) {
                $current_row['wpcl_shipping_address_1'] = $order->get_shipping_address_1();
            }
            if ( isset( $columns['wpcl_shipping_address_2'] ) ) {
                $current_row['wpcl_shipping_address_2'] = $order->get_shipping_address_2();
            }
            if ( isset( $columns['wpcl_shipping_city'] ) ) {
                $current_row['wpcl_shipping_city'] = $order->get_shipping_city();
            }
            if ( isset( $columns['wpcl_shipping_state'] ) ) {
                $current_row['wpcl_shipping_state'] = $order->get_shipping_state();
            }
            if ( isset( $columns['wpcl_shipping_postalcode'] ) ) {
                $current_row['wpcl_shipping_postalcode'] = $order->get_shipping_postcode();
            }
            if ( isset( $columns['wpcl_shipping_country'] ) ) {
                $current_row['wpcl_shipping_country'] = $order->get_shipping_country();
            }
            if ( isset( $columns['wpcl_customer_message'] ) ) {
                $current_row['wpcl_customer_message'] = $order->get_customer_note();
            }
            $customer_id = $order->get_customer_id();
            $customer_info = ( !empty($customer_id) ? get_userdata( $customer_id ) : '' );
            $customer_username = ( !empty($customer_info) ? $customer_info->user_login : '' );
            $customer_userlogin = ( !empty($customer_info) ? get_admin_url() . 'user-edit.php?user_id=' . $customer_id : '' );
            $customer_displayname = ( !empty($customer_info) ? $customer_info->display_name : '' );
            if ( isset( $columns['wpcl_customer_login'] ) ) {
                $current_row['wpcl_customer_login'] = $customer_userlogin;
            }
            if ( isset( $columns['wpcl_customer_id'] ) ) {
                $current_row['wpcl_customer_id'] = $customer_id;
            }
            if ( isset( $columns['wpcl_customer_username'] ) ) {
                $current_row['wpcl_customer_username'] = $customer_username;
            }
            if ( isset( $columns['wpcl_customer_display_name'] ) ) {
                $current_row['wpcl_customer_display_name'] = $customer_displayname;
            }
            if ( isset( $columns['wpcl_order_status'] ) ) {
                $current_row['wpcl_order_status'] = wc_get_order_status_name( $order->get_status() );
            }
            if ( isset( $columns['wpcl_order_payment'] ) ) {
                $current_row['wpcl_order_payment'] = $order->get_payment_method_title();
            }
            if ( isset( $columns['wpcl_order_shipping'] ) ) {
                $current_row['wpcl_order_shipping'] = $order->get_shipping_method();
            }
            if ( isset( $columns['wpcl_order_coupon'] ) ) {
                $current_row['wpcl_order_coupon'] = implode( ', ', $order->get_used_coupons() );
            }
            
            if ( isset( $columns['wpcl_variations'] ) ) {
                $current_row['wpcl_variations'] = $order->get_item( $sale->order_item_id );
                $current_row['wpcl_variations_data'] = array();
                foreach ( $current_row['wpcl_variations']->get_meta_data() as $itemvariation ) {
                    if ( !is_array( $itemvariation->value ) ) {
                        $current_row['wpcl_variations_data'][] = array(
                            'label' => wc_attribute_label( $itemvariation->key ),
                            'value' => wc_attribute_label( $itemvariation->value ),
                        );
                    }
                }
            }
            
            if ( isset( $columns['wpcl_order_total'] ) ) {
                $current_row['wpcl_order_total'] = $order->get_formatted_order_total();
            }
            if ( isset( $columns['wpcl_order_qty'] ) ) {
                
                if ( $split_rows == 'true' ) {
                    $current_row['wpcl_order_qty'] = 1;
                } else {
                    $current_row['wpcl_order_qty'] = $quantity;
                }
            
            }
            $productcount[] = $quantity;
            if ( $order->get_billing_email() ) {
                $all_information['email_list'][] = $order->get_billing_email();
            }
            $all_information['data'][] = $current_row;
        }
        // End foreach
        
        if ( !empty($all_information['email_list']) ) {
            $all_information['email_list'] = array_unique( $all_information['email_list'] );
            $all_information['email_list'] = implode( ',', $all_information['email_list'] );
        }
        
        $all_information['columns'] = $columns;
        $all_information['product_count'] = array_sum( $productcount );
        return $all_information;
    }

}