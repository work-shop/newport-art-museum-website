<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! class_exists( 'YITH_WooCommerce_Gift_Cards' ) ) {
	
	/**
	 *
	 * @class   YITH_WooCommerce_Gift_Cards
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Lorenzo Giuffrida
	 */
	class YITH_WooCommerce_Gift_Cards {
		
		
		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 */
		protected static $instance;

        /**
         * @var string Premium version landing link
         */
        protected $_premium_landing = '//yithemes.com/themes/plugins/yith-woocommerce-gift-cards/';

        /**
         * @var string Plugin official documentation
         */
        protected $_official_documentation = 'https://docs.yithemes.com/yith-woocommerce-gift-cards/';

        /**
         * @var string Plugin panel page
         */
        protected $_panel_page = 'yith_woocommerce_gift_cards_panel';

        /**
         * @var string Official plugin landing page
         */
        protected $_premium_live = 'https://plugins.yithemes.com/yith-woocommerce-gift-cards/';
		
		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		protected function __construct() {
			
			/**
			 * Show a message on general tab stating that a new gift card product type can be created
			 */
			add_action( 'woocommerce_admin_field_ywgc_create_first_product', array(
				$this,
				'notify_create_first_product'
			) );
			
			/**
			 * If the plugin is disabled, stop now!
			 */
			if ( 'no' == get_option( 'yith_ywgc_enable_plugin' ) ) {
				return;
			}
			
			/**
			 * Do some stuff on plugin init
			 */
			add_action( 'init', array( $this, 'on_plugin_init' ) );
			
			/** Add styles and scripts */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_files' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_files' ) );
			
			/**
			 * Add the "Gift card" type to product type list
			 */
			add_filter( 'product_type_selector', array( $this, 'add_gift_card_product_type' ) );
			
			/**
			 * Append gift card amount generation controls
			 */
			if ( version_compare( WC()->version, '2.6.0', '<' ) ) {
				
				/**
				 * Append gift card amount generation controls to general tab of product page, below the SKU element
				 */
				add_action( 'woocommerce_product_options_sku', array( $this, 'show_gift_card_product_content' ) );
				
			} else {
				/**
				 * Append gift card amount generation controls to general tab on product page
				 */
				add_action( 'woocommerce_product_options_general_product_data', array(
					$this,
					'show_gift_card_product_content'
				) );
			}
			
			/**
			 * * Save gift card amount when a product is saved
			 */
			add_action( 'save_post', array( $this, 'save_gift_card' ), 1, 2 );
			
			/**
			 * Ajax call for adding and removing gift card amount
			 */
			add_action( 'wp_ajax_add_gift_card_amount', array( $this, 'add_gift_card_amount_callback' ) );
			add_action( 'wp_ajax_remove_gift_card_amount', array( $this, 'remove_gift_card_amount_callback' ) );
			
			add_action( 'woocommerce_gift-card_add_to_cart', array( $this, 'variable_add_to_cart' ), 30 );
			
			/*
			 * Custom add_to_cart handler for gift card product type
			 */
			add_action( 'woocommerce_add_to_cart_handler_gift-card', array( $this, 'add_to_cart_handler' ) );
			
			add_filter( 'woocommerce_get_cart_item_from_session', array(
				$this,
				'woocommerce_get_cart_item_from_session'
			), 10, 3 );
			
			add_filter( 'woocommerce_checkout_coupon_message', array( $this, 'ask_for_coupon_or_gift_card' ) );
			
			/**
			 * Generate a valid card number for every gift card product in the order
			 */
            $allowed_status = apply_filters( 'yith_ywgc_generate_gift_card_on_order_status',
                array( 'completed', 'processing' ) );

            foreach ( $allowed_status as $status ){
                add_action( 'woocommerce_order_status_' . $status, array( $this, 'generate_gift_card_number' ), 5, 1 );
            }


			
			/*
			 * Enable coupons in cart page when this plugin is enable, so a gift code is possible but
			 * don't permit coupon code if coupons are disabled
			 */
			//add_filter( 'woocommerce_coupons_enabled', array( $this, 'show_field_for_gift_code' ) );
			
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_item_meta' ) );
			
			/**
			 * Verify if a coupon code inserted on cart page or checkout page belong to a valid gift card.
			 * In this case, make the gift card working as a temporary coupon
			 */
			add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'verify_coupon_code' ), 10, 2 );
			
			/*
			 * Check if a gift card discount code was used and deduct the amount from the gift card.
			 */
			if ( version_compare( WC()->version, '3.0', '<' ) ) {
				add_action( 'woocommerce_order_add_coupon', array(
					$this,
					'deduct_amount_from_gift_card'
				), 10, 5 );
			} else {
				add_action( 'woocommerce_new_order_item', array(
					$this,
					'deduct_amount_from_gift_card_wc_3_plus'
				), 10, 3 );
			}
			
			/**
			 * Prevent more than one order to get the gift card amount applied
			 */
			add_action( 'woocommerce_after_checkout_validation', array(
				$this,
				'woocommerce_after_checkout_validation'
			) );
			
			add_action( 'woocommerce_order_item_meta_start', array(
				$this,
				'show_gift_card_code',
			), 10, 3 );

            /* === Show Plugin Information === */

            add_filter( 'plugin_action_links_' . plugin_basename( YITH_YWGC_DIR . '/' . basename( YITH_YWGC_FILE ) ), array(
                $this,
                'action_links',
            ) );

            add_filter( 'yith_show_plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 5 );
			
		}
		
		
		public function show_gift_card_code( $order_item_id, $item, $order ) {
			
			$code = wc_get_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_NUMBER );
			
			if ( ! empty( $code ) ) {
				
				printf( '<br>' . __( 'Gift card code: %s', 'yith-woocommerce-gift-cards' ), $code );
			}
		}
		
		/**
		 * Add some data to the options table
		 */
		public function update_database() {
			
			$db_version = get_option( YWGC_DB_VERSION_OPTION );
			
			if ( ! $db_version ) {
				
				//  Initialize database tables...
				global $wpdb;
				
				//  Update metakey from YITH Gift Cards 1.0.0
				$query = "Update {$wpdb->prefix}woocommerce_order_itemmeta
                        set meta_key = '" . YWGC_META_GIFT_CARD_POST_ID . "'
                        where meta_key = 'gift_card_post_id'";
				$wpdb->query( $query );
				
				$db_version = '1.0.0';
			}
			
			/**
			 * Start the database update step by step
			 */
			if ( version_compare( $db_version, '1.0.0', '<=' ) ) {
				
				global $wpdb;
				
				//  Update metakey from YITH Gift Cards 1.0.0
				$query = "Update {$wpdb->prefix}woocommerce_order_itemmeta
                        set meta_key = '" . YWGC_META_GIFT_CARD_NUMBER . "'
                        where meta_key = 'gift_card_number'";
				$wpdb->query( $query );
				
				$db_version = '1.0.1';
			}
			
			add_option( YWGC_DB_VERSION_OPTION, YITH_YWGC_DB_CURRENT_VERSION );
		}
		
		/**
		 *  Execute all the operation need when the plugin init
		 */
		public function on_plugin_init() {
			$this->init_post_type();
			
			$this->update_database();
		}
		
		/**
		 * Add frontend style
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function enqueue_frontend_files() {
			if ( ! is_product () ) {
				return;
			}
			
			global $post;
			
			if ( ! $post ) {
				return;
			}
			
			$product      = wc_get_product( $post );
			$product_type = version_compare( WC()->version, '3.0', '<' ) ? $product->product_type : $product->get_type();
			
			if ( 'gift-card' != $product_type ) {
				return;
			}
			
			//  register and enqueue ajax calls related script file
			wp_register_script( "ywgc-frontend",
				YITH_YWGC_URL . 'assets/js/' . yit_load_js_file( 'ywgc-frontend.js' ),
				array(
					'jquery',
					'woocommerce',
				),
				YITH_YWGC_VERSION,
				true );
			
			wp_enqueue_script( "ywgc-frontend" );
		}
		
		/**
		 * Enqueue scripts on administration comment page
		 *
		 * @param $hook
		 */
		function enqueue_backend_files( $hook ) {
			$screen = get_current_screen();
			$enabled_ids = array(
				'edit-product',
				'product'
			);
			
			if ( ! $screen ) {
				return;
			}
			
			if ( ! in_array( $screen->id, $enabled_ids ) ) {
				return;
			}
			
			/**
			 * Add styles
			 */
			wp_enqueue_style( 'ywgc-backend-css', YITH_YWGC_ASSETS_URL . '/css/ywgc-backend.css' );
			
			/**
			 * Add scripts
			 */
			wp_register_script( "ywgc-backend",
				YITH_YWGC_URL . 'assets/js/' . yit_load_js_file( 'ywgc-backend.js' ),
				array(
					'jquery',
					'jquery-blockui',
				),
				YITH_YWGC_VERSION,
				true );
			
			wp_localize_script( 'ywgc-backend', 'ywgc_vars', array(
				'loader'   => apply_filters( 'yith_questions_and_answers_loader', YITH_YWGC_ASSETS_URL . '/images/loading.gif' ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			) );
			
			wp_enqueue_script( "ywgc-backend" );
		}
		
		/**
		 * Add the "Gift card" type to product type list
		 *
		 * @param array $types current product types
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function add_gift_card_product_type( $types ) {
			$types['gift-card'] = __( "Gift card", 'yith-woocommerce-gift-cards' );
			
			return $types;
		}
		
		/**
		 * Show the gift card amounts list
		 *
		 * @param int $product_id the product id
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		private function show_gift_card_amount_list( $product_id ) {
			$product = wc_get_product( $product_id );

            $gift_card_product = new WC_Product_Gift_Card($product);

			$amounts = $gift_card_product->get_gift_card_product_amounts();
			?>
			<p class="form-field _gift_card_amount_field">
				
				<?php if ( $amounts ): ?>
					<?php foreach ( $amounts as $amount ) : ?>
						<span class="variation-amount"><?php echo wc_price( $amount ); ?>
							<input type="hidden" name="gift-card-amounts[]" value="<?php _e( $amount ); ?>">
                        <a href="#" class="remove-amount"></a></span>
					<?php endforeach; ?>
				<?php else: ?>
					<span
						class="no-amounts"><?php _e( "You don't have configured any gift card yet", 'yith-woocommerce-gift-cards' ); ?></span>
				<?php endif; ?>
			</p>
			<?php
		}
		
		/**
		 * Retrieve the html content that shows the gift card amounts list
		 *
		 * @param $product_id int gift card product id
		 *
		 * @return string
		 */
		private function gift_card_amount_list_html( $product_id ) {
			ob_start();
			$this->show_gift_card_amount_list( $product_id );
			$html = ob_get_contents();
			ob_end_clean();
			
			return $html;
		}
		
		/**
		 * Show controls on backend product page to let create the gift card price
		 */
		public function show_gift_card_product_content() {
			global $thepostid;
			?>
			<div class="options_group show_if_gift-card">
				<p class="form-field">
					<label><?php _e( "Gift card amount", 'yith-woocommerce-gift-cards' ); ?></label>
					<span class="wrap add-new-amount-section">
                    <input type="text" id="gift_card-amount" name="gift_card-amount" class="short" style=""
                           placeholder="">
                    <a href="#" class="add-new-amount"><?php _e( "Add", 'yith-woocommerce-gift-cards' ); ?></a>
                </span>
				</p>
				<?php
				$this->show_gift_card_amount_list( $thepostid );
				?>
			</div>
			<?php
		}
		
		/**
		 * Save gift card amount when a product is saved
		 *
		 * @param int     $post_id the post id
		 * @param WP_Post $post    the post object
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		function save_gift_card( $post_id, $post ) {
			
			$product = wc_get_product( $post_id );
			if ( null == $product ) {
				return;
			}
			
			if ( ! isset( $_POST["product-type"] ) || ( 'gift-card' != $_POST["product-type"] ) ) {
				
				return;
			}
			
			// verify this is not an auto save routine.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			
			$amounts = isset( $_POST["gift-card-amounts"] ) ? $_POST["gift-card-amounts"] : array();
			
			/**
			 * Update gift card amounts
			 */
			ywgc_instance()->save_gift_card_amounts( $post_id, $amounts );
		}
		
		/**
		 * Add a new amount to a gift card prdduct
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function add_gift_card_amount_callback() {
			$amount = number_format( $_POST['amount'], 2, wc_get_price_decimal_separator(), '' );
			
			$product_id = intval( $_POST['product_id'] );
			
			$res = ywgc_instance()->add_amount_to_gift_card( $product_id, $amount );
			
			wp_send_json( array( "code" => $res, "value" => $this->gift_card_amount_list_html( $product_id ) ) );
		}
		
		/**
		 * Remove amount to a gift card prdduct
		 *
		 * @since  1.0
		 * @author Lorenzo Giuffrida
		 */
		public function remove_gift_card_amount_callback() {
			$amount     = number_format( $_POST['amount'], 2, wc_get_price_decimal_separator(), '' );
			$product_id = intval( $_POST['product_id'] );
			
			$res = ywgc_instance()->remove_amount_to_gift_card( $product_id, $amount );
			
			wp_send_json( array( "code" => $res ) );
		}
		
		/**
		 * Show the add_to_cart template for gift card
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function variable_add_to_cart() {
			global $product;
			
			// Load the template
			wc_get_template( 'single-product/add-to-cart/gift-card.php', '', '', trailingslashit( YITH_YWGC_TEMPLATES_DIR ) );
		}
		
		public function show_cart_message_on_added_product( $product_id, $quantity = 1 ) {
			//  From WC 2.6.0 the parameter format in wc_add_to_cart_message changed
			$gt_255 = version_compare( WC()->version, '2.5.5', '>' );
			$param  = $gt_255 ? array( $product_id => $quantity ) : $product_id;
			wc_add_to_cart_message( $param, true );
		}

        /**
         * Custom add_to_cart handler for gift card product type
         *
         * @return bool
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         * @throws Exception
         */
		public function add_to_cart_handler( $url ) {
			$product_id = absint( $_REQUEST['add-to-cart'] );
			$quantity   = isset( $_REQUEST['quantity'] ) ? intval( $_REQUEST['quantity'] ) : 1;
			$amount     = $_REQUEST['gift_amounts'];


			for ( $i = 0; $i < $quantity; $i ++ ) {
				$new_gift_card             = new YWGC_Gift_Card();

				$new_gift_card->product_id = $product_id;
				$new_gift_card->set_amount($amount);

				//fixing warning with no price values
                update_post_meta($product_id, '_price', $amount);
				
				WC()->cart->add_to_cart( $product_id, 1, 0, array(), $new_gift_card );
			}


			// If we added the product to the cart we can now optionally do a redirect.
			if ( 0 === wc_notice_count( 'error' ) ) {
				if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', $url ) ) {
					wp_safe_redirect( $url );
					exit;
				} elseif ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
					wp_safe_redirect( wc_get_cart_url() );
					exit;
				}
			}

			$this->show_cart_message_on_added_product( $product_id, $quantity );

			return true;
		}
		
		/**
		 * Set the gift card amount
		 *
		 * @param array  $session_data
		 * @param array  $values
		 * @param string $key
		 *
		 * @return mixed
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function woocommerce_get_cart_item_from_session( $session_data, $values, $key ) {
			if ( ! ( $session_data["data"] instanceof WC_Product_Gift_Card ) ) {
				return $session_data;
			}
			
			if ( isset( $session_data['amount'] ) ) {
				$session_data['data']->set_price( $session_data['amount'] );
			}
			
			return $session_data;
		}
		
		/**
		 * Modify the standard text shown asking for coupon code
		 *
		 * @param string $text current text being shown
		 *
		 * @return string
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function ask_for_coupon_or_gift_card( $text ) {
			return __( 'Do you have a coupon or a gift card?', 'yith-woocommerce-gift-cards' ) . ' <a href="#" class="showcoupon">' . __( 'Click here to enter your code', 'woocommerce' ) . '</a>';
		}
		
		/**
		 * Register the custom post type
		 */
		public function init_post_type() {
			$args = array(
				'label'               => __( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				'description'         => __( 'Gift Cards', 'yith-woocommerce-gift-cards' ),
				//'labels' => $labels,
				// Features this CPT supports in Post Editor
				'supports'            => array(
					'title',
					//'editor',
					//'author',
				),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'menu_position'       => 9,
				'can_export'          => false,
				'has_archive'         => false,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-clipboard',
				'query_var'           => false,
			);
			
			// Registering your Custom Post Type
			register_post_type( YWGC_CUSTOM_POST_TYPE_NAME, $args );
		}
		
		/**
		 * When the order is completed, generate a card number for every gift card product
		 *
		 * @param int|WC_Order $order The order which status is changing
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function generate_gift_card_number( $order_id ) {
			
			if ( is_numeric( $order_id ) ) {
				$order = wc_get_order( $order_id );
			}
			

			

			foreach ( $order->get_items( 'line_item' ) as $order_item_id => $order_item_data ) {
				
				$product_id = $order_item_data["product_id"];
				$amount     = $order_item_data["line_total"] + $order_item_data["line_tax"];
				
				$prev_code = wc_get_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_NUMBER );
				if ( ! empty( $prev_code ) ) {
					continue;
				}
				
				$product = wc_get_product( $product_id );
				
				if ( ! ( $product instanceof WC_Product_Gift_Card ) ) {
					continue;
				}
				
				$gift_card = ywgc_instance()->create_gift_card( $product_id, yit_get_prop( $order, 'id' ), $amount );
				
				wc_update_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_NUMBER, $gift_card->gift_card_number );
				wc_update_order_item_meta( $order_item_id, YWGC_META_GIFT_CARD_POST_ID, $gift_card->ID );
			}
		}
		
		/**
		 * Show the fields for entering the gift card code
		 *
		 * @param bool $coupon_enabled if coupons are enabled by WooCommerce settings
		 *
		 * @return bool
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function show_field_for_gift_code( $coupon_enabled ) {
			
			if ( is_cart() ) {
				?>
				<div class="<?php echo apply_filters( 'yith_ywgc_cart_discount_classes', "coupon" ); ?>">
					
					<label
						for="coupon_code"><?php echo apply_filters( 'yith_ywgc_cart_discount_label', __( "Discount code: ", 'yith-woocommerce-gift-cards' ) ); ?></label>
					<input type="text" name="coupon_code" class="input-text" id="coupon_code" value=""
					       placeholder="<?php echo apply_filters( 'yith_ywgc_cart_discount_placeholder', __( "Discount code", 'yith-woocommerce-gift-cards' ) ); ?>">
					<input type="submit" class="button" name="apply_coupon"
					       value="<?php echo apply_filters( 'yith_ywgc_cart_discount_submit_text', __( "Apply discount", 'yith-woocommerce-gift-cards' ) ); ?>">
				
				</div>
				<?php
				return false;
			}
			
			return $coupon_enabled;
			
		}
		
		/**
		 * Modify the label being shown
		 *
		 * @param string          $attribute_label current attribute label
		 * @param string          $meta_key        current metakey
		 * @param WC_Product|bool $product
		 *
		 * @return string|void
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function modify_attribute_label( $attribute_label, $meta_key, $product = false ) {
			
			if ( $meta_key == 'gift_card_number' ) {
				$attribute_label = __( 'Gift card code', 'yith-woocommerce-gift-cards' );
			}
			
			return $attribute_label;
		}
		
		/**
		 * Hide some item meta
		 *
		 * @param array $args the item meta array
		 *
		 * @return array
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function hide_item_meta( $args ) {
			$args[] = 'gift_card_post_id';
			
			return $args;
		}
		
		/**
		 * Verify the gift card value
		 *
		 * @param array  $return_val the returning value
		 * @param string $code       the gift card code
		 *
		 * @return array
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function verify_coupon_code( $return_val, $code ) {
			$gift_card = ywgc_instance()->get_gift_card_by_code( $code );
			
			if ( null == $gift_card ) {
				return $return_val;
			}
			
			if ( $gift_card->ID ) {
				$temp_coupon_array = array(
					'discount_type' => 'fixed_cart',
					'coupon_amount' => $gift_card->get_amount(),
					'amount'        => $gift_card->get_amount(),
					'id'            => true,
				);
				
				
				return $temp_coupon_array;
			}
			
			return $return_val;
		}
		
		/**
		 * Deduct an amount from the gift card balance
		 *
		 * @param int    $id                  the order id
		 * @param int    $item_id             the item id
		 * @param string $code                the gift card code
		 * @param float  $discount_amount     the amount to deduct
		 * @param float  $discount_amount_tax the tax amount to deduct
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function deduct_amount_from_gift_card( $id, $item_id, $code, $discount_amount, $discount_amount_tax ) {
			$gift = ywgc_instance()->get_gift_card_by_code( $code );
			if ( $gift != null ) {
				$gift->deduct_amount( $discount_amount + $discount_amount_tax );
			}
		}
		
		public function deduct_amount_from_gift_card_wc_3_plus( $item_id, $item, $order_id ) {
			if ( $item instanceof WC_Order_Item_Coupon ) {
				$this->deduct_amount_from_gift_card( $item->get_id(), $item_id, $item->get_code(), $item->get_discount(), $item->get_discount_tax() );
			}
		}
		
		/**
		 * Prevent the current order to completed if the gift card code is no more valid
		 *
		 * @param array $posted
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function woocommerce_after_checkout_validation( $posted ) {
			
			$gift_cards_used = WC()->cart->coupon_discount_amounts;
			
			if ( $gift_cards_used ) {
				foreach ( $gift_cards_used as $code => $amount ) {
					//  Check if the code belong to a gift card and there is enough credit
					//  to cover the amount requested.
					
					$gift = ywgc_instance()->get_gift_card_by_code( $code );
					
					if ( ( $gift != null ) && ! $gift->has_credit( $amount ) ) {
						wc_add_notice( sprintf( __( "The gift card identified by the code %s has no credit left.", 'yith-woocommerce-gift-cards' ), $code ), "error" );
					}
				}
			}
		}
		
		
		/**
		 * Save the gift card product amounts
		 *
		 * @param int   $product_id the gift card product id
		 * @param array $amounts    current amount list
		 *
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function save_gift_card_amounts( $product_id, $amounts = array() ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				
				yit_save_prop( $product, array( YWGC_AMOUNTS => $amounts ) );
			}
		}
		
		/**
		 * Add a new amount to a gift card
		 *
		 * @param int   $product_id the gift card product id
		 * @param float $amount     the amount to add
		 *
		 * @return bool amount added, false if the same value still exists
		 */
		public function add_amount_to_gift_card( $product_id, $amount ) {
			$product = wc_get_product( $product_id );

			$gift_card_product = new WC_Product_Gift_Card($product);

			$amounts = $gift_card_product->get_gift_card_product_amounts();

			if ( ! in_array( $amount, $amounts ) ) {

				$amounts[] = $amount;
				sort( $amounts, SORT_NUMERIC );
				$this->save_gift_card_amounts( $product_id, $amounts );
				
				return true;
			}
			
			return false;
		}
		
		/**
		 * Remove an amount from a gift card
		 *
		 * @param int   $product_id the gift card product id
		 * @param float $amount     the amount to remove
		 *
		 * @return bool amount added, false if the same value still exists
		 */
		public function remove_amount_to_gift_card( $product_id, $amount ) {
			$product = wc_get_product( $product_id );
			
			if ( ! $product instanceof WC_Product_Gift_Card ) {
				return;
			}
			
			$amounts = $product->get_gift_card_product_amounts();
			
			if ( in_array( $amount, $amounts ) ) {
				if ( ( $key = array_search( $amount, $amounts ) ) !== false ) {
					unset( $amounts[ $key ] );
				}
				
				
				$this->save_gift_card_amounts( $product_id, $amounts );
				
				return true;
			}
			
			return false;
		}
		
		/**
		 * Create a gift card
		 *
		 * @param int   $product_id the gift card product id
		 * @param int   $order_id   the order id
		 * @param float $amount     the amount
		 *
		 * @return YWGC_Gift_Card
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function create_gift_card( $product_id, $order_id, $amount ) {
			$gift_card = new YWGC_Gift_Card();
			$gift_card->generate_gift_card_code();
			$gift_card->set_amount( $amount );
			$gift_card->product_id = $product_id;
			$gift_card->order_id   = $order_id;
			
			$gift_card->save();
			
			return $gift_card;
		}
		
		/**
		 * Retrieve a gift card for a specific code
		 *
		 * @param string $code the gift card code to search for
		 *
		 * @return YWGC_Gift_Card
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public function get_gift_card_by_code( $code ) {
			$object = get_page_by_title( $code, OBJECT, YWGC_CUSTOM_POST_TYPE_NAME );
			if ( null == $object ) {
				return null;
			}
			$args = array( 'gift_card_number' => $code );
			
			return new YWGC_Gift_Card( $object );
		}
		
		public function notify_create_first_product( $value ) {
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php echo esc_html( $value['title'] ) ?>
				</th>
				
				<td class="forminp plugin-option">
					<span><?php echo esc_html( $value['desc'] ); ?></span>
					<br>
					<a href="<?php echo esc_url( admin_url( '/post-new.php?post_type=product' ) ); ?>"><?php _e( "Create your first gift card", 'yith-woocommerce-gift-cards' ); ?></a>
				</td>
			</tr>
			<?php
		}

        /**
         * Action links
         *
         *
         * @return void
         * @since    1.3.2
         * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
         */
        public function action_links( $links ) {

            $links = yith_add_action_links( $links, $this->_panel_page, false );
            return $links;

        }

        /**
         * Plugin Row Meta
         *
         *
         * @return void
         * @since    1.3.2
         * @author   Daniel Sanchez <daniel.sanchez@yithemes.com>
         */
        public function plugin_row_meta( $new_row_meta_args, $plugin_meta, $plugin_file, $plugin_data, $status, $init_file = 'YITH_YWGC_FREE_INIT' ) {

            if ( defined( $init_file ) && constant( $init_file ) == $plugin_file ) {
                $new_row_meta_args['slug'] = YITH_YWGC_SLUG;
            }

            return $new_row_meta_args;
        }

	}
}