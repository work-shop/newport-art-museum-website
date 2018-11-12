'use strict';

function viewportLabel( config ) {
	//console.log('viewport-label.js loaded');

    var timeout = false, // holder for timeout id
    showViewportLabel = false,
    delay = 50, // delay after event is "complete" to run callback
    w = 0,
    viewportLabelPx = $(config.viewportLabelPxSelector);


    $( document ).ready( function() {

    	if( $('#viewport-label').length > 0 && ( window.location.href.indexOf('localhost') !== -1 || $('#viewport-label').hasClass('override') ) ){
    		showViewportLabel = true;
    		viewportLabelUpdate();
    		window.addEventListener('resize', function() {
    			clearTimeout(timeout);
    			timeout = setTimeout(viewportLabelUpdate, delay);
    		});
    	}

    });


	//update the viewport label
	function viewportLabelUpdate(){
		if(showViewportLabel){
			w = window.innerWidth;
			viewportLabelPx.text(w);
		}
	}


}

export { viewportLabel };
