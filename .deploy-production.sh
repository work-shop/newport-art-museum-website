#!/bin/bash

source ./.env

# just functions.php
scp -P $KINSTA_PRODUCTION_PORT -r ./wp-content/themes/custom/functions.php $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content/themes/custom

# Custom theme
#scp -P $KINSTA_PRODUCTION_PORT -r ./wp-content/themes/custom $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content/themes

#scp -P $KINSTA_PRODUCTION_PORT -r ./wp-content/plugins/wc-product-customer-list-premium $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content/plugins

#scp -P $KINSTA_PRODUCTION_PORT -r ./wp-content/themes/custom/woocommerce $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content/themes/custom

#scp -P $KINSTA_PRODUCTION_PORT ./wp-content/themes/custom/functions/post-types/classes/class-nam-class.php $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content/themes/custom/functions/post-types/classes/

#scp -P $KINSTA_PRODUCTION_PORT ./wp-content/themes/custom/functions/class-nam-site-admin.php $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content/themes/custom/functions/

#scp -P $KINSTA_PRODUCTION_PORT -r ./wp-content/plugins $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content

curl -L https://newportartmuseum.org/kinsta-clear-cache-all/ 