<?php defined( 'ABSPATH' ) || die; ?>

<div id="woo-mp-main">

    <?php require WOO_MP_PATH . '/templates/payment-tabs.php'; ?>

    <div id="charge" class="charge tab-content tab-content-active">

        <div id="charge-notice" hidden></div>

        <div class="charge-form">
        
            <?php require WOO_MP_PATH . '/templates/' . WOO_MP_PAYMENT_PROCESSOR . '-charge-form.php'; ?>

        </div>

    </div>

    <div id="refund" class="refund tab-content">

        <?php

        if ( WOO_MP_PRO ) {
            require WOO_MP_PRO_PATH . '/templates/' . WOO_MP_PAYMENT_PROCESSOR .'-refund.php';
        } else {
            require WOO_MP_PATH . '/templates/upgrade.php';
        }

        ?>

    </div>

    <?php require WOO_MP_PATH . '/templates/notice-template.php'; ?>

</div>