<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Flexible_Checkout_Fields_Pro_Docs_Metabox {

	/**
	 * Flexible_Checkout_Fields_Pro_Docs_Metabox constructor.
	 *
	 * @param Flexible_Checkout_Fields_Pro_Plugin $plugin Plugin.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		add_action( 'flexible_checkout_fields_after_add_new_field', array( $this, 'flexible_checkout_fields_after_add_new_field' ) );
	}

	public function flexible_checkout_fields_after_add_new_field() {
		include( 'views/docs-metabox.php' );
	}

}
