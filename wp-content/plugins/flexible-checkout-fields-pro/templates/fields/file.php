<?php
/**
 * File
 *
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields-pro/fields/file.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$required = '';
if($args['required'] == true){
    $required = '<abbr class="*" title="'. __( 'Required Field', 'flexible-checkout-fields-pro' ).'">*</abbr>';
}
$custom_attributes         = array();
if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
    foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
        $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
    }
}
?>
<p class="form-row form-file <?php echo implode( ' ', $args['class'] ) ?>" id="<?php echo $key; ?>_field" data-priotiry="<?php echo $args['priority']; ?>">
    <label for="<?php echo $key; ?>"><?php echo $args['label']; ?> <?php echo $required; ?></label>
    <input type="text" style="display:none;" class="inspire-file" name="<?php echo $key; ?>" id="<?php echo $key; ?>" placeholder="<?php echo $args['placeholder']; ?>" value="<?php echo $value; ?>" />
    <input type="file" field_name="<?php echo $key; ?>" style="display:none;" class="inspire-file-file" name="<?php echo $key; ?>_file" id="<?php echo $key; ?>_file" value="<?php echo $value; ?>"
	<?php echo empty( $custom_attributes ) ? '' : implode( ' ', $custom_attributes ); ?>
    '/>
    <span class="inspire-file-info" style="display:none;"><br/></span>
    <span class="inspire-file-error" style="display:none;"><br/></span>
    <input class="inspire-file-add-button" type="button" value="<?php echo __( 'Upload File', 'flexible-checkout-fields-pro' ); ?>" />
    <input class="inspire-file-delete-button" type="button" value="<?php echo __( 'Remove File', 'flexible-checkout-fields-pro' ); ?>" style="display:none;" />
</p>
