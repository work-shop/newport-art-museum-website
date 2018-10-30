<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div>
	<label for="option_<?php echo $name ?>"><?php _e( 'Date format', 'flexible-checkout-fields-pro' ) ?></label>

	<input type="text" id="option_<?php echo $name ?>" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][date_format]" value="<?php echo isset($settings[$key][$name]['date_format'])?$settings[$key][$name]['date_format']:'dd.mm.yy'; ?>"  class="field_date_format" data-qa-id="field-date-format" />

	<p class="description"><?php _e( 'Date format <code>dd.mm.yy</code>', 'flexible-checkout-fields-pro' ) ?></p>
</div>								    	        	
<div>
	<label for="option_<?php echo $name ?>"><?php _e( 'Days before', 'flexible-checkout-fields-pro' ) ?></label>

	<input type="text" id="option_<?php echo $name ?>" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][days_before]" value="<?php echo isset($settings[$key][$name]['days_before'])?$settings[$key][$name]['days_before']:'0'; ?>"  class="field_days_before" data-qa-id="field-days-before" />

	<p class="description"><?php _e( 'Leave blank for unlimited', 'flexible-checkout-fields-pro' ) ?></p>
</div>								    	        	
<div>
	<label for="option_<?php echo $name ?>"><?php _e( 'Days after', 'flexible-checkout-fields-pro' ) ?></label>

	<input type="text" id="option_<?php echo $name ?>" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][days_after]" value="<?php echo isset($settings[$key][$name]['days_after'])?$settings[$key][$name]['days_after']:''; ?>"  class="field_days_after" data-qa-id="field-days-after" />

	<p class="description"><?php _e( 'Leave blank for unlimited', 'flexible-checkout-fields-pro' ) ?></p>
</div>								    	        	
