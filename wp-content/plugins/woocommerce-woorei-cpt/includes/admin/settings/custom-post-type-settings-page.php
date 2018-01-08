<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WooCommerceCustomPostTypeManagerSettings' ) ) :

/**
 * WooCommerceCustomPostTypeManagerSettings.
 */
class WooCommerceCustomPostTypeManagerSettings {
	
	private $cpt_list;
	
	public function __construct(){
		add_action( 'woocommerce_init', array( $this, 'init' ) );
	}
	
	public function init(){
		add_filter( 'woocommerce_get_sections_products', array( $this, 'custom_cpt_section_products' ) );
		add_filter( 'woocommerce_get_settings_products', array( $this, 'custom_cpt_settings_products' ), 10, 2 );
		add_action( 'woocommerce_admin_field_cpt_table', array( $this, 'woocommerce_admin_field_cpt_table' ) );
		add_action( 'woocommerce_settings_save_products', array( $this, 'settings_save' ) );
	}
	
	
	public function custom_cpt_section_products( $sections ){
		
		$sections['custom_cpt_section'] = __( 'Custom Post Types', 'woocommerce' );
		
		return $sections;
	}
	
	public function custom_cpt_settings_products( $settings, $current_section ){
		
		if ( $current_section == 'custom_cpt_section' ) {
			
			if ( isset($_GET['cpt'] ) && ( $cpt = WC_CPT_List::get( $_GET['cpt'] ) ) ) {
				
				$settings = array(
					array(
						'title' 		=> sprintf( __( '[ %s ] Custom Post Type settings', 'woocommerce' ), $cpt ),
						'type' 			=> 'title',
						'id' 			=> 'product_cpt_options'
					),
					array(
						'title'    		=> __( 'Archive Page', 'woocommerce' ),
						'desc'     		=> __( 'This is where your archive for this Post Type. <br>Note: If the page you want is not listed, create that page then do a refresh here.', 'woocommerce' ),
						'id'       		=> 'woocommerce_'. $cpt .'_page_id',
						'type'     		=> 'single_select_page',
						'default'  		=> '',
						'class'    		=> 'wc-enhanced-select-nostd',
						'css'      		=> 'min-width:300px;',
						'desc_tip' 		=> __( 'This sets the base page of your "shop" for this Post Type - this is where your product archive will be.', 'woocommerce' ),
					),
					array(
						'title'           => __( 'WooCommerce Template Loader', 'woocommerce' ),
						'desc'            => __( 'If checked, this Post Type will use WooCommerce Template files.', 'woocommerce' ),
						'id'              => $cpt . '_woorei_woocommerce_template_loader',
						'default'         => 'yes',
						'type'            => 'checkbox',
					),
					array(
						'title'			=> __( 'Enable this Post Type', 'woocommerce' ),
						'desc'			=> __( 'Enable posts of this Post Type be able to be added to cart.', 'woocommerce' ),
						'id'			=> $cpt . '_woorei_woocommerce_active',
						'default'		=> 'no',
						'type'			=> 'checkbox',
					),
				);
				
			} else {
			
				$settings = array(
					array(
						'title' 	=> __( 'Custom Post Types', 'woocommerce' ),
						'type' 		=> 'title',
						'id' 		=> 'product_cpt_options'
					),
					array(
						'type' => 'cpt_table',
						'id' => 'cpt_table',
					),
				);
			}
			
			$settings[] = array(
				'type' => 'sectionend',
				'id' => 'woorei_settings_tab_end'
			);
			
		}
		return apply_filters( 'woocommerce_products_custom_cpt_section_settings', $settings );
	}
	
	public function woocommerce_admin_field_cpt_table() {
		$GLOBALS['hide_save_button'] = true;
		
		$post_types = WC_CPT_List::get();
		
		?>
		<tr valign="top">
			<td class="forminp" colspan="2">
				<table class="shippingrows widefat" cellspacing="0">
					<thead>
						<tr>
							<th class="name" style="padding-left:11px"><?php _e( 'Menu Name', 'woorei' ); ?></th>
							<th class="name" style="padding-left:11px"><?php _e( 'Archive Name', 'woorei' ); ?></th>
							<th class="slug" style="padding-left:11px"><?php _e( 'Slug', 'woorei' ); ?></th>
							<th class="status" style="text-align:center;width:120px" align="center"><?php _e( 'WooCommerce', 'woorei' ); ?></th>
							<th class="settings" style="text-align:center;width:120px">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if ( !empty( $post_types ) )
						foreach ( $post_types as $key => $post_type ) {
						?>
						<tr>
							<td class="menu-name" ><?php echo $post_type['menu_name'];?></td>
							<td class="cpt-name" ><?php echo $post_type['name']; ?></td>
							<td class="slug"><?php echo $post_type['slug'];?></td>
							<td class="status" align="center"><?php echo ( $post_type['active'] )?'<span class="status-enabled">Yes</span>':'-'; ?></td>
							<td align="center">
								<a class="button" href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=products&section=custom_cpt_section&cpt=' . $key );?> "><?php echo  __( 'Settings', 'woocommerce' ); ?></a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
	}
	
	public function settings_save(){
		if (isset($_GET['cpt'])) {
			update_option( 'wccpt_need_flush_rewrite_rules', true );
		}
	}

	
}

endif;
new WooCommerceCustomPostTypeManagerSettings();
