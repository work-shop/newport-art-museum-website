<?php

namespace Woo_MP\Payment_Gateways\Stripe;

defined( 'ABSPATH' ) || die;

/**
 * A custom replacement for the Stripe SDK's built in HTTP client.
 * The built in client uses curl. We don't want this dependency.
 */
class HTTP_Client implements \Stripe\HttpClient\ClientInterface {

    public function request( $method, $abs_url, $headers, $params, $has_file ) {
        $processed_headers = \WP_Http::processHeaders( $headers )['headers'];
        $query             = http_build_query( $params );

        $response = wp_remote_request( "$abs_url?$query", [
            'timeout' => 15,
            'method'  => strtoupper( $method ),
            'headers' => $processed_headers,
            'body'    => $params
        ] );

        if ( is_wp_error( $response ) ) {
            if ( isset( $response->errors['http_request_failed'] ) ) {
                $http_error = $response->errors['http_request_failed'][0]; 

                if ( strpos( $http_error, 'timed out' ) !== false ) {
                    throw new \Stripe\Error\ApiConnection( "Sorry, Stripe did not respond. This means we don't know whether the transaction was successful. Please check your Stripe account to confirm." );
                }

                throw new \Stripe\Error\ApiConnection( "Sorry, there was an error:<br>$http_error" );
            }

            throw new \Stripe\Error\ApiConnection( 'There was an unexpected error communicating with Stripe. The following is the response object: ' . print_r( $response, true ) );
        }

        return [
            $response['body'],
            $response['response']['code'],
            $response['headers']
        ];
    }

}