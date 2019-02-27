<h2 class="nav-tab-wrapper">
    <?php foreach ( $args['tabs'] as $tab_key => $tab_caption ): ?>
        <?php $active = $args['current_tab'] == $tab_key ? 'nav-tab-active' : '';?>
        <a class="nav-tab <?= $active ?>" href="?page=inspire_checkout_fields_settings&tab=<?= $tab_key ?>"><?php echo $tab_caption; ?></a>
    <?php endforeach; ?>
</h2>
<?php if ( !is_flexible_checkout_fields_pro_active() ) : ?>
    <p><?php echo sprintf( __( 'Read the %sconfiguration manual &rarr;%s', 'flexible-checkout-fields' ), '<a href="' . $docs_link . '" target="_blank">', '</a>' ); ?></p>
<?php endif; ?>
<?php if ( function_exists('icl_object_id') ) : ?>
    <p><?php echo sprintf( __( 'WPML detected. Read %sthis instructions if you want to translate Flexible Checkout Fields. &rarr;%s', 'flexible-checkout-fields' ), '<a href="https://wpml.org/faq/string-translation-default-language-not-english/" target="_blank">', '</a>' ); ?></p>
<?php endif; ?>
