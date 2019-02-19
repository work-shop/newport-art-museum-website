<?php

/** @var array $field */

$field_args = array();

$field_args['id']            = isset( $field['id'] ) ? $field['id'] : '';
$field_args['label']         = isset( $field['label'] ) ? $field['label'] : '';
$field_args['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
$field_args['class']         = 'short';
$field_args['style']         = isset( $field['style'] ) ? $field['style'] : '';
$field_args['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
$field_args['name']          = isset( $field['id'] ) ? $field['id'] : '';
$field_args['type']          = isset( $field['type'] ) ? $field['type'] : 'text';
$field_args['options']       = isset( $field['options'] ) ? $field['options'] : array();
$field_args['desc_tip']      = isset( $field['desc_tip'] ) ? $field['desc_tip'] : false;
$field_args['data_type']     = empty( $field['data_type'] ) ? '' : $field['data_type'];

$field_args['value'] = $value;

switch ( $field['type'] ) {
	case 'checkbox':
	case 'inspirecheckbox':
		$field_args['type']    = 'checkbox';
		$field_args['cbvalue'] = $field['placeholder'];
		unset( $field_args['class'] );
		woocommerce_wp_checkbox( $field_args );
		break;
	case 'textarea':
		$field_args['type'] = 'textarea';
		woocommerce_wp_textarea_input( $field_args );
		break;
	case 'select':
		$field_args['type'] = 'select';
		woocommerce_wp_select( $field_args );
		break;
	case Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type::FIELD_TYPE_MULTISELECT:
		$field_args['type']  = Flexible_Checkout_Fields_Pro_Multi_Select_Field_Type::FIELD_TYPE_MULTISELECT;
		$field_args['name'] .= '[]';
		$renderer            = new Flexible_Checkout_Fields_Pro_Order_Multi_Select_Metabox_Renderer( $field_args );
		$renderer->render();
		break;
	default:
		$field_args['type'] = 'text';
		woocommerce_wp_text_input( $field_args );
}

