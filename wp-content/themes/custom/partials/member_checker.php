
<div class="member-check row">
	<div class="col-md-6 mb3">
		<h4 class="member-check-label bold">
			Enter your email address
		</h4>
		<form class="member-check-form">
			<input type="email" name="email" class="member-check-email-input">
			<input type="submit" name="submit" class="member-check-submit">
		</form>
	</div>
	<div class="col-md-6">
		<div class="member-check-messages">
			<div class="member-check-message member-check-message-no-account">
				Sorry, we couldn't find an account with that email address. 
				<br>
				<br>
				Your account may be registered with a different email, or you don't have an account on this website. 
			</div>
			<div class="member-check-message member-check-message-has-account">
				<h4>Great, we found your account. </h4>
				<a href="/my-account/subscriptions" class="button">Log In</a>
				<h4>Or</h4>
				<?php echo do_shortcode( '[passwordless-login]'); ?>
				<!-- <a href="/my-account/subscriptions" class="button">Email me a link to log in with</a> -->
			</div>
			<div class="member-check-message member-check-message-error">

			</div>
		</div>
	</div>
</div>