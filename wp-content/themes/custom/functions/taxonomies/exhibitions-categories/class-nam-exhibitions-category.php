<?php

class NAM_Exhibitions_Category extends NAM_Taxonomy {

    public static $slug = 'exhibitions-categories';

    public static $singular_name = 'Exhibitions Category';

    public static $plural_name = 'Exhibitions Categories';

    public static $registered_post_types = array( 'exhibitions' );

    public static function register() { parent::register( NAM_Exhibitions_Category::$slug, NAM_Exhibitions_Category::$singular_name, NAM_Exhibitions_Category::$plural_name, NAM_Exhibitions_Category::$registered_post_types ); }

}

?>
