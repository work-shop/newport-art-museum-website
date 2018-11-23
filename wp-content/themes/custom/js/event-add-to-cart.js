'use strict';


function postForm( form, successCallback, errorCallback ) {

    console.log( 'form ' + form.attr('id') + ', qty: ' + form.find('.qty').val() );

    if ( form.length > 0 && form.find('.qty').val() > 0 ) {

        console.log('form ' + form.attr('id') + ' has >0 qty');

        $.ajax({
            url: '/visit?save_notices=true',
            data: form.serialize(),
            type: form.attr('method'),
            success: successCallback,
            error: function( err ) {
                console.error( 'error ' + form.attr('id') );
                $('body').removeClass('events-submitting');
                $('body').addClass('events-submission-error');
            }
        });

    } else {

        console.log('form ' + form.attr('id') + ' has 0 qty');

        successCallback();

    }

}

function submitEventForm() {
	console.log('ecommerce-helpers.js loaded');

    const targetURL = '/cart';

	$(document).ready( function() {

        $('.events_add_to_cart_button').on('click', function( e ) {

            e.preventDefault();

            var form1 = $(this).parent('.sidebar-inner').find('.form-0');
            var form2 = $(this).parent('.sidebar-inner').find('.form-1');

            if ( form1.find('.qty').val() > 0 || form2.find('.qty').val() > 0 ) {

                $('body').addClass('events-submitting');

                postForm( form1,

                   function() { postForm( form2, function() { location.assign( targetURL ); } ); }

                );

            }

        });

	});

}

export { submitEventForm };
