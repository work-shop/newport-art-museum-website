<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Define most used string as constants
 */
defined ( 'YWGC_CUSTOM_POST_TYPE_NAME' ) || define ( 'YWGC_CUSTOM_POST_TYPE_NAME', 'gift_card' );
defined ( 'YWGC_AMOUNTS' ) || define ( 'YWGC_AMOUNTS', '_gift_card_amounts' );
defined ( 'YWGC_META_GIFT_CARD_POST_ID' ) || define ( 'YWGC_META_GIFT_CARD_POST_ID', '_ywgc_gift_card_post_id' );
defined ( 'YWGC_META_GIFT_CARD_NUMBER' ) || define ( 'YWGC_META_GIFT_CARD_NUMBER', '_ywgc_gift_card_number' );
defined ( 'YWGC_META_GIFT_CARD_ORDER_ID' ) || define ( 'YWGC_META_GIFT_CARD_ORDER_ID', '_gift_card_order_id' );
defined ( 'YWGC_META_GIFT_CARD_AMOUNT' ) || define ( 'YWGC_META_GIFT_CARD_AMOUNT', '_gift_card_amount' );
defined ( 'YWGC_DB_VERSION_OPTION' ) || define ( 'YWGC_DB_VERSION_OPTION', 'yith_gift_cards_free_db_version' );

if ( ! function_exists ( 'is_plugin_active' ) ) {
    require_once ( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( ! function_exists ( 'yith_initialize_plugin_fw' ) ) {
    /**
     * Initialize plugin-fw
     */
    function yith_initialize_plugin_fw ( $plugin_dir ) {
        if ( ! function_exists ( 'yit_deactive_free_version' ) ) {
            require_once $plugin_dir . 'plugin-fw/yit-deactive-plugin.php';
        }

        if ( ! function_exists ( 'yith_plugin_registration_hook' ) ) {
            require_once $plugin_dir . 'plugin-fw/yit-plugin-registration-hook.php';
        }

        /* Plugin Framework Version Check */
        if ( ! function_exists ( 'yit_maybe_plugin_fw_loader' ) && file_exists ( $plugin_dir . 'plugin-fw/init.php' ) ) {
            require_once ( $plugin_dir . 'plugin-fw/init.php' );
        }
    }
}

if ( ! function_exists ( 'yith_ywgc_install_woocommerce_admin_notice' ) ) {
    /**
     * Show a notice if WooCommerce is not enabled
     *
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    function yith_ywgc_install_woocommerce_admin_notice () {
        ?>
        <div class="error">
            <p><?php _e ( 'YITH WooCommerce Gift Cards is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-gift-cards' ); ?></p>
        </div>
        <?php
    }
}


if ( ! function_exists ( 'yith_ywgc_install_free_admin_notice' ) ) {

    function yith_ywgc_install_free_admin_notice () {
        ?>
        <div class="error">
            <p><?php _e ( 'You can\'t activate the free version of YITH WooCommerce Gift Cards while you are using the premium one.', 'yith-woocommerce-gift-cards' ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists ( 'yith_ywgc_install' ) ) {
    /**
     * Install the plugin
     *
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    function yith_ywgc_install () {

        if ( ! function_exists ( 'WC' ) ) {
            add_action ( 'admin_notices', 'yith_ywgc_install_woocommerce_admin_notice' );
        } else {
            do_action ( 'yith_ywgc_init' );
        }
    }
}

if ( ! function_exists ( 'yith_ywgc_init' ) ) {
    /**
     * Let's start the plugin
     *
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    function yith_ywgc_init () {

        /**
         * Load text domain and start plugin
         */
        load_plugin_textdomain ( 'yith-woocommerce-gift-cards', false, dirname ( YITH_YWGC_PLUGIN_BASENAME ) . '/languages/' );

        /**
         * ******************************************************
         * Include plugin files
         */
        require_once ( YITH_YWGC_DIR . 'lib/class.yith-woocommerce-gift-cards.php' );
        require_once ( YITH_YWGC_DIR . 'lib/class.ywgc-product-gift-card.php' );
        require_once ( YITH_YWGC_DIR . 'lib/class.ywgc-gift-card.php' );
        require_once ( YITH_YWGC_DIR . 'lib/class.ywgc-plugin-fw-loader.php' );

        //  Let's start the game!
        YITH_WooCommerce_Gift_Cards::get_instance ();
    }
}

if ( ! function_exists ( 'ywgc_instance' ) ) {
    /**
     * The YITH_WooCommerce_Gift_Cards instance
     *
     * @return YITH_WooCommerce_Gift_Cards
     * @author Lorenzo Giuffrida
     * @since  1.0.0
     */
    function ywgc_instance () {
        return YITH_WooCommerce_Gift_Cards::get_instance ();
    }
}