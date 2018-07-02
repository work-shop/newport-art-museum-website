'use strict';

function ecommerceHelpers() {
	//console.log('ecommerce-helpers.js loaded');

	$(document).ready( function() {

		$( '.notice-close-link' ).click(function(e) {
			e.preventDefault();
			noticeClose($(this));
		});

		$( '.button-donation-tier' ).click(function(e) {
			e.preventDefault();
			var donationCartUrl = $(this).data('cart-url'); 
			donationButton(donationCartUrl);
			$( '.button-donation-tier' ).removeClass('active');
			$(this).addClass('active');
		});

	});


	function noticeClose(link){
		link.closest('.notice').addClass('hidden');
	}


	function donationButton(donationCartUrl){
		$('#sidebar-donate-button').attr('href',donationCartUrl);
	}

}

export { ecommerceHelpers };
