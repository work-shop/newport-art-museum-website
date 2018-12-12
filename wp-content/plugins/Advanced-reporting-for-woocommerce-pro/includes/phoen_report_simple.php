<?php
global $wpdb,$woocommerce,$product;
$phoen_products_args = array(

    'post_type' => 'product',

    'numberposts' => -1,

);

$products_simple = get_posts( $phoen_products_args );


?>


<?php
if(isset($_POST['phoen_check_stock_submit']))
{
	$phoen_stock_data = $_POST['phoen_check_stock'];
	
	update_option( 'phoen_simple_stock_limit', $phoen_stock_data );
	
	
}

$phoen_get_stock_val = get_option( 'phoen_simple_stock_limit' );

if($phoen_get_stock_val=='')
{
	$phoen_get_stock_val='10';
	
}else{
	
	$phoen_get_stock_val = get_option( 'phoen_simple_stock_limit' );
}

?>

<div class="phoen_search">

<input type="search" placeholder="<?php _e('Search','advanced-reporting-for-woocommerce'); ?>" class="phoen_product_simple_instock">

</div>


<form method="post" class="phoen_report_instock">
	<label><?php _e('Show Stock Less Than','advanced-reporting-for-woocommerce'); ?></label>
	<input type="number" name="phoen_check_stock" value="<?php echo $phoen_get_stock_val; ?>"> 

	<input type="submit" name="phoen_check_stock_submit" value="<?php _e('submit','advanced-reporting-for-woocommerce'); ?>">

</form>



<div class="phoen_out_stock_download_btn">
	<a href="phoen-repot-simple-instock.csv"><?php _e('Download csv','advanced-reporting-for-woocommerce'); ?></a>
</div>

<body>

	<div class="phoen_instock_simple_product container">


		<table class="phoeniixx_simple_table" id="phoen_product_simple_instock_table">
		
			<tr>

				<th><?php _e( 'Product Name', 'advanced-reporting-for-woocommerce' ); ?></th>
				<th><?php _e( 'Sku', 'advanced-reporting-for-woocommerce' ); ?></th>
				<th><?php _e( 'Category Name', 'advanced-reporting-for-woocommerce' ); ?></th>
				<th><?php _e( 'Units In Stock', 'advanced-reporting-for-woocommerce' ); ?></th>
				<th><?php _e( 'Stock Status', 'advanced-reporting-for-woocommerce' ); ?></th>

			</tr>
			
			<?php

			$phoen_simple_instock_csv=array();
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
					
					if($phoen_stock_status !='outofstock')
					{
						if($phoen_simple_stock < $phoen_get_stock_val)
						{
							$data_count++;
							?>
							<tr class="phoen_product_simple_instock_tr">
							
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
									
										echo $phoen_simple_stock=(round($phoen_simple_stock, 0));
										
									}else{
										
										echo $phoen_simple_stock = "-";
									}

									?> 
								
								</td>
								
								<td style="color:#7ad03a">
								
									<?php if($phoen_stock_status =='instock')
									{
										echo $phoen_stock_status_name='In Stock'; 
									}										
								
									?>
								</td>
								
							</tr>
							<?php
							
								$phoen_simple_instock_csv[$key]=array(
									
									'name'=>$phoen_product_title,
									'Sku'=>$phoen_simple_sku,
									'cat_name'=>$phoen_cat_names,
									'unit'=>$phoen_simple_stock,
									'stock_status'=>$phoen_stock_status_name
								
								);
							
						}	
					}
				}
			}
				$phoen_simple_instock_file = fopen('phoen-repot-simple-instock.csv', 'w');
																						
				fputcsv($phoen_simple_instock_file, array('Product Name', 'Sku', 'Category Name', 'Units In Stock', 'Stock Status'));
			 
				foreach ($phoen_simple_instock_csv as $phoen_simple_instock_row)
				{
					fputcsv($phoen_simple_instock_file, $phoen_simple_instock_row);
				}
			
				fclose($phoen_simple_instock_file);
				
			?> 

		</table>
		<div class="paging-container" id="phoen_product_simple_instock_pagination"> </div>
	</div>
	
	<script>
	
	jQuery(function () {
		
			load = function() {
				window.tp = new Pagination('#phoen_product_simple_instock_pagination', {
					itemsCount: <?php echo ($data_count != '')?$data_count:0;?>,
					onPageSizeChange: function (ps) {
						console.log('changed to ' + ps);
					},
					onPageChange: function (paging) {
						//custom paging logic here
						console.log(paging);
						var start = paging.pageSize * (paging.currentPage - 1),
							end = start + paging.pageSize,
							$rows = jQuery('#phoen_product_simple_instock_table').find('.phoen_product_simple_instock_tr');

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