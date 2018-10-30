<tr valign="top">
	<th class="titledesc" scope="row">
		<label><?php _e( 'Custom Sections', 'flexible-checkout-fields-pro' ); ?></label>
	</th>

	<td class="forminp forminp-text">
	    <p><?php _e( 'Select custom sections and Save changes. Selected sections appear as tabs in the plugin menu.', 'flexible-checkout-fields-pro' ); ?></p>

		<?php foreach ( $sections as $section => $section_data ) : ?>
			<?php $checked = ""; ?>
			<?php if ( get_option( 'inspire_checkout_fields_' . $section_data['section'], '0' ) == 1 ) $checked = 'checked'; ?>
			<input value="0" id="woocommerce_checkout_fields_css" name="inspire_checkout_fields[<?php echo $section_data['section']; ?>]" type="hidden" />
			<label><input class="regular-checkbox" value="1" id="woocommerce_checkout_fields_css" name="inspire_checkout_fields[<?php echo $section_data['section']; ?>]" type="checkbox" <?php echo $checked; ?> /> <?php echo $section_data['tab_title']; ?> <code class="hook"><?php echo $section; ?></code></label><br/>
		<?php endforeach; ?>

        <p><a class="toggle-hooks" href="#"><?php _e( 'Toggle Hooks', 'flexible-checkout-fields-pro' ); ?></a></p>

        <p class="description"><?php _e( 'Please note that some sections may not be available in your theme or may look garbled. This is not the plugin issue, but the theme issue.', 'flexible-checkout-fields-pro' ); ?></p>
	</td>
</tr>
