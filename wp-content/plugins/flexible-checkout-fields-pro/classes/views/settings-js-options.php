<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
case 'select':
case '<?php echo Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type::FIELD_TYPE_MULTISELECT; ?>':
	jQuery('<div class="element-option show"><label for="option_' + field_slug + '"><?php _e( 'Options', 'flexible-checkout-fields-pro' ) ?></label><textarea data-field="' + field_slug + '" class="fcf_options" id="option_' + field_slug + '" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][option]" data-qa-id="field-option"></textarea><p><?php _e( 'Format: <code>Value : Name</code>. Value will be in the code, name will be visible to the user. One option per line. Example:<br /><code>woman : I am a woman</code><br /><code>man : I am a man</code>', 'flexible-checkout-fields-pro' ) ?></p></div>').insertAfter('.element_' + field_slug + ' .field-validation');
	jQuery('#option_' + field_slug).attr('required','required');
	jQuery('.element_' + field_slug + ' .field_placeholder label').html('<?php _e( 'Placeholder', 'flexible-checkout-fields-pro' ); ?>');
	jQuery('.element_' + field_slug + ' .field_placeholder').hide();
	jQuery('.element_' + field_slug + ' input.field_class').val('select2');
	fcf_add_select_field_description( jQuery( '.element_' + field_slug + ' .field_class' ).attr('id') );
break;

case 'inspireradio':
	jQuery('<div class="element-option show"><label for="option_' + field_slug + '"><?php _e( 'Options', 'flexible-checkout-fields-pro' ) ?></label><textarea data-field="' + field_slug + '" class="fcf_options" id="option_' + field_slug + '" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][option]" data-qa-id="field-option"></textarea><p><?php _e( 'Format: <code>Value : Name</code>. Value will be in the code, name will be visible to the user. One option per line. Example:<br /><code>woman : I am a woman</code><br /><code>man : I am a man</code>', 'flexible-checkout-fields-pro' ) ?></p></div>').insertAfter('.element_' + field_slug + ' .field-validation');
	jQuery('#option_' + field_slug).attr('required','required');
	jQuery('.element_' + field_slug + ' .field_placeholder label').html('<?php _e( 'Placeholder', 'flexible-checkout-fields-pro' ); ?>');
	jQuery('.element_' + field_slug + ' .field_placeholder').hide();
break;

case 'inspirecheckbox':
case 'checkbox':
	jQuery('.element_' + field_slug + ' .field_placeholder label').html('<?php _e( 'Value', 'flexible-checkout-fields-pro' ); ?>');
	jQuery('.element_' + field_slug + ' .field_placeholder input').attr('required','required');
	jQuery('.element_' + field_slug + ' .field_placeholder input').val('<?php _e( 'Yes', 'flexible-checkout-fields-pro' ); ?>');
	jQuery('.element_' + field_slug + ' .field_placeholder').show();
break;

case 'file':
	jQuery('.element_' + field_slug + ' .field_file_types').show();
	jQuery('.element_' + field_slug + ' .field_file_size').show();
	jQuery('.element_' + field_slug + ' .field_placeholder').hide();
	jQuery('.element_' + field_slug + ' .element-file-description').show();
break;

case 'datepicker':
	jQuery('.element_' + field_slug + ' .field_date_format').show();
	jQuery('.element_' + field_slug + ' .field_date_days_before').show();
	jQuery('.element_' + field_slug + ' .field_date_days_after').show();
break;

case 'info':
	jQuery('.element_' + field_slug + ' .field_placeholder').hide();
break;

case 'heading':
	jQuery('.element_' + field_slug + ' .field_placeholder').hide();
	jQuery('.element_' + field_slug + ' .field_required').closest('div').hide();
break;
