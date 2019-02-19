<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
var fcf_categories = [
<?php foreach ( $flexible_checkout_fields_all_product_categories as $id => $title ) :?>
{ id: <?php echo $id; ?>, text: '<?php echo esc_html( $title ); ?>' }, 
<?php endforeach; ?>
];

var fcf_all_field_options = <?php echo json_encode( $flexible_checkout_fields_all_field_options, JSON_FORCE_OBJECT );  ?>;
var fcf_all_field_values = <?php echo json_encode( $flexible_checkout_fields_all_field_values, JSON_FORCE_OBJECT );  ?>;
var fcf_all_conditions = <?php echo json_encode( $flexible_checkout_fields_all_conditions );  ?>;

jQuery(document).on('change', '.field-conditional-logic-fields', function() {
    if (jQuery(this).is(':checked')) {
        jQuery(this).parent().parent().parent().find('.conditional-logic-fields-fields').each(function() {
            jQuery(this).show();
        });
    }
    else {
        jQuery(this).parent().parent().parent().find('.conditional-logic-fields-fields').each(function() {
            jQuery(this).hide();
        });
    }
});



jQuery(document).on('change', '.field-conditional-logic', function() {
	if (jQuery(this).is(':checked')) {
		jQuery(this).parent().parent().parent().find('.conditional-logic-fields').each(function() {
			jQuery(this).show();
		});
	}
	else {
		jQuery(this).parent().parent().parent().find('.conditional-logic-fields').each(function() {
			jQuery(this).hide();
		});
	}
});

jQuery(document).on('change','select.what',function() {
	if ( jQuery(this).val() == 'product' ) {
		jQuery(this).parent().parent().parent().find('div.products').each(function(){
			jQuery(this).show();
		});
		jQuery(this).parent().parent().parent().find('div.product_categories').each(function(){
			jQuery(this).hide();
		});
	}
	else {
		jQuery(this).parent().parent().parent().find('div.products').each(function(){
			jQuery(this).hide();
		});
		jQuery(this).parent().parent().parent().find('div.product_categories').each(function(){
			jQuery(this).show();
		});
	}
})

function fcf_select2( item, data_type ) {
	jQuery(item).addClass('wc-enhanced-select');
	var fcf_data = [];
	if ( data_type == 'products' ) {
		fcf_data = fcf_products;
	}
	else {
		fcf_data = fcf_categories;
	}
	jQuery(item).select2({
		data		: fcf_data,
/*
        ajax: {
            url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
            dataType: 'json',
            quietMillis: 250,
            data: function (term, page) {
                return {
                    q: term, // search term,
                    action: 'flexible_checkout_fields_ajax',
                };
            },
            results: function (data, page) { // parse the results into the format expected by Select2.
            // since we are using custom formatting functions we do not need to alter the remote JSON data
                return { results: data };
            },
        },
/**/
        minimumInputLength: 3,
        cache: true,
		placeholder: jQuery(item).attr('placeholder'),
		width: '100%',
		multiple: true,		
    });
}


jQuery(document).on('click','a.add_rule_fields_button', function() {
    var data_count = jQuery(this).attr('data-count');
    var data_key = jQuery(this).attr('data-key');
    var data_name = jQuery(this).attr('data-name');
    var clone_div = jQuery('div.flexible_checkout_fields_add_rule_fields').clone();
    clone_div.removeClass('flexible_checkout_fields_add_rule_fields');
    clone_div.show();
    clone_div.removeClass('add_rule');
    clone_div.find('div.product_categories').hide();
    var field_value = false;
    clone_div.find('select,input').each(function(index) {
        var name = jQuery(this).attr('name');
        jQuery(this).attr('name',name.replace('[settings][key][name][conditional_logic_fields_rules][-1]', '[settings][' + data_key + '][' + data_name + '][conditional_logic_fields_rules][' + data_count + ']'));
        jQuery(this).removeAttr('disabled');
        if ( jQuery(this).hasClass('field') ) {
            var select_field = this;
            jQuery(this).empty();
            jQuery(select_field).append( jQuery('<option selected disabled></option>').val('').html('<?php _e( 'Select field', 'flexible-checkout-fields-pro' ); ?>') );
            jQuery.each( fcf_all_field_options, function(val, text) {
                if ( val != data_name ) {
                    jQuery(select_field).append( jQuery('<option></option>').val(val).html(text) );
                    if ( field_value == false ) {
                        field_value = val;
                    }
                }
            });
        }
        if ( jQuery(this).hasClass('value') ) {
            var select_field = this;
            jQuery(select_field).empty();
            //jQuery(select_field).append( jQuery('<option disabled></option>').val('').html('<?php _e( 'Select field first', 'flexible-checkout-fields-pro' ); ?>') );
            jQuery.each( fcf_all_field_values[field_value], function(val, text) {
                jQuery(select_field).append( jQuery('<option></option>').val(val).html(text) );
            });
        }
    });
    jQuery(this).parent().before(clone_div);
    data_count++;
    jQuery(this).attr('data-count',data_count);
    return false;
});


jQuery(document).on('click','a.add_rule_button', function() {
	var data_count = jQuery(this).attr('data-count');
	var data_key = jQuery(this).attr('data-key');
	var data_name = jQuery(this).attr('data-name');
	var clone_div = jQuery('div.flexible_checkout_fields_add_rule').clone();
	clone_div.removeClass('flexible_checkout_fields_add_rule');
	clone_div.show();
	clone_div.removeClass('add_rule');
	clone_div.find('div.product_categories').hide();
	clone_div.find('select,input').each(function(index) {
		var name = jQuery(this).attr('name');
        if ( name != undefined ) {
            jQuery(this).attr('name',name.replace('[settings][key][name][conditional_logic_rules][-1]', '[settings][' + data_key + '][' + data_name + '][conditional_logic_rules][' + data_count + ']'));
		    jQuery(this).removeAttr('disabled');
		    if ( jQuery(this).hasClass('products-new-select2') ) {
			    jQuery(this).removeClass('products-new-select2');
                jQuery(this).addClass('wc-product-search');
                jQuery(this).addClass('wc-enhanced-select');
                var select2_args = {
                    allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
                    placeholder: jQuery( this ).data( 'placeholder' ),
                    sortable: jQuery( this ).data( 'sortable' ),
                    action: jQuery( this ).data( 'action' ),
                    minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
                    escapeMarkup: function( m ) {
                        return m;
                    },
                    ajax: {
                        url:         wc_enhanced_select_params.ajax_url,
                        dataType:    'json',
                        delay:       250,
                        data:        function( params ) {
                            return {
                                term:     params.term,
                                action:   jQuery( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
                                security: wc_enhanced_select_params.search_products_nonce,
                                exclude:  jQuery( this ).data( 'exclude' ),
                                include:  jQuery( this ).data( 'include' ),
                                limit:    jQuery( this ).data( 'limit' )
                            };
                        },
                        processResults: function( data ) {
                            var terms = [];
                            if ( data ) {
                                jQuery.each( data, function( id, text ) {
                                    terms.push( { id: id, text: text } );
                                });
                            }
                            return {
                                results: terms
                            };
                        },
                        cache: true
                    }
                };

                jQuery( this ).selectWoo( select2_args );
            }
		    if ( jQuery(this).hasClass('categories-new-select2') ) {
			    fcf_select2( this, 'categories' );
		    }
        }
	});
	jQuery(this).parent().before(clone_div);
	data_count++;
	jQuery(this).attr('data-count',data_count);
	return false;
});

jQuery(document).ready(function() {
	jQuery('.products-select2').each(function() {
		fcf_select2( this, 'products' );
	});
	jQuery('.categories-select2').each(function() {
		fcf_select2( this, 'categories' );
	});
});

jQuery(document).on('click','a.delete_rule', function() {
	jQuery(this).parent().parent().remove();
	return false;
});

jQuery(document).on('click','a.delete_rule_fields', function() {
    jQuery(this).parent().parent().remove();
    return false;
});

jQuery(document).on('change','div.field select', function() {
    var select_value = jQuery(this).parent().parent().parent().find('div.value').find('select');
    var select_field_value = select_value.val();
    select_value.empty();
    jQuery(select_value).append( jQuery('<option disabled></option>').val('').html('<?php _e( 'Select field first', 'flexible-checkout-fields-pro' ); ?>') );
    if ( jQuery(this).val() != '' ) {
        select_value.empty();
        jQuery(select_value).append( jQuery('<option disabled selected></option>').val('').html('<?php _e( 'Select value', 'flexible-checkout-fields-pro' ); ?>') );
        jQuery.each( fcf_all_field_values[jQuery(this).val()], function(val, text) {
            select_value.append( jQuery('<option></option>').val(val).html(text) )
        });
    }
    select_value.val(select_field_value);
    return false;
});

function fcf_field_values_change( field ) {
    var field_label = jQuery( '#label_' + field ).val();
    var field_type = jQuery( '#field_type_' + field ).val();
    var field_options = jQuery( '#option_' + field ).val();
    if ( fcf_all_field_options[field] != field_label ) {
        fcf_all_field_options[field] = field_label;
    }
    if ( field_type != 'inspirecheckbox' && field_type != 'checkbox' ) {
        var lines = field_options.split("\n");
        var values = {};
        jQuery.each(lines,function(index, value){
            value = value.trim();
            line = value.split(":");
            values[line[0]] = line[1];
        });
        fcf_all_field_values[field] = values;
    }
    jQuery('div.field select').each(function(){
        var field_value = jQuery(this).val();
        jQuery(this).empty();
        var select_field = this;
        jQuery(select_field).append( jQuery('<option disabled></option>').val('').html('<?php _e( 'Select field', 'flexible-checkout-fields-pro' ); ?>') );
        jQuery.each( fcf_all_field_options, function(val, text) {
            jQuery(select_field).append( jQuery('<option></option>').val(val).html(text) );
        });
        jQuery(this).val(field_value);
        jQuery(this).change();
    });
}

jQuery(document).on( 'fcf:pre_remove_field', '.field-item a.remove-field', function() {
    var field_name = jQuery(this).closest('li').find('.field_name').val();
    var field_label = '';
    jQuery('.conditional-logic-fields-fields .field select').each(function(){
        if ( field_name == jQuery(this).val() ) {
            window.fcf_do_remove_field = false;
            if ( field_label != '' ) {
                field_label = field_label + ', ';
            }
            field_label = field_label + jQuery(this).closest('li').find('.field_label').val() + ' [' + fcf_current_section.tab_title + ']';
        }
    });
    jQuery.each(fcf_all_conditions,function(condition_index,condition_value){
        jQuery.each(condition_value,function(section,field_value){
            if ( section != fcf_current_section.section ) {
                jQuery.each(field_value,function(index,value){
                    if ( condition_index == field_name ) {
                        window.fcf_do_remove_field = false;
                        if ( field_label != '' ) {
                            field_label = field_label + ', ';
                        }
                        field_label = field_label + value + ' [' + fcf_all_sections[section]['tab_title'] + ']';
                    }
                });
            }
        });
    });
    if ( window.fcf_do_remove_field == false ) {
        var message = '<?php echo sprintf( __( 'Unable to delete this field. This field is used in conditional logic on field(s): [fields].' ) ); ?>';
        message = message.replace( '[fields]', field_label );
        alert( message );
    }
});

jQuery(document).on( 'fcf:remove_field', '.field-item a.remove-field', function() {
    var data_field = jQuery(this).attr('data-field');
    delete fcf_all_field_options[data_field];
    delete fcf_all_field_values[data_field];
    jQuery('div.field select').each(function(){
        var field_value = jQuery(this).val();
        jQuery(this).empty();
        var select_field = this;
        jQuery(select_field).append( jQuery('<option hidden></option>').val('').html('<?php _e( 'Select field', 'flexible-checkout-fields-pro' ); ?>') );
        jQuery.each( fcf_all_field_options, function(val, text) {
            jQuery(select_field).append( jQuery('<option></option>').val(val).html(text) );
        });
        jQuery(this).val(field_value);
        jQuery(this).change();
    });
    return true;
});

function fcf_add_field( event, field_slug ) {
    var tab = jQuery('.nav-tab-active').html();
    if ( jQuery('#woocommerce_checkout_fields_field_type').val() == 'inspirecheckbox'
        || jQuery('#woocommerce_checkout_fields_field_type').val() == 'checkbox'
    ) {
        fcf_all_field_options[field_slug] = jQuery('#label_' + field_slug ).val() + ' [' + tab + ']';
        fcf_all_field_values[field_slug] = { checked : "<?php _e( 'checked', 'flexible-checkout-fields-pro'); ?>", unchecked : "<?php _e( 'unchecked', 'flexible-checkout-fields-pro'); ?>" };
    }
    if ( jQuery('#woocommerce_checkout_fields_field_type').val() == 'inspireradio' ) {
        fcf_all_field_options[field_slug] = jQuery('#label_' + field_slug ).val() + ' [' + tab + ']';
        fcf_all_field_values[field_slug] = {};
    }
    if ( jQuery('#woocommerce_checkout_fields_field_type').val() == 'select' ) {
        fcf_all_field_options[field_slug] = jQuery('#label_' + field_slug ).val() + ' [' + tab + ']';
        fcf_all_field_values[field_slug] = {};
    }
    if ( jQuery('#woocommerce_checkout_fields_field_type').val() == 'inspirecheckbox'
        || jQuery('#woocommerce_checkout_fields_field_type').val() == 'checkbox'
        || jQuery('#woocommerce_checkout_fields_field_type').val() == 'inspireradio'
        || jQuery('#woocommerce_checkout_fields_field_type').val() == 'select'
    ) {
        jQuery('div.field select').each(function(){
            var field_value = jQuery(this).val();
            jQuery(this).empty();
            var select_field = this;
            jQuery(select_field).append( jQuery('<option hidden></option>').val('').html('<?php _e( 'Select field', 'flexible-checkout-fields-pro' ); ?>') );
            jQuery.each( fcf_all_field_options, function(val, text) {
                jQuery(select_field).append( jQuery('<option></option>').val(val).html(text) );
            });
            jQuery(this).val(field_value);
        });
    }
    return true;
}

jQuery(document).on( 'fcf:add_field', function( event, field_slug ) {
    return fcf_add_field( event, field_slug );
});

jQuery(document).on( 'change', '.fcf_options,.fcf_label', function() {
    var data_field = jQuery(this).attr('data-field');
    fcf_field_values_change( data_field );
});

jQuery(document).ready(function() {
});

</script>

            <div class="flexible_checkout_fields_add_rule_fields" style="display:none;">
                <fieldset>
                    <legend><?php printf( __( 'New Rule', 'flexible-checkout-fields-pro' ), '' ); ?></legend>

                    <div class="field">
                        <div>
                            <select data-qa-id="field-conditional-logic-fields-rules-field" class="field" name="inspire_checkout_fields[settings][key][name][conditional_logic_fields_rules][-1][field]">
                                <option value="" disabled><?php _e( 'Select field', 'flexible-checkout-fields-pro' ); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="condition">
                        <select data-qa-id="field-conditional-logic-fields-rules-condition" name="inspire_checkout_fields[settings][key][name][conditional_logic_fields_rules][-1][condition]">
                            <option value="is"><?php _e( 'is', 'flexible-checkout-fields-pro' ); ?></option>
                        </select>
                    </div>
                    <div class="value">
                        <div>
                            <select data-qa-id="field-conditional-logic-fields-rules-value" class="value" name="inspire_checkout_fields[settings][key][name][conditional_logic_fields_rules][-1][value]">
                                <option value="" disabled selected><?php _e( 'Select value', 'flexible-checkout-fields-pro' ); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="delete_rule">
                        <a class="delete_rule" href="#delete_rule"><?php _e( 'Delete rule', 'flexible-checkout-fields-pro' ); ?></a>
                    </div>
                </fieldset>
            </div>

            <div class="flexible_checkout_fields_add_rule" style="display:none;">
			    <fieldset>
			        <legend><?php printf( __( 'New Rule', 'flexible-checkout-fields-pro' ), '' ); ?></legend>

				    <div class="condition">
				    	<select data-qa-id="field-conditional-logic-rules-condition" name="inspire_checkout_fields[settings][key][name][conditional_logic_rules][-1][condition]">
			           		<option value="cart_contains"><?php _e( 'Cart contains', 'flexible-checkout-fields-pro' ); ?></option>
				    	</select>
				    </div>
				    <div class="what">
				    	<div>
				    		<select data-qa-id="field-conditional-logic-rules-what" class="what" name="inspire_checkout_fields[settings][key][name][conditional_logic_rules][-1][what]">
				           		<option value="product"><?php _e( 'Product', 'flexible-checkout-fields-pro' ); ?></option>
				           		<option value="product_category"><?php _e( 'Product Category', 'flexible-checkout-fields-pro' ); ?></option>
				    		</select>
				    	</div>
				    </div>
				    <div class="products">
				    	<div>
                            <select
                                class="products-new-select2"
                                multiple="multiple"
                                style="width: 100%;"
                                name="inspire_checkout_fields[settings][key][name][conditional_logic_rules][-1][products][]"
                                data-sortable="true"
                                data-placeholder="<?php _e( 'Select products', 'flexible-checkout-fields-pro' ); ?>"
                                data-action="woocommerce_json_search_products_and_variations"
                            ></select>
				    	</div>
				    </div>
				    <div class="product_categories">
				    	<div>
							<input type="hidden" style="width: 100%" placeholder="<?php _e( 'Select categories', 'flexible-checkout-fields-pro' ); ?>" class="categories-new-select2" multiple="multiple" name="inspire_checkout_fields[settings][key][name][conditional_logic_rules][-1][product_categories]" />				    	
				    	</div>
				    </div>

				    <div class="delete_rule">
				    	<a class="delete_rule" href="#delete_rule"><?php _e( 'Delete rule', 'flexible-checkout-fields-pro' ); ?></a>
				    </div>
			    </fieldset>
			</div>

<script>
