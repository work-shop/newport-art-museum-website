'use strict';

function ecommerceHelpers() {
	//console.log('accordian.js loaded');

	$(document).ready( function() {

		$( '.notice-close-link' ).click(function() {
			noticeClose($(this));
		});

	});


	function noticeClose(link){
		console.log(link);
	}

}

export { ecommerceHelpers };
