<?php
/**
* Plugin Name: Advanced Reporting For Woocommerces Pro
* Plugin URI: www.phoeniixx.com
* Version:2.6
* Author: phoeniixx
* Text Domain: advanced-reporting-for-woocommerce
* Author URI: http://www.phoeniixx.com/
* Domain Path: /languages/
* Description: What does your plugin do and what features does it offer...
* WC requires at least: 2.6.0
* WC tested up to: 3.2.1
*/

	add_action('admin_init', 'phoen_advance_reporting_admin_init');

	
	function phoen_advance_reporting_admin_init()
	{
		
		wp_enqueue_style( 'style-advanced-reportings', plugin_dir_url(__FILE__).'assets/css/phoen-arfw-bootstrap-iso.css' );
		
		wp_enqueue_style( 'phoeniixx-advance-datepick', plugin_dir_url(__FILE__).'assets/css/jquery-ui-datepicker.css' );
		
		wp_enqueue_script( 'phoen-advance-scripts', plugin_dir_url(__FILE__)."assets/js/bootstrap.min.js" , array( 'jquery' ),true );
		
		wp_enqueue_script( 'phoen-advance-search', plugin_dir_url(__FILE__)."assets/js/phoen_report_search.js" , array( 'jquery' ),true );
		
		wp_enqueue_script( 'phoen-advance-chart-loder', plugin_dir_url(__FILE__)."assets/js/phoen_chart_loder.js" , array( 'jquery' ),true );
		
		wp_enqueue_script( 'phoen-advance-google-char', plugin_dir_url(__FILE__)."assets/js/phoen_google_char.js" , array( 'jquery' ),true );
		
		wp_enqueue_script( 'phoen-advance-datetimepickers', plugin_dir_url(__FILE__)."assets/js/jquery-ui-datepicker.js" , array( 'jquery' ),true );
	
	} 
	
	function pmsc_repoting_activate() {

		$phoen_reporting_enable_settings = get_option('phoen_reportings_enable');
		
		if($phoen_reporting_enable_settings == ''){
			
			update_option('phoen_reportings_enable',1);
			
		}
	}
		
	register_activation_hook( __FILE__, 'pmsc_repoting_activate' );
	
	add_action('admin_menu', 'phoe_advance_reporting_menu');
	
	function phoe_advance_reporting_menu() {
	   
		add_menu_page('advanced_reporting_for_woocommerce', __( 'Reporting', 'advanced-reporting-for-woocommerce' ) ,'manage_options','advanced_reporting_for_woocommerce','phoe_advance_reporting', plugin_dir_url( __FILE__ ).'assets/images/aaa2.png');
	  
		add_submenu_page( 'advanced_reporting_for_woocommerce', 'phoen_product',  __( 'Product', 'advanced-reporting-for-woocommerce' ),'manage_options', 'phoen_products', 'phoen_reportin_products');
	  
		add_submenu_page( 'advanced_reporting_for_woocommerce', 'phoen_settings',  __( 'Settings', 'advanced-reporting-for-woocommerce' ),'manage_options', 'phoen_setting', 'phoen_reportin_settings');
	
	}
		
		function phoe_advance_reporting()
		{
		
			$phoen_reporting_enable_settings = get_option('phoen_reportings_enable');
			
			if(isset($phoen_reporting_enable_settings) && $phoen_reporting_enable_settings == 1)
			{
			
				include_once(plugin_dir_path(__FILE__).'includes/phoe_reporting.php');
			}
			
		}
		
		function phoen_reportin_settings()
		{
			
			  _e( '<h3>General Settings</h3>', 'advanced-reporting-for-woocommerce' );
		
			include_once(plugin_dir_path(__FILE__).'includes/phoen_reporting_settings.php');
			
		}
		
		function phoen_reportin_products()
		{
			wp_enqueue_script( 'phoen-advance-pagination-scripts', plugin_dir_url(__FILE__)."assets/js/pagination.js", array( 'jquery' ),true );
			 ?>
			<style>
			
				.pagination.disabled a,  .pagination.disabled a:hover,  .pagination.disabled a:focus,  .pagination.disabled span {
					  color: #eee;
					  background: #fff;
					  cursor: default;
					}

					.pagination { display: inline-block; vertical-align: middle; }
					
					.pagination li {display: inline-block;}
	
					.pagination.disabled li.active a {
					  color: #fff;
					  background: #cccccc;
					  border-color: #cccccc;
					}
					
					select.page-range {margin-top: -5px;}


			</style>
			
			<div id="profile-page" class="wrap">
    
				<?php
					
				if(isset($_GET['tab']))
						
				{
					$tab = sanitize_text_field( $_GET['tab'] );
					
				}
				
				else
					
				{
					
					$tab="";
					
				}
				
				?>
				
				<?php $tab = (isset($_GET['tab']))?$_GET['tab']:'';?>
				
				<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
				
					<a class="nav-tab <?php if($tab == 'phoen_report_simple_instock_product' || $tab == ''){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=phoen_products&amp;tab=phoen_report_simple_instock_product"><?php _e('Simple Instock','advanced-reporting-for-woocommerce'); ?></a>
					
					<a class="nav-tab <?php if($tab == 'phoen_report_simple_outstock_product' ){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=phoen_products&amp;tab=phoen_report_simple_outstock_product"><?php _e('Simple Out Of Stock','advanced-reporting-for-woocommerce'); ?></a>
				
					<a class="nav-tab <?php if($tab == 'phoen_report_variable_product' ){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=phoen_products&amp;tab=phoen_report_variable_product"><?php _e('Variable Instock ','advanced-reporting-for-woocommerce'); ?></a>
					
					<a class="nav-tab <?php if($tab == 'phoen_report_variable_outstock'){ echo esc_html( "nav-tab-active" ); } ?>" href="?page=phoen_products&amp;tab=phoen_report_variable_outstock"><?php _e('Variable Out Of Stock','advanced-reporting-for-woocommerce'); ?></a>
				
				</h2>
				
			</div>
        
			<?php
			
			if($tab=='phoen_report_simple_instock_product' || $tab == '')
			{
				
			   include_once(plugin_dir_path(__FILE__).'includes/phoen_report_simple.php');
									 
			}
			if($tab=='phoen_report_simple_outstock_product' )
			{
				
			   include_once(plugin_dir_path(__FILE__).'includes/phoen_report_simple_outstock.php');
							 
			}
			
			if($tab=='phoen_report_variable_product' )
			{
				
			  include_once(plugin_dir_path(__FILE__).'includes/phoen_report_product.php');
										
			}
			
			if($tab=='phoen_report_variable_outstock' )
			{
				
			  include_once(plugin_dir_path(__FILE__).'includes/phoen_report_van_out_stock.php');
										
			}
		
		}		
?>