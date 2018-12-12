<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
html += '<div class="field_file_types" style="display:none;">';
html += '<label for="file_types_' + field_slug + '"><?php _e( 'Allowed file types', 'flexible-checkout-fields-pro' ) ?></label>';
html += '<input type="text" id="option_' + field_slug + '" name="inspire_checkout_fields[settings]['+ field_section +'][' + field_slug + '][file_types]" value="" class=""field_file_types" data-qa-id="field-file-types" />';
html += '<p class="description"><?php _e( 'Format: comma separated list. Example: <code>pdf,doc</code>', 'flexible-checkout-fields-pro' ) ?></p>';
html += '</div>';
html += '<div class="field_file_size" style="display:none;">';
html += '<label for="file_size_' + field_slug + '"><?php _e( 'Maximum file size [MB]', 'flexible-checkout-fields-pro' ) ?></label>';
html += '<input type="number" step="1" min="1" id="option_' + field_slug + '" name="inspire_checkout_fields[settings]['+ field_section +'][' + field_slug + '][file_size]" value="" class="field_file_size" data-qa-id="field-file-size" />';
html += '<p class="description"><?php _e( 'Maximum file size in MB.', 'flexible-checkout-fields-pro' ) ?></p>';
html += '</div>';
html += '<div class="field_date_format" style="display:none;">';
html += '<label for="option_' + field_slug + '"><?php _e( 'Date format', 'flexible-checkout-fields-pro' ) ?></label>';
html += '<input type="text" id="option_' + field_slug + '" name="inspire_checkout_fields[settings]['+ field_section +'][' + field_slug + '][date_format]" value="dd.mm.yy" class="field_date_format" data-qa-id="field-date-format" />';
html += '<p class="description"><?php _e( 'Date format <code>dd.mm.yy</code>.', 'flexible-checkout-fields-pro' ) ?></p>';
html += '</div>';								    	        	
html += '<div class="field_date_days_before" style="display:none;">';
html += '<label for="option_' + field_slug + '"><?php _e( 'Days before', 'flexible-checkout-fields-pro' ) ?></label>';
html += '<input type="text" id="option_' + field_slug + '" name="inspire_checkout_fields[settings]['+ field_section +'][' + field_slug + '][days_before]" value="" class="field_days_before" data-qa-id="field-days-before" />';
html += '<p class="description"><?php _e( 'Leave blank for unlimited', 'flexible-checkout-fields-pro' ) ?></p>';
html += '</div>';								    	        	
html += '<div class="field_date_days_after" style="display:none;">';
html += '<label for="option_' + field_slug + '"><?php _e( 'Days after', 'flexible-checkout-fields-pro' ) ?></label>';
html += '<input type="text" id="option_' + field_slug + '" name="inspire_checkout_fields[settings]['+ field_section +'][' + field_slug + '][days_after]" value="" class="field_days_after" data-qa-id="field-days-after" />';
html += '<p class="description"><?php _e( 'Leave blank for unlimited', 'flexible-checkout-fields-pro' ) ?></p>';
html += '</div>';							    	        	
