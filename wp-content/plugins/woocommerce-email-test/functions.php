<?php

function wetp_run_email_script(){
    
    global 
    $wetp_test_email_class, 
    $wetp_test_email_class_additional, 
    $wetp_test_email_class_subscriptions, 
    $wetp_test_email_class_subscriptions_filters;
    
	// the email type to send
	$email_class = get_query_var('woocommerce_email_test');	
    
    // check type is in array
    if( !array_key_exists( $email_class, $wetp_test_email_class) && !array_key_exists( $email_class, $wetp_test_email_class_additional)  && !array_key_exists( $email_class, $wetp_test_email_class_subscriptions) ){
        echo "Invalid email type";
        exit();
    }  
    
    
    
    
	// assign email address and order id variables
    
    if( substr( $email_class, 0, 3) == 'WCS' ){
        // subscription
        if( get_option( "wc_email_test_sub_id", false ) == 'recent' ){       
            $wc_email_test_order_id = '';           
        } else {     
            $wc_email_test_order_id = get_option( "wc_email_test_sub_id", false );           
        }	 
        
        if( ! $wc_email_test_order_id ) {
            echo "Please create a test subscription first to test the emails";
            return;           
        }

        $for_filter = $wetp_test_email_class_subscriptions_filters[$email_class];
        
    } else {
        // woo
        if( get_option( "wc_email_test_order_id", false ) == 'recent' ){       
            $wc_email_test_order_id = '';           
        } else {     
            $wc_email_test_order_id = get_option( "wc_email_test_order_id", false );           
        }	

        if( ! $wc_email_test_order_id ) {		

            // get a valid and most recent order_id ( if no order is has been selected )
            global $wpdb;
            $order_id_query = 'SELECT order_id FROM '.$wpdb->prefix.'woocommerce_order_items ORDER BY order_item_id DESC LIMIT 1';
            $order_id = $wpdb->get_results( $order_id_query );
            
            if( empty( $order_id ) ) {
            
                echo "No order within your WooCommerce shop. Please create a test order first to test the emails";
                return;
            
            } else {
            
                 $wc_email_test_order_id = $order_id[0]->order_id ;
                
            }
        
        }        

        $for_filter = strtolower( str_replace( 'WC_Email_', '' , $email_class ) );
        
    }
   


    
  
   
    
	// change email address within order to saved option	
	add_filter( 'woocommerce_email_recipient_'.$for_filter , 'wetp_email_recipient_filter_function', 10, 2);
	function wetp_email_recipient_filter_function($recipient, $object) {

        if( get_option( "wc_email_test_email", false ) ) {       
            $wc_email_test_email = get_option( "wc_email_test_email", false );           
        } else {    
            $wc_email_test_email = ""; 
        }              
		$recipient = $wc_email_test_email;
        
		return $recipient;
	}
    
	// change subject link	
    if( $for_filter == 'customer_completed_renewal_order' || $for_filter == 'customer_completed_switch_order'  ){
        $subject_filter_prefix = 'woocommerce_subscriptions_email_subject_'; 
        $subject_filter = $for_filter;
    } elseif( $for_filter == 'customer_renewal_invoice' ){
         $subject_filter_prefix = 'woocommerce_subscriptions_email_subject_new_renewal_order';
         $subject_filter = '';
    } else {
        $subject_filter_prefix = 'woocommerce_email_subject_';
        $subject_filter = $for_filter;
    }
    add_filter($subject_filter_prefix.$subject_filter , 'wept_change_admin_email_subject', 1, 2);	 
    function wept_change_admin_email_subject( $subject, $order ) {
        //global $woocommerce;       
        $subject = "TEST EMAIL: ".$subject;		
        return $subject;
    } 
    
	// email send toggle
	add_filter('woocommerce_email_enabled_'.$for_filter , 'wept_change_email_enabled', 1, 2);	 
	function wept_change_email_enabled( $enabled, $order ) {
        if( get_option( "wc_email_test_email", false ) ) {       
            return true;           
        } else {    
            return false;
        }
	}      
	
	if( isset( $GLOBALS['wc_advanced_notifications'] ) ) {
		unset( $GLOBALS['wc_advanced_notifications'] );
	}
	
	// load the email classs
	$wc_emails = new WC_Emails( );
	$emails = $wc_emails->get_emails();

	// select the email we want & send
	$new_email = $emails[ $email_class ];

	if( $for_filter == 'customer_note' ) {

		$new_email->trigger( array( 'order_id'=>$wc_email_test_order_id ) );

	} else {
		
		$new_email->trigger( $wc_email_test_order_id  );
		
	}

	// echo the email content for browser
	echo apply_filters( 'woocommerce_mail_content', $new_email->style_inline( $new_email->get_content() ) );

}

function wetp_get_order_id_select_field( $wc_email_test_order_id ) {

	global $wpdb;
	
	$order_id_query = 'SELECT ID as order_id FROM '.$wpdb->prefix . 'posts'.' WHERE post_type = "shop_order" GROUP BY ID ORDER BY ID DESC LIMIT 100';
	$order_id = $wpdb->get_results( $order_id_query  );
	if( empty( $order_id ) ) {
	
		return "<strong style='color:red;'>No Orders - Please create an order</strong><br><select style='height:20px;width:320px;' id='wc_email_test_order_id' size='40' name='wc_email_test_order_id'></select>";
	
	} else {
	
		$order_id_select_options = "<option value='recent'>Most Recent</option>";
		foreach( $order_id as $id ) {
			$order_id_select_options .= "<option value='{$id->order_id}'>#{$id->order_id}</option>";
		}
		
		$order_id_select_options = str_replace( "value='{$wc_email_test_order_id}'", "value='{$wc_email_test_order_id}' selected", $order_id_select_options ); 
		
		$order_id_select = "<select style='height:200px;width:200px;' id='wc_email_test_order_id' size='40' name='wc_email_test_order_id'>{$order_id_select_options}</select>";
		
		return $order_id_select;
		
	}
	
}

function wetp_get_sub_id_select_field( $wc_email_test_order_id ) {

	global $wpdb;
	
	$order_id_query = 'SELECT ID as order_id FROM '.$wpdb->prefix . 'posts'.' WHERE post_type = "shop_subscription" GROUP BY ID ORDER BY ID DESC LIMIT 100';
	$order_id = $wpdb->get_results( $order_id_query  );
	if( empty( $order_id ) ) {
	
		return "<strong style='color:red;'>No Subscriptions - Please create a subscription</strong><br><select style='height:20px;width:320px;' id='wc_email_test_sub_id' size='40' name='wc_email_test_sub_id'></select>";
	
	} else {
	
		$order_id_select_options = "<option value='recent'>Most Recent</option>";
		foreach( $order_id as $id ) {
			$order_id_select_options .= "<option value='{$id->order_id}'>#{$id->order_id}</option>";
		}
		
		$order_id_select_options = str_replace( "value='{$wc_email_test_order_id}'", "value='{$wc_email_test_order_id}' selected", $order_id_select_options ); 
		
		$order_id_select = "<select style='height:200px;width:200px;' id='wc_email_test_sub_id' size='40' name='wc_email_test_sub_id'>{$order_id_select_options}</select>";
		
		return $order_id_select;
		
	}
	
}

function wetp_show_test_email_buttons(){

	global $wetp_test_email_class, $wetp_test_email_class_additional, $wetp_test_email_class_subscriptions;
	
	$site_url = site_url();
	
	foreach( $wetp_test_email_class as $class=>$name ) {
	
		echo " <a href='{$site_url}/?woocommerce_email_test={$class}' class='button button-primary' target='_blank'>{$name}</a> ";			

	} 
    
    echo "<br/><br/>";
    
	foreach( $wetp_test_email_class_additional as $class=>$name ) {
	
		echo " <a href='{$site_url}/?woocommerce_email_test={$class}' class='button button-primary' target='_blank'>{$name}</a> ";			

	} 
    
    if( is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php') ) {

        echo "<br/><br/>";
        
        echo "<h3>WooCommerce Subscription Emails</h3>";
        
        foreach( $wetp_test_email_class_subscriptions as $class=>$name ) {
        
            echo " <a href='{$site_url}/?woocommerce_email_test={$class}' class='button button-primary' target='_blank'>{$name}</a> ";			

        } 
    
    }
    
}
 
function wetp_update_test_email_options() {

	$updated = false;

	if( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'wept_update_form' ) ) {

		if( isset( $_POST['wc_email_test_email'] ) ){
            
            if( $_POST['wc_email_test_email'] == get_option("wc_email_test_email") ){
                $updated = true;
                $email = true;                
            } else {

                $result = update_option( "wc_email_test_email", sanitize_email( $_POST['wc_email_test_email'] ) );
                if( $result ){
                    $updated = true;
                    $email = true;
                } else {
                    $email = false;
                }
                
			}		
		} else {
            
            $result = update_option( "wc_email_test_email", "" );
            $updated = true;
            $email = true;
        }
				
		if( isset( $_POST['wc_email_test_order_id'] )  ){
				
			$result = update_option( "wc_email_test_order_id", intval( $_POST['wc_email_test_order_id'] ) );					
			$updated = true;
					
		}	
        
		if( isset( $_POST['wc_email_test_sub_id'] )  ){
				
			$result = update_option( "wc_email_test_sub_id", intval( $_POST['wc_email_test_sub_id'] ) );					
			$updated = true;
					
		}
        
		if( !$email ) {
				
			echo "<div id='message' class='error fade'><p><strong>Your email looks invalid</strong></p></div>";
				
		} else {
            if( $updated ) {
                    
                echo "<div id='message' class='updated fade'><p><strong>Your settings have been saved.</strong></p></div>";
                    
            }            
        }
	}

	
	return $updated;

}


function wetp_get_test_email_options() {

	$return = array();

	if( get_option( "wc_email_test_email", "false" ) ) {
	
		$return['wc_email_test_email'] = get_option( "wc_email_test_email", "" );
		
	} else {
	
		$return['wc_email_test_email'] = '';
		
	}
	if( get_option( "wc_email_test_order_id", "false" ) ) {
	
		$return['wc_email_test_order_id'] = get_option( "wc_email_test_order_id", "false" );
		
	} else {
	
		$return['wc_email_test_order_id'] = '';
		
	}
	if( get_option( "wc_email_test_sub_id", "false" ) ) {
	
		$return['wc_email_test_sub_id'] = get_option( "wc_email_test_sub_id", "false" );
		
	} else {
	
		$return['wc_email_test_sub_id'] = '';
		
	}
	
	return $return;
	
}



function wetp_update_license_key(){
    
	$updated = false;

	if( isset( $_POST['nonce'] ) && wp_verify_nonce( $_POST['nonce'], 'wept_update_lisence_key' ) ) {

		if( $_POST['wetp_main_lisence_key']  ){
            
            $wetp_main_lisence_key = sanitize_text_field( $_POST['wetp_main_lisence_key'] );
				
			$result = update_option( "wetp_main_lisence_key", $wetp_main_lisence_key );
					
			$updated = true;
            
            // check for updates
            require 'plugin-update-checker/plugin-update-checker.php';
            $MyUpdateChecker = PucFactory::buildUpdateChecker(
                'http://raiserweb.com/wp-update-server-master/?action=get_metadata&slug=woocommerce-email-test&license_key='.$wetp_main_lisence_key,
                __FILE__,
                'woocommerce-email-test'
            );	   
            $MyUpdateChecker->checkForUpdates();           
					
		} else {
            
            $result = update_option( "wetp_main_lisence_key", null );
            
        }

    }    
    
}

function wetp_get_license_options(){
    
	$return = array();

	if( get_option( "wetp_main_lisence_key", "false" ) ) {
	
		$return['wetp_main_lisence_key'] = get_option( "wetp_main_lisence_key", "" );
		
	} else {
	
		$return['wetp_main_lisence_key'] = '';
        
	}

    return $return;    
    
}