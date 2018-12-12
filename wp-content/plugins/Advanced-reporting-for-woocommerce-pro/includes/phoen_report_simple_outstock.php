<?php
global $wpdb,$woocommerce,$product;
$phoen_products_args = array(

    'post_type' => 'product',

    'numberposts' => -1,

);

$products_simple = get_posts( $phoen_products_args );


?>
<body>
<div class="phoen_search">

	<input type="search" placeholder="<?php _e('Search','advanced-reporting-for-woocommerce'); ?>" class="phoen_product_simple_outstock">

</div>

<div class="phoen_out_stock_download_btn">
	<a href="phoen-repot-simple-outstock.csv"><?php _e('Download csv','advanced-reporting-for-woocommerce'); ?></a>
</div>

<div class="phoen_simple_outstock_pro">
	<table class="phoeniixx_simple_table" id="phoen_product_outstock_table">
		
		<tr>

			<th><?php _e( 'Product Name', 'advanced-reporting-for-woocommerce' ); ?></th>
			<th><?php _e( 'Sku', 'advanced-reporting-for-woocommerce' ); ?></th>
			<th><?php _e( 'Category Name', 'advanced-reporting-for-woocommerce' ); ?></th>
			<th><?php _e( 'Units In Stock', 'advanced-reporting-for-woocommerce' ); ?></th>
			<th><?php _e( 'Stock Status', 'advanced-reporting-for-woocommerce' ); ?></th>

		</tr>
		
		<?php
		$phoen_simple_outstock_csv=array();
		$data_count = 0;
		foreach($products_simple as $key => $products_simple_data)
		{
			
			$product_s = wc_get_product( $products_simple_data->ID );
			
			$product_type = new WC_Product( $products_simple_data->ID );
		
			if( $product_type->is_type( 'simple' ) ) {
				
				$phoen_cat_args = array( 'taxonomy' => 'product_cat',);
				
				$phoen_cat_terms = wp_get_post_terms($products_simple_data->ID ,'product_cat', $phoen_cat_args);
				
				$phoen_products_simple = get_post_meta($products_simple_data->ID);
				
				$phoen_stock_status=$phoen_products_simple['_stock_status'][0];
					
				$phoen_simple_stock = $phoen_products_simple['_stock'][0];
				
				if($phoen_stock_status =='outofstock')
				{
					$data_count++;
				?>
				<tr class="phoen_product_outstock_tr">
				
					<td> <?php echo $phoen_product_title = $products_simple_data->post_title; ?> </td>
					
					<td> <?php $phoen_simple_sku = $phoen_products_simple['_sku'][0]; 
						if($phoen_simple_sku !='')
						{
							echo $phoen_simple_sku;
						}else{
							
							echo $phoen_simple_sku = "-";
						}
					
						?> 
					</td>
					
					<td> <?php 
						if (!empty($phoen_cat_terms))
						{	  
							foreach ($phoen_cat_terms as $phoen_cats_terms) {
						
								$phoen_cat_names= $phoen_cats_terms->name;
								
								echo $phoen_cat_names;
						
							}
						}else{
							
							echo $phoen_cat_names = "-";
							
						}?>
						
					</td>
					
					<td> <?php 

						$phoen_simple_stock = $phoen_products_simple['_stock'][0];
						
						if($phoen_simple_stock !='')
						{
							echo round($phoen_simple_stock); 
							
						}else{
							
							echo $phoen_simple_stock = "-";
						}

						?> 
					
					</td>
					
					<td style="color:#a44">
					
						<?php if($phoen_stock_status =='outofstock')
						{
							echo $phoen_stock_status_name='Out of stock';
						} 
						
						?>
					
					</td>
					
				</tr>
				<?php
					$phoen_simple_outstock_csv[$key]=array(
						
						'name'=>$phoen_product_title,
						'Sku'=>$phoen_simple_sku,
						'cat_name'=>$phoen_cat_names,
						'unit'=>$phoen_simple_stock,
						'stock_status'=>$phoen_stock_status_name
					
					);
				}
			}
		}
		$phoen_simple_outstock_file = fopen('phoen-repot-simple-outstock.csv', 'w');
																					
		fputcsv($phoen_simple_outstock_file, array('Product Name', 'Sku', 'Category Name', 'Units In Stock', 'Stock Status'));
	 
		foreach ($phoen_simple_outstock_csv as $phoen_simple_outstock_row)
		{
			fputcsv($phoen_simple_outstock_file, $phoen_simple_outstock_row);
		}
	
		fclose($phoen_simple_outstock_file);	
		?> 

	</table>
<div class="paging-container" id="simple_out_stock_tablePaging"> </div>
</div>
<script>
	
	jQuery(function () {
		
			load = function() {
				window.tp = new Pagination('#simple_out_stock_tablePaging', {
					itemsCount: <?php echo ($data_count != '')?$data_count:0;?>,
					onPageSizeChange: function (ps) {
						console.log('changed to ' + ps);
					},
					onPageChange: function (paging) {
						//custom paging logic here
						console.log(paging);
						var start = paging.pageSize * (paging.currentPage - 1),
							end = start + paging.pageSize,
							$rows = jQuery('#phoen_product_outstock_table').find('.phoen_product_outstock_tr');

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
</tbody>