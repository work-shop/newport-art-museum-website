<?php 

/**
 * When the_post is called, put product data into a global.
 *
 * @param mixed $post
 * @return WC_Product
 */
function wccpt_setup_product_data( $post ) {
	
	remove_filter( current_filter(), __FUNCTION__ ); 
	if ( is_int( $post ) )
		$post = get_post( $post );
	
	$cpt = WC_CPT_List::get( $post->post_type );
	if ( empty( $cpt ) || ! WC_CPT_List::is_active( $cpt ) ) return;
	
	unset( $GLOBALS['product'] );
	//if ( ! is_archive( $cpt ) ) {
		//$post->cpt_post_type = $post->post_type;
		//$post->post_type = 'product';
	//}
	$GLOBALS['product'] = wc_get_product( $post );
}
add_action( 'the_post', 'wccpt_setup_product_data', 999 );

function wccpt_body_class( $classes ) {
	global $post;
	$classes = (array) $classes;
	
	if (!empty($post)) {
		$cpt = WC_CPT_List::get( $post->post_type );
		if ( empty( $cpt ) || ! WC_CPT_List::is_active( $cpt ) || ( $post->post_type === 'product' ) ) return $classes;
		
		$classes[] = 'single-product';
	}
	return $classes;
}
add_filter( 'body_class', 'wccpt_body_class' );
function wccpt_product_post_class( $classes, $class = '', $post_id = '' ) {
	$post = get_post( $post_id );
	
	$cpt = WC_CPT_List::get( $post->post_type );
	if ( empty( $cpt ) || ! WC_CPT_List::is_active( $cpt ) || ( $post->post_type === 'product' ) ) return $classes;

	$product = wc_get_product( $post_id );

	if ( $product ) {
		$classes[] = 'product';
		$classes[] = wc_get_loop_class();
		$classes[] = $product->get_stock_status();

		if ( $product->is_on_sale() ) {
			$classes[] = 'sale';
		}
		if ( $product->is_featured() ) {
			$classes[] = 'featured';
		}
		if ( $product->is_downloadable() ) {
			$classes[] = 'downloadable';
		}
		if ( $product->is_virtual() ) {
			$classes[] = 'virtual';
		}
		if ( $product->is_sold_individually() ) {
			$classes[] = 'sold-individually';
		}
		if ( $product->is_taxable() ) {
			$classes[] = 'taxable';
		}
		if ( $product->is_shipping_taxable() ) {
			$classes[] = 'shipping-taxable';
		}
		if ( $product->is_purchasable() ) {
			$classes[] = 'purchasable';
		}
		if ( $product->get_type() ) {
			$classes[] = "product-type-" . $product->get_type();
		}
		if ( $product->is_type( 'variable' ) ) {
			if ( ! $product->get_default_attributes() ) {
				$classes[] = 'has-default-attributes';
			}
			if ( $product->has_child() ) {
				$classes[] = 'has-children';
			}
		}
	}

	if ( false !== ( $key = array_search( 'hentry', $classes ) ) ) {
		unset( $classes[ $key ] );
	}

	return $classes;
}
add_filter( 'post_class', 'wccpt_product_post_class', 20, 3 );


/**
 * wccpt_is_woocommerce - Returns true if on a page which uses WooCommerce templates (cart and checkout are standard pages with shortcodes and thus are not included).
 * @return bool
 */
function wccpt_is_woocommerce( $is_woocommerce ) {
	global $post;
	
	$cpt_active = false;
	
	if (!empty($post)) {
		$cpt = WC_CPT_List::get( $post->post_type );
		$cpt_active = ( !empty( $cpt ) && WC_CPT_List::is_active( $cpt ) );
	}
	
	return ( $is_woocommerce || $cpt_active ) ? true : false;
}
add_filter( 'is_woocommerce', 'wccpt_is_woocommerce' );

function woocommerce_cpt_object( $the_product ) {
	
	$cpt = WC_CPT_List::get( $the_product->post_type );
	if ( get_option( $cpt . '_woorei_woocommerce_template_loader' ) !== 'yes' ) { return $the_product; }
	
	if ( ! empty( $cpt ) && WC_CPT_List::is_active( $cpt ) && ! is_archive( $cpt ) ) {
		$the_product->cpt_post_type = $the_product->post_type;
		$the_product->post_type = 'product';
	}
	return $the_product;
}
add_filter( 'woocommerce_product_object', 'woocommerce_cpt_object' );

/**
 * Output generator tag to aid debugging.
 *
 * @access public
 */
function wccpt_generator_tag( $gen, $type ) {
	switch ( $type ) {
		case 'html':
			$gen .= "\n" . '<meta name="generator" content="reigelgallarde.me">';
			break;
		case 'xhtml':
			$gen .= "\n" . '<meta name="generator" content="reigelgallarde.me" />';
			break;
	}
	return $gen;
}
add_action( 'get_the_generator_html', 'wccpt_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'wccpt_generator_tag', 10, 2 );

function wccpt_woocommerce_template_loop_add_to_cart() {
	global $product, $post;
	$the_product = $post;
	$the_product->post_type = 'product';
	$product = wc_get_product( $the_product );
}
add_action( 'woocommerce_after_shop_loop_item', 'wccpt_woocommerce_template_loop_add_to_cart', 1 );


function wccpt_woocommerce_result_count() {
	global $wp_query;
	$wp_query->set('post_type','product');
}
add_action( 'woocommerce_before_shop_loop', 'wccpt_woocommerce_result_count', 1 );

// fixed for wc_get_page_id( 'shop' ) used on archive page of cpt
function woocommerce_get_cpt_page_id($page) {
	global $wp_query;
	$cpt = isset($wp_query->query_vars['post_type'])?$wp_query->query_vars['post_type']:false;
	if ( $cpt && WC_CPT_List::is_active( $cpt ) && is_post_type_archive( $cpt ) ) {
		$page = get_option('woocommerce_' . $cpt . '_page_id' );
	}
	return $page ? absint( $page ) : -1;
}
add_filter( 'woocommerce_get_shop_page_id', 'woocommerce_get_cpt_page_id' );
