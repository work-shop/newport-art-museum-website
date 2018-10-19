<?php

namespace Woo_MP;

defined( 'ABSPATH' ) || die;

/**
 * Exception allowing for additional details and a non-integer error code.
 */
class Detailed_Exception extends \Exception {

    /**
     * Arbitrary additional data associated with the exception.
     * 
     * @var array
     */
    private $data = [];

    /**
     * Construct the exception.
     * 
     * @param string $message  The exception message.
     * @param mixed  $code     The exception code. There is no type restriction.
     * @param array  $data     Arbitrary additional data associated with the exception.
     * @param mixed  $previous The previous exception.
     */
    public function __construct( $message = '', $code = 0, $data = [], $previous = null ) {
        parent::__construct( $message, 0, $previous );

        // Bypass the integer restriction.
        $this->code = $code;

        $this->data = $data;
    }

    /**
     * Get any additional data associated with the exception.
     * 
     * @return array The data.
     */
    public function get_data() {
        return $this->data;
    }

}