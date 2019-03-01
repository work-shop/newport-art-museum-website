#!/bin/bash

source ./.env

# just functions.php
#scp -P $KINSTA_PRODUCTION_PORT -r ./wp-content/themes/custom/functions.php $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content/themes/custom

# Custom theme
scp -P $KINSTA_PRODUCTION_PORT -r ./wp-content/themes/custom $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content/themes

# Plugins
#scp -P $KINSTA_PRODUCTION_PORT -r ./wp-content/plugins $KINSTA_PRODUCTION_USER@$KINSTA_PRODUCTION_IP:./public/wp-content

curl -L https://newportartmuseum.org/kinsta-clear-cache-all/ 