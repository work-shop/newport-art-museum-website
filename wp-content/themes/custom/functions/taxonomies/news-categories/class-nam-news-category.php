<?php

class NAM_News_Category extends NAM_Taxonomy {

    public static $slug = 'news-categories';

    public static $singular_name = 'News Category';

    public static $plural_name = 'News Categories';

    public static $registered_post_types = array( 'news' );

    public static function register() { parent::register( NAM_News_Category::$slug, NAM_News_Category::$singular_name, NAM_News_Category::$plural_name, NAM_News_Category::$registered_post_types ); }

}

?>
