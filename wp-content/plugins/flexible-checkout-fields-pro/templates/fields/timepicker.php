<?php
/**
 * Timepicker
 *
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields-pro/fields/timepicker.php
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
<p class="form-row form-timepicker <?php echo implode( ' ', $args['class'] ); ?>" id="<?php echo $key; ?>_field" data-priotiry="<?php echo $args['priority']; ?>">
    <label for="<?php echo $key; ?>"><?php echo $args['label']; ?> <?php echo $required; ?></label>
    <input
        type="text"
        class="input-text load-timepicker"
        name="<?php echo $key; ?>"
        id="<?php echo $key; ?>"
        placeholder="<?php echo $args['placeholder']; ?>"
        value="<?php echo $value; ?>"
		<?php echo empty( $custom_attributes ) ? '' : implode( ' ', $custom_attributes ); ?>
    />
</p>