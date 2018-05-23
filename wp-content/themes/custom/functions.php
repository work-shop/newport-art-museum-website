<?php

/** Theme-specific global constants for NAM */
define( '__ROOT__', dirname( __FILE__ ) );

require_once( __ROOT__ . '/functions/class-nam-abstract-taxonomy.php' );
require_once( __ROOT__ . '/functions/class-nam-abstract-custom-post-type.php' );

require_once( __ROOT__ . '/functions/library/class-ws-cdn-url.php');

require_once( __ROOT__ . '/functions/taxonomies/groups/class-nam-group.php');
require_once( __ROOT__ . '/functions/taxonomies/classes-categories/class-nam-classes-category.php');
require_once( __ROOT__ . '/functions/taxonomies/events-categories/class-nam-events-category.php');
require_once( __ROOT__ . '/functions/taxonomies/news-categories/class-nam-news-category.php');
require_once( __ROOT__ . '/functions/taxonomies/exhibitions-categories/class-nam-exhibitions-category.php');

require_once( __ROOT__ . '/functions/post-types/shop-products/class-nam-shop-product.php');
require_once( __ROOT__ . '/functions/post-types/donation-tiers/class-nam-donation-tier.php');
require_once( __ROOT__ . '/functions/post-types/membership-tiers/class-nam-membership-tier.php');
require_once( __ROOT__ . '/functions/post-types/exhibitions/class-nam-exhibition.php');
require_once( __ROOT__ . '/functions/post-types/classes/class-nam-class.php');
require_once( __ROOT__ . '/functions/post-types/events/class-nam-event.php');
require_once( __ROOT__ . '/functions/post-types/news/class-nam-news.php');

require_once( __ROOT__ . '/functions/class-nam-site-admin.php' );
require_once( __ROOT__ . '/functions/class-nam-site-init.php' );

require_once( __ROOT__ . '/functions/library/class-nam-helpers.php' );
require_once( __ROOT__ . '/functions/library/class-ws-flexible-content.php' );

new NAM_Site();
new NAM_Site_Admin();











add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

?>
