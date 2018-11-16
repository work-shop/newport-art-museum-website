<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

    if( isset($_GET['tab']) ){
        $tab = $_GET['tab'];
    } else {
        $tab = 'options';
    }
?>
    <div class="wrap">

		<h2>WooCommerce Email Test (Premium)</h2>

        <h2 class="nav-tab-wrapper">
            <a href="?page=woocommerce-email-test&tab=options" class="nav-tab <?php echo $tab == 'options' ? 'nav-tab-active' : ''; ?>">Email Test</a>
            <a href="?page=woocommerce-email-test&tab=premium" class="nav-tab <?php echo $tab == 'premium' ? 'nav-tab-active' : ''; ?>">Premium Key</a>
        </h2>       



        <!-- options tab -->
        <?php if( $tab == 'options' ){ ?>
        
        
        
 		<?php 
		
		// update options if POST	
		wetp_update_test_email_options();
		
        // get option values
		$test_email_options = wetp_get_test_email_options();

		?>       
        
		
		<h3>Settings</h3>
		
		<form method="post" action=""> 
			
			<div class="form-field form-required">
				<label for="wc_email_test_email"><strong>Email Address</strong> for receiving all tests (No email sent if left blank)</label><br/>
				<input id="wc_email_test_email" type="text" size="40" style="width:320px;" width="320px" value="<?php echo $test_email_options['wc_email_test_email']; ?>" name="wc_email_test_email"></input>
			</div>				

			<br/>
                
			<div class="form-field ">
				<label for="wc_email_test_order_id"><strong>Order ID</strong> for test email content (defaults to most recent if left blank)</label>	<br/>				
				<?php echo $order_id_select = wetp_get_order_id_select_field( $test_email_options['wc_email_test_order_id'] ); ?>						
			</div>	
            
            <?php if( is_plugin_active('woocommerce-subscriptions/woocommerce-subscriptions.php') ) { ?>
            <br/>
			<div class="form-field ">
				<label for="wc_email_test_sub_id"><strong>Subscription ID</strong></label>	<br/>				
				<?php echo $sub_id_select = wetp_get_sub_id_select_field( $test_email_options['wc_email_test_sub_id'] ); ?>						
			</div>     
            <?php } ?>
            
			<?php wp_nonce_field( 'wept_update_form', 'nonce' ); ?>

			<p class="submit">
				<input id="submit" class="button button-primary" type="submit" value="Save Settings" name="submit"></input>
			</p>
			
		</form>
	
		<hr/>
		
		<h3>Email Preview</h3>
		<p>The below buttons will open a new tab containing a preview of the test email within your browser, <br/>and send an email to your chosen email address, if you have entered an email address above.

		</p>
		
		<br/>
		
		<?php wetp_show_test_email_buttons(); ?>
        
        <?php } ?>
        <!-- .options tab -->
        
        
        
        
        <!-- premium tab -->
        <?php if( $tab == 'premium' ){ ?>

            <?php 
            // update options if POST	
            wetp_update_license_key();
            
            // get option values
            $test_email_options = wetp_get_license_options();            
            ?>

            
            <h2>Thank you for being a premium plugin user.</h2>
            
            <form method="post" action="">
                     
                <div class="form-field ">
                    <label for="wc_email_test_license_key"><strong>Your License Key:</strong><br/>				
                    <input style="width:400px;" type="text" value="<?php echo $test_email_options['wetp_main_lisence_key']; ?>" name="wetp_main_lisence_key" >					
                </div>	            
                <p class="submit">
                    <input id="submit"  class="button button-primary" type="submit" value="Save Key" name="submit"></input>
                </p>
                
                <?php wp_nonce_field( 'wept_update_lisence_key', 'nonce' ); ?>

            </form>

            
        <?php } ?>
        <!-- .premium tab -->
        
        
    </div>