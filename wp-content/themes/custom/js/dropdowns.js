'use strict';

function dropdowns( config ) {
	//console.log('dropdowns.js loaded');

	$(document).ready( function() {

		var dropdownDelay = 250, timer;

		$( config.linkSelector ).hover(
			function() {
				if( window.innerWidth > 767){
					var currentLink = $(this);
					timer = setTimeout(function() {
						openDropdown( currentLink );
					}, dropdownDelay);
				}
			}, function() {
				if( window.innerWidth > 767){
					clearTimeout(timer);
				}
			}
			);

		// $(config.blanketSelector).click(function(){
		// 	closeDropdown();
		// });

		$( config.blanketSelector ).hover(
			function() {
				if( window.innerWidth > 767){
					closeDropdown();
				}
			}, function() {
				if( window.innerWidth > 767){
					
				}
			}
			);

		$('.dropdown-link').click(function(){
			closeDropdown();
			//$('body').removeClass('loaded').addClass('loading');
		});

	});

	//open the dropdown
	function openDropdown( link ){
		var linkTarget = link.data('dropdown-target');
		var dropdownTarget = 'menu[data-dropdown="' + linkTarget + '"]';
		var dropdown = $(dropdownTarget);
		closeDropdown();

		if($('body').hasClass(config.bodyOffClass)){
			$(dropdown).removeClass('off').addClass('on');
			$(link).removeClass('off').addClass('on');
			$(config.blanketSelector).removeClass('off').addClass('on');						
			$('body').removeClass(config.bodyOffClass).addClass(config.bodyOnClass);
		}

	}	

	//close the dropdown
	function closeDropdown(){
		if($('body').hasClass(config.bodyOnClass)){
			$( config.linkSelector ).removeClass('on').addClass('off');
			$(config.dropdownSelector).removeClass('on').addClass('off');
			$(config.blanketSelector).removeClass('on').addClass('off');			
			$('body').removeClass(config.bodyOnClass).addClass(config.bodyOffClass);
		}
	}

}

export { dropdowns };
