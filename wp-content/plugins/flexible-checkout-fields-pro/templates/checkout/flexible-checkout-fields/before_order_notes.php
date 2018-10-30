<?php
/**
 * Before Order Notes
 *
 * This template can be overridden by copying it to yourtheme/flexible-checkout-fields-pro/checkout/before_order_notes.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="flexible-checkout-fields-before_order_notes <?php echo $section_settings['section_css']; ?>">

	<?php if ( isset( $section_settings['section_title'] ) && $section_settings['section_title'] != '' ) : ?>
		<<?php echo $section_settings['section_title_type']; ?>><?php echo $section_settings['section_title']; ?></<?php echo $section_settings['section_title_type']; ?>>
	<?php endif; ?>

	<?php foreach ( $fields as $key => $field ) : ?>

		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>

</div>
