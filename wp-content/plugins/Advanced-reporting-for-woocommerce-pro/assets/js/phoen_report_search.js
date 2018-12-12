jQuery(document).ready(function($){

	//Order summary day week
	
	jQuery('.phoen_sale_order').keyup(function()
	{
		phoen_order_searchTable($(this).val());
	});
	

	function phoen_order_searchTable(inputVal)
	{
		var table = jQuery('#phoen_order_tblData');
		table.find("tr.phoen_order_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	//Sales Order Status
	
	jQuery('.phoen_order_status').keyup(function()
	{
		phoen_order_status_searchTable($(this).val());
	});
	

	function phoen_order_status_searchTable(inputVal)
	{
		var table = jQuery('#phoen_order_status_table');
		table.find("tr.phoen_order_status_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	//Top products
	
	jQuery('.phoen_top_product').keyup(function()
	{
		phoen_products_searchTable($(this).val());
	});
	

	function phoen_products_searchTable(inputVal)
	{
		var table = jQuery('#phoen_top_product_table');
		table.find("tr.phoen_top_product_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	//Top category
	
	jQuery('.phoen_top_category').keyup(function()
	{
		phoen_category_searchTable($(this).val());
	});
	

	function phoen_category_searchTable(inputVal)
	{
		var table = jQuery('#phoen_top_category_table');
		table.find("tr.phoen_top_category_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	

  //customer search  
	
	jQuery('.phoen_search_customet').keyup(function()
	{
		phoen_customer_searchTable($(this).val());
	});
	

	function phoen_customer_searchTable(inputVal)
	{
		var table = jQuery('#phoen_customer_table');
		table.find("tr.phoen_customer_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	//Top Billing Country
      
	jQuery('.phoen_search_country').keyup(function()
	{
		phoen_country_searchTable($(this).val());
	});
	

	function phoen_country_searchTable(inputVal)
	{
		var table = jQuery('#phoen_search_country_table');
		table.find("tr.phoen_search_country_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	
	//Top Billing state
	
	   
	jQuery('.phoen_search_states').keyup(function()
	{
		phoen_state_searchTable($(this).val());
	});
	

	function phoen_state_searchTable(inputVal)
	{
		var table = jQuery('#phoen_search_states_table');
		table.find("tr.phoen_search_states_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	
	//Recent orders
	
	   
	jQuery('.phoen_search_recent_order').keyup(function()
	{
		phoen_recent_orders_searchTable($(this).val());
	});
	

	function phoen_recent_orders_searchTable(inputVal)
	{
		var table = jQuery('#phoen_search_recent_order_table');
		table.find("tr.phoen_search_recent_order_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	//Top coupon
	
	   
	jQuery('.phoen_search_coupan').keyup(function()
	{
		phoen_coupon_searchTable($(this).val());
	});
	

	function phoen_coupon_searchTable(inputVal)
	{
		var table = jQuery('#phoen_search_coupan_table');
		table.find("tr.phoen_search_coupan_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
//Top payment Gateway
	
	   
	jQuery('.phoen_search_payments').keyup(function()
	{
		phoen_payment_searchTable($(this).val());
	});
	

	function phoen_payment_searchTable(inputVal)
	{
		var table = jQuery('#phoen_search_payments_table');
		table.find("tr.phoen_search_payments_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	//year data
	
	jQuery('.phoen_totle_year').keyup(function()
	{
		phoen_year_searchTable($(this).val());
	});
	

	function phoen_year_searchTable(inputVal)
	{
		var table = jQuery('#phoen_year_data_table');
		table.find("tr.phoen_year_data_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	
	//variable instock search 
	
	jQuery('.phoen_product_variation').keyup(function()
	{
		phoen_variable_instock_searchTable($(this).val());
	});
	

	function phoen_variable_instock_searchTable(inputVal)
	{
		var table = jQuery('#phoen_product_attr_table');
		table.find("tr.phoen_attr_data_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	
	
	//variable outstock search 
	
	jQuery('.phoen_product_variation_outstock').keyup(function()
	{
		phoen_variable_outstock_searchTable($(this).val());
	});
	

	function phoen_variable_outstock_searchTable(inputVal)
	{
		var table = jQuery('#phoen_product_attr_outstock_table');
		table.find("tr.phoen_product_attr_outstock_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	
	
	// search simple instock
	
	jQuery('.phoen_product_simple_instock').keyup(function()
	{
		phoen_simple_instock_searchTable($(this).val());
	});
	

	function phoen_simple_instock_searchTable(inputVal)
	{
		var table = jQuery('#phoen_product_simple_instock_table');
		table.find("tr.phoen_product_simple_instock_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	// search simple optstock
	
	jQuery('.phoen_product_simple_outstock').keyup(function()
	{
		phoen_simple_optstock_searchTable($(this).val());
	});
	

	function phoen_simple_optstock_searchTable(inputVal)
	{
		var table = jQuery('#phoen_product_outstock_table');
		table.find("tr.phoen_product_outstock_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	
	//search unsold products
	
	jQuery('.phoen_unsold_pro').keyup(function()
	{
		phoen_unsold_searchTable($(this).val());
	});
	
	function phoen_unsold_searchTable(inputVal)
	{
		var table = jQuery('#phoen_top_unsold_rorduct_table');
		table.find("tr.phoen_top_unsold_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
	//search sold products
	
	jQuery('.phoen_sold_pro').keyup(function()
	{
		phoen_sold_searchTable($(this).val());
	});
	
	function phoen_sold_searchTable(inputVal)
	{
		var table = jQuery('#phoen_top_sold_rorduct_table');
		table.find("tr.phoen_top_sold_tr").each(function(index, row)
		{
			var allCells = jQuery(row).find('td');
			if(allCells.length>=0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test(jQuery(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)jQuery(row).show();else jQuery(row).hide();
			}
		});
	}
	
});	