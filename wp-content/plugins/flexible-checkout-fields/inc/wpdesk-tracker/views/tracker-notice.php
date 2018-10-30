<?php if ( ! defined( 'ABSPATH' ) ) { exit; } ?>

<div id="wpdesk_tracker_notice" class="updated notice wpdesk_tracker_notice is-dismissible">
    <p>
        <?php printf ( __( 'Hey %s,', 'wpdesk-tracker'), $username ); ?><br/>
        <?php _e( 'We need your help to improve <strong>WP Desk plugins</strong>, so they are more useful for you and the rest of <strong>30,000+ users</strong>. By collecting data on how you use our plugins, you will help us a lot. We will not collect any sensitive data, so you can feel safe.', 'wpdesk-tracker'); ?>
        <a href="<?php echo $terms_url; ?>" target="_blank"><?php _e( 'Find out more &raquo;', 'wpdesk-tracker' ); ?></a>
    </p>
    <p>
        <button id="wpdesk_tracker_allow_button_notice" class="button button-primary"><?php _e( 'Allow', 'wpdesk-tracker' ); ?></button>
    </p>
</div>

<script type="text/javascript">
    jQuery(document).on('click', '#wpdesk_tracker_notice .notice-dismiss',function(e){
        e.preventDefault();
        console.log('dismiss');
        jQuery.ajax( '<?php echo admin_url('admin-ajax.php'); ?>',
            {
                type: 'POST',
                data: {
                    action: 'wpdesk_tracker_notice_handler',
                    type: 'dismiss',
                }
            }
        );
    })
    jQuery(document).on('click', '#wpdesk_tracker_allow_button_notice',function(e){
        e.preventDefault();
        console.log('allow');
        jQuery.ajax( '<?php echo admin_url('admin-ajax.php'); ?>',
            {
                type: 'POST',
                data: {
                    action: 'wpdesk_tracker_notice_handler',
                    type: 'allow',
                }
            }
        );
        jQuery('#wpdesk_tracker_notice').hide();
    });
</script>
