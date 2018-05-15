'use strict';

var slick = require ('slick-carousel');

function slickSlideshows( config ) {
	//console.log('slick-slideshows.js loaded');

	$( document ).ready( function() {
		
		$('.slick-default').slick({
			slidesToShow: config.slidesToShow,
			dots: config.dots,
			arrows: config.arrows,
			autoplay: false,
			fade: config.fade,
			autoplaySpeed: config.autoplaySpeed,
			speed: config.speed,
			pauseOnHover: true,
			pauseOnDotsHover: true
		});

		$('.slick-exhibitions').slick({
			slidesToShow: 1,
			dots: true,
			arrows: true,
			autoplay: false,
			fade: false,
			autoplaySpeed: config.autoplaySpeed,
			speed: config.speed,
			pauseOnHover: true,
			pauseOnDotsHover: true
		});

	});

}


export { slickSlideshows };