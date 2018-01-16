<?php

class NAM_Classes_Category extends NAM_Taxonomy {

    public static $slug = 'classes-categories';

    public static $singular_name = 'Classes Category';

    public static $plural_name = 'Classes Categories';

    public static $registered_post_types = array( 'classes' );

    public static function register() { parent::register( NAM_Classes_Category::$slug, NAM_Classes_Category::$singular_name, NAM_Classes_Category::$plural_name, NAM_Classes_Category::$registered_post_types ); }

}

?>
