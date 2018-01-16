<?php

class NAM_Product_Category extends NAM_Taxonomy {

    public static $slug = 'product-categories';

    public static $singular_name = 'Product Category';

    public static $plural_name = 'Product Categories';

    public static $registered_post_types = array( 'shop-products' );

    public static function register() { parent::register( NAM_Product_Category::$slug, NAM_Product_Category::$singular_name, NAM_Product_Category::$plural_name, NAM_Product_Category::$registered_post_types ); }

}

?>
