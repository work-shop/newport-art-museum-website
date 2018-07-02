<?php

class NAM_Classes_Days extends NAM_Taxonomy {

    public static $slug = 'classes-days';

    public static $singular_name = 'Class Day';

    public static $plural_name = 'Class Days';

    public static $registered_post_types = array( 'classes' );

    public static function register() { parent::register( NAM_Classes_Days::$slug, NAM_Classes_Days::$singular_name, NAM_Classes_Days::$plural_name, NAM_Classes_Days::$registered_post_types ); }

}

?>
