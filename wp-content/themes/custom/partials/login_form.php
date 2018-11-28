<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
<?php wc_print_notices(); ?>
<div class="nam-login-form">
	<form class="woocommerce-form woocommerce-form-login login" method="post">
		<div class="form-top">
			<h4 class="bold">Log in</h4>
		</div>
		<div class="form-body">
			<?php do_action( 'woocommerce_login_form_start' ); ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide username-row">
				<label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
			</p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide password-row">
				<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
				<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
			</p>
			<?php do_action( 'woocommerce_login_form' ); ?>
			<div class="form-options">
				<div class="row">
					<div class="col-6">
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox inline">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
						</label>
					</div>
					<div class="col-6 woocommerce-LostPassword lost_password">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot Password', 'woocommerce' ); ?></a>
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
			<button type="submit" class="woocommerce-Button button" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
		</div>
		<?php do_action( 'woocommerce_login_form_end' ); ?>
	</form>
</div
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
