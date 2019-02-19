<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

$general_options = array (

    'general' => array (

        'section_general_settings'     => array (
            'name'         => __ ( 'General settings', 'yith-woocommerce-gift-cards' ),
            'type'         => 'title',
        ),
        'enable_plugin'                => array (
            'name'    => __ ( 'Enable plugin', 'yith-woocommerce-gift-cards' ),
            'id'      => 'yith_ywgc_enable_plugin',
            'type'    => 'checkbox',
            'desc'    => __ ( "Uncheck this if you want to disable temporarly the plugin", 'yith-woocommerce-gift-cards' ),
            'default' => 'yes',
        ),
        'create_first_product'         => array (
            'name' => __ ( 'Create a gift card', 'yith-woocommerce-gift-cards' ),
            'type' => 'ywgc_create_first_product',
            'desc' => __ ( "You're able to create your own gift cards. In order to sell gift cards you need to create a product and set the product type to 'gift card' (you can find the gift card option in the same dropdown that allows you to choose between simple or variable product)", 'yith-woocommerce-gift-cards' ),
        ),
        'section_general_settings_end' => array (
            'type' => 'sectionend',
        ),
    ),
);

return $general_options;

