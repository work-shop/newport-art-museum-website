
<?php
if( NAM_Helpers::is_tree(23) || NAM_Helpers::is_tree(30) ): $GLOBALS['tree_slug'] = 'visit'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(40) || is_tax( 'exhibitions-categories' ) ): $GLOBALS['tree_slug'] = 'exhibitions'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(42) ): $GLOBALS['tree_slug'] = 'collection'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(46) ): $GLOBALS['tree_slug'] = 'about'; $GLOBALS['include_page_nav'] = true; endif;
if( is_post_type_archive( 'news' ) || is_tax( 'news-categories' ) ): $GLOBALS['tree_slug'] = 'about'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(76) || is_tax( 'classes-categories' ) ): $GLOBALS['tree_slug'] = 'education'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(74) || is_tax( 'events-categories' ) ): $GLOBALS['tree_slug'] = 'events'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(70) ): $GLOBALS['tree_slug'] = 'join'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(66) ): $GLOBALS['tree_slug'] = 'support'; $GLOBALS['include_page_nav'] = true; endif;
if( NAM_Helpers::is_tree(4) || is_tax( 'products-categories' ) ): $GLOBALS['tree_slug'] = 'shop'; $GLOBALS['include_page_nav'] = true; endif;
?>