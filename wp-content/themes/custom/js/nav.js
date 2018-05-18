"use strict";

var stickyNavProperties = {
	offset : {},
	triggerPosition : {}
};


function nav( config ) {
	//console.log("nav.js loaded");

	stickyNavProperties.selector = config.selector || '#nav';
	stickyNavProperties.navHeight = config.navHeight || 75;
	stickyNavProperties.mobileNavHeight = config.mobileNavHeight || 50;
	stickyNavProperties.element = $(stickyNavProperties.selector);
	stickyNavProperties.mobileBreakpoint = config.mobileBreakpoint;
	stickyNavProperties.activeOnMobile = config.activeOnMobile;

	$(document).ready( function() {

		navHighlight();

		calculatePositions();
		window.requestAnimationFrame(checkNavPosition);

		$('body').on({ 'touchmove': function(e) { 
			window.requestAnimationFrame(checkNavPosition); } 
		});

		$( window ).scroll( function() {
			window.requestAnimationFrame(checkNavPosition);
		});

		$( window ).resize( function() {
			window.requestAnimationFrame(calculatePositions);
			window.requestAnimationFrame(checkNavPosition);
		});	

		$('#sitewide-alert-close').click(function(e) {
			e.preventDefault();
			$('#sitewide-alert').addClass('hidden');
			$('body').removeClass('sitewide-alert-on');
			var cookie = 'nam_show_sitewide_alert';
			var d = new Date();
			d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
			var expires = 'expires='+d.toUTCString();
			document.cookie = cookie + '=' + 'false' + ';' + expires + ';path=/';
		});

	});

}


function calculatePositions(){

	if( $('#page-nav').hasClass('present') ){
		stickyNavProperties.triggerPosition = $('#page-nav').height();
	} else{
		stickyNavProperties.triggerPosition = stickyNavProperties.element.height();
	}

}


function checkNavPosition(){
	
	if( $(window).width() > stickyNavProperties.mobileBreakpoint || stickyNavProperties.activeOnMobile ){

		if ( $(window).scrollTop() >= stickyNavProperties.triggerPosition && stickyNavProperties.element.hasClass('before') ){
			toggleNav();
		}else if($(window).scrollTop() < stickyNavProperties.triggerPosition && stickyNavProperties.element.hasClass('after') ){
			toggleNav();
		}

	}

}


function toggleNav(){

	if ( stickyNavProperties.element.hasClass('before') ){
		stickyNavProperties.element.removeClass('before').addClass('after');
		$('body').addClass('nav-after');
	}else if( stickyNavProperties.element.hasClass('after') ){
		stickyNavProperties.element.removeClass('after').addClass('before');
		$('body').removeClass('nav-after');			
	}	

}


function navHighlight() {

	var str = window.location.href.split(window.location.host);
	var currentUrl = str[1];
	//console.log('currentUrl: ' + currentUrl);

	var selector = '#page-nav a[href$="' + currentUrl + '"]';
	//console.log('selector: ' + selector);

	var activeLink = $(selector);
	//console.log(activeLink.attr('href'));
	activeLink.addClass('active');

}


export { nav };