<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_CPT' ) ) :

/**
 * WC_CPT.
 */
class WC_CPT_List {
	
	public function __construct() {
		add_filter( 'register_post_type_args', array( $this, 'register_post_type_args' ), 10, 2 );
		add_action( 'init', array( $this, 'maybe_need_flush_rewrite_rules' ));
	}
	
	public function maybe_need_flush_rewrite_rules() {
		if ( get_option( 'wccpt_need_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			update_option( 'wccpt_need_flush_rewrite_rules', false );
		}
	}
	
	public function register_post_type_args( $args, $post_type ){
		$cpt = self::get( $post_type );
		
		if (! self::is_active( $post_type) ) { return $args; }
		$must_have = array(
			'public'              => true,
			'show_ui'             => true,
			'capability_type'     => 'product',
			'map_meta_cap'        => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
			'query_var'           => true,
			'has_archive'         => ( $shop_page_id = wc_get_page_id( $post_type ) ) && get_post( $shop_page_id ) ? get_page_uri( $shop_page_id ) : $post_type,
			'show_in_nav_menus'   => true
		);
		$args = wp_parse_args( $must_have, $args );
		return $args;
	}
		
	public static function get( $cpt = null ){
		$cpt_list = array();
		
		$post_types = get_post_types(array( 'public'   => true, '_builtin' => false, 'show_ui' => true),'objects');
		if ( $post_types ) {
			foreach ( $post_types as $key => $post_type ) {
				if ( $key === 'product' ) { continue; }
				$cpt_list[ $key ] = array(
					'slug' => $post_type->rewrite['slug'],
					'menu_name' => $post_type->labels->menu_name,
					'name' => $post_type->name,
					'active' => self::is_active( $key ),
					'registered' => true,
				);
			}
		}
		
		asort($cpt_list);
		
		return ( $cpt === null ) ? $cpt_list : ( isset( $cpt_list[ $cpt ] ) ? $cpt : false );
	}
	
	
	public static function is_active( $cpt ){
		return apply_filters( 'wccpt_is_active', get_option( $cpt . '_woorei_woocommerce_active', 'no' ) === 'yes', $cpt );
	}
		
	public static function use_woocommerce_template_loader( $cpt ){
		return ( get_option( $cpt . '_woorei_woocommerce_template_loader', 'yes' ) === 'yes' );
	}
}
new WC_CPT_List();
endif;