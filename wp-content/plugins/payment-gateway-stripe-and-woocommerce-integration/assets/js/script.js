jQuery(function() {

	// Set the default dates
	var startDate	= Date.create().addDays(-6),	// 7 days ago
		endDate = Date.create(); 				// today

	var range = jQuery('#range');

	// Show the dates in the range input
	range.val(startDate.format('{MM}/{dd}/{yyyy}') + ' - ' + endDate.format('{MM}/{dd}/{yyyy}'));

	// Load chart
	ajaxLoadChart(startDate,endDate);
	
	range.daterangepicker({
		
		startDate: startDate,
		endDate: endDate,
		
		ranges: {
            'Today': ['today', 'today'],
            'Yesterday': ['yesterday', 'yesterday'],
            'Last 7 Days': [Date.create().addDays(-6), 'today'],
            'Last 30 Days': [Date.create().addDays(-29), 'today']
        }
	},function(start, end){
		
		ajaxLoadChart(start, end);
		
	});
	
	// The tooltip shown over the chart
	var tt = jQuery('<div class="ex-tooltip">').appendTo('body'),
		topOffset = -32;

	var data = {
		"xScale" : "time",
		"yScale" : "linear",
		"main" : [{
			className : ".Stripe",
			"data" : []
		}]
	};

	var opts = {
		paddingLeft : 30,
		paddingTop : 30,
		paddingRight : 0,
                paddingBottom : 30,
		axisPaddingLeft : 25,
		tickHintX: 9, // How many ticks to show horizontally

		dataFormatX : function(x) {
			
			// This turns converts the timestamps coming from
			// ajax.php into a proper JavaScript Date object
			
			return Date.create(x);
		},

		tickFormatX : function(x) {
			
			// Provide formatting for the x-axis tick labels.
			// This uses sugar's format method of the date object. 

			return x.format('{dd} {Mon} {yy}');
		},
		
		"mouseover": function (d, i) {
			var pos = jQuery(this).offset();
			
			tt.text(d.x.format('{Month} {ord}') + ': ' + d.y).css({
				
				top: topOffset + pos.top,
				left: pos.left
				
			}).show();
		},
		
		"mouseout": function (x) {
			tt.hide();
		}
	};

	// Create a new xChart instance, passing the type
	// of chart a data set and the options object
	
	var chart = new xChart('line-dotted', data, '#chart' , opts);
	
	// Function for loading data via AJAX and showing it on the chart
	function ajaxLoadChart(startDate,endDate) {

		// If no data is passed (the chart was cleared)
		
		if(!startDate || !endDate){
			chart.setData({
				"xScale" : "time",
				"yScale" : "linear",
				"main" : [{
					className : ".Stripe",
					data : []
				}]
			});
			
			return;
		}
                jQuery("#analytics .loader").css("display", "block");
		// Otherwise, issue an AJAX request
                jQuery.ajax({
                        type: 'post',
                        url: ajaxurl,
                        data:{
                            action  :   'eh_spg_analytics',
                            start   :   startDate.format('{yyyy}-{MM}-{dd}'),
                            end     :	endDate.format('{yyyy}-{MM}-{dd}')
                        },
                        success: function(data) {
                            console.log(JSON.parse(data));
                            jQuery("#analytics .loader").css("display", "none");
                            var set = [];
                            jQuery.each(JSON.parse(data), function() {
                                    set.push({
                                            x : this.label,
                                            y : this.value,
                                    });
                            });

                            chart.setData({
                                    "xScale" : "time",
                                    "yScale" : "linear",
                                    "main" : [{
                                            className : ".Stripe",
                                            data : set
                                    }]
                            });
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
	}
});
