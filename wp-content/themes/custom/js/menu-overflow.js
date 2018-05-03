'use strict';


function menuOverflow() {
	//console.log('menu-overflow.js loaded');
	var elements = $('.menu-links-list');

	$(document).ready( function() {
		checkOverflows();
	});

	var overflowUpdate = debounce(function() {
		window.requestAnimationFrame(checkOverflows);	
	}, 50);		

	window.addEventListener('resize', overflowUpdate);

	function checkOverflows(){
		$('.overflowed').removeClass('overflowed');
		elements.each(function(i) {
			if ( $(this)[0].offsetWidth < $(this)[0].scrollWidth ) {
				//console.log('overflow on menu: ' + i);
				$(this).addClass('overflowed');
			}
		});
	}

	// Returns a function, that, as long as it continues to be invoked, will not be triggered. The function will be called after it stops being called for N milliseconds. If `immediate` is passed, trigger the function on the leading edge, instead of the trailing.
	function debounce(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate){ func.apply(context, args); }
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) { func.apply(context, args); }
		};
	}


}

export { menuOverflow };