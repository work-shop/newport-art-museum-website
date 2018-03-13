<?php
/**
 * Class file for the Object_Sync_Sf_Salesforce class.
 *
 * @file
 */

if ( ! class_exists( 'Object_Sync_Salesforce' ) ) {
	die();
}

/**
 * Ability to authorize and communicate with the Salesforce REST API. This class can make read and write calls to Salesforce, and also cache the responses in WordPress.
 */
class Object_Sync_Sf_Salesforce {

	public $response;

	/**
	* Constructor which initializes the Salesforce APIs.
	*
	* @param string $consumer_key
	*   Salesforce key to connect to your Salesforce instance.
	* @param string $consumer_secret
	*   Salesforce secret to connect to your Salesforce instance.
	* @param string $login_url
	*   Login URL for Salesforce auth requests - differs for production and sandbox
	* @param string $callback_url
	*   WordPress URL where Salesforce should send you after authentication
	* @param string $authorize_path
	*   Oauth path that Salesforce wants
	* @param string $token_path
	*   Path Salesforce uses to give you a token
	* @param string $rest_api_version
	*   What version of the Salesforce REST API to use
	* @param object $wordpress
	*   Object for doing things to WordPress - retrieving data, cache, etc.
	* @param string $slug
	*   Slug for this plugin. Can be used for file including, especially
	* @param object $logging
	*   Logging object for this plugin.
	* @param array $schedulable_classes
	*   array of classes that can have scheduled tasks specific to them
	*/
	public function __construct( $consumer_key, $consumer_secret, $login_url, $callback_url, $authorize_path, $token_path, $rest_api_version, $wordpress, $slug, $logging, $schedulable_classes ) {
		$this->consumer_key        = $consumer_key;
		$this->consumer_secret     = $consumer_secret;
		$this->login_url           = $login_url;
		$this->callback_url        = $callback_url;
		$this->authorize_path      = $authorize_path;
		$this->token_path          = $token_path;
		$this->rest_api_version    = $rest_api_version;
		$this->wordpress           = $wordpress;
		$this->slug                = $slug;
		$this->logging             = $logging;
		$this->schedulable_classes = $schedulable_classes;
		$this->options             = array(
			'cache'            => true,
			'cache_expiration' => $this->cache_expiration(),
			'type'             => 'read',
		);

		$this->success_codes              = array( 200, 201, 204 );
		$this->refresh_code               = 401;
		$this->success_or_refresh_codes   = $this->success_codes;
		$this->success_or_refresh_codes[] = $this->refresh_code;

		$this->debug = get_option( 'object_sync_for_salesforce_debug_mode', false );

	}

	/**
	* Converts a 15-character case-sensitive Salesforce ID to 18-character
	* case-insensitive ID. If input is not 15-characters, return input unaltered.
	*
	* @param string $sf_id_15
	*   15-character case-sensitive Salesforce ID
	* @return string
	*   18-character case-insensitive Salesforce ID
	*/
	public static function convert_id( $sf_id_15 ) {
		if ( strlen( $sf_id_15 ) !== 15 ) {
			return $sf_id_15;
		}
		$chunks = str_split( $sf_id_15, 5 );
		$extra  = '';
		foreach ( $chunks as $chunk ) {
			$chars = str_split( $chunk, 1 );
			$bits  = '';
			foreach ( $chars as $char ) {
				$bits .= ( ! is_numeric( $char ) && strtoupper( $char ) === $char ) ? '1' : '0';
			}
			$map    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ012345';
			$extra .= substr( $map, base_convert( strrev( $bits ), 2, 10 ), 1 );
		}
		return $sf_id_15 . $extra;
	}

	/**
	* Given a Salesforce ID, return the corresponding SObject name. (Based on
	*  keyPrefix from object definition, @see
	*  https://developer.salesforce.com/forums/?id=906F0000000901ZIAQ )
	*
	* @param string $sf_id
	*   15- or 18-character Salesforce ID
	* @return string
	*   sObject name, e.g. "Account", "Contact", "my__Custom_Object__c" or FALSE
	*   if no match could be found.
	* @throws Object_Sync_Sf_Exception
	*/
	public function get_sobject_type( $sf_id ) {
		$objects = $this->objects(
			array(
				'keyPrefix' => substr( $sf_id, 0, 3 ),
			)
		);
		if ( 1 === count( $objects ) ) {
			// keyPrefix is unique across objects. If there is exactly one return value from objects(), then we have a match.
			$object = reset( $objects );
			return $object['name'];
		}
		// Otherwise, we did not find a match.
		return false;
	}

	/**
	* Determine if this SF instance is fully configured.
	*
	*/
	public function is_authorized() {
		return ! empty( $this->consumer_key ) && ! empty( $this->consumer_secret ) && $this->get_refresh_token();
	}

	/**
	* Get REST API versions available on this Salesforce organization
	* This is not an authenticated call, so it would not be a helpful test
	*/
	public function get_api_versions() {
		$options = array(
			'authenticated' => false,
			'full_url'      => true,
		);
		return $this->api_call( $this->get_instance_url() . '/services/data', [], 'GET', $options );
	}

	/**
	* Make a call to the Salesforce REST API.
	*
	* @param string $path
	*   Path to resource.
	* @param array $params
	*   Parameters to provide.
	* @param string $method
	*   Method to initiate the call, such as GET or POST. Defaults to GET.
	* @param array $options
	*   Any method can supply options for the API call, and they'll be preserved as far as the curl request
	*   They get merged with the class options
	* @param string $type
	*   Type of call. Defaults to 'rest' - currently we don't support other types.
	*   Other exammple in Drupal is 'apexrest'
	*
	* @return mixed
	*   The requested response.
	*
	* @throws Object_Sync_Sf_Exception
	*/
	public function api_call( $path, $params = array(), $method = 'GET', $options = array(), $type = 'rest' ) {
		if ( ! $this->get_access_token() ) {
			$this->refresh_token();
		}
		$this->response = $this->api_http_request( $path, $params, $method, $options, $type );

		// analytic calls that are expired return 404s for some absurd reason
		if ( $this->response['code'] && 'run_analytics_report' === debug_backtrace()[1]['function'] ) {
			return $this->response;
		}

		switch ( $this->response['code'] ) {
			// The session ID or OAuth token used has expired or is invalid.
			case $this->response['code'] === $this->refresh_code:
				// Refresh token.
				$this->refresh_token();
				// Rebuild our request and repeat request.
				$options['is_redo'] = true;
				$this->response     = $this->api_http_request( $path, $params, $method, $options, $type );
				// Throw an error if we still have bad response.
				if ( ! in_array( $this->response['code'], $this->success_codes, true ) ) {
					throw new Object_Sync_Sf_Exception( $this->response['data'][0]['message'], $this->response['code'] );
				}
				break;
			case in_array( $this->response['code'], $this->success_codes, true ):
				// All clear.
				break;
			default:
				// We have problem and no specific Salesforce error provided.
				if ( empty( $this->response['data'] ) ) {
					throw new Object_Sync_Sf_Exception( $this->response['error'], $this->response['code'] );
				}
		}

		if ( ! empty( $this->response['data'][0] ) && 1 === count( $this->response['data'] ) ) {
			$this->response['data'] = $this->response['data'][0];
		}

		if ( isset( $this->response['data']['error'] ) ) {
			throw new Object_Sync_Sf_Exception( $this->response['data']['error_description'], $this->response['data']['error'] );
		}

		if ( ! empty( $this->response['data']['errorCode'] ) ) {
			throw new Object_Sync_Sf_Exception( $this->response['data']['message'], $this->response['code'] );
		}

		return $this->response;
	}

	/**
	* Private helper to issue an SF API request.
	* This method is the only place where we read to or write from the cache
	*
	* @param string $path
	*   Path to resource.
	* @param array $params
	*   Parameters to provide.
	* @param string $method
	*   Method to initiate the call, such as GET or POST.  Defaults to GET.
	* @param array $options
	*   This is the options array from the api_call method
	*   This is where it gets merged with $this->options
	* @param string $type
	*   Type of call. Defaults to 'rest' - currently we don't support other types
	*   Other exammple in Drupal is 'apexrest'
	*
	* @return array
	*   The requested data.
	*/
	protected function api_http_request( $path, $params, $method, $options = array(), $type = 'rest' ) {
		$options = array_merge( $this->options, $options ); // this will override a value in $this->options with the one in $options if there is a matching key
		$url     = $this->get_api_endpoint( $type ) . $path;
		if ( isset( $options['full_url'] ) && true === $options['full_url'] ) {
			$url = $path;
		}
		$headers = array(
			'Authorization'   => 'Authorization: OAuth ' . $this->get_access_token(),
			'Accept-Encoding' => 'Accept-Encoding: gzip, deflate',
		);
		if ( 'POST' === $method || 'PATCH' === $method ) {
			$headers['Content-Type'] = 'Content-Type: application/json';
		}
		if ( isset( $options['authenticated'] ) && true === $options['authenticated'] ) {
			$headers = false;
		}
		// if this request should be cached, see if it already exists
		// if it is already cached, load it. if not, load it and then cache it if it should be cached
		// add parameters to the array so we can tell if it was cached or not
		if ( true === $options['cache'] && 'write' !== $options['type'] ) {
			$cached = $this->wordpress->cache_get( $url, $params );
			// some api calls can send a reset option, in which case we should redo the request anyway
			if ( is_array( $cached ) && ( ! isset( $options['reset'] ) || true !== $options['reset'] ) ) {
				$result               = $cached;
				$result['from_cache'] = true;
				$result['cached']     = true;
			} else {
				$data   = wp_json_encode( $params );
				$result = $this->http_request( $url, $data, $headers, $method, $options );
				if ( in_array( $result['code'], $this->success_codes, true ) ) {
					$result['cached'] = $this->wordpress->cache_set( $url, $params, $result, $options['cache_expiration'] );
				} else {
					$result['cached'] = false;
				}
				$result['from_cache'] = false;
			}
		} else {
			$data                 = wp_json_encode( $params );
			$result               = $this->http_request( $url, $data, $headers, $method, $options );
			$result['from_cache'] = false;
			$result['cached']     = false;
		}

		if ( isset( $options['is_redo'] ) && true === $options['is_redo'] ) {
			$result['is_redo'] = true;
		} else {
			$result['is_redo'] = false;
		}

		// it would be very unfortunate to ever have to do this in a production site
		if ( 1 === (int) $this->debug ) {
			// create log entry for the api call if debug is true
			$status = 'debug';
			if ( isset( $this->logging ) ) {
				$logging = $this->logging;
			} elseif ( class_exists( 'Object_Sync_Sf_Logging' ) ) {
				$logging = new Object_Sync_Sf_Logging( $this->wpdb, $this->version );
			}

			// translators: placeholder is the URL of the Salesforce API request
			$title = sprintf( esc_html__( 'Debug: on Salesforce API HTTP Request to URL: %1$s.', 'object-sync-for-salesforce' ),
				esc_url( $url )
			);

			$logging->setup(
				$title,
				print_r( $result, true ), // log the result because we are debugging the whole api call
				0,
				0,
				$status
			);
		}

		return $result;
	}

	/**
	* Make the HTTP request. Wrapper around curl().
	*
	* @param string $url
	*   Path to make request from.
	* @param array $data
	*   The request body.
	* @param array $headers
	*   Request headers to send as name => value.
	* @param string $method
	*   Method to initiate the call, such as GET or POST. Defaults to GET.
	* @param array $options
	*   This is the options array from the api_http_request method
	*
	* @return array
	*   Salesforce response object.
	*/
	protected function http_request( $url, $data, $headers = array(), $method = 'GET', $options = array() ) {
		// Build the request, including path and headers. Internal use.

		/*
		 * Note: curl is used because wp_remote_get, wp_remote_post, wp_remote_request don't work. Salesforce returns various errors.
		 * There is a GitHub branch attempting with the goal of addressing this in a future version: https://github.com/MinnPost/object-sync-for-salesforce/issues/94
		*/

		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
		if ( false !== $headers ) {
			curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
		} else {
			curl_setopt( $curl, CURLOPT_HEADER, false );
		}

		if ( 'POST' === $method ) {
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
		} elseif ( 'PATCH' === $method || 'DELETE' === $method ) {
			curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
		}
		$json_response = curl_exec( $curl ); // this is possibly gzipped json data
		$code          = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

		if ( ( 'PATCH' === $method || 'DELETE' === $method ) && '' === $json_response && 204 === $code ) {
			// delete and patch requests return a 204 with an empty body upon success for whatever reason
			$data = array(
				'success' => true,
				'body'    => '',
			);
			curl_close( $curl );
			return array(
				'json' => wp_json_encode( $data ),
				'code' => $code,
				'data' => $data,
			);
		}

		if ( ( ord( $json_response[0] ) == 0x1f ) && ( ord( $json_response[1] ) == 0x8b ) ) {
			// skip header and ungzip the data
			$json_response = gzinflate( substr( $json_response, 10 ) );
		}
		$data = json_decode( $json_response, true ); // decode it into an array

		// don't use the exception if the status is a success one, or if it just needs a refresh token (salesforce uses 401 for this)
		if ( ! in_array( $code, $this->success_or_refresh_codes, true ) ) {
			$curl_error = curl_error( $curl );
			if ( '' !== $curl_error ) {
				// create log entry for failed curl
				$status = 'error';
				if ( isset( $this->logging ) ) {
					$logging = $this->logging;
				} elseif ( class_exists( 'Object_Sync_Sf_Logging' ) ) {
					$logging = new Object_Sync_Sf_Logging( $this->wpdb, $this->version );
				}

				// translators: placeholder is the URL of the Salesforce API request
				$title = sprintf( esc_html__( 'Error: %1$s: on Salesforce http request', 'object-sync-for-salesforce' ),
					esc_attr( $code )
				);

				$logging->setup(
					$title,
					$curl_error,
					0,
					0,
					$status
				);
			} elseif ( isset( $data[0]['errorCode'] ) && '' !== $data[0]['errorCode'] ) { // salesforce uses this structure to return errors
				// create log entry for failed curl
				$status = 'error';
				if ( isset( $this->logging ) ) {
					$logging = $this->logging;
				} elseif ( class_exists( 'Object_Sync_Sf_Logging' ) ) {
					$logging = new Object_Sync_Sf_Logging( $this->wpdb, $this->version );
				}

				// translators: placeholder is the server code returned by the api
				$title = sprintf( esc_html__( 'Error: %1$s: on Salesforce http request', 'object-sync-for-salesforce' ),
					absint( $code )
				);

				// translators: placeholders are: 1) the URL requested, 2) the message returned by the error, 3) the server code returned
				$body = sprintf( '<p>' . esc_html__( 'URL: %1$s', 'object-sync-for-salesforce' ) . '</p><p>' . esc_html__( 'Message: %2$s', 'object-sync-for-salesforce' ) . '</p><p>' . esc_html__( 'Code: %3$s', 'object-sync-for-salesforce' ),
					esc_attr( $url ),
					esc_html( $data[0]['message'] ),
					absint( $code )
				);

				$logging->setup(
					$title,
					$body,
					0,
					0,
					$status
				);
			} else {
				// create log entry for failed curl
				$status = 'error';
				if ( isset( $this->logging ) ) {
					$logging = $this->logging;
				} elseif ( class_exists( 'Object_Sync_Sf_Logging' ) ) {
					$logging = new Object_Sync_Sf_Logging( $this->wpdb, $this->version );
				}

				// translators: placeholder is the server code returned by Salesforce
				$title = sprintf( esc_html__( 'Error: %1$s: on Salesforce http request', 'object-sync-for-salesforce' ),
					absint( $code )
				);

				$logging->setup(
					$title,
					print_r( $data, true ), // log the result because we are debugging the whole api call
					0,
					0,
					$status
				);
			} // End if().
		} // End if().

		curl_close( $curl );

		return array(
			'json' => $json_response,
			'code' => $code,
			'data' => $data,
		);
	}

	/**
	* Get the API end point for a given type of the API.
	*
	* @param string $api_type
	*   E.g., rest, partner, enterprise.
	*
	* @return string
	*   Complete URL endpoint for API access.
	*/
	public function get_api_endpoint( $api_type = 'rest' ) {
		// Special handling for apexrest, since it's not in the identity object.
		if ( 'apexrest' === $api_type ) {
			$url = $this->get_instance_url() . '/services/apexrest/';
		} else {
			$identity = $this->get_identity();
			$url      = str_replace( '{version}', $this->rest_api_version, $identity['urls'][ $api_type ] );
			if ( '' === $identity ) {
				$url = $this->get_instance_url() . '/services/data/v' . $this->rest_api_version . '/';
			}
		}
		return $url;
	}

	/**
	* Get the SF instance URL. Useful for linking to objects.
	*/
	public function get_instance_url() {
		return get_option( 'object_sync_for_salesforce_instance_url', '' );
	}

	/**
	* Set the SF instanc URL.
	*
	* @param string $url
	*   URL to set.
	*/
	protected function set_instance_url( $url ) {
		update_option( 'object_sync_for_salesforce_instance_url', $url );
	}

	/**
	* Get the access token.
	*/
	public function get_access_token() {
		return get_option( 'object_sync_for_salesforce_access_token', '' );
	}

	/**
	* Set the access token.
	*
	* It is stored in session.
	*
	* @param string $token
	*   Access token from Salesforce.
	*/
	protected function set_access_token( $token ) {
		update_option( 'object_sync_for_salesforce_access_token', $token );
	}

	/**
	* Get refresh token.
	*/
	protected function get_refresh_token() {
		return get_option( 'object_sync_for_salesforce_refresh_token', '' );
	}

	/**
	* Set refresh token.
	*
	* @param string $token
	*   Refresh token from Salesforce.
	*/
	protected function set_refresh_token( $token ) {
		update_option( 'object_sync_for_salesforce_refresh_token', $token );
	}

	/**
	* Refresh access token based on the refresh token. Updates session variable.
	*
	* todo: figure out how to do this as part of the schedule class
	* this is a scheduleable class and so we could add a method from this class to run every 24 hours, but it's unclear to me that we need it. salesforce seems to refresh itself as it needs to.
	* but it could be a performance boost to do it at scheduleable intervals instead.
	*
	* @throws Object_Sync_Sf_Exception
	*/
	protected function refresh_token() {
		$refresh_token = $this->get_refresh_token();
		if ( empty( $refresh_token ) ) {
			throw new Object_Sync_Sf_Exception( esc_html__( 'There is no refresh token.', 'object-sync-for-salesforce' ) );
		}

		$data = array(
			'grant_type'    => 'refresh_token',
			'refresh_token' => $refresh_token,
			'client_id'     => $this->consumer_key,
			'client_secret' => $this->consumer_secret,
		);

		$url      = $this->login_url . $this->token_path;
		$headers  = array(
			// This is an undocumented requirement on Salesforce's end.
			'Content-Type'    => 'Content-Type: application/x-www-form-urlencoded',
			'Accept-Encoding' => 'Accept-Encoding: gzip, deflate',
			'Authorization'   => 'Authorization: OAuth ' . $this->get_access_token(),
		);
		$headers  = false;
		$response = $this->http_request( $url, $data, $headers, 'POST' );

		if ( 200 !== $response['code'] ) {
			throw new Object_Sync_Sf_Exception(
				esc_html(
					sprintf(
						__( 'Unable to get a Salesforce access token. Salesforce returned the following errorCode: ', 'object-sync-for-salesforce' ) . $response['code']
					)
				),
				$response['code']
			);
		}

		$data = $response['data'];

		if ( is_array( $data ) && isset( $data['error'] ) ) {
			throw new Object_Sync_Sf_Exception( $data['error_description'], $data['error'] );
		}

		$this->set_access_token( $data['access_token'] );
		$this->set_identity( $data['id'] );
		$this->set_instance_url( $data['instance_url'] );
	}

	/**
	* Retrieve and store the Salesforce identity given an ID url.
	*
	* @param string $id
	*   Identity URL.
	*
	* @throws Object_Sync_Sf_Exception
	*/
	protected function set_identity( $id ) {
		$headers  = array(
			'Authorization'   => 'Authorization: OAuth ' . $this->get_access_token(),
			//'Content-type'  => 'application/json',
			'Accept-Encoding' => 'Accept-Encoding: gzip, deflate',
		);
		$response = $this->http_request( $id, null, $headers );
		if ( 200 !== $response['code'] ) {
			throw new Object_Sync_Sf_Exception( esc_html__( 'Unable to access identity service.', 'object-sync-for-salesforce' ), $response['code'] );
		}
		$data = $response['data'];
		update_option( 'object_sync_for_salesforce_identity', $data );
	}

	/**
	* Return the Salesforce identity, which is stored in a variable.
	*
	* @return array
	*   Returns FALSE if no identity has been stored.
	*/
	public function get_identity() {
		return get_option( 'object_sync_for_salesforce_identity', false );
	}

	/**
	* OAuth step 1: Redirect to Salesforce and request and authorization code.
	*/
	public function get_authorization_code() {
		$url = add_query_arg(
			array(
				'response_type' => 'code',
				'client_id'     => $this->consumer_key,
				'redirect_uri'  => $this->callback_url,
			),
			$this->login_url . $this->authorize_path
		);
		return $url;
	}

	/**
	* OAuth step 2: Exchange an authorization code for an access token.
	*
	* @param string $code
	*   Code from Salesforce.
	*/
	public function request_token( $code ) {
		$data = array(
			'code'          => $code,
			'grant_type'    => 'authorization_code',
			'client_id'     => $this->consumer_key,
			'client_secret' => $this->consumer_secret,
			'redirect_uri'  => $this->callback_url,
		);

		$url      = $this->login_url . $this->token_path;
		$headers  = array(
			// This is an undocumented requirement on SF's end.
			//'Content-Type'  => 'application/x-www-form-urlencoded',
			'Accept-Encoding' => 'Accept-Encoding: gzip, deflate',
		);
		$response = $this->http_request( $url, $data, $headers, 'POST' );

		$data = $response['data'];

		if ( 200 !== $response['code'] ) {
			$error = isset( $data['error_description'] ) ? $data['error_description'] : $response['error'];
			throw new Object_Sync_Sf_Exception( $error, $response['code'] );
		}

		// Ensure all required attributes are returned. They can be omitted if the
		// OAUTH scope is inadequate.
		$required = array( 'refresh_token', 'access_token', 'id', 'instance_url' );
		foreach ( $required as $key ) {
			if ( ! isset( $data[ $key ] ) ) {
				return false;
			}
		}

		$this->set_refresh_token( $data['refresh_token'] );
		$this->set_access_token( $data['access_token'] );
		$this->set_identity( $data['id'] );
		$this->set_instance_url( $data['instance_url'] );

		return true;
	}

	/* Core API calls */

	/**
	* Available objects and their metadata for your organization's data.
	*
	* @param array $conditions
	*   Associative array of filters to apply to the returned objects. Filters
	*   are applied after the list is returned from Salesforce.
	* @param bool $reset
	*   Whether to reset the cache and retrieve a fresh version from Salesforce.
	*
	* @return array
	*   Available objects and metadata.
	*
	* part of core API calls. this call does require authentication, and the basic url it becomes is like this:
	* https://instance.salesforce.com/services/data/v#.0/sobjects
	*
	* updateable is really how the api spells it
	*/
	public function objects(
		$conditions = array(
			'updateable'  => true,
			'triggerable' => true,
		),
		$reset = false
	) {

		$options = array(
			'reset' => $reset,
		);
		$result  = $this->api_call( 'sobjects', array(), 'GET', $options );

		if ( ! empty( $conditions ) ) {
			foreach ( $result['data']['sobjects'] as $key => $object ) {
				foreach ( $conditions as $condition => $value ) {
					if ( $object[ $condition ] !== $value ) {
						unset( $result['data']['sobjects'][ $key ] );
					}
				}
			}
		}

		ksort( $result['data']['sobjects'] );

		return $result['data']['sobjects'];
	}

	/**
	* Use SOQL to get objects based on query string.
	*
	* @param string $query
	*   The SOQL query.
	* @param array $options
	*   Allow for the query to have options based on what the user needs from it, ie caching, read/write, etc.
	* @param boolean $all
	*   Whether this should get all results for the query
	* @param boolean $explain
	*   If set, Salesforce will return feedback on the query performance
	*
	* @return array
	*   Array of Salesforce objects that match the query.
	*
	* part of core API calls
	*/
	public function query( $query, $options = array(), $all = false, $explain = false ) {
		$search_data = [
			'q' => (string) $query,
		];
		if ( true === $explain ) {
			$search_data['explain'] = $search_data['q'];
			unset( $search_data['q'] );
		}
		// all is a search through deleted and merged data as well
		if ( true === $all ) {
			$path = 'queryAll';
		} else {
			$path = 'query';
		}
		$result = $this->api_call( $path . '?' . http_build_query( $search_data ), array(), 'GET', $options );
		return $result;
	}

	/**
	* Retrieve all the metadata for an object.
	*
	* @param string $name
	*   Object type name, E.g., Contact, Account, etc.
	* @param bool $reset
	*   Whether to reset the cache and retrieve a fresh version from Salesforce.
	*
	* @return array
	*   All the metadata for an object, including information about each field,
	*   URLs, and child relationships.
	*
	* part of core API calls
	*/
	public function object_describe( $name, $reset = false ) {
		if ( empty( $name ) ) {
			return array();
		}
		$options = array(
			'reset' => $reset,
		);
		$object  = $this->api_call( "sobjects/{$name}/describe", array(), 'GET', $options );
		// Sort field properties, because salesforce API always provides them in a
		// random order. We sort them so that stored and exported data are
		// standardized and predictable.
		$fields = array();
		foreach ( $object['data']['fields'] as &$field ) {
			ksort( $field );
			if ( ! empty( $field['picklistValues'] ) ) {
				foreach ( $field['picklistValues'] as &$picklist_value ) {
					ksort( $picklist_value );
				}
			}
			$fields[ $field['name'] ] = $field;
		}
		ksort( $fields );
		$object['fields'] = $fields;
		return $object;
	}

	/**
	* Create a new object of the given type.
	*
	* @param string $name
	*   Object type name, E.g., Contact, Account, etc.
	* @param array $params
	*   Values of the fields to set for the object.
	*
	* @return array
	*   json: {"id":"00190000001pPvHAAU","success":true,"errors":[]}
	*   code: 201
	*   data:
	*     "id" : "00190000001pPvHAAU",
	*     "success" : true
	*     "errors" : [ ],
	*   from_cache:
	*   cached:
	*   is_redo:
	*
	* part of core API calls
	*/
	public function object_create( $name, $params ) {
		$options = array(
			'type' => 'write',
		);
		$result  = $this->api_call( "sobjects/{$name}", $params, 'POST', $options );
		return $result;
	}

	/**
	* Create new records or update existing records.
	*
	* The new records or updated records are based on the value of the specified
	* field.  If the value is not unique, REST API returns a 300 response with
	* the list of matching records.
	*
	* @param string $name
	*   Object type name, E.g., Contact, Account.
	* @param string $key
	*   The field to check if this record should be created or updated.
	* @param string $value
	*   The value for this record of the field specified for $key.
	* @param array $params
	*   Values of the fields to set for the object.
	*
	* @return array
	*   json: {"id":"00190000001pPvHAAU","success":true,"errors":[]}
	*   code: 201
	*   data:
	*     "id" : "00190000001pPvHAAU",
	*     "success" : true
	*     "errors" : [ ],
	*   from_cache:
	*   cached:
	*   is_redo:
	*
	* part of core API calls
	*/
	public function object_upsert( $name, $key, $value, $params ) {
		$options = array(
			'type' => 'write',
		);
		// If key is set, remove from $params to avoid UPSERT errors.
		if ( isset( $params[ $key ] ) ) {
			unset( $params[ $key ] );
		}

		// allow developers to change both the key and value by which objects should be matched
		$key   = apply_filters( 'object_sync_for_salesforce_modify_upsert_key', $key );
		$value = apply_filters( 'object_sync_for_salesforce_modify_upsert_value', $value );

		$data = $this->api_call( "sobjects/{$name}/{$key}/{$value}", $params, 'PATCH', $options );
		if ( 300 === $this->response['code'] ) {
			$data['message'] = esc_html( 'The value provided is not unique.' );
		}
		return $data;
	}

	/**
	* Update an existing object.
	*
	* @param string $name
	*   Object type name, E.g., Contact, Account.
	* @param string $id
	*   Salesforce id of the object.
	* @param array $params
	*   Values of the fields to set for the object.
	*
	* part of core API calls
	*
	* @return array
	*   json: {"success":true,"body":""}
	*   code: 204
	*   data:
		success: 1
		body:
	*   from_cache:
	*   cached:
	*   is_redo:
	*/
	public function object_update( $name, $id, $params ) {
		$options = array(
			'type' => 'write',
		);
		$result  = $this->api_call( "sobjects/{$name}/{$id}", $params, 'PATCH', $options );
		return $result;
	}

	/**
	* Return a full loaded Salesforce object.
	*
	* @param string $name
	*   Object type name, E.g., Contact, Account.
	* @param string $id
	*   Salesforce id of the object.
	*
	* @return object
	*   Object of the requested Salesforce object.
	*
	* part of core API calls
	*/
	public function object_read( $name, $id ) {
		return $this->api_call( "sobjects/{$name}/{$id}", array(), 'GET' );
	}

	/**
	* Make a call to the Analytics API
	*
	* @param string $name
	*   Object type name, E.g., Report
	* @param string $id
	*   Salesforce id of the object.
	* @param string $route
	*   What comes after the ID? E.g. instances, ?includeDetails=True
	* @param array $params
	*   Params to put with the request
	* @param string $method
	*   GET or POST
	*
	* @return object
	*   Object of the requested Salesforce object.
	*
	* part of core API calls
	*/
	public function analytics_api( $name, $id, $route = '', $params = array(), $method = 'GET' ) {
		return $this->api_call( "analytics/{$name}/{$id}/{$route}", $params, $method );
	}

	/**
	* Run a specific Analytics report
	*
	* @param string $id
	*   Salesforce id of the object.
	* @param bool $async
	*   Whether the report is asynchronous
	* @param array $params
	*   Params to put with the request
	* @param string $method
	*   GET or POST
	*
	* @return object
	*   Object of the requested Salesforce object.
	*
	* part of core API calls
	*/
	public function run_analytics_report( $id, $async = true, $clear_cache = false, $params = array(), $method = 'GET', $report_cache_expiration = '', $cache_instance = true, $instance_cache_expiration = '' ) {

		$id         = $this->convert_id( $id );
		$report_url = 'analytics/reports/' . $id . '/' . 'instances';

		if ( true === $clear_cache ) {
			delete_transient( $report_url );
		}

		$instance_id = $this->wordpress->cache_get( $report_url, '' );

		// there is no stored instance id or this is synchronous; retrieve the results for that instance
		if ( false === $async || false === $instance_id ) {

			$result = $this->analytics_api(
				'reports',
				$id,
				'?includeDetails=true',
				array(),
				'GET'
			);
			// if we get a reportmetadata array out of this, continue
			if ( is_array( $result['data']['reportMetadata'] ) ) {
				$params = array(
					'reportMetadata' => $result['data']['reportMetadata'],
				);
				$report = $this->analytics_api(
					'reports',
					$id,
					'instances',
					$params,
					'POST'
				);
				// if we get an id from the post, that is the instance id
				if ( isset( $report['data']['id'] ) ) {
					$instance_id = $report['data']['id'];
				} else {
					// run the call again if we don't have an instance id
					//error_log('run report again. we have no instance id.');
					$this->run_analytics_report( $id, true );
				}

				// cache the instance id so we can get the report results if they are applicable
				if ( '' === $report_cache_expiration ) {
					$report_cache_expiration = $this->cache_expiration();
				}
				$this->wordpress->cache_set( $report_url, '', $instance_id, $report_cache_expiration );
			} else {
				// run the call again if we don't have a reportMetadata array
				//error_log('run report again. we have no reportmetadata.');
				$this->run_analytics_report( $id, true );
			}
		} // End if().

		$result = $this->api_call( $report_url . "/{$instance_id}", array(), $method );

		// the report instance is expired. rerun it.
		if ( 404 === $result['code'] ) {
			//error_log('run report again. it expired.');
			$this->run_analytics_report( $id, true, true );
		}

		// cache the instance results as a long fallback if the setting says so
		// do this because salesforce will have errors if the instance has expired or is currently running
		// remember: the result of the above api_call is already cached (or not) according to the plugin's generic settings
		// this is fine I think, although it is a bit of redundancy in this case
		if ( true === $cache_instance ) {
			$cached = $this->wordpress->cache_get( $report_url . '_instance_cached', '' );
			if ( is_array( $cached ) ) {
				$result = $cached;
			} else {
				if ( 'Success' === $result['data']['attributes']['status'] ) {
					if ( '' === $instance_cache_expiration ) {
						$instance_cache_expiration = $this->cache_expiration();
					}
					$this->wordpress->cache_set( $report_url . '_instance_cached', '', $result, $instance_cache_expiration );
				}
			}
		}

		return $result;

	}

	/**
	* Return a full loaded Salesforce object from External ID.
	*
	* @param string $name
	*   Object type name, E.g., Contact, Account.
	* @param string $field
	*   Salesforce external id field name.
	* @param string $value
	*   Value of external id.
	*
	* @return object
	*   Object of the requested Salesforce object.
	*
	* part of core API calls
	*/
	public function object_readby_external_id( $name, $field, $value ) {
		return $this->api_call( "sobjects/{$name}/{$field}/{$value}" );
	}

	/**
	* Delete a Salesforce object.
	*
	* @param string $name
	*   Object type name, E.g., Contact, Account.
	* @param string $id
	*   Salesforce id of the object.
	*
	* @return array
	*
	* part of core API calls
	*/
	public function object_delete( $name, $id ) {
		$options = array(
			'type' => 'write',
		);
		$result  = $this->api_call( "sobjects/{$name}/{$id}", array(), 'DELETE', $options );
		return $result;
	}

	/**
	* Retrieves the list of individual objects that have been deleted within the
	* given timespan for a specified object type.
	*
	* @param string $type
	*   Object type name, E.g., Contact, Account.
	* @param string $startDate
	*   Start date to check for deleted objects (in ISO 8601 format).
	* @param string $endDate
	*   End date to check for deleted objects (in ISO 8601 format).
	* @return GetDeletedResult
	*/
	public function get_deleted( $type, $start_date, $end_date ) {
		$options = array(
			'cache' => false,
		); // this is timestamp level specific; probably should not cache it
		return $this->api_call( "sobjects/{$type}/deleted/?start={$start_date}&end={$end_date}", array(), 'GET', $options );
	}


	/**
	* Return a list of available resources for the configured API version.
	*
	* @return array
	*   Associative array keyed by name with a URI value.
	*
	* part of core API calls
	*/
	public function list_resources() {
		$resources = $this->api_call( '' );
		foreach ( $resources as $key => $path ) {
			$items[ $key ] = $path;
		}
		return $items;
	}

	/**
	* Return a list of SFIDs for the given object, which have been created or
	* updated in the given timeframe.
	*
	* @param string $type
	*   Object type name, E.g., Contact, Account.
	*
	* @param int $start
	*   unix timestamp for older timeframe for updates.
	*   Defaults to "-29 days" if empty.
	*
	* @param int $end
	*   unix timestamp for end of timeframe for updates.
	*   Defaults to now if empty
	*
	* @return array
	*   return array has 2 indexes:
	*     "ids": a list of SFIDs of those records which have been created or
	*       updated in the given timeframe.
	*     "latestDateCovered": ISO 8601 format timestamp (UTC) of the last date
	*       covered in the request.
	*
	* @see https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/resources_getupdated.htm
	*
	* part of core API calls
	*/
	public function get_updated( $type, $start = null, $end = null ) {
		if ( empty( $start ) ) {
			$start = strtotime( '-29 days' );
		}
		$start = rawurlencode( gmdate( DATE_ATOM, $start ) );

		if ( empty( $end ) ) {
			$end = time();
		}
		$end = rawurlencode( gmdate( DATE_ATOM, $end ) );

		$options = array(
			'cache' => false,
		); // this is timestamp level specific; probably should not cache it
		return $this->api_call( "sobjects/{$type}/updated/?start=$start&end=$end", array(), 'GET', $options );
	}

	/**
	* Given a DeveloperName and SObject Name, return the SFID of the
	* corresponding RecordType. DeveloperName doesn't change between Salesforce
	* environments, so it's safer to rely on compared to SFID.
	*
	* @param string $name
	*   Object type name, E.g., Contact, Account.
	*
	* @param string $devname
	*   RecordType DeveloperName, e.g. Donation, Membership, etc.
	*
	* @return string SFID
	*   The Salesforce ID of the given Record Type, or null.
	*/

	public function get_record_type_id_by_developer_name( $name, $devname, $reset = false ) {

		// example of how this runs: $this->get_record_type_id_by_developer_name( 'Account', 'HH_Account' );

		$cached = $this->wordpress->cache_get( 'salesforce_record_types', '' );
		if ( is_array( $cached ) && ( ! isset( $reset ) || true !== $reset ) ) {
			return ! empty( $cached[ $name ][ $devname ] ) ? $cached[ $name ][ $devname ]['Id'] : null;
		}

		$query         = new Object_Sync_Sf_Salesforce_Select_Query( 'RecordType' );
		$query->fields = array( 'Id', 'Name', 'DeveloperName', 'SobjectType' );

		$result       = $this->query( $query );
		$record_types = array();

		foreach ( $result['data']['records'] as $record_type ) {
			$record_types[ $record_type['SobjectType'] ][ $record_type['DeveloperName'] ] = $record_type;
		}

		$cached = $this->wordpress->cache_set( 'salesforce_record_types', '', $record_types, $this->options['cache_expiration'] );

		return ! empty( $record_types[ $name ][ $devname ] ) ? $record_types[ $name ][ $devname ]['Id'] : null;

	}

	/**
	* If there is a WordPress setting for how long to keep the cache, return it and set the object property
	* Otherwise, return seconds in 24 hours
	*
	*/
	private function cache_expiration() {
		$cache_expiration = $this->wordpress->cache_expiration( 'object_sync_for_salesforce_cache_expiration', 86400 );
		return $cache_expiration;
	}

}

class Object_Sync_Sf_Exception extends Exception {
}
