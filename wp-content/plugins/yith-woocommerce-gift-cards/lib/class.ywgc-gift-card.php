<?php
if ( ! defined ( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists ( 'YWGC_Gift_Card' ) ) {

    /**
     *
     * @class   YWGC_Gift_Card
     * @package Yithemes
     * @since   1.0.0
     * @author  Your Inspiration Themes
     */
    class YWGC_Gift_Card {
        /**
         * @var int the gift card id
         */
        public $ID;

        /**
         * @var int  the product id
         */
        public $product_id;

        /**
         * @var int the order id
         */
        public $order_id;

        /**
         * @var string the gift card code
         */
        public $gift_card_number;

        /**
         * @var float the gift card amount
         */
        public $amount;

        /**
         * Constructor
         *
         * Initialize plugin and registers actions and filters to be used
         *
         * @param $args int|array|WP_Post
         *
         * @since  1.0
         * @author Lorenzo Giuffrida
         */
        public function __construct ( $args = null ) {
            $this->temporary_key = md5 ( microtime ( true ) . rand () );

            if ( is_numeric ( $args ) || ( $args instanceof WP_Post ) ) {
                $args = $this->get_array ( $args );//retrieve args from the post object
            }

            if ( $args ) {
                foreach ( $args as $key => $value ) {
                    $this->{$key} = $value;
                }
            }
        }

        /**
         * Get an array of attribute and values for this object
         *
         * @param int|array|WP_Post $post the object to be used
         *
         * @return array|null
         */
        private function get_array ( $post ) {
            if ( is_numeric ( $post ) ) {
                $post = get_post ( $post );
            } else if ( ! ( $post instanceof WP_Post ) || ( YWGC_CUSTOM_POST_TYPE_NAME != $post->post_type ) ) {
                return null;
            }

            if ( ! isset( $post ) ) {
                return null;
            }

            return array (
                "ID"               => $post->ID,
                "amount"           => get_post_meta ( $post->ID, YWGC_META_GIFT_CARD_AMOUNT, true ),
                "gift_card_number" => $post->post_title,
                "product_id"       => $post->post_parent,
                "order_id"         => get_post_meta ( $post->ID, YWGC_META_GIFT_CARD_ORDER_ID, true ),
            );
        }

        /**
         * Set the current gift card amount
         *
         * @param float $amount the gift card amount
         *
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function set_amount ( $amount ) {

            $this->amount = $amount;
            if ( $this->ID ) {
                update_post_meta ( $this->ID, YWGC_META_GIFT_CARD_AMOUNT, $amount );
            }
        }

        /**
         * Retrieve the gift card balance
         *
         * @return float the current gift card amount
         *
         */
        public function get_amount () {
            return $this->amount;
        }

        /**
         * Generate a new univoque code
         *
         * @param bool|false $overwrite overwrite previous code if exists
         *
         * @return bool
         * @author Lorenzo Giuffrida
         * @since  1.0.0
         */
        public function generate_gift_card_code ( $overwrite = false ) {
            if ( ! $overwrite && ! empty( $this->gift_card_number ) ) {
                return false;   // gift card code not updated
            }

            //  Create a new gift card number

            //http://stackoverflow.com/questions/3521621/php-code-for-generating-decent-looking-coupon-codes-mix-of-alphabets-and-number
            $code = strtoupper ( substr ( base_convert ( sha1 ( uniqid ( mt_rand () ) ), 16, 36 ), 0, 16 ) );

            $code = sprintf ( "%s-%s-%s-%s",
                substr ( $code, 0, 4 ),
                substr ( $code, 4, 4 ),
                substr ( $code, 8, 4 ),
                substr ( $code, 12, 4 )
            );

            $this->gift_card_number = apply_filters( 'yith_ywgc_gift_card_code', $code );

            return true;
            //todo check if this code still exists
        }

        /**
         * Deduct an amount from the gift card
         *
         * @param int $amount the amount to be deducted from current gift card balance
         */
        public function deduct_amount ( $amount ) {
            $new_amount = $this->get_amount () - $amount;
            if ( $new_amount < 0 ) {
                $new_amount = 0;
            }
            $this->set_amount ( $new_amount );
        }

        /**
         * Check if the gift card has enough balance to cover the amount requested
         *
         * @param float $amount the amount to be deducted from current gift card balance
         *
         * @return bool the gift card has enough credit
         */
        public function has_credit ( $amount ) {
            return $this->get_amount () >= $amount;
        }

        /**
         * Save the current question
         */
        public function save () {
            // Create post object
            $args = array (
                'post_title'  => $this->gift_card_number,
                'post_status' => 'publish',
                'post_type'   => YWGC_CUSTOM_POST_TYPE_NAME,
                'post_parent' => $this->product_id,
            );

            if ( ! isset( $this->ID ) ) {
                // Insert the post into the database
                $this->ID = wp_insert_post ( $args );
            } else {
                $args[ "ID" ] = $this->ID;
                $this->ID     = wp_update_post ( $args );
            }

            //  Save Gift Card post_meta
            update_post_meta ( $this->ID, YWGC_META_GIFT_CARD_AMOUNT, $this->amount );
            update_post_meta ( $this->ID, YWGC_META_GIFT_CARD_ORDER_ID, $this->order_id );

            do_action ( 'yith_gift_cards_saved', $this );

            return $this->ID;
        }
    }
}