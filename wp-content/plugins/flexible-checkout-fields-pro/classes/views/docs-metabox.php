<br/>
<?php $pl = get_locale() === 'pl_PL'; ?>
<div id="side-sortables" class="accordion-container fcf-knowledge-base">
	<h3><?php _e( 'Knowledge Base', 'flexible-checkout-fields-pro' ); ?></h3>
	<div class="docs-content accordion-section-content" style="display:block;">
		<h4><?php _e( 'Documentation', 'flexible-checkout-fields-pro' ); ?></h4>
		<ul>
			<li>
				<?php
				if ( $pl ) {
					$link = 'https://www.wpdesk.pl/docs/woocommerce-checkout-fields-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=adding-custom-fields#Dodatkowe_pola_formularza_zamowienia';
				}
				else {
					$link = 'https://www.wpdesk.net/docs/flexible-checkout-fields-pro-woocommerce-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=adding-custom-fields#Adding_Custom_Fields';
				}
				?>
				<a target="_blank" href="<?php echo $link; ?>"><?php _e( 'Adding Custom Fields', 'flexible-checkout-fields-pro' ); ?></a>
			</li>
			<li>
				<?php
				if ( $pl ) {
					$link = 'https://www.wpdesk.pl/docs/woocommerce-checkout-fields-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=field-types#Typy_pol';
				}
				else {
					$link = 'https://www.wpdesk.net/docs/flexible-checkout-fields-pro-woocommerce-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=field-types#Field_Types';
				}
				?>
				<a target="_blank" href="<?php echo $link; ?>"><?php _e( 'Field Types', 'flexible-checkout-fields-pro' ); ?></a>
			</li>
			<li>
				<?php
				if ( $pl ) {
					$link = 'https://www.wpdesk.pl/docs/woocommerce-checkout-fields-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=conditional-logic#Logika_warunkowa';
				}
				else {
					$link = 'https://www.wpdesk.net/docs/flexible-checkout-fields-pro-woocommerce-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=conditional-logic#Conditional_Logic';
				}
				?>
				<a target="_blank" href="<?php echo $link; ?>"><?php _e( 'Conditional Logic', 'flexible-checkout-fields-pro' ); ?></a>
			</li>
			<li>
				<?php
				if ( $pl ) {
					$link = 'https://www.wpdesk.pl/docs/woocommerce-checkout-fields-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=custom-sections#Edycja_dodatkowych_sekcji';
				}
				else {
					$link = 'https://www.wpdesk.net/docs/flexible-checkout-fields-pro-woocommerce-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=custom-sections#Custom_Sections';
				}
				?>
				<a target="_blank" href="<?php echo $link; ?>"><?php _e( 'Custom Sections', 'flexible-checkout-fields-pro' ); ?></a>
			</li>
		</ul>

		<h4><?php _e( 'Usage Examples', 'flexible-checkout-fields-pro' ); ?></h4>
		<ul>
			<li>
				<?php
				if ( $pl ) {
					$link = 'https://www.wpdesk.pl/docs/woocommerce-checkout-fields-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=additional-agreements-at-the-checkout#Dodatkowe_checkboxy';
				}
				else {
					$link = 'https://www.wpdesk.net/docs/flexible-checkout-fields-pro-woocommerce-docs/?utm_source=flexible-checkout-fields-settings&utm_medium=link&utm_campaign=settings-docs-link&utm_content=additional-agreements-at-the-checkout#Additional_Agreements_at_the_Checkout';
				}
				?>
				<a target="_blank" href="<?php echo $link; ?>"><?php _e( 'Additional Agreements at the Checkout', 'flexible-checkout-fields-pro' ); ?></a>
			</li>
		</ul>
	</div>
</div>