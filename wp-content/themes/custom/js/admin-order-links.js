'use strict';




function adminOrderLinks() {

    $( document ).ready( function(){

        const rest_uri =
            window.location.protocol +
            '//' +
            window.location.hostname +
            (( window.location.port !== '80' ) ? (':' + window.location.port) : '') +
            '/wp-json/nam/v1/parent/';

        if ( $( document.body ).hasClass('post-type-shop_order') ) {

            const items = $('#order_line_items .item');

            items.each( function() {

                const item = $( this );
                const a = item.find('a');
                const replacement = $('<span>').text( a.text() );

                replacement.prepend( $('<img>').addClass('order-loading-icon').attr('src', '/wp-content/themes/custom/images/loading.gif') );
                a.replaceWith( replacement );

                const href = a.attr('href');
                const product_id = href.match(/.*post=([0-9]+)&/)[1]; // Get the first capturing group.

                $.get( rest_uri + product_id, function( data ) {

                    if ( data.success ) {

                        let new_href = '/wp-admin/post.php?post=' + data.parent_id + '&action=edit';
                        let final_replacement = $('<a>').attr('href', new_href ).text( replacement.text() );
                        replacement.replaceWith( final_replacement );

                        if ( data.warning ) { console.error( data.message ); }

                    } else {

                        console.error( data.message );

                    }

                });

            });

        }
    });
}

export { adminOrderLinks };
