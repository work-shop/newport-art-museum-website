=== WooCommerce Manual Payment ===
Contributors: bfl
Tags: backend, manual, phone, payment, woocommerce
Requires at least: 4.4
Tested up to: 4.9
Requires PHP: 5.5
Stable tag: 1.13.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Process payments right from the backend. No need to leave the WooCommerce Edit Order screen.

== Description ==
Charge credit and debit cards directly from the WooCommerce Edit Order page. Fully secure.

### Features
* **Charge a credit or debit card**
* **Refund a payment** (Pro)

== Installation ==
Scroll down for configuration instructions.
### Requirements
* WordPress 4.4+
* WooCommerce 2.6+
* PHP 5.5+
* An SSL certificate (not needed for eWAY)
* Curl (only needed for eWAY)

If you're not sure whether your website is compatible, please contact your website administrator, web developer, or hosting company. You can also post your question in the support forum.
### Automatic Installation
1. Go to **Plugins > Add New**.
2. In the search box, type "woo-mp".
3. Click **Install Now**.
4. When it's finished installing, click **Activate**.
5. Now you can move on to the **Configuration** section below.
### Manual Installation
If you need to install the plugin manually, you can follow the instructions [here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation). Once that's done, you can move on to the **Configuration** section below.
### Configuration
To get started, you'll want to choose a payment processor and enter some API keys.
#### Stripe
1. Follow [these instructions](https://support.stripe.com/questions/where-do-i-find-my-api-keys) to find your API keys.
2. Go to the WooCommerce settings page and choose **Manual Payment**.
3. Choose **Stripe** from the drop-down and click **Save changes**.
4. Now choose **Stripe** from the sub-menu (under the main tabs).
5. Copy and paste your **Live Secret Key** (from your Stripe account settings) into the **Secret Key** field.
6. Copy and paste your **Live Publishable Key** (from your Stripe account settings) into the **Publishable Key** field.
7. Click **Save changes**. That's it, you're all set.
#### Authorize.Net
1. Follow [these instructions](https://support.authorize.net/authkb/index?page=content&id=A576) to find your API keys.
2. Go to the WooCommerce settings page and choose **Manual Payment**.
3. Choose **Authorize.Net** from the drop-down and click **Save changes**.
4. Now choose **Authorize.Net** from the sub-menu (under the main tabs).
5. Copy and paste your **API Login ID** (from your Authorize.Net account settings) into the **Login ID** field.
6. Copy and paste your **Transaction Key** (from your Authorize.Net account settings) into the **Transaction Key** field.
7. Follow these instructions to find your **Public Client Key**:
    1. Log in to the Authorize.Net merchant interface and navigate to **Account > Settings > Security Settings > General Security Settings > Manage Public Client Key**.
    2. In the **Create New Public Client Key** section, enter your secret answer to the secret question and click Submit.
8. Copy and paste your **Public Client Key** (from your Authorize.Net account settings) into the **Client Key** field.
9. Click **Save changes**. That's it, you're all set.
#### eWAY
1. Follow [these instructions](https://go.eway.io/s/article/How-do-I-setup-my-Live-eWAY-API-Key-and-Password) to find your API keys.
2. Go to the WooCommerce settings page and choose **Manual Payment**.
3. Choose **eWAY** from the drop-down and click **Save changes**.
4. Now choose **eWAY** from the sub-menu (under the main tabs).
5. Copy and paste your **API Key** (from your eWAY account settings) into the **API Key** field.
6. Copy and paste your **API Password** (from your eWAY account settings) into the **API Password** field.
9. Click **Save changes**. That's it, you're all set.

== Frequently Asked Questions ==
= Which payment processors are supported? =
As of now, Stripe, Authorize.Net, and eWAY are supported.
= Can I authorize a charge without capturing it? =
Yes. You can choose whether to capture charges.
= Can I process multiple payments for a single order? =
Yes, you can make as many payments as you want.
= Can I process partial payments? =
Yes.
= Can I process partial refunds? (Pro) =
Yes.

== Screenshots ==
1. The charge form.
2. The refund form (Pro).
3. The general settings page.
4. The Stripe settings page.
5. The Authorize.Net settings page.

== Changelog ==
= 1.14.0 =
* Declare support for WooCommerce 3.5.
* Add support for multisite.
* Add support for renaming the plugin directory and the main plugin file.
* Display PHP and JavaScript errors encountered while processing payments.
* Add CSRF protection.
* Don't deactivate the plugin when a system requirement is not met.
* Update the Stripe SDK to the latest version.
= 1.13.0 =
* Automatically reduce order item stock levels after a payment. There is now a setting to control this feature.
* Add charge amount autocomplete suggestions. The amount field now contains a button which will open a menu allowing you to easily populate the field with either the order total or the amount of the order total that has not been paid yet (if applicable). You can also open this menu by pressing the up/down arrow keys while the field is focused.
= 1.12.3 =
* Support WooCommerce 3.4.
= 1.12.2 =
* Fix payments not working for auto-draft orders on WooCommerce 3.x with the "Update Order Status When" option enabled.
* Fix payments not working for auto-draft orders on WooCommerce 2.6 with Stripe.
* Display errors thrown by the Stripe.js library.
= 1.12.1 =
* Fix autoloader so it doesn't try to load symbols from other plugins.
= 1.12.0 =
* Add eWAY gateway.
* Add support for multi-currency stores.
* The order note that is added when a payment is made will now indicate which user processed the payment.
* Scroll the page to the top after processing a payment. This makes the success notice and order note immediately visible.
* Fix bug where processing a payment on the *Add new order* page would cause another new order to be created.
* Improve a few Stripe errors.
= 1.11.2 =
* Improve settings organization.
* Do more field validation before passing off requests to payment gateways. This results in faster errors for invalid field values.
* Fix JS error which would occur when certain types of Authorize.Net errors were encountered.
* Minor design tweaks.
= 1.11.1 =
* Fix bug where charge panel would freeze instead of displaying certain errors.
* Use a more reliable technique to determine if a charge made with Authorize.Net has been held for review.
= 1.11.0 =
* Send "Taxable" field to Authorize.Net for "Itemized Order Information".
* Implement the Authorize.Net "Itemized Order Information" feature for WooCommerce 2.6.
* When sending "Itemized Order Information" to Authorize.Net, use the "Item Name" from the line item itself, not the associated product.
* Fix bug where sending "Itemized Order Information" to Authorize.Net would result in an error if any of the associated products had been deleted.
* Remove HTML tags and shortcodes from line item descriptions before sending them to Authorize.Net.
* Trim billing and shipping information before sending it to Authorize.Net. This will prevent errors from occuring when the values being sent are too long.
* Improve Authorize.Net errors.
* Update the Stripe SDK to the latest version.
* Remove unnecessary files from the Stripe SDK.
* Fix PHP notice and unnecessary icon on the Plugins page when an update is available and there is no upgrade notice.
* The success message and order note will now indicate when a payment is held for review.
* Utilize the core WordPress notice design for charge error messages.
= 1.10.1 =
* Patch bug where attempting to send order item details to Authorize.Net on WooCommerce 2.6 results in an error.
* Fix bug where sending an order item that doesn't have a price to Authorize.Net would result in an error.
= 1.10.0 =
* Add payment method title option for all gateways.
* Symlinking the plugin directory is now fully supported.
= 1.9.0 =
* Allow any status to be set for the "Update Order Status To" option. This means that you can now choose a status that has been added by a plugin.
* Refresh the page after a successful transaction. This makes it impossible to accidentally overwrite changes made to the order in the background.
* Populate the Authorize.Net "Invoice Number" field with order numbers instead of order IDs. These are usually the same, however there are plugins that allow the order number to be customized. These plugins are now supported. Stripe already supports this.
* Prevent direct access to plugin files.
* Add plugin requirements verification.
= 1.8.1 =
* Add WooCommerce version check support. This will confirm compatibility with version 3.3.
* Bump minimum required PHP version to 5.5.
= 1.8.0 =
* Add official WooCommerce payment records to orders. There is now a setting to control when an official payment record is saved.
= 1.7.1 =
* Mobile design improvements.
* Tested with WordPress 4.9.
= 1.7.0 =
* Added the ability to send level 2 data to Authorize.Net.
* Design improvements.
= 1.6.2 =
* Add handling for Authorize.Net transaction detail character limits.
= 1.6.1 =
* Fix Authorize.Net error when order contains product with no SKU.
= 1.6.0 =
* Add option to send billing details to Authorize.Net.
* Add option to send item details to Authorize.Net.
* Tested with WordPress 4.8.
* Update help links to go directly to installation section.
* Improve error handling.
= 1.5.0 =
* Add option to send customer name and email to Stripe.
* Add option to send shipping details to Authorize.Net.
* Add option to update order status when chosen condition is met.
* Send order number to payment processor.
* Improve error messages.
* Add charge amount to Charge button.
* Add support for Stripe zero-decimal currencies.
* Change success message for authorizations.
* Remove curl dependency.
* Fix charge amount formatting.
= 1.4.1 =
* Make "Capture Payment" on by default.
= 1.4.0 =
* Add "Capture Payment" option.
* Fix Authorize.Net bug.
* Small design improvement on the settings page.
= 1.3.4 =
* Fix plugin conflict.
= 1.3.3 =
* Update help links.
= 1.3.2 =
* Fix bug: If there was a problem with the setup of the plugin, the "Screen Options" and "Help" buttons at the top of the Edit Order page did not work.
* Make it clear when there is no payment processor chosen. Previously it would show "Stripe" because it was the first option. This could make the user think that a payment processor was already chosen. Now it shows "Select a payment processor...".
= 1.3.1 =
* Patch faulty setting.
* Fix typos.
* Add handling for invalid API keys.
* Fix PHP notice.
= 1.3.0 =
* Add transaction description setting.
* Switch from curl to WP functions for Authorize.Net. Curl is no longer a requirement for Authorize.Net.
* Fix small design issue with loading animation on Edge.
* Fix small design issue with tabs.
* Fix some setup bugs.
* Update Stripe SDK to latest version.
= 1.2.0 =
* Added ability to process multiple payments without refreshing the page.
* Added help link to settings page and initial setup instructions.
* Improved user-friendliness of error messages.
* Minor design changes and assorted tweaks.
* Added compatibility check.
* Update readme.
= 1.1.0 =
* Added amount field to charge form.
* Added description to Authorize.Net refund transactions.
* Code improvements.
= 1.0.0 =
* Initial release.

== Upgrade Notice ==
= 1.9.0 =
We recommend creating a full website backup before updating.