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

	$.ajax({
		url: endpoint
	})
	.done(function( data ) {
		console.log('success');
		parseUser( data );
	})
	.fail(function( error ) {
		renderMessages('Oops, something went wrong. Please try again.', true );
		//console.log('error');
	})
	.always(function() {
		//console.log('complete');
	});
}


function parseUser( user ){
	if( user.length === 1 ){
		user = user[0];
		var message = 'You do have an account on this website. Your User id is ' + user.id;
		renderMessages( message );
	} else{
		var message = 'You do not have an account on this website.';
		renderMessages( message );
	}
}



function renderMessages(message, error){
	var errorContainer = $('.member-check-message-error');
	var successContainer = $('.member-check-message-success');
	var messagesContainer = $('member-check-messages');

	clearMessages();

	if( error ){
		errorContainer.append( message );
		errorContainer.addClass('active');
	} else{
		successContainer.append( message );
		successContainer.addClass('active');
	}

	messagesContainer.addClass('active');

	
}


function clearMessages(){
	var messages = $('.member-check-message');
	var messagesContainer = $('member-check-messages');
	messagesContainer.addClass('active');
	messages.removeClass('active');
	messages.html(' ');
}


export { members };