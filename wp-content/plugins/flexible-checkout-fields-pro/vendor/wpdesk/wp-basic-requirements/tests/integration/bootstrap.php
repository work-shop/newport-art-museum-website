<?php
// disable xdebug backtrace
if ( function_exists( 'xdebug_disable' ) ) {
	xdebug_disable();
}

if ( getenv( 'PLUGIN_PATH' ) !== false ) {
	define( 'PLUGIN_PATH', getenv( 'PLUGIN_PATH' ) );
} else {
	define( 'PLUGIN_PATH', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR );
}

require_once( getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit/includes/functions.php' );

putenv('WP_TESTS_DIR=' . getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit');
require_once( getenv( 'WC_DEVELOP_DIR' ) . '/tests/bootstrap.php' );
