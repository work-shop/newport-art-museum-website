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

		var end = new Date();
		end.setHours(23,59,59,999);
		//console.log(end);

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
			var cookie = 'nam_show_sitewide_alert_2';
			var d = new Date();
			d.setHours(23,59,59,999);
			var expires = 'expires='+d.toUTCString();
			document.cookie = cookie + '=' + 'false' + ';' + expires + ';path=/';
		});

		$('.has-sub-menu>a').click(function(e) {
			if( $(window).width() < 768){
				e.preventDefault();
				toggleSubMenu($(this));
			}
		});
		// $('.has-sub-menu>a').click(function(e) {
		// 	e.preventDefault();
		// 	toggleSubMenu($(this));
		// });

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

function toggleSubMenu(link){
	//console.log(item);

	var item = link.parent('.has-sub-menu');

	if ( item.hasClass('sub-menu-closed') ){
		item.removeClass('sub-menu-closed').addClass('sub-menu-open');
	}else if( item.hasClass('sub-menu-open') ){
		item.removeClass('sub-menu-open').addClass('sub-menu-closed');		
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