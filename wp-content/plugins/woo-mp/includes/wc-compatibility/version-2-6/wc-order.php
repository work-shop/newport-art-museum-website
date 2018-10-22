<?php

namespace Woo_MP\WC_Compatibility\Version_2_6;

defined( 'ABSPATH' ) || die;

/**
 * Transparently handles differences between WC_Order on WooCommerce 2.6.x and the latest version.
 * 
 * This is a drop-in replacement for WC_Order.
 * All methods that are simply 2.6.x property names prefixed with 'get_' are automatically supported.
 * For example, WC_Order::get_id() is supported even though there is no special handling for that particular method.
 * All methods that do not fit that pattern are supported on an as-needed basis.
 */
class WC_Order {

    /**
     * The core order object.
     * 
     * This will normally be an instance of 'WC_Order'.
     * 
     * @var object
     */
    private $order;

    /**
     * A map of methods to the meta fields that they update.
     * 
     * @var array
     */
    private $setters_to_meta = [
        'set_payment_method'       => '_payment_method',
        'set_payment_method_title' => '_payment_method_title',
        'set_transaction_id'       => '_transaction_id'
    ];

    /**
     * Set the original order object.
     * 
     * @param object $order The order object. This will normally be an instance of 'WC_Order'.
     */
    public function __construct( $order ) {
        $this->order = $order;
    }

    /**
     * Simulate the latest WC API on a WC 2.6.x order object.
     * 
     * @param  string $name      The method name.
     * @param  array  $arguments The arguments to pass to the method.
     * @return mixed             The return value of the method.
     */
    public function __call( $name, $arguments ) {
        if ( isset( $this->setters_to_meta[ $name ] ) ) {
            return $this->update_meta_data(
                $this->setters_to_meta[ $name ],
                isset( $arguments[0] ) ? $arguments[0] : null
            );
        }

        if ( is_callable( [ $this->order, $name ] ) ) {
            return call_user_func_array( [ $this->order, $name ], $arguments );
        }

        return $this->order->{preg_replace( '/^get_/', '', $name )};
    }

    /**
     * This method signature should match 'WC_Abstract_Order::get_currency()'.
     * 
     * The $context parameter is not functional.
     * 
     * @link https://docs.woocommerce.com/wc-apidocs/class-WC_Abstract_Order.html Latest Documentation
     */
	public function get_currency( $context = 'view' ) {
        return $this->order->get_order_currency() ?: get_woocommerce_currency();
    }

    /**
     * This method signature should match 'WC_Order::get_date_paid()'.
     * 
     * The $context parameter is not functional.
     * 
     * @link https://docs.woocommerce.com/wc-apidocs/class-WC_Order.html Latest Documentation
     */
	public function get_date_paid( $context = 'view' ) {
        return $this->order->paid_date;
    }

    /**
     * This method signature should match 'WC_Order::set_date_paid()'.
     * 
     * @link https://docs.woocommerce.com/wc-apidocs/class-WC_Order.html Latest Documentation
     */
	public function set_date_paid( $date = null ) {
        if ( $date ) {
            $time_zone = new \DateTimeZone( get_option( 'timezone_string' ) );
            $date      = ( new \DateTime( ( is_numeric( $date ) ? '@' : '' ) . $date, $time_zone ) )
                ->setTimezone( $time_zone )
                ->format( 'Y-m-d H:i:s' );
        }

        $this->update_meta_data( '_paid_date', $date );
    }

    /**
     * This method signature should match 'WC_Order::save()'.
     * 
     * @link https://docs.woocommerce.com/wc-apidocs/class-WC_Order.html Latest Documentation
     */
    public function save() {
        return $this->get_id();
    }

    /**
     * This method signature should match 'WC_Data::get_meta()'.
     * 
     * The $context parameter is not functional.
     * 
     * @link https://docs.woocommerce.com/wc-apidocs/class-WC_Data.html Latest Documentation
     */
    public function get_meta( $key = '', $single = true, $context = 'view' ) {
        return get_post_meta( $this->get_id(), $key, $single );
    }

    /**
     * This method signature should match 'WC_Data::update_meta_data()'.
     * 
     * The $meta_id parameter is not functional.
     * 
     * @link https://docs.woocommerce.com/wc-apidocs/class-WC_Data.html Latest Documentation
     */
    public function update_meta_data( $key, $value, $meta_id = 0 ) {
        update_post_meta( $this->get_id(), $key, $value );
    }

}