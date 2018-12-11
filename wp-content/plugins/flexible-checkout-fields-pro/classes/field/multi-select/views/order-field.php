<?php

global $thepostid, $post;

$field = wp_parse_args(
	$field, array(
		'class'             => 'select',
		'style'             => '',
		'wrapper_class'     => '',
		'name'              => $field['id'],
		'desc_tip'          => false,
		'custom_attributes' => array(),
	)
);

$wrapper_attributes = array(
	'class' => $field['wrapper_class'] . "form-field {$field['id']}_field",
	'id'    => $field['id'] . '_field',
);

$label_attributes = array(
	'for' => $field['id'],
);

$field_attributes          = (array) $field['custom_attributes'];
$field_attributes['style'] = $field['style'];
$field_attributes['id']    = $field['id'];
$field_attributes['name']  = $field['name'];
$field_attributes['class'] = $field['class'];

if ( is_array( $field_attributes['class'] ) ) {
	$field_attributes['class'] = implode( ' ', $field_attributes['class'] );
}

$tooltip     = ! empty( $field['description'] ) && false !== $field['desc_tip'] ? $field['description'] : '';
$description = ! empty( $field['description'] ) && false === $field['desc_tip'] ? $field['description'] : '';

$required = '';
if ( isset( $field['required'] ) && true === $field['required'] ) {
	$required = ' <abbr class="required" title="' . __( 'Required Field', 'flexible-checkout-fields-pro' ) . '">*</abbr>';
}
?>
<p <?php echo wc_implode_html_attributes( $wrapper_attributes ); // WPCS: XSS ok. ?>>
	<label <?php echo wc_implode_html_attributes( $label_attributes ); // WPCS: XSS ok. ?>><?php echo wp_kses_post( $field['label'] ); ?><?php echo $required; // WPCS: Escaping ok. ?></label>
	<?php if ( $tooltip ) : ?>
		<?php echo wc_help_tip( $tooltip ); // WPCS: XSS ok. ?>
	<?php endif; ?>
	<span class="woocommerce-input-wrapper">
		<select multiple <?php echo wc_implode_html_attributes( $field_attributes ); // WPCS: XSS ok. ?>>
			<?php
			foreach ( $field['options'] as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '"' . selected( '1', in_array( strval( $key ), $field_values, true ) ? '1' : '0', false ) . '>' . esc_html( $value ) . '</option>';
				echo "\n";
			}
			?>
		</select>
	</span>
	<?php if ( $description ) : ?>
		<span class="description"><?php echo wp_kses_post( $description ); ?></span>
	<?php endif; ?>
</p>

