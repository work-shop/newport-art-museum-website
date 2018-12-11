jQuery(document).ready(function(){
    function fcf_fields_conditions() {
        jQuery.each( fcf_conditions, function(index,value){
            var match = false;
            if ( value['conditional_logic_fields_operator'] == 'and' ) {
                match = true;
            }
            jQuery.each( value['conditional_logic_fields_rules'], function(index_rule,value_rule){
                var field_value = '';
                if ( jQuery('input[name=' + value_rule['field'] +']').attr('type') == 'radio' ) {
                    field_value = jQuery('input[name=' + value_rule['field'] + ']:checked').val();
                }
                else if ( jQuery('input[name=' + value_rule['field'] +']').attr('type') == 'checkbox' ) {
                    field_value = 'unchecked';
                    if ( jQuery('input[name=' + value_rule['field'] + ']').is(':checked')) {
                        field_value = 'checked';
                    }
                }
                else {
                    field_value = jQuery('input[name=' + value_rule['field'] + ']').val();
                    if ( typeof field_value == 'undefined' ) {
                        field_value = jQuery('select[name=' + value_rule['field'] + ']').val();
                    }
                }
                if ( value_rule['condition'] == 'is' ) {
                    if ( field_value == value_rule['value'] ) {
                        if ( value['conditional_logic_fields_operator'] == 'and' ) {
                            match = match && true;
                        }
                        else {
                            match = match || true;
                        }
                    }
                    else {
                        if ( value['conditional_logic_fields_operator'] == 'and' ) {
                            match = match && false;
                        }
                        else {
                            match = match || false;
                        }
                    }
                }
            });
            var hidden = true;
            if ( value['conditional_logic_fields_action'] == 'hide' ) {
                if ( match ) {
                    hidden = true;
                }
                else {
                    hidden = false;
                }
            }
            if ( value['conditional_logic_fields_action'] == 'show' ) {
                if ( match ) {
                    hidden = false;
                }
                else {
                    hidden = true;
                }
            }
            if ( hidden ) {
                jQuery('#' + index + '_field').hide();
                jQuery('#' + index +'_field').removeClass('validate-required');
                jQuery('#' + index +'_field').find('input,select').attr('disabled',true);
                jQuery('#' + index +'_field').addClass('fcf-hidden');
            }
            else {
                jQuery('#' + index + '_field').show();
                jQuery('#' + index +'_field').find('input,select').attr('disabled',false);
                jQuery('#' + index +'_field').removeClass('fcf-hidden');
            }
        });
    }

    jQuery(document).on( 'change', 'input', function () {
        fcf_fields_conditions();
    });
    jQuery(document).on( 'change', 'select', function () {
        fcf_fields_conditions();
    });
    jQuery( 'body' ).on( 'updated_checkout', function () {
        fcf_fields_conditions();
    });

    function fcf_select2_fields() {
        if (jQuery.fn.selectWoo) {
            jQuery('p.select2 select').selectWoo();
        } else {
            if (jQuery.fn.select2) {
                jQuery('p.select2 select').select2();
            }
        }
    }

    fcf_select2_fields();
    fcf_fields_conditions();
})
