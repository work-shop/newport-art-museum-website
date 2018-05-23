<?php



foreach ( NAM_Site::$product_post_types as $i => $product_class_name ) {

    if ( $product_class_name::is_archive() ) {

        include( locate_template( $product_class_name::archive_template() ) );

    }

}

?>
