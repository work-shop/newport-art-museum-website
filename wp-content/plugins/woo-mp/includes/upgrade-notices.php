<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Apply the 'readme.txt' 'Upgrade Notice' section to the Plugins page.
 * 
 * This is similar to what is already done natively on the WordPress Updates page.
 */
class Upgrade_Notices {

    /**
     * Register a hook for the area where we want to insert the notice.
     */
    public function __construct() {
        add_action( 'in_plugin_update_message-' . WOO_MP_BASENAME, [ $this, 'output_upgrade_notice' ], 10, 2 );
    }

    /**
     * Output upgrade notice.
     * 
     * @param object $plugin_data An array of plugin metadata.
     * @param object $response    An array of metadata about the available plugin update.
     */
    public function output_upgrade_notice( $plugin_data, $response ) {

        if ( empty( $response->upgrade_notice ) ) {
            return;
        }

        ?>

        </p>

        <style>
            .woo-mp-upgrade-notice:before {
                float: left;
                font: 400 20px/1 dashicons;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                margin: <?= version_compare( $GLOBALS['wp_version'], '4.6.0', '>' ) ? '0 6px 0 0' : '0 10px 0 -30px' ?>;
                vertical-align: bottom;
                color: #f56e28;
                content: "\f348";
            }

            .woo-mp-upgrade-notice p:before {
                display: none;
            }
        </style>

        <div class="woo-mp-upgrade-notice">
            <?= wp_kses_post( $response->upgrade_notice ) ?>
        </div>

        <p style="display: none;">

        <?php

    }

}

new Upgrade_Notices();