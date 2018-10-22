<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Enqueue a script with a 'woo-mp-' handle prefix and a version number set to the plugin's version.
 *
 * @param string $handle    Name of the script. Should be unique.
 * @param string $src       Full URL of the script, or path of the script relative to the WordPress root directory.
 * @param array  $deps      An array of registered script handles this script depends on.
 * @param bool   $in_footer Whether to enqueue the script before </body> instead of in the <head>.
 */
function script( $handle, $src = '', $deps = [], $in_footer = false ) {
    wp_enqueue_script( 'woo-mp-' . $handle, $src, $deps, WOO_MP_VERSION, $in_footer );
}

/**
 * Enqueue a stylesheet with a 'woo-mp-' handle prefix and a version number set to the plugin's version.
 *
 * @param string $handle Name of the stylesheet. Should be unique.
 * @param string $src    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
 * @param array  $deps   An array of registered stylesheet handles this stylesheet depends on.
 * @param string $media  The media for which this stylesheet has been defined.
 *                       Accepts media types like 'all', 'print' and 'screen', or media queries like
 *                       '(orientation: portrait)' and '(max-width: 640px)'.
 */
function style( $handle, $src = '', $deps = [], $media = 'all' ) {
    wp_enqueue_style( 'woo-mp-' . $handle, $src, $deps, WOO_MP_VERSION, $media );
}

/**
 * Returns true if the version of WooCommerce is 3.0.0 or above, false otherwise.
 *
 * @return bool The answer.
 */
function is_wc3() {
    return version_compare( WC_VERSION, '3.0.0', '>' );
}