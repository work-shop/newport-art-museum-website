<?php
	
if ( ! empty( $_POST ) && check_admin_referer( 'phoen_reporting_function', 'phoen_reporting_nonce_field' ) ) {
	
	if(isset($_POST['phoen_set_reporting']))
	{
		
		if(isset($_POST['phoen_reporting_enable'])){
			
			$phoen_enable = sanitize_text_field($_POST['phoen_reporting_enable']);
			
			
		}else{
			
			$phoen_enable = '';
		}
		
		update_option('phoen_reportings_enable',$phoen_enable);	
	}
	
}
		
$phoen_reporting_enable_settings = get_option('phoen_reportings_enable');
		
?>
		
<form method="post">

	<?php wp_nonce_field( 'phoen_reporting_function', 'phoen_reporting_nonce_field' ); ?>

	<table class="form-table">

		<tbody>
		
			<tr class="phoen-user-user-login-wrap">
			
				<th><label for="phoen_advance_reporting"><?php _e("Enable Reporting Plugin",'advanced-reporting-for-woocommerce'); ?></label></th>
			
				<td>
				
					<input type="checkbox" value="1" <?php echo(isset($phoen_reporting_enable_settings) && $phoen_reporting_enable_settings == '1')?'checked':'';?> name="phoen_reporting_enable">
					
				</td>
			
			</tr>
			
			
		</tbody>
		
	</table>
	<br />
	<input type="submit" value="<?php _e('Save changes','advanced-reporting-for-woocommerce'); ?>" class="button-primary" name="phoen_set_reporting">
	
</form>
		
<style>

	.form-table th {
	
		width: 270px;
		
		padding: 25px;
		
	}
	
	.form-table td {
	
		padding: 20px 10px;
	}
	
	.form-table {
	
		background-color: #fff;
	}
	
	h3 {
	
		padding: 10px;
		
	}

</style>
	