<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div>
	<label for="option_<?php echo $name ?>"><?php _e( 'Allowed file types', 'flexible-checkout-fields-pro' ) ?></label>

	<input type="text" id="option_<?php echo $name ?>" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][file_types]" value="<?php echo isset($settings[$key][$name]['file_types'])?$settings[$key][$name]['file_types']:''; ?>" class="field_file_types" data-qa-id="field-file-types" />

	<p class="description"><?php _e( 'Format: comma separated list. Example: <code>pdf,doc</code>', 'flexible-checkout-fields-pro' ) ?></p>
</div>								    	        	
<div>
	<label for="option_<?php echo $name ?>"><?php _e( 'Maximum file size [MB]', 'flexible-checkout-fields-pro' ) ?></label>

	<input type="number" step="1" min="1" id="option_<?php echo $name ?>" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][file_size]" value="<?php echo isset($settings[$key][$name]['file_size'])?$settings[$key][$name]['file_size']:''; ?>" class="field_file_size" data-qa-id="field-file-size" />

	<p class="description"><?php _e( 'Maximum file size in MB.', 'flexible-checkout-fields-pro' ) ?></p>
</div>								    	        	
