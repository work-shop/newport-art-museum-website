<?php
/**
 * Plugin Name: WooCommerce Custom Post Type Manager
 * Plugin URI: http://reigelgallarde.me/product/woocommerce-woorei-cpt
 * Description: An extension to WooCommerce enabling site owners to add or use Custom Post Types as products
 * Author: Reigel Gallarde
 * Author URI: http://reigelgallarde.me
 * Version: 3.0.1
 * WooCommerce: 3.2.5
 *
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     
 * @author      Reigel Gallarde
 * @category    Plugin
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
 
 if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if (!function_exists('is_plugin_active'))
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 

if ( ! class_exists('WooCommerceCustomPostTypeManager') ) :

class WooCommerceCustomPostTypeManager {
	
	
	protected static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function __construct(){
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && !is_plugin_active( 'woocommerce-woorei-shop-manager/woorei.php' ) ) { 
			add_filter( 'woocommerce_data_stores', array( $this, 'woocommerce_data_stores' ) );
			add_action( 'after_setup_theme', array( $this, 'init' ) );
			//$this->init();
		}
	}
	
	public function init() {
		
		// check for required plugins...
		require_once $this->plugin_path() . '/includes/required/required.php';
		
		require_once $this->plugin_path() . '/includes/data-stores/class-wccpt-product-data-store-cpt.php';
		
		require_once $this->plugin_path() . '/includes/admin/class-wccpt-admin-post-types.php';
		
		// Custom Post Type helper functions
		require_once $this->plugin_path() . '/includes/admin/custom-post-types-helper.php';
		
		if ( is_admin() ){
			// add settings page: WooCommerce > Products > Custom Post Types
			require_once $this->plugin_path() . '/includes/admin/settings/custom-post-type-settings-page.php';
			
			// add product meta box to enabled Custom Post Types add/edit screens.
			require_once $this->plugin_path() . '/includes/admin/custom-post-type-admin-assets.php';
			
		} else {
			// Template Functions.
			 require_once $this->plugin_path() . '/includes/wccpt-template-functions.php';
			// Template Loader.
			 require_once $this->plugin_path() . '/includes/class-wccpt-template-loader.php';
			// Frontend Scripts
			 require_once $this->plugin_path() . '/includes/class-wccpt-frontend-scripts.php';
		}
		
	}
	
	public function woocommerce_data_stores ( $stores ) {		
		$stores['product'] 				= 'WCCPT_Product_Data_Store_CPT';		
		$stores['product-grouped']     	= 'WCCPT_Product_Grouped_Data_Store_CPT';
		$stores['product-variable']   	= 'WCCPT_Product_Variable_Data_Store_CPT';
		$stores['product-variation']   	= 'WCCPT_Product_Variation_Data_Store_CPT';
		return $stores;
	}
	
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	
	
}

endif;


function WCCPTM() {
	return WooCommerceCustomPostTypeManager::instance();
}

WCCPTM();

