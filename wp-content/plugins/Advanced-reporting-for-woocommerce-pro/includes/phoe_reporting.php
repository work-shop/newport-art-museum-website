<?php

wp_enqueue_style( 'style-advanced-reportingss',plugin_dir_url(__FILE__).'./../assets/font-awesome/css/font-awesome.min.css' );

global $wpdb,$woocommerce,$product;

// Total Registered user	

	$phoen_user= get_users(); 
	 
	$total_registed = count($phoen_user);
	
//Top Products Data
$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_product_query = "
		SELECT  
			posts.post_title AS product_name,
			meta.meta_key as mkey, 
			meta.meta_value as product_value, 
			posts.ID AS ID
			FROM  {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS meta 
				ON posts.ID = meta.post_id 
		WHERE 
			posts.post_status IN ( 'publish','private' ) 
			AND posts.post_type IN ( 'product' ) 
			AND meta.meta_key IN ( 'total_sales' ,'_price' ,'post_views_count') 
		ORDER BY
			posts.ID ASC, 
			meta.meta_key ASC
	";
	$phoen_product_data = $wpdb->get_results(  $phoen_product_query,ARRAY_A);
	
	foreach($phoen_product_data as $key1 => $valuee){
		
		if(!isset( $phoen_top_products[$valuee['ID']])){
			
			$phoen_top_products[$valuee['ID']] = Array();
			
			$phoen_top_products[$valuee['ID']] = Array(
			
				"produc_total" => 0,
				
				"product_price" => 0,
				
				"product_count" => 0,
				
				"product_views" => 0
				
			);
		
		}
		
		switch ($valuee['mkey']) {
			
			case "_price":
				
				$phoen_top_products[$valuee['ID']]["product_price"] = $valuee['product_value'];
				
				break;
				
			case "post_views_count":
			
				$phoen_top_products[$valuee['ID']]["product_views"] = $valuee['product_value'];
				
				break;
				
			case "total_sales":
			
				$phoen_top_products[$valuee['ID']]["product_count"] = $valuee['product_value'];
				
				$phoen_top_products[$valuee['ID']]["produc_total"] = $valuee['product_value'] * $phoen_top_products[$valuee['ID']]["product_price"];
				
				$phoen_top_products[$valuee['ID']]["product_name"] = $valuee['product_name'];
				
				$phoen_top_products[$valuee['ID']]["ID"] = $valuee['ID'];
				
				break;
				
			default:
			
				break;
				
		}

	}

	function phoen_products_short($a, $b) {
		return $a['product_count'] < $b['product_count'];
	}
	
	usort($phoen_top_products, "phoen_products_short");


	 
	$phoen_totle_sale_products='0';

	for($i=0; $i<count($phoen_product_data); $i++)
	{
		if($phoen_product_data[$i]['mkey']=='total_sales')
		{
			
			$phoen_totle_sale_products+=$phoen_product_data[$i]['product_value'];
			
		}
		
	}
	
//Category Data
$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_category_query = "
		SELECT  
			posts.post_title AS product_name,
			meta.meta_value * pmeta.meta_value as total_amount, 
			pmeta.meta_value as totle_products, 
			meta.meta_value as total_sale_count, 
			terms.name as category_name, 
			terms.term_id as category_id, 
			posts.ID AS product_ID
			FROM  {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id 
			LEFT JOIN {$wpdb->postmeta} AS pmeta ON(posts.ID = pmeta.post_id)
			LEFT JOIN {$wpdb->term_relationships} AS rel ON(posts.ID = rel.object_id)
			LEFT JOIN {$wpdb->term_taxonomy} AS taxo ON(rel.term_taxonomy_id = taxo.term_taxonomy_id)
			LEFT JOIN {$wpdb->terms} AS terms ON(taxo.term_id = terms.term_id)
		WHERE 
			posts.post_status IN ( 'publish','private' ) 
			AND posts.post_type IN ( 'product' ) 
			AND meta.meta_key IN ( 'total_sales' ) 
			AND pmeta.meta_key IN ( '_price') 
			AND taxo.taxonomy IN ( 'product_cat' ) 
		ORDER BY
			posts.ID ASC
	";
	$phoen_cat_data = $wpdb->get_results(  $phoen_category_query,ARRAY_A);
	
	$phoen_all_cat_data=array();
	
	if(isset($phoen_cat_data) && is_array($phoen_cat_data)){
		
		foreach($phoen_cat_data as $kes => $phoen_category_values)
		{
			if(!isset( $phoen_all_cat_data[$phoen_category_values['category_id']])){

				$phoen_all_cat_data[$phoen_category_values['category_id']] = Array(
				
				"category_names" => $phoen_category_values['category_name'],
				
				"total_sale_counts" => 0,
				
				"total_amount" => 0
				
				);
				
			}
			
			$phoen_all_cat_data[$phoen_category_values['category_id']]["total_sale_counts"] += $phoen_category_values['total_sale_count'];
			
			$phoen_all_cat_data[$phoen_category_values['category_id']]["total_amount"] += $phoen_category_values['total_sale_count'] * $phoen_category_values['totle_products'];
		
		}
		
	}
	
	
	function phoen_category_shorts($a, $b) {
			
		return $a['total_sale_counts'] < $b['total_sale_counts'];
		
	}
	
	usort($phoen_all_cat_data, "phoen_category_shorts");  
/********************End of Category Data *******************************/


/********************Top Coupan Data**********************************/ 
$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_coupan_query = "
		SELECT  
			posts.post_title AS coupan_name, 
			posts.ID AS coupan_id, 
			posts.post_date as order_date,
			meta.meta_value AS coupan_amount, 
			cmeta.meta_value AS coupan_count
			FROM  {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS meta 
				ON posts.ID = meta.post_id 
			LEFT JOIN {$wpdb->postmeta} AS cmeta 
				ON posts.ID = cmeta.post_id 
		WHERE 
			meta.meta_key = 'coupon_amount'
			AND cmeta.meta_key = 'usage_count'
			AND posts.post_type = 'shop_coupon'
			AND posts.post_status IN ( 'publish' )
				
	";
	$phoen_coupon_data = $wpdb->get_results(  $phoen_coupan_query,ARRAY_A);
	
	function phoen_coupan_short($a, $b) {
		return $a['coupan_count'] < $b['coupan_count'];
	}
		
	usort($phoen_coupon_data, "phoen_coupan_short"); 

/*****************************End of top coupan ***********************/

/*************************** Top Billing Country***********************/ 


	$phoen_shop_order_date_data = get_posts( array(

		'posts_per_page' => 400,

		'post_type'      => 'shop_order',

		'order' => 'DESC',

		'post_status' => array_keys( wc_get_order_statuses() ),

	) );


$phoen_order_dates=array();

foreach($phoen_shop_order_date_data as $ke=> $phoe_order_date)
{
	
	$phoen_order_date = $phoe_order_date->post_date;
	
	$phoen_order_dates[]= $phoe_order_date->post_date;
	
}

//current date 

$phoen_last_order_dates = current($phoen_order_dates);

$date_country_date_from =isset($_POST['date_country_froms'])?$_POST['date_country_froms']:'';

$date_country_date_to =isset($_POST['date_country_tos'])?$_POST['date_country_tos']:''; 


if(($date_country_date_from =='') ||($date_country_date_to==''))
{
	
	$date_country_from = date("Y-m-d", strtotime($phoen_order_date));
	
	$phoen_reports_recent_current = date("Y-m-d"); 
	
	$date_country_to = date("Y-m-d", strtotime($phoen_reports_recent_current));
	
	$years_months = date('Y-m',strtotime($phoen_reports_recent_current));
	
	$phoen_day = date('d',strtotime($phoen_reports_recent_current));
	
	
	if($phoen_day =='31')
	{
		$phoen_tow_day_country = $years_months."-".($phoen_day);
	
	}else{
		
		$phoen_tow_day_country = $years_months."-".($phoen_day+1);
	}
	
}else{
	
	$date_country_from = date("Y-m-d", strtotime($date_country_date_from));

	$date_country_to = date("Y-m-d", strtotime($date_country_date_to));
	
	$date_year_to = date("Y", strtotime($date_country_to));
	
	$date_month_to = date("m", strtotime($date_country_to));
	
	$date_day_to = date("d", strtotime($date_country_to));
	
	if($date_day_to =='31')
	{
		$phoen_tow_day_country = $date_year_to."-".$date_month_to."-".($date_day_to);
		
	}else{
		
		$phoen_tow_day_country = $date_year_to."-".$date_month_to."-".($date_day_to+1);
	}

}

$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_billing_country_querys = "
		SELECT 
			first_name_meta.meta_value as first_name,
			last_name_meta.meta_value as last_name,
			posts.post_status as ordr_status,  
			billing_amount_meta.meta_value as billing_amount,  
			billing_state_meta.meta_value as billing_state,  
			pay_method_meta.meta_value as pay_method,  
			payment_method_title_meta.meta_value as payment_method_title,  
			billing_email_meta.meta_value as billing_email,
			billing_country_meta.meta_value as billing_country, 
			posts.post_date as order_date,			
			posts.ID AS ID
			
			FROM    {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS first_name_meta ON(posts.ID = first_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS last_name_meta ON(posts.ID = last_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_amount_meta ON(posts.ID = billing_amount_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_country_meta ON(posts.ID = billing_country_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_state_meta ON(posts.ID = billing_state_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS payment_method_title_meta ON(posts.ID = payment_method_title_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS pay_method_meta ON(posts.ID = pay_method_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_email_meta ON(posts.ID = billing_email_meta.post_id)
		WHERE 
			posts.post_status LIKE 'wc-%' 
			AND posts.post_type IN ( 'shop_order' ) 
			AND billing_amount_meta.meta_key IN ( '_order_total')
			AND billing_country_meta.meta_key IN ( '_billing_country')
			AND billing_state_meta.meta_key IN ( '_billing_state')
			AND pay_method_meta.meta_key IN ( '_payment_method')
			AND payment_method_title_meta.meta_key IN ( '_payment_method_title')
			AND last_name_meta.meta_key IN ( '_billing_last_name')
			AND first_name_meta.meta_key IN ( '_billing_first_name')
			AND billing_email_meta.meta_key IN ( '_billing_email')
			AND posts.post_date >= '$date_country_from' and posts.post_date <= '$phoen_tow_day_country'
			
		ORDER BY
			posts.ID ASC
	";
	
	$phoen_Billing_country_data = $wpdb->get_results(  $phoen_billing_country_querys,ARRAY_A);
	
	$phoen_billings_countrys=array();
	
	$phoen_billings_states=array();
	
	$phoen_payment_method=array();
	
	$phoen_order_status=array();
	
	$phoen_top_customer=array();
	
	
	
	foreach($phoen_Billing_country_data as $ky=>$billing_data)
	{
		
		
		if($billing_data['ordr_status'] != 'wc-refunded')
		{
			if($billing_data['ordr_status'] != 'wc-cancelled')
			{
		
				if(!isset( $phoen_billings_countrys[$billing_data['billing_country']])){
				
					$phoen_billings_countrys[$billing_data['billing_country']] = Array(
					
						"country_name" =>$billing_data['billing_country'],
						
						"totle_order_counts" => 0,
						
						"total_amount" =>0
				
					);
				}	
			
				$phoen_billings_countrys[$billing_data['billing_country']]["totle_order_counts"] += 1;
				$phoen_billings_countrys[$billing_data['billing_country']]["total_amount"] += $billing_data['billing_amount'];
			}	
		}
	
	}
	
	function phoen_country_short($c, $d) {
		return $c['totle_order_counts'] < $d['totle_order_counts'];
	}
	
	usort($phoen_billings_countrys, "phoen_country_short"); 
	 


/************************end billing country *********************************/


/*************************Top Billing state ***********************************/
	

$date_state_date_from =isset($_POST['date_state_from'])?$_POST['date_state_from']:'';

$date_state_date_to =isset($_POST['date_state_to'])?$_POST['date_state_to']:'';

if(($date_state_date_from =='') ||($date_state_date_to=='') )
{
	
	$date_state_from = date("Y-m-d", strtotime($phoen_order_date));
	
	$phoen_reports_recent_current = date("Y-m-d"); 
	
	$date_state_to = date("Y-m-d", strtotime($phoen_reports_recent_current));
	
	$year_months = date('Y-m',strtotime($phoen_reports_recent_current));
	
	$phoen_dayy = date('d',strtotime($phoen_reports_recent_current));
	
	if($phoen_dayy =='31')
	{
		$phoen_tow_day_state = $year_months."-".($phoen_dayy);
		
	}else{
		
		$phoen_tow_day_state = $year_months."-".($phoen_dayy+1);
	}
	
	
	
}else{
	
	$date_state_from = date("Y-m-d", strtotime($date_state_date_from));

	$date_state_to = date("Y-m-d", strtotime($date_state_date_to));
	
	$date_years_to = date("Y", strtotime($date_state_to));
	
	$date_months_to = date("m", strtotime($date_state_to));
	
	$date_days_to = date("d", strtotime($date_state_to));
	
	if($date_days_to =='31')
	{
		$phoen_tow_day_state = $date_years_to."-".$date_months_to."-".($date_days_to);
		
	}else{
		
		$phoen_tow_day_state = $date_years_to."-".$date_months_to."-".($date_days_to+1);
	
	}

}
	$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_billing_State_querys = "SET MAX_JOIN_SIZE = 99;
		SELECT 
			first_name_meta.meta_value as first_name,
			last_name_meta.meta_value as last_name,
			posts.post_status as ordr_status,  
			billing_amount_meta.meta_value as billing_amount,  
			billing_state_meta.meta_value as billing_state,  
			pay_method_meta.meta_value as pay_method,  
			payment_method_title_meta.meta_value as payment_method_title,  
			billing_email_meta.meta_value as billing_email,
			billing_country_meta.meta_value as billing_country,  
			posts.post_date as order_date,
			posts.ID AS ID
			
			FROM    {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS first_name_meta ON(posts.ID = first_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS last_name_meta ON(posts.ID = last_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_amount_meta ON(posts.ID = billing_amount_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_country_meta ON(posts.ID = billing_country_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_state_meta ON(posts.ID = billing_state_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS payment_method_title_meta ON(posts.ID = payment_method_title_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS pay_method_meta ON(posts.ID = pay_method_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_email_meta ON(posts.ID = billing_email_meta.post_id)
		WHERE 
			posts.post_status LIKE 'wc-%' 
			AND posts.post_type IN ( 'shop_order' ) 
			AND billing_amount_meta.meta_key IN ( '_order_total')
			AND billing_country_meta.meta_key IN ( '_billing_country')
			AND billing_state_meta.meta_key IN ( '_billing_state')
			AND pay_method_meta.meta_key IN ( '_payment_method')
			AND payment_method_title_meta.meta_key IN ( '_payment_method_title')
			AND last_name_meta.meta_key IN ( '_billing_last_name')
			AND first_name_meta.meta_key IN ( '_billing_first_name')
			AND billing_email_meta.meta_key IN ( '_billing_email')
			AND posts.post_date >= '$date_state_from' and posts.post_date <= '$phoen_tow_day_state'
			
		ORDER BY
			posts.ID ASC
LIMIT  10
	";
	//echo $query_data = $wpdb->query($phoen_billing_State_querys);

	$phoen_Billing_State_data = $wpdb->get_results(  $phoen_billing_State_querys,ARRAY_A);
	
	
	foreach($phoen_Billing_State_data as $ky=>$billing_data)
	{
		if($billing_data['ordr_status'] != 'wc-refunded')
		{
			if($billing_data['ordr_status'] != 'wc-cancelled')
			{
		
				if(!isset( $phoen_billings_states[$billing_data['billing_state']])){
					
					$phoen_billings_states[$billing_data['billing_state']] = Array(
					
						"state_name" =>$billing_data['billing_state'],
						
						"totle_order_counts" => 0,
						
						"total_amount" =>0
					
					);
					
				}
				$phoen_billings_states[$billing_data['billing_state']]["totle_order_counts"] += 1;
				$phoen_billings_states[$billing_data['billing_state']]["total_amount"] += $billing_data['billing_amount'];
			}
		}	
	}

	function phoen_state_short($e, $f) {
		return $e['totle_order_counts'] < $f['totle_order_counts'];
	}
	
	usort($phoen_billings_states, "phoen_state_short"); 
	 
	
/***********************end of billing state *********************************/	
	
	
/***********************Top payment Method Data******************************/	

	$date_payment_date_from =isset($_POST['date_payment_from'])?$_POST['date_payment_from']:'';

	$date_payment_date_to = isset($_POST['date_payment_to'])?$_POST['date_payment_to']:''; 
	

	if(($date_payment_date_from =='') ||($date_payment_date_to=='') )
	{
		
		$date_payment_from = date("Y-m-d", strtotime($phoen_order_date));
		
		$phoen_reports_recent_current = date("Y-m-d"); 
		
		$date_payment_to = date("Y-m-d", strtotime($phoen_reports_recent_current));
		
		$phoen_year_months = date('Y-m',strtotime($phoen_reports_recent_current));
	
		$phoen_day_date = date('d',strtotime($phoen_reports_recent_current));
		
		if($phoen_day_date =='31')
		{
			$phoen_tow_payment_state = $phoen_year_months."-".($phoen_day_date);
		
		}else{
			
			$phoen_tow_payment_state = $phoen_year_months."-".($phoen_day_date+1);
		}
		
		
		
	}else{
		
		$date_payment_from = date("Y-m-d", strtotime($date_payment_date_from));

		$date_payment_to = date("Y-m-d", strtotime($date_payment_date_to));
		
		$date_years_to = date("Y", strtotime($date_payment_to));
	
		$date_months_to = date("m", strtotime($date_payment_to));
	
		$date_days_to = date("d", strtotime($date_payment_to));
		
		if($date_days_to =='31')
		{
			$phoen_tow_payment_state = $date_years_to."-".$date_months_to."-".($date_days_to);
			
		}else{
			
			$phoen_tow_payment_state = $date_years_to."-".$date_months_to."-".($date_days_to+1);
		}
	
	}
$wpdb->get_results("SET SESSION sql_big_selects=on");
		$phoen_billing_Payment_querys = "
		SELECT 
			first_name_meta.meta_value as first_name,
			last_name_meta.meta_value as last_name,
			posts.post_status as ordr_status,  
			billing_amount_meta.meta_value as billing_amount,  
			billing_state_meta.meta_value as billing_state,  
			pay_method_meta.meta_value as pay_method,  
			payment_method_title_meta.meta_value as payment_method_title,  
			billing_email_meta.meta_value as billing_email,
			billing_country_meta.meta_value as billing_country, 
			posts.post_date as order_date,
			posts.ID AS ID
			
			FROM    {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS first_name_meta ON(posts.ID = first_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS last_name_meta ON(posts.ID = last_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_amount_meta ON(posts.ID = billing_amount_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_country_meta ON(posts.ID = billing_country_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_state_meta ON(posts.ID = billing_state_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS payment_method_title_meta ON(posts.ID = payment_method_title_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS pay_method_meta ON(posts.ID = pay_method_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_email_meta ON(posts.ID = billing_email_meta.post_id)
		WHERE 
			posts.post_status LIKE 'wc-%' 
			AND posts.post_type IN ( 'shop_order' ) 
			AND billing_amount_meta.meta_key IN ( '_order_total')
			AND billing_country_meta.meta_key IN ( '_billing_country')
			AND billing_state_meta.meta_key IN ( '_billing_state')
			AND pay_method_meta.meta_key IN ( '_payment_method')
			AND payment_method_title_meta.meta_key IN ( '_payment_method_title')
			AND last_name_meta.meta_key IN ( '_billing_last_name')
			AND first_name_meta.meta_key IN ( '_billing_first_name')
			AND billing_email_meta.meta_key IN ( '_billing_email')
			AND posts.post_date >= '$date_payment_from' and posts.post_date <= '$phoen_tow_payment_state'
			
		ORDER BY
			posts.ID ASC
	";
	$phoen_Billing_Payment_data = $wpdb->get_results(  $phoen_billing_Payment_querys,ARRAY_A);
	
	foreach($phoen_Billing_Payment_data as $ky=>$billing_data)
	{
		if($billing_data['ordr_status'] != 'wc-refunded')
		{
			if($billing_data['ordr_status'] != 'wc-cancelled')
			{	

				if(!isset( $phoen_payment_method[$billing_data['pay_method']])){
					
				
					$phoen_payment_method[$billing_data['pay_method']] = Array(
					
						"payment_name" => $billing_data['payment_method_title'],
						
						"totle_order_counts" => 0,
					
						"total_amount" => 0
					
					);
					
				}
				
				$phoen_payment_method[$billing_data['pay_method']]["totle_order_counts"] += 1;
				$phoen_payment_method[$billing_data['pay_method']]["total_amount"] += $billing_data['billing_amount'];
			}
		}	
		
	}

	function phoen_payment_short($g, $h) {
		return $g['totle_order_counts'] < $h['totle_order_counts'];
	}
	
	usort($phoen_payment_method, "phoen_payment_short"); 
	
/************************end payement method*********************************/

/************************top customer data***********************************/

	$date_customer_date_from =isset($_POST['date_customer_from'])?$_POST['date_customer_from']:'';

	$date_customer_date_to =isset($_POST['date_customer_to'])?$_POST['date_customer_to']:''; 
	
	
	if(($date_customer_date_from =='') ||($date_customer_date_to=='') )
	{
		
		$date_customer_from = date("Y-m-d", strtotime($phoen_order_date));
		
		$phoen_reports_recent_current = date("Y-m-d"); 
		
		$date_customer_to = date("Y-m-d", strtotime($phoen_reports_recent_current));
		
		$phoen_year_month = date('Y-m',strtotime($phoen_reports_recent_current));
	
		$phoen_days_date = date('d',strtotime($phoen_reports_recent_current));
		
		if($phoen_days_date =='31')
		{
			$phoen_tow_customer_state = $phoen_year_month."-".($phoen_days_date);
		
		}else{
			
			$phoen_tow_customer_state = $phoen_year_month."-".($phoen_days_date+1);
		}
		
		
		
	}else{
		
		$date_customer_from = date("Y-m-d", strtotime($date_customer_date_from));

		$date_customer_to = date("Y-m-d", strtotime($date_customer_date_to));
		
		$date_years_to = date("Y", strtotime($date_customer_to));
	
		$date_months_to = date("m", strtotime($date_customer_to));
	
		$date_days_to = date("d", strtotime($date_customer_to));
		
		if($date_days_to == '31')
		{
			$phoen_tow_customer_state = $date_years_to."-".$date_months_to."-".($date_days_to);
		
		}else{
			
			$phoen_tow_customer_state = $date_years_to."-".$date_months_to."-".($date_days_to+1);
		}
	
		

	}
	$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_billing_customer_querys = "
		SELECT 
			first_name_meta.meta_value as first_name,
			last_name_meta.meta_value as last_name,
			posts.post_status as ordr_status,  
			billing_amount_meta.meta_value as billing_amount,  
			billing_state_meta.meta_value as billing_state,  
			pay_method_meta.meta_value as pay_method,  
			payment_method_title_meta.meta_value as payment_method_title,  
			billing_email_meta.meta_value as billing_email,
			billing_country_meta.meta_value as billing_country, 
			posts.post_date as order_date,
			posts.ID AS ID
			
			FROM    {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS first_name_meta ON(posts.ID = first_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS last_name_meta ON(posts.ID = last_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_amount_meta ON(posts.ID = billing_amount_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_country_meta ON(posts.ID = billing_country_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_state_meta ON(posts.ID = billing_state_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS payment_method_title_meta ON(posts.ID = payment_method_title_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS pay_method_meta ON(posts.ID = pay_method_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_email_meta ON(posts.ID = billing_email_meta.post_id)
		WHERE 
			posts.post_status LIKE 'wc-%' 
			AND posts.post_type IN ( 'shop_order' ) 
			AND billing_amount_meta.meta_key IN ( '_order_total')
			AND billing_country_meta.meta_key IN ( '_billing_country')
			AND billing_state_meta.meta_key IN ( '_billing_state')
			AND pay_method_meta.meta_key IN ( '_payment_method')
			AND payment_method_title_meta.meta_key IN ( '_payment_method_title')
			AND last_name_meta.meta_key IN ( '_billing_last_name')
			AND first_name_meta.meta_key IN ( '_billing_first_name')
			AND billing_email_meta.meta_key IN ( '_billing_email')
			AND posts.post_date >= '$date_customer_from' and posts.post_date <= '$phoen_tow_customer_state'
			
		ORDER BY
			posts.ID ASC
	";
	$phoen_Billing_customer_data = $wpdb->get_results(  $phoen_billing_customer_querys,ARRAY_A);
	
	foreach($phoen_Billing_customer_data as $ky=>$billing_data)
	{
	
		if($billing_data['ordr_status'] != 'wc-refunded')
		{
			if($billing_data['ordr_status'] != 'wc-cancelled')
			{	
	
				if(!isset( $phoen_top_customer[$billing_data['billing_email']])){
					
					$phoen_top_customer[$billing_data['billing_email']] = Array(
					
						"total_customer_amoun" => 0,
						
						"totle_cust_order_count" => 0,
						
						"customer_fname" => $billing_data['first_name'],
						
						"customer_lname" => $billing_data['last_name'],
						
						"customer_email" => $billing_data['billing_email'],
						
						"customer_payment_method"=>$billing_data['payment_method_title']
						
					);
					
				}
			
				$phoen_top_customer[$billing_data['billing_email']]["totle_cust_order_count"] += 1;
				$phoen_top_customer[$billing_data['billing_email']]["total_customer_amoun"] += $billing_data['billing_amount'];
			}
		}	
	}

	function phoen_customer_short($g, $h) {
		return $g['total_customer_amoun'] < $h['total_customer_amoun'];
	}
	
	usort($phoen_top_customer, "phoen_customer_short");  

/*************************end of top customer **********************************/
	 	
/*******Totle status hold & completed & processing & cancelled & refunded data***/

 $date_status_date_from = isset($_POST['date_status_from'])?$_POST['date_status_from']:'';

$date_status_date_to = isset($_POST['date_status_to'])?$_POST['date_status_to']:'';


if(($date_status_date_from =='') ||($date_status_date_to=='') )
{
	
	$date_status_from = date("Y-m-d", strtotime($phoen_order_date));
	
	$phoen_reports_recent_current = date("Y-m-d"); 
	
	$date_status_to = date("Y-m-d", strtotime($phoen_reports_recent_current));
	
	$year = date('Y-m',strtotime($phoen_reports_recent_current));
	
	$day = date('d',strtotime($phoen_reports_recent_current));
	
	if($day =='31')
	{
		$phoen_status_to = $year."-".($day);
		
	}else{
		
		$phoen_status_to = $year."-".($day+1);
	
	}
	
	
}else{
	
	$date_status_from = date("Y-m-d", strtotime($date_status_date_from));

	$date_status_to = date("Y-m-d", strtotime($date_status_date_to));
	
	$date_years_to = date("Y", strtotime($date_status_to));

	$date_months_to = date("m", strtotime($date_status_to));

	$date_days_to = date("d", strtotime($date_status_to));
	
	if($date_days_to =='31')
	{
		$phoen_status_to = $date_years_to."-".$date_months_to."-".($date_days_to);
	}else{
		
		$phoen_status_to = $date_years_to."-".$date_months_to."-".($date_days_to+1);
	}

	

} 
$wpdb->get_results("SET SESSION sql_big_selects=on");
$phoen_order_status_querys = "
		SELECT 
			first_name_meta.meta_value as first_name,
			last_name_meta.meta_value as last_name,
			posts.post_status as ordr_status,  
			billing_amount_meta.meta_value as billing_amount,  
			billing_state_meta.meta_value as billing_state,  
			pay_method_meta.meta_value as pay_method,  
			payment_method_title_meta.meta_value as payment_method_title,  
			billing_email_meta.meta_value as billing_email,
			billing_country_meta.meta_value as billing_country, 
			posts.post_date as order_date,
			posts.ID AS ID
			
			FROM    {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS first_name_meta ON(posts.ID = first_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS last_name_meta ON(posts.ID = last_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_amount_meta ON(posts.ID = billing_amount_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_country_meta ON(posts.ID = billing_country_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_state_meta ON(posts.ID = billing_state_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS payment_method_title_meta ON(posts.ID = payment_method_title_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS pay_method_meta ON(posts.ID = pay_method_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_email_meta ON(posts.ID = billing_email_meta.post_id)
		WHERE 
			posts.post_status LIKE 'wc-%' 
			AND posts.post_type IN ( 'shop_order' ) 
			AND billing_amount_meta.meta_key IN ( '_order_total')
			AND billing_country_meta.meta_key IN ( '_billing_country')
			AND billing_state_meta.meta_key IN ( '_billing_state')
			AND pay_method_meta.meta_key IN ( '_payment_method')
			AND payment_method_title_meta.meta_key IN ( '_payment_method_title')
			AND last_name_meta.meta_key IN ( '_billing_last_name')
			AND first_name_meta.meta_key IN ( '_billing_first_name')
			AND billing_email_meta.meta_key IN ( '_billing_email')
			AND posts.post_date >= '$date_status_from' and posts.post_date <= '$phoen_status_to'
			
		ORDER BY
			posts.ID ASC
	
	";
	
	$phoen_status_order_datas = $wpdb->get_results(  $phoen_order_status_querys,ARRAY_A); 

	$phoen_repot_hold=0;	
	$phoen_repot_status_count='0';
	$phoen_status_name_hold='';
	
	$phoen_repot_completed=0;
	$phoen_repot_status_count_completed='0';
	$phoen_status_name_completed='';
	
	$phoen_repot_processing=0;
	$phoen_repot_status_count_processing='0';
	$phoen_status_name_processing='';
	
	$phoen_repot_cancelled=0;
	$phoen_repot_status_count_cancelled='0';
	$phoen_status_name_cancelled='';
	
	
	$phoen_repot_refunded=0;
	$phoen_repot_status_count_refunded='0';
	$phoen_status_name_refunded='';
	
	$phoen_reporting_order_date_hold='0';
	$phoen_reporting_order_date_completed='0';
	$phoen_reporting_order_date_processing='0';
	$phoen_reporting_order_date_cancelled='0';
	$phoen_reporting_order_date_refunded='0';
		
	 
		
	foreach($phoen_status_order_datas as $keys=> $phoen_reporing_status_data)
	{

		$phoen_repot_order_status = $phoen_reporing_status_data['ordr_status'];
		
		$phoen_reporting_order_date = $phoen_reporing_status_data['order_date'];
		
		if($phoen_repot_order_status =='wc-on-hold')  //status hold
		{
			$phoen_repot_hold+=$phoen_reporing_status_data['billing_amount'];
			
			$phoen_repot_status_count+=count($phoen_reporing_status_data['ordr_status']);
			
			$phoen_status_name_hold = $phoen_reporing_status_data['ordr_status'];
			
			$phoen_reporting_order_date_hold = $phoen_reporing_status_data['order_date'];
			
		}else if($phoen_repot_order_status =='wc-completed'){    //status completed
			
			$phoen_repot_completed+=$phoen_reporing_status_data['billing_amount'];
			
			$phoen_repot_status_count_completed+=count($phoen_reporing_status_data['ordr_status']);
			
			$phoen_status_name_completed = $phoen_reporing_status_data['ordr_status'];	
			
			$phoen_reporting_order_date_completed = $phoen_reporing_status_data['order_date'];
			
		}else if($phoen_repot_order_status =='wc-processing'){       //status processing
		
			$phoen_repot_processing+=$phoen_reporing_status_data['billing_amount'];
			
			$phoen_repot_status_count_processing+=count($phoen_reporing_status_data['ordr_status']);
			
			$phoen_status_name_processing = $phoen_reporing_status_data['ordr_status'];
			
			$phoen_reporting_order_date_processing = $phoen_reporing_status_data['order_date'];
			
		}else if($phoen_repot_order_status =='wc-cancelled'){     //status cancelled
			
			$phoen_repot_cancelled+=$phoen_reporing_status_data['billing_amount'];
			
			$phoen_repot_status_count_cancelled+=count($phoen_reporing_status_data['ordr_status']);
			
			$phoen_status_name_cancelled = $phoen_reporing_status_data['ordr_status'];
			
			$phoen_reporting_order_date_cancelled = $phoen_reporing_status_data['order_date'];
		
		}else if($phoen_repot_order_status =='wc-refunded'){     //status refunded
			
			$phoen_repot_refunded+=$phoen_reporing_status_data['billing_amount'];
			
			$phoen_repot_status_count_refunded+=count($phoen_reporing_status_data['ordr_status']);
			
			$phoen_status_name_refunded = $phoen_reporing_status_data['ordr_status'];
			
			$phoen_reporting_order_date_refunded = $phoen_reporing_status_data['order_date'];
						
		}
	
	}
	
	 $phoen_status_repot = array(
		
		'phoen_repot_holds' => array(
		
			'status_name'=>$phoen_status_name_hold,
			'status_count'=>$phoen_repot_status_count,
			'status_amount'=>$phoen_repot_hold,
			'date'=>$phoen_reporting_order_date_hold
			
		),
		
		'phoen_repot_completeds'=>array(
		
			'status_name'=>$phoen_status_name_completed,
			'status_count'=>$phoen_repot_status_count_completed,
			'status_amount'=>$phoen_repot_completed,
			'date'=>$phoen_reporting_order_date_completed
			
		
		),
		
		'phoen_repot_processings'=>array(
		
			'status_name'=>$phoen_status_name_processing,
			'status_count'=>$phoen_repot_status_count_processing,
			'status_amount'=>$phoen_repot_processing,
			'date'=>$phoen_reporting_order_date_processing
		
		),
		
		'phoen_repot_cancelleds'=>array(
		
			'status_name'=>$phoen_status_name_cancelled,
			'status_count'=>$phoen_repot_status_count_cancelled,
			'status_amount'=>$phoen_repot_cancelled,
			'date'=>$phoen_reporting_order_date_cancelled
		
		),
		
		'phoen_repot_refundeds'=>array(
		
			'status_name'=>$phoen_status_name_refunded,
			'status_count'=>$phoen_repot_status_count_refunded,
			'status_amount'=>$phoen_repot_refunded,
			'date'=>$phoen_reporting_order_date_refunded
		
		)
	
	
	);
	
	function phoen_reporting_status_short($q, $v) {
		
		return $q['status_count'] < $v['status_count'];
	}
		
		usort($phoen_status_repot, "phoen_reporting_status_short");  		
		
		
/****************end of status data ********************************/		
	
/***Total Tax & Total Coupan Amount & Total Customers User Data & Total Average Gross Monthly Sales & Last Day*********/

	
	$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_billing_customer_queryss = "
		SELECT 
			first_name_meta.meta_value as first_name,
			last_name_meta.meta_value as last_name,
			posts.post_status as ordr_status,  
			billing_amount_meta.meta_value as billing_amount,  
			billing_state_meta.meta_value as billing_state,  
			pay_method_meta.meta_value as pay_method,  
			payment_method_title_meta.meta_value as payment_method_title,  
			billing_email_meta.meta_value as billing_email,
			billing_country_meta.meta_value as billing_country, 
			posts.post_date as order_date,
			posts.ID AS ID
			
			FROM    {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS first_name_meta ON(posts.ID = first_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS last_name_meta ON(posts.ID = last_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_amount_meta ON(posts.ID = billing_amount_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_country_meta ON(posts.ID = billing_country_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_state_meta ON(posts.ID = billing_state_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS payment_method_title_meta ON(posts.ID = payment_method_title_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS pay_method_meta ON(posts.ID = pay_method_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_email_meta ON(posts.ID = billing_email_meta.post_id)
			
		WHERE 
			posts.post_status LIKE 'wc-%' 
			AND posts.post_type IN ( 'shop_order' ) 
			AND billing_amount_meta.meta_key IN ( '_order_total')
			AND billing_country_meta.meta_key IN ( '_billing_country')
			AND billing_state_meta.meta_key IN ( '_billing_state')
			AND pay_method_meta.meta_key IN ( '_payment_method')
			AND payment_method_title_meta.meta_key IN ( '_payment_method_title')
			AND last_name_meta.meta_key IN ( '_billing_last_name')
			AND first_name_meta.meta_key IN ( '_billing_first_name')
			AND billing_email_meta.meta_key IN ( '_billing_email')
			
		ORDER BY
			posts.ID ASC
	";
	
	$phoen_recent_order_datas = $wpdb->get_results(  $phoen_billing_customer_queryss,ARRAY_A);
	
	$phoen_ord_tax=0;	
	$phoe_ship_tax=0;
	$phoen_toatal_cupan_amount=0;
	$phoen_guest_user=0;
	
	$totle_billing_amount=0;
	$phoen_order_rf_counts='0';
	$totle_billing_amountt=0;
	$pho_order_ship=0;
	$totle_billing_refunds_amoun='0';
	
	$pho_order_shipr=0;
	$phoen_ord_taxr=0;
	$phoe_ship_taxr=0;
	$phoen_toatal_cupan_amountt=0;
	$phoen_totle_year_amount=0;
	
	$phoen_reporting_years='0';
	$phoen_year_count='0';
	$phoen_reporting_months='0';
	$phoen_totle_month_amount=0;
	$phoen_month_count='0';
	$phoen_totle_day_amount=0;
	$phoen_reporting_days='0';
	$phoen_day_count='0';
	$phoen_totle_yesterday_amount=0;
	$phoen_reporting_yester='0';
	$phoen_yesterday_count='0';
	$phoen_reporting_weeks='0';
	$phoen_totle_week_amount=0;
	$phoen_week_count='0';
	
	$phoen_totle_refund_amount=0;
	$phoen_today_order_ship_tax='0';
	$phoen_today_order_ship_taxx='0';
	$phoen_today_ord_tax='0';
	$phoen_today_average_sale='0';
	$pho_order_shipf=0;
	
	$phoen_reporting_year='0';
	
	$phoen_totle_refund_amount_add=0;
	
	$phoen_refund_count=0;
	
	$phoen_totle_year_sale_items_count='0';
	
	$phoen_repor_date=array();
	
	for($i=0; $i<count($phoen_recent_order_datas); $i++)
	{
		
		
		$phoen_post_id_totle = $phoen_recent_order_datas[$i]['ID'];
		$phoen_shop_order_meta = get_post_meta($phoen_post_id_totle);
		
		/* $phoen_totle_datas_order_shippings+=$phoen_shop_order_meta['_order_shipping'][0];
		$phoen_totle_datas_order_taxs+=$phoen_shop_order_meta['_order_tax'][0]; 
		$phoen_totle_datas_order_shipping_taxs+=$phoen_shop_order_meta['_order_shipping_tax'][0];
		$phoen_totle_datas_cart_discounts+=$phoen_shop_order_meta['_cart_discount'][0]; */

		
		$phoen_reporting_year = $phoen_recent_order_datas[$i]['order_date'];
		
		$phoen_repor_date[] = $phoen_reporting_year;
		
		$phoen_last_order_date = $phoen_recent_order_datas[0]['order_date'];
		
		$phoen_toatal_cupan_amount+=$phoen_shop_order_meta['_cart_discount'][0];
		
		$phoen_guest_user_data = isset($phoen_shop_order_meta[$i]['_customer_user'])?$phoen_shop_order_meta[$i]['_customer_user']:'';
		
		if($phoen_guest_user_data=='0')
		{
			$phoen_guest_user++;
		} 
		
		$phoen_today_refund = $phoen_recent_order_datas[$i]['ordr_status'];
		
		if($phoen_today_refund=='wc-refunded')
		{
			$phoen_totle_refund_amount_add+=$phoen_recent_order_datas[$i]['billing_amount'];
			
		}
		
		$phoen_current_date = date("Y-m-d");   //Total day refunded Amount
		
		$createDate = new DateTime($phoen_reporting_year);
		
		$phoen_reporting_day = $createDate->format('Y-m-d');
		
		if($phoen_current_date == $phoen_reporting_day)
		{
			if($phoen_today_refund =='wc-refunded')
			{
				$phoen_totle_refund_amount+=$phoen_recent_order_datas[$i]['billing_amount'];
			}
		}
		
		if($phoen_recent_order_datas[$i]['ordr_status']!='wc-cancelled')
		{
			$phoen_post_id_totle = $phoen_recent_order_datas[$i]['ID'];
			$phoen_shop_order_meta = get_post_meta($phoen_post_id_totle);
			$phoen_totle_datas_order_shippings =isset($phoen_shop_order_meta['_order_shipping'][0])?$phoen_shop_order_meta['_order_shipping'][0]:'';
		
			if($phoen_totle_datas_order_shippings!='0' && $phoen_totle_datas_order_shippings!='')
			{
				$pho_order_shipf+=$phoen_totle_datas_order_shippings;
			}
		
			
			$phoen_totle_year_sale_items_count+=count($phoen_recent_order_datas[$i]['order_date']);
		}
		
		$phoen_adv_status = isset($phoen_recent_order_datas[$i]['ordr_status'])?$phoen_recent_order_datas[$i]['ordr_status']:'';
		
		if($phoen_adv_status!='wc-refunded')   //Totle Sale Amount
		{
			if($phoen_adv_status!='wc-cancelled')
			{
				$phoen_post_id_totle = isset($phoen_recent_order_datas[$i]['ID'])?$phoen_recent_order_datas[$i]['ID']:'';
				$phoen_shop_order_meta = get_post_meta($phoen_post_id_totle);
				
				$pho_order_shipval=isset($phoen_shop_order_meta['_order_shipping'][0])?$phoen_shop_order_meta['_order_shipping'][0]:'';
				if($pho_order_shipval!='0' && $pho_order_shipval!='')
				{
					$pho_order_ship+=$phoen_shop_order_meta['_order_shipping'][0];
				}
				
				$phoen_ord_taxval=isset($phoen_shop_order_meta['_order_tax'][0])?$phoen_shop_order_meta['_order_tax'][0]:'';
				
				if($phoen_ord_taxval!='0' && $phoen_ord_taxval!='')
				{
					$phoen_ord_tax+=$phoen_shop_order_meta['_order_tax'][0];
				}
				
				$phoe_ship_taxval=isset($phoen_shop_order_meta['_order_shipping_tax'][0])?$phoen_shop_order_meta['_order_shipping_tax'][0]:'';
				
				if($phoe_ship_taxval!='0' && $phoe_ship_taxval!='')
				{
					$phoe_ship_tax+=$phoen_shop_order_meta['_order_shipping_tax'][0];
				}
				
				$totle_billing_amount+=$phoen_recent_order_datas[$i]['billing_amount'];
				
				$phoen_reporting_year = $phoen_recent_order_datas[$i]['order_date'];  //Total Year Amount 
				
				$phoen_reporting_cureent_year = date("Y");
				
				$yphoen_reporting_year = date('Y',strtotime($phoen_reporting_year));
				
				if($phoen_reporting_cureent_year == $yphoen_reporting_year)   //year
				{
					$phoen_totle_year_amount+= $phoen_recent_order_datas[$i]['billing_amount'];
					$phoen_year_count+=count($phoen_recent_order_datas[$i]['order_date']);
					$phoen_reporting_years = $phoen_recent_order_datas[$i]['order_date'];
				}
				
				$phoen_reporting_cureent_month = date("m");				//Total month Amount
				$phoen_reporting_month = date('m',strtotime($phoen_reporting_year));
				$phoen_current_year_month=$phoen_reporting_cureent_month."-".$phoen_reporting_cureent_year;
				$phoen_rept_year_month=$phoen_reporting_month."-".$yphoen_reporting_year;
				
				if($phoen_current_year_month == $phoen_rept_year_month)   //month
				{
					$phoen_totle_month_amount+= $phoen_recent_order_datas[$i]['billing_amount'];
					$phoen_month_count+=count($phoen_recent_order_datas[$i]['order_date']);
					$phoen_reporting_months = $phoen_recent_order_datas[$i]['order_date'];
				}
				
				$phoen_current_date = date("Y-m-d");   //Total day Amount
				$createDate = new DateTime($phoen_reporting_year);
				$phoen_reporting_day = $createDate->format('Y-m-d');
				
				if($phoen_current_date == $phoen_reporting_day) //day
				{
					$phoen_totle_day_amount+= $phoen_recent_order_datas[$i]['billing_amount'];
					$phoen_day_count+=count($phoen_recent_order_datas[$i]['order_date']);
					$phoen_reporting_days = $phoen_recent_order_datas[$i]['order_date'];
					
					$phoen_post_id_totle = $phoen_recent_order_datas[$i]['ID'];
					$phoen_shop_order_meta = get_post_meta($phoen_post_id_totle);
					
					$phoen_today_order_ship_taxx+=$phoen_shop_order_meta['_order_shipping'][0]; //shipping tax
					$phoen_today_ord_tax+=$phoen_shop_order_meta['_order_tax'][0]; 
					$phoen_today_order_ship_tax+=$phoen_shop_order_meta['_order_shipping_tax'][0];
					
					
					$phoen_today_average_sale+=count($phoen_recent_order_datas[$i]['billing_email']); // today avg sale
					
				}
				
				
				$phoen_yesterday_repot_date=date('Y-m-d',strtotime("-1 days"));  //Total yesterday Amount
				
				if($phoen_yesterday_repot_date == $phoen_reporting_day) //yesterday
				{
					$phoen_totle_yesterday_amount+= $phoen_recent_order_datas[$i]['billing_amount'];
					$phoen_yesterday_count+=count($phoen_recent_order_datas[$i]['order_date']);
					$phoen_reporting_yester = $phoen_recent_order_datas[$i]['order_date'];
				}
				 //Total week Amount
				
				$phoen_week_dates = date('Y-m-d', strtotime('-7 days'));
				
				if($phoen_week_dates <= $phoen_reporting_day )  //week
				{
			
					$phoen_totle_week_amount+= $phoen_recent_order_datas[$i]['billing_amount'];  
					$phoen_week_count+=count($phoen_recent_order_datas[$i]['order_date']);
					$phoen_reporting_weeks = $phoen_recent_order_datas[$i]['order_date'];
				}
				
				$phoen_last_monday = date("Y-m-d",strtotime('monday this week'));
				
				
			}
			
			
		}else{
			
		$refund_count = isset($phoen_recent_order_datas[$i]['ordr_status'])?$phoen_recent_order_datas[$i]['ordr_status']:'';
			
			if($refund_count!='0' && $refund_count!='')
			{
				$phoen_refund_count+=count($phoen_recent_order_datas[$i]['ordr_status']);
			}
		
		}
		$phoen_red_status = isset($phoen_recent_order_datas[$i]['ordr_status'])?$phoen_recent_order_datas[$i]['ordr_status']:'';
		 if( $phoen_red_status== 'wc-refunded' || $phoen_red_status == 'wc-cancelled')
		{
			$phoen_order_rf_counts+=count($phoen_recent_order_datas[$i]['ID']);
	
		} 
		
		 if($phoen_recent_order_datas[$i]['ordr_status'] != 'wc-refunded' || $phoen_recent_order_datas[$i]['ordr_status'] != 'wc-cancelled')
		{
			$totle_billing_amountt+=isset($phoen_recent_order_datas[$i]['billing_amount'])?$phoen_recent_order_datas[$i]['billing_amount']:'';
			
			$phoen_post_id_totle =isset($phoen_recent_order_datas[$i]['ID'])?$phoen_recent_order_datas[$i]['ID']:'';
			$phoen_shop_order_meta = get_post_meta($phoen_post_id_totle);
			
			$pho_order_shipr_va = isset($phoen_shop_order_meta['_order_shipping'][0])?$phoen_shop_order_meta['_order_shipping'][0]:'';
			
			if($pho_order_shipr_va!='0' && $pho_order_shipr_va!='')
			{
				$pho_order_shipr+=$phoen_shop_order_meta['_order_shipping'][0];
			}
			
				$phoen_ord_taxr_val=isset($phoen_shop_order_meta['_order_tax'][0])?$phoen_shop_order_meta['_order_tax'][0]:'';
			
			if($phoen_ord_taxr_val!='0' && $phoen_ord_taxr_val!='')
			{
				$phoen_ord_taxr+=$phoen_shop_order_meta['_order_tax'][0];
			}
			$phoe_ship_taxr_val=isset($phoen_shop_order_meta['_order_shipping_tax'][0])?$phoen_shop_order_meta['_order_shipping_tax'][0]:'';
			if($phoe_ship_taxr_val!='0' && $phoe_ship_taxr_val!='')
			{
				$phoe_ship_taxr+=$phoen_shop_order_meta['_order_shipping_tax'][0];
			}
			
			$phoen_toatal_cupan_amountt_val=isset($phoen_shop_order_meta['_cart_discount'][0])?$phoen_shop_order_meta['_cart_discount'][0]:'';
			
			if($phoen_toatal_cupan_amountt_val!='0' && $phoen_toatal_cupan_amountt_val!='')
			{
				$phoen_toatal_cupan_amountt+=$phoen_shop_order_meta['_cart_discount'][0];
			}
			
			
		}
	
	}
	
	$phoen_today_totle_tax=$phoen_today_order_ship_tax+$phoen_today_ord_tax;
	
	$phoen_all_tax=$phoen_ord_tax+$phoe_ship_tax;
	
	
	$phoen_reporting_all_data=array(
		
			'phoen_report_today'=>array(
			
				'phoen_name'	=>'Today',
				'phoen_count'	=>$phoen_day_count,
				'phoen_amount'	=>$phoen_totle_day_amount,
				'date'			=>$phoen_reporting_days
				
			),
			'phoen_reporting_yesterday'=>array(
				
				'phoen_name'	=>'Yesterday',
				'phoen_count'	=>$phoen_yesterday_count,
				'phoen_amount'	=>$phoen_totle_yesterday_amount,
				'date'			=>$phoen_reporting_yester
			
			),
			
			'phoen_reporting_week'=>array(
			
				'phoen_name'	=>'Week',
				'phoen_count'	=>$phoen_week_count,
				'phoen_amount'	=>$phoen_totle_week_amount,
				'date'			=>$phoen_reporting_weeks
					
			),
	
			'phoen_reporting_month'=>array(
			
				'phoen_name'	=>'Month',
				'phoen_count'	=>$phoen_month_count,
				'phoen_amount'	=>$phoen_totle_month_amount,
				'date'			=>$phoen_reporting_months
				
			),
			
			'phoen_reporting_year'=>array(
			
				'phoen_name'	=>'Year',
				'phoen_count'	=>$phoen_year_count,
				'phoen_amount'	=>$phoen_totle_year_amount,
				'date'			=>$phoen_reporting_years
			)
	
	);

 
	$phoen_month= date("m");
	
	$phoen_totle_gross_sale=($totle_billing_amount/$phoen_month);
	
	$total_sale_amount =  number_format((float)$phoen_totle_gross_sale, 2, '.', '');
	
	$phoe_totles=($pho_order_shipr+$phoen_ord_taxr+$phoe_ship_taxr);
	
	$phoe_prices=$totle_billing_amount-$phoe_totles;
	
	$phoen_net_sales=$phoe_prices/$phoen_month;
	
	$phoen_net_sale =  number_format((float)$phoen_net_sales, 2, '.', '');
	 
	$phe_datas=1.11;
 
	
/**********************month and year data***********************************/ 
	$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_year_order_querys = "
	SELECT 
			first_name_meta.meta_value as first_name,
			last_name_meta.meta_value as last_name,
			posts.post_status as ordr_status,  
			billing_amount_meta.meta_value as billing_amount,  
			billing_state_meta.meta_value as billing_state,  
			pay_method_meta.meta_value as pay_method,  
			payment_method_title_meta.meta_value as payment_method_title,  
			billing_email_meta.meta_value as billing_email,
			billing_country_meta.meta_value as billing_country, 
			posts.post_date as order_date,
			posts.ID AS ID
			
			FROM    {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS first_name_meta ON(posts.ID = first_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS last_name_meta ON(posts.ID = last_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_amount_meta ON(posts.ID = billing_amount_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_country_meta ON(posts.ID = billing_country_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_state_meta ON(posts.ID = billing_state_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS payment_method_title_meta ON(posts.ID = payment_method_title_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS pay_method_meta ON(posts.ID = pay_method_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_email_meta ON(posts.ID = billing_email_meta.post_id)
			
		WHERE 
			posts.post_status LIKE 'wc-%' 
			AND posts.post_type IN ( 'shop_order' ) 
			AND billing_amount_meta.meta_key IN ( '_order_total')
			AND billing_country_meta.meta_key IN ( '_billing_country')
			AND billing_state_meta.meta_key IN ( '_billing_state')
			AND pay_method_meta.meta_key IN ( '_payment_method')
			AND payment_method_title_meta.meta_key IN ( '_payment_method_title')
			AND last_name_meta.meta_key IN ( '_billing_last_name')
			AND first_name_meta.meta_key IN ( '_billing_first_name')
			AND billing_email_meta.meta_key IN ( '_billing_email')
			
		ORDER BY
			posts.ID ASC
	";
	$phoen_year_month_order_querys = $wpdb->get_results(  $phoen_year_order_querys,ARRAY_A);
	
	$phoen_totle_amount_month_jan=0;
	$phoen_totle_amount_refunds_jan=0;
	$phoen_totle_datas_cart_discount_jan=0;
	$phoen_totle_datas_order_shipping_jan=0;
	$phoen_totle_datas_order_tax_jan=0;
	$phoen_totle_datas_order_shipping_tax_jan=0;
	$phoen_totle_dicount_jan=0;
	
	$phoen_totle_amount_month_feb=0;
	$phoen_totle_datas_cart_discount_feb=0;
	$phoen_totle_amount_refunds_feb=0;
	$phoen_totle_datas_order_shipping_feb=0;
	$phoen_totle_datas_order_tax_feb=0;
	$phoen_totle_datas_order_shipping_tax_feb=0;
	$phoen_totle_dicount_feb=0;
	
	$phoen_totle_amount_month_mar=0;
	$phoen_totle_amount_refunds_mar=0;
	$phoen_totle_datas_cart_discount_mar=0;
	$phoen_totle_datas_order_shipping_mar=0;
	$phoen_totle_datas_order_tax_mar=0;
	$phoen_totle_datas_order_shipping_tax_mar=0;
	$phoen_totle_dicount_mar=0;
	
	$phoen_totle_amount_month_apr=0;
	$phoen_totle_amount_refunds_apr=0;
	$phoen_totle_datas_cart_discount_apr=0;
	$phoen_totle_datas_order_shipping_apr=0;
	$phoen_totle_datas_order_tax_apr=0;
	$phoen_totle_datas_order_shipping_tax_apr=0;
	$phoen_totle_dicount_apr=0;
	
	$phoen_totle_amount_month_may=0;
	$phoen_totle_amount_refunds_may=0;
	$phoen_totle_datas_cart_discount_may=0;
	$phoen_totle_datas_order_shipping_may=0;
	$phoen_totle_datas_order_tax_may=0;
	$phoen_totle_datas_order_shipping_tax_may=0;
	$phoen_totle_dicount_may=0;
	
	$phoen_totle_amount_month_jun=0;
	$phoen_totle_amount_refunds_jun=0;
	$phoen_totle_datas_cart_discount_jun=0;
	$phoen_totle_datas_order_shipping_jun=0;
	$phoen_totle_datas_order_tax_jun=0;
	$phoen_totle_datas_order_shipping_tax_jun=0;
	$phoen_totle_dicount_jun=0;
		
	$phoen_totle_amount_month_jul=0;
	$phoen_totle_amount_refunds_jul=0;
	$phoen_totle_datas_cart_discount_jul=0;
	$phoen_totle_datas_order_shipping_jul=0;
	$phoen_totle_datas_order_tax_jul=0;
	$phoen_totle_datas_order_shipping_tax_jul=0;
	$phoen_totle_dicount_jul=0;
				
	$phoen_totle_amount_month_aug=0;
	$phoen_totle_amount_refunds_aug=0;
	$phoen_totle_datas_cart_discount_aug=0;
	$phoen_totle_datas_order_shipping_aug=0;
	$phoen_totle_datas_order_tax_aug=0;
	$phoen_totle_datas_order_shipping_tax_aug=0;
	$phoen_totle_dicount_aug=0;
	
	$phoen_totle_amount_month_sept=0;
	$phoen_totle_amount_refunds_sep=0;
	$phoen_totle_datas_cart_discount_sept=0;
	$phoen_totle_datas_order_shipping_sept=0;
	$phoen_totle_datas_order_tax_sept=0;
	$phoen_totle_datas_order_shipping_tax_sept=0;
	$phoen_totle_dicount_sep=0;
				
			
	$phoen_totle_amount_month_oct=0;
	$phoen_totle_amount_refunds_oct=0;
	$phoen_totle_datas_cart_discount_oct=0;
	$phoen_totle_datas_order_shipping_oct=0;
	$phoen_totle_datas_order_tax_oct=0;
	$phoen_totle_datas_order_shipping_tax_oct=0;
	$phoen_totle_dicount_oct=0;
				
	$phoen_totle_amount_month_nov=0;
	$phoen_totle_amount_refunds_nov=0;
	$phoen_totle_datas_cart_discount_nov=0;
	$phoen_totle_datas_order_shipping_nov=0;
	$phoen_totle_datas_order_tax_nov=0;
	$phoen_totle_datas_order_shipping_tax_nov=0;
	$phoen_totle_dicount_nov=0;			
		
	$phoen_totle_amount_month_dec=0;
	$phoen_totle_amount_refunds_dec=0;
	$phoen_totle_datas_cart_discount_dec=0;
	$phoen_totle_datas_order_shipping_dec=0;
	$phoen_totle_datas_order_tax_dec=0;
	$phoen_totle_datas_order_shipping_tax_dec=0;
	$phoen_totle_dicount_dec=0;
	

	$phoen_year_month_data=array();
	
	$phoen_reporting_year_month_date='0';
	

	for($i=0; $i<count($phoen_year_month_order_querys); $i++)
	{
		
		$phoen_status = $phoen_year_month_order_querys[$i]['ordr_status'];
		
		$phoen_reporting_year_month_date = $phoen_year_month_order_querys[$i]['order_date']; 

		$phoen_year_reportings_ydata = date('Y',strtotime($phoen_reporting_year_month_date));
		
		$phoen_report_cureent_year = date("Y");
		
		$phoen_year_reporting_ydata = date('Y',strtotime($phoen_reporting_year_month_date));
		$phoen_month_reporting_mdata = date('m',strtotime($phoen_reporting_year_month_date));
		
		if($phoen_year_reportings_ydata == $phoen_report_cureent_year)
		{
			if($phoen_month_reporting_mdata =='01')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
					
						$phoen_totle_amount_month_jan+= $phoen_year_month_order_querys[$i]['billing_amount'];
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						$phoen_totle_datas_order_shipping_jan+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_jan+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_jan+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_jan+=$phoen_shop_order['_cart_discount'][0];
						$phoen_totle_dicount_jan=$phoen_totle_datas_order_tax_jan+$phoen_totle_datas_order_shipping_tax_jan;
					}
				}else{
					
					$phoen_totle_amount_refunds_jan+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='02')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
					
						$phoen_totle_amount_month_feb+= $phoen_year_month_order_querys[$i]['billing_amount'];
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						$phoen_totle_datas_order_shipping_feb+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_feb+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_feb+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_feb+=$phoen_shop_order['_cart_discount'][0];
						$phoen_totle_dicount_feb=$phoen_totle_datas_order_tax_feb+$phoen_totle_datas_order_shipping_tax_feb;
						
					}
				}else{
					
					$phoen_totle_amount_refunds_feb+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='03')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
					
						$phoen_totle_amount_month_mar+= $phoen_year_month_order_querys[$i]['billing_amount'];
						
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						$phoen_totle_datas_order_shipping_mar+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_mar+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_mar+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_mar+=$phoen_shop_order['_cart_discount'][0];
						$phoen_totle_dicount_mar=$phoen_totle_datas_order_tax_mar+$phoen_totle_datas_order_shipping_tax_mar;
					}
				}else{
					
					$phoen_totle_amount_refunds_mar+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='04')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
						
						$phoen_totle_amount_month_apr+= $phoen_year_month_order_querys[$i]['billing_amount'];
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						
						$phoen_totle_datas_order_shipping_apr+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_apr+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_apr+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_apr+=$phoen_shop_order['_cart_discount'][0];
						$phoen_totle_dicount_apr=$phoen_totle_datas_order_tax_apr+$phoen_totle_datas_order_shipping_tax_apr;
					}
				}else{
					
					$phoen_totle_amount_refunds_apr+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='05')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
						
						$phoen_totle_amount_month_may+= $phoen_year_month_order_querys[$i]['billing_amount'];
						
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						
						$phoen_totle_datas_order_shipping_may+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_may+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_may+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_may+=$phoen_shop_order['_cart_discount'][0];
						
						$phoen_totle_dicount_may=$phoen_totle_datas_order_tax_may+$phoen_totle_datas_order_shipping_tax_may;
					}
				}else{
					
					$phoen_totle_amount_refunds_may+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='06')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
					
						$phoen_totle_amount_month_jun+= $phoen_year_month_order_querys[$i]['billing_amount'];
						
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						
						$phoen_totle_datas_order_shipping_jun+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_jun+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_jun+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_jun+=$phoen_shop_order['_cart_discount'][0];
						
						$phoen_totle_dicount_jun=$phoen_totle_datas_order_shipping_tax_jun+$phoen_totle_datas_order_tax_jun;
					}
				}else{
					
					$phoen_totle_amount_refunds_jun+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='07')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
					
						$phoen_totle_amount_month_jul+= $phoen_year_month_order_querys[$i]['billing_amount'];
						
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						
						$phoen_totle_datas_order_shipping_jul+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_jul+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_jul+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_jul+=$phoen_shop_order['_cart_discount'][0];
						
						$phoen_totle_dicount_jul=$phoen_totle_datas_order_shipping_tax_jul+$phoen_totle_datas_order_tax_jul;
					}
				}else{
					
					$phoen_totle_amount_refunds_jul+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='08')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
						
						$phoen_totle_amount_month_aug+= $phoen_year_month_order_querys[$i]['billing_amount'];
						
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						
						$phoen_totle_datas_order_shipping_aug+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_aug+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_aug+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_aug+=$phoen_shop_order['_cart_discount'][0];
						
						$phoen_totle_dicount_aug=$phoen_totle_datas_order_shipping_tax_aug+$phoen_totle_datas_order_tax_aug;
					}
				}else{
					
					$phoen_totle_amount_refunds_aug+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='09')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
					
						$phoen_totle_amount_month_sept+= $phoen_year_month_order_querys[$i]['billing_amount'];
						
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						
						$phoen_totle_datas_order_shipping_sept+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_sept+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_sept+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_sept+=$phoen_shop_order['_cart_discount'][0];
						
						$phoen_totle_dicount_sep=$phoen_totle_datas_order_shipping_tax_sept+$phoen_totle_datas_order_tax_sept;
					}
				}else{
					
					$phoen_totle_amount_refunds_sep+= $phoen_year_month_order_querys[$i]['billing_amount'];
					
					
				}
			}
			if($phoen_month_reporting_mdata =='10')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
						
						$phoen_totle_amount_month_oct+= $phoen_year_month_order_querys[$i]['billing_amount'];
						
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						
						$phoen_totle_datas_order_shipping_oct+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_oct+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_oct+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_oct+=$phoen_shop_order['_cart_discount'][0];
						
						$phoen_totle_dicount_oct=$phoen_totle_datas_order_shipping_tax_oct+$phoen_totle_datas_order_tax_oct;
						
					}
				}else{
					
					$phoen_totle_amount_refunds_oct+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='11')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{ 
						$phoen_totle_amount_month_nov+=$phoen_year_month_order_querys[$i]['billing_amount'];
						
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						
						$phoen_totle_datas_order_shipping_nov+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_nov+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_nov+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_nov+=$phoen_shop_order['_cart_discount'][0];
						
						$phoen_totle_dicount_nov=$phoen_totle_datas_order_tax_nov+$phoen_totle_datas_order_shipping_tax_nov;
					
					}
				}else{
					
					$phoen_totle_amount_refunds_nov+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
			if($phoen_month_reporting_mdata =='12')
			{
				if($phoen_status !='wc-refunded')
				{
					if($phoen_status !='wc-cancelled')
					{
						
						$phoen_totle_amount_month_dec+=$phoen_year_month_order_querys[$i]['billing_amount'];
						
						$phoen_post_id = $phoen_year_month_order_querys[$i]['ID'];
						$phoen_shop_order = get_post_meta($phoen_post_id);
						
						$phoen_totle_datas_order_shipping_dec+=$phoen_shop_order['_order_shipping'][0];
						$phoen_totle_datas_order_tax_dec+=$phoen_shop_order['_order_tax'][0]; 
						$phoen_totle_datas_order_shipping_tax_dec+=$phoen_shop_order['_order_shipping_tax'][0];
						$phoen_totle_datas_cart_discount_dec+=$phoen_shop_order['_cart_discount'][0];
						
						$phoen_totle_dicount_dec=$phoen_totle_datas_order_tax_dec+$phoen_totle_datas_order_shipping_tax_dec;
						
					}
				}else{
					
					$phoen_totle_amount_refunds_dec+= $phoen_year_month_order_querys[$i]['billing_amount'];
				}
			}
				
		}
		$phoen_year_month_data=array(
		
			'phoen_jan'=>array(
				'month_name'=>'January',
				'total_sales_amt'=>$phoen_totle_amount_month_jan,
				'total_refund_amt'=>$phoen_totle_amount_refunds_jan,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_jan,
				'shipping_order'=>$phoen_totle_datas_order_shipping_jan,
				'order_tax'=>$phoen_totle_datas_order_tax_jan,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_jan,
				'total_tax'=>$phoen_totle_dicount_jan
				
			),
		
			'phoen_feb'=>array(
				'month_name'=>'February',
				'total_sales_amt'=>$phoen_totle_amount_month_feb,
				'total_refund_amt'=>$phoen_totle_datas_cart_discount_feb,
				'total_discount_amt'=>$phoen_totle_amount_refunds_feb,
				'shipping_order'=>$phoen_totle_datas_order_shipping_feb,
				'order_tax'=>$phoen_totle_datas_order_tax_feb,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_feb,
				'total_tax'=>$phoen_totle_dicount_feb
				
			),
		
			'phoen_mar'=>array(
				'month_name'=>'March',
				'total_sales_amt'=>$phoen_totle_amount_month_mar,
				'total_refund_amt'=>$phoen_totle_amount_refunds_mar,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_mar,
				'shipping_order'=>$phoen_totle_datas_order_shipping_mar,
				'order_tax'=>$phoen_totle_datas_order_tax_mar,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_mar,
				'total_tax'=>$phoen_totle_dicount_mar
				
			),
	
			'phoen_apr'=>array(
				'month_name'=>'April',
				'total_sales_amt'=>$phoen_totle_amount_month_apr,
				'total_refund_amt'=>$phoen_totle_amount_refunds_apr,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_apr,
				'shipping_order'=>$phoen_totle_datas_order_shipping_apr,
				'order_tax'=>$phoen_totle_datas_order_tax_apr,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_apr,
				'total_tax'=>$phoen_totle_dicount_apr
				
			),
	
			'phoen_may'=>array(
				'month_name'=>'May',
				'total_sales_amt'=>$phoen_totle_amount_month_may,
				'total_refund_amt'=>$phoen_totle_amount_refunds_may,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_may,
				'shipping_order'=>$phoen_totle_datas_order_shipping_may,
				'order_tax'=>$phoen_totle_datas_order_tax_may,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_may,
				'total_tax'=>$phoen_totle_dicount_may
				
			),
			
			'phoen_jun'=>array(
				'month_name'=>'June',
				'total_sales_amt'=>$phoen_totle_amount_month_jun,
				'total_refund_amt'=>$phoen_totle_amount_refunds_jun,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_jun,
				'shipping_order'=>$phoen_totle_datas_order_shipping_jun,
				'order_tax'=>$phoen_totle_datas_order_tax_jun,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_jun,
				'total_tax'=>$phoen_totle_dicount_jun
				
			),
	
			'phoen_jul'=>array(
				'month_name'=>'July',
				'total_sales_amt'=>$phoen_totle_amount_month_jul,
				'total_refund_amt'=>$phoen_totle_amount_refunds_jul,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_jul,
				'shipping_order'=>$phoen_totle_datas_order_shipping_jul,
				'order_tax'=>$phoen_totle_datas_order_tax_jul,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_jul,
				'total_tax'=>$phoen_totle_dicount_jul
				
			),
			'phoen_aug'=>array(
				'month_name'=>'August',
				'total_sales_amt'=>$phoen_totle_amount_month_aug,
				'total_refund_amt'=>$phoen_totle_amount_refunds_aug,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_aug,
				'shipping_order'=>$phoen_totle_datas_order_shipping_aug,
				'order_tax'=>$phoen_totle_datas_order_tax_aug,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_aug,
				'total_tax'=>$phoen_totle_dicount_aug
			
			),

			'phoen_sep'=>array(
				'month_name'=>'September',
				'total_sales_amt'=>$phoen_totle_amount_month_sept,
				'total_refund_amt'=>$phoen_totle_amount_refunds_sep,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_sept,
				'shipping_order'=>$phoen_totle_datas_order_shipping_sept,
				'order_tax'=>$phoen_totle_datas_order_tax_sept,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_sept,
				'total_tax'=>$phoen_totle_dicount_sep
				
			),
	
			'phoen_oct'=>array(
				'month_name'=>'October',
				'total_sales_amt'=>$phoen_totle_amount_month_oct,
				'total_refund_amt'=>$phoen_totle_amount_refunds_oct,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_oct,
				'shipping_order'=>$phoen_totle_datas_order_shipping_oct,
				'order_tax'=>$phoen_totle_datas_order_tax_oct,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_oct,
				'total_tax'=>$phoen_totle_dicount_oct
				
			),
			'phoen_nov'=>array(
				'month_name'=>'November',
				'total_sales_amt'=>$phoen_totle_amount_month_nov,
				'total_refund_amt'=>$phoen_totle_amount_refunds_nov,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_nov,
				'shipping_order'=>$phoen_totle_datas_order_shipping_nov,
				'order_tax'=>$phoen_totle_datas_order_tax_nov,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_nov,
				'total_tax'=>$phoen_totle_dicount_nov
			
			),
			
			'phoen_dec'=>array(
				'month_name'=>'December',
				'total_sales_amt'=>$phoen_totle_amount_month_dec,
				'total_refund_amt'=>$phoen_totle_amount_refunds_dec,
				'total_discount_amt'=>$phoen_totle_datas_cart_discount_dec,
				'shipping_order'=>$phoen_totle_datas_order_shipping_dec,
				'order_tax'=>$phoen_totle_datas_order_tax_dec,
				'total_shipping_tax'=>$phoen_totle_datas_order_shipping_tax_dec,
				'total_tax'=>$phoen_totle_dicount_dec
			
			)
		);
			
	}
	
/*************************end of month year data *******************************/	

/************************Recent Order Data*************************************/

$date_recent_froms =isset($_POST['date_recent_from'])?$_POST['date_recent_from']:''; 

$date_recent_tos = isset($_POST['date_recent_to'])?$_POST['date_recent_to']:''; 


if(($date_recent_froms =='') ||($date_recent_tos=='') )
{
	
	$date_recent_from = date("Y-m-d", strtotime($phoen_order_date));
	
	$phoen_reports_recent_current = date("Y-m-d"); 
	
	$date_recent_to = date("Y-m-d", strtotime($phoen_reports_recent_current));
	
	$years = date('Y-m',strtotime($phoen_reports_recent_current));
	
	$days = date('d',strtotime($phoen_reports_recent_current));
	
	if($days =='31')
	{
		$phoen_recent_to = $years."-".($days);
	
	}else{
		
		$phoen_recent_to = $years."-".($days+1);
	
	}

	
}else{
	
	$date_recent_from = date("Y-m-d", strtotime($date_recent_froms));

	$date_recent_to = date("Y-m-d", strtotime($date_recent_tos));
	
	
	$date_years_to = date("Y", strtotime($date_recent_to));

	$date_months_to = date("m", strtotime($date_recent_to));

	$date_days_to = date("d", strtotime($date_recent_to));
	
	if($date_days_to == '31')
	{ 
		/* if($date_years_to == '2016')
		{
			$phoen_recent_to = "2017"."-"."01"."-"."01";
			
		}else if($date_years_to == '2017')
		{
			$phoen_recent_to = $date_years_to."-".($date_months_to+1)."-"."01";
		} */
		
		$phoen_recent_to = $date_years_to."-".($date_months_to+1)."-"."01";
		
		
		//$phoen_recent_to = $date_years_to."-".$date_months_to."-".($date_days_to);
		
		//$phoen_recent_to = ($date_years_to+1)."-".($date_months_to+1)."-".($date_days_to);
	
	}else{
		
		$phoen_recent_to = $date_years_to."-".$date_months_to."-".($date_days_to+1);
	}

}

if(isset($_POST['report_limit_submit_recent_order']))
{
	$phoen_recent_order_value =$_POST['phoen_ten_recent_order']; 
	
	update_option( 'phoen_top_recent_order', $phoen_recent_order_value );
	
	$phoen_recent_ordering_value = $_POST['phoen_ten_recent_shorting_order'];
	
	update_option( 'phoen_top_recent_ordering', $phoen_recent_ordering_value ); 
	
}

$phoen_get_recent_ordering_val = get_option( 'phoen_top_recent_ordering' );
								
if($phoen_get_recent_ordering_val=='')
{
	$phoen_get_recent_ordering_val='DESC';
	
}else{
	$phoen_get_recent_ordering_val = get_option( 'phoen_top_recent_ordering' );
}

$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_recent_order_querys_all = "
		SELECT 
			first_name_meta.meta_value as first_name,
			last_name_meta.meta_value as last_name,
			posts.post_status as ordr_status,  
			billing_amount_meta.meta_value as billing_amount,  
			billing_email_meta.meta_value as billing_email,
			billing_address1_meta.meta_value as billing_address1,
			billing_address2_meta.meta_value as billing_address2,
			billing_phone_meta.meta_value as billing_phone_no,
			posts.post_date as order_date,
			posts.ID AS ID
			
			FROM    {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS first_name_meta ON(posts.ID = first_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS last_name_meta ON(posts.ID = last_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_amount_meta ON(posts.ID = billing_amount_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_email_meta ON(posts.ID = billing_email_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_address1_meta ON(posts.ID = billing_address1_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_address2_meta ON(posts.ID = billing_address2_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_phone_meta ON(posts.ID = billing_phone_meta.post_id)
			
		WHERE 
			posts.post_status LIKE 'wc-%' 
			AND posts.post_type IN ( 'shop_order' ) 
			AND billing_amount_meta.meta_key IN ( '_order_total')
			
			AND last_name_meta.meta_key IN ( '_billing_last_name')
			AND first_name_meta.meta_key IN ( '_billing_first_name')
			AND billing_email_meta.meta_key IN ( '_billing_email')
			AND billing_address1_meta.meta_key IN ( '_billing_address_1')
			AND billing_address2_meta.meta_key IN ( '_billing_address_2')
			AND billing_phone_meta.meta_key IN ( '_billing_phone')
		
			AND posts.post_date >= '$date_recent_from' and posts.post_date <= '$phoen_recent_to'
			
		ORDER BY
			posts.ID $phoen_get_recent_ordering_val
	";
	$phoen_recent_order_datas_all = $wpdb->get_results(  $phoen_recent_order_querys_all,ARRAY_A);  
	
	$phoen_reward_coupon_name=array();
	
	
	$phoen_get_recent_order_value = get_option( 'phoen_top_recent_order' );
								
		if($phoen_get_recent_order_value=='')
		{
			$phoen_get_recent_order_val='10';
			
		}else{
			
			$phoen_get_recent_order_val = get_option( 'phoen_top_recent_order' );
		}

	$phoen_recent_count=0;	
	
	foreach($phoen_recent_order_datas_all as $k=>$phoen_recent_order_datas_all_dat)
	{
		if($phoen_get_recent_order_val!='View All')
		{
		
			if($phoen_recent_count<$phoen_get_recent_order_val)
			{ 
				 $order_id = $phoen_recent_order_datas_all_dat['ID'];
				
				$orders_data = new WC_Order( $order_id );
				
				$coupons_data = $orders_data->get_items( 'coupon' );
				
				foreach ( $coupons_data as $item_id => $item_data ) {
				
					$phoen_reward_coupon_name[$k] = $item_data['name'];
			
				}
				
			}
		 $phoen_recent_count++;		
		}else{
			
			 $order_id = $phoen_recent_order_datas_all_dat['ID'];
				
				$orders_data = new WC_Order( $order_id );
				
				$coupons_data = $orders_data->get_items( 'coupon' );
				
				foreach ( $coupons_data as $item_id => $item_data ) {
				
					$phoen_reward_coupon_name[$k] = $item_data['name'];
			
				}	
		}
	
	} 
	
/**********************End of Recent data ********************************/
	
	 $phoen_products_args = array(

		'post_type' => 'product',

		'numberposts' => 400,

	);  
	
	
//$products_unsold = get_posts( $phoen_products_args );
	

	$products = get_posts( $phoen_products_args );
	
	$phoen_product_name_instock='0';
	$phoen_instock_count='0';
	$phoen_product_name_outstock='0';
	$phoen_outstock_count='0';
	
	$phoen_product_name_simple_instock='0';
	$phoen_product_name_simple_outstock='0';
	
	foreach($products as $key=> $product)
	{
		$product_s = wc_get_product( $product->ID );
		
		$product_type = new WC_Product( $product->ID );
		
        if( $product_type->is_type( 'variable' ) ) {
        
		//if ($product_s->product_type == 'variable') {

			$variations = $product_s->get_available_variations();
			
			foreach($variations as $kery=> $val)
			{	
				if($val['max_qty'] !='')
				{
					
					$phoen_product_name_instock+=count($product->post_title);
					
				}else{
					
					$phoen_product_name_outstock+=count($product->post_title);
					
				}
			}		
			
			
		}else{
			
			$phoen_products_simple = get_post_meta($product->ID);
				
			$phoen_simple_stock = $phoen_products_simple['_stock'][0];
			
			$phoen_stock_status=$phoen_products_simple['_stock_status'][0];
			
			if($phoen_stock_status !='outofstock')
			{
				$phoen_product_name_simple_instock+=count($product->post_title);
				
			}else{
				
				$phoen_product_name_simple_outstock+=count($product->post_title);
			}
		
		}
		
	} 
	
//unsold products Detail 	

	 $phoen_products_unsold = array(

		'post_type' => 'product',

		'numberposts' => 400,

	);

	$products_unsold = get_posts( $phoen_products_unsold );
	
	$phoen_unsold=array();
	$phoen_pro_id=array();
	foreach($products_unsold as $k=>$phoen_unsold_products)
	{
		
		$phoen_pro_id[$k] = $phoen_unsold_products->ID;
		
		
		$phoen_products_unsold_val = get_post_meta($phoen_unsold_products->ID);
			
		$phoen_produc_name = $phoen_unsold_products->post_name;
		
		$phoen_totle_stock = $phoen_products_unsold_val['_stock'][0];
		
		if($phoen_totle_stock !='')
		{
			
			$phoen_add_price_val = $phoen_products_unsold_val['_price'][0];
			
			if($phoen_add_price_val =='')
			{
				$phoen_add_price='0';
				
			}else{
				
				$phoen_add_price = ($phoen_add_price_val*$phoen_totle_stock);
				
			}
			
			
			
			$phoen_stock_totle = round($phoen_totle_stock, 0);
		
			$phoen_unsold[$k]=array(
			
				'name'=>$phoen_produc_name,
				'total_stock'=>$phoen_stock_totle,
				'per_product'=>$phoen_add_price_val,
				'totle_price'=>$phoen_add_price
			
			);
		} 
	
	}
	
	function phoen_unsold_short($m, $p) {
		return $m['totle_price'] < $p['totle_price'];
	}
	
	usort($phoen_unsold, "phoen_unsold_short");  
	
	
	// sold product detais
	
	
$date_recent_froms_sold =isset($_POST['date_sold_from'])?$_POST['date_sold_from']:''; 

$date_recent_tos_sold = isset($_POST['date_sold_to'])?$_POST['date_sold_to']:''; 
  

if(($date_recent_froms_sold =='') ||($date_recent_tos_sold=='') )
{
	
	$date_sold_from = date("Y-m-d", strtotime($phoen_order_date));
	
	$phoen_reports_recent_current = date("Y-m-d"); 
	
	$date_sold_to = date("Y-m-d", strtotime($phoen_reports_recent_current));
	
	$years = date('Y-m',strtotime($phoen_reports_recent_current));
	
	$days = date('d',strtotime($phoen_reports_recent_current));
	
	if($days =='31')
	{
		$phoen_sold_to = $years."-".($days);
	
	}else{
		
		$phoen_sold_to = $years."-".($days+1);
	
	}

	
}else{
	
	$date_sold_from = date("Y-m-d", strtotime($date_recent_froms_sold));

	$date_sold_to = date("Y-m-d", strtotime($date_recent_tos_sold));
	
	
	$date_years_to = date("Y", strtotime($date_sold_to));

	$date_months_to = date("m", strtotime($date_sold_to));

	$date_days_to = date("d", strtotime($date_sold_to));
	
	if($date_days_to == '31')
	{ 
		/* if($date_years_to == '2016')
		{
			$phoen_recent_to = "2017"."-"."01"."-"."01";
			
		}else if($date_years_to == '2017')
		{
			$phoen_recent_to = $date_years_to."-".($date_months_to+1)."-"."01";
		} */
		
		if($date_months_to=='12'){
			$date_months_to=0;
			$date_years_to=$date_years_to+1;
			
		}
		
		$phoen_sold_to = $date_years_to."-".($date_months_to+1)."-"."01";
		
	}else{
		
		$phoen_sold_to = $date_years_to."-".$date_months_to."-".($date_days_to+1);
	}

}

	$phoen_get_recent_ordering_vals='DESC';
	$wpdb->get_results("SET SESSION sql_big_selects=on");
	$phoen_recent_order_querys_all_sold = "
		SELECT 
			first_name_meta.meta_value as first_name,
			last_name_meta.meta_value as last_name,
			posts.post_status as ordr_status,  
			billing_amount_meta.meta_value as billing_amount,  
			billing_email_meta.meta_value as billing_email,
			billing_address1_meta.meta_value as billing_address1,
			billing_address2_meta.meta_value as billing_address2,
			billing_phone_meta.meta_value as billing_phone_no,
			posts.post_date as order_date,
			posts.ID AS ID
			
			FROM    {$wpdb->posts} AS posts
			LEFT JOIN {$wpdb->postmeta} AS first_name_meta ON(posts.ID = first_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS last_name_meta ON(posts.ID = last_name_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_amount_meta ON(posts.ID = billing_amount_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_email_meta ON(posts.ID = billing_email_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_address1_meta ON(posts.ID = billing_address1_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_address2_meta ON(posts.ID = billing_address2_meta.post_id)
			LEFT JOIN {$wpdb->postmeta} AS billing_phone_meta ON(posts.ID = billing_phone_meta.post_id)
			
		WHERE 
			posts.post_status LIKE 'wc-%' 
			AND posts.post_type IN ( 'shop_order' ) 
			AND billing_amount_meta.meta_key IN ( '_order_total')
			
			AND last_name_meta.meta_key IN ( '_billing_last_name')
			AND first_name_meta.meta_key IN ( '_billing_first_name')
			AND billing_email_meta.meta_key IN ( '_billing_email')
			AND billing_address1_meta.meta_key IN ( '_billing_address_1')
			AND billing_address2_meta.meta_key IN ( '_billing_address_2')
			AND billing_phone_meta.meta_key IN ( '_billing_phone')
		
			AND posts.post_date >= '$date_sold_from' and posts.post_date <= '$phoen_sold_to'
			
		ORDER BY
			posts.ID $phoen_get_recent_ordering_vals
	";
	
	
	$phoen_recent_order_datas_all_sold_pro = $wpdb->get_results(  $phoen_recent_order_querys_all_sold,ARRAY_A);  
	
	$productId = 2888;
	$product = get_post_meta( $productId );
	
	$phoen_array_sold_data=array();
	
	$item_total=0;
	$item_count=0;
	foreach($phoen_recent_order_datas_all_sold_pro as $key =>$phoen_sold_pro_data)
	{
		$phoen_order_id = $phoen_sold_pro_data['ID'];
		
		$order = new WC_Order( $phoen_order_id );
		$items = $order->get_items(); 
		$item_quantity=0;
		foreach ( $items as $ke => $item ) {
			
			 $product_id = $item['product_id'];
			 if (in_array($product_id, $phoen_pro_id))
			{ 
				$item_quantity= $item['quantity'];
				$item_total= $item['total'];
				$item_name=$item['name'];
			
			 } 
			 //echo $item_name."<br />";
			 $phoen_array_sold_data[$item_count]=array(
			
					'sold_product_name'=>$item_name,
					'sold_product_total'=>$item_total,
					'sold_product_quantity'=>$item_quantity,
					'sold_product_id'=>$product_id
					
			
				);
		$item_count++;
		}

	}
	
	$phoen_array_sold_data_ar=array();
	$sold_product_total_val=0;
	$sold_product_quantity_val=0;

	foreach($phoen_array_sold_data as $ky=>$phoen_array_sold_datas)
	{
		
		$pro_id = $phoen_array_sold_datas['sold_product_id'];
	
		if (in_array($pro_id, $phoen_pro_id))
		{
			$sold_product_name_val = $phoen_array_sold_datas['sold_product_name'];
			
			//$sold_product_total_val+= $phoen_array_sold_datas['sold_product_total'];
			
			//$sold_product_quantity_val += $phoen_array_sold_datas['sold_product_quantity'];
			
			$pro_ids = $phoen_array_sold_datas['sold_product_id'];
			
			if(isset($phoen_array_sold_data_ar[$pro_id])){
				$quantity = $phoen_array_sold_data_ar[$pro_id]['sold_product_quantitys'];
			}else{
				$quantity =0;
			}
			
			if(isset($phoen_array_sold_data_ar[$pro_id])){
				$sold_product_totals = $phoen_array_sold_data_ar[$pro_id]['sold_product_totals'];
			}else{
				$sold_product_totals =0;
			}
			
				$phoen_array_sold_data_ar[$pro_id]=array(
				
					'sold_product_names'=>$sold_product_name_val,
					'sold_product_totals'=>$sold_product_totals+$phoen_array_sold_datas['sold_product_total'],
					'sold_product_quantitys'=>$quantity+$phoen_array_sold_datas['sold_product_quantity'],
					'sold_product_ids'=>$pro_ids
						
				);
			
			
		}
	
	}

	/* $phoen_array_sold_data_ar=array();
	$sold_product_total_val=0;
	$sold_product_quantity_val=0;
	
	foreach($phoen_array_sold_data as $ky=>$phoen_array_sold_datas)
	{
		
	
		$pro_id = $phoen_array_sold_datas['sold_product_id'];
	
		
		if (in_array($pro_id, $phoen_pro_id))
		{
			$sold_product_name_val = $phoen_array_sold_datas['sold_product_name'];
			$sold_product_total_val+= $phoen_array_sold_datas['sold_product_total'];
			$sold_product_quantity_val+= $phoen_array_sold_datas['sold_product_quantity'];
			
			
			$pro_ids = $phoen_array_sold_datas['sold_product_id'];
			
				
				$phoen_array_sold_data_ar[$ky]=array(
				
					'sold_product_names'=>$sold_product_name_val,
					'sold_product_totals'=>$sold_product_total_val,
					'sold_product_quantitys'=>$sold_product_quantity_val,
					'sold_product_ids'=>$pro_ids
						
				
			);	
			
		}
	
	} */
	
	?>
	
	<div class="phoe-war">
		<div class="phoe-report-wrap">
		<div class="row">
				<div class="col-sm-12 col-xs-12">
					<div class="phoe-war-coupns">
						
						<div class="tab-content">
								<div id="home"  class="tab-pane fade in active">
									<div class="phoe_today_summary_main">
										<h2><?php _e( 'Today Summary', 'advanced-reporting-for-woocommerce' ); ?></h2>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Todays Total Sale ', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														
																echo get_woocommerce_currency_symbol().($phoen_totle_day_amount);
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Todays Avg. Sales', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														
																if($phoen_totle_day_amount!='')
																{
																	$phoen_totle_today_avg_sale = ($phoen_totle_day_amount/$phoen_today_average_sale);
																	
																	$phoen_totle_today_avgs_sales=isset($phoen_totle_today_avg_sale)?$phoen_totle_today_avg_sale:'';
																	
																	$phoen_totle_today_avg_sales =  number_format((float)$phoen_totle_today_avgs_sales, 2, '.', '');
																	
																	echo get_woocommerce_currency_symbol().($phoen_totle_today_avg_sales);
																
																}else{
																	
																	echo get_woocommerce_currency_symbol()."0";
																}
															
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Todays Total Refund ', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														
																echo get_woocommerce_currency_symbol().($phoen_totle_refund_amount);
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Todays Order Tax', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														
																echo get_woocommerce_currency_symbol().($phoen_today_ord_tax);
														?>
													
													</span>
												</div>
											</div>
										</div>
										
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Todays Order Shipping Tax', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														
																echo get_woocommerce_currency_symbol().($phoen_today_order_ship_tax);
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Todays Total Tax', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														
															echo get_woocommerce_currency_symbol().($phoen_today_totle_tax);
														?>
													
													</span>
												</div>
											</div>
										</div>
									</div>
									
									<div class="phoe_mnth_and_year_summary_main">
										<h2><?php _e( 'Month and Year Summary', 'advanced-reporting-for-woocommerce' ); ?></h2>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Month Sales ', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<?php 
														$phoen_reporting_cureent_months = date("m"); 
														$phoen_reporting_cureent_year = date("Y");
														$dateObj   = DateTime::createFromFormat('!m', $phoen_reporting_cureent_months);
														$monthName = $dateObj->format('F'); 
														echo "(".$monthName.$phoen_reporting_cureent_year.")";
														
													?>
													
													<span>
													
														<?php 
														
																echo get_woocommerce_currency_symbol().($phoen_totle_month_amount);
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Avg. Sales/Order ', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<?php echo "(".$monthName.$phoen_reporting_cureent_year.")"; ?>
													
													<span>
													
														<?php 
														
														if(($phoen_totle_month_amount !='') && ($phoen_month_count !=''))
														{
															$phoen_avg_months_sale = $phoen_totle_month_amount/$phoen_month_count;
															$phoen_avg_months_sales =  number_format((float)$phoen_avg_months_sale, 2, '.', '');
															echo get_woocommerce_currency_symbol().($phoen_avg_months_sales);
														}
														
														
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Avg. Sales/Day ', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<?php echo "(".$monthName.$phoen_reporting_cureent_year.")"; ?>
													
													<span>
													
														<?php 
														$phoen_reports_cureent_day = date("d"); 
														$phoen_avg_months_sales = $phoen_totle_month_amount/$phoen_reports_cureent_day;
														$phoen_avgs_months_sales =  number_format((float)$phoen_avg_months_sales, 2, '.', '');
														echo get_woocommerce_currency_symbol().($phoen_avgs_months_sales);
														?>
													
													</span>
												</div>
											</div>
										</div>
										
										
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd"> 
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Year Sales ', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<?php 
														$phoen_reporting_cureent_year = date("Y"); 
														echo "(".$phoen_reporting_cureent_year.")"; 
														
													?>
													
													<span>
													
														<?php echo get_woocommerce_currency_symbol().($phoen_totle_year_amount); ?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd"> 
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Year Avg. Sales/Order', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<?php echo "(".$phoen_reporting_cureent_year.")";  ?>
												
													<span>
													
														<?php 
														
														if($phoen_year_count !='')
                                                       {
															if($phoen_refund_count !='')
															{
																$phoen_avg_year_sale = $phoen_totle_year_amount/($phoen_year_count+$phoen_refund_count);
																$phoen_rept_avg_year_sales =  number_format((float)$phoen_avg_year_sale, 2, '.', '');
																echo get_woocommerce_currency_symbol().($phoen_rept_avg_year_sales); 
															} 
															
														}	 
														
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd"> 
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Year Avg. Sales/Day', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<?php echo "(".$phoen_reporting_cureent_year.")";  ?>
												
													<span>
													
														<?php 
														$current_yer= date("Y");
														$phoen_year_date =$current_yer."-01-01";
														$date1=date_create($phoen_year_date);
														$current_date= date("Y-m-d");
														$date2=date_create($current_date);
														$diff=date_diff($date1,$date2);
														$pgf= $diff->format("%R%a days");
														$output = substr($pgf, 1, -1);
														$phoen_total_day_year = preg_replace('/\W\w+\s*(\W*)$/', '$1', $output);
														$phoen_avg_year_sale_year = $phoen_totle_year_amount/$phoen_total_day_year;
														$phoen_repts_avgs_years_sales =  number_format((float)$phoen_avg_year_sale_year, 2, '.', '');
														echo get_woocommerce_currency_symbol().($phoen_repts_avgs_years_sales); ?>
													
													</span>
												</div>
											</div>
										</div>
									</div>
									
									<div class="phoe_total_summary_main">
									<h2><?php _e( 'Total Summary', 'advanced-reporting-for-woocommerce' ); ?></h2>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-money"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Sales', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span><?php
												
													echo get_woocommerce_currency_symbol().($totle_billing_amount);?>
												
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-tags"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Refund', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												  
													<?php 
													
														echo get_woocommerce_currency_symbol().($phoen_totle_refund_amount_add);
													
													?>
												
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-tags"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Order Tax', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php
														 $phoen_ord_taxs=isset($phoen_ord_tax)?$phoen_ord_tax:'';
														 
														 $phoen_ord_taxs_totle =  number_format((float)$phoen_ord_taxs, 2, '.', '');

													echo get_woocommerce_currency_symbol().($phoen_ord_taxs_totle);?>
												
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-tags"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Order Shipping Tax', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php
														 $phoe_ship_taxs=isset($phoe_ship_tax)?$phoe_ship_tax:'';

													echo get_woocommerce_currency_symbol().($phoe_ship_taxs);?>
												
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-tags"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Tax', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php
														 $phoe_stotal_tax_add=isset($phoen_all_tax)?$phoen_all_tax:'';
														 
														 $phoe_stotal_tax_add_totle =  number_format((float)$phoe_stotal_tax_add, 2, '.', '');

													echo get_woocommerce_currency_symbol().($phoe_stotal_tax_add_totle);?>
												
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-tags"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Order Shipping Total', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php
														 $phoen_order_ship_amounts=isset($pho_order_shipf)?$pho_order_shipf:'';

													echo get_woocommerce_currency_symbol().($phoen_order_ship_amounts);?>
												
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-tags"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Last Order Date', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php
													$phoen_last_order_date_year = date('Y',strtotime($phoen_last_order_dates));
													
													$phoen_last_order_date_months = date('m',strtotime($phoen_last_order_dates));
													
													$phoen_last_order_date_day = date('d',strtotime($phoen_last_order_dates));
												
													$phoe_order_date_dateObj   = DateTime::createFromFormat('!m', $phoen_last_order_date_months);
													$phoen_monthName = $phoe_order_date_dateObj->format('F'); 
												
													echo $phoen_monthName." ".$phoen_last_order_date_day .",".$phoen_last_order_date_year; 
													
													
													?>
												
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">	
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-line-chart"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Coupons', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
												
													<?php

													$phoe_scupan_total=isset($phoen_toatal_cupan_amount)?$phoen_toatal_cupan_amount:'';
													
													echo get_woocommerce_currency_symbol().($phoe_scupan_total);?>
													
												</span>
											
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-tags"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Registered', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php 
													   
													   $phoe_stotal_registed=isset($total_registed)?$total_registed:'';
													
														echo $phoe_stotal_registed; ?>
												
												</span>
											</div>
										</div>
									</div>
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-pie-chart"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Guest Customers', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php 
													
														$phoen_sguest_user=isset($phoen_guest_user)?$phoen_guest_user:'';
														
														
														echo $phoen_sguest_user;

													?>
												
												</span>
											</div>
										</div>
									</div>
									
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-pie-chart"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Average Gross Monthly Sales ', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php 
													
														echo get_woocommerce_currency_symbol().($total_sale_amount);
													?>
												
												</span>
											</div>
										</div>
									</div>
									
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-pie-chart"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Average Net Monthly Sales ', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php 
													
															echo get_woocommerce_currency_symbol().($phoen_net_sale+$phe_datas);
													?>
												
												</span>
											</div>
										</div>
									</div>
									
									<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
										<div class="phoe-war-top-sec">
											<div class="phoe-war-top-sec-load">
												<span class="fa fa-pie-chart"></span>
											</div>
											<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Items Purchased ', 'advanced-reporting-for-woocommerce' ); ?></h1>
												<span>
												
													<?php 
											
														echo $phoen_totle_year_sale_items_count;
													?>
												
												</span>
											</div>
										</div>
									</div>
									
									</div>
									
									<div class="phoe_today_summary_main">
										<h2><?php _e( 'Product Summary ', 'advanced-reporting-for-woocommerce' ); ?></h2>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Total Variable Instock ', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														        echo $phoen_product_name_instock;
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Variable Out Of Stock', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														
															echo $phoen_product_name_outstock;
														
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
													<h1><?php _e( 'Total Simple Instock', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														
															echo $phoen_product_name_simple_instock;
														
														?>
													
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4 col-xs-12 phoe-war-top-sec-padd">
											<div class="phoe-war-top-sec">
												<div class="phoe-war-top-sec-load">
													<span class="fa fa-pie-chart"></span>
												</div>
												<div class="phoe-war-top-sec-text">
												<h1><?php _e( 'Total Simple Out Of Stock', 'advanced-reporting-for-woocommerce' ); ?></h1>
													<span>
													
														<?php 
														
																echo $phoen_product_name_simple_outstock;
														?>
													
													</span>
												</div>
											</div>
										</div>
									
									</div>
								
							</div>
						</div>
					</div>
				
			</div>
			
			<div class="col-sm-6 col-xs-12 phoe-war-ear-main">
						<div class="phoe-war-ear-sec-head">
							<h2><?php _e( 'Order Today', 'advanced-reporting-for-woocommerce' ); ?></h2>
						</div>
						<div class="phoe-war-ear-sec phoe-order-today">
							<span class="fa fa-shopping-cart"></span>
							<h3><?php echo $phoen_day_count; ?></h3>
							<p><?php _e( 'New Orders', 'advanced-reporting-for-woocommerce' ); ?></p>
						</div>
						<div class="phoe-war-last">
							<p><?php _e( 'Last Day:', 'advanced-reporting-for-woocommerce' ); ?><span><?php echo $phoen_yesterday_count; ?></span></p>
						</div>
			</div>
			<div class="col-sm-6 col-xs-12 phoe-war-ear-main">  
				<div class="phoe-war-ear-sec-head">
					<h2><?php _e( 'Earnings Today', 'advanced-reporting-for-woocommerce' ); ?></h2>
				</div>
				<div class="phoe-war-ear-sec phoe-order-today">
					<span class="fa fa-usd"></span>
					<h3><?php echo get_woocommerce_currency_symbol().($phoen_totle_day_amount);?></h3>
					<p><?php _e( 'Earning', 'advanced-reporting-for-woocommerce' ); ?></p>
				</div>
				<div class="phoe-war-last">
					<p><?php _e( 'Last Day:', 'advanced-reporting-for-woocommerce' ); ?> <span><?php echo get_woocommerce_currency_symbol().($phoen_totle_yesterday_amount);?></span></p>
				</div>
			</div>

		</div>
	
			<div class="row">
				<div class="col-sm-12 col-xs-12">
					<div class="phoe-war-latest-prjct-main">
					<div class="phoe-war-latest-prjct-head">
					<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse8"><?php _e('Summary Of The Year', 'advanced-reporting-for-woocommerce' ); ?>
										<span class="fa fa-caret-down"></span>
									</a>
								</h4>
							</div>
							
							<ul class="nav nav-tabs">
								<li class="active"><a href="#home_201" data-toggle="tab"><span class="fa fa-table"></span></a></li>
								<li><a href="#menu_202" data-toggle="tab"><span class="fa fa-bar-chart"></span></a></li>
								<li><a href="phoen-repot-month.csv"><span class="fa fa-download"></span></a></li>
							</ul>
							
						<div class="phoe-war-ear-sec">
						<div id="collapse8" class="panel-collapse collapse in ">
						<div class="tab-content">
						<div id="home_201" class="tab-pane fade in active">
							<table class="table table-striped table-bordered widefat" id="phoen_year_data_table">
									<thead>
									  <tr>
										<th width="50px"><?php _e( 'Month', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="100px"><?php _e( 'Total Sales Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php _e( 'Total Refund Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php _e( 'Total Discount Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php  _e( 'Shipping Order', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php _e( 'Order Tax', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php  _e( 'Total Shipping Tax', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php _e( 'Total Tax Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
									  </tr>
									</thead>
									<tbody>
										<?php
											$phoen_year_csv=array();
											$kayy='0';
											foreach($phoen_year_month_data as $kay=>$phoen_year_month_datas) 
											{
												?>
												<tr class="phoen_year_data_tr">
												
													<td><?php  $name = ($phoen_year_month_datas['month_name']);
															echo $name."-".$phoen_reporting_cureent_year;

													?></td>
													
													<td><?php if ($phoen_year_month_datas['total_sales_amt']!='')
													{
														$totle_sale = $phoen_year_month_datas['total_sales_amt'];
														echo get_woocommerce_currency_symbol().($phoen_year_month_datas['total_sales_amt']);

													}else{
														echo get_woocommerce_currency_symbol().'0';
														$totle_sale=0;
													}	
														?></td>
												
													<td><?php if($phoen_year_month_datas['total_refund_amt']!='')
													{
														$refund = $phoen_year_month_datas['total_refund_amt'];
														echo get_woocommerce_currency_symbol().($phoen_year_month_datas['total_refund_amt']);
														
													}else{
														echo get_woocommerce_currency_symbol().'0';
														$refund=0;
													}
													 ?></td>
													
													<td><?php if($phoen_year_month_datas['total_discount_amt']!='')
													{
														$discount = $phoen_year_month_datas['total_discount_amt']	; 
														echo get_woocommerce_currency_symbol().($phoen_year_month_datas['total_discount_amt'])	; 
													
													}else{
														echo get_woocommerce_currency_symbol().'0';
														$discount=0;
													}?></td>
													
													<td><?php if($phoen_year_month_datas['shipping_order']!='')
													{
														$shiping_order = $phoen_year_month_datas['shipping_order'] ; 
														echo get_woocommerce_currency_symbol().($phoen_year_month_datas['shipping_order']) ; 
														
													}else{
														echo get_woocommerce_currency_symbol().'0';
														$shiping_order=0;
													}
													?></td>
													
													<td><?php if($phoen_year_month_datas['order_tax']!='') 
													{
														$order_taxx = $phoen_year_month_datas['order_tax'] ;
														echo get_woocommerce_currency_symbol().($phoen_year_month_datas['order_tax']) ;
													}else{
														echo get_woocommerce_currency_symbol().'0';
														$order_taxx=0;
													}
													?></td>
													
													<td><?php if($phoen_year_month_datas['total_shipping_tax']!='')
													{
														$shiping_taxx = $phoen_year_month_datas['total_shipping_tax'] ;
														echo get_woocommerce_currency_symbol().($phoen_year_month_datas['total_shipping_tax']) ;
													}else{
														echo get_woocommerce_currency_symbol().'0';
														$shiping_taxx=0;
													}														 ?></td>
													
													<td><?php if($phoen_year_month_datas['total_tax']!='')
													{
														$totle_taxx = $phoen_year_month_datas['total_tax'];
														echo get_woocommerce_currency_symbol().($phoen_year_month_datas['total_tax']);
													}else{
														
														echo get_woocommerce_currency_symbol().'0';
														$totle_taxx=0;
													}												
													?></td>
													
												</tr>
												<?php
											
												$phoen_year_csv[$kayy]=array(
													'name'=>$name,
													'totle_sale'=>$totle_sale,
													'refund'=>$refund,
													'discount'=>$discount,
													'shiping_order'=>$shiping_order,
													'order_taxx'=>$order_taxx,
													'shiping_taxx'=>$shiping_taxx,
													'totle_taxx'=>$totle_taxx
												);
												$kayy++;
											}
											$phoen_month_file = fopen('phoen-repot-month.csv', 'w');
																					
												fputcsv($phoen_month_file, array('Month', 'Total Sales Amt', 'Total Refund Amt', 'Total Discount Amt', 'Shipping Order', 'Order Tax', 'Total Shipping Tax', 'Total Tax Amt'));
												 
												foreach ($phoen_year_csv as $phoen_year_csv_row)
												{
													fputcsv($phoen_month_file, $phoen_year_csv_row);
												}
												
												fclose($phoen_month_file);	
											
											?>
										
									</tbody>
							</table>
							
							</div>
							<div id="menu_202" class="tab-pane fade">
								<div id="chart_mnth"></div>
							</div>
							
							<div id="menu_203" class="tab-pane fade">
								<?php _e( 'Download 1', 'advanced-reporting-for-woocommerce' ); ?>
							</div>
							</div>
							</div>
						</div>
					</div>
					</div>
				</div>
			</div>
			</div>
			
				<div class="row">
						
					<div class="col-sm-6 col-xs-12 phoe-war-top-tens-main phoe_big_expand">
	
							<div class="panel panel-default">
								<div class="panel-heading">
									<a data-toggle="collapse" href="#collapse_one"><?php _e('Order Summary', 'advanced-reporting-for-woocommerce' ); ?>
										<span class="fa fa-caret-down"></span>
									</a>
								</div>
								<ul class="nav nav-tabs">
									<li class="active"><a href="#homett1" data-toggle="tab"><span class="fa fa-table"></span></a></li>
									<li><a href="#menutt2" data-toggle="tab"><span class="fa fa-bar-chart"></span></a></li>
									<li><a href="phoen-repot-order.csv"><span class="fa fa-download"></span></a></li>
									<li><a><span class="fa fa-arrows-alt phoe_expand" data-id="phoe_big_expand"></span></a></li>
								</ul>
							
								<div id="collapse_one" class="panel-collapse collapse in">
									<div class="tab-content">
										<div id="homett1" class="tab-pane fade in active">
											<table class="table table-striped table-bordered" id="phoen_order_tblData">
												<thead>
												  <tr>
													<th><?php _e( 'Sales Order', 'advanced-reporting-for-woocommerce' ); ?></th>
													<th><?php _e( 'Qty', 'advanced-reporting-for-woocommerce' ); ?></th>
													<th><?php _e( 'Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
												  </tr>
												</thead>
												<tbody>
												
												<?php
												$phoen_order_csv=array();
												
												$keyss=0;
												
												foreach($phoen_reporting_all_data as $key => $phoen_reporting_all_datas)
												{
													if($phoen_reporting_all_datas['phoen_name']!='')
													{
														
														?>
														<tr class="phoen_order_tr"> 
														
															<td>
															<?php echo $phoen_reporting_all_datas['phoen_name'] ;?>
															</td>
															
															<td>
															<?php echo $phoen_reporting_all_datas['phoen_count'] ;?>
															</td>
															
															<td>
																<?php echo get_woocommerce_currency_symbol().($phoen_reporting_all_datas['phoen_amount']);?>
															</td>
														
														</tr>	
														
														<?php
														
														
														
														$phoen_order_csv[$keyss]=array(
															
															'name'=>$phoen_reporting_all_datas['phoen_name'],
															'order_count'=>$phoen_reporting_all_datas['phoen_count'],
															'amount'=>$phoen_reporting_all_datas['phoen_amount']
														
														
														);
														
														$keyss++;
													}
												}
												
												$phoen_order_file = fopen('phoen-repot-order.csv', 'w');
																					
												fputcsv($phoen_order_file, array('Sales Order', 'Qty', 'Amount'));
												 
												foreach ($phoen_order_csv as $phoen_order_row)
												{
													fputcsv($phoen_order_file, $phoen_order_row);
												}
												
												fclose($phoen_order_file);	
												
												?>
												</tbody>
											
											</table>
										</div>
										
										<div id="menutt2" class="tab-pane fade">
											<div id="chart_order"></div>
										</div>
								
										<div id="menutt3" class="tab-pane fade">
										<?php _e( 'Download', 'advanced-reporting-for-woocommerce' ); ?>
										</div>
										
									</div>
								</div>
							</div>
		
						</div>
						
		
					<div class="col-sm-6 col-xs-12 phoe-war-top-tens-main phoen_report_date_bott phoe_big_expand1">
					<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse2"><?php _e('Sales Order Status', 'advanced-reporting-for-woocommerce' ); ?> <span class="fa fa-caret-down"></span></a>
									
								</h4>
							</div>
							
							
							<ul class="nav nav-tabs">
								<li class="active"><a href="#home_21" data-toggle="tab"><span class="fa fa-table"></span></a></li>
								<li><a href="#menu_22" data-toggle="tab"><span class="fa fa-bar-chart"></span></a></li>
								<li><a href="phoen-repot-order-status.csv"><span class="fa fa-download"></span></a></li>
								<li><a><span class="fa fa-arrows-alt phoe_expand" data-id="phoe_big_expand1"></span></a></li>
							</ul>
							
							<div id="collapse2" class="panel-collapse collapse in">
								<div class="tab-content">
									<div id="home_21" class="tab-pane fade in active">
									
										<form method="POST" class="phoen_report_date">  
											<div class="phoen_recent_order from">
												<label for="from_date"><?php _e( 'From', 'advanced-reporting-for-woocommerce' ); ?> </label>
												<input type="text" class="datepicker" name="date_status_from" value="<?php echo $date_status_from ; ?>">
												
											</div>
											<div class ="phoen_recent_order to">
												<label for="to_date"><?php _e( 'To', 'advanced-reporting-for-woocommerce' ); ?> </label>
												<input type="text" class="datepicker" name="date_status_to" value="<?php echo $date_status_to ; ?>">
											
											</div>
											
											<div class="phoe_date_form_submit_btn">
											
												<input type="submit" name="submit_status_order" value="">
											
											</div>
										</form>										
											<table class="table table-striped table-bordered" id="phoen_order_status_table">
												<thead>
												  <tr>
													<th><?php _e( 'Order Status', 'advanced-reporting-for-woocommerce' ); ?></th>
													<th><?php _e( 'Qty', 'advanced-reporting-for-woocommerce' ); ?></th>
													<th><?php _e( 'Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
												  </tr>
												</thead>
												<tbody>
													<?php
													$phoen_repot_status_csv=array();
													$phoen_status_count=0;
												
													foreach($phoen_status_repot as $key => $phoen_reportings_status_data)
													{
														
														if($phoen_reportings_status_data['status_name']!='')
														{
															
															?>
															<tr class="phoen_order_status_tr">
															
																
																<td><?php 
																
																$phoen_status_name = $phoen_reportings_status_data['status_name'] ;
																$phoen_status_str = substr($phoen_status_name, 3);
																
																echo $phoen_status_str;
																
																
																?>
																
																</td>
																
																
																<td> <?php echo $phoen_reportings_status_data['status_count'] ;?> </td>
																
																
																<td><?php echo get_woocommerce_currency_symbol().($phoen_reportings_status_data['status_amount']);?> </td>
																
															
															</tr>	
															
															<?php
															
															$phoen_repot_status_csv[$key]=array(
																
																'name'=>$phoen_status_str,
																'count'=>$phoen_reportings_status_data['status_count'],
																'amount'=>$phoen_reportings_status_data['status_amount']
															
															);
														}
													
														$phoen_status_count++;
														
													}
													
													$phoen_order_status_file = fopen('phoen-repot-order-status.csv', 'w');
																					
													fputcsv($phoen_order_status_file, array('Order Status', 'Qty', 'Amount'));
												 
													foreach ($phoen_repot_status_csv as $phoen_order_status_row)
													{
														fputcsv($phoen_order_status_file, $phoen_order_status_row);
													}
												
													fclose($phoen_order_status_file);
												
													 ?>
														
												</tbody>
											</table>
											
									</div>
											
										<div id="menu_22" class="tab-pane fade">
										
											<div id="chart_sale_order"></div>
										
										</div>
										
										<div id="menu_23" class="tab-pane fade">
											<?php _e( 'Download 1', 'advanced-reporting-for-woocommerce' ); ?>
										</div>
										
									
								</div>
							</div>
					</div>
					</div>
					</div>
				
				<div class="row">
				
				<div class="col-sm-6 col-xs-12 phoe-war-top-tens-main phoe-bott-gap">
					

						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
								<a data-toggle="collapse" href="#collapse1"><?php _e('Top Products', 'advanced-reporting-for-woocommerce' ); ?>
									<span class="fa fa-caret-down"></span>
								</a>
								</h4>
							</div>
						
						<ul class="nav nav-tabs">
							<li class="active"><a href="#home_31" data-toggle="tab"><span class="fa fa-table"></span></a></li>
							<li><a href="phoen-repot-product.csv"><span class="fa fa-download"></span></a></li>
						</ul>
						<?php
							if(isset($_POST['report_limit_submit']))
							{
								$phoen_prod_value = $_POST['phoen_ten_product']; 
								
								update_option( 'phoen_top_productd', $phoen_prod_value );
						
							}
							
							$phoen_get_product_value = get_option( 'phoen_top_productd' );
							
							if($phoen_get_product_value=='')
							{
								$phoen_get_product_val='10';
								
							}else{
								
								$phoen_get_product_val = get_option( 'phoen_top_productd' );
							}
						
						?>
						

							<div id="collapse1" class="panel-collapse collapse in">
								<div class="tab-content">
									<div id="home_31" class="tab-pane fade in active">
									
										<div class="phoen_report_data clearfix">
											<input type="search" placeholder="<?php _e( 'Search', 'advanced-reporting-for-woocommerce' ); ?>" class="phoen_top_product">
											<form method="POST">	
												<select name="phoen_ten_product" class="phoen_product_limit">
													<option <?php echo ($phoen_get_product_val == '10')?'selected':'';?>><?php _e( '10', 'advanced-reporting-for-woocommerce' ); ?></option>
													<option <?php echo ($phoen_get_product_val == '20')?'selected':'';?>><?php _e( '20', 'advanced-reporting-for-woocommerce' ); ?></option>
													<option <?php echo ($phoen_get_product_val == '50')?'selected':'';?>><?php _e( '50', 'advanced-reporting-for-woocommerce' ); ?></option>
													<option <?php echo ($phoen_get_product_val == '100')?'selected':'';?>><?php _e( '100', 'advanced-reporting-for-woocommerce' ); ?></option>
													<option <?php echo ($phoen_get_product_val == 'View All')?'selected':'';?>><?php _e( 'View All', 'advanced-reporting-for-woocommerce' ); ?></option>
												</select>
												<input type="submit" name="report_limit_submit" value="<?php _e( 'submit', 'advanced-reporting-for-woocommerce' ); ?>"> 
											</form>
											
										</div>
									
										<table class="table table-striped table-bordered" id="phoen_top_product_table">
											<thead>
											  <tr>
												<th><?php _e( 'Product Name', 'advanced-reporting-for-woocommerce' ); ?></th>
												<th><?php _e( 'Sku', 'advanced-reporting-for-woocommerce' ); ?></th>
												<th><?php _e( 'Qty', 'advanced-reporting-for-woocommerce' ); ?></th>
												<th><?php _e( 'Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
											  </tr>
											</thead>
											<tbody>
											<?php
											$phoen_repot_product=array();
											$phoen_product_count=0;
											
											for($i=0; $i<count($phoen_top_products); $i++)
											{
												
												
												 $phoen_product_ID=isset($phoen_top_products[$i]['ID'])?$phoen_top_products[$i]['ID']:'';
												 
												$get_post_sku =  get_post_meta($phoen_product_ID);
												
												
												
												if($phoen_get_product_val!='View All')
												{
													if($phoen_product_count<$phoen_get_product_val)
													{
														
														?>
														<tr class="phoen_top_product_tr">
															<td>
															
																<?php $phoen_product_title=isset($phoen_top_products[$i]['product_name'])?$phoen_top_products[$i]['product_name']:'';
																
																echo $phoen_product_title; ?> 
																
															</td>
															
															<td>
															
																<?php $get_post_sku_value =($get_post_sku['_sku'][0])?$get_post_sku['_sku'][0]:'';
																if($get_post_sku_value=='')
																{
																	echo $get_post_sku_value="-";
																}else{
																	echo $get_post_sku_value;
																}
																 ?> 
																
															</td>
															
															<td>
															
																<?php 
																
																	$phoen_product_quentity=isset($phoen_top_products[$i]['product_count'])?$phoen_top_products[$i]['product_count']:'';     
																
																	echo $phoen_product_quentity; 
															
																?>
															
															</td>
															
															<td>
																<?php 
																
																$phoen_total_product_price=isset($phoen_top_products[$i]['produc_total'])?$phoen_top_products[$i]['produc_total']:'';
																
																	echo get_woocommerce_currency_symbol().($phoen_total_product_price);
																
																?>
															</td>
															
														
														</tr>	
														
														<?php
														
														$phoen_repot_product[$i]= array(
														
															'name'=>$phoen_product_title,
															'sku'=>$get_post_sku_value,
															'product_count'=>$phoen_product_quentity,
															'amount'=>$phoen_total_product_price
														
														);
													}
														$phoen_product_count++;
												}else{
													?>
														<tr class="phoen_top_product_tr">
															<td>
															
																<?php $phoen_product_title=isset($phoen_top_products[$i]['product_name'])?$phoen_top_products[$i]['product_name']:'';
																
																echo $phoen_product_title; ?> 
																
															</td>
															
															<td>
															
																<?php $get_post_sku_value =($get_post_sku['_sku'][0])?$get_post_sku['_sku'][0]:'';
																
																if($get_post_sku_value=='')
																{
																	echo $get_post_sku_value="-";
																}else{
																	echo $get_post_sku_value;
																}
																?> 
																
															</td>
															
															<td>
															
																<?php 
																
																	$phoen_product_quentity=isset($phoen_top_products[$i]['product_count'])?$phoen_top_products[$i]['product_count']:'';     
																
																	echo $phoen_product_quentity; 
															
																?>
															
															</td>
															
															<td>
																<?php 
																
																$phoen_total_product_price=isset($phoen_top_products[$i]['produc_total'])?$phoen_top_products[$i]['produc_total']:'';
																
																	echo get_woocommerce_currency_symbol().($phoen_total_product_price);
																
																?>
															</td>
															
														
														</tr>	
														
														<?php
														
														$phoen_repot_product[$i]= array(
														
															'name'=>$phoen_product_title,
															'sku'=>$get_post_sku_value,
															'product_count'=>$phoen_product_quentity,
															'amount'=>$phoen_total_product_price
														
														);
												}
												
													
											}
											
											$phoen_product_file = fopen('phoen-repot-product.csv', 'w');
																					
											fputcsv($phoen_product_file, array('Product Name', 'Sku', 'Qty', 'Amount'));
											 
											foreach ($phoen_repot_product as $phoen_product_row)
											{
												fputcsv($phoen_product_file, $phoen_product_row);
											}
											
											fclose($phoen_product_file);	
			 
											 ?>
											
											</tbody>
										
										</table>
									</div>
									
								</div>
								
							</div>
						
						</div>
					

					
				</div>
				<div class="col-sm-6 col-xs-12 phoe-war-top-tens-main phoe-bott-gap">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
							<a data-toggle="collapse" href="#collapse27"><?php _e('Top Category', 'advanced-reporting-for-woocommerce' ); ?> <span class="fa fa-caret-down"></span></a>
							</h4>
						</div>
						
						<ul class="nav nav-tabs">
							<li class="active"><a href="#home_41" data-toggle="tab"><span class="fa fa-table"></span></a></li>
							<li><a href="phoen-repot-category.csv"><span class="fa fa-download"></span></a></li>
						</ul>
						

						<div id="collapse27" class="panel-collapse collapse in">
							<div class="tab-content">
								<div id="home_41" class="tab-pane fade in active">
								
									<div class="phoen_report_data clearfix">
										<?php
											if(isset($_POST['report_limit_submit_cat']))
											{
												$phoen_cat_value =$_POST['phoen_ten_cat']; 
												
												update_option( 'phoen_top_cat', $phoen_cat_value );
												
											}
											
											$phoen_get_cat_value = get_option( 'phoen_top_cat' );
											
											if($phoen_get_cat_value=='')
											{
												$phoen_get_cat_val='10';
												
											}else{
												
												$phoen_get_cat_val = get_option( 'phoen_top_cat' );
											}
											
										?>
											
										<form method="POST">	
											<select name="phoen_ten_cat" class="phoen_cat_limit">
												<option <?php echo ($phoen_get_cat_val == '10')?'selected':'';?>><?php _e( '10', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_cat_val == '20')?'selected':'';?>><?php _e( '20', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_cat_val == '50')?'selected':'';?>><?php _e( '50', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_cat_val == '100')?'selected':'';?>><?php _e( '100', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_cat_val == 'View All')?'selected':'';?>><?php _e( 'View All', 'advanced-reporting-for-woocommerce' ); ?></option>
											</select>
											<input type="submit" name="report_limit_submit_cat" value="<?php _e( 'submit', 'advanced-reporting-for-woocommerce' ); ?>"> 
										</form>
										
										<input type="search" placeholder="<?php _e( 'Search', 'advanced-reporting-for-woocommerce' ); ?>" class="phoen_top_category">
									
									</div>
								
								  <table class="table table-striped table-bordered" id="phoen_top_category_table">
										<thead>
										  <tr>
											<th><?php _e( 'Category Name', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th><?php _e( 'Qty', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th><?php _e( 'Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
										  </tr>
										</thead>
										<tbody>
											
													<?php 
													$phoen_cat=0;
													for($i=0; $i<count($phoen_all_cat_data); $i++)
													{
														if($phoen_get_cat_val!='View All')
														{
															if($phoen_cat<$phoen_get_cat_val)
															{
																
															
																?>
																<tr class="phoen_top_category_tr">
																	<td>
																	<?php
																		
																		$pho_cat_name=isset($phoen_all_cat_data[$i]['category_names'])?$phoen_all_cat_data[$i]['category_names']:'';
																		
																		echo $pho_cat_name;
																	?>
																	</td>
																
																	<td>
																		<?php
																		
																		$phoe_sale_counts=isset($phoen_all_cat_data[$i]['total_sale_counts'])?$phoen_all_cat_data[$i]['total_sale_counts']:'';
																		echo $phoe_sale_counts;
																		?>
																	</td>
																	
																	<td>
																		<?php 
																		
																		$pho_totale_sale_amount=isset($phoen_all_cat_data[$i]['total_amount'])?$phoen_all_cat_data[$i]['total_amount']:'';
																	
																		echo get_woocommerce_currency_symbol().($pho_totale_sale_amount);
																	
																		?>
																	</td>
																
																</tr>
															
														<?php
															}
															$phoen_cat++;
														}else{
															?>
															<tr class="phoen_top_category_tr">
																<td>
																<?php
																	
																	$pho_cat_name=isset($phoen_all_cat_data[$i]['category_names'])?$phoen_all_cat_data[$i]['category_names']:'';
																	
																	echo $pho_cat_name;
																?>
																</td>
															
																<td>
																	<?php
																	
																	$phoe_sale_counts=isset($phoen_all_cat_data[$i]['total_sale_counts'])?$phoen_all_cat_data[$i]['total_sale_counts']:'';
																	echo $phoe_sale_counts;
																	?>
																</td>
																
																<td>
																	<?php 
																	
																	$pho_totale_sale_amount=isset($phoen_all_cat_data[$i]['total_amount'])?$phoen_all_cat_data[$i]['total_amount']:'';
																
																	echo get_woocommerce_currency_symbol().($pho_totale_sale_amount);
																
																	?>
																</td>
															
															</tr>
														
														<?php
														}
														
													}
													
													$phoen_category_file = fopen('phoen-repot-category.csv', 'w');
																					
													fputcsv($phoen_category_file, array('Category Name', 'Qty', 'Amount'));
													 
													foreach ($phoen_all_cat_data as $phoen_category_row)
													{
														if($phoen_get_cat_val!='View All')
														{
															if($phoen_cat<$phoen_get_cat_val)
															{
																fputcsv($phoen_category_file, $phoen_category_row);
															}
															$phoen_cat++;
														}else{
															fputcsv($phoen_category_file, $phoen_category_row);
														}
														
													}
													
													fclose($phoen_category_file);	
													
													?>
												
										</tbody>
									</table>
								</div>
								
							</div>
						</div>
				</div>
				</div>
					
					
				</div>
				
				
			<div class="row">
				
				
				<div class="col-sm-12 col-xs-12 phoe-war-top-tens-main phoe-bott-gap">
				
					<div class="panel panel-default phoe-top-ten-customers">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" href="#collapse29"><?php _e( 'Top Customer', 'advanced-reporting-for-woocommerce' ); ?>
									<span class="fa fa-caret-down"></span>
								</a>
							</h4>
							
							
						</div>
						
						<ul class="nav nav-tabs">
							<li class="active"><a href="#home_51" data-toggle="tab"><span class="fa fa-table"></span></a></li>
							<li><a href="phoen-repot-customer.csv"><span class="fa fa-download"></span></a></li>
						</ul>
						
						
						<div id="collapse29" class="panel-collapse collapse in">
							<div class="tab-content">
								<div id="home_51" class="tab-pane fade in active">

									
									<div class="phoen_report_data clearfix">
								
										<?php
											if(isset($_POST['report_limit_submit_customer']))
											{
												$phoen_customer_value =$_POST['phoen_ten_customer']; 
												
												update_option( 'phoen_top_customer', $phoen_customer_value );
												
											}
											
											$phoen_get_customer_value = get_option( 'phoen_top_customer' );
											
											if($phoen_get_customer_value=='')
											{
												$phoen_get_customer_val='10';
												
											}else{
												
												$phoen_get_customer_val = get_option( 'phoen_top_customer' );
											}
											
										?>
											
										<form method="POST">	
											<select name="phoen_ten_customer" class="phoen_cat_limit">
												<option <?php echo ($phoen_get_customer_val == '10')?'selected':'';?>><?php _e( '10', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_customer_val == '20')?'selected':'';?>><?php _e( '20', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_customer_val == '50')?'selected':'';?>><?php _e( '50', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_customer_val == '100')?'selected':'';?>><?php _e( '100', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_customer_val == 'View All')?'selected':'';?>><?php _e( 'View All', 'advanced-reporting-for-woocommerce' ); ?></option>
											</select>
											<input type="submit" name="report_limit_submit_customer" value="<?php _e( 'submit', 'advanced-reporting-for-woocommerce' ); ?>"> 
										</form>
										
										<input type="search" placeholder="<?php _e( 'Search', 'advanced-reporting-for-woocommerce' ); ?>" class="phoen_search_customet">
									
									</div>
									
									<form method="POST" class="phoen_report_date">
										<div class="phoen_recent_order from">
											<label for="from_date"><?php _e( 'From', 'advanced-reporting-for-woocommerce' ); ?> </label>
											<input type="text" class="datepicker" name="date_customer_from" value="<?php echo $date_customer_from ; ?>">
											
										</div>
										<div class ="phoen_recent_order to">
											<label for="to_date"><?php _e( 'To', 'advanced-reporting-for-woocommerce' ); ?></label>
											<input type="text" class="datepicker" name="date_customer_to" value="<?php echo $date_customer_to ; ?>">
										
										</div>
										<div class="phoe_date_form_submit_btn">
											<input type="submit" name="submit_recent_order" value="">
										</div>
									</form>
									
								
									<table class="table table-striped table-bordered" id="phoen_customer_table">
										<thead>
										
										  <tr>
										  
											<th width="30%" ><?php _e( 'Billing Name', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th width="30%" ><?php _e( 'Billing Email', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th width="30%" ><?php _e( 'Payment Method', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th width="20%" ><?php _e( 'Order Count', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th width="20%" ><?php _e( 'Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
										 
										 </tr>
										  
										</thead>
										
										<tbody>
										
									
											<?php  
											
													$phoen_repot_customer_csv=array();
													$phoen_customer_count=0;
													
													foreach($phoen_top_customer as $ksy=>$phoen_top_customers)
													{ 
													
														if($phoen_get_customer_val!='View All')
														{
															if($phoen_customer_count<$phoen_get_customer_val)
															{
														
																?>		
																	<tr class="phoen_customer_tr">
																		<td><?php echo $phoen_top_customers['customer_fname']." ".$phoen_top_customers['customer_lname']; ?></td>
																		<td><?php echo $phoen_top_customers['customer_email'];?></td>
																		<td><?php echo $phoen_top_customers['customer_payment_method'];?></td>
																		<td><?php echo $phoen_top_customers['totle_cust_order_count'];?></td> 
																		<td><?php echo get_woocommerce_currency_symbol().$phoen_top_customers['total_customer_amoun'];?></td>
																		
																	</tr>
																	
																<?php
																
																$phoen_repot_customer_csv[$ksy]=array(
																
																	'name'=>$phoen_top_customers['customer_fname']." ".$phoen_top_customers['customer_lname'],
																	'email'=>$phoen_top_customers['customer_email'],
																	'payment_method'=>$phoen_top_customers['customer_payment_method'],
																	'order_count'=>$phoen_top_customers['totle_cust_order_count'],
																	'amount'=>$phoen_top_customers['total_customer_amoun']
																
																
																);
															}
															$phoen_customer_count++;	
														}else{
															?>		
																	<tr class="phoen_customer_tr">
																		<td><?php echo $phoen_top_customers['customer_fname']." ".$phoen_top_customers['customer_lname']; ?></td>
																		<td><?php echo $phoen_top_customers['customer_email'];?></td>
																		<td><?php echo $phoen_top_customers['customer_payment_method'];?></td>
																		<td><?php echo $phoen_top_customers['totle_cust_order_count'];?></td> 
																		<td><?php echo get_woocommerce_currency_symbol().$phoen_top_customers['total_customer_amoun'];?></td>
																		
																	</tr>
																	
																<?php
																
																$phoen_repot_customer_csv[$ksy]=array(
																
																	'name'=>$phoen_top_customers['customer_fname']." ".$phoen_top_customers['customer_lname'],
																	'email'=>$phoen_top_customers['customer_email'],
																	'payment_method'=>$phoen_top_customers['customer_payment_method'],
																	'order_count'=>$phoen_top_customers['totle_cust_order_count'],
																	'amount'=>$phoen_top_customers['total_customer_amoun']
																
																
																);
														}
														
															
													}
													
													$phoen_customer_file = fopen('phoen-repot-customer.csv', 'w');
																					
													fputcsv($phoen_customer_file, array('Billing Name', 'Billing Email','Payment Method','Order Count','Amount'));
													 
													foreach ($phoen_repot_customer_csv as $phoen_customer_row)
													{
														fputcsv($phoen_customer_file, $phoen_customer_row);
													}
													
													fclose($phoen_customer_file);													
											
												?>  
				
										</tbody>
									</table>
								</div>
								
							</div>
						</div>
					</div>
				</div>
				
			</div>
			
				<div class="row">
					<div class="col-sm-6 col-xs-12 phoe-re-top-ten-cntry-state phoe_big_expand2">
						<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse7"><?php _e('Top Billing Country', 'advanced-reporting-for-woocommerce' ); ?>  <span class="fa fa-caret-down"></span></a>
									</h4>
									
									
								
								</div>
								
							<ul class="nav nav-tabs">
								<li class="active"><a href="#home_61" data-toggle="tab"><span class="fa fa-table"></span></a></li>
								<li><a href="#menu_62" data-toggle="tab"><span class="fa fa-bar-chart"></span></a></li>
								<li><a href="phoen-repot-country.csv"><span class="fa fa-download"></span></a></li>
								<li><a><span class="fa fa-arrows-alt phoe_expand" data-id="phoe_big_expand2"></span></a></li>
							</ul>
							
							 

								<div id="collapse7" class="panel-collapse collapse in">
									<div class="tab-content">
										<div id="home_61" class="tab-pane fade in active">
										
										
										<div class="phoen_report_data clearfix">
								
											<?php
											if(isset($_POST['report_limit_submit_Country']))
											{
												$phoen_country_value =$_POST['phoen_ten_country']; 
												
												update_option( 'phoen_top_country', $phoen_country_value );
												
											}
											
											$phoen_get_country_value = get_option( 'phoen_top_country' );
											
											if($phoen_get_country_value=='')
											{
												$phoen_get_country_val='10';
												
											}else{
												
												$phoen_get_country_val = get_option( 'phoen_top_country' );
											}
											
										?>
											
										<form method="POST">	
											<select name="phoen_ten_country" class="phoen_cat_limit">
												<option <?php echo ($phoen_get_country_val == '10')?'selected':'';?>><?php _e( '10', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_country_val == '20')?'selected':'';?>><?php _e( '20', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_country_val == '50')?'selected':'';?>><?php _e( '50', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_country_val == '100')?'selected':'';?>><?php _e( '100', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_country_val == 'View All')?'selected':'';?>><?php _e( 'View All', 'advanced-reporting-for-woocommerce' ); ?></option>
											</select>
											<input type="submit" name="report_limit_submit_Country" value="<?php _e( 'submit', 'advanced-reporting-for-woocommerce' ); ?>"> 
										</form>
											
											<input type="search" placeholder="<?php _e( 'Search', 'advanced-reporting-for-woocommerce' ); ?>" class="phoen_search_country">
										
										</div>
										
										<form method="POST" class="phoen_report_date">
											<div class="phoen_recent_order from">
												<label for="from_date"><?php _e( 'From', 'advanced-reporting-for-woocommerce' ); ?> </label>
												<input type="text" class="datepicker" name="date_country_froms" value="<?php echo $date_country_from ; ?>">
												
											</div>
											<div class ="phoen_recent_order to">
												<label for="to_date"><?php _e( 'To', 'advanced-reporting-for-woocommerce' ); ?> </label>
												<input type="text" class="datepicker" name="date_country_tos" value="<?php echo $date_country_to ; ?>">
											
											</div>
											<div class="phoe_date_form_submit_btn">
												<input type="submit" name="submit_recent_order" value="">
											</div>
										</form>	
										
											<table class="table table-striped table-bordered" id="phoen_search_country_table">
													<thead>
													  <tr>
														<th><?php _e( 'Billing Country', 'advanced-reporting-for-woocommerce' ); ?></th>
														<th><?php _e( 'Order Count', 'advanced-reporting-for-woocommerce' ); ?></th>
														<th><?php _e( 'Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
													  </tr>
													</thead>
													<tbody>
													
														<?php 
														$phoen_countery_count=0;
															foreach($phoen_billings_countrys as $kky=>$phoen_billings_countryss)
															{
																if($phoen_get_country_val!='View All')
																{
																	if($phoen_countery_count<$phoen_get_country_val)
																	{
																		?>
																		<tr class="phoen_search_country_tr">
																			<td>
																			
																				<?php
																					
																					$phoeni_country_name=isset($phoen_billings_countryss['country_name'])?$phoen_billings_countryss['country_name']:'';
																				
																					echo $phoeni_country_name;
																				?>
																				
																			</td>
																			
																			<td>
																			
																				<?php 
																					
																					$phoeni_totale_order=isset($phoen_billings_countryss['totle_order_counts'])?$phoen_billings_countryss['totle_order_counts']:'';
																					
																					echo $phoeni_totale_order;
																					
																				?>
																			
																			</td>
																			
																			<td>
																			
																				<?php 
																				
																					$phoeni_totle_amounts=isset($phoen_billings_countryss['total_amount'])?$phoen_billings_countryss['total_amount']:'';
																					
																					echo get_woocommerce_currency_symbol().($phoeni_totle_amounts);
																				
																				?>
																			
																			</td>
																		
																		</tr>
																		
																		<?php 
																	}
																	$phoen_countery_count++;
																}else{
																	?>
																		<tr class="phoen_search_country_tr">
																			<td>
																			
																				<?php
																					
																					$phoeni_country_name=isset($phoen_billings_countryss['country_name'])?$phoen_billings_countryss['country_name']:'';
																				
																					echo $phoeni_country_name;
																				?>
																				
																			</td>
																			
																			<td>
																			
																				<?php 
																					
																					$phoeni_totale_order=isset($phoen_billings_countryss['totle_order_counts'])?$phoen_billings_countryss['totle_order_counts']:'';
																					
																					echo $phoeni_totale_order;
																					
																				?>
																			
																			</td>
																			
																			<td>
																			
																				<?php 
																				
																					$phoeni_totle_amounts=isset($phoen_billings_countryss['total_amount'])?$phoen_billings_countryss['total_amount']:'';
																					
																					echo get_woocommerce_currency_symbol().($phoeni_totle_amounts);
																				
																				?>
																			
																			</td>
																		
																		</tr>
																		
																		<?php 
																}
																
															} 
															$phoen_country_file = fopen('phoen-repot-country.csv', 'w');
																							
															fputcsv($phoen_country_file, array('Billing Country', 'Order Count', 'Amount'));
															 
															foreach ($phoen_billings_countrys as $phoen_country_row)
															{
																if($phoen_get_country_val!='View All')
																{
																	if($phoen_countery_count<$phoen_get_country_val)
																	{
																		
																		fputcsv($phoen_country_file, $phoen_country_row);
																		
																	}
																}else{
																	
																	fputcsv($phoen_country_file, $phoen_country_row);
																}
																$phoen_countery_count++;
																
															}
															
															fclose($phoen_country_file);
											 
														
														?>
														
													</tbody>
											</table>
										</div>
										<div id="menu_62" class="tab-pane fade">
											<div id='chart_thrd'></div>
										</div>
								
										<div id="menu_63" class="tab-pane fade">
											<?php _e( 'Download 5', 'advanced-reporting-for-woocommerce' ); ?>
										</div>
										
									</div>
								</div>
						</div>
					</div>
					<div class="col-sm-6 col-xs-12 phoe-re-top-ten-cntry-state phoe_big_expand3">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse4"><?php _e( 'Top Billing State', 'advanced-reporting-for-woocommerce' ); ?><span class="fa fa-caret-down"></span></a>
								</h4>
						
							</div>
						
							<ul class="nav nav-tabs">
								<li class="active"><a href="#home_71" data-toggle="tab"><span class="fa fa-table"></span></a></li>
								<li><a href="#menu_72" data-toggle="tab"><span class="fa fa-bar-chart"></span></a></li>
								<li><a href="phoen-repot-states.csv"><span class="fa fa-download"></span></a></li>
								<li><a><span class="fa fa-arrows-alt phoe_expand" data-id="phoe_big_expand3"></span></a></li>
							</ul>
							

							<div id="collapse4" class="panel-collapse collapse in">
								<div class="tab-content">
									<div id="home_71" class="tab-pane fade in active">
																			
										<div class="phoen_report_data clearfix">
								
											<?php
											if(isset($_POST['report_limit_submit_state']))
											{
												$phoen_state_value =$_POST['phoen_ten_state']; 
												
												update_option( 'phoen_top_state', $phoen_state_value );
												
											}
											
											$phoen_get_state_value = get_option( 'phoen_top_state' );
											
											if($phoen_get_state_value=='')
											{
												$phoen_get_state_val='10';
												
											}else{
												
												$phoen_get_state_val = get_option( 'phoen_top_state' );
											}
											
											?>
											
										<form method="POST">	
											<select name="phoen_ten_state" class="phoen_cat_limit">
												<option <?php echo ($phoen_get_state_val == '10')?'selected':'';?>><?php _e( '10', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_state_val == '20')?'selected':'';?>><?php _e( '20', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_state_val == '50')?'selected':'';?>><?php _e( '50', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_state_val == '100')?'selected':'';?>><?php _e( '100', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_state_val == 'View All')?'selected':'';?>><?php _e( 'View All', 'advanced-reporting-for-woocommerce' ); ?></option>
											</select>
											<input type="submit" name="report_limit_submit_state" value="<?php _e( 'submit', 'advanced-reporting-for-woocommerce' ); ?>"> 
										</form>
											
											<input type="search" placeholder="<?php _e( 'Search', 'advanced-reporting-for-woocommerce' ); ?>" class="phoen_search_states">
										
									</div>
									
									<form method="POST" class="phoen_report_date">
										<div class="phoen_recent_order from">
											<label for="from_date"><?php _e( 'From' , 'advanced-reporting-for-woocommerce' ); ?> </label>
											<input type="text" class="datepicker" name="date_state_from" value="<?php echo $date_state_from ; ?>">
											
										</div>
										<div class ="phoen_recent_order to">
											<label for="to_date"><?php _e( 'To', 'advanced-reporting-for-woocommerce' ); ?> </label>
											<input type="text" class="datepicker" name="date_state_to" value="<?php echo $date_state_to ; ?>">
										
										</div>
										<div class="phoe_date_form_submit_btn">
											<input type="submit" name="submit_recent_order" value="">
										</div>
									</form>
									
									  <table class="table table-striped table-bordered" id="phoen_search_states_table">
											<thead>
											  <tr>
												<th><?php _e( 'Billing State', 'advanced-reporting-for-woocommerce' ); ?></th>
												<th><?php _e( 'Order Count', 'advanced-reporting-for-woocommerce' ); ?></th>
												<th><?php _e( 'Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
											  </tr>
											</thead>
											<tbody>
											<?php
											$phoen_state_count=0;
											foreach($phoen_billings_states as $kyy => $phoen_billings_statess)
											{
												if($phoen_get_state_val!='View All')
												{
													if($phoen_state_count<$phoen_get_state_val)
													{
														?>
														<tr class="phoen_search_states_tr">
															<td>
																<?php echo $phoen_billings_statess['state_name']; ?>
															</td>
															
															<td>
																<?php echo $phoen_billings_statess['totle_order_counts']; ?>
															</td>
															
															<td>
																<?php echo $phoen_billings_statess['total_amount']; ?>
															</td>
															
														</tr>
														
														<?php
													}
													
													$phoen_state_count++;
												}else{
													?>
														<tr class="phoen_search_states_tr">
															<td>
																<?php echo $phoen_billings_statess['state_name']; ?>
															</td>
															
															<td>
																<?php echo $phoen_billings_statess['totle_order_counts']; ?>
															</td>
															
															<td>
																<?php echo $phoen_billings_statess['total_amount']; ?>
															</td>
															
														</tr>
														
														<?php
												}
												
											
											}
											$phoen_states_file = fopen('phoen-repot-states.csv', 'w');
																							
											fputcsv($phoen_states_file, array('Billing State', 'Order Count', 'Amount'));
											 
											foreach ($phoen_billings_states as $phoen_states_row)
											{
												if($phoen_get_state_val!='View All')
												{
													if($phoen_state_count<$phoen_get_state_val)
													{
														fputcsv($phoen_states_file, $phoen_states_row);
													}	
													
													$phoen_state_count++;
												}else{
													
													fputcsv($phoen_states_file, $phoen_states_row);
												}	
											}
											
											fclose($phoen_states_file);
											 
											?>								
											</tbody>
										
										</table>
									</div>
									<div id="menu_72" class="tab-pane fade">
										<div id="chart_ts"></div>
									</div>
							
									<div id="menu_73" class="tab-pane fade">
										<?php _e( 'Download 6', 'advanced-reporting-for-woocommerce' ); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			
		<div class="row">
				<div class="col-sm-12 col-xs-12">
					<div class="phoe-war-latest-prjct-main">
					<div class="panel panel-default">
						<div class="phoe-war-latest-prjct-head">
						
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" href="#collapse5"><?php _e('Recent Orders', 'advanced-reporting-for-woocommerce' ); ?> <span class="fa fa-caret-down"></span></a>
								</h4>
							</div>
							
						</div>
						
						<ul class="nav nav-tabs">
							<li class="active"><a href="#home_81" data-toggle="tab"><span class="fa fa-table"></span></a></li>
							<li><a href="phoen-repot-recent-order.csv"><span class="fa fa-download"></span></a></li>
						</ul>
						
						
						
						
						<div class="phoe-war-ear-sec">
						<div id="collapse5" class="panel-collapse collapse in ">
						<div class="tab-content">
						<div id="home_81" class="tab-pane fade in active">
													
							<div class="phoen_report_data clearfix">
								
							<?php
								
								
								$phoen_get_recent_order_value = get_option( 'phoen_top_recent_order' );
								
								$phoen_get_recent_ordering_val = get_option( 'phoen_top_recent_ordering' );
								
								if($phoen_get_recent_ordering_val=='')
								{
									$phoen_get_recent_ordering_val='DESC';
									
								}else{
									$phoen_get_recent_ordering_val = get_option( 'phoen_top_recent_ordering' );
								}
								
								if($phoen_get_recent_order_value=='')
								{
									$phoen_get_recent_order_val='10';
									
								}else{
									
									$phoen_get_recent_order_val = get_option( 'phoen_top_recent_order' );
								}
								
								
								?>
								
							<form method="POST">	
								
								<select name="phoen_ten_recent_shorting_order" class="phoen_cat_limit">
								
									<option value="DESC"<?php echo ($phoen_get_recent_ordering_val == 'DESC')?'selected':'';?>><?php _e( 'Descending Order', 'advanced-reporting-for-woocommerce' ); ?></option>
									<option  value="ASC" <?php echo ($phoen_get_recent_ordering_val == 'ASC')?'selected':'';?>><?php _e( 'Ascending Order', 'advanced-reporting-for-woocommerce' ); ?></option>
									
								</select>
								
								
								<select name="phoen_ten_recent_order" class="phoen_cat_limit">
									<option <?php echo ($phoen_get_recent_order_val == '10')?'selected':'';?>><?php _e( '10', 'advanced-reporting-for-woocommerce' ); ?></option>
									<option <?php echo ($phoen_get_recent_order_val == '20')?'selected':'';?>><?php _e( '20', 'advanced-reporting-for-woocommerce' ); ?></option>
									<option <?php echo ($phoen_get_recent_order_val == '50')?'selected':'';?>><?php _e( '50', 'advanced-reporting-for-woocommerce' ); ?></option>
									<option <?php echo ($phoen_get_recent_order_val == '100')?'selected':'';?>><?php _e( '100', 'advanced-reporting-for-woocommerce' ); ?></option>
									<option <?php echo ($phoen_get_recent_order_val == 'View All')?'selected':'';?>><?php _e( 'View All', 'advanced-reporting-for-woocommerce' ); ?></option>
								</select>
								<input type="submit" name="report_limit_submit_recent_order" value="<?php _e( 'submit', 'advanced-reporting-for-woocommerce' ); ?>"> 
							</form>
								
								<input type="search" placeholder="<?php _e( 'Search', 'advanced-reporting-for-woocommerce' ); ?>" class="phoen_search_recent_order">
								
							</div>
							
							<form method="POST" class="phoen_report_date">
								<div class="phoen_recent_order from">
									<label for="from_date"><?php _e( 'From', 'advanced-reporting-for-woocommerce' ); ?> </label>
									<input type="text" class="datepicker" name="date_recent_from" value="<?php echo $date_recent_from ; ?>">
									
								</div>
								<div class ="phoen_recent_order to">
									<label for="to_date"><?php _e( 'To', 'advanced-reporting-for-woocommerce' ); ?> </label>
									<input type="text" class="datepicker" name="date_recent_to" value="<?php echo $date_recent_to ; ?>">
								
								</div>
								<div class="phoe_date_form_submit_btn">
									<input type="submit" name="submit_recent_order" value="">
								</div>
							</form>
							
							<table class="table table-striped table-bordered widefat" id="phoen_search_recent_order_table">
									<thead>
									  <tr>
										<th width="50px"><?php  _e( 'Order ID', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="100px"><?php _e( 'Name', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="100px"><?php _e( 'Email', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="100px"><?php _e( 'Address', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="100px"><?php _e( 'Phone No', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php  _e( 'Date', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php  _e( 'Coupan Used', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="60px"><?php  _e( 'Status', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php  _e( 'Gross Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php  _e( 'Order Discount Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php  _e( 'Total Discount Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php _e( 'Shipping Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php _e( 'Shipping Tax Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php _e( 'Order Tax Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php  _e( 'Total Tax Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php _e( 'Part Refund Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
										<th width="55px"><?php _e( 'Net Amt', 'advanced-reporting-for-woocommerce' ); ?></th>
									  </tr>
									</thead>
									<tbody>
									
									<?php
								
										$phoen_recent_order_csv=array();
										$phoen_recent_count=0;										
										for($i=0; $i<count($phoen_recent_order_datas_all); $i++)
										{
											if($phoen_get_recent_order_val!='View All')
											{
											
												if($phoen_recent_count<$phoen_get_recent_order_val)
												{
													/* echo "<pre>";
													print_r($phoen_recent_order_datas_all);
													echo "</pre>"; */
											
													?>
													<tr class="phoen_search_recent_order_tr">
													
														<td><?php echo $phoen_recent_order_datas_all[$i]['ID'];?></td>
														
														<td><?php echo $phoen_fist_last_name = $phoen_recent_order_datas_all[$i]['first_name']." ".$phoen_recent_order_datas_all[$i]['last_name'];?></td>
													
														<td><?php echo $phoen_recent_order_datas_all[$i]['billing_email'];?></td>
														<?php
														 $billing_address1 = $phoen_recent_order_datas_all[$i]['billing_address1'];
													
														if($billing_address1!='')
														{
															?>
															<td>	<?php echo $billing_address1 = $phoen_recent_order_datas_all[$i]['billing_address1']; ?></td>
															<?php
														}else{
															?>
															<td>	<?php echo $billing_address1 = $phoen_recent_order_datas_all[$i]['billing_address2']; ?></td>
															<?php
														}
														
														?>
														
														<td><?php echo $phoen_recent_order_datas_all[$i]['billing_phone_no'];?></td>
														
														<td><?php echo $phoen_recent_order_datas_all[$i]['order_date'];?></td>
														
														<td><?php echo isset($phoen_reward_coupon_name[$i])?$phoen_reward_coupon_name[$i]:'-' ; ?></td>
														
														
														
														<td>
														
														<?php 
																$status="";
																
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-cancelled")
															{
																
																$status="Cancelled";
																
																?>
																<mark class="phoen_canclled"><?php echo $status; ?></mark>
																<?php
															}
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-processing")
															{
																
																$status="Processing";
																?>
																<mark class="phoen_processing"><?php echo $status; ?></mark>
																<?php
																
															}
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-completed")
															{
																
																$status="Completed";
																
																?>
																<mark class="phoen_completed"><?php echo $status; ?></mark>
																<?php
															}
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-on-hold")
															{
																
																$status="On-hold";
																
																?>
																<mark class="phoen_holds"><?php echo $status; ?></mark>
																<?php
															}
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-refunded")
															{
																
																$status="Refunded";
																
																?>
																<mark class="phoen_refunded"><?php echo $status; ?></mark>
																<?php
															} 
															
															$phoen_post_id = $phoen_recent_order_datas_all[$i]['ID'];
															$phoen_recent_shop_order = get_post_meta($phoen_post_id);
															$phoen_totle_datas_order_shipping=$phoen_recent_shop_order['_order_shipping'][0];
															$phoen_totle_datas_order_tax=$phoen_recent_shop_order['_order_tax'][0]; 
															$phoen_totle_datas_order_shipping_taxs=$phoen_recent_shop_order['_order_shipping_tax'][0];
															$phoen_totle_datas_cart_discount=$phoen_recent_shop_order['_cart_discount'][0];
															
														
														?>
														
														</td>
														
														<td>
															<?php 
															
															$phoen_recent_gross_amt =($phoen_recent_order_datas_all[$i]['billing_amount']-$phoen_totle_datas_order_shipping_taxs-$phoen_totle_datas_order_tax-$phoen_totle_datas_order_shipping-$phoen_totle_datas_cart_discount);
															
															echo get_woocommerce_currency_symbol().($phoen_recent_order_datas_all[$i]['billing_amount']-$phoen_totle_datas_order_shipping_taxs-$phoen_totle_datas_order_tax-$phoen_totle_datas_order_shipping-$phoen_totle_datas_cart_discount);?>
															
														</td>
														
														<td><?php 
															$phoen_order_discount_amt = $phoen_totle_datas_cart_discount;
															
															echo get_woocommerce_currency_symbol().$phoen_totle_datas_cart_discount;?>
														
														</td>
														
														<td>
															<?php 
															$phoen_total_discount_amt = $phoen_totle_datas_cart_discount;
															
															echo get_woocommerce_currency_symbol().$phoen_totle_datas_cart_discount;?>
														
														</td>
														
														<td><?php
															$phoen_shipping_amt = $phoen_totle_datas_order_shipping;
															echo get_woocommerce_currency_symbol().$phoen_totle_datas_order_shipping;?>
															
														</td>
														
														<td><?php 
															$phoen_shipping_tax_amt = $phoen_totle_datas_order_shipping_taxs;
															
															echo  get_woocommerce_currency_symbol().$phoen_totle_datas_order_shipping_taxs;?>
														
														</td>
														
														<td><?php 
														
															$phoen_order_tax_amt = $phoen_totle_datas_order_tax;
															echo get_woocommerce_currency_symbol().$phoen_totle_datas_order_tax;?>
															
														</td>
														
														<td><?php 
														
															$phoen_total_tax_amt =($phoen_totle_datas_cart_discount+$phoen_totle_datas_order_shipping+$phoen_totle_datas_order_shipping_taxs+$phoen_totle_datas_order_tax);
															
															echo get_woocommerce_currency_symbol().($phoen_totle_datas_cart_discount+$phoen_totle_datas_order_shipping+$phoen_totle_datas_order_shipping_taxs+$phoen_totle_datas_order_tax);?>
														
														</td>
														
														<td>
														
															<?php 
																
																if($phoen_recent_order_datas_all[$i]['ordr_status']=='wc-refunded')
																{
																	$phoen_part_refund_amt = $phoen_recent_order_datas_all[$i]['billing_amount'];
																	
																	echo get_woocommerce_currency_symbol().($phoen_recent_order_datas_all[$i]['billing_amount']);
																}else{
																	
																	$phoen_part_refund_amt ='0';
																	
																	echo  get_woocommerce_currency_symbol().'0';
																}
															?>
														
														
														
														</td>
														
														<td>
														
															<?php 
														
															if($phoen_recent_order_datas_all[$i]['ordr_status']=='wc-refunded')
															{
																echo get_woocommerce_currency_symbol().'0';
																
																$phoen_net_amt='0';
																
															}else{
																
																$phoen_net_amt = $phoen_recent_order_datas_all[$i]['billing_amount']; 
																
																echo get_woocommerce_currency_symbol().($phoen_recent_order_datas_all[$i]['billing_amount']); 
					
															}
															?>
														
														</td>
														
														
													</tr>
													
													
													<?php
													if(isset($phoen_reward_coupon_name[$i])){
														$phoen_reward_coupon_name = $phoen_reward_coupon_name[$i];
													}else{
														$phoen_reward_coupon_name = '-';
													}

													$phoen_recent_order_csv[$i]=array(
														
														'id'=>$phoen_recent_order_datas_all[$i]['ID'],
														'name'=>$phoen_fist_last_name,
														'email'=>$phoen_recent_order_datas_all[$i]['billing_email'],
														'address'=>$billing_address1,
														'phone_no'=>$phoen_recent_order_datas_all[$i]['billing_phone_no'],
														'date'=>$phoen_recent_order_datas_all[$i]['order_date'],
														'coupan_code'=>$phoen_reward_coupon_name,
														'status'=>$status,
														'gross_amt'=>$phoen_recent_gross_amt,	
														'order_disc_amount'=>$phoen_order_discount_amt,
														'phoen_total_discount_amts'=>$phoen_total_discount_amt,
														'phoen_shipping_amts'=>$phoen_shipping_amt,
														'phoen_shipping_tax_amts'=>$phoen_shipping_tax_amt,
														'phoen_order_tax_amts'=>$phoen_order_tax_amt,
														'phoen_total_tax_amts'=>$phoen_total_tax_amt,
														'phoen_part_refund_amts'=>$phoen_part_refund_amt,
														'phoen_net_amts'=>$phoen_net_amt
													
													);	
												}
												$phoen_recent_count++;
											}else{
												?>
													<tr class="phoen_search_recent_order_tr">
													
														<td><?php echo $phoen_recent_order_datas_all[$i]['ID'];?></td>
														
														<td><?php echo $phoen_fist_last_name = $phoen_recent_order_datas_all[$i]['first_name']." ".$phoen_recent_order_datas_all[$i]['last_name'];?></td>
													
														<td><?php echo $phoen_recent_order_datas_all[$i]['billing_email'];?></td>
														<?php
														 $billing_address1 = $phoen_recent_order_datas_all[$i]['billing_address1'];
													
														if($billing_address1!='')
														{
															?>
															<td>	<?php echo $billing_address1 = $phoen_recent_order_datas_all[$i]['billing_address1']; ?></td>
															<?php
														}else{
															?>
															<td>	<?php echo $billing_address1 =  $phoen_recent_order_datas_all[$i]['billing_address2']; ?></td>
															<?php
														}
														
														?>
														
														<td><?php echo $phoen_recent_order_datas_all[$i]['billing_phone_no'];?></td>
														
														<td><?php echo $phoen_recent_order_datas_all[$i]['order_date'];?></td>
														
														<td><?php echo $phoen_reward_coupon_name[$i] ; ?></td>
														
														<td>
														
														<?php 
																$status="";
																
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-cancelled")
															{
																
																$status="Cancelled";
																
																?>
																<mark class="phoen_canclled"><?php echo $status; ?></mark>
																<?php
															}
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-processing")
															{
																
																$status="Processing";
																?>
																<mark class="phoen_processing"><?php echo $status; ?></mark>
																<?php
																
															}
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-completed")
															{
																
																$status="Completed";
																
																?>
																<mark class="phoen_completed"><?php echo $status; ?></mark>
																<?php
															}
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-on-hold")
															{
																
																$status="On-hold";
																
																?>
																<mark class="phoen_holds"><?php echo $status; ?></mark>
																<?php
															}
															if($phoen_recent_order_datas_all[$i]['ordr_status']=="wc-refunded")
															{
																
																$status="Refunded";
																
																?>
																<mark class="phoen_refunded"><?php echo $status; ?></mark>
																<?php
															} 
														
															$phoen_post_id = $phoen_recent_order_datas_all[$i]['ID'];
															$phoen_recent_shop_order = get_post_meta($phoen_post_id);
															$phoen_totle_datas_order_shipping=$phoen_recent_shop_order['_order_shipping'][0];
															$phoen_totle_datas_order_tax=$phoen_recent_shop_order['_order_tax'][0]; 
															$phoen_totle_datas_order_shipping_taxs=$phoen_recent_shop_order['_order_shipping_tax'][0];
															$phoen_totle_datas_cart_discount=$phoen_recent_shop_order['_cart_discount'][0];
														
														
														?>
														
														</td>
														
														<td>
															<?php 
															$phoen_recent_gross_amt =($phoen_recent_order_datas_all[$i]['billing_amount']-$phoen_totle_datas_order_shipping_taxs-$phoen_totle_datas_order_tax-$phoen_totle_datas_order_shipping-$phoen_totle_datas_cart_discount);
															
															echo get_woocommerce_currency_symbol().($phoen_recent_order_datas_all[$i]['billing_amount']-$phoen_totle_datas_order_shipping_taxs-$phoen_totle_datas_order_tax-$phoen_totle_datas_order_shipping-$phoen_totle_datas_cart_discount);?>
															
														</td>
														
														<td><?php 
															$phoen_order_discount_amt = $phoen_totle_datas_cart_discount;
															
															echo get_woocommerce_currency_symbol().$phoen_totle_datas_cart_discount;?>
														
														</td>
														
														<td>
															<?php 
															$phoen_total_discount_amt = $phoen_totle_datas_cart_discount;
															
															echo get_woocommerce_currency_symbol().$phoen_totle_datas_cart_discount;?>
														
														</td>
														
														<td><?php
															$phoen_shipping_amt = $phoen_totle_datas_order_shipping;
															echo get_woocommerce_currency_symbol().$phoen_totle_datas_order_shipping;?>
															
														</td>
														
														<td><?php 
															$phoen_shipping_tax_amt = $phoen_totle_datas_order_shipping_taxs;
															
															echo  get_woocommerce_currency_symbol().$phoen_totle_datas_order_shipping_taxs;?>
														
														</td>
														
														<td><?php 
														
															$phoen_order_tax_amt = $phoen_totle_datas_order_tax;
															echo get_woocommerce_currency_symbol().$phoen_totle_datas_order_tax;?>
															
														</td>
														
														<td><?php 
														
															$phoen_total_tax_amt =($phoen_totle_datas_cart_discount+$phoen_totle_datas_order_shipping+$phoen_totle_datas_order_shipping_taxs+$phoen_totle_datas_order_tax);
															
															echo get_woocommerce_currency_symbol().($phoen_totle_datas_cart_discount+$phoen_totle_datas_order_shipping+$phoen_totle_datas_order_shipping_taxs+$phoen_totle_datas_order_tax);?>
														
														</td>
														
														<td>
														
															<?php 
																
																if($phoen_recent_order_datas_all[$i]['ordr_status']=='wc-refunded')
																{
																	$phoen_part_refund_amt = $phoen_recent_order_datas_all[$i]['billing_amount'];
																	
																	echo get_woocommerce_currency_symbol().($phoen_recent_order_datas_all[$i]['billing_amount']);
																}else{
																	
																	$phoen_part_refund_amt ='0';
																	
																	echo  get_woocommerce_currency_symbol().'0';
																}
															?>
														
														
														
														</td>
														
														<td>
														
															<?php 
														
															if($phoen_recent_order_datas_all[$i]['ordr_status']=='wc-refunded')
															{
																echo get_woocommerce_currency_symbol().'0';
																
																$phoen_net_amt='0';
																
															}else{
																
																$phoen_net_amt = $phoen_recent_order_datas_all[$i]['billing_amount']; 
																
																echo get_woocommerce_currency_symbol().($phoen_recent_order_datas_all[$i]['billing_amount']); 
					
															}
															?>
														
														</td>
														
														
													</tr>
													
													
													<?php

													$phoen_recent_order_csv[$i]=array(
														
														'id'=>$phoen_recent_order_datas_all[$i]['ID'],
														'name'=>$phoen_fist_last_name,
														'email'=>$phoen_recent_order_datas_all[$i]['billing_email'],
														'address'=>$billing_address1,
														'phone_no'=>$phoen_recent_order_datas_all[$i]['billing_phone_no'],
														'date'=>$phoen_recent_order_datas_all[$i]['order_date'],
														'coupan_code'=>$phoen_reward_coupon_name[$i],
														'status'=>$status,
														'gross_amt'=>$phoen_recent_gross_amt,	
														'order_disc_amount'=>$phoen_order_discount_amt,
														'phoen_total_discount_amts'=>$phoen_total_discount_amt,
														'phoen_shipping_amts'=>$phoen_shipping_amt,
														'phoen_shipping_tax_amts'=>$phoen_shipping_tax_amt,
														'phoen_order_tax_amts'=>$phoen_order_tax_amt,
														'phoen_total_tax_amts'=>$phoen_total_tax_amt,
														'phoen_part_refund_amts'=>$phoen_part_refund_amt,
														'phoen_net_amts'=>$phoen_net_amt
													
													);	
											}	
												
										}
										
										$phoen_recent_order_file = fopen('phoen-repot-recent-order.csv', 'w');
																							
										fputcsv($phoen_recent_order_file, array('Order ID', 'Name', 'Email','Address','Phone No' ,'Date','Coupan Used' ,'Status', 'Gross Amt', 'Order Discount Amt', 'Total Discount Amt', 'Shipping Amt', 'Shipping Tax Amt', 'Order Tax Amt', 'Total Tax Amt', 'Part Refund Amt', 'Net Amt',));	
										 
										foreach ($phoen_recent_order_csv as $phoen_recent_order_row)
										{
											fputcsv($phoen_recent_order_file, $phoen_recent_order_row);
										}
										
										fclose($phoen_recent_order_file);
										 
									?>
									
									</tbody>
							</table>
							</div>
							</div>
							</div>
						</div>
					</div>
					</div>
				</div>
			</div>
			
			
			<div class="row">
				<div class="col-sm-6 col-xs-12 phoe_big_expand4">
					<div class="phoe-war-coupns">
						<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse55"><?php _e( 'Top Coupon', 'advanced-reporting-for-woocommerce' ); ?> <span class="fa fa-caret-down"></span></a>
										
									</h4>
								</div>
								
								
								<ul class="nav nav-tabs">
									<li class="active"><a href="#home_91" data-toggle="tab"><span class="fa fa-table"></span></a></li>
									<li><a href="#menu_92" data-toggle="tab"><span class="fa fa-bar-chart"></span></a></li>
									<li><a href="phoen-repot-coupan.csv"><span class="fa fa-download"></span></a></li>
									<li><a><span class="fa fa-arrows-alt phoe_expand" data-id="phoe_big_expand4"></span></a></li>
								</ul>
								

								<div id="collapse55" class="panel-collapse collapse in">
									<div class="tab-content">
									
										<div class="phoen_report_data clearfix">
								
											<?php
											if(isset($_POST['report_limit_submit_coupan']))
											{
												$phoen_coupan_value =$_POST['phoen_ten_coupan']; 
												
												update_option( 'phoen_top_coupan', $phoen_coupan_value );
												
											}
											
											$phoen_get_coupan_value = get_option( 'phoen_top_coupan' );
											
											if($phoen_get_coupan_value=='')
											{
												$phoen_get_coupan_val='10';
												
											}else{
												
												$phoen_get_coupan_val = get_option( 'phoen_top_coupan' );
											}
											
											?>
												
											<form method="POST">	
												<select name="phoen_ten_coupan" class="phoen_cat_limit">
													<option <?php echo ($phoen_get_coupan_val == '10')?'selected':'';?>><?php _e( '10', 'advanced-reporting-for-woocommerce' ); ?></option>
													<option <?php echo ($phoen_get_coupan_val == '20')?'selected':'';?>><?php _e( '20', 'advanced-reporting-for-woocommerce' ); ?></option>
													<option <?php echo ($phoen_get_coupan_val == '50')?'selected':'';?>><?php _e( '50', 'advanced-reporting-for-woocommerce' ); ?></option>
													<option <?php echo ($phoen_get_coupan_val == '100')?'selected':'';?>><?php _e( '100', 'advanced-reporting-for-woocommerce' ); ?></option>
													<option <?php echo ($phoen_get_coupan_val == 'View All')?'selected':'';?>><?php _e( 'View All', 'advanced-reporting-for-woocommerce' ); ?></option>
												</select>
												<input type="submit" name="report_limit_submit_coupan" value="<?php _e( 'submit', 'advanced-reporting-for-woocommerce' ); ?>"> 
											</form>
													
											<input type="search" placeholder="<?php _e( 'Search', 'advanced-reporting-for-woocommerce' ); ?>" class="phoen_search_coupan">
											
										</div>
									
										<div id="home_91" class="tab-pane fade in active">
											<table class="table table-striped table-bordered" id="phoen_search_coupan_table">
												<thead>
												  <tr>
													<th><?php _e( 'Coupon Code', 'advanced-reporting-for-woocommerce' ); ?></th>
													<th><?php _e( 'Coupon Count', 'advanced-reporting-for-woocommerce' ); ?></th>
													<th><?php _e( 'Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
												  </tr>
												</thead>
												<tbody>
													<?php
													$phoen_coupan_csv=array();
													
													$phoen_coupan_count=0;
													
													foreach($phoen_coupon_data as $kys => $phoen_coupon_datas)
													{
														if($phoen_get_coupan_val!='View All')
														{
															if($phoen_coupan_count<$phoen_get_coupan_val)
															{
																?>
																	<tr class="phoen_search_coupan_tr">
																		<td>
																			
																			<?php echo $phoen_coupon_datas['coupan_name']; ?>
																	
																		</td>
																		
																		<td>
																		
																			<?php echo $phoen_coupon_datas['coupan_count'];?>
											
																		</td>
																		
																		<td>
																		
																			<?php
																				$phoen_coupan_amount = ($phoen_coupon_datas['coupan_amount'])*($phoen_coupon_datas['coupan_count']);
																				
																				echo get_woocommerce_currency_symbol().(($phoen_coupon_datas['coupan_amount'])*($phoen_coupon_datas['coupan_count']));
																				?>

																		</td>
																		
																	</tr>
																
																<?php
																
																$phoen_coupan_csv[$kys]=array(
																		
																	'name'=>$phoen_coupon_datas['coupan_name'],
																	'count'=>$phoen_coupon_datas['coupan_count'],
																	'amount'=>$phoen_coupan_amount,
																
																); 
															}
															
															$phoen_coupan_count++;
														}else{
															?>
																<tr class="phoen_search_coupan_tr">
																	<td>
																		
																		<?php echo $phoen_coupon_datas['coupan_name']; ?>
																
																	</td>
																	
																	<td>
																	
																		<?php echo $phoen_coupon_datas['coupan_count'];?>
										
																	</td>
																	
																	<td>
																	
																		<?php
																			$phoen_coupan_amount = ($phoen_coupon_datas['coupan_amount'])*($phoen_coupon_datas['coupan_count']);
																			
																			echo get_woocommerce_currency_symbol().(($phoen_coupon_datas['coupan_amount'])*($phoen_coupon_datas['coupan_count']));
																			?>

																	</td>
																	
																</tr>
																
																<?php
																
																$phoen_coupan_csv[$kys]=array(
																		
																	'name'=>$phoen_coupon_datas['coupan_name'],
																	'count'=>$phoen_coupon_datas['coupan_count'],
																	'amount'=>$phoen_coupan_amount,
																
																); 
														}
													
													}
													
													$phoen_coupon_file = fopen('phoen-repot-coupan.csv', 'w');
																							
													fputcsv($phoen_coupon_file, array('Coupon Code', 'Coupon Count', 'Amount'));
													 
													foreach ($phoen_coupan_csv as $phoen_coupon_row)
													{
														fputcsv($phoen_coupon_file, $phoen_coupon_row);
													}
													
													fclose($phoen_coupon_file);
												
													?>
												</tbody>
											</table>
										</div>
										<div id="menu_92" class="tab-pane fade">
											<div id="chart"></div>
										</div>
								
										<div id="menu_93" class="tab-pane fade">
											<?php _e( 'Download 7', 'advanced-reporting-for-woocommerce' ); ?>
										</div>
									</div>
								</div>
						</div>
					</div>
				</div>
				
				<div class="col-sm-6 col-xs-12 phoe_big_expand5">
					<div class="phoe-war-coupns">
						<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" href="#collapse6"><?php _e('Top Payment Gateway', 'advanced-reporting-for-woocommerce' ); ?> <span class="fa fa-caret-down"></span></a>
										
									</h4>
								</div>
								
								<ul class="nav nav-tabs">
									<li class="active"><a href="#home_101" data-toggle="tab"><span class="fa fa-table"></span></a></li>
									<li><a href="#menu_102" data-toggle="tab"><span class="fa fa-bar-chart phoen_data"></span></a></li>
									<li><a href="payments.csv"><span class="fa fa-download"></span></a></li>
									<li><a><span class="fa fa-arrows-alt phoe_expand" data-id="phoe_big_expand5"></span></a></li>									
								</ul>
								
								<div id="collapse6" class="panel-collapse collapse in">
									<div class="tab-content">
										<div id="home_101" class="tab-pane fade in active">
										
											<form method="POST" class="phoen_report_date">
												<div class="phoen_recent_order from">
													<label for="from_date"><?php _e( 'From', 'advanced-reporting-for-woocommerce' ); ?> </label>
													<input type="text" class="datepicker" name="date_payment_from" value="<?php echo $date_payment_from ; ?>">
													
												</div>
												<div class ="phoen_recent_order to">
													<label for="to_date"><?php _e( 'To', 'advanced-reporting-for-woocommerce' ); ?> </label>
													<input type="text" class="datepicker" name="date_payment_to" value="<?php echo $date_payment_to ; ?>">
												
												</div>
												<div class="phoe_date_form_submit_btn">
													<input type="submit" name="submit_recent_order" value="">
												</div>
											</form>
										
										  <table class="table table-striped table-bordered" id="phoen_search_payments_table">
												<thead>
												  <tr>
													<th width="46%"><?php _e( 'Payment Method', 'advanced-reporting-for-woocommerce' ); ?></th>
													<th><?php _e( 'Order Count', 'advanced-reporting-for-woocommerce' ); ?></th>
													<th><?php _e( 'Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
												  </tr>
												</thead>
												<tbody>
												<?php
												
												foreach($phoen_payment_method as $key => $valuees)
												{
													
													
													?>
													
													<tr class="phoen_search_payments_tr">
													
														<td class="phoen_pay_name">
														
															<?php echo $phoen_payments_name = $valuees['payment_name']; ?>
														
														</td>
														
														<td>
														
															<?php echo $valuees['totle_order_counts']; ?>
														
														</td>
														
														<td>
														
															<?php 
															$phoen_payment_amounts = $valuees['total_amount'];
															echo get_woocommerce_currency_symbol().($valuees['total_amount']); ?>
														
														</td>
													
													</tr>
													
													<?php
												
												}
												
												$phoen_payment_file = fopen('payments.csv', 'w');
																						
												fputcsv($phoen_payment_file, array('Payment Method', 'Order Count', 'Amount'));
												 
												foreach ($phoen_payment_method as $phoen_payment_row)
												{
													
													fputcsv($phoen_payment_file, $phoen_payment_row);
														
												}
												
												fclose($phoen_payment_file);
												
												?>
												
												</tbody>
											</table>
										</div>
										<div id="menu_102" class="tab-pane fade">
											<div id="chart_scnd"></div>
										</div>
								
										<div id="menu_103" class="tab-pane fade">
											<?php _e( 'Download 9', 'advanced-reporting-for-woocommerce' ); ?>
										</div>
									</div>
								</div>
						</div>
					</div>
				</div>
				
			</div>
			
			<div class="col-sm-6 col-xs-12 phoe-war-top-tens-main phoe-bott-gap">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
							<a data-toggle="collapse" href="#collapse28"><?php _e( 'Unsold Products Detail ', 'advanced-reporting-for-woocommerce' ); ?> <span class="fa fa-caret-down"></span></a>
							</h4>
						</div>
						
						<ul class="nav nav-tabs">
							<li class="active"><a href="#home_41" data-toggle="tab"><span class="fa fa-table"></span></a></li>
							<li><a href="phoen-repot-unsold_product.csv"><span class="fa fa-download"></span></a></li>
						</ul>
						

						<div id="collapse28" class="panel-collapse collapse in">
							<div class="tab-content">
								<div id="home_41" class="tab-pane fade in active">
								
									<div class="phoen_report_data clearfix">
										<?php
											if(isset($_POST['report_limit_submit_unsold']))
											{
												$phoen_unsold_value =$_POST['phoen_ten_unsold']; 
												
												update_option( 'phoen_top_unsold', $phoen_unsold_value );
												
											}
											
											$phoen_get_unsold_value = get_option( 'phoen_top_unsold' );
											
											if($phoen_get_unsold_value=='')
											{
												$phoen_get_unsold_val='10';
												
											}else{
												
												$phoen_get_unsold_val = get_option( 'phoen_top_unsold' );
											}
											
										?>
											
										<form method="POST">	
											<select name="phoen_ten_unsold" class="phoen_cat_limit">
												<option <?php echo ($phoen_get_unsold_val == '10')?'selected':'';?>><?php _e( '10', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_unsold_val == '20')?'selected':'';?>><?php _e( '20', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_unsold_val == '50')?'selected':'';?>><?php _e( '50', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_unsold_val == '100')?'selected':'';?>><?php _e( '100', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_unsold_val == 'View All')?'selected':'';?>><?php _e( 'View All', 'advanced-reporting-for-woocommerce' ); ?></option>
											</select>
											<input type="submit" name="report_limit_submit_unsold" value="<?php _e( 'submit', 'advanced-reporting-for-woocommerce' ); ?>"> 
										</form>
										
										<input type="search" placeholder="<?php _e( 'Search', 'advanced-reporting-for-woocommerce' ); ?>" class="phoen_unsold_pro">
									
									</div>
								
								  <table class="table table-striped table-bordered" id="phoen_top_unsold_rorduct_table">
										<thead>
										  <tr>
											<th><?php _e( 'Product Name', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th><?php _e( 'Units In Stock', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th><?php _e( 'Unit Price', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th><?php _e( 'Total Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
										  </tr>
										</thead>
										<tbody>
										
													<?php
													
													$phoen_unsold_csv=array();
													
													$limit_unsold=0;
													
														foreach($phoen_unsold as $unsold=>$phoen_unsold_data)
														{
															if($phoen_get_unsold_val!='View All')
															{
																if($limit_unsold<$phoen_get_unsold_val)
																{
														
																	?>
																	<tr class="phoen_top_unsold_tr">
																	
																		<td> <?php echo $phoen_name_csv= $phoen_unsold_data['name'] ; ?> </td>
																		<td> <?php echo $phoen_stock_csv = $phoen_unsold_data['total_stock'] ; ?> </td>
																		<td> <?php 
																		
																			$phoen_per_pro = $phoen_unsold_data['per_product'];
																			
																			echo get_woocommerce_currency_symbol().($phoen_unsold_data['per_product']); ?> 
																		
																		</td>
																		<td> <?php
																		
																			$phoen_price_csv = $phoen_unsold_data['totle_price'];
																			echo get_woocommerce_currency_symbol().($phoen_unsold_data['totle_price']) ; ?> 
																		
																		</td>
																	
																	</tr>
																	<?php
																	
																	$phoen_unsold_csv[$unsold]=array(
																		'name'=>$phoen_name_csv,
																		'stock'=>$phoen_stock_csv,
																		'per_pro'=>$phoen_per_pro,
																		'price'=>$phoen_price_csv
																	
																	);
																	
																	
																}	
																$limit_unsold++;
															}else{
																
																?>
																<tr class="phoen_top_unsold_tr">
																
																	<td> <?php echo $phoen_name_csv = $phoen_unsold_data['name'] ; ?> </td>
																	<td> <?php echo $phoen_stock_csv = $phoen_unsold_data['total_stock'] ; ?> </td>
																	<td> <?php 
																	
																		$phoen_per_pro = $phoen_unsold_data['per_product'];
																		
																		echo get_woocommerce_currency_symbol().($phoen_unsold_data['per_product']); ?> 
																		
																	</td>
																	
																	<td> <?php 
																	
																		$phoen_price_csv = $phoen_unsold_data['totle_price'];
																		
																		echo get_woocommerce_currency_symbol().($phoen_unsold_data['totle_price']) ; ?> 
																	
																	</td>
																
																</tr>
																<?php
																
																$phoen_unsold_csv[$unsold]=array(
																	'name'=>$phoen_name_csv,
																	'stock'=>$phoen_stock_csv,
																	'per_pro'=>$phoen_per_pro,
																	'price'=>$phoen_price_csv
																
																);
																
															}
															
														}
														
														$phoen_unsold_file = fopen('phoen-repot-unsold_product.csv', 'w');
																							
														fputcsv($phoen_unsold_file, array('Product Name', 'Units In Stock', 'Unit Price', 'Total Amount'));
														 
														foreach ($phoen_unsold_csv as $phoen_unsold_row)
														{
															fputcsv($phoen_unsold_file, $phoen_unsold_row);
														}
														
														fclose($phoen_unsold_file);
													
													?>
											
										</tbody>
										
									</table>
								</div>
								
							</div>
						</div>
				</div>
				</div>
				
				<div class="col-sm-6 col-xs-12 phoe-war-top-tens-main phoe-bott-gap">
				<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
							<a data-toggle="collapse" href="#collapse299"><?php _e( 'Sold Products Detail ', 'advanced-reporting-for-woocommerce' ); ?> <span class="fa fa-caret-down"></span></a>
							</h4>
						</div>
						
						<ul class="nav nav-tabs">
							<li class="active"><a href="#home_422" data-toggle="tab"><span class="fa fa-table"></span></a></li>
							<li><a href="phoen-repot-sold_product.csv"><span class="fa fa-download"></span></a></li>
						</ul>
						

						<div id="collapse299" class="panel-collapse collapse in">
							<div class="tab-content">
								<div id="home_422" class="tab-pane fade in active">
								
									<div class="phoen_report_data clearfix phoen_sold_product_main">
										<?php
											if(isset($_POST['report_limit_submit_sold']))
											{
												$phoen_sold_value =$_POST['phoen_ten_sold']; 
												
												update_option( 'phoen_top_sold', $phoen_sold_value );
												
											}
											
											$phoen_get_sold_value = get_option( 'phoen_top_sold' );
											
											if($phoen_get_sold_value=='')
											{
												$phoen_get_sold_val='10';
												
											}else{
												
												$phoen_get_sold_val = get_option( 'phoen_top_sold' );
											}
											
										?>
											
										<form method="POST">	
											<select name="phoen_ten_sold" class="phoen_cat_limit">
												<option <?php echo ($phoen_get_sold_val == '10')?'selected':'';?>><?php _e( '10', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_sold_val == '20')?'selected':'';?>><?php _e( '20', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_sold_val == '50')?'selected':'';?>><?php _e( '50', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_sold_val == '100')?'selected':'';?>><?php _e( '100', 'advanced-reporting-for-woocommerce' ); ?></option>
												<option <?php echo ($phoen_get_sold_val == 'View All')?'selected':'';?>><?php _e( 'View All', 'advanced-reporting-for-woocommerce' ); ?></option>
											</select>
											<input type="submit" name="report_limit_submit_sold" value="<?php _e( 'submit', 'advanced-reporting-for-woocommerce' ); ?>"> 
										</form>
										
										<input type="search" placeholder="<?php _e( 'Search', 'advanced-reporting-for-woocommerce' ); ?>" class="phoen_sold_pro">
									
									<form method="POST" class="phoen_report_date">
										<div class="phoen_recent_order from">
											<label for="from_date"><?php _e( 'From', 'advanced-reporting-for-woocommerce' ); ?> </label>
											<input type="text" class="datepicker" name="date_sold_from" value="<?php echo $date_sold_from ; ?>">
											
										</div>
										<div class ="phoen_recent_order to">
											<label for="to_date"><?php _e( 'To', 'advanced-reporting-for-woocommerce' ); ?> </label>
											<input type="text" class="datepicker" name="date_sold_to" value="<?php echo $date_sold_to ; ?>">
										
										</div>
										<div class="phoe_date_form_submit_btn">
											<input type="submit" name="submit_sold_order" value="">
										</div>
									</form>
									
									</div>
									
									
								
								  <table class="table table-striped table-bordered" id="phoen_top_sold_rorduct_table">
										<thead>
										  <tr>
											<th><?php _e( 'Product Name', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th><?php _e( 'Qty', 'advanced-reporting-for-woocommerce' ); ?></th>
											<th><?php _e( 'Total Amount', 'advanced-reporting-for-woocommerce' ); ?></th>
										  </tr>
										</thead>
										<tbody>
										
													<?php
													
													//$phoen_array_sold_data_ar=array();
													
													$sold_product_total_val=0;
													$sold_product_quantity_val=0;
													
													$phoen_sold_csv=array();
													
													$limit_sold=0;
													
														foreach($phoen_array_sold_data_ar as $ky=>$phoen_array_sold_datas)
														{
															if($phoen_get_sold_val!='View All')
															{
																if($limit_sold<$phoen_get_sold_val)
																{
																	
	
	
	
														
																	?>
																	<tr class="phoen_top_sold_tr">
																	
																		<td> <?php echo $sold_product_name_val = $phoen_array_sold_datas['sold_product_names'];?> </td>
																		<td> <?php echo $sold_product_quantity_val= $phoen_array_sold_datas['sold_product_quantitys']; ; ?> </td>
																		<td> <?php 
																		
																			$sold_product_total_val= $phoen_array_sold_datas['sold_product_totals'];
																			
																			echo get_woocommerce_currency_symbol().($sold_product_total_val); ?> 
																		
																		</td>
																		
																	</tr>
																	<?php
																	
																	$phoen_sold_csv[$ky]=array(
																		'name'=>$sold_product_name_val,
																		'Qty'=>$sold_product_quantity_val,
																		'per_pro'=>$sold_product_total_val,
																	
																	);
																	
																	
																}	
																$limit_sold++;
															}else{
																
																?>
																<tr class="phoen_top_sold_tr">
																
																	<td> <?php echo $sold_product_name_val = $phoen_array_sold_datas['sold_product_names'];?> </td>
																<td> <?php echo $sold_product_quantity_val = $phoen_array_sold_datas['sold_product_quantitys']; ; ?> </td>
																	<td> <?php 
																	
																			$sold_product_total_val = $phoen_array_sold_datas['sold_product_totals'];
																			
																			echo get_woocommerce_currency_symbol().($sold_product_total_val); ?> 
																		
																	</td>
																	
																</tr>
																<?php
																
																$phoen_sold_csv[$ky]=array(
																	'name'=>$sold_product_name_val,
																	'Qty'=>$sold_product_quantity_val,
																	'per_pro'=>$sold_product_total_val,
																
																
																);
																
															}
															
														}
														
														$phoen_sold_file = fopen('phoen-repot-sold_product.csv', 'w');
																							
														fputcsv($phoen_sold_file, array('Product Name', 'Qty', 'Total Amount'));
														 
														foreach ($phoen_sold_csv as $phoen_sold_row)
														{
															fputcsv($phoen_sold_file, $phoen_sold_row);
														}
														
														fclose($phoen_sold_file);
													
													?>
											
										</tbody>
										
									</table>
								</div>
								
							</div>
						</div>
				</div>
				</div>
		
	</div>
	
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

	<script>
	
		jQuery(document).ready(function(){
			
			jQuery(".datepicker").datepicker({ dateFormat: 'yy-mm-dd' });
			
			var result = [];
			var result_all = [];
			var current_page = <?php echo (json_encode($phoen_payment_method)); ?>;
			
			jQuery(current_page).each(function( index, element ) {
			
				result[index] =[element.payment_name,element.total_amount];
			
				result_all.push(result);
			});	
			
			// Load Charts and the corechart package.
			google.charts.load('44', {'packages':['corechart']});
			
			// for top 3 paymnet getway
			google.setOnLoadCallback (createChart2);
 
            //callback function
			
            function createChart2() {
				
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Quarters 2009');
                dataTable.addColumn('number', 'Payment Gateway');
 
                //define rows of data
				dataTable.addRows(result_all[0]);
				
                //instantiate our chart objects
                var chart = new google.visualization.ColumnChart (document.getElementById('chart_scnd'));
               
 
                //define options for visualization
                var options = {width: 1000, height: 240, is3D: true, title: ''};
 
                //draw our chart
                chart.draw(dataTable, options);
             
            }
			
			var result_coupan = [];
			var result_coupan_all = [];
			var result_coupan_page = <?php echo (json_encode($phoen_coupan_csv)); ?>;
			
			jQuery(result_coupan_page).each(function( index, element ) {
			
				result_coupan[index] =[element.name,element.amount];
			
				result_coupan_all.push(result_coupan);
				
			});	
			
			// for top 3 coupens
			google.setOnLoadCallback (createChart);
 
            //callback function
            function createChart() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Quarters 2009');
                dataTable.addColumn('number', 'Coupons');
 
                //define rows of data
                dataTable.addRows(result_coupan_all[0]);
 
                //instantiate our chart objects
                var chart = new google.visualization.ColumnChart (document.getElementById('chart'));
               
 
                //define options for visualization
                var options = {width: 1000, height: 240, is3D: true, title: ''};
 
                //draw our chart
                chart.draw(dataTable, options);
              
            }
			
					// for top 3 Billing Country
					
			var result_country = [];
			var result_country_all = [];
			var result_country_page = <?php echo (json_encode($phoen_billings_countrys)); ?>;
			
			jQuery(result_country_page).each(function( index, element ) {
			
				result_country[index] =[element.country_name,element.total_amount];
			
				result_country_all.push(result_country);
			});	
					
		google.setOnLoadCallback (createChart3);
 
            //callback function
            function createChart3() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Quarters 2009');
                dataTable.addColumn('number', 'Billing Country');
 
                //define rows of data
                dataTable.addRows(result_country_all[0]);
 
                //instantiate our chart objects
                var chart = new google.visualization.ColumnChart (document.getElementById('chart_thrd'));
                
 
                //define options for visualization
                var options = {width: 1000, height: 240, is3D: true, title: ''};
 
                //draw our chart
                chart.draw(dataTable, options);
             
            }
			
			// for top 10 states
					
			var result_states = [];
			var result_states_all = [];
			var result_states_page = <?php echo (json_encode($phoen_billings_states)); ?>;
			
			jQuery(result_states_page).each(function( index, element ) {
			
				result_states[index] =[element.state_name,element.total_amount];
			
				result_states_all.push(result_states);
			});	
			google.setOnLoadCallback (createChart9);
 
            //callback function
            function createChart9() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Quarters 2009');
                dataTable.addColumn('number', 'State');
 
                //define rows of data
                dataTable.addRows(result_states_all[0]);
 
                //instantiate our chart objects
                var chart = new google.visualization.ColumnChart (document.getElementById('chart_ts'));
                
 
                //define options for visualization
                var options = {width: 1000, height: 350, is3D: true, title: ''};
 
                //draw our chart
                chart.draw(dataTable, options);
               
            }
			
			// for Order Summery   
				
			var result_order_summery = [];
			var result_order_summery_all = [];
			var result_order_summery_page = <?php echo (json_encode($phoen_order_csv)); ?>;
			
			jQuery(result_order_summery_page).each(function( index, element ) {
			
				result_order_summery[index] =[element.name,element.amount];
			
				result_order_summery_all.push(result_order_summery);
			});	
			
			google.setOnLoadCallback (createChart4);
 
            //callback function
            function createChart4() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Quarters 2009');
                dataTable.addColumn('number', 'Summary');
 
                //define rows of data
                dataTable.addRows(result_order_summery_all[0]);
 
                //instantiate our chart objects
                var chart = new google.visualization.ColumnChart (document.getElementById('chart_order'));
                
                //define options for visualization
                var options = {width: 1100, height: 240, is3D: true, title: ''};
 
                //draw our chart
                chart.draw(dataTable, options);
              
            }
			
				// for Sale order_status
			var result_order_status = [];
			var result_order_status_all = [];
			var result_order_status_page = <?php echo (json_encode($phoen_repot_status_csv)); ?>;
			
			jQuery(result_order_status_page).each(function( index, element ) {
			
				result_order_status[index] =[element.name,element.amount];
			
				result_order_status_all.push(result_order_status);
			});	
			
			google.setOnLoadCallback (createChart5);
 
            //callback function
            function createChart5() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Quarters 2009');
                dataTable.addColumn('number', 'Status');
 
                //define rows of data
                dataTable.addRows(result_order_status_all[0]);
 
                //instantiate our chart objects
                var chart = new google.visualization.ColumnChart (document.getElementById('chart_sale_order'));
              
 
                //define options for visualization
                var options = {width: 1000, height: 240, is3D: true, title: ''};
 
                //draw our chart
                chart.draw(dataTable, options);
               
 
            }
			
			//for sales by month chart
			
			var result_month = [];
			var result_month_all = [];
			var result_month_page = <?php echo (json_encode($phoen_year_csv)); ?>;
			
			jQuery(result_month_page).each(function( index, element ) {
			
				result_month[index] =[element.name,element.totle_sale];
			
				result_month_all.push(result_month);
			});
			
			google.setOnLoadCallback (drawChart1);
			
			function drawChart1() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Quarters 2009');
                dataTable.addColumn('number', 'Month');
 
                //define rows of data
                dataTable.addRows(result_month_all[1]);
 
                //instantiate our chart objects
                var chart = new google.visualization.ColumnChart (document.getElementById('chart_mnth'));
              
 
                //define options for visualization
                var options = {width: 1100, height: 240, is3D: true, title: ''};
 
                //draw our chart
                chart.draw(dataTable, options);
              
            }
			
			jQuery(".phoe_expand").click(function(){
				
				var div_id = jQuery(this).data('id');
				jQuery("."+div_id).toggleClass('phoe_click_width');
				
			});
			
			
		});
		
		
  
	</script>
