<?php

/**
 * @package WC_Product_Customer_List
 * @version 2.7.7
 */
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
        
        // Check for translated products if WPML is activated
        
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
        $columns = array();
        if ( get_option( 'wpcl_order_number', 'yes' ) == 'yes' ) {
            $columns[] = __( 'Order', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_order_date', 'no' ) == 'yes' ) {
            $columns[] = __( 'Date', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_first_name', 'yes' ) == 'yes' ) {
            $columns[] = __( 'Billing First name', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_last_name', 'yes' ) == 'yes' ) {
            $columns[] = __( 'Billing Last name', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_company', 'no' ) == 'yes' ) {
            $columns[] = __( 'Billing Company', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_email', 'yes' ) == 'yes' ) {
            $columns[] = __( 'Billing E-mail', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_phone', 'yes' ) == 'yes' ) {
            $columns[] = __( 'Billing Phone', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_address_1', 'no' ) == 'yes' ) {
            $columns[] = __( 'Billing Address 1', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_address_2', 'no' ) == 'yes' ) {
            $columns[] = __( 'Billing Address 2', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_city', 'no' ) == 'yes' ) {
            $columns[] = __( 'Billing City', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_state', 'no' ) == 'yes' ) {
            $columns[] = __( 'Billing State', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_postalcode', 'no' ) == 'yes' ) {
            $columns[] = __( 'Billing Postal Code / Zip', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_billing_country', 'no' ) == 'yes' ) {
            $columns[] = __( 'Billing Country', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_shipping_first_name', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping First name', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_shipping_last_name', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping Last name', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_shipping_company', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping Company', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_shipping_address_1', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping Address 1', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_shipping_address_2', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping Address 2', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_shipping_city', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping City', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_shipping_state', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping State', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_shipping_postalcode', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping Postal Code / Zip', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_shipping_country', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping Country', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_customer_message', 'yes' ) == 'yes' ) {
            $columns[] = __( 'Customer Message', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_customer_id', 'no' ) == 'yes' ) {
            $columns[] = __( 'Customer ID', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_customer_username', 'no' ) == 'yes' ) {
            $columns[] = __( 'Customer username', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_customer_display_name', 'no' ) == 'yes' ) {
            $columns[] = __( 'Customer display name', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_order_status', 'no' ) == 'yes' ) {
            $columns[] = __( 'Order Status', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_order_payment', 'no' ) == 'yes' ) {
            $columns[] = __( 'Payment method', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_order_shipping', 'no' ) == 'yes' ) {
            $columns[] = __( 'Shipping method', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_order_coupon', 'no' ) == 'yes' ) {
            $columns[] = __( 'Coupons used', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_variations', 'yes' ) == 'yes' ) {
            $columns[] = __( 'Variation', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_order_total', 'no' ) == 'yes' ) {
            $columns[] = __( 'Order total', 'wc-product-customer-list' );
        }
        if ( get_option( 'wpcl_order_qty', 'yes' ) == 'yes' ) {
            $columns[] = __( 'Qty', 'wc-product-customer-list' );
        }
        ?>

		<div class="wpcl-init"></div>
		<div id="postcustomstuff" class="wpcl">
			<?php 
        
        if ( $item_sales ) {
            $email_list = array();
            $productcount = array();
            ?>
				<table id="list-table" style="width:100%">
					<thead>
						<tr>
							<?php 
            foreach ( $columns as $column ) {
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
            do_action( 'wpcl_admin_add_column_head', $columns );
            ?>
						</tr>
					</thead>
					<tbody>
						<?php 
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
                $quantity = wc_get_order_item_meta( $sale->order_item_id, '_qty', true );
                $quantity += $refunded_qty;
                // Check for partially refunded orders
                
                if ( $quantity == 0 && get_option( 'wpcl_order_partial_refunds', 'no' ) == 'yes' ) {
                    // Order has been partially refunded
                } else {
                    ?>
								<tr data-email="<?php 
                    echo  $order->get_billing_email() ;
                    ?>">
									<?php 
                    
                    if ( get_option( 'wpcl_order_number', 'yes' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  '<a href="' . admin_url( 'post.php' ) . '?post=' . $sale->order_id . '&action=edit" target="_blank">' . $sale->order_id . '</a>' ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_order_date', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  date_format( $order->get_date_created(), 'Y-m-d' ) ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_first_name', 'yes' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_billing_first_name() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_last_name', 'yes' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_billing_last_name() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_company', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_billing_company() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_email', 'yes' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  '<a href="mailto:' . $order->get_billing_email() . '">' . $order->get_billing_email() . '</a>' ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_phone', 'yes' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  '<a href="tel:' . $order->get_billing_phone() . '">' . $order->get_billing_phone() . '</a>' ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_address_1', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_billing_address_1() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_address_2', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_billing_address_2() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_city', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_billing_city() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_state', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_billing_state() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_postalcode', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_billing_postcode() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_billing_country', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_billing_country() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_shipping_first_name', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_first_name() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_shipping_last_name', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_last_name() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_shipping_company', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_company() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_shipping_address_1', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_address_1() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_shipping_address_2', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_address_2() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_shipping_city', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_city() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_shipping_state', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_state() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_shipping_postalcode', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_postcode() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_shipping_country', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_country() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_customer_message', 'yes' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_customer_note() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_customer_ID', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        if ( $order->get_customer_id() ) {
                            echo  '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $order->get_customer_id() . '" target="_blank">' . $order->get_customer_id() . '</a>' ;
                        }
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_customer_username', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        $customerid = $order->get_customer_id();
                        
                        if ( $customerid ) {
                            $user_info = get_userdata( $customerid );
                            echo  '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $order->get_customer_id() . '" target="_blank">' . $user_info->user_login . '</a>' ;
                        }
                        
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_customer_display_name', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p><?php 
                        $customerid = $order->get_customer_id();
                        
                        if ( $customerid ) {
                            $user_info = get_userdata( $customerid );
                            echo  $user_info->display_name ;
                        }
                        
                        ?></p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_order_status', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        $status = wc_get_order_status_name( $order->get_status() );
                        echo  $status ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_order_payment', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_payment_method_title() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_order_shipping', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_shipping_method() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_order_coupon', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        $coupons = $order->get_used_coupons();
                        echo  implode( ', ', $coupons ) ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_variations', 'yes' ) == 'yes' ) {
                        $item = $order->get_item( $sale->order_item_id );
                        ?>
									<td>
										<p>
											<?php 
                        foreach ( $item->get_meta_data() as $itemvariation ) {
                            if ( !is_array( $itemvariation->value ) ) {
                                echo  '<strong>' . wc_attribute_label( $itemvariation->key ) . '</strong>: &nbsp;' . wc_attribute_label( $itemvariation->value ) . '<br />' ;
                            }
                        }
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_order_total', 'no' ) == 'yes' ) {
                        ?>
									<td>
										<p>
											<?php 
                        echo  $order->get_formatted_order_total() ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>
									<?php 
                    
                    if ( get_option( 'wpcl_order_qty', 'yes' ) == 'yes' ) {
                        $productcount[] = $quantity;
                        ?>
									<td>
										<p>
											<?php 
                        echo  $quantity ;
                        ?>
										</p>
									</td>
									<?php 
                    }
                    
                    ?>

									<?php 
                    ?>
									<?php 
                    // Add wpcl_admin_add_row
                    do_action(
                        'wpcl_admin_add_row',
                        $order,
                        $product,
                        $sale
                    );
                    ?>
								</tr>
								<?php 
                    if ( $order->get_billing_email() ) {
                        $email_list[] = $order->get_billing_email();
                    }
                }
                
                // End partial refund check
            }
            // End foreach
            $email_list = implode( ',', array_unique( $email_list ) );
            ?>
					</tbody>
				</table>
				<?php 
            
            if ( get_option( 'wpcl_order_qty' ) == 'yes' ) {
                ?>
					<p class="total">
						<?php 
                echo  '<strong>' . __( 'Total', 'wc-product-customer-list' ) . ' : </strong>' . array_sum( $productcount ) ;
                ?>
					</p>
				<?php 
            }
            
            ?>
					<a href="mailto:?bcc=<?php 
            echo  $email_list ;
            ?>" class="button"><?php 
            _e( 'Email all customers', 'wc-product-customer-list' );
            ?></a>
					<a href="#" class="button" id="email-selected" disabled><?php 
            _e( 'Email selected customers', 'wc-product-customer-list' );
            ?></a>
					<?php 
            do_action( 'wpcl_after_email_button', $email_list );
            ?>
						<?php 
        } else {
            _e( 'This product currently has no customers', 'wc-product-customer-list' );
        }
        
        ?>
			</div>
				<?php 
    }

}