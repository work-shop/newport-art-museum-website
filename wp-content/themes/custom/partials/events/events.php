<?php $posts = NAM_Event::get_posts(); ?>

<?php foreach ( $posts as $i => $event ) : ?>

    <?php include( locate_template( 'partials/events/event_card.php' ) ); ?>

<?php endforeach; ?>
