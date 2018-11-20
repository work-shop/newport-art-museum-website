'use strict';

// NOTE: http://localhost:8080/events/faculty-art-sale-sat/

function submitEventForm() {
	//console.log('ecommerce-helpers.js loaded');

    const targetURL = '/cart';

	$(document).ready( function() {

        $('.events_add_to_cart_button').on('click', function( e ) {

            e.preventDefault();

            $('body').addClass('events-submitting');

            var form1 = $('#form-0');
            var form2 = $('#form-1');

            console.log( form1.serialize() );
            console.log( form2.serialize() );

            $.ajax({
                url: form1.attr('action'),
                data: form1.serialize(),
                type: form1.attr('method'),
                success: function( data ){

                    console.log('form1 done.');
                    $.ajax({
                        url: form2.attr('action'),
                        data: form2.serialize(),
                        type: form2.attr('method'),
                        success: function( data ){
                            console.log('form2 done');
                            location.assign('/cart');
                        },
                        error: function( err ) {
                            console.error( 'error form2' );
                            $('body').removeClass('events-submitting');
                            $('body').addClass('events-submission-error');

                        }
                    });

                },
                error: function( err ) {
                    console.error( 'error form1' );
                    $('body').removeClass('events-submitting');
                    $('body').addClass('events-submission-error');
                }
            });

            // $( 'form.cart' ).each( function() {
            //
            //     var form = $( this );
            //     var serialized = form.serialize();
            //
            //     console.log( serialized );
            //     console.log( form.attr('action') );
            //     console.log( form.attr('method') );
            //     console.log();
            //
            //
            //     $.ajax({
            //         url: form.attr('action'),
            //         data: serialized,
            //         type: form.attr('method'),
            //         success: function( data ){
            //             console.log('done');
            //             //location.assign( targetURL );
            //         },
            //         error: function( err ) {
            //             console.error( 'error' );
            //         }
            //     });
            //
            // })

        });


	});

}

export { submitEventForm };
