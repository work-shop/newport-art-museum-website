jQuery( document ).on( 'click', '.notice-dismiss', function() {
    var notice_name = jQuery(this).closest('div.notice').data('notice-name');
    if ('' !== notice_name) {
        jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                action: 'wpdesk_notice_dismiss',
                notice_name: notice_name
            },
            success: function (response) {
            }
        });
    }
});

