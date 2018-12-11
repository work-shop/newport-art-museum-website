#!/bin/bash

#npm run build

source ./.env

# Uploads
#scp -P $KINSTA_PORT -r ./uploads $KINSTA_USER@$KINSTA_IP:./public/wp-content/

# Custom Theme
scp -P $KINSTA_STAGING_PORT -r ./wp-content/themes/custom $KINSTA_STAGING_USER@$KINSTA_STAGING_IP:./public/wp-content/themes

# Plugins and must use plugins
#scp -P $KINSTA_STAGING_PORT -r ./wp-content/plugins $KINSTA_STAGING_USER@$KINSTA_STAGING_IP:./public/wp-content/
#scp -P $KINSTA_STAGING_PORT -r ./wp-content/mu-plugins $KINSTA_STAGING_USER@$KINSTA_STAGING_IP:./public/wp-content/

#specific plugins
#scp -P $KINSTA_STAGING_PORT -r ./wp-content/plugins/wc-product-customer-list-premium $KINSTA_STAGING_USER@$KINSTA_STAGING_IP:./public/wp-content/plugins

#specific files
#scp -P $KINSTA_STAGING_PORT ./wp-content/themes/custom/functions/post-types/classes/class-nam-class.php $KINSTA_STAGING_USER@$KINSTA_STAGING_IP:./public/wp-content/themes/custom/functions/post-types/classes/

#functions.php
#scp -P $KINSTA_STAGING_PORT ./wp-content/themes/custom/functions.php $KINSTA_STAGING_USER@$KINSTA_STAGING_IP:./public/wp-content/themes/custom/

#scp -r ./dist/wp-content/uploads root@$DROPLET_IP:/var/www/html/wp-content/
#scp ./dist/migration.sql root@$DROPLET_IP:/root
#scp ./.remote.deploy.sh root@$DROPLET_IP:/root

#ssh root@$DROPLET_IP 'cd /root ; chmod +x ./.remote.deploy.sh ; ./.remote.deploy.sh'

#rm -rf ./dist/wp-content/uploads
#rm ./dist/migration.sql


# TODO: Add a hook to migrate and string-replace the database.
