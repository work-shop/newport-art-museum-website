<?php
/**
 * Datepicker
 *
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields-pro/fields/datepicker.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$required = '';
if($args['required'] == true){
    $required = '<abbr class="required" title="'. __( 'Required Field', 'flexible-checkout-fields-pro' ).'">*</abbr>';
}
$custom_attributes         = array();
if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
    foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
        $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
    }
}
?>
<p class="form-row form-datepicker <?php echo implode( ' ', $args['class'] ); ?>" id="<?php echo $key ?>_field" data-priotiry="<?php echo $args['priority']; ?>">
	<label for="<?php echo $key; ?>"><?php echo $args['label']; ?> <?php echo $required; ?></label>
	<input type="text"
        class="input-text load-datepicker"
        name="<?php echo $key; ?>"
        id="<?php echo $key; ?>"
	    placeholder="<?php echo $args['placeholder']; ?>"
        value="<?php echo $value; ?>"
		date_format="<?php echo $args['custom_attributes']['date_format'] ?>"
        days_before="<?php echo ( isset( $args['custom_attributes']['days_before'] ) ? $args['custom_attributes']['days_before'] : '' ); ?>"
        days_after="<?php echo ( isset( $args['custom_attributes']['days_after'] ) ? $args['custom_attributes']['days_after'] : '' ); ?>"
        <?php echo empty( $custom_attributes ) ? '' : implode( ' ', $custom_attributes ); ?>
	/>
</p>