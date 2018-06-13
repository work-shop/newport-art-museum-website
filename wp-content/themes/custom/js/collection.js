'use strict';

function collection() {
	//console.log('collection.js loaded');

	$(document).ready( function() {
		$('.collection-gallery-more').click(function(e) {
			e.preventDefault();
			collectionToggle($(this));
		});				
		
	});

	//open and close the menu
	function collectionToggle(link){

		var target = link.data('target');
		//console.log(target);

		if( $(target).hasClass('closed') ){
			$(target).removeClass('closed').addClass('open');
		}
		else if( $(target).hasClass('open') ){
			$(target).removeClass('open').addClass('closed');
		}

	}	


}

export { collection };
