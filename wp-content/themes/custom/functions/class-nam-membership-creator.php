<?php


class NAM_Membership_Creator {

    public static $imported_member_meta = '_nam_imported_member_user';
    public static $imported_membership_meta = '_nam_imported_membership_subscription';
    public static $imported_membership_order_meta = '_nam_imported_membership_order';

    public static $field_keys = array(

        'member_email' => 'field_5be7480ff9ddf',
        'member_first_name' => 'field_5be74823f9de0',
        'member_last_name' => 'field_5be74845f9de1',

        'use_existing_account' => 'field_5be7513f031e4',

        'member_level' => 'field_5be749037b218',
        'membership_start_date' => 'field_5be748b7f9de4',
        'membership_expiration_date' => 'field_5be748def9de5',

        'create_membership_subscription' => 'field_5be9ad2a0f99f'

    );


    public function add_meta_box_actions() {
        add_action( 'add_meta_boxes', array( $this, 'add_subscription_meta_box' ) );
    }


    public function add_acf_actions() {
        add_action( 'acf/save_post', array( $this, 'do_acf_membership_creation_actions' ) );
        add_filter( 'acf/validate_value/key=' . static::$field_keys['member_email'], array( $this, 'validate_email'), 10, 4);
        add_filter( 'acf/validate_value/key=' . static::$field_keys['member_first_name'], array( $this, 'validate_username'), 10, 4);
        add_filter( 'acf/validate_value/key=' . static::$field_keys['member_last_name'], array( $this, 'validate_username'), 10, 4);
    }

    public function remove_acf_actions() {
        remove_action( 'acf/save_post', array( $this, 'do_acf_membership_creation_actions' ) );
        remove_filter( 'acf/validate_value/key=' . static::$field_keys['member_email'], array( $this, 'validate_email'), 10, 4);
        remove_filter( 'acf/validate_value/key=' . static::$field_keys['member_first_name'], array( $this, 'validate_username'), 10, 4);
        remove_filter( 'acf/validate_value/key=' . static::$field_keys['member_last_name'], array( $this, 'validate_username'), 10, 4);
    }



    public function __construct() {
        $this->add_acf_actions();
        $this->add_meta_box_actions();
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

        $this->add_acf_actions();

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
            'create_subscription' => (int) get_field('create_membership_subscription', 'option')
        );

        return $member_data;

    }

    /**
     * Given parsed user data, create a new user
     * based on the passed parameters.
     *
     *
     */
    public function create_member( $user_data ) {

        if ( $user_data['use_existing_account'] ) {

            $user = get_user_by( 'email', $user_data[ 'email' ] );

            $user_id = $user->ID;

        } else {

            $user_id = wp_insert_user( array(
                'user_login' => $user_data['username'],
                'user_email' => $user_data['email'],
                'user_pass' => wp_generate_password(),
                'first_name' => $user_data['first_name'],
                'last_name' => $user_data['last_name'],
                'user_nicename' => $user_data['first_name'] . ' ' . $user_data['last_name'],
                'description' => 'Imported via Membership Creator.',
                'role' => 'customer'
            ) );



        }

        update_user_meta( $user_id, static::$imported_member_meta, 'yes' );

        return $user_id;

    }


    public function create_subscription( $user_id, $user_data ) {

        $product = $user_data['member_level'];
        $start_date = $user_data['membership_start_date'];
        $exp_date = $user_data['membership_expiration_date'];
        $quantity = 1;

        // $order_args = array(
        //     'attribute_billing-period' => 'Yearly',
        //     'attribute_subscription-type' => 'Both'
        // );
        //
        // $order = wc_create_order( array( 'customer_id' => $user_id ) );
        // $order->add_product( $product, $quantity, $order_args );
        // $order->calculate_totals();
        // $order->update_status('completed', 'Order imported via Membership Creator.', TRUE );

        $period = WC_Subscriptions_Product::get_period( $product );
        $interval = WC_Subscriptions_Product::get_interval( $product );
        $length = WC_Subscriptions_Product::get_interval( $product );

        $subscription = wcs_create_subscription( array(
            'customer_id' => $user_id,
            //'order_id' => $order->get_id(),
            'billing_period' => $period,
            'billing_interval' => $interval,
            'start_date' => $start_date
        ) );

        $subscription->set_customer_id( $user_id );
        $subscription->add_product( $product, $quantity, $order_args );

        // NOTE: Add Next Payment here, if needed. 'end' must be later than 'next_payment'.
        // In fact, to match salesforce, end date should be one month after next payment.
        // We'd need to flag this for the user, though.
        $subscription->update_dates( array( 'end' => $exp_date ) );
        $subscription->calculate_totals();

        $subscription->save();

        update_post_meta( $subscription->id, '_customer_user', $user_id );
        update_post_meta( $subscription->id, static::$imported_membership_meta, 'yes' );
        //update_post_meta( $order->id, static::$imported_membership_order_meta, 'yes' );

        $subscription->update_status('active', 'Activated via Membership Creator.', true);
        $subscription->save();

        //WC_Subscriptions_Manager::activate_subscriptions_for_order( $order );

    }


    public function build_member_data_from_csv_row( $row ) {

    }


    // VIEW FUNCTIONS and META BOXES

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
