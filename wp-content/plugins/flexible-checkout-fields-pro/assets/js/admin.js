jQuery.noConflict();
(function($) {
	$(function() {

		$('.load-timepicker').timeselector({
		    hours12: false
		});
		$('.load-datepicker').datepicker();
		$('.load-colorpicker').ColorPicker({
		    onChange: function (hsb, hex, rgb) {
				$('.load-colorpicker').val('#' + hex);
			}
		}).bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});

        // Toggle Hooks Visibility for Sections
        $('.woocommerce_page_inspire_checkout_fields_settings a.toggle-hooks').click(function(){
          if ( $('.woocommerce_page_inspire_checkout_fields_settings code.hook').css('visibility') == 'hidden' )
            $('.woocommerce_page_inspire_checkout_fields_settings code.hook').css('visibility','visible');
          else
            $('.woocommerce_page_inspire_checkout_fields_settings code.hook').css('visibility','hidden');
        });


	});
})(jQuery);
