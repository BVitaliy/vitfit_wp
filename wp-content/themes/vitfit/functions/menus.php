<?php
/**
 * Nav menus as plain trees, so the header / mobile menu / footer templates
 * print the markup's HTML literally (dropdown panel, mobile accordion)
 * instead of fighting a Walker.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Items of the menu at a location, two levels deep, with the current state.
 *
 * @param string $location Theme location.
 * @return array<int, array{title:string,url:string,new_tab:bool,current:bool,children:array}>
 */
function vtf_menu_tree( string $location ): array {
	static $cache = [];

	if ( isset( $cache[ $location ] ) ) {
		return $cache[ $location ];
	}

	$cache[ $location ] = [];
	$locations          = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		return [];
	}

	$items = wp_get_nav_menu_items( $locations[ $location ] );

	if ( ! is_array( $items ) ) {
		return [];
	}

	// Sets current-menu-item / ancestor classes the way wp_nav_menu() does.
	_wp_menu_item_classes_by_context( $items );

	$by_id = [];
	$tree  = [];

	foreach ( $items as $item ) {
		$classes = (array) $item->classes;
		$current = ! is_front_page() && (bool) array_intersect( [ 'current-menu-item', 'current_page_item', 'current-menu-ancestor', 'current-menu-parent' ], $classes );

		$by_id[ (int) $item->ID ] = [
			'title'    => (string) $item->title,
			'url'      => vtf_resolve_anchor( (string) $item->url ),
			'new_tab'  => '_blank' === $item->target,
			'current'  => $current,
			'parent'   => (int) $item->menu_item_parent,
			'children' => [],
		];
	}

	foreach ( $by_id as $id => $item ) {
		if ( $item['parent'] && isset( $by_id[ $item['parent'] ] ) ) {
			$by_id[ $item['parent'] ]['children'][] = $item;
		}
	}

	foreach ( $by_id as $item ) {
		if ( ! $item['parent'] ) {
			$tree[] = $item;
		}
	}

	$cache[ $location ] = $tree;

	return $tree;
}

/**
 * Name of the menu at a location (footer column titles).
 *
 * @param string $location Theme location.
 */
function vtf_menu_name( string $location ): string {
	$locations = get_nav_menu_locations();
	$menu      = ! empty( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : false;

	return $menu ? (string) $menu->name : '';
}

/**
 * Attributes of a menu link: href, target, aria-current, class.
 *
 * @param array  $item  Tree item.
 * @param string $class Link classes.
 */
function vtf_menu_link_attrs( array $item, string $class = '' ): string {
	$class = trim( $class . ( $item['current'] ? ' is-active' : '' ) );
	$html  = '' !== $class ? ' class="' . esc_attr( $class ) . '"' : '';
	$html .= ' href="' . esc_url( $item['url'] ) . '"';

	if ( $item['new_tab'] ) {
		$html .= ' target="_blank" rel="noopener"' . vtf_aria_label( $item['title'], true );
	}

	if ( $item['current'] ) {
		$html .= ' aria-current="page"';
	}

	return $html;
}
