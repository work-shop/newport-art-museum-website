'use strict';

var globalEmail = '';

function members(){
	//console.log("members.js loaded");

	$(document).ready( function() {

		if( $('.member-check-form').length > 0 ){

			$('.member-check-form').submit(function(e) {
				e.preventDefault();
				var email = $(this).children('.member-check-email-input').val();
				checkMemberByEmail( email );
			});

			if( $('.notice').length > 0 ){
				console.log('notices present');
				hasSubscription(true);
			}

		}

		if( $('.woocommerce-ResetPassword.lost_reset_password').length > 0 ){

			var urlEmail = getURLParameter('email');

			if( urlEmail !== 'undefined' && typeof urlEmail !== 'undefined' ){
				$('#user_login').val(urlEmail);
			}

		}

	});

}


function checkMemberByEmail(email){
	var base = '/wp-json/members/v1/status/?email=';
	var endpoint = base + email;
	globalEmail = email;

	$('#username').val(email);

	$('body').addClass('member-checking');
	$('.member-check-form').attr('disabled','true');

	$.ajax({
		url: endpoint
	})
	.done(function( data ) {
		//console.log('success');
		var response = JSON.parse( data );
		parseResponse( response );

		//console.log( response );
	})
	.fail(function( error ) {
		renderMessages('Oops, something went wrong. Please try again.');
		//console.log('error');
	})
	.always(function() {
		//console.log('complete');
		$('body').removeClass('member-checking');
		$('.member-check-form').attr('disabled','false');
	});

}


function parseResponse( response ){
	if( response.has_account ){
		if( response.has_subscription ){
			hasSubscription();
		} else{
			noMembership();
		}
	} else{
		noAccount();
	}
}


function noMembership(){
	clearMessages();
	$('#member-check-heading').html('');
	$('#member-check-heading').html('Try another email address');
	$('.member-check-message-no-membership').addClass('active');	
	//$('.member-check-login').addClass('active');		
	//$('.member-check-intro').hide();
	//$('#lwa_user_remember').val(globalEmail);
}


function hasSubscription(preserveEmail){
	clearMessages();
	$('.member-check-message-has-account').addClass('active');
	$('.member-check-login').addClass('active');		
	$('.member-check-intro').hide();
	if( preserveEmail ===  false ){
		$('#username').val(globalEmail);
	} else{
		globalEmail = $('#username').val();
		var href = $('#lost-password-link').attr('href');
		href = href + '?email=' + globalEmail;
		$('#lost-password-link').attr('href', href);
	}
}


function noAccount(){
	clearMessages();
	$('#member-check-heading').html('');
	$('#member-check-heading').html('Try another email address');
	$('.member-check-message-no-account').addClass('active');		
}


function renderMessages(message){
	var errorContainer = $('.member-check-message-error-container');
	clearMessages();
	errorContainer.append( message );
	errorContainer.addClass('active');
}


function clearMessages(){
	var errorContainer = $('.member-check-message-error-container');
	var messages = $('.member-check-message');
	messages.removeClass('active');
	errorContainer.html('');
}


function getURLParameter(sParam){
	var sPageURL = window.location.search.substring(1);
	var sURLVariables = sPageURL.split('&');
	for (var i = 0; i < sURLVariables.length; i++) {
		var sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] === sParam) {
			return sParameterName[1];
		}
	}
}

export { members };