
<div class="member-check">
	<div class="member-check-1">
		<div class="member-check-messages-container">
			<?php get_template_part('partials/member_checker_messages' ); ?>
		</div>
		<div class="member-check-links member-check-links-desktop">
			<a href="/contact" class="button button-bordered mr2">Contact Us</a>
			<a href="/member-account-information" class="button button-bordered">Member Account Info</a>
		</div>
	</div>
	<div class="member-check-2">
		<div class="mb3 member-check-form-container">
			<div class="member-check-intro">
				<form class="member-check-form">
					<label>
						Enter your email address
					</label>
					<input type="email" name="email" class="member-check-email-input">
					<input type="submit" name="submit" class="member-check-submit">
				</form>
			</div>
			<div class="member-check-login member-check-hidden">
				<?php login_with_ajax(); ?>
			</div>
		</div>
		<div class="member-check-links member-check-links-mobile">
			<a href="/contact" class="button button-bordered mr2">Contact Us</a>
			<a href="/member-account-information" class="button button-bordered">Member Account Info</a>
		</div>
	</div>
</div>