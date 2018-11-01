=== Product Customer List for WooCommerce ===
Contributors: kokomoweb, freemius
Tags: woocommerce, customer list, who bought, admin order list, product-specific, export customers to csv, email customers, customer list, customer, list, print, front-end, tickets, shows, courses, customers, shortcode
Requires at least: 4.0
Tested up to: 4.9.8
Stable tag: 2.7.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a list of customers who bought a specific product at the bottom of the product edit page in WooCommerce and send them e-mails.

== Description ==

A plugin that simply displays a list of customers who bought a specific product at the bottom of the WooCommerce product edit page or as a shortcode. You can also send an email to the list of customers, print the list or export it as a CSV, PDF or Excel file. Requires WooCommerce 2.2+ to be installed and activated. 

Great for sending out e-mails or getting a list of customers for courses, for shows or for product recalls.

= Features: =

* Support for variable products
* Options page to select which info columns to display
* Displays customer name, email, phone number, address, order number, order date, shipping method, order total and quantity for each product
* Shortcode to display orders in the front-end. You can select which information to display using attributes
* Button to e-mail all customers for a specific product using your favorite e-mail client (b.c.c.)
* Email selected customers
* Export the customer list to CSV (great for importing into Mailchimp!)
* Export the customer list to Excel
* Export the customer list to PDF (choose your orientation and page size in the settings)
* Copy the customer list to clipboard
* Print the list of customers
* Search any column in the list
* Sort by any column in the list
* Drag and drop columns to reorder them
* Localized and WPML / Polylang ready (.pot file included)
* Included translations: French, French (France), French (Canada), Spanish, Dutch, Dutch (Netherlands), Dutch (Belgium).
* All functions are pluggable
* Performance oriented
* Responsive
* Multisite compatible
* Support for custom statuses

= Premium version: =

* Support for Custom Fields
* Support for WooCommerce Custom Fields (RightPress)
* Support for WooTours
* Support for WooEvents
* Support for YITH WooCommerce Product Add-ons
* Shortcode by variation ID
* Datatables functionalities for the shortcode (export PDF, export CSV, print, email customers, search, paging, etc...).
* Change default sorting column
* Much more coming soon!

To upgrade the plugin to the premium version, simply click on "upgrade" under the plugin title in the plugin list page, or [purchase it here](https://checkout.freemius.com/mode/dialog/plugin/2009/plan/2994/).

= Contributors: =, freemius
* Support for variable products: [Alexandre Simard](https://profiles.wordpress.org/brocheafoin/)
* Dutch translation: [pieterclaesen](https://wordpress.org/support/profile/pieterclaesen)
* Portuguese (Brazil) translation: [Marcello Ruoppolo](https://profiles.wordpress.org/mragenciadigital)

== Installation ==

1. Upload the plugin files to the "/wp-content/plugins/wc-product-customer-list" directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Edit any WooCommerce product to view the list of customers that bought it.
4. Make sure that the 'Product Customer List for WooCommerce’ checkbox is ticked in your screen options.
5. Access the settings page in WooCommerce / Settings / Products / Product Customer List


== Frequently Asked Questions ==

= How do I use the shortcode? =

**WARNING: The shortcode can display very private information about your customers (if you decide to). Please use with caution.**

To display the list in the front end, simply use the following shortcode: [customer_list] along with the following attributes and their "true" or "false" value:

* **product** : The ID of the product for which you wish to display the customer list. If you do not put an ID number, it will use the ID of the current product (if used on a product page)
* **table_title** : Add a title that will be added at the top of the list. Uses <h3> tags that can be styled via css.
* **order_status** : The order status for which the shortcode will display your customers. If you have more than one, seperate them with commas. Options are: wc-completed, wc-processing, wc-on-hold, wc-pending, wc-cancelled, wc-refunded, wc-failed. Default is "wc-completed".
* **show_titles** : Display the titles of each column in the head of the table. Titles cannot be modified at this time.
* **order_number** : The ID of the order. Can be a variation ID in the premium version of the plugin.
* **order_date** : The date of the order.
* **billing_first_name** : Billing first name.
* **billing_last_name** : Billing last name.
* **billing_company** : Billing company.
* **billing_email** : Billing e-mail.
* **billing_phone** : Billing phone.
* **billing_address_1** : Billing address 1.
* **billing_address_2** : Billing address 2.
* **billing_city** : Billing city.
* **billing_state** : Billing state.
* **billing_postalcode** : Billing postal / Zip code.
* **billing_country** : Billing country.
* **shipping_first_name** : Shipping first name.
* **shipping_last_name** : Shipping last name.
* **shipping_company** : Shipping company.
* **shipping_address_1** : Shipping address 1.
* **shipping_address_2** : Shipping address 2.
* **shipping_city** : Shipping city.
* **shipping_state** : Shipping state.
* **shipping_postalcode** : Shipping postal / Zip code.
* **shipping_country** : Shipping country.
* **customer_message** : Message from the customer.
* **customer_id** : The ID of the customer (if registered).
* **customer_username** : The user name of the Customer (if registered).
* **customer_username_link** : Add a link to the customer's profile page (requires customer_username).
* **customer_display_name** : The display name of the Customer (if registered).
* **order_status_column** : The order status.
* **order_payment** : The order payment method.
* **order_shipping** : The order shipping method.
* **order_coupon** : The coupon(s) used at checkout.
* **order_variations** : The variations column. Default: true.
* **order_total** : The order total.
* **order_qty** : The quantity of products purchased.
* **order_qty_total** : The total of products purchased for all customers. This field is added at the bottom of the table.
* **order_qty_total_column** : The total of products purchased for all customers. This field is added as a column.
* **limit** : The amount of orders you want to display.

**Premium version**

* **custom_fields** : A comma separated list of your custom field keys. You can copy the keys that are available in the list in the admin settings.
* **sortable** : Activates datatables script and enables sorting by column. Default: false.
* **export_pdf** : Adds button to export the list as PDF. Default: false.
* **export_csv** : Adds button to export the list as CSV. Default: false.
* **email_all** : Adds button to e-mail all customers with your e-mail client (b.c.c.). Default: false.
* **copy** : Adds button to copy table data to clipboard. Default: false.
* **print** : Adds button to print the table. Default: false.
* **search** : Adds option to search the list. Default: false.
* **paging** : Adds paging to list. Default: false.
* **info** : Displays additional information such as posts per page. Default: false.
* **scrollx** : Enables horizontal scrolling. Default: false.
* **pdf_pagesize** : Sets the paper format for the PDF export. Options are LETTER|LEGAL|A3|A4|A5. Default: LETTER.
* **pdf_orientation** : Sets the paper orientation for the PDF export. Options are portrait|landscape. Default: portrait.

If you do not use any attributes for the product ID, it will display the customers of the current product (on a product page). 

Here is an example containing every attribute of the shortcode, with the default values. Please note that it is not needed to include each attribute, you can simply use the attributes that you wish to modify (replace 999 with your product id):

`[customer_list product="999" show_titles="true" order_status="wc-completed" order_number="false" order_date="false" billing_first_name="true" billing_last_name="true" billing_company="false" billing_email="false" billing_phone="false" billing_address_1="false" billing_address_2="false" billing_city="false" billing_state="false" billing_postalcode="false" billing_country="false" shipping_first_name="false" shipping_last_name="false" shipping_company="false" shipping_address_1="false" shipping_address_2="false" shipping_city="false" shipping_state="false" shipping_postalcode="false" shipping_country="false" customer_message="false" customer_id="false" customer_username="false" order_status="false" order_payment="false" order_shipping="false" order_coupon="false" order_variations="true" order_total="false" order_qty="false" order_qty_total="false" order_qty_total_column="false" limit="9999"]`

= Why doesn't the customer list appear when I edit a product? =

Make sure that the 'Product Customer List for WooCommerce’ checkbox is ticked in your screen options.

= Where can I select which columns to display =

You can access the settings page in WooCommerce / Settings / Products / Product Customer List

= How can I reorder the columns? = 

You can reorder the columns by dragging them and dropping them in the order you want. The browser will remember your selection. You can press the "Reset column order" button at any time to reset the order to it's initial state.

= Available hooks and filters = 

Many hooks and filters. Documentation coming soon.

== Screenshots ==

1. The customer list in the product edit page.
2. The settings page.

== Changelog ==

= 2.7.8 =
* Fix for shortcode on WPML
* Add support for RightPress
* Add "split by row" option for RightPress

= 2.7.7 =
* Updated datatables to latest version
* Simplified the customer email selection
* Updated .pot file
* Updated freemius to the latest version
* Added setting to select the default column to order by (Pro)
* Added setting to enable/disable state save (Pro).

= 2.7.6 =
* Fixed unicode character related errors.

= 2.7.5 =
* Added customer_display_name in shortcode
* Added table_title in shortcode
* Added Customer display name column in admin
* Updated .pot file
* Premium: Added support for WooEvents
* Premium: Fixed issue with custom fields in shortcode
* Premium: Fixed issue with email_all in shortcode

= 2.7.4 =
* Updated .pot file and re-uploaded french files
* Freemius GDPR compliance
* Compatibility with YITH WooCommerce Product Add-ons

= 2.7.3 =
* Premium: Added function wpcl_product_sales($product, $status) to return actual sales.
* Free: Fixed variable column

= 2.7.2 =
* Fixed other bug with Freemius

= 2.7.1 =
* Fixed bug with Freemius
* Updated .pot file

= 2.7.0 =
* Fixed issue with billing email in shortcode
* Premium version: Added support for shortcode by variation ID.

= 2.6.9 =
* Fixed issue with settings page (again)

= 2.6.8 =
* Fixed issue with settings page

= 2.6.7 =
* Added support for Preemius / licensing system

= 2.6.6 =
* Added support for Pro version
* Added multiple hooks and filters (documentation to come)
* Added style for shortcode
* Added variations settings for admin
* Added variations settings for shortcode
* Updated shortcode documentation

= 2.6.5 =
* Fixed shameful PHP notice.

= 2.6.4 =
* Fixed duplicate order_status option in shortcode (please use order_status_column to display the order status column.
* Added a few more shortcode options (please see FAQ on how to use the shortcode).

= 2.6.3 =
* Returning shortcode output instead of echo (thanks to aerobass)

= 2.6.2 =
* Fixed rogue '</div>' at the end of the shortcode (thanks to aerobass)

= 2.6.1 =
* Added shortcode attributes for all columns

= 2.6.0 =
* Fixed compatibility bug in PHP 7.1 (Thanks to mmagnani)

= 2.5.9 =
* Added username column

= 2.5.8 =
* Fixed partially refunded orders

= 2.5.7 =
* Added billing company column
* Added shipping company column
* Added coupons used

= 2.5.6 =
* Added compatibility with Avada theme and The events calendar plugin
* Changed payment output to title instead of slug
* Added option to hide partially refunded orders

= 2.5.5 =
* Fixed datatables related javascript errors
* Added missing translation in settings page

= 2.5.4 =
* Fixed bug where some variations wouldn’t display (again!)

= 2.5.3 =
* Fixed bug where some variations wouldn’t display
* Added row selection for emails
* Added shipping method column
* Updated screenshots

= 2.5.2 =
* Added dropdown to select list length

= 2.5.1 =
* Added hook “wpcl_after_email_button” to display content after the email button.
* Fixed variation display.

= 2.5.0 =
* Fixed issue where the email list would be incomplete.

= 2.4.9 =
* Added support for custom statuses

= 2.4.8 =
* Fixed deprecation notices and bugs in variable products

= 2.4.7 =
* Script optimizations

= 2.4.6 =
* Fixed settings text mismatch

= 2.4.5 =
* Fixed bug where current date would be show instead of the order date
* Added plugin action links
* Added order total column
* Added translations for order statuses

= 2.4.4 =
* WooCommerce 3.0+ compatibility
* Script optimizations (thanks to [Alexandre Simard](https://profiles.wordpress.org/brocheafoin/))
* Code optimization
* Improved multisite compatibility
* Updated .pot file

= 2.4.3 =
* Added Customer ID column
* Fixed wpdb notice (thanks to [Michal Bluma](https://profiles.wordpress.org/michalbluma))

= 2.4.2 =
* Fixed multisite compatibility

= 2.4.1 =
* Fixed compatibility issue with plugin “WooCommerce Amazon S3 storage”

= 2.4.0 =
* Added multisite compatibility

= 2.3.9 =
* Added the option for city in the settings

= 2.3.8 =
* Fixed bug where quantity would not show up in shortcode

= 2.3.7 =
* Added compatibility with WPML

= 2.3.6 =
* Fixed PDF orientation and size.
* Added payment method column and option.

= 2.3.5 =
* Added settings for PDF orientation and size.

= 2.3.4 =
* Fixed bug where refunds would appear in the list.
* Removed old unused code.

= 2.3.3 =
* Fixed trailing slash in scripts and stylesheet urls which could prevent them to load on certain servers.

= 2.3.2 =
* Fixed bug where featured image uploader wouldn’t work when activated.
* Updated PDFMake script to latest version (local)

= 2.3.1 =
* Added column reordering and state save
* Fixed javascript localization handling (wp_localize_script)

= 2.3.0 =
* Changed print and export system to reflect filters and order
* Added export to excel
* Added export to PDF
* Added copy to clipboard

= 2.2.9 =
* Added all missing order statuses in settings

= 2.2.8 =
* Fixed bug where shipping postal code wouldn’t be displayed in CSV export

= 2.2.7 =
* Fixed bug where two extra columns would appear while printing
* Fixed bug where there would be an error if you delete a variation after it is purchased

= 2.2.6 =
* Added Portuguese (Brazil) translation (thanks to [Marcello Ruoppolo](https://profiles.wordpress.org/mragenciadigital))
* Fixed alignment shortcode bug and added default product as current product

= 2.2.5 =
* Added support for variable products (thanks to [Alexandre Simard](https://profiles.wordpress.org/brocheafoin/))
* Bug fixes & optimisation

= 2.2.4 =
* Fixed Urls for wordpress subdirectory installs

= 2.2.3 =
* Fixed issue where columns would shift when printing

= 2.2.2 =
* Added front-end shortcode
* Fixed default order type in settings

= 2.2.1 =
* Added date column
* Added compatibility with Wordpress 4.5
* Fixed some bugs

= 2.2.0 =
* Added settings tab section
* Added support for horizontal scrolling
* Loaded datatables CSS and JS via CDN

= 2.1.2 =
* Fixed undefined object error when there are no customers
* Fixed text domain to match plugin slug
* Added Dutch (Belgium) translation

= 2.1.1 =
* Fixed issue where the plugin would prevent WooCommerce from displaying or saving product attributes (price & stock)

= 2.1.0 =
* Added pagination
* Added search
* Added sortable columns
* Added Dutch (Netherlands) translation (thanks to [pieterclaesen](https://wordpress.org/support/profile/pieterclaesen))
* Added row actions
* Fixed empty table notice
* Cleaned code

= 2.0.4 =
* Fixed other “cannot send session cache limiter” warning 

= 2.0.3 =
* Fixed bug where variations wouldn’t be added to the quantity column sum

= 2.0.2 =
* Fixed “session_start(): Cannot send session cookie” warning
* Fixed “session_start(): Cannot send session cache limiter” warning

= 2.0.1 =
* Fixed quantity bug

= 2.0.0 =
* Added “export to CSV” button
* Added print button

= 1.11 =
* Improved table styling
* Added Spanish translation
* Optimized code: now even lighter files!

= 1.1 =
* Added quantity column
* Fixed and optimized WooCommerce plugin check
* Improved code readability
* Updated translations

= 1.02 =
* Fixed email button

= 1.01 =
* Updated deprecated WooCommerce order statuses
* Added pluggable functions
* Optimized code

= 1.0 =
* First stable version