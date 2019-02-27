<?php
/**
 * Radio
 *
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields-pro/fields/inspireradio.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$required = '';
if ( $args['required'] == true ) {
    $required = '<abbr class="required" title="'. __( 'Required Field', 'flexible-checkout-fields-pro' ).'">*</abbr>';
}
$custom_attributes         = array();
if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
    foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
        $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
    }
}
?>

<div class="form-row form-row-wide form-inspireradio <?php echo implode( ' ', $args['class'] ); ?>" id="<?php echo $key; ?>_field">
    <fieldset><legend><?php echo $args['label']; ?> <?php echo $required; ?></legend><?php foreach ( $args['options'] as $okey => $option ) :
	    $checked = "";
	    if ( $okey == $value ) {
		    $checked = " checked";
	    } ?><label><input
                    type="radio"
                    class="input-radio input-inspireradio"
                    name="<?php echo $key; ?>"
                    id="<?php echo $key; ?>_<?php echo $okey; ?>"
                    value="<?php echo $okey; ?>" <?php echo $checked; ?>
			        <?php echo empty( $custom_attributes ) ? '' : implode( ' ', $custom_attributes ); ?>
            /> <?php echo $option; ?></label>
	    <?php unset($checked); ?>
    <?php endforeach; ?></fieldset>
</div>
