<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
		</div>
	</div>
<div class="clear"></div>
<div class="order_data_column_container">
	<div class="order_data_column">
		<h3><?php _e( 'Additional checkout fields', 'flexible-checkout-fields-pro' ); ?></h3><br/>
		<?php
			foreach ( $sections as $section => $section_data ) {
				if ( isset( $settings[$section_data['section']] ) && is_array( $settings[$section_data['section']] ) ) {
					foreach ( $settings[$section_data['section']] as $key => $field ) {
						if ( $field['visible'] == 0 && empty( $checkout_field_type[$field['type']]['exclude_in_admin'] ) ) {
							$value = wpdesk_get_order_meta( $order, '_'.$key, true );
							?>
							<b><?php echo $field['label']; ?>:</b> <?php echo $value; ?><br/>
							<?php
						}
					}
				}
			}
		
