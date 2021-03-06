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

		$('.slick-sponsors').slick({
			slidesToShow: 3,
			autoplay: false,
			dots: true,
			fade: false,
			autoplaySpeed: config.autoplaySpeed,
			speed: config.speed,
			pauseOnHover: true,
			pauseOnDotsHover: true
		});

		$('.slick-collection').slick({
			slidesToShow: 1,
			autoplay: false,
			dots: false,
			fade: true,
			autoplaySpeed: config.autoplaySpeed,
			speed: config.speed,
			pauseOnHover: true,
			pauseOnDotsHover: true
		});

		$('.slick-classes').slick({
			slidesToShow: 3,
			autoplay: false,
			dots: true,
			fade: false,
			autoplaySpeed: config.autoplaySpeed,
			speed: config.speed,
			pauseOnHover: true,
			pauseOnDotsHover: true,
			responsive: [
			{
				breakpoint: 993,
				settings: {
					slidesToShow: 2
				}
			},
			{
				breakpoint: 480,
				settings: {
					slidesToShow: 1
				}
			}
			]
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
			pauseOnDotsHover: true,
			centerMode: true,
			centerPadding: '295px',
			responsive: [
			{
				breakpoint: 1281,
				settings: {
					centerPadding: '200px'
				}
			},
			{
				breakpoint: 993,
				settings: {
					centerPadding: '100px'
				}
			},
			{
				breakpoint: 769,
				settings: {
					centerPadding: '30px'
				}
			}
			]
		});

	});

}


// function offsetSlickExhibitions( slick ){

// 	var slideTrack = slick.$slideTrack;
// 	var slideTrackTransform = parseInt(slideTrack.css('transform').split(',')[4]);
// 	var offset = 200;
// 	var newTransform = slideTrackTransform - offset;
// 	newTransform = 'translate3d(' + newTransform + 'px, 0, 0)';

// 	slideTrack.css('transform', newTransform);

// }



export { slickSlideshows };