<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div class="field-settings-tab-container field-settings-advanced" style="display:none;">
    <div>
        <?php
        $checked = '';
        if ( isset($settings[$key][$name]['conditional_logic_fields']) && $settings[$key][$name]['conditional_logic_fields'] == '1' ) {
            $checked = ' checked';
        }
        ?>
        <label>
            <input data-qa-id="field-conditional-logic-fields" class="field-conditional-logic-fields" type="checkbox" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_fields]" value="1" <?php echo $checked; ?>>
            <?php _e( 'Enable Fields Conditional Logic', 'flexible-checkout-fields-pro' ) ?>
        </label>
    </div>
    <?php
    $style = 'style="display:none;"';
    if ( isset($settings[$key][$name]['conditional_logic_fields']) && $settings[$key][$name]['conditional_logic_fields'] == '1' ) {
        $style = '';
    }
    ?>
    <div class="conditional-logic-fields-fields" <?php echo $style; ?>>
        <div class="options">
            <select data-qa-id="field-conditional-logic-fields-action" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_fields_action]">
                <?php
                $selected = '';
                if ( isset($settings[$key][$name]['conditional_logic_fields_action']) && $settings[$key][$name]['conditional_logic_fields_action'] == 'show' ) {
                    $selected = ' selected';
                }
                ?>
                <option value="show" <?php echo $selected; ?>><?php _e( 'Show this field if', 'flexible-checkout-fields-pro' ); ?></option>
                <?php
                $selected = '';
                if ( isset($settings[$key][$name]['conditional_logic_fields_action']) && $settings[$key][$name]['conditional_logic_fields_action'] == 'hide' ) {
                    $selected = ' selected';
                }
                ?>
                <option value="hide" <?php echo $selected; ?>><?php _e( 'Hide this field if', 'flexible-checkout-fields-pro' ); ?></option>
            </select>
            <select data-qa-id="field-conditional-logic-operator-field" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_fields_operator]">
                <?php
                $selected = '';
                if ( isset( $settings[$key][$name]['conditional_logic_fields_operator'] ) && $settings[$key][$name]['conditional_logic_fields_operator'] == 'and' ) {
                    $selected = ' selected';
                }
                ?>
                <option value="and" <?php echo $selected; ?>><?php _e( 'All rules match (and)', 'flexible-checkout-fields-pro' ); ?></option>
                <?php
                $selected = '';
                if ( isset($settings[$key][$name]['conditional_logic_fields_operator']) && $settings[$key][$name]['conditional_logic_fields_operator'] == 'or' ) {
                    $selected = ' selected';
                }
                ?>
                <option value="or" <?php echo $selected; ?>><?php _e( 'One or more rules match (or)', 'flexible-checkout-fields-pro' ); ?></option>
            </select>
        </div>
        <div class="rules">
            <?php if ( isset( $settings[$key][$name]['conditional_logic_fields_rules'] ) ) : ?>
                <?php $count = 0; ?>
                <?php foreach ( $settings[$key][$name]['conditional_logic_fields_rules'] as $rule ) : ?>
                    <?php $count++; ?>
                    <div class="rule">
                        <fieldset>
                            <legend><?php printf( __( 'Rule #%s', 'flexible-checkout-fields-pro' ), $count ); ?></legend>
                            <div class="field">
                                <div>
                                    <select data-qa-id="field-conditional-logic-fields-rules-field" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_fields_rules][<?php echo $count; ?>][field]">
	                                    <?php $selected = ''; ?>
	                                    <?php if ( !isset( $rule['field'] ) || $rule['field'] == '' ) $selected = 'selected'; ?>
                                        <option <?php echo $selected; ?> value="" disabled><?php _e( 'Select field', 'flexible-checkout-fields-pro' ); ?></option>
                                        <?php foreach ( $flexible_checkout_fields_all_field_options as $option_key => $option_value ) : ?>
                                            <?php if ( $option_key != $name ) : ?>
                                                <?php $selected = ''; ?>
                                                <?php if ( isset( $rule['field'] ) && $rule['field'] == $option_key ) $selected = 'selected'; ?>
                                                <option value="<?php echo $option_key; ?>" <?php echo $selected; ?> ><?php echo $option_value; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="condition">
                                <div>
                                    <select data-qa-id="field-conditional-logic-fields-rules-condition" class="what" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_fields_rules][<?php echo $count; ?>][condition]">
                                        <?php $selected = 'selected'; ?>
                                        <option value="is" <?php echo $selected; ?>><?php _e( 'is', 'flexible-checkout-fields-pro' ); ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="value">
                                <div>
                                    <select data-qa-id="field-conditional-logic-fields-rules-value" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_fields_rules][<?php echo $count; ?>][value]">
	                                    <?php $selected = 'selected'; ?>
	                                    <?php if ( isset( $rule['field'] ) && isset( $flexible_checkout_fields_all_field_values[$rule['field']] ) ) : ?>
		                                    <?php foreach ( $flexible_checkout_fields_all_field_values[$rule['field']] as $option_key => $option_value ) : ?>
			                                    <?php if ( isset( $rule['value'] ) && $rule['value'] == $option_key ) $selected = ''; ?>
		                                    <?php endforeach; ?>
	                                    <?php endif; ?>
                                        <option value="" <?php echo $selected; ?> disabled><?php _e( 'Select value', 'flexible-checkout-fields-pro' ); ?></option>
                                        <?php if ( isset( $rule['field'] ) && isset( $flexible_checkout_fields_all_field_values[$rule['field']] ) ) : ?>
                                            <?php foreach ( $flexible_checkout_fields_all_field_values[$rule['field']] as $option_key => $option_value ) : ?>
                                                <?php $selected = ''; ?>
                                                <?php if ( isset( $rule['value'] ) && $rule['value'] == $option_key ) $selected = 'selected'; ?>
                                                <option value="<?php echo $option_key; ?>" <?php echo $selected; ?> ><?php echo $option_value; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="delete_rule">
                                <a class="delete_rule_fields" href="#delete_rule"><?php _e( 'Delete rule', 'flexible-checkout-fields-pro' ); ?></a>
                            </div>
                        </fieldset>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php $count++; ?>
            <div class="add_rule_button">
                <a class="button add_rule_fields_button" href="#add_rule" data-count="<?php echo $count; ?>" data-key="<?php echo $key; ?>" data-name="<?php echo $name; ?>"><?php _e( 'Add rule', 'flexible-checkout-fields-pro' ); ?></a>
            </div>
        </div>
    </div>

	<div>
		<?php
			$checked = '';
			if ( isset($settings[$key][$name]['conditional_logic']) && $settings[$key][$name]['conditional_logic'] == '1' ) {
				$checked = ' checked';
			}
		?>
		<label>
    		<input data-qa-id="field-conditional-logic" class="field-conditional-logic" type="checkbox" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic]" value="1" <?php echo $checked; ?>>
    	    <?php _e( 'Enable Product/Category Conditional Logic', 'flexible-checkout-fields-pro' ) ?>
		</label>
	</div>
	<?php
		$style = 'style="display:none;"';
		if ( isset($settings[$key][$name]['conditional_logic']) && $settings[$key][$name]['conditional_logic'] == '1' ) {
			$style = '';
		}
	?>
	<div class="conditional-logic-fields" <?php echo $style; ?>>
	   	<div class="options">
			<select data-qa-id="field-conditional-logic-action" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_action]">
				<?php
					$selected = '';
					if ( isset($settings[$key][$name]['conditional_logic_action']) && $settings[$key][$name]['conditional_logic_action'] == 'show' ) {
						$selected = ' selected';
					}
				?>
	       		<option value="show" <?php echo $selected; ?>><?php _e( 'Show this field if', 'flexible-checkout-fields-pro' ); ?></option>
				<?php
					$selected = '';
					if ( isset($settings[$key][$name]['conditional_logic_action']) && $settings[$key][$name]['conditional_logic_action'] == 'hide' ) {
						$selected = ' selected';
					}
				?>
	       		<option value="hide" <?php echo $selected; ?>><?php _e( 'Hide this field if', 'flexible-checkout-fields-pro' ); ?></option>
			</select>
			<select data-qa-id="field-conditional-logic-operator-product" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_operator]">
				<?php
					$selected = '';
					if ( isset( $settings[$key][$name]['conditional_logic_operator'] ) && $settings[$key][$name]['conditional_logic_operator'] == 'and' ) {
						$selected = ' selected';
					}
				?>
	       		<option value="and" <?php echo $selected; ?>><?php _e( 'All rules match (and)', 'flexible-checkout-fields-pro' ); ?></option>
				<?php
					$selected = '';
					if ( isset($settings[$key][$name]['conditional_logic_operator']) && $settings[$key][$name]['conditional_logic_operator'] == 'or' ) {
						$selected = ' selected';
					}
				?>
	       		<option value="or" <?php echo $selected; ?>><?php _e( 'One or more rules match (or)', 'flexible-checkout-fields-pro' ); ?></option>
			</select>
	    </div>
		<div class="rules">
			<?php if ( isset( $settings[$key][$name]['conditional_logic_rules'] ) ) : ?>
				<?php $count = 0; ?>
				<?php foreach ( $settings[$key][$name]['conditional_logic_rules'] as $rule ) : ?>
					<?php $count++; ?>
					<div class="rule">
                        <fieldset>
                            <legend><?php printf( __( 'Rule #%s', 'flexible-checkout-fields-pro' ), $count ); ?></legend>

                            <div class="condition">
                                <div>
                                	<select data-qa-id="field-conditional-logic-rules-condition" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_rules][<?php echo $count; ?>][condition]">
                                		<?php
                                			$selected = '';
                                			if ( $rule['condition'] == 'cart_contains' ) {
                                				$selected = ' selected';
                                			}
                                		?>
                                   		<option value="cart_contains" <?php echo $selected; ?>><?php _e( 'Cart contains', 'flexible-checkout-fields-pro' ); ?></option>
                                	</select>
                                </div>
                            </div>
                            <div class="what">
                                <div>
                                	<select data-qa-id="field-conditional-logic-rules-what" class="what" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_rules][<?php echo $count; ?>][what]">
                                		<?php
                                			$selected = '';
                                			if ( $rule['what'] == 'product' ) {
                                				$selected = ' selected';
                                			}
                                		?>
                                   		<option value="product" <?php echo $selected; ?>><?php _e( 'Product', 'flexible-checkout-fields-pro' ); ?></option>
                                		<?php
                                			$selected = '';
                                			if ( $rule['what'] == 'product_category' ) {
                                				$selected = ' selected';
                                			}
                                		?>
                                   		<option value="product_category" <?php echo $selected; ?>><?php _e( 'Product Category', 'flexible-checkout-fields-pro' ); ?></option>
                                	</select>
                                </div>
                            </div>
                            <?php
                                $style = ' style="display:none;"';
                                if ( $rule['what'] == 'product' ) {
                                	$style = '';
                                }
                            ?>
                            <div class="products" <?php echo $style; ?>>
                                <div>
                                    <select
                                            class="wc-product-search"
                                            multiple="multiple"
                                            style="width: 100%;"
                                            name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_rules][<?php echo $count; ?>][products][]"
                                            data-sortable="true"
                                            data-placeholder="<?php _e( 'Select products', 'flexible-checkout-fields-pro' ); ?>"
                                            data-action="woocommerce_json_search_products_and_variations"
                                    >
		                                <?php
		                                if ( isset( $rule['products'] ) ) {
		                                    if ( !is_array( $rule['products'] ) ) {
			                                    $product_ids = explode( ',', $rule['products'] );
		                                    }
		                                    else {
			                                    $product_ids = $rule['products'];
                                            }
			                                foreach ( $product_ids as $product_id ) {
				                                $product = wc_get_product( $product_id );
				                                if ( is_object( $product ) ) {
					                                echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
				                                }
			                                }
		                                }
		                                ?>
                                    </select>
                                </div>
                            </div>
                            <?php
                                $style = ' style="display:none;"';
                                if ( $rule['what'] == 'product_category' ) {
                                	$style = '';
                                }
                            ?>
                            <div class="product_categories" <?php echo $style; ?>>
                                <div>
                                   	<input type="hidden" placeholder="<?php _e( 'Select categories', 'flexible-checkout-fields-pro' ); ?>" class="categories-select2" multiple="multiple" name="inspire_checkout_fields[settings][<?php echo $key ?>][<?php echo $name ?>][conditional_logic_rules][<?php echo $count; ?>][product_categories]" value="<?php echo $rule['product_categories']; ?>">
                                </div>
                            </div>
                            <div class="delete_rule">
                                <a class="delete_rule" href="#delete_rule"><?php _e( 'Delete rule', 'flexible-checkout-fields-pro' ); ?></a>
                            </div>
                        </fieldset>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			<?php $count++; ?>
			<div class="add_rule_button">
				<a class="button add_rule_button" href="#add_rule" data-count="<?php echo $count; ?>" data-key="<?php echo $key; ?>" data-name="<?php echo $name; ?>"><?php _e( 'Add rule', 'flexible-checkout-fields-pro' ); ?></a>
			</div>
		</div>

	</div>
</div>
