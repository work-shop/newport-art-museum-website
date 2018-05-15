<?php


foreach( NAM_Site::$product_post_types as $i => $product ) {

    if ( $product::is_single() ) {

        include( locate_template( $product::single_template() ) );

    }

}

?>
