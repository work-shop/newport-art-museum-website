<?php

/**
 * @package WC_Product_Customer_List
 * @version 2.7.5
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly
}

// Create Shortcode customer_list
// Use the shortcode: [customer_list product="1111" table_title="" hide_titles="false" order_status="wc-completed" order_number="false" order_date="false" billing_first_name="true" billing_last_name="true" billing_company="false" billing_email="false" billing_phone="false" billing_address_1="false" billing_address_2="false" billing_city="false" billing_state="false" billing_postalcode="false" billing_country="false" shipping_first_name="false" shipping_last_name="false" shipping_company="false" shipping_address_1="false" shipping_address_2="false" shipping_city="false" shipping_state="false" shipping_postalcode="false" shipping_country="false" customer_message="false" customer_id="false" customer_username="false" customer_username_link="true" customer_display_name="false" order_status_column="false" order_payment="false" order_shipping="false" order_coupon="false" order_variations="true" order_total="false" order_qty="false" order_qty_total="false" order_qty_total_column="false" limit="9999"]
function wpcl_shortcode( $atts )
{
    ob_start();
    // Register / Enqueue style
    wp_register_style(
        'wpcl-shortcode-css',
        plugin_dir_url( __FILE__ ) . '../admin/assets/shortcode.css',
        false,
        '2.6.6'
    );
    wp_enqueue_style( 'wpcl-shortcode-css' );
    // Default attribute pairs
    $pairs = array(
        'product'                => get_the_id(),
        'table_title'            => NULL,
        'order_status'           => 'wc-completed',
        'show_titles'            => 'false',
        'order_number'           => 'false',
        'order_date'             => 'false',
        'billing_first_name'     => 'true',
        'billing_last_name'      => 'true',
        'billing_company'        => 'false',
        'billing_email'          => 'false',
        'billing_phone'          => 'false',
        'billing_address_1'      => 'false',
        'billing_address_2'      => 'false',
        'billing_city'           => 'false',
        'billing_state'          => 'false',
        'billing_postalcode'     => 'false',
        'billing_country'        => 'false',
        'shipping_first_name'    => 'false',
        'shipping_last_name'     => 'false',
        'shipping_company'       => 'false',
        'shipping_address_1'     => 'false',
        'shipping_address_2'     => 'false',
        'shipping_city'          => 'false',
        'shipping_state'         => 'false',
        'shipping_postalcode'    => 'false',
        'shipping_country'       => 'false',
        'customer_message'       => 'false',
        'customer_id'            => 'false',
        'customer_username'      => 'false',
        'customer_username_link' => 'true',
        'customer_display_name'  => 'false',
        'order_status_column'    => 'false',
        'order_payment'          => 'false',
        'order_shipping'         => 'false',
        'order_coupon'           => 'false',
        'order_variations'       => 'true',
        'order_total'            => 'false',
        'order_qty'              => 'false',
        'order_qty_total'        => 'false',
        'order_qty_total_column' => 'false',
        'limit'                  => 9999,
        'custom_fields'          => NULL,
        'sortable'               => 'false',
        'export_pdf'             => 'false',
        'export_csv'             => 'false',
        'email_all'              => 'false',
        'copy'                   => 'false',
        'print'                  => 'false',
        'search'                 => 'false',
        'paging'                 => 'false',
        'info'                   => 'false',
        'scrollx'                => 'false',
        'pdf_pagesize'           => 'LETTER',
        'pdf_orientation'        => 'portrait',
    );
    $atts = shortcode_atts( $pairs, $atts, 'customer_list' );
    // Attributes in var
    $post_id = $atts['product'];
    $table_title = $atts['table_title'];
    $order_status = $atts['order_status'];
    $show_titles = $atts['show_titles'];
    $order_number = $atts['order_number'];
    $order_date = $atts['order_date'];
    $billing_first_name = $atts['billing_first_name'];
    $billing_last_name = $atts['billing_last_name'];
    $billing_company = $atts['billing_company'];
    $billing_email = $atts['billing_email'];
    $billing_phone = $atts['billing_phone'];
    $billing_address_1 = $atts['billing_address_1'];
    $billing_address_2 = $atts['billing_address_2'];
    $billing_city = $atts['billing_city'];
    $billing_state = $atts['billing_state'];
    $billing_postalcode = $atts['billing_postalcode'];
    $billing_country = $atts['billing_country'];
    $shipping_first_name = $atts['shipping_first_name'];
    $shipping_last_name = $atts['shipping_last_name'];
    $shipping_company = $atts['shipping_company'];
    $shipping_address_1 = $atts['shipping_address_1'];
    $shipping_address_2 = $atts['shipping_address_2'];
    $shipping_city = $atts['shipping_city'];
    $shipping_state = $atts['shipping_state'];
    $shipping_postalcode = $atts['shipping_postalcode'];
    $shipping_country = $atts['shipping_country'];
    $customer_message = $atts['customer_message'];
    $customer_id = $atts['customer_id'];
    $customer_username = $atts['customer_username'];
    $customer_username_link = $atts['customer_username_link'];
    $customer_display_name = $atts['customer_display_name'];
    $order_status_column = $atts['order_status_column'];
    $order_payment = $atts['order_payment'];
    $order_shipping = $atts['order_shipping'];
    $order_coupon = $atts['order_coupon'];
    $order_variations = $atts['order_variations'];
    $order_total = $atts['order_total'];
    $order_qty = $atts['order_qty'];
    $order_qty_total = $atts['order_qty_total'];
    $order_qty_total_column = $atts['order_qty_total_column'];
    $limit = $atts['limit'];
    global  $sitepress, $post, $wpdb ;
    // Check for translated products if WPML is activated
    
    if ( isset( $sitepress ) ) {
        $trid = $sitepress->get_element_trid( $post_id, 'post_product' );
        $translations = $sitepress->get_element_translations( $trid, 'product' );
        $post_id = array();
        foreach ( $translations as $lang => $translation ) {
            $post_id[] = $translation->element_id;
        }
    }
    
    // Query the orders related to the product
    $pieces = explode( ",", $order_status );
    $order_statuses = array_map( 'esc_sql', (array) $pieces );
    $order_statuses_string = "'" . implode( "', '", $order_statuses ) . "'";
    $post_id_arr = array_map( 'esc_sql', (array) $post_id );
    $post_string = "'" . implode( "', '", $post_id_arr ) . "'";
    // Get post type
    
    if ( isset( $sitepress ) ) {
        $post_type = get_post_type( $post_id[0] );
    } else {
        $post_type = get_post_type( $post_id );
    }
    
    // Check if ID is for a product
    if ( $post_type == 'product' ) {
        $item_sales = $wpdb->get_results( $wpdb->prepare( "SELECT o.ID as order_id, oi.order_item_id FROM\n\t\t\t{$wpdb->prefix}woocommerce_order_itemmeta oim\n\t\t\tINNER JOIN {$wpdb->prefix}woocommerce_order_items oi\n\t\t\tON oim.order_item_id = oi.order_item_id\n\t\t\tINNER JOIN {$wpdb->posts} o\n\t\t\tON oi.order_id = o.ID\n\t\t\tWHERE oim.meta_key = %s\n\t\t\tAND oim.meta_value IN ( {$post_string} )\n\t\t\tAND o.post_status IN ( {$order_statuses_string} )\n\t\t\tAND o.post_type NOT IN ('shop_order_refund')\n\t\t\tORDER BY o.ID DESC\n\t\t\tLIMIT {$limit}", '_product_id' ) );
    }
    // Get selected columns from the options page
    $product = WC()->product_factory->get_product( $post_id );
    $columns = array();
    if ( $order_number == 'true' ) {
        $columns[] = __( 'Order', 'wc-product-customer-list' );
    }
    if ( $order_date == 'true' ) {
        $columns[] = __( 'Date', 'wc-product-customer-list' );
    }
    if ( $billing_first_name == 'true' ) {
        $columns[] = __( 'Billing First name', 'wc-product-customer-list' );
    }
    if ( $billing_last_name == 'true' ) {
        $columns[] = __( 'Billing Last name', 'wc-product-customer-list' );
    }
    if ( $billing_company == 'true' ) {
        $columns[] = __( 'Billing Company', 'wc-product-customer-list' );
    }
    if ( $billing_email == 'true' ) {
        $columns[] = __( 'Billing E-mail', 'wc-product-customer-list' );
    }
    if ( $billing_phone == 'true' ) {
        $columns[] = __( 'Billing Phone', 'wc-product-customer-list' );
    }
    if ( $billing_address_1 == 'true' ) {
        $columns[] = __( 'Billing Address 1', 'wc-product-customer-list' );
    }
    if ( $billing_address_2 == 'true' ) {
        $columns[] = __( 'Billing Address 2', 'wc-product-customer-list' );
    }
    if ( $billing_city == 'true' ) {
        $columns[] = __( 'Billing City', 'wc-product-customer-list' );
    }
    if ( $billing_state == 'true' ) {
        $columns[] = __( 'Billing State', 'wc-product-customer-list' );
    }
    if ( $billing_postalcode == 'true' ) {
        $columns[] = __( 'Billing Postal Code / Zip', 'wc-product-customer-list' );
    }
    if ( $billing_country == 'true' ) {
        $columns[] = __( 'Billing Country', 'wc-product-customer-list' );
    }
    if ( $shipping_first_name == 'true' ) {
        $columns[] = __( 'Shipping First name', 'wc-product-customer-list' );
    }
    if ( $shipping_last_name == 'true' ) {
        $columns[] = __( 'Shipping Last name', 'wc-product-customer-list' );
    }
    if ( $shipping_company == 'true' ) {
        $columns[] = __( 'Shipping Company', 'wc-product-customer-list' );
    }
    if ( $shipping_address_1 == 'true' ) {
        $columns[] = __( 'Shipping Address 1', 'wc-product-customer-list' );
    }
    if ( $shipping_address_2 == 'true' ) {
        $columns[] = __( 'Shipping Address 2', 'wc-product-customer-list' );
    }
    if ( $shipping_city == 'true' ) {
        $columns[] = __( 'Shipping City', 'wc-product-customer-list' );
    }
    if ( $shipping_state == 'true' ) {
        $columns[] = __( 'Shipping State', 'wc-product-customer-list' );
    }
    if ( $shipping_postalcode == 'true' ) {
        $columns[] = __( 'Shipping Postal Code / Zip', 'wc-product-customer-list' );
    }
    if ( $shipping_country == 'true' ) {
        $columns[] = __( 'Shipping Country', 'wc-product-customer-list' );
    }
    if ( $customer_message == 'true' ) {
        $columns[] = __( 'Customer Message', 'wc-product-customer-list' );
    }
    if ( $customer_id == 'true' ) {
        $columns[] = __( 'Customer ID', 'wc-product-customer-list' );
    }
    if ( $customer_username == 'true' ) {
        $columns[] = __( 'Customer username', 'wc-product-customer-list' );
    }
    if ( $customer_display_name == 'true' ) {
        $columns[] = __( 'Display Name', 'wc-product-customer-list' );
    }
    if ( $order_status_column == 'true' ) {
        $columns[] = __( 'Order Status', 'wc-product-customer-list' );
    }
    if ( $order_payment == 'true' ) {
        $columns[] = __( 'Payment method', 'wc-product-customer-list' );
    }
    if ( $order_shipping == 'true' ) {
        $columns[] = __( 'Shipping method', 'wc-product-customer-list' );
    }
    if ( $order_coupon == 'true' ) {
        $columns[] = __( 'Coupons used', 'wc-product-customer-list' );
    }
    if ( $order_variations == 'true' ) {
        $columns[] = __( 'Variation', 'wc-product-customer-list' );
    }
    if ( $order_total == 'true' ) {
        $columns[] = __( 'Order total', 'wc-product-customer-list' );
    }
    if ( $order_qty_total_column == 'true' ) {
        $columns[] = __( 'Total qty', 'wc-product-customer-list' );
    }
    if ( $order_qty == 'true' ) {
        $columns[] = __( 'Qty', 'wc-product-customer-list' );
    }
    // Action before table
    do_action(
        'wpcl_shortcode_before_table',
        $post_id,
        $columns,
        $atts
    );
    ?>

	<?php 
    
    if ( $table_title ) {
        ?>
		<h3><?php 
        echo  $table_title ;
        ?></h3>
	<?php 
    }
    
    ?>

	<?php 
    
    if ( $item_sales ) {
        $email_list = array();
        $productcount = array();
        ?>
		<div class="wpcl">
			<table id="list-table" style="width:100%" class="wpcl-shortcode">
				<thead <?php 
        if ( $show_titles == 'false' ) {
            echo  'style="display: none"' ;
        }
        ?>>
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
        // Add wpcl_shortcode_add_column_head action
        do_action( 'wpcl_shortcode_add_column_head', $columns, $atts );
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
							<tr>
								<?php 
                
                if ( $order_number == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  '<a href="' . admin_url( 'post.php' ) . '?post=' . $sale->order_id . '&action=edit" target="_blank">' . $sale->order_id . '</a>' ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $order_date == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  date_format( $order->get_date_created(), 'Y-m-d' ) ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_first_name == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_billing_first_name() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_last_name == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_billing_last_name() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_company == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_billing_company() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_email == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  '<a href="mailto:' . $order->get_billing_email() . '">' . $order->get_billing_email() . '</a>' ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_phone == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  '<a href="tel:' . $order->get_billing_phone() . '">' . $order->get_billing_phone() . '</a>' ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_address_1 == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_billing_address_1() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_address_2 == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_billing_address_2() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_city == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_billing_city() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_state == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_billing_state() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_postalcode == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_billing_postcode() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $billing_country == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_billing_country() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $shipping_first_name == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_first_name() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $shipping_last_name == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_last_name() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $shipping_company == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_company() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $shipping_address_1 == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_address_1() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $shipping_address_2 == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_address_2() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $shipping_city == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_city() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $shipping_state == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_state() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $shipping_postalcode == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_postcode() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $shipping_country == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_country() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $customer_message == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_customer_note() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $customer_id == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    if ( $order->get_customer_id() ) {
                        echo  '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $order->get_customer_id() . '" target="_blank">' . $order->get_customer_id() . '</a>' ;
                    }
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $customer_username == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    $customerid = $order->get_customer_id();
                    
                    if ( $customerid ) {
                        $user_info = get_userdata( $customerid );
                        
                        if ( $customer_username_link == 'true' ) {
                            echo  '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $order->get_customer_id() . '" target="_blank">' . $user_info->user_login . '</a>' ;
                        } else {
                            echo  $user_info->user_login ;
                        }
                    
                    }
                    
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $customer_display_name == 'true' ) {
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
                
                if ( $order_status_column == 'true' ) {
                    ?>
								<td>
									<p>
									<?php 
                    $status = wc_get_order_status_name( $order->get_status() );
                    echo  $status ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $order_payment == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_payment_method_title() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $order_shipping == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_shipping_method() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $order_coupon == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    $coupons = $order->get_used_coupons();
                    echo  implode( ', ', $coupons ) ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>

								<?php 
                
                if ( $order_variations == 'true' ) {
                    $item = $order->get_item( $sale->order_item_id );
                    ?>
								<td>
									<p>
										<?php 
                    foreach ( $item->get_meta_data() as $itemvariation ) {
                        echo  '<strong>' . wc_attribute_label( $itemvariation->key ) . '</strong>: &nbsp;' . wc_attribute_label( $itemvariation->value ) . '<br />' ;
                    }
                    ?>
									</p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $order_total == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  $order->get_formatted_order_total() ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $order_qty == 'true' ) {
                    $productcount[] = $quantity;
                    ?>
								<td>
									<p><?php 
                    echo  $quantity ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                
                if ( $order_qty_total_column == 'true' ) {
                    ?>
								<td>
									<p><?php 
                    echo  get_post_meta( $post_id, 'total_sales', true ) ;
                    ?></p>
								</td>
								<?php 
                }
                
                ?>
								<?php 
                ?>
								<?php 
                // Add wpcl_shortcode_add_row
                do_action(
                    'wpcl_shortcode_add_row',
                    $order,
                    $product,
                    $sale,
                    $atts
                );
                ?>
							</tr>

						<?php 
                if ( $order->get_billing_email() ) {
                    $email_list[] = $order->get_billing_email();
                }
            }
            
            // End partial refund check
        }
        // End foreach
        ?>
				</tbody>
			</table>
		
		<?php 
        
        if ( $order_qty_total == 'true' ) {
            ?>
		<p class="total">
			<?php 
            echo  '<strong>' . __( 'Total', 'wc-product-customer-list' ) . ' : </strong>' . array_sum( $productcount ) ;
            ?>
		</p>
		<?php 
        }
        
        ?>

		<?php 
        
        if ( $email_all == 'true' ) {
            ?>
			<a href="mailto:?bcc=<?php 
            echo  $email_list ;
            ?>" class="button"><?php 
            _e( 'Email all customers', 'wc-product-customer-list' );
            ?></a>
		<?php 
        }
        
        ?>

		<?php 
        do_action(
            'wpcl_shortcode_after_table',
            $post_id,
            $email_list,
            $atts
        );
        ?>

	</div>

	<?php 
    } else {
        _e( 'This product currently has no customers', 'wc-product-customer-list' );
    }
    
    $out = ob_get_clean();
    $shortcode = 'customer_list';
    apply_filters(
        'shortcode_atts_{$shortcode}',
        $out,
        $pairs,
        $atts,
        $shortcode
    );
    return $out;
}

add_shortcode( 'customer_list', 'wpcl_shortcode' );