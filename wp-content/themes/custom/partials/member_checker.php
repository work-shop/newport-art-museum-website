
<div class="member-check">
	<div class="member-check-1">
		<div class="member-check-messages-container">
			<?php get_template_part('partials/member_checker_messages' ); ?>
		</div>
		<div class="member-check-links member-check-links-desktop">
			<a href="/contact" class="button button-bordered mr2">Contact Us</a>
			<a href="/member-account-information" class="button button-bordered">About Member Accounts</a>
		</div>
	</div>
	<div class="member-check-2">
		<?php //var_dump($_GET); ?>
		<div class="mb3 member-check-form-container">
			<div class="member-check-intro">
				<form class="member-check-form">
					<label id="member-check-heading">
						Enter your email address
					</label>
					<input type="email" name="email" class="member-check-email-input">
					<input type="submit" name="submit" class="member-check-submit">
				</form>
			</div>
			<div class="member-check-login member-check-hidden">
				<?php get_template_part('partials/login_form'); ?>
			</div>
		</div>
		<div class="member-check-links member-check-links-mobile">
			<a href="/contact" class="button button-bordered mr2">Contact Us</a>
			<a href="/member-account-information" class="button button-bordered">About Member Accounts</a>
		</div>
	</div>
</div>