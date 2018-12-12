<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
case 'select':
	jQuery(this).closest('.field-item').find('.element-option').addClass('show');
	jQuery(this).closest('.field-item').find('.field_placeholder').hide();
	jQuery(this).closest('.field-item').find('.field_placeholder label').html('<?php _e( 'Placeholder', 'flexible-checkout-fields-pro' ); ?>');
break;

case 'inspireradio':
	jQuery(this).closest('.field-item').find('.element-option').addClass('show');
	jQuery(this).closest('.field-item').find('.field_placeholder').hide();
	jQuery(this).closest('.field-item').find('.field_placeholder label').html('<?php _e( 'Placeholder', 'flexible-checkout-fields-pro' ); ?>');
break;

case 'inspirecheckbox':
case 'checkbox':
	jQuery(this).closest('.field-item').find('.element-option').removeClass('show');
	jQuery(this).closest('.field-item').find('.field_placeholder label').html('<?php _e( 'Value', 'flexible-checkout-fields-pro' ); ?>');
	jQuery(this).closest('.field-item').find('.field_placeholder').show();
break;
