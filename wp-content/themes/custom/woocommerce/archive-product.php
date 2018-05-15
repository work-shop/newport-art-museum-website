<?php

foreach( NAM_Site::$product_post_types as $i => $product ) {

    if ( $product::is_archive() ) {

        include( locate_template( $product::archive_template() ) );

    }

}

?>
