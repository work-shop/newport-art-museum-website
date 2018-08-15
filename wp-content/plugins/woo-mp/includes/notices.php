<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Notice system.
 */
class Notices {

    /**
     * All queued notices.
     * 
     * @var array
     */
    private static $notices = [];

    /**
     * Set up.
     */
    public function __construct() {
        self::$notices = get_option( 'woo_mp_notices', [] );

        add_action( 'admin_notices', [ __CLASS__, 'output_all' ] );
        add_action( 'shutdown', [ __CLASS__, 'save_notices' ] );
    }

    /**
     * Add a notice.
     * 
     * @param array $notice Associative array of the following format:
     * 
     * [
     *     'message'     => NULL,
     *     'type'        => 'info', // Can be: 'error', 'warning', 'success', 'info'
     *     'inline'      => FALSE,  // Whether the notice will be displayed inline or at the top of the page.
     *                              // This will not work if the current request is an AJAX request.
     *     'dismissible' => FALSE,
     *     'post_id'     => NULL    // A post ID to limit the notice to.
     *                              // This will cause the notice to only be displayed if the current post is as specified.
     * ]
     */
    public static function add( $notice ) {
        $notice += [
            'message'     => NULL,
            'type'        => 'info',
            'inline'      => FALSE,
            'dismissible' => FALSE,
            'post_id'     => NULL
        ];

        if ( ! defined( 'DOING_AJAX' ) && $notice['inline'] ) {
            self::output( $notice );

            return;
        }

        self::$notices[] = $notice;
    }

    /**
     * Output a notice.
     * 
     * @param  array $notice Associative array. The format of this parameter is available at the 'add' method.
     * @return bool          Whether the notice was outputted.
     */
    private static function output( $notice ) {

        // Some versions of WordPress will return an ID from 'get_the_ID' within loops.
        if ( $notice['post_id'] && ( get_the_ID() != $notice['post_id'] || get_current_screen()->base != 'post' ) ) {
            return FALSE;
        }

        $classes = [
            'notice',
            'notice-' . $notice['type'],
            $notice['inline'] ? 'inline' : '',
            $notice['dismissible'] ? 'is-dismissible' : ''
        ];

        ?>

        <div class="<?= esc_attr( implode( ' ', $classes ) ) ?>">
            <p><?= wp_kses_post( $notice['message'] ) ?></p>
        </div>

        <?php

        return TRUE;
    }

    /**
     * Attempt to output all notices and remove those that actually got outputted.
     */
    public static function output_all() {
        foreach ( self::$notices as $key => $notice ) {
            if ( self::output( $notice ) ) {
                unset( self::$notices[ $key ] );
            }
        }
    }

    /**
     * Save all queued notices.
     */
    public static function save_notices() {
        update_option( 'woo_mp_notices', self::$notices );
    }

}

new Notices();