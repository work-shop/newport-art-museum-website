<?php
/*
 * Plugin Name: WooCommerce Email Test
 * Plugin URI: 
 * Description: Let's you test WooCommerce emails (Premium Version)
 * Version:  2.1
 * Author: RaiserWeb
 * Author URI: http://www.raiserweb.com
 * Developer: RaiserWeb
 * Developer URI: http://www.raiserweb.com
 * Text Domain: raiserweb
 * License: Private
 *
 *
 
 */
 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

		// plugin updater
        $wetp_main_lisence_key = get_option('wetp_main_lisence_key', "false");
        if( $wetp_main_lisence_key ){
            require 'plugin-update-checker/plugin-update-checker.php';
            // check for updates
            $MyUpdateChecker = PucFactory::buildUpdateChecker(
                'http://raiserweb.com/wp-update-server-master/?action=get_metadata&slug=woocommerce-email-test&license_key='.$wetp_main_lisence_key,
                __FILE__,
                'woocommerce-email-test'
            );
        }  
	 	
		// set email classes for test buttons
		$wetp_test_email_class = array(
			'WC_Email_New_Order'=>'New Order',
			'WC_Email_Customer_Processing_Order'=>'Processing Order',
			'WC_Email_Customer_Completed_Order'=>'Completed Order',
			'WC_Email_Customer_Invoice'=>'Customer Invoice',
			'WC_Email_Customer_Note'=>'Customer Note',
		);
        
        $wetp_test_email_class_additional = array(
			'WC_Email_Cancelled_Order'=>'Cancelled Order',
			'WC_Email_Failed_Order'=>'Failed Order',
			'WC_Email_Customer_On_Hold_Order'=>'Order On Hold',
			'WC_Email_Customer_Refunded_Order'=>'Refunded Order',
			'WC_Email_Customer_Reset_Password'=>'Reset Password',          
            'WC_Email_Customer_New_Account'=>'New Account',   
        );
        
        
        // SUBSCRIPTIONS
        $wetp_test_email_class_subscriptions = array(
			'WCS_Email_New_Renewal_Order'=>'New Renewal Order',
            'WCS_Email_Processing_Renewal_Order'=>'Processing Renewal Order',
			'WCS_Email_Completed_Renewal_Order'=>'Completed Renewal Order',
			'WCS_Email_Completed_Switch_Order'=>'Completed Switch Order',
			'WCS_Email_Customer_Renewal_Invoice'=>'Renewal Invoice',         
        );
        $wetp_test_email_class_subscriptions_filters = array(
			'WCS_Email_New_Renewal_Order'=>'new_renewal_order',
            'WCS_Email_Processing_Renewal_Order'=>'customer_processing_renewal_order',
			'WCS_Email_Completed_Renewal_Order'=>'customer_completed_renewal_order',
			'WCS_Email_Completed_Switch_Order'=>'customer_completed_switch_order',
			'WCS_Email_Customer_Renewal_Invoice'=>'customer_renewal_invoice',  
        );
        

        
		// include plugin files
		include( 'functions.php' );
		include( 'email-trigger.php' );
        
		if( is_admin() ) { 
		 
			// register admin page and add menu
			add_action('admin_menu', 'wept_register_test_email_submenu_page');

			function wept_register_test_email_submenu_page() {
				add_submenu_page( 'woocommerce', 'Email Test', 'Email Test', 'manage_options', 'woocommerce-email-test', 'wept_register_test_email_submenu_page_callback' ); 
			}

			function wept_register_test_email_submenu_page_callback() {
				include( 'admin-menu.php' );
			}
			
		}

	
}