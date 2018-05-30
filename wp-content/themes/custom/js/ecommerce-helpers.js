'use strict';

function ecommerceHelpers() {
	//console.log('accordian.js loaded');

	$(document).ready( function() {

		$( '.notice-close-link' ).click(function(e) {
			e.preventDefault();
			noticeClose($(this));
		});

	});


	function noticeClose(link){
		link.closest('.notice').addClass('hidden');
	}

}

export { ecommerceHelpers };
