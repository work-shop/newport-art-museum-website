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
     * This method should be called before the 'admin_notices' hook, unless the notice being added is inline.
     * 
     * @param array $notice Associative array of the following format:
     * 
     * [
     *     'message'     => null,
     *     'type'        => 'info', // Can be: 'error', 'warning', 'success', 'info'
     *     'inline'      => false,  // Whether the notice will be displayed inline or at the top of the page.
     *                              // This will not work if the current request is an AJAX request.
     *     'dismissible' => false,
     *     'persist'     => false,  // Whether the notice will persist until shown.
     *                              // A notice would normally be lost if it was added during an AJAX operation or if
     *                              // the 'post_id' condition was not met.
     *     'post_id'     => null    // A post ID to limit the notice to.
     *                              // This will cause the notice to only be displayed if the current post is as specified.
     * ]
     */
    public static function add( $notice ) {
        $notice += [
            'message'     => null,
            'type'        => 'info',
            'inline'      => false,
            'dismissible' => false,
            'persist'     => false,
            'post_id'     => null
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
            return false;
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

        return true;
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
     * Save persistent notices.
     */
    public static function save_notices() {
        update_option( 'woo_mp_notices', array_filter( self::$notices, function ( $notice ) {
            return $notice['persist'];
        } ) );
    }

}

new Notices();