<?php
/**
 * Card lists for the blocks: picked posts in the editor's order, or all /
 * latest ones — always in one query with only the caches the cards use.
 *
 * rspanel (corporate plugin) forces `menu_order` on post / page queries that
 * carry `suppress_filters` (get_posts() always does) — so lists use
 * WP_Query directly and set their order explicitly.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP_Query posts without the `suppress_filters` key (see above).
 *
 * @param array $args Query args.
 * @return WP_Post[]
 */
function vtf_get_posts( array $args ): array {
	$query = new WP_Query( $args );

	return $query->posts;
}

/**
 * Posts for a card block.
 *
 * @param string $post_type Post type.
 * @param mixed  $picked    Relationship value (IDs); empty = automatic.
 * @param int    $limit     Max items when automatic (-1 = all).
 * @return WP_Post[]
 */
function vtf_card_posts( string $post_type, $picked, int $limit = -1 ): array {
	$args = [
		'post_type'           => $post_type,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	];

	if ( ! empty( $picked ) && is_array( $picked ) ) {
		$args['post__in']       = array_map( 'intval', $picked );
		$args['orderby']        = 'post__in';
		$args['posts_per_page'] = count( $picked );
	} elseif ( 'post' === $post_type ) {
		$args['posts_per_page'] = $limit;
		$args['orderby']        = 'date';
		$args['order']          = 'DESC';
	} else {
		$args['posts_per_page'] = $limit;
		$args['orderby']        = [
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		];
	}

	// Blog cards print no terms; products read their flavour (terms) and fields (meta).
	if ( 'post' === $post_type ) {
		$args['update_post_term_cache'] = false;
	}

	$posts = vtf_get_posts( $args );

	vtf_prime_posts( array_map( 'get_post_thumbnail_id', $posts ) );

	return $posts;
}

/**
 * Product card (`.product`) — template-parts/cards/product.php.
 *
 * @param int    $product_id Product ID.
 * @param string $class      Extra classes, e.g. 'swiper-slide'.
 */
function vtf_product_card( int $product_id, string $class = '' ): void {
	get_template_part(
		'template-parts/cards/product',
		null,
		[
			'product' => vtf_product_data( $product_id ),
			'class'   => $class,
		]
	);
}

/**
 * Blog card (`.post`) — template-parts/cards/post.php.
 *
 * @param WP_Post $post  Post.
 * @param string  $class Extra classes, e.g. 'swiper-slide'.
 */
function vtf_post_card( WP_Post $post, string $class = '' ): void {
	get_template_part(
		'template-parts/cards/post',
		null,
		[
			'post'  => $post,
			'class' => $class,
		]
	);
}

/**
 * Post date as in the design: "15 жовтня 2026" (genitive month in Ukrainian —
 * WordPress' uk locale gives it for the 'j F Y' format).
 *
 * @param WP_Post $post Post.
 */
function vtf_post_date( WP_Post $post ): string {
	return (string) get_the_date( 'j F Y', $post );
}

/**
 * Colour of a blog card: the post's «Card colour» (preset slug).
 *
 * @param WP_Post $post Post.
 */
function vtf_post_flavor( WP_Post $post ): string {
	return function_exists( 'get_field' ) ? (string) get_field( 'card_color', $post->ID ) : '';
}
