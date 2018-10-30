<?php if ( ! is_flexible_checkout_fields_pro_active() ): ?>

    <div class="stuffbox">
        <h3><?php _e( 'Enjoying the free version? Rate it!', 'flexible-checkout-fields' ); ?></h3>

        <div class="inside">
            <div class="main">
                <p class="rate"><a href="https://wpde.sk/fcf-rate" target="_blank"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></a></p>

                <p><?php printf( __( 'If you want to continue using Flexible Checkout Fields for free, %splease add a review%s. You will help us support the free version. Thank you.', 'flexible-checkout-fields' ),  '<a href="https://wpde.sk/fcf-rate" target="_blank">', '</a>' ); ?></p>
            </div>
        </div>
    </div>

<?php endif; ?>
