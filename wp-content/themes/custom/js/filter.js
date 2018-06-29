'use strict';

var Pikaday = require('pikaday');

function filter() {
	//console.log('filter.js loaded');

	var categoryFiltered = false;
	var categoryFilteredCurrent = 'all';
	var dateFiltered = false;
	var pikaStart, pikaEnd;
	var dateOptions = { day: '2-digit', month: '2-digit', year: 'numeric'};

	$(document).ready( function() {
		filterCategories('all');

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

		$('.filter-button').click(function(e) {
			e.preventDefault();
			if( $(this).hasClass('filter-active') ){
				categoryFiltered = false;
				categoryFilteredCurrent = 'all';
				filterCategories('all');
				$(this).removeClass('filter-active');
			} else{
				categoryFiltered = true;
				scrollToFilter();
				var filterClass = $(this).data('target');
				filterClass = 'filter-'+filterClass;
				categoryFilteredCurrent = filterClass;
				filterCategories(filterClass);
				filterButtonActivate( $(this) );
			}
		});	

		$('.filter-date-input').change( function(){
			filterDates();
		});	

	});


	function filterCategories(filterClass) {
		clearFilterMessages();
		var elements = $('.filter-target');
		var newElements = getElementsByCategory( elements, filterClass );
		updateElements(newElements);
		if(dateFiltered){
			filterDates();
		}
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


	function filterDates(){
		// var startDate = $('.filter-date-start').val();
		// var endDate = $('.filter-date-end').val();
		// if( isEmpty(endDate) || isEmpty(startDate) ){
		// 	return null;
		// } else{
		// 	dateFiltered = true;
		// }

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


	function filterButtonActivate(button){
		$('.filter-active').removeClass('filter-active');
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


}


export { filter };
