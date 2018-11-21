'use strict';

var consumer_key = 'ck_94391e026732e6950dc9cf4b0a03fbce33664624';
var consumer_secret = 'cs_ffa887c94ac099dd74d2e7685818868aff49114c';

function members(){
	//console.log("members.js loaded");

	$(document).ready( function() {

		$('.member-check-form').submit(function(e) {
			e.preventDefault();
			var email = $(this).children('.member-check-email-input').val();
			checkMemberByEmail( email );
		});

	});

}


function checkMemberByEmail(email){
	var base = '/wp-json/wp/v2/users/?search=';
	var endpoint = base + email;

	$('body').addClass('member-checking');

	$.ajax({
		url: endpoint
	})
	.done(function( data ) {
		console.log('success');
		parseUser( data );
	})
	.fail(function( error ) {
		renderMessages('Oops, something went wrong. Please try again.');
		//console.log('error');
	})
	.always(function() {
		//console.log('complete');
		$('body').removeClass('member-checking');
	});
}


function parseUser( user ){
	if( user.length === 1 ){
		user = user[0];
		clearMessages();
		$('.member-check-message-has-account').addClass('active');		
	} else{
		clearMessages();
		$('.member-check-message-no-account').addClass('active');		
	}
}



function renderMessages(message){
	var errorContainer = $('.member-check-message-error');
	clearMessages();
	errorContainer.append( message );
	errorContainer.addClass('active');
}


function clearMessages(){
	var errorContainer = $('.member-check-message-error');
	var messages = $('.member-check-message');
	messages.removeClass('active');
	errorContainer.html('');
}


export { members };