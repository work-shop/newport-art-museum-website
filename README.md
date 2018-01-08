# Newport Art Museum Wordpress Development


## Content Structure


### Membership

### Donations

### Exhibitions

### Events

### Classes

### Groups

### Pages

## Permissions Structure

### System Admin

### Admin

### Editor





## Development Assumptions

## DevOps Assumptions

## Plugin Dependencies: Wordpress

- [Object Sync for Salesforce](https://wordpress.org/plugins/object-sync-for-salesforce/). This plugin provides a low-level abstraction for connecting an arbitrary salesforce instance with an arbitrary wordpress installation. I intend to use it as a base for keeping User data in sync between salesforce and Wordpress.

- [WooCommerce Base](https://woocommerce.com/developers/). Ecommerce Manager. Free. The obvious base ecommerce package for the site.

- [WooCommerce Stripe](https://woocommerce.com/products/stripe/). Payment gateway. Free. We'll use stripe for all transactions. We had a good experience integrating with stripe on a previous project. Stripe also plays well with WooCommerce Subscriptions and securely saving cards, which we'll need.

- [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/). Secure Recurring Payments Scheduler. $199.00. Subscriptions and Recurring payments. We'll want to use this to manage recurring payments, as in donations and memberships.

- [WooCommerce Product Bundles](https://docs.woocommerce.com/documentation/plugins/woocommerce/woocommerce-extensions/product-bundles/). For selling bundled products. $49.00. These may be things like a Course, and an Associate Course Fee.

- [WooCommerce Custom Post Types Manager](http://reigelgallarde.me/doc/woocommerce-custom-post-type-manager/). Allows for woocommerce fields to be associated with non-product post-types. $25.99. We'll use this to turn each of our critical custom post types.

- [TechCrunch WP Asynchronous Tasks](https://github.com/techcrunch/wp-async-task). Allows for Asynchronous request processing for WordPress in PHP: helpful because php execution environments are single-threaded, and we have a lot of processing to do at a given time. Free.


## Setting up the Development Environment.

1. Install [Docker](https://docs.docker.com/engine/installation/). Make sure that the docker installation you're selecting contains `docker-machine` and `docker-compose` as well as the simple `docker` command. Follow the installation prompts.

2. Clone this repository locally to a directory in your workspace. The specific location of the directory is not significant.

3. From inside the cloned drectory, Run `npm install` to get the dependencies for this project. When that's done, run `npm run build` to test your installation. If a directory called `dist` shows up in your workspace, and you don't get any error messages, your front-end development environment is configured properly.

4. Next, we need to enable Public Key access to the staging environment for you. You can do this using the `ssh-copy-id` command line tool, along with the typical public key you use for Digital Ocean, for example. The password for this specific instance (which you'll need to copy your `id_rsa.pub` file over) can be found in the Kinsta management console.

5. Rename the `.sample.env` file to `.env`, and fill in the missing fields `KINSTA_IP`, `KINSTA_IP` with the target Kinsta instance for this project (if relevant, otherwise delete the field), and the `ACF_PRO_KEY` field.

6. run `docker-compose up -d` to provision a new virtual environment and database running wordpress.

7. run `npm run watch` to start the development environment. The docker container will watch for changes as you make them, and reload the page at `http://localhost:8080`.

8. When you're done working, run `docker-compose down` to safely close the development environment.


## Starting Work

1. You only need to do the above steps once. To start working locally, run `docker-compose up -d` to start your containers, and `npm run watch` to start webpack. When you're done working, kill `npm run watch` with `^C`, and run `docker-compose down` to bring down your containers.

2. To deploy your work to the staging site, run `npm run deploy` from the root directory. This command will place whatever's in your compiled `dist` directory into the appropriate locations on the server.


## Pulling the Remote Database

1. Head to the staging environment, log in, and use WP-Migrate-DB to generate a new, string-replaced database file.

2. Run `./.pull-database.sh <path-to-mysql-export>` from the project directory. This script will import this file into the docker mysql instance you have running locally.
