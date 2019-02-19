<style>
.section{
    margin-left: -20px;
    margin-right: -20px;
    font-family: "Raleway",san-serif;
}
.section h1{
    text-align: center;
    text-transform: uppercase;
    color: #808a97;
    font-size: 35px;
    font-weight: 700;
    line-height: normal;
    display: inline-block;
    width: 100%;
    margin: 50px 0 0;
}
.section ul{
    list-style-type: disc;
    padding-left: 15px;
}
.section:nth-child(even){
    background-color: #fff;
}
.section:nth-child(odd){
    background-color: #f1f1f1;
}
.section .section-title img{
    display: table-cell;
    vertical-align: middle;
    width: auto;
    margin-right: 15px;
}
.section h2,
.section h3 {
    display: inline-block;
    vertical-align: middle;
    padding: 0;
    font-size: 24px;
    font-weight: 700;
    color: #808a97;
    text-transform: uppercase;
}

.section .section-title h2{
    display: table-cell;
    vertical-align: middle;
    line-height: 25px;
}

.section-title{
    display: table;
}

.section h3 {
    font-size: 14px;
    line-height: 28px;
    margin-bottom: 0;
    display: block;
}

.section p{
    font-size: 13px;
    margin: 25px 0;
}
.section ul li{
    margin-bottom: 4px;
}
.landing-container{
    max-width: 750px;
    margin-left: auto;
    margin-right: auto;
    padding: 50px 0 30px;
}
.landing-container:after{
    display: block;
    clear: both;
    content: '';
}
.landing-container .col-1,
.landing-container .col-2{
    float: left;
    box-sizing: border-box;
    padding: 0 15px;
}
.landing-container .col-1 img{
    width: 100%;
}
.landing-container .col-1{
    width: 55%;
}
.landing-container .col-2{
    width: 45%;
}
.premium-cta{
    background-color: #808a97;
    color: #fff;
    border-radius: 6px;
    padding: 20px 15px;
}
.premium-cta:after{
    content: '';
    display: block;
    clear: both;
}
.premium-cta p{
    margin: 7px 0;
    font-size: 14px;
    font-weight: 500;
    display: inline-block;
    width: 60%;
}
.premium-cta a.button{
    border-radius: 6px;
    height: 60px;
    float: right;
    background: url(<?php echo YITH_YWGC_ASSETS_URL?>/images/upgrade.png) #ff643f no-repeat 13px 13px;
    border-color: #ff643f;
    box-shadow: none;
    outline: none;
    color: #fff;
    position: relative;
    padding: 9px 50px 9px 70px;
}
.premium-cta a.button:hover,
.premium-cta a.button:active,
.premium-cta a.button:focus{
    color: #fff;
    background: url(<?php echo YITH_YWGC_ASSETS_URL?>/images/upgrade.png) #971d00 no-repeat 13px 13px;
    border-color: #971d00;
    box-shadow: none;
    outline: none;
}
.premium-cta a.button:focus{
    top: 1px;
}
.premium-cta a.button span{
    line-height: 13px;
}
.premium-cta a.button .highlight{
    display: block;
    font-size: 20px;
    font-weight: 700;
    line-height: 20px;
}
.premium-cta .highlight{
    text-transform: uppercase;
    background: none;
    font-weight: 800;
    color: #fff;
}

.section:nth-of-type(2){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/01-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section:nth-of-type(3){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/02-bg.png) no-repeat; background-position: 15% 75%
}
.section:nth-of-type(4){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/03-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section:nth-of-type(5){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/04-bg.png) no-repeat; background-position: 15% 75%
}
.section:nth-of-type(6){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/05-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section:nth-of-type(7){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/06-bg.png) no-repeat; background-position: 15% 75%
}
.section:nth-of-type(8){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/07-bg.png) no-repeat #fff; background-position: 85% 75%
}
.section:nth-of-type(10){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/09-bg.png) no-repeat; background-position: 85% 75%
}
.section:nth-of-type(11){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/10-bg.png) no-repeat #fff; background-position: 15% 75%
}
.section:nth-of-type(12){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/11-bg.png) no-repeat; background-position: 85% 75%
}
.section:nth-of-type(13){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/12-bg.png) no-repeat #fff; background-position: 15% 75%
}
.section:nth-of-type(14){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/13-bg.png) no-repeat; background-position: 85% 75%
}
.section:nth-of-type(15){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/14-bg.png) no-repeat #fff; background-position: 15% 75%
}
.section:nth-of-type(16){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/15-bg.png) no-repeat; background-position: 85% 75%
}
.section:nth-of-type(17){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/16-bg.png) no-repeat #fff; background-position: 15% 75%
}
.section:nth-of-type(18){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/17-bg.png) no-repeat; background-position: 85% 75%
}
.section:nth-of-type(19){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/18-bg.png) no-repeat #fff; background-position: 15% 75%
}
.section:nth-of-type(20){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/17-bg.png) no-repeat; background-position: 85% 75%
}
.section:nth-of-type(21){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/18-bg.png) no-repeat #fff; background-position: 15% 75%
}
.section:nth-of-type(22){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/21-bg.png) no-repeat; background-position: 85% 75%
}
.section:nth-of-type(23){
    background: url(<?php echo YITH_YWGC_ASSETS_URL ?>/images/22-bg.png) no-repeat #fff; background-position: 15% 75%
}

@media (max-width: 768px) {
    .section{margin: 0}
    .premium-cta p{
        width: 100%;
    }
    .premium-cta{
        text-align: center;
    }
    .premium-cta a.button{
        float: none;
    }
}

@media (max-width: 480px){
    .wrap{
        margin-right: 0;
    }
    .section{
        margin: 0;
    }
    .landing-container .col-1,
    .landing-container .col-2{
        width: 100%;
        padding: 0 15px;
    }
    .section-odd .col-1 {
        float: left;
        margin-right: -100%;
    }
    .section-odd .col-2 {
        float: right;
        margin-top: 65%;
    }
}

@media (max-width: 320px){
    .premium-cta a.button{
        padding: 9px 20px 9px 70px;
    }

    .section .section-title img{
        display: none;
    }
}
</style>
<div class="landing">
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Gift Cards%2$s to benefit from all features!','yith-woocommerce-gift-cards'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-gift-cards');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-gift-cards');?></span>
                </a>
            </div>
        </div>
    </div>
    <div class="one section section-even clear">
        <h1><?php _e('Premium Features','yith-woocommerce-gift-cards');?></h1>
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/01.png" alt="Feature 01" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/01-icon.png" alt="icon 01"/>
                    <h2><?php _e('Fixed or variable amount?','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Choose the %1$samount of the gift cards%2$s you want to sell in your shop. The choice is simple and for everybody\'s needs. Set the amounts available for the gift cards, or let users decide the amount they want. Two different options for two positive outcomes.', 'yith-woocommerce-gift-cards'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="two section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/02-icon.png" alt="icon 02" />
                    <h2><?php _e(' Who will receive the gift?','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('The receiver of the gift card is expressed by his/her email address. So easy! But what if you want to send the gift cards to more people? You just have to add all the %1$semail addresses%2$s et voilà, the gift will be sent.', 'yith-woocommerce-gift-cards'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/02.png" alt="feature 02" />
            </div>
        </div>
    </div>
    <div class="three section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/03.png" alt="Feature 03" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/03-icon.png" alt="icon 03" />
                    <h2><?php _e( 'Customization of the gift cards','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Each gift card is totally customizable by who purchases it. %1$sImages%2$s and %1$stexts%2$s can be changed by users\' tastes, to make them free to shape their gift.', 'yith-woocommerce-gift-cards'), '<b>', '</b>');?>
                </p>
            </div>
        </div>
    </div>
    <div class="four section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/04-icon.png" alt="icon 04" />
                    <h2><?php _e('...when will it be delivered?','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf(__('Users can decide when sending the email that contains the gift card: a vital feature to allow your customers to %1$spostpone the delivery of the gift%2$s. A completely automatic process, with no problems nor worries.', 'yith-woocommerce-gift-cards'), '<b>', '</b>');?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/04.png" alt="Feature 04" />
            </div>
        </div>
    </div>
    <div class="five section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/05.png" alt="Feature 05" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL?>/images/05-icon.png" alt="icon 05" />
                    <h2><?php _e('I suggest you this product','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Another new feature of the premium version! %1$sYou can give you a gift card and at the same time suggest what to purchase.%2$s Obviously, who will receive your card can purchase whatever he/she wants, but it is always a good thing to advise something.%3$s You will have an order of the amount of the selected product, and your shop will grow more and more.','yith-woocommerce-gift-cards' ),'<b>','</b>','<br>') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="six section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/06-icon.png" alt="icon 06" />
                    <h2><?php _e('Not just virtual...','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('If you prefer to deliver the physical copy of the gift card, %1$syou are free to create a tailored product%2$s for this need. There are many people who prefer to touch their gifts rather than see them in an email.','yith-woocommerce-gift-cards'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/06.png" alt="Feature 06" />
            </div>
        </div>
    </div>
    <div class="five section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/07.png" alt="Feature 07" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL?>/images/07-icon.png" alt="icon 07" />
                    <h2><?php _e('Notification for used gift cards','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Who purchases a gift card would probably like to be %1$snotified whenever it is used%2$s. Enable the specific option and let users know their gift cards have been appreciated!','yith-woocommerce-gift-cards' ),'<b>','</b>') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="six section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/08-icon.png" alt="icon 08" />
                    <h2><?php _e('Email status check','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Check the status and potential errors of emails containing gift cards. In case you receive a notice about %1$ssending/receiving error%2$s, in one click you could easily send it again and make your users happy.','yith-woocommerce-gift-cards'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/08.png" alt="Feature 08" />
            </div>
        </div>
    </div>
    <div class="nine section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/09.png" alt="Feature 07" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL?>/images/09-icon.png" alt="icon 07" />
                    <h2><?php _e('Suspended gift card','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'There are many reasons why you could need to temporarily block the use of a specific %1$sgift card purchased%2$s on your shop. You could do it with a simple click by administration panel, and reactivate it only when you decide.','yith-woocommerce-gift-cards' ),'<b>','</b>') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="ten section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/10-icon.png" alt="icon 10" />
                    <h2><?php _e('Synchronization with orders','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Gifts cards can be automatically %1$ssuspended%2$s or %1$sremoved%2$s when the related order acquires the "refunded" or "completed" status.','yith-woocommerce-gift-cards'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/10.png" alt="Feature 10" />
            </div>
        </div>
    </div>
    <div class="eleven section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/11.png" alt="Feature 07" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL?>/images/11-icon.png" alt="icon 07" />
                    <h2><?php _e('Company logo','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Two different templates for the gift cards of your shop: %1$schoose if and where to show your company logo%2$s by using the structure you prefer.','yith-woocommerce-gift-cards' ),'<b>','</b>') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="twelve section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/12-icon.png" alt="icon 10" />
                    <h2><?php _e('Wide range of gift cards','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('No more only one gift card typology to sell in your shop! Release your creative energy by assigning a %1$sdifferent image to each gift card product%2$s. Your users could choose from several solutions.','yith-woocommerce-gift-cards'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/12.png" alt="Feature 10" />
            </div>
        </div>
    </div>
    <div class="thirteen section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/13.png" alt="Feature 13" />
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL?>/images/11-icon.png" alt="icon 13" />
                    <h2><?php _e('Image gallery','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'More freedom for your users! Create a %1$sgift card portfolio%2$s and give them the possibility to choose the one to use for the gift card during the purchase. With a rich and varied image gallery you could satisfy all your users’ tastes.','yith-woocommerce-gift-cards' ),'<b>','</b>') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="fourteen section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/14-icon.png" alt="icon 14" />
                    <h2><?php _e('Shipping fees','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php _e('How did you feel when, purchasing by a gift card, you realized you had to pay the shipping fees separately?','yith-woocommerce-gift-cards'); ?>
                </p>
                <p>
                    <?php echo sprintf( __('It must be frustrating.%3$s From now on, with YITH WooCommerce Gift Cards, you can choose to %1$spay the shipping fees through the Gift Card%2$s and offer your users the possibility to take advantage of promotional code they own. ','yith-woocommerce-gift-cards'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/14.jpg" alt="Feature 14" />
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/15.png"/>
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL?>/images/15-icon.png"/>
                    <h2><?php _e('Create and manage the gift cards from the back-end','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'From now on, you can %1$screate%2$s and %1$sedit%2$s your gift cards also from your site back-end.%3$sCreate a new gift card in a few steps and deliver the coupon code to your user. ','yith-woocommerce-gift-cards' ),'<b>','</b>','<br>') ?>
                </p>
                <p>
                    <?php echo _e( 'Or else, edit the details of an available gift card if the buyer made an error related to the delivery date or the recipient\'s email address. ','yith-woocommerce-gift-cards' ); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/16-icon.png"/>
                    <h2><?php _e('Manage the gift cards offsite','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Is part of your shop also physical? You can rely on YITH WooCommerce Gift Cards. %3$sLet your users purchase the product in your shop by using their gift card and %1$supdate the balance manually%2$s.%3$s In this way, you can avoid the checkout process and the creation of a new order on your online shop! ','yith-woocommerce-gift-cards'),'<b>','</b>','<br>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/16.png"/>
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/17.png"/>
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL?>/images/17-icon.png"/>
                    <h2><?php _e('Stock management','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Have you created a %1$slimited run gift card?%2$s If so, take the opportunity to enable the "Stock management".','yith-woocommerce-gift-cards' ),'<b>','</b>') ?>
                </p>
                <p>
                    <?php _e( 'Configure the quantity for the product so that it will turn into out-of-stock as soon as it reaches the maximum available number. ','yith-woocommerce-gift-cards' ); ?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/18-icon.png"/>
                    <h2><?php _e('Create your gift card templates','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Doesn\'t our design match with the style of your site? %1$sOverride the plugin default templates%2$s and apply the changes you need to achieve the result you wish. ','yith-woocommerce-gift-cards'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/18.png"/>
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/19.png"/>
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL?>/images/19-icon.png"/>
                    <h2><?php _e('Set an expiration for the gift card','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Now you can set an expiration for all the gift cards of your shop. This will %1$sprevent to have active cards for too long%2$s and will encourage the users to use the remaining balance before it expires. ','yith-woocommerce-gift-cards' ),'<b>','</b>') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/20-icon.png"/>
                    <h2><?php _e('Show the recipients in the Cart page','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('A small action to %1$simprove the user experience%2$s during the purchase of one or more gift cards. Show the email address linked to each card to let the %1$suser check%2$s the correct addition of the information before proceeding to checkout.','yith-woocommerce-gift-cards'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/20.png"/>
            </div>
        </div>
    </div>
    <div class="section section-even clear">
        <div class="landing-container">
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/21.png"/>
            </div>
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL?>/images/21-icon.png"/>
                    <h2><?php _e('Decide where customers can add their code','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __( 'Place the %1$s"apply gift card code"%2$s form wherever you want in the cart page and checkout page. You decide where on the page you want to place the form. With the drop down menu you can select the exact location. Customers will be able to %1$sdirectly enter%2$s their code without clicking on a link.' ),'<b>','</b>') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="section section-odd clear">
        <div class="landing-container">
            <div class="col-2">
                <div class="section-title">
                    <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/22-icon.png"/>
                    <h2><?php _e('Switch easily from Smart Coupons to YITH Gift Cards','yith-woocommerce-gift-cards');?></h2>
                </div>
                <p>
                    <?php echo sprintf( __('Thanks to the integration with WooCommerce Smart Coupons, you can dismiss to use Smart Coupons and convert all the coupons created with that plugin, into new gift cards','yith-woocommerce-gift-cards'),'<b>','</b>'); ?>
                </p>
            </div>
            <div class="col-1">
                <img src="<?php echo YITH_YWGC_ASSETS_URL ?>/images/22.png"/>
            </div>
        </div>
    </div>
    <div class="section section-cta section-odd">
        <div class="landing-container">
            <div class="premium-cta">
                <p>
                    <?php echo sprintf( __('Upgrade to %1$spremium version%2$s of %1$sYITH WooCommerce Gift Cards%2$s to benefit from all features!','yith-woocommerce-gift-cards'),'<span class="highlight">','</span>' );?>
                </p>
                <a href="<?php echo $this->get_premium_landing_uri() ?>" target="_blank" class="premium-cta-button button btn">
                    <span class="highlight"><?php _e('UPGRADE','yith-woocommerce-gift-cards');?></span>
                    <span><?php _e('to the premium version','yith-woocommerce-gift-cards');?></span>
                </a>
            </div>
        </div>
    </div>
</div>