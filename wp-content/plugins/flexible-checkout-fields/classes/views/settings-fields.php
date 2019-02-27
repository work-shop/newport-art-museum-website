<?php
global $woocommerce;

/** @see validate_checkout method https://github.com/woocommerce/woocommerce/blob/master/includes/class-wc-checkout.php#L719 */
$fields_always_required_by_woocommerce = array( 'shipping_country', 'billing_country' );

$validation_options = $this->plugin->field_validation->get_validation_options();

$checkout_fields = $args['checkout_fields'];
$settings        = get_option( 'inspire_checkout_fields_settings' );

$checkout_field_type = $args['plugin']->get_fields();

$fields_display_on = array(
	'thank_you' => array( 'label' => __( 'Thank You Page', 'flexible-checkout-fields' ) ),
    'address'   => array( 'label' => __( 'My Account - address', 'flexible-checkout-fields' ) ),
    'order'     => array( 'label' => __( 'My Account - order', 'flexible-checkout-fields' ) ),
    'emails'    => array( 'label' => __( 'Emails', 'flexible-checkout-fields' ) ),
);

$plugin = $args['plugin'];
$current_section = array();

foreach ( $plugin->sections as $section ) {
    if ( $section['tab'] == $current_tab ) {
        $current_section = $section;
    }
}

?>

<div class="wrap">
    <?php $this->admin_notices(); ?>
	<?php if ( ! empty( $_POST['option_page'] ) && $_POST['option_page'] === 'inspire_checkout_fields_settings' ): ?>
		<?php if ( isset( $_POST['reset_settings'] ) ) : ?>
            <div id="message" class="updated fade">
                <p><strong><?php _e( 'Settings resetted.', 'flexible-checkout-fields' ); ?></strong></p>
            </div>
		<?php endif; ?>
        <div id="message" class="updated fade">
            <p><strong><?php _e( 'Settings saved.', 'flexible-checkout-fields' ); ?></strong></p>
        </div>
	<?php endif; ?>

    <div id="nav-menus-frame" class="wp-clearfix">
        <div id="menu-settings-column" class="metabox-holder add-new-field-box">
            <div id="side-sortables" class="accordion-container">
                <form method="post" action="" id="add-new-field">
                    <h3><?php _e( 'Add New Field', 'flexible-checkout-fields' ); ?></h3>

                    <div class="add-new-field-content accordion-section-content" style="display:block;">
                        <div>
                            <label for="woocommerce_checkout_fields_field_type"><?php _e( 'Field Type', 'flexible-checkout-fields' ); ?></label>

                            <select id="woocommerce_checkout_fields_field_type"
                                    name="woocommerce_checkout_fields_field_type">
								<?php foreach ( $checkout_field_type as $key => $value ): ?>
                                    <?php if ( $key == 'checkbox' ) continue; ?>
                                    <option value="<?php echo $key ?>"><?php echo $value['name'] ?></option>
								<?php endforeach; ?>
                            </select>
                        </div>

                        <div id="woocommerce_checkout_fields_field_name_container">
                            <label for="woocommerce_checkout_fields_field_name"><?php _e( 'Label', 'flexible-checkout-fields' ); ?></label>

                            <textarea id="woocommerce_checkout_fields_field_name"
                                      name="woocommerce_checkout_fields_field_name"></textarea>

                            <p class="description"><?php _e( 'You can use HTML.', 'flexible-checkout-fields' ); ?></p>
                        </div>

                        <div id="woocommerce_checkout_fields_field_short_name_container">
                            <label for="woocommerce_checkout_fields_field_short_name"><?php _e( 'Name', 'flexible-checkout-fields' ); ?></label>

                            <input type="text" data-changed="0" id="woocommerce_checkout_fields_field_short_name" name="woocommerce_checkout_fields_field_short_name" style="width:100%;"/>

                            <p class="description"><?php echo sprintf( __( 'Meta name: %s.', 'flexible-checkout-fields' ), '<span id="woocommerce_checkout_fields_field_short_name_meta"></span>' ); ?></p>
                        </div>

                        <div id="woocommerce_checkout_fields_field_name_container_pro" style="display:none;">
                            <div class="updated">
                                <?php
                                    $pro_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/sklep/woocommerce-checkout-fields/' : 'https://www.wpdesk.net/products/flexible-checkout-fields-pro-woocommerce/';
                                ?>
                                <p><?php _e( 'This field is available in the PRO version.', 'flexible-checkout-fields' ); ?> <a href="<?php echo $pro_link; ?>?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=flexible-checkout-fields-pro-fields" target="_blank"><?php _e( 'Upgrade to PRO now &rarr;', 'flexible-checkout-fields' ); ?></a></p>
                            </div>
                        </div>

                        <div style="display:none;">
                            <label for="woocommerce_checkout_fields_field_section"><?php _e( 'Section', 'flexible-checkout-fields' ); ?></label>

                            <select id="woocommerce_checkout_fields_field_section"
                                    name="woocommerce_checkout_fields_field_section">
								<?php foreach ( $this->plugin->sections as $custom_section => $custom_section_data ) : ?>
									<?php $selected = ""; ?>
									<?php if ( $custom_section_data['tab'] == $current_tab ) {
										$selected = " selected";
									} ?>
                                    <option value="<?php echo $custom_section_data['section']; ?>" <?php echo $selected; ?>><?php echo $custom_section_data['tab_title']; ?></option>
								<?php endforeach; ?>
                            </select>
                        </div>

                        <p class="list-controls"><?php _e( 'Save changes after adding a field.', 'flexible-checkout-fields' ) ?></p>

                        <p class="button-controls wp-clearfix">
							<span class="add-to-menu">
								<input id="button_add_field" type="button" name=""
                                       value="<?php _e( 'Add Field', 'flexible-checkout-fields' ) ?>"
                                       class="button-secondary right">
							</span>
                        </p>
                    </div>
                </form>
            </div>
            <?php if ($args['show_ads']): ?>
			    <?php include( 'settings-ads.php' ); ?>
            <?php endif; ?>
            <?php do_action( 'flexible_checkout_fields_after_add_new_field' ); ?>
        </div>

        <div id="menu-management-liquid">
            <div id="menu-management">
                <form method="post" action="" id="inspire_checkout_field" class="nav-menus-php">
					<?php settings_fields( 'inspire_checkout_fields_settings' ); ?>

                    <div class="menu-edit wp-clearfix">
                        <div id="nav-menu-header">
                            <div class="major-publishing-actions wp-clearfix">
                                <h3><?php _e( 'Edit Section', 'flexible-checkout-fields' ) ?></h3>

                                <div class="publishing-action">
                                    <span class="spinner"></span>
                                    <input type="submit" name=""
                                           value="<?php _e( 'Save Changes', 'flexible-checkout-fields' ) ?>"
                                           class="button button-primary">
                                </div>
                            </div>
                        </div>

						<?php foreach ( $checkout_fields as $key => $fields ): ?>
							<?php if ( 'fields_' . $key != $current_tab ) {
								continue;
							} ?>
                            <input class="field_key" type="hidden" name="inspire_checkout_fields[settings][<?php echo $key ?>]" value=""/>
                            <div id="post-body" class="fields-container">
                                <h3><?php _e( 'Section Fields', 'flexible-checkout-fields' ) ?><?php //echo $checkout_field_type_name[$key] ?></h3>

                                <ul class="fields menu sortable" id="<?php echo $key; ?>">
									<?php foreach ( $fields as $name => $field ) : ?>
										<?php
                                            if ( empty( $settings[ $key ][ $name ]['short_name'] ) ) {
	                                            $field['short_name'] = $name;
	                                            $settings[ $key ][ $name ]['short_name'] = $name;
	                                            if ( strpos( $name, $key) === 0 ) {
		                                            $settings[ $key ][ $name ]['short_name'] = substr( $name, strlen( $key ) + 1 );
		                                            $field['short_name']                     = substr( $name, strlen( $key ) + 1 );
	                                            }
                                            }
										    $field_required = ( ! empty( $settings[ $key ][ $name ]['required'] ) && $settings[ $key ][ $name ]['required'] == '1' ) || ( isset( $field['required'] ) && $field['required'] == 1 && empty( $settings[ $key ][ $name ]['required'] ) );
										    $field_visible  = empty( $settings[ $key ][ $name ]['visible'] );
										    //$is_custom_field = ! empty( $settings[ $key ][ $name ]['custom_field'] ) and $settings[ $key ][ $name ]['custom_field'] == 1;
										    $is_custom_field = ! empty( $field['custom_field'] ) && $field['custom_field'] == 1;
										    $field_type = 'text';
										    if ( isset( $field['type'] ) ) {
											    $field_type = $field['type'];
                                            }
										    if ( $is_custom_field && empty( $settings[ $key ][ $name ]['type'] ) ) {
											    $settings[ $key ][ $name ]['type'] = 'text';
										    }
										?>
                                        <li
	                                        class="field-item menu-item<?php if ( ! $field_visible ): ?> field-hidden<?php endif; ?> fcf-field-<?php echo $field_type; ?>"
	                                        data-qa-id="field-item"
	                                        data-qa-id2="<?php if ( isset( $settings[ $key ][ $name ]['label'] ) ): echo esc_attr( $settings[ $key ][ $name ]['label'] ); elseif ( isset( $field['label'] ) ): echo esc_attr( $field['label'] ); endif; ?>"
                                        >
                                            <div class="menu-item-bar">
                                                <div class="menu-item-handle field-item-handle">
													<?php if ( ! empty( $settings[ $key ][ $name ]['custom_field'] ) && $settings[ $key ][ $name ]['custom_field'] == '1' ): ?>
                                                        <input class="field_custom_field"
                                                               type="hidden"
                                                               name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][custom_field]"
                                                               value="1"
                                                               data-qa-id="field-custom-field"
                                                        />
                                                    <?php else : ?>
                                                        <input class="field_custom_field"
                                                               type="hidden"
                                                               name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][custom_field]"
                                                               value="0"
                                                               data-qa-id="field-custom-field"
                                                        />
													<?php endif; ?>

                                                    <input
                                                        class="field_name"
                                                        type="hidden"
                                                        name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][name]"
                                                        value="<?php echo $name ?>"
                                                        data-qa-id="field-name"
                                                    />

                                                    <span class="item-title">
                                                        <span class="item-type">
                                                            <?php if ( isset( $checkout_field_type[$field_type] ) ) : ?>
	                                                            <?php echo $checkout_field_type[$field_type]['name']; ?>
                                                            <?php else : ?>
	                                                            <?php echo __( ucfirst( $field_type ), 'woocommerce' ); ?>
                                                            <?php endif; ?>
    													</span>
								    	    			<?php if ( ! empty( $field['label'] ) ): ?>
													        <?php echo esc_html( strip_tags( $field['label'] ) ); ?>
												        <?php else: ?>
													        <?php echo $name ?>
												        <?php endif; ?>

														<?php if ( $field_required ): ?> *<?php endif; ?>
													</span>

                                                    <span class="item-controls">
								    	    			<a href="#" class="item-edit more"><span class="screen-reader-text"><?php _e( 'Edit', 'flexible-checkout-fields' ) ?></span></a>
													</span>
                                                </div>
                                            </div>

                                            <div class="menu-item-settings field-settings">
                                                <div class="nav-tab-wrapper">
                                                    <a href="#general"
                                                       class="nav-tab nav-tab-active"><?php _e( 'General', 'flexible-checkout-fields' ); ?></a>
                                                    <a class="nav-tab"
                                                       href="#appearance"><?php _e( 'Appearance', 'flexible-checkout-fields' ); ?></a>
                                                    <a class="nav-tab display-options display-options-<?php echo $field_type; ?>"
                                                       href="#display-options"><?php _e( 'Display On', 'flexible-checkout-fields' ); ?></a>
													<?php
													$additional_tabs = apply_filters( 'flexible_checkout_fields_field_tabs', array() );
													foreach ( $additional_tabs as $additional_tab ) {
														?>
                                                        <a class="nav-tab"
                                                           href="#<?php echo $additional_tab['hash']; ?>"><?php echo $additional_tab['title']; ?></a>
														<?php
													}
													?>
                                                </div>
                                                <div class="field-settings-tab-container field-settings-general">
													<?php if ( $is_custom_field ): ?>
														<?php if ( isset( $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['description'] ) ) : ?>
                                                            <div class="element-<?php echo $settings[ $key ][ $name ]['type']; ?>-description show">
                                                                <p class="description"><?php echo $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['description']; ?></p>
                                                            </div>
														<?php endif; ?>
													<?php endif; ?>


                                                    <div>
                                                        <input type="hidden"
                                                               name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][visible]"
                                                               value="1"
                                                        />

                                                        <label>
                                                            <input class="field_visible"
                                                                   type="checkbox"
                                                                   name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][visible]"
                                                                   value="0" <?php if ( $field_visible ): ?> checked<?php endif; ?>
                                                                   data-qa-id="field-visible"
                                                            />
															<?php _e( 'Enable Field', 'flexible-checkout-fields' ) ?>
                                                        </label>
                                                    </div>

													<?php
                                                    $disabled = '';
													$checked = '';
													$style   = '';
													if ( isset( $settings[ $key ][ $name ]['type'] )
													     && isset( $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['has_required'] )
													     && $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['has_required'] == false
													) {
														$style = ' display:none; ';
													} else {
														if ( $field_required ) {
															$checked = ' checked';
														}
													}
													if ( in_array( $name, $fields_always_required_by_woocommerce ) ) {
													    if ( $field_required ) {
														    $disabled = ' disabled';
													    }
                                                    }

													if( empty( $field['class'] ) ) {
														$field['class'] = array( 'form-row' );
                                                    }
													?>
                                                    <div style="<?php echo $style; ?>">
                                                        <input type="hidden"
                                                               name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][required]"
                                                               value="0"
                                                        />
                                                        <label>
                                                            <input class="field_required"
                                                                   type="checkbox"
                                                                   name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][required]"
                                                                   value="1" <?php echo $checked; ?> <?php echo $disabled; ?>
                                                                   data-qa-id="field-required"
                                                            />
															<?php _e( 'Required Field', 'flexible-checkout-fields' ) ?>
                                                        </label>
                                                    </div>

                                                    <div class="element_<?php echo $name ?> field-type-label">

                                                        <label for="label_<?php echo $name ?>"><?php _e( 'Label', 'flexible-checkout-fields' ) ?></label>

                                                        <textarea data-field="<?php echo $name; ?>" class="fcf_label field-name field_label" id="label_<?php echo $name ?>" class="field-name"
                                                                  name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][label]"
                                                                  data-qa-id="field-name"
                                                        ><?php if ( isset( $settings[ $key ][ $name ]['label'] ) ): echo esc_html( $settings[ $key ][ $name ]['label'] );
															elseif ( isset( $field['label'] ) ): echo esc_html( $field['label'] ); endif; ?></textarea>

                                                        <p class="description"><?php _e( 'You can use HTML.', 'flexible-checkout-fields' ); ?></p>
                                                    </div>

                                                    <?php $short_name_disabled = 'readonly'; ?>
	                                                <?php if ( !$is_custom_field ): ?>
		                                                <?php $short_name_disabled = 'disabled'; ?>
                                                    <?php endif; ?>
                                                    <div class="field-type-name">

                                                        <label for="short_name_<?php echo $name ?>"><?php _e( 'Name', 'flexible-checkout-fields' ) ?></label>

                                                        <?php $short_name_value = ''; ?>
                                                        <?php if ( isset( $settings[ $key ][ $name ]['short_name'] ) ): $short_name_value = esc_attr( $settings[ $key ][ $name ]['short_name'] ); elseif ( isset( $field['short_name'] ) ): $short_name_value = $field['short_name']; endif; ?>

                                                        <input
                                                                required <?php echo $short_name_disabled; ?>
                                                                class="short_name field_short_name"
                                                                type="text"
                                                                name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][short_name]" value="<?php echo $short_name_value; ?>"
                                                                data-qa-id="field-short-nem"
                                                        />

                                                        <p class="description"><?php echo sprintf( __( 'Meta name: %s.', 'flexible-checkout-fields' ), '<strong>' . '_' . $key . '_' . $short_name_value . '</strong>' ); ?></p>

                                                    </div>

                                                    <div class="field-validation field-validation-<?php echo $field_type; ?>">

                                                        <label for="validation_<?php echo $name ?>"><?php _e( 'Validation', 'flexible-checkout-fields' ) ?></label>

		                                                <?php $validation_value = ''; ?>
		                                                <?php if ( isset( $settings[ $key ][ $name ]['validation'] ) ): $validation_value = esc_attr( $settings[ $key ][ $name ]['validation'] ); elseif ( isset( $field['validation'] ) ): $short_name_value = $field['validation']; endif; ?>

                                                        <select class="validation field_validation" type="text" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][validation]" data-qa-id="field-validation">
                                                            <?php foreach ( $validation_options as $option_value => $option ) : ?>
                                                                <?php if ( $is_custom_field && $option_value == '' ) continue; ?>
                                                                <option value="<?php echo $option_value; ?>" <?php echo selected( $validation_value, $option_value ); ?>><?php echo $option; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <p class="description"><?php echo __( 'For Post Code validation works only with country.', 'flexible-checkout-fields' ); ?></p>
                                                    </div>

	                                                <?php if ( $is_custom_field ): ?>

														<?php
														$required = '';
														if ( isset( $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['has_options'] ) && $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['has_options'] ) {
															$required = ' required';
														}
														?>
                                                        <div class="element-option<?php if ( isset( $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['has_options'] ) && $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['has_options'] )
															echo " show" ?>">
                                                            <label for="option_<?php echo $name ?>"><?php _e( 'Options', 'flexible-checkout-fields' ) ?></label>

                                                            <textarea class="field_option"
                                                                      data-field="<?php echo $name; ?>" class="fcf_options"
                                                                      id="option_<?php echo $name ?>"
                                                                      data-qa-id="field-option"
                                                                      name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][option]" <?php echo $required; ?>><?php echo isset( $settings[ $key ][ $name ]['option'] ) ? esc_textarea( stripslashes( $settings[ $key ][ $name ]['option'] ) ) : ''; ?></textarea>
                                                            <p><?php _e( 'Format: <code>Value : Name</code>. Value will be in the code, name will be visible to the user. One option per line. Example:<br /><code>woman : I am a woman</code><br /><code>man : I am a man</code>', 'flexible-checkout-fields' ) ?></p>
                                                        </div>
													<?php endif; ?>

													<?php if ( $is_custom_field ): ?>
														<?php do_action( 'flexible_checkout_fields_settings_html', $key, $name, $settings ); ?>
                                                        <div class="field_type">
                                                            <label for="type_<?php echo $name ?>"><?php _e( 'Field Type', 'flexible-checkout-fields' ) ?></label>

                                                            <select class="field_type" id="field_type_<?php echo $name ?>"
                                                                    name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][type]"
                                                                    disabled
                                                                    data-qa-id="field-type"
                                                            >
																<?php foreach ( $checkout_field_type as $type_key => $value ): ?>
                                                                    <option value="<?php echo $type_key ?>"<?php if ( $settings[ $key ][ $name ]['type'] == $type_key ) {
																		echo " selected";
																	} ?>><?php echo $value['name'] ?></option>
																<?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    <?php else : ?>
                                                        <?php if ( !empty( $field['type'] ) ) : ?>
                                                            <input
                                                                type="hidden"
                                                                id="field_type_<?php echo $name ?>"
                                                                name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][type]"
                                                                value="<?php echo $field['type']; ?>"
                                                                data-qa-id="field-type"
                                                            />
                                                        <?php endif; ?>
													<?php endif; ?>
                                                </div>
                                                <div class="field-settings-tab-container field-settings-appearance" style="display:none;">
													<?php if ( ! $is_custom_field || empty( $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['disable_placeholder'] ) || ! $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['disable_placeholder'] ): ?>
                                                        <div class="field_placeholder">
															<?php
															$required = '';
															if ( isset( $settings[ $key ][ $name ]['type'] ) && isset( $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['label_is_required'] ) ) {
																$required = ' required';
															}
															?>
                                                            <label for="placeholder_<?php echo $name ?>"><?php if ( $is_custom_field && isset( $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['placeholder_label'] ) ): ?><?php echo $checkout_field_type[ $settings[ $key ][ $name ]['type'] ]['placeholder_label']; ?><?php else: ?><?php _e( 'Placeholder', 'flexible-checkout-fields' ) ?><?php endif; ?></label>

	                                                        <?php
                                                                $disabled = '';
                                                                $tip = '';
                                                                if ( in_array( $name, array(
                                                                    'billing_state', 'billing_country',
                                                                    'shipping_state', 'shipping_country'
                                                                ) ) ) {
		                                                            $disabled = 'disabled';
                                                                    $tip = __( 'This field is address locale dependent and cannot be modified.', 'flexible-checkout-fields' );
                                                                    ?>
                                                                    <span class="woocommerce-help-tip" data-tip="<?php echo $tip;?>"></span>
                                                                    <?php
                                                                }
	                                                        ?>

                                                            <input class="field_placeholder"
                                                                   <?php echo $disabled; ?> type="text" id="placeholder_<?php echo $name ?>"
                                                                   name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][placeholder]"
                                                                   value="<?php if ( ! empty( $settings[ $key ][ $name ]['placeholder'] ) ): echo esc_attr( $settings[ $key ][ $name ]['placeholder'] );
															       else: echo isset( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : ''; endif; ?>" <?php echo $required; ?>
                                                                   data-qa-id="field-placeholder"
                                                            />
                                                        </div>
													<?php endif; ?>
                                                    <div class="field-class">
                                                        <label for="class_<?php echo $name ?>"><?php _e( 'CSS Class', 'flexible-checkout-fields' ) ?></label>
                                                        <input class="field_class" type="text" id="class_<?php echo $name ?>"
                                                               name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][class]"
                                                               value="<?php if ( ! empty( $settings[ $key ][ $name ]['class'] ) ): echo $settings[ $key ][ $name ]['class'];
														       else: if ( ! empty( $field['class'] ) ) {
															       echo implode( ' ', $field['class'] );
														       } endif; ?>"
                                                               data-qa-id="field-class"
                                                        />
                                                    </div>

													<?php do_action( 'flexible_checkout_fields_setting_appearance_html', $key, $name, $settings ); ?>

                                                </div>


                                                <div class="field-settings-tab-container field-settings-display-options" style="display:none;">

                                                    <?php foreach ( $fields_display_on as $display_on_field_key => $display_on_field ) : ?>
                                                        <?php if ( $display_on_field_key == 'address' && !in_array( $key, array( 'billing', 'shipping' ) ) ) continue; ?>
                                                        <div class="fcf-display-on-<?php echo $display_on_field_key; ?>">
                                                            <?php
                                                                $checked = ' checked';
                                                                $style   = '';
                                                                if ( isset( $settings[ $key ][ $name ]['display_on_' . $display_on_field_key ] )
                                                                    && $settings[ $key ][ $name ]['display_on_' . $display_on_field_key ] == '0'
                                                                ) {
                                                                    $checked = '';
                                                                }
                                                            ?>
                                                            <input type="hidden"
                                                                   name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][display_on_<?php echo $display_on_field_key; ?>]"
                                                                   value="0"/>

                                                            <label>
                                                                <input class="field_required"
                                                                       type="checkbox"
                                                                       name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][display_on_<?php echo $display_on_field_key; ?>]"
                                                                       value="1" <?php echo $checked; ?>
                                                                       data-qa-id="field-display-on-address"
                                                                />
                                                                <?php echo $display_on_field['label']; ?>
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>

                                                </div>

												<?php do_action( 'flexible_checkout_fields_field_tabs_content', $key, $name, $field, $settings ); ?>
												<?php if ( $is_custom_field ) : ?>
                                                    <a class="remove-field" data-field="<?php echo $name; ?>"
                                                       href="#"><?php _e( 'Delete Field', 'flexible-checkout-fields' ) ?></a>
												<?php endif; ?>
                                            </div>

                                        </li>
									<?php endforeach; ?>
                                </ul>
                            </div>

							<?php do_action( 'flexible_checkout_fields_section_settings', $key, $settings ); ?>

						<?php endforeach; ?>

                        <div id="nav-menu-footer">
                            <div class="major-publishing-actions wp-clearfix">
                                <input type="submit" name=""
                                       value="<?php _e( 'Save Changes', 'flexible-checkout-fields' ) ?>"
                                       class="button button-primary">
                                <input type="submit"
                                       value="<?php _e( 'Reset Section Settings', 'flexible-checkout-fields' ); ?>"
                                       class="button reset_settings" id="submit" name="reset_settings">
                                <span class="spinner"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var fcf_all_sections = <?php echo json_encode( $plugin->sections, JSON_FORCE_OBJECT );  ?>;
    var fcf_current_section = <?php echo json_encode( $current_section, JSON_FORCE_OBJECT );  ?>;
    var fcf_field_type = <?php echo json_encode( $checkout_field_type ); ?>

    jQuery(document).ready(function () {

        function validate_field_name( field ) {
            var return_false = false;
            jQuery('.short_name').each(function() {
                var field_name = jQuery(this).attr('name');
                var field_settings = jQuery(this).closest('.field-item');
                if ( field_name != jQuery(field).attr('name') ) {
                    if ( jQuery(field).val() == jQuery(this).val() ) {
                        var message = '<?php echo sprintf(__( 'Invalid field name: %s. The name already exists.', 'flexible-checkout-fields' ), '[field]' ); ?>';
                        message = message.replace( '[field]',  jQuery(field).val() );
                        alert( message );
                        return_false = true;
                       return false;
                    }
                }
            })
            if ( return_false ) {
                return false;
            }
            if ( jQuery(field).val() == '' ) {
                alert( '<?php _e( 'Field name can not be empty!', 'flexible-checkout-fields' ); ?>' );
                return false;
            }
            if ( !isNaN( jQuery(field).val() ) ) {
                alert( '<?php _e( 'Field name can not be number!', 'flexible-checkout-fields' ); ?>' );
                return false;
            }
            return true;
        }

        function generate_short_name( short_name ) {
            var unique = false;
            var count = 0;
            var tmp_short_name = short_name;
            while ( !unique ) {
                unique = true;
                jQuery('.short_name').each(function(){
                    if ( jQuery(this).val() == tmp_short_name ) {
                        unique = false;
                        count++;
                        tmp_short_name = short_name + '_' + count.toString();
                    }
                })
            }
            return tmp_short_name;
        }

        jQuery('.sortable').sortable({
            handle: '.field-item-handle',
            placeholder: 'sortable-placeholder',
            opacity: 0.7,
            activate: function (event, ui) {
                ui.item.find('.field-settings').hide();
            }
        });

        function strip_tags( html ) {
            return jQuery('<p>' + html + '</p>').text();
        }

        function htmlEncode(value){
            //create a in-memory div, set it's inner text(which jQuery automatically encodes)
            //then grab the encoded contents back out.  The div never exists on the page.
            return jQuery('<div/>').text(value).html();
        }

        function htmlDecode(value){
            return jQuery('<div/>').html(value).text();
        }

        // Add New Field
        jQuery("#button_add_field").click(function (e) {
            e.preventDefault();

            var field_label = jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_name').val();
            var field_section = jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_section').val();
            var field_type = jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_type').val();
            var field_option = jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_option').val();

            //var field_short_name = generate_short_name( stringToSlug(field_label).substr(0, 20) );
            var field_short_name = jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_short_name').val();
            var field_slug = field_section + '_' + field_short_name;

            // Proceed if Name (label) is filled
            if ( field_label ) {
                if ( !validate_field_name( jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_short_name') ) ) {
                    return false;
                }
                var html = '';
                html += '<li class="field-item menu-item element_' + field_slug + ' just-added fcf-field-' + field_type + '" data-qa-id="field-item" data-qa-id2="' + htmlEncode( field_label ) + '">';
                //html += '<li class="field-item menu-item">';
                html += '<div class="menu-item-bar">';
                html += '<div class="menu-item-handle field-item-handle">';
                html += '<input class="field_custom_field" type="hidden" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][custom_field]" value="1" data-qa-id="field-custom-field">';
                html += '<span class="item-title">';
                html += '<span class="item-type">';
                html += fcf_field_type[field_type]['name'];
                html += '</span>';
                html += htmlEncode( strip_tags(field_label) );
                html += '</span>';
                html += '<span class="item-controls">';
                html += '<a href="#" class="item-edit more"><span class="screen-reader-text"><?php _e( 'Edit', 'flexible-checkout-fields' ) ?></span></a>';
                html += '</span>';
                html += '</div>';
                html += '</div>';
                html += '<div class="menu-item-settings field-settings">';

                html += '<div class="nav-tab-wrapper">';
                html += '<a href="#general" class="nav-tab nav-tab-active"><?php _e( 'General', 'flexible-checkout-fields' ); ?></a>';
                html += '<a class="nav-tab" href="#appearance"><?php _e( 'Appearance', 'flexible-checkout-fields' ); ?></a>';
                html += '<a class="nav-tab display-options display-options-' + field_type + '" href="#display-options"><?php _e( 'Display On', 'flexible-checkout-fields' ); ?></a>';
				<?php
				$additional_tabs = apply_filters( 'flexible_checkout_fields_field_tabs', array() );
				foreach ( $additional_tabs as $additional_tab ) {
				?>
                html += '<a class="nav-tab" href="#<?php echo $additional_tab['hash']; ?>"><?php echo $additional_tab['title']; ?></a>';
				<?php
				}
				?>
                html += '</div>';
                html += '<div class="field-settings-tab-container field-settings-general">';

				<?php foreach ( $checkout_field_type as $key => $value ) : ?>
				<?php if ( isset( $value['description'] ) ) : ?>
                html += '<div class="element-<?php echo $key; ?>-description">';
                html += '<p class="description"><?php echo $value['description']; ?></p>';
                html += '</div>';
				<?php endif; ?>
				<?php endforeach; ?>
                html += '<div>';
                html += '<input type="hidden" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][visible]" value="1">';
                html += '<label>';
                html += '<input class="field_visible" type="checkbox" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][visible]" value="0" checked data-qa-id="field-visible">';
                html += '<?php _e( 'Enable Field', 'flexible-checkout-fields' ) ?>';
                html += '</label>';
                html += '</div>';
                html += '<div>';
                html += '<input type="hidden" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][required]" value="0">';
                html += '<label>';
                html += '<input class="field_required" type="checkbox" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][required]" value="1" data-qa-id="field-required">';
                html += '<?php _e( 'Required Field', 'flexible-checkout-fields' ) ?>';
                html += '</label>';
                html += '</div>';
                html += '<div class="field-type-label">';
                html += '<label class="fcf_label" for="label_' + field_slug + '"><?php _e( 'Label', 'flexible-checkout-fields' ) ?></label>';
                html += '<textarea class="field_label" data-field="' + field_slug + '" id="label_' + field_slug + '" class="fcf_label field-name" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][label]" data-qa-id="field-label">' + field_label + '</textarea>';
                html += '<p class="description"><?php _e( 'You can use HTML.', 'flexible-checkout-fields' ); ?></p>';
                html += '</div>';

                html += '<div class="field-type-name">';
                html += '    <label for="short_name_' + field_slug + '"><?php _e( 'Name', 'flexible-checkout-fields' ) ?></label>';
                html += '    <input required class="field_short_name short_name" type="text" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][short_name]" value="' + field_short_name + '" data-qa-id="field-short-name" readonly />';
                html += '    <p class="description"><?php echo __( 'Meta name: ', 'flexible-checkout-fields' ); ?><strong>_' + field_section + '_' + field_short_name + '</strong></p>';
                html += '    </div>';

                html += '<div class="field-validation field-validation-' + field_type + '">';
                html += '   <label for="validation_' + field_slug + '"><?php _e( 'Validation', 'flexible-checkout-fields' ) ?></label>';
                html += '   <select class="validation field_validation" type="text" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][validation]" data-qa-id="field-validation">';
                <?php unset( $validation_options[''] ); ?>
		        <?php foreach ( $validation_options as $option_value => $option ) : ?>
		            <?php if ( $option_value == '' ) continue; ?>
                    html += '       <option value="<?php echo $option_value; ?>"><?php echo $option; ?></option>';
                <?php endforeach; ?>
                html += '   </select>';
                html += '    <p class="description"><?php echo __( 'For Post Code validation works only with country.', 'flexible-checkout-fields' ); ?></p>';
                html += '</div>';

                <?php do_action( 'flexible_checkout_fields_settings_js_html' ); ?>

                html += '<div class="field_type">';
                html += '<label for="type_' + field_slug + '"><?php _e( 'Field Type', 'flexible-checkout-fields' ) ?></label>';
                html += '<select class="field_type" id="field_type_' + field_slug + '" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][type]" disabled data-qa-id="field-type">' + printSelectTypeOptions(field_type) + '</select>';
                html += '</div>';

                html += '</div>';
                html += '<div class="field-settings-tab-container field-settings-appearance" style="display:none;">';

                html += '<div class="field_placeholder">';
                html += '<label for="placeholder_' + field_slug + '"><?php _e( 'Placeholder', 'flexible-checkout-fields' ) ?></label>';
                html += '<input class="field_placeholder" type="text" id="placeholder_' + field_slug + '" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][placeholder]" value="" data-qa-id="field-placeholder" />';
                html += '</div>';
                html += '<div class="field-class">';
                html += '<label for="class_' + field_slug + '"><?php _e( 'CSS Class', 'flexible-checkout-fields' ) ?></label>';
                html += '<input class="field_class" type="text" id="class_' + field_slug + '" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][class]" value="form-row" data-qa-id="field-class" />';
                html += '</div>';

                html += '</div>';

                html += '<div class="field-settings-tab-container field-settings-display-options" style="display:none;">';

                <?php foreach ( $fields_display_on as $display_on_field_key => $display_on_field ) : ?>
                    html += '<div class=" fcf-display-on-<?php echo $display_on_field_key; ?>">';
                    html += '<input type="hidden" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][display_on_<?php echo $display_on_field_key; ?>]" value="0"/>';
                    html += '<label>';
                    html += '<input class="field_required" type="checkbox" name="inspire_checkout_fields[settings][' + field_section + '][' + field_slug + '][display_on_<?php echo $display_on_field_key; ?>]" value="1" checked data-qa-id="field-display-on-address" />';
                    html += '<?php echo $display_on_field['label']; ?>';
                    html += '</label>';
                    html += '</div>';
                <?php endforeach; ?>

                html += '</div>';

				<?php do_action( 'flexible_checkout_fields_field_tabs_content_js' ); ?>

                html += '<a class="remove-field" href="#"><?php _e( 'Delete Field', 'flexible-checkout-fields' ) ?></a>';
                html += '</li>';
                html += '';

                jQuery('#' + field_section).append(html);
                jQuery('.element_' + field_slug + ' .element-file-description').hide();

                // Add Field Options or Value or Placeholder
                switch (field_type) {

				<?php do_action( 'flexible_checkout_fields_settings_js_options' ); ?>

                    default:
                        jQuery('.element_' + field_slug + ' .field_placeholder label').html('<?php _e( 'Placeholder', 'flexible-checkout-fields' ); ?>');
                        jQuery('.element_' + field_slug + ' .field_placeholder').show();
                        break;
                }
                jQuery(document).trigger("fcf:add_field", [ field_slug ] );
                jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_name').val('');
                jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_short_name').val('');
                jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_short_name_meta').html('');
                jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_short_name').attr('data-changed',0);
            }
            // Display Alert if Name (label) is NOT filled
            else {
                alert('<?php _e( 'Field label can not be empty!', 'flexible-checkout-fields' ) ?>');
            }
        });

        // Toggle field settings
        jQuery(document).on('click', '.field-item a.more', function (e) {
            e.preventDefault();
            jQuery(this).closest('.field-item').find('.field-settings').slideToggle('fast');
            jQuery(this).closest('.field-item').toggleClass('menu-item-edit-active');
            if (jQuery(this).closest('.field-item').hasClass('menu-item-edit-active')) {
                jQuery(this).closest('.field-item').attr('data-qa-id','field-item-active');
            }
            else {
                jQuery(this).closest('.field-item').attr('data-qa-id','field-item');
            }
        });

        jQuery(document).on('change', '#woocommerce_checkout_fields_field_type', function (e) {
            <?php if (!is_flexible_checkout_fields_pro_active()) : ?>
                if ( jQuery(this).val() == 'text' || jQuery(this).val() == 'textarea' ) {
                    jQuery('#woocommerce_checkout_fields_field_name_container').show();
                    jQuery('#woocommerce_checkout_fields_field_short_name_container').show();
                    jQuery('#woocommerce_checkout_fields_field_name_container_pro').hide();
                    jQuery('#button_add_field').prop('disabled',false);
                }
                else {
                    jQuery('#woocommerce_checkout_fields_field_name_container').hide();
                    jQuery('#woocommerce_checkout_fields_field_short_name_container').hide();
                    jQuery('#woocommerce_checkout_fields_field_name_container_pro').show();
                    jQuery('#button_add_field').prop('disabled',true);
                }
            <?php endif; ?>
        })

        var current_field_name_value = '';
        jQuery(document).on( 'keydown', '#woocommerce_checkout_fields_field_short_name', function (e) {
            current_field_name_value = jQuery(this).val();
        });

        jQuery(document).on( 'keyup', '#woocommerce_checkout_fields_field_short_name', function (e) {
            if ( current_field_name_value != jQuery(this).val() ) {
                jQuery(this).attr('data-changed', 1);
                jQuery(this).change();
                current_field_name_value = jQuery(this).val();
            }
        });

        jQuery(document).on( 'change', '#woocommerce_checkout_fields_field_short_name', function (e) {
            var field_section = jQuery(this).closest('form').find('#woocommerce_checkout_fields_field_section').val();
            jQuery('#woocommerce_checkout_fields_field_short_name_meta').html( '_' + field_section + '_' + jQuery(this).val());
        })

        jQuery(document).on( 'keyup', '#woocommerce_checkout_fields_field_name', function (e) {
            if ( jQuery('#woocommerce_checkout_fields_field_short_name').attr('data-changed') == 0 ) {
                var field_label = jQuery(this).val();
                var field_name = generate_short_name( stringToSlug(field_label).substr(0, 20) );
                current_field_name_value = field_name;
                jQuery('#woocommerce_checkout_fields_field_short_name').val(field_name);
                jQuery('#woocommerce_checkout_fields_field_short_name').change();
            }
        })

        // Toggle between placeholder or value
        jQuery(document).on('change', '.field-item .field-settings #woocommerce_checkout_fields_field_type', function (e) {
            switch (jQuery(this).val()) {
                default:
                    jQuery(this).closest('.field-item').find('.element-option').removeClass('show');
                    jQuery(this).closest('.field-item').find('.field_placeholder label').html('<?php _e( 'Placeholder', 'flexible-checkout-fields' ); ?>');
                    jQuery(this).closest('.field-item').find('.field_placeholder').show();
                    break;
            }
            e.preventDefault();
        });

        window.fcf_do_remove_field = false;
        // Remove field
        jQuery(document).on('click', '.field-item a.remove-field', function (e) {
            e.preventDefault();
            var toRemove = jQuery(this).closest('li');
            window.fcf_do_remove_field = true;
            jQuery(this).trigger('fcf:pre_remove_field');
            if ( window.fcf_do_remove_field == true ) {
                var message = '<?php echo sprintf( __( 'Do you really want to delete this field: %s? Deleting a field remove it from all orders.', 'flexible-checkout-fields' ), '[field_name]' ); ?>';
                message = message.replace('[field_name]', toRemove.find('.field-name').val());
                if (confirm(message)) {
                    jQuery(this).trigger('fcf:remove_field');
                    toRemove.remove();
                }
            }
        });

        // When Saving Form Remove disabled from Selects
        jQuery('form').bind('submit', function (event) {
            var return_false = false;
            jQuery('.short_name').each(function(){
                var field_settings = jQuery(this).closest('.field-item');
                var custom_field = jQuery(field_settings).find('.field_custom_field');
                if ( jQuery(custom_field).length && jQuery(custom_field).val() == '1' ) {
                    var validate_field = validate_field_name( this );
                    if ( !validate_field ) {
                        return_false = true;
                        return false;
                    }
                }
            });
            if ( return_false ) {
                return false;
            }
            jQuery(this).find('select').prop('disabled', false);
            jQuery(this).find('.field_required').prop('disabled', false);
            jQuery(this).find('.major-publishing-actions').find('.spinner').css('visibility', 'visible');
            jQuery('.flexible_checkout_fields_add_rule select').each(function () {
                jQuery(this).attr('disabled', 'disabled');
            });
        });

        // Activate Spinner on Save
        jQuery('input[type="submit"]').on('click', function (event) {
            jQuery('#inspire_checkout_field [required]').each(function () {
                if (jQuery(this).val() === '' && jQuery(this).is(':hidden')) {
                    jQuery(this).css('border-color', 'red' );
                    var classes = jQuery(this).closest('.field-settings-tab-container').attr('class').split(' ');
                    var tab = classes[1].split('-')[2];
                    jQuery(this).closest('.field-settings').find('.field-settings-tab-container').hide();
                    jQuery(this).closest('.field-settings').find('.nav-tab-wrapper a').removeClass('nav-tab-active');
                    jQuery(this).closest('.field-settings').find('a[href="#' + tab + '"]').addClass('nav-tab-active');
                    jQuery('.' + classes[1] ).show();
                    jQuery(this).closest('li').find('.field-settings').slideDown();
                }
            });
            if (jQuery(this).hasClass("reset_settings")) {
                if (!confirm('<?php _e( 'Do you really want to reset section settings?. Resetting a section remove all added fields from orders.', 'flexible-checkout-fields' ); ?>')) {
                    return false;
                }
            }
        });
    });

	<?php do_action( 'flexible_checkout_fields_java_script', $settings ); ?>

    jQuery(document).on('click', '.field-settings .nav-tab-wrapper > a', function () {
        jQuery(this).parent().find('a').each(function () {
            jQuery(this).removeClass('nav-tab-active');
        });
        jQuery(this).addClass('nav-tab-active');
        jQuery(this).parent().parent().find('.field-settings-tab-container').each(function () {
            jQuery(this).hide();
        });
        var href = jQuery(this).attr("href");
        var hash = href.substr(href.indexOf("#") + 1);
        jQuery(this).parent().parent().find('.field-settings-' + hash).each(function () {
            jQuery(this).show();
        });
        jQuery(this).blur();
        return false;
    });

    function printSelectTypeOptions(selected) {
        var index;
        var select;
        var sel = "";

        var type = {
		<?php foreach ( $checkout_field_type as $key => $value ) : ?>
		<?php echo $key; ?>:
        '<?php echo $value['name']; ?>',
		<?php endforeach; ?>
    }
        ;

        jQuery.each(type, function (key, value) {
            if (key == selected) sel = " selected";
            select += '<option value="' + key + '"' + sel + '>' + value + '</option>';
            sel = "";
        });

        return select;
    }

    function stringToSlug(str) {
        str = str.replace(/^\s+|\s+$/g, '');
        str = str.toLowerCase();

        var from = "àáäâèéëêìíïîòóöôùúüûñçęóąśłżźćń·/_,:;";
        var to = "aaaaeeeeiiiioooouuuunceoaslzxcn------";
        for (var i = 0, l = from.length; i < l; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
            .replace(/\s+/g, '_') // collapse whitespace and replace by -
            .replace(/-+/g, '_'); // collapse dashes

        return str;
    }

</script>
