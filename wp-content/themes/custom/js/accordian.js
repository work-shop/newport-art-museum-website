'use strict';

function accordian( config ) {
	console.log('accordian.js loaded');

	$(document).ready( function() {
		$('.accordion').accordion({
			'transitionSpeed': 400
		});
	});

}

export { accordian };
