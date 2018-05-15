<div id="event-<?php echo $event->ID; ?>" class="">
    <h1><?php echo $event->ID; ?></h1>

    <form class="" action="<?php echo esc_url( get_permalink() ); ?>" method="post" enctype="multipart/form-data">
        <button type="submit" name="add-to-cart" value="<?php echo $event->ID; ?>"><?php var_dump( $event ); ?></button>
    </form>
</div>
