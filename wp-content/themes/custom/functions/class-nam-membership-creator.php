<?php


class NAM_Membership_Creator {

    public static $imported_member_meta = '_nam_imported_member_user';
    public static $imported_membership_meta = '_nam_imported_membership_subscription';
    public static $imported_membership_order_meta = '_nam_imported_membership_order';
    public static $imported_membership_salesforce_id = '_nam_imported_membership_salesforce_id';

    public static $required_headers = array(
        'Salesforce ID' => 'salesforce_id',
        'First Name' => 'first_name',
        'Last Name' => 'last_name',
        'Email' => 'email',
        'Member Level' => 'member_level',
        'Start Date' => 'membership_start_date',
        'End Date' => 'membership_expiration_date'
    );

    public static $membership_levels = array(
        'Individual' => 1562,
        'Senior' => 1560,
        'Student' => 1558,
        'Military Individual' => 1556,
        'Household' => 1554,
        'Military Household' => 1552,
        'Senior Household' => 1549,
        'Patron' => 1545,
        'Council' => 1543,
        'Benefactor' => 1527
    );

    public static $field_keys = array(

        'member_email' => 'field_5be7480ff9ddf',
        'member_first_name' => 'field_5be74823f9de0',
        'member_last_name' => 'field_5be74845f9de1',

        'use_existing_account' => 'field_5be7513f031e4',

        'member_level' => 'field_5be749037b218',
        'membership_start_date' => 'field_5be748b7f9de4',
        'membership_expiration_date' => 'field_5be748def9de5',

        'create_membership_subscription' => 'field_5be9ad2a0f99f',

        'import_csv' => 'field_5be9dda4dfa06',
        'membership_csv' => 'field_5be9cb4d6234a'

    );


    public function add_meta_box_actions() {
        add_action( 'add_meta_boxes', array( $this, 'add_subscription_meta_box' ) );
    }


    public function add_acf_actions() {
        add_action( 'acf/save_post', array( $this, 'do_acf_membership_creation_actions' ) );
        add_action( 'acf/save_post', array( $this, 'do_csv_membership_import_actions' ) );
        add_filter( 'acf/validate_value/key=' . static::$field_keys['member_email'], array( $this, 'validate_email'), 10, 4);
        add_filter( 'acf/validate_value/key=' . static::$field_keys['member_first_name'], array( $this, 'validate_username'), 10, 4);
        add_filter( 'acf/validate_value/key=' . static::$field_keys['member_last_name'], array( $this, 'validate_username'), 10, 4);
        add_filter( 'acf/validate_value/key=' . static::$field_keys['membership_csv'], array( $this, 'validate_csv'), 10, 4);
    }

    public function remove_acf_actions() {
        remove_action( 'acf/save_post', array( $this, 'do_acf_membership_creation_actions' ) );
        remove_action( 'acf/save_post', array( $this, 'do_csv_membership_import_actions' ) );
        remove_filter( 'acf/validate_value/key=' . static::$field_keys['member_email'], array( $this, 'validate_email'), 10, 4);
        remove_filter( 'acf/validate_value/key=' . static::$field_keys['member_first_name'], array( $this, 'validate_username'), 10, 4);
        remove_filter( 'acf/validate_value/key=' . static::$field_keys['member_last_name'], array( $this, 'validate_username'), 10, 4);
        remove_filter( 'acf/validate_value/key=' . static::$field_keys['membership_csv'], array( $this, 'validate_csv'), 10, 4);
    }



    public function __construct() {
        $this->add_acf_actions();
        $this->add_meta_box_actions();
    }


    /**
     * This routine validates the uploaded file
     * is readable, and ensures it has the headers
     * that are needed for processing. It does not validate
     * the values in those headers match the required values
     * for the import.
     *
     */
    public function validate_csv( $valid, $value, $field, $input ) {

        $path = get_attached_file( $value );

        if ( !is_readable( $path ) ) { chmod( $path, 0774 ); }

        if ( !is_readable( $path ) ) { return 'Selected CSV file is not readable.'; }

        if ( $file = fopen( $path, 'r' ) ) {

            $headers = fgetcsv( $file );
            $missing_headers = array();
            $headers_valid = TRUE;

            foreach ( array_keys( static::$required_headers ) as $column_header ) {

                if ( !in_array( $column_header, $headers ) ) {

                    $missing_headers[] = $column_header;
                    $headers_valid = FALSE;

                }

            }

            fclose( $file );

            if ( $headers_valid ) {

                return $valid;

            } else {

                return 'This CSV is missing ' . join( ', ', $missing_headers ) . ' as headers.';

            }


        } else {

            return 'Couldn\'t open the file for reading.';

        }

    }


    /**
     * This routine is called when the Membership Creator
     * form fields are submitted. It's responsible for
     * validating that an email is available depending
     * on the configuration specified by the user.
     *
     * @hooked acf/validate_value/key=field_5be7480ff9ddf
     */
    public function validate_email( $valid, $value, $field, $input ) {

        if ( !$valid ) { return $valid; }

        $use_existing = (int) $_POST['acf'][ static::$field_keys['use_existing_account'] ];

        $email_exists = email_exists( $value );

        if ( 1 == $use_existing ) {

            if ( !$email_exists ) {

                $valid = 'No account with this email exists yet!';

            }

        } else {

            if ( $email_exists ) {

                $valid = 'An account with this email already exists.';

            }

        }

        return $valid;

    }

    /**
     * This routine is called when the Membership Creator
     * form fields are submitted. It's responsible for
     * validating that A username is available depending
     * on the configuration specified by the user.
     *
     * @hooked acf/validate_value/key=field_5be74823f9de0
     * @hooked acf/validate_value/key=field_5be74845f9de1
     */
    public function validate_username( $valid, $value, $field, $input ) {

        $use_existing = (int) $_POST['acf'][ static::$field_keys['use_existing_account'] ];
        $first_name = $_POST['acf'][ static::$field_keys['member_first_name'] ];
        $last_name = $_POST['acf'][ static::$field_keys['member_last_name'] ];
        $username = sanitize_title_with_dashes( $first_name . '-' . $last_name, '', 'save' );

        $username_exists = username_exists( $username );

        if ( 0 == $use_existing ) {

            if ( $username_exists ) {

                $valid = 'An account with this First Name / Last Name pair exists.';

            }

        }

        return $valid;


    }


    public function do_csv_membership_import_actions() {

        $screen = get_current_screen();

        if ( $screen->id != 'toplevel_page_acf-options-membership-importer' ) { return; }

        $this->remove_acf_actions();

        $data = $this->parse_csv_as_array();

        $this->insert_csv_data( $data );

        $this->clean_up_csv_fields();

        $this->add_acf_actions();

    }


    /**
     * This function strings together the key actions required
     * for creating a new user and membership based on options
     * input.
     */
    public function do_acf_membership_creation_actions() {

        $screen = get_current_screen();

        if ( $screen->id != 'toplevel_page_acf-options-membership-creator' ) { return; }

        $this->remove_acf_actions();

        $user_data = $this->build_member_data_from_acf();

        $user_id = $this->create_member( $user_data );

        if ( $user_data['create_subscription'] ) {

            $subscription_id = $this->create_subscription( $user_id, $user_data );

        }

        $this->clean_up_acf_fields();

        $this->add_acf_actions();

    }

    /**
     * This function deletes values
     * from the ACF fields in membership creator.
     */
    public function clean_up_acf_fields() {

        delete_field('member_email', 'option');
        delete_field('member_first_name', 'option');
        delete_field('member_last_name', 'option');

        delete_field('member_level', 'option');
        delete_field('membership_start_date', 'option');
        delete_field('membership_expiration_date', 'option');

    }

    /**
     * This function deletes values
     * from the ACF fields in membership importer.
     */
    public function clean_up_csv_fields() {
        delete_field('membership_csv', 'option');
    }


    /**
     * This routine parses the user-set ACF data
     * To get the key information for programatically
     * inserting a user and creating a subscription.
     *
     * @return Array array of member data parsed from ACF fields.
     */
    public function build_member_data_from_acf() {

        $membership_product = get_field('member_level', 'option');
        $membership_product = wc_get_product( $membership_product->ID );

        $first_name = get_field('member_first_name', 'option');
        $last_name = get_field('member_last_name', 'option');

        $member_data = array(
            'email' => get_field('member_email', 'option'),
            'first_name' => $first_name,
            'last_name' => $last_name,
            'username' => sanitize_title_with_dashes( $first_name . '-' . $last_name, '', 'save' ),
            'member_level' => $membership_product,
            'membership_start_date' => get_field('membership_start_date', 'option'),
            'membership_expiration_date' => get_field('membership_expiration_date', 'option'),
            'use_existing_account' => (int) get_field( 'use_existing_account', 'option' ),
            'create_subscription' => (int) get_field('create_membership_subscription', 'option'),
            'salesforce_id' => ''
        );

        return $member_data;

    }

    /**
     * Given parsed user data, create a new user
     * based on the passed parameters.
     *
     *
     */
    public function create_member( $user_data, $description='Imported via Membership Creator.' ) {

        if ( $user_data['use_existing_account'] ) {

            $user = get_user_by( 'email', $user_data[ 'email' ] );

            $user_id = $user->ID;

        } else {

            $user_params = array(
                'user_login' => $user_data['username'],
                'user_email' => $user_data['email'],
                'user_pass' => wp_generate_password(),
                'first_name' => $user_data['first_name'],
                'last_name' => $user_data['last_name'],
                'user_nicename' => $user_data['first_name'] . ' ' . $user_data['last_name'],
                'description' => $description,
                'role' => 'customer'
            );

            $user_id = wp_insert_user( $user_params );

        }

        update_user_meta( $user_id, static::$imported_member_meta, 'yes' );

        return $user_id;

    }


    public function create_subscription( $user_id, $user_data, $description='Activated via Membership Creator.' ) {

        $product = $user_data['member_level'];
        $start_date = $user_data['membership_start_date'];
        $exp_date = $user_data['membership_expiration_date'];
        $next_payment_date = DateTime::createFromFormat( 'Y-m-d H:i:s', $exp_date );
        $next_payment_date = $next_payment_date->modify('-1 day');
        $next_payment_date = $next_payment_date->format('Y-m-d H:i:s');
        $quantity = 1;

        $order_args = array(
            'attribute_billing-period' => 'Yearly',
            'attribute_subscription-type' => 'Both'
        );
        //
        // $order = wc_create_order( array( 'customer_id' => $user_id ) );
        // $order->add_product( $product, $quantity, $order_args );
        // $order->calculate_totals();
        // $order->payment_complete();
        // $order->update_status('completed', 'Order imported via Membership Creator.', TRUE );

        $period = WC_Subscriptions_Product::get_period( $product );
        $interval = WC_Subscriptions_Product::get_interval( $product );
        $length = WC_Subscriptions_Product::get_length( $product );

        $subscription = wcs_create_subscription( array(
            'customer_id' => $user_id,
            //'order_id' => $order->get_id(),
            'billing_period' => $period,
            'billing_interval' => $interval,
        //    'billing_length' => $length,
            'start_date' => $start_date
        ) );

        $subscription->set_customer_id( $user_id );
        $subscription->add_product( $product, $quantity, $order_args );
        $subscription->calculate_totals();
        $subscription->payment_complete();



        // NOTE: Add Next Payment here, if needed. 'end' must be later than 'next_payment'.
        // In fact, to match salesforce, end date should be one month after next payment.
        // We'd need to flag this for the user, though.


        if ( $exp_date > date('Y-m-d H:i:s') ) {
            $subscription->update_dates( array( 'next_payment' => $next_payment_date, 'end' => $exp_date ) );
            $subscription->update_status('active', $description, true);
        } else {
            $subscription->update_dates( array( 'next_payment' => $next_payment_date, 'end' => $exp_date ) );
            $subscription->update_status('expired', $description);
        }

        $subscription->save();

        update_post_meta( $subscription->id, '_customer_user', $user_id );
        update_post_meta( $subscription->id, static::$imported_membership_meta, 'yes' );
        update_post_meta( $subscription->id, static::$imported_membership_salesforce_id, $user_data['salesforce_id'] );
        //update_post_meta( $order->id, static::$imported_membership_order_meta, 'yes' );

    }



    public function parse_csv_as_array() {

        $handle = get_field( 'membership_csv', 'option');
        $handle_id = $handle['ID'];

        $path = get_attached_file( $handle_id );

        if ( !is_readable( $path ) ) { chmod( $path, 0774 ); }

        if ( !is_readable( $path ) ) { return 'Selected CSV file is not readable.'; }

        if ( $file = fopen( $path, 'r' ) ) {

            $headers = fgetcsv( $file );
            $data = array();

            while( $row = fgetcsv( $file ) ) {

                $record = array();

                foreach( $headers as $index => $header ) {
                    $record[ static::$required_headers[ $header ] ] = $row[ $index ];
                }

                $record = $this->build_username( $record );

                $record = $this->map_membership_products( $record );

                $data[] = $record;

            }

            fclose( $file );

            return $data;

        } else {

            throw new Exception('Couldn\'t open the CSV for reading.');

        }

    }


    public function build_username( $row ) {

        $row[ 'username' ] = sanitize_title_with_dashes( $row['first_name'] . '-' . $row['last_name'], '', 'save' );

        return $row;

    }



    public function map_membership_products( $row ) {

        $id = static::$membership_levels[ $row[ static::$required_headers['Member Level'] ] ];

        $product = wc_get_product( $id );

        $row[ static::$required_headers['Member Level'] ] = $product;

        return $row;

    }


    public function insert_csv_data( $csv_data ) {

        foreach ( $csv_data as $row ) {

            $email = $row[ static::$required_headers['Email'] ];

            $row['use_existing_account'] = 0;

            if ( email_exists( $email ) ) { $row['use_existing_account'] = 1; }

            $user_id = $this->create_member( $row, 'Imported via Membership CSV Import.' );

            $this->create_subscription( $user_id, $row, 'Activated via Membership CSV Import.' );

        }


    }




    // VIEW FUNCTIONS and META BOXES ===========================================

    public function add_subscription_meta_box() {
        add_meta_box(
            'nam_subscription_flag',
            'Subscription Record Type',
            array( $this, 'render_subscription_meta_box' ),
            'shop_subscription',
            'normal',
            'high'
        );
    }

    public function render_subscription_meta_box() {
        global $post;

        $order_status = get_post_meta( $post->ID, static::$imported_membership_meta, true );

        if ( 'yes' == $order_status ) {

            echo '<p class="nam-imported-subscription subscription-flag">';
            echo 'This membership subscription was imported via the Membership Creator.';
            echo '</p>';

        } else {

            echo '<p class="nam-user-created-subscription subscription-flag">';
            echo 'This membership subscription was created through the site\'s front-end.';
            echo '</p>';

        }

    }


}

new NAM_Membership_Creator();



?>
