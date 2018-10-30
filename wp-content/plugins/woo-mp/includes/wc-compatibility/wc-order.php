<?php

namespace Woo_MP\WC_Compatibility;

defined( 'ABSPATH' ) || die;

/**
 * Transparently handles differences in WC_Order on different versions of WooCommerce.
 * 
 * This is intended to be a drop-in replacement for WC_Order,
 * however each feature is only supported on an as-needed basis.
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
     * The class to pass method calls on to.
     * 
     * @var object
     */
    private $class;

    /**
     * Set the original order object.
     * 
     * @param object $order The order object. This will normally be an instance of 'WC_Order'.
     */
    public function __construct( $order ) {
        $this->order = $order;
    }

    /**
     * Call a method on the core order object or pass the call on to the relevant version's shim.
     * 
     * @param  string $name      The method name.
     * @param  array  $arguments The arguments to pass to the method.
     * @return mixed             The return value of the method.
     */
    public function __call( $name, $arguments ) {
        if ( ! $this->class ) {
            $this->class = $this->order;

            if ( strpos( WC_VERSION, '2.6.' ) === 0 ) {
                $this->class = new Version_2_6\WC_Order( $this->order );
            }
        }

        return call_user_func_array( [ $this->class, $name ], $arguments );
    }

    /**
     * Get the core order object.
     * 
     * This will normally be an instance of 'WC_Order'.
     * 
     * @return object The object.
     */
    public function get_core_order() {
        return $this->order;
    }

}