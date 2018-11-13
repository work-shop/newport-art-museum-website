'use strict';

function museumStatus() {
	//console.log('collection.js loaded');

	$(document).ready( function() {

		var endpoint = '/wp-json/museum-status/v1/status';

		$.ajax({
			url: endpoint
		})
		.done(function( data ) {
			console.log('success');
			console.log(data);
			renderStatus( data );
		})
		.fail(function(error ) {
			//console.error(error);
			console.log('error on museum status');
		})
		.always(function() {
			console.log('complete');
		});

		
	});


	function renderStatus( data ){

		var statusElements = $('.museum-status');

		$.each(statusElements, function(index, val) {
			$(this).append(data);	 
		});

	}	


}

export { museumStatus };
