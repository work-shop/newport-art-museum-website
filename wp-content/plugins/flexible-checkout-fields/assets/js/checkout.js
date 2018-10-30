jQuery(document).on("click",".inspire-file-add-button",function() {
	jQuery(this).parent().find('input[type=file]').click();
});

jQuery(document).on("click",".inspire-file-delete-button",function() {
	jQuery(this).parent().find('input[type=file]').val('');
	jQuery(this).parent().find('input[type=text]').val('');
	jQuery(this).parent().find('.inspire-file-info').empty();
	jQuery(this).parent().find('.inspire-file-info').hide();
	jQuery(this).parent().find('.inspire-file-delete-button').hide();
	jQuery(this).parent().find('.inspire-file-add-button').show();	
});

jQuery(document).on("change",".inspire-file-file",function() {
	var id = jQuery(this).parent().attr('id');
	jQuery(this).parent().find('.inspire-file-info').empty();
	jQuery(this).parent().find('.inspire-file-error').empty();
	jQuery(this).parent().find('.inspire-file-error').hide();
	jQuery('#' + id).find('.inspire-file-info').show();
	jQuery('#' + id).find('.inspire-file-info').append( words.uploading );
	jQuery(this).parent().find('input[type=text]').val(jQuery(this).val());
	jQuery(this).parent().find('.inspire-file-add-button').hide();	
	var fd = new FormData();	
    fd.append(jQuery(this).attr('field_name'), jQuery(this).prop('files')[0]);
    fd.append( 'action', 'cf_upload' );
    fd.append( 'inspire_upload_nonce', inspire_upload_nonce );

    jQuery('#place_order').prop('disabled',true);

    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: fd,
        contentType: false,
        processData: false,
        success: function(response){        	
            jQuery('#place_order').prop('disabled',false);
            if ( response != 0 ) {
            	response = JSON.parse(response);     
            	if ( response.status != 'ok' ) {
            		jQuery('#' + id).find('.inspire-file-add-button').show();
	            	jQuery('#' + id).find('.inspire-file-info').empty();
	            	jQuery('#' + id).find('.inspire-file-info').hide();
	            	jQuery('#' + id).find('.inspire-file-error').empty();
	            	jQuery('#' + id).find('.inspire-file-error').show();
	            	jQuery('#' + id).find('.inspire-file-error').append( response.message + '<br/>' );
                    jQuery('#' + id).find('.inspire-file-file').val('');
                    jQuery('#' + id).find('.inspire-file').val('');
            	}
            	else {
            		jQuery('#' + id).find('.inspire-file-delete-button').show();	            	
	            	jQuery('#' + id).find('.inspire-file-error').empty();
	            	jQuery('#' + id).find('.inspire-file-error').hide();
	            	jQuery('#' + id).find('.inspire-file-info').empty();
	            	jQuery('#' + id).find('.inspire-file-info').show();
                    var filename = jQuery('#' + id).find('input[type=file]').val().replace(/.*(\/|\\)/, '');
                    jQuery('#' + id).find('.inspire-file-info').append(filename + '<br/>');
            	}
                if ( jQuery('#' + id).find('.inspire-file').val() == '' ) {
                    jQuery(this).parent().find('.inspire-file-info').empty();
                    jQuery(this).parent().find('.inspire-file-error').empty();
                    jQuery(this).parent().find('.inspire-file-error').hide();
                }
            }
            else {            	
            }
        }
    });
});
