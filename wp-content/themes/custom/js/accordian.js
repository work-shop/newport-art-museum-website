'use strict';

function accordian() {
	//console.log('accordian.js loaded');

	$(document).ready( function() {
		$('.accordion').accordion({
			'transitionSpeed': 400
		});
	});

}

export { accordian };
