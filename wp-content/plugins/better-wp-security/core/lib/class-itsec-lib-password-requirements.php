<?php
/**
 * Tool to manage password requirements across modules.
 *
 * @since   3.9.0
 * @license GPLv2+
 */

/**
 * Class ITSEC_Lib_Password_Requirements
 */
class ITSEC_Lib_Password_Requirements {

	const LOGIN_ACTION = 'itsec_update_password';
	const META_KEY = '_itsec_update_password_key';

	/** @var string */
	private $error_message = '';

	public function run() {

		add_action( 'user_profile_update_errors', array( $this, 'forward_profile_pass_update' ), 0, 3 );
		add_action( 'validate_password_reset', array( $this, 'forward_reset_pass' ), 10, 2 );
		add_action( 'profile_update', array( $this, 'set_password_last_updated' ), 10, 2 );

		add_action( 'itsec_login_interstitial_init', array( $this, 'register_interstitial' ) );
	}

	/**
	 * When a user's password is updated, or a new user created, verify that the new password is valid.
	 *
	 * @param WP_Error         $errors
	 * @param bool             $update
	 * @param WP_User|stdClass $user
	 */
	public function forward_profile_pass_update( $errors, $update, $user ) {

		if ( ! isset( $user->user_pass ) ) {
			return;
		}

		if ( ! $update ) {
			$context = 'admin-user-create';
		} elseif ( isset( $user->ID ) && $user->ID === get_current_user_id() ) {
			$context = 'profile-update';
		} else {
			$context = 'admin-profile-update';
		}

		$args = array(
			'error'   => $errors,
			'context' => $context
		);

		if ( isset( $user->role ) ) {
			$args['role'] = $user->role;
		}

		self::validate_password( $user, $user->user_pass, $args );
	}

	/**
	 * When a user attempts to reset their password, verify that the new password is valid.
	 *
	 * @param WP_Error $errors
	 * @param WP_User  $user
	 */
	public function forward_reset_pass( $errors, $user ) {

		if ( ! isset( $_POST['pass1'] ) || is_wp_error( $user ) ) {
			// The validate_password_reset action fires when first rendering the reset page and when handling the form
			// submissions. Since the pass1 data is missing, this must be the initial page render. So, we don't need to
			// do anything yet.
			return;
		}

		self::validate_password( $user, $_POST['pass1'], array(
			'error'   => $errors,
			'context' => 'reset-password',
		) );
	}

	/**
	 * Whenever a user object is updated, set when their password was last updated.
	 *
	 * @param int    $user_id
	 * @param object $old_user_data
	 */
	public function set_password_last_updated( $user_id, $old_user_data ) {

		$user = get_userdata( $user_id );

		if ( $user->user_pass === $old_user_data->user_pass ) {
			return;
		}

		delete_user_meta( $user_id, 'itsec_password_change_required' );
		update_user_meta( $user_id, 'itsec_last_password_change', ITSEC_Core::get_current_time_gmt() );
	}

	/**
	 * Register the password change interstitial.
	 *
	 * @param ITSEC_Lib_Login_Interstitial $lib
	 */
	public function register_interstitial( $lib ) {
		$lib->register( 'update-password', array( $this, 'render_interstitial' ), array(
			'show_to_user' => array( __CLASS__, 'password_change_required' ),
			'info_message' => array( __CLASS__, 'get_message_for_password_change_reason' ),
			'submit'       => array( $this, 'submit' ),
		) );
	}

	/**
	 * Render the interstitial.
	 *
	 * @param WP_User $user
	 */
	public function render_interstitial( $user ) {
		?>

		<div class="user-pass1-wrap">
			<p><label for="pass1"><?php _e( 'New Password', 'better-wp-security' ); ?></label></p>
		</div>

		<div class="wp-pwd">
				<span class="password-input-wrapper">
					<input type="password" data-reveal="1"
						   data-pw="<?php echo esc_attr( wp_generate_password( 16 ) ); ?>" name="pass1" id="pass1"
						   class="input" size="20" value="" autocomplete="off" aria-describedby="pass-strength-result"/>
				</span>
			<div id="pass-strength-result" class="hide-if-no-js" aria-live="polite"><?php _e( 'Strength indicator', 'better-wp-security' ); ?></div>
		</div>

		<p class="user-pass2-wrap">
			<label for="pass2"><?php _e( 'Confirm new password' ) ?></label><br/>
			<input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off"/>
		</p>

		<p class="description indicator-hint"><?php echo wp_get_password_hint(); ?></p>
		<br class="clear"/>

		<p class="submit">
			<input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large"
				   value="<?php esc_attr_e( 'Update Password', 'better-wp-security' ); ?>"/>
		</p>

		<?php
	}

	/**
	 * Handle the request to update the user's password.
	 *
	 * @param WP_User $user
	 * @param array   $data POSTed data.
	 *
	 * @return WP_Error|null
	 */
	public function submit( $user, $data ) {

		if ( empty( $data['pass1'] ) ) {
			return new WP_Error(
				'itsec-password-requirements-empty-password',
				__( 'Please enter your new password.', 'better-wp-security' )
			);
		}

		$error = self::validate_password( $user, $data['pass1'] );

		if ( $error->get_error_message() ) {
			return $error;
		}

		$error = wp_update_user( array(
			'ID'        => $user->ID,
			'user_pass' => $data['pass1']
		) );

		if ( is_wp_error( $error ) ) {
			return $error;
		}

		return null;
	}

	/**
	 * Get a message indicating to the user why a password change is required.
	 *
	 * @param WP_User $user
	 *
	 * @return string
	 */
	public static function get_message_for_password_change_reason( $user ) {

		if ( ! $reason = self::password_change_required( $user ) ) {
			return '';
		}

		/**
		 * Retrieve a human readable description as to why a password change has been required for the current user.
		 *
		 * Modules MUST HTML escape their reason strings before returning them with this filter.
		 *
		 * @param string $message
		 */
		$message = apply_filters( "itsec_password_change_requirement_description_for_{$reason}", '' );

		if ( $message ) {
			return $message;
		}

		return esc_html__( 'A password change is required for your account.', 'better-wp-security' );
	}

	/**
	 * Validate a user's password.
	 *
	 * @param WP_User|stdClass|int $user
	 * @param string               $new_password
	 * @param array                $args
	 *
	 * @return WP_Error Error object with new errors.
	 */
	public static function validate_password( $user, $new_password, $args = array() ) {

		$args = wp_parse_args( $args, array(
			'error'   => new WP_Error(),
			'context' => ''
		) );

		$error = isset( $args['error'] ) ? $args['error'] : new WP_Error();

		$user = $user instanceof stdClass ? $user : ITSEC_Lib::get_user( $user );

		if ( ! $user ) {
			$error->add( 'invalid_user', esc_html__( 'Invalid User', 'better-wp-security' ) );

			return $error;
		}

		/**
		 * Fires when modules should validate a password according to their rules.
		 *
		 * @since 3.9.0
		 *
		 * @param \WP_Error         $error
		 * @param \WP_User|stdClass $user
		 * @param string            $new_password
		 * @param array             $args
		 */
		do_action( 'itsec_validate_password', $error, $user, $new_password, $args );

		return $error;
	}

	/**
	 * Flag that a password change is required for a user.
	 *
	 * @param WP_User|int $user
	 * @param string      $reason
	 */
	public static function flag_password_change_required( $user, $reason ) {
		$user = ITSEC_Lib::get_user( $user );

		if ( $user ) {
			update_user_meta( $user->ID, 'itsec_password_change_required', $reason );
		}
	}

	/**
	 * Check if a password change is required for the given user.
	 *
	 * @param WP_User|int $user
	 *
	 * @return string|false Either the reason code a change is required, or false.
	 */
	public static function password_change_required( $user ) {
		$user = ITSEC_Lib::get_user( $user );

		if ( ! $user ) {
			return false;
		}

		$reason = get_user_meta( $user->ID, 'itsec_password_change_required', true );

		if ( ! $reason ) {
			return false;
		}

		return $reason;
	}

	/**
	 * Get the GMT time the user's password has last been changed.
	 *
	 * @param WP_User|int $user
	 *
	 * @return int
	 */
	public static function password_last_changed( $user ) {

		$user = ITSEC_Lib::get_user( $user );

		if ( ! $user ) {
			return 0;
		}

		$changed    = (int) get_user_meta( $user->ID, 'itsec_last_password_change', true );
		$deprecated = (int) get_user_meta( $user->ID, 'itsec-password-updated', true );

		if ( $deprecated > $changed ) {
			return $deprecated;
		}

		return $changed;
	}
}