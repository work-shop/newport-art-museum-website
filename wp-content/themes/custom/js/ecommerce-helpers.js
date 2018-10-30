'use strict';

function ecommerceHelpers() {
	//console.log('ecommerce-helpers.js loaded');

	$(document).ready( function() {

		$( '.notice-close-link' ).click(function(e) {
			e.preventDefault();
			noticeClose($(this));
		});

		$( '.button-donation-select' ).click(function(e) {
			e.preventDefault();

			if( $( '.button-donation-toggle' ).hasClass('active') ){
				$( '#nyp-fields' ).addClass('hidden');
				$( '#nyp-button' ).addClass('hidden');
				$( '#sidebar-donate-button' ).removeClass('hidden');
			}
			var donationCartUrl = $(this).data('cart-url'); 
			donationButton(donationCartUrl);
			$( '.button-donation-tier' ).removeClass('active');
			$(this).addClass('active');
		});

		$( '.button-donation-toggle' ).click(function(e) {
			e.preventDefault();
			if( $(this).hasClass('active') === false ){
				$( '#nyp-fields' ).removeClass('hidden');
				$( '#nyp-button' ).removeClass('hidden');
				$( '#sidebar-donate-button' ).addClass('hidden');
				$( '.button-donation-tier' ).removeClass('active');
				$(this).addClass('active');
			}
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
