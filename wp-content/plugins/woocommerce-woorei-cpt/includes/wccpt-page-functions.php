<?php


/**
 * Fix active class in nav for cpt shop page.
 *
 * @param array $menu_items
 * @return array
 */
function wc_nav_menu_item_classes( $menu_items ) {

	if ( ! is_woocommerce() ) {
		return $menu_items;
	}

	$shop_page 		= (int) wc_get_page_id('shop');
	$page_for_posts = (int) get_option( 'page_for_posts' );

	foreach ( (array) $menu_items as $key => $menu_item ) {

		$classes = (array) $menu_item->classes;

		// Unset active class for blog page
		if ( $page_for_posts == $menu_item->object_id ) {
			$menu_items[$key]->current = false;

			if ( in_array( 'current_page_parent', $classes ) ) {
				unset( $classes[ array_search('current_page_parent', $classes) ] );
			}

			if ( in_array( 'current-menu-item', $classes ) ) {
				unset( $classes[ array_search('current-menu-item', $classes) ] );
			}

		// Set active state if this is the shop page link
		} elseif ( is_shop() && $shop_page == $menu_item->object_id && 'page' === $menu_item->object ) {
			$menu_items[ $key ]->current = true;
			$classes[] = 'current-menu-item';
			$classes[] = 'current_page_item';

		// Set parent state if this is a product page
		} elseif ( is_singular( 'product' ) && $shop_page == $menu_item->object_id ) {
			$classes[] = 'current_page_parent';
		}

		$menu_items[ $key ]->classes = array_unique( $classes );

	}

	return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'wc_nav_menu_item_classes', 2 );