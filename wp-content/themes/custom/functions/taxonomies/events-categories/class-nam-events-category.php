<?php

class NAM_Events_Category extends NAM_Taxonomy {

    public static $slug = 'events-categories';

    public static $singular_name = 'Events Category';

    public static $plural_name = 'Events Categories';

    public static $registered_post_types = array( 'events' );

    public static function register() { parent::register( NAM_Events_Category::$slug, NAM_Events_Category::$singular_name, NAM_Events_Category::$plural_name, NAM_Events_Category::$registered_post_types ); }

}

?>
