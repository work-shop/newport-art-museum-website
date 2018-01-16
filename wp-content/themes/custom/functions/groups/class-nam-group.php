<?php

class NAM_Group extends NAM_Taxonomy {

    public static $slug = 'group';

    public static $singular_name = 'Group';

    public static $plural_name = 'Groups';

    public static $registered_post_types = array( 'user', 'exhibitions', 'classes', 'donation-tiers', 'membership-tiers', 'shop-products' );

    public static function register() { parent::register( NAM_Group::$slug, NAM_Group::$singular_name, NAM_Group::$plural_name, NAM_Group::$registered_post_types ); }

}

?>
