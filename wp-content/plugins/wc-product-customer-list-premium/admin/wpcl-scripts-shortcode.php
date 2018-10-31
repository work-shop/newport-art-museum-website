<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.6.7
 */

if( ! function_exists('wpcl_register_scripts_shortcode') ) {
	function wpcl_register_scripts_shortcode() {

		// Register styles
		wp_register_style( 'wpcl-datatables-css', 'https://cdn.datatables.net/t/dt/dt-1.10.11,r-2.0.2/datatables.min.css', false, '1.10.11' );
		wp_register_style( 'wpcl-datatables-buttons-css', 'https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css', false, '1.2.2' );
		wp_register_style( 'wpcl-datatables-select-css', 'https://cdn.datatables.net/select/1.2.2/css/select.dataTables.min.css', false, '1.0' );

		// Register scripts
		wp_register_script( 'wpcl-datatables-js', 'https://cdn.datatables.net/t/dt/dt-1.10.11,r-2.0.2/datatables.min.js', true, '2.0.2' );
		wp_register_script( 'wpcl-datatables-buttons-js', 'https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js', true, '1.2.2' );
		wp_register_script( 'wpcl-datatables-buttons-flash', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js', true, '1.2.2' );
		wp_register_script( 'wpcl-datatables-print', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js', true, '1.2.2' );
		wp_register_script( 'wpcl-datatables-jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js', true, '2.5.0' );
		wp_register_script( 'wpcl-datatables-pdfmake', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js', true, '0.1.36' );
		wp_register_script( 'wpcl-datatables-vfs-fonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.20/vfs_fonts.js', true, '0.1.20' );
		wp_register_script( 'wpcl-datatables-buttons-html', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js', true, '1.2.2' );
		wp_register_script( 'wpcl-datatables-buttons-print', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js', true, '1.2.2' );
		//wp_register_script( 'wpcl-datatables-colreorder', 'https://cdn.datatables.net/colreorder/1.3.2/js/dataTables.colReorder.min.js', true, '1.3.2' );
		//wp_register_script( 'wpcl-datatables-select', 'https://cdn.datatables.net/select/1.2.2/js/dataTables.select.min.js', true, '1.2.2' );
		wp_register_script( 'wpcl-script-shortcode', plugin_dir_url( __FILE__ ) . 'assets/shortcode.js', true, '1.0' );
	}
	add_action( 'wp_enqueue_scripts', 'wpcl_register_scripts_shortcode' );
}