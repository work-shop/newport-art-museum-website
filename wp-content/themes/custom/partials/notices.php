<?php

/**
 * This file hooks woocommerce's internal notice printing mechanism
 * Whenever an action happens on the site, a message might be generated
 * by wocommerce. This function echos those that are relevant to the current
 * session, and clears them from the log so that they don't accumulate.
 *
 * You can modify the html associated with these messages in the `woocommerce/notices` director.
 */

wc_print_notices();

?>
