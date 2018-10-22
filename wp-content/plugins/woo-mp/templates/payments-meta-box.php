<?php defined( 'ABSPATH' ) || die; ?>

<div id="woo-mp-main">

    <?php $this->template( 'payment-tabs' ); ?>

    <div id="charge" class="charge tab-content tab-content-active">

        <div id="charge-notice" hidden></div>

        <div class="charge-form">

            <?php $this->template( 'charge-form' ); ?>

        </div>

    </div>

    <div id="refund" class="refund tab-content">

        <?php

        if ( WOO_MP_PRO ) {
            $this->template( 'refund' );
        } else {
            $this->template( 'upgrade' );
        }

        ?>

    </div>

    <?php $this->template( 'notice-template' ); ?>

</div>