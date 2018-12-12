<?php
global $wpdb,$woocommerce,$product;

	$phoen_products_args = array(

    'post_type' => 'product',

    'numberposts' => -1,

);

$products = get_posts( $phoen_products_args );

if(isset($_POST['phoen_check_var_stock_submit']))
{
	$phoen_var_stock_data = $_POST['phoen_var_check_stock'];
	
	update_option( 'phoen_var_stock_limit', $phoen_var_stock_data );
	
}

$phoen_get_var_stock_val = get_option( 'phoen_var_stock_limit' );

if($phoen_get_var_stock_val=='')
{
	$phoen_get_var_stock_val='10';
	
}else{
	
	$phoen_get_var_stock_val = get_option( 'phoen_var_stock_limit' );
}

?>

<div class="phoen_search">

	<input type="search" placeholder="<?php _e('Search','advanced-reporting-for-woocommerce'); ?>" class="phoen_product_variation">

</div>

<form method="post" class="phoen_report_instock">
	
	<label><?php _e('Show Stock Less Than','advanced-reporting-for-woocommerce'); ?></label>

	<input type="number" name="phoen_var_check_stock" value="<?php echo $phoen_get_var_stock_val; ?>"> 

	<input type="submit" name="phoen_check_var_stock_submit" value="<?php _e('submit','advanced-reporting-for-woocommerce'); ?>">

</form>

<body>	

	<div class="phoen_out_stock_download_btn">
	
		<a href="phoen-repot-var-instock.csv"><?php _e('Download csv','advanced-reporting-for-woocommerce'); ?></a>
	
	</div>
	
	<div class="phoen_var_instocks">

		<table class="phoeniixx_variable_table paginated" id="phoen_product_attr_table">

			<?php

			$attribute_taxonomies = wc_get_attribute_taxonomies();

			$phoen_name=array();

			foreach($attribute_taxonomies as $phoen_name_key=>$phoen_attribute_name)
			{

				$phoen_att_id=$phoen_attribute_name->attribute_id;

				$phoen_att_name=$phoen_attribute_name->attribute_name;
				
				$phoen_name[$phoen_name_key]=array(

					'id'=>$phoen_att_id,

					'name'=>$phoen_att_name,

				);

			} 

			?>

			<tr>
				
				<th><?php _e( 'Product Name', 'advanced-reporting-for-woocommerce' ); ?></th>
				<th><?php _e( 'Sku', 'advanced-reporting-for-woocommerce' ); ?></th>
				<th><?php _e( 'Units In Stock', 'advanced-reporting-for-woocommerce' ); ?></th>
				<th><?php _e( 'Category Name', 'advanced-reporting-for-woocommerce' ); ?></th>
				
				<?php

				$phoen_attribute=array();		

				foreach($phoen_name as $k=>$phoen_attr_name)
				{	
					?>
						<th> <?php echo $phoen_attr_name['name'];

						$phoen_attribute[$k] = "attribute_pa_".$phoen_attr_name['name'] ; ?> </th>
						
					<?php
					
					$phoen_att_names[$k]=$phoen_attr_name['name'];
					
				}
				?> 

			</tr> <?php	
			
			$phoen_all_variation=array();
			
			$phoen_var_outstock=array();
			
			$phoen_all_variation_data = array();
			
			$count='0';
			
			$data_count = 0;
			
			foreach($products as $key=> $product):

			$product_s = wc_get_product( $product->ID );
			
			$product_type = new WC_Product( $product->ID );
		
				if( $product_type->is_type( 'variable' ) ) {
			
					//if ($product_s->product_type == 'variable') {

					$variations = $product_s->get_available_variations();
					
					$phoen_productname='0';
					$phoen_var_sku='0';
					$phoen_stock='0';
					$phoen_cat_name='0';
				
					
					foreach($variations as $kery=> $val)
					{
						
						if($val['max_qty'] !='')
						{

							if($val['max_qty'] < $phoen_get_var_stock_val)
							{
								$data_count++;
								?>
							
								<tr class="phoen_attr_data_tr" > 

									<td> <?php echo $phoen_productname = $product->post_title; ?>  </td>

									<td> <?php
										if($val['sku'] !='')
										{
											echo $phoen_var_sku = $val['sku'];
										
										}else{
											echo $phoen_var_sku = "-";
										}					
										?> 
										
									</td>

									<td><?php 
									
										if($val['max_qty'] !='')
										{
											echo $phoen_stock = $val['max_qty'] ; 
											
										}else{
											
											echo $phoen_stock =  "-";
										}						
										?>
										
									</td>
									<td>
									<?php
									
										$args = array( 'taxonomy' => 'product_cat',);
										$terms = wp_get_post_terms($product->ID ,'product_cat', $args);
										
										if (!empty($terms))
										{	  
											foreach ($terms as $term) {
										
												$phoen_cat_name= $term->name;
												
												echo $phoen_cat_name;
										
											}
										}else{
											
											echo $phoen_cat_name = "-";
											
										}
								
									?></td><?php
									
										$all_attributes_count = count($phoen_attribute);

										$jk=0;
										
										foreach($val['attributes'] as $keysss=>$phoen_variation_data)
										{
											
											for($jk;$jk<$all_attributes_count;$jk++){
												
												if($keysss == $phoen_attribute[$jk]){
												?>

													<td> <?php echo $phoen_variation_data; ?>  </td>
													<?php $phoen_all_variation[$jk] = $phoen_variation_data; ?>
													
												<?php
												
													$jk++;
													
													break;
													
												}else{
													
													echo "<td>-</td>";
													
													$phoen_all_variation[$jk]= "-";
												
												}
											
											}
									
										}
									
									?>
									
								</tr>

							 <?php 
							 
								$phoen_var_outstock[$kery] = array(
													
									'name'=>$phoen_productname,
									'sku'=>$phoen_var_sku,
									'stock'=>$phoen_stock,
									'cat_name'=>$phoen_cat_name,
									'variation'=>$phoen_all_variation
								
								); 
							}
						
						}
					
					}

				}

			endforeach; 
			
			$phoenb_attr=array();
			
			foreach($phoen_var_outstock as $p_key => $phoen_attrr_names) 
			{
			
				$phoen_attrr_names['name'];
				$phoen_attrr_names['sku'];
				$phoen_attrr_names['stock'];
				$phoen_attrr_names['cat_name'];
				
				$phoen_datas=array();
				
				foreach($phoen_attrr_names['variation'] as $kt=> $phoe_var_data)
				{
				
					$phoen_datas[$kt] = $phoe_var_data;	
				}
				
				$header_item_data = array(
				
					'phoen_name'=>$phoen_attrr_names['name'],
					'phoen_sku'=>$phoen_attrr_names['sku'],
					'phoen_stock'=>$phoen_attrr_names['stock'],
					'phoen_cat_names'=>$phoen_attrr_names['cat_name'],
					
				);
				
				if(!empty($phoen_datas))
				{
					$dynamic_header_data = array_merge($header_item_data,$phoen_datas);
				
					$phoenb_attr[$p_key]= $dynamic_header_data;
					
				}
				
			}
			
			if(!empty($phoen_att_names))
			{
				$phoen_var_instock_file = fopen('phoen-repot-var-instock.csv', 'w');
			
				$header_item = array('Product Name', 'Sku', 'Units In Stock',  'Category Name');
				
				$dynamic_header = array_merge($header_item,$phoen_att_names);
				
				fputcsv($phoen_var_instock_file, $dynamic_header);
				
				foreach ($phoenb_attr as $phoen_var_instock_row)
				{
					fputcsv($phoen_var_instock_file, $phoen_var_instock_row);
				}

				fclose($phoen_var_instock_file);
				
			}
			
			
			
			?>

		</table>
		
		<div class="paging-container" id="phoen_attr_data_pagination"> </div>
		
	</div>
	
	<script>
	
	jQuery(function () {
			
			load = function() {
				window.tp = new Pagination('#phoen_attr_data_pagination', {
					itemsCount: <?php echo ($data_count != '')?$data_count:0;?>,
					onPageSizeChange: function (ps) {
						console.log('changed to ' + ps);
					},
					onPageChange: function (paging) {
						//custom paging logic here
						console.log(paging);
						var start = paging.pageSize * (paging.currentPage - 1),
							end = start + paging.pageSize,
							$rows = jQuery('#phoen_product_attr_table').find('.phoen_attr_data_tr');

						$rows.hide();

						for (var i = start; i < end; i++) {
							$rows.eq(i).show();
						}
					}
				});
			}

		load();
	});
	
	</script>
	
</body>