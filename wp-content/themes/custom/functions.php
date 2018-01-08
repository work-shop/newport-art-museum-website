<?php

    /** Theme-specific global constants for NAM */
    define( '__ROOT__', dirname( __FILE__ ) );

    require_once( __ROOT__ . '/functions/library/class-ws-cdn-url.php');

    require_once( __ROOT__ . '/functions/groups/class-nam-group.php');
    require_once( __ROOT__ . '/functions/shop-products/class-nam-shop-product.php');
    require_once( __ROOT__ . '/functions/donation-tiers/class-nam-donation-tier.php');
    require_once( __ROOT__ . '/functions/membership-tiers/class-nam-membership-tier.php');
    require_once( __ROOT__ . '/functions/exhibitions/class-nam-exhibition.php');
    require_once( __ROOT__ . '/functions/classes/class-nam-class.php');
    require_once( __ROOT__ . '/functions/events/class-nam-event.php');

    require_once( __ROOT__ . '/functions/class-nam-site-admin.php' );
    require_once( __ROOT__ . '/functions/class-nam-site-init.php' );


    new NAM_Site();
    new NAM_Site_Admin();

?>
