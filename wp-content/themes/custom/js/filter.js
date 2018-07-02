'use strict';

var Pikaday = require('pikaday');

function filter() {
	//console.log('filter.js loaded');

	var categoryFiltered = false;
	var categoryFilteredCurrent = 'all';
	var dateFiltered = false;
	var dayFiltered = false;
	var pikaStart, pikaEnd;
	var dateOptions = { day: '2-digit', month: '2-digit', year: 'numeric'};

	$(document).ready( function() {

		var filterClassStart = 'all';
		var urlVars = getUrlVars();
		var urlCategory = urlVars.category;
		if( !isEmpty(urlCategory) ){
			var categoryButtonSelector = '.filter-button[data-target=' + urlCategory + ']';
			var categoryButtonCheck = $(categoryButtonSelector);
			if( !isEmpty(categoryButtonCheck) ){
				$(categoryButtonSelector).addClass('filter-active');
				filterClassStart = 'filter-' + urlVars.category;
			}
		}
		filterCategories(filterClassStart);

		pikaStart = new Pikaday({ 
			field: $('.filter-date-start')[0],
			format: 'YYYY-MM-DD',
			onSelect: function() {
				console.log(this._d);
			}
		});
		pikaEnd = new Pikaday({ 
			field: $('.filter-date-end')[0],
			onSelect: function() {
				console.log(this._d);
			}
		});

		$('.filter-button-category').click(function(e) {
			e.preventDefault();
			console.log('filter-button-category');
			if( $(this).hasClass('filter-active') ){
				categoryFiltered = false;
				categoryFilteredCurrent = 'all';
				filterCategories('all');
				$(this).removeClass('filter-active');
			} else{
				scrollToFilter();
				var filterClass = $(this).data('target');
				filterClass = 'filter-'+filterClass;
				filterCategories(filterClass);
				filterButtonActivate( $(this), 'categories' );
			}
		});	

		$('.filter-button-day').click(function(e) {
			console.log('filter-button-day');
			e.preventDefault();
			if( $(this).hasClass('filter-active') ){
				dayFiltered = false;
				filterDays('all');
				$(this).removeClass('filter-active');
			} else{
				dayFiltered = true;
				scrollToFilter();
				var day = $(this).data('target');
				day = 'filter-'+ day;
				filterDays(day);
				filterButtonActivate( $(this), 'days' );
			}
		});

		$('.filter-date-input').change( function(){
			filterDates();
		});	

	});


	function filterCategories(filterClass) {
		clearFilterMessages();
		categoryFiltered = true;
		categoryFilteredCurrent = filterClass;
		var elements = $('.filter-target');
		var newElements = getElementsByCategory( elements, filterClass );
		updateElements(newElements);
		setTimeout(function() {
			if(dateFiltered){
				filterDates(); 
			}
			if(dayFiltered){
				filterDays();
			}
		}, 10);
	}


	function filterDays(day){
		clearFilterMessages();
		var elements = $('.filter-target');
		if( categoryFiltered ){
			elements = getElementsByCategory( elements, categoryFilteredCurrent );
		}
		var newElements = getElementsByCategory( elements, day );
		updateElements( newElements );
	}


	function filterDates(){
		var startDate = pikaStart._d;
		var endDate = pikaEnd._d;
		if( isEmpty(endDate) || isEmpty(startDate) ){
			return null;
		} else{
			dateFiltered = true;
		}
		startDate = startDate.toLocaleDateString("en-US", dateOptions);
		endDate = endDate.toLocaleDateString("en-US", dateOptions);

		clearFilterMessages();

		var elements = $('.filter-target');
		if( categoryFiltered ){
			elements = getElementsByCategory( elements, categoryFilteredCurrent );
		}

		var newElements = getElementsByDate( elements, startDate, endDate );
		updateElements( newElements );
	}


	function getElementsByCategory( elements, filterClass ){
		var newElements = [];

		$.each(elements, function(index, val) {
			var element = $(val);
			if( element.hasClass(filterClass) || filterClass === 'all' ){
				newElements.push(element);
			}
		});

		return newElements;
	}


	function getElementsByDate( elements, startDate, endDate ){
		var newElements = [];

		$.each(elements, function(index, val) {
			var element = $(val);
			var date = element.data('date');
			console.log('if ' + date + ' >= ' + startDate + ' && ' + date + ' <= ' + endDate);
			if( date >= startDate && date <= endDate ){
				newElements.push(element);
			} else{
			}
		});

		return newElements;
	}


	function updateElements(newElements){
		var elementsFound = false;
		hideElements();

		$.each(newElements, function(index, val) {
			var element = $(val);
			element.addClass('filter-show');
			elementsFound = true;
		});

		if( !elementsFound ){
			console.log('no elements found');
			$('#filter-messages').addClass('filter-show');
		}
	}

	function hideElements(){
		var elements = $('.filter-target');
		elements.removeClass('filter-show');
	}


	function clearFilterMessages(){
		$('#filter-messages').removeClass('filter-show');
	}


	function filterButtonActivate(button, context){

		if( context === 'days' ){
			$('.filter-days .filter-active').removeClass('filter-active');
		} else if ( context === 'categories' ){
			$('.filter-categories .filter-active').removeClass('filter-active');
		}

		
		button.addClass('filter-active');		
	}


	function scrollToFilter(){
		var offset = 105;
		$('html,body').animate({
			scrollTop: $('.filters').offset().top - offset
		}, 100);
	}


	function isEmpty(val){
		return (val === undefined || val == null || val.length <= 0) ? true : false;
	}

	// Read a page's GET URL variables and return them as an associative array.
	function getUrlVars(){
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for(var i = 0; i < hashes.length; i++)
		{
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}


}


export { filter };
