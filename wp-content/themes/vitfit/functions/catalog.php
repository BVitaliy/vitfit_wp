<?php
/**
 * Catalog: category lists, the product page data, reviews and ratings.
 *
 * The catalog is small (flavours × packs), so a category page prints all of
 * its products and js/app-catalog.js filters / sorts / pages them in the
 * browser exactly as in the markup (?pack=can from the footer pre-filters).
 *
 * The product page = its own top (template-parts/product/hero.php) + the
 * blocks of one shared builder page (Site Settings → Catalog → «Product page
 * blocks»), so reviews / where to buy / FAQ / the question form / «Try also»
 * are arranged once for every product.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Products of a catalog category (all published, by «Order»).
 *
 * @param int $term_id Category term ID.
 * @return WP_Post[]
 */
function vtf_category_products( int $term_id ): array {
	$posts = vtf_get_posts(
		[
			'post_type'      => 'vtf_product',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'no_found_rows'  => true,
			'orderby'        => [
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			],
			'tax_query'      => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small catalog.
				[
					'taxonomy' => 'vtf_product_cat',
					'terms'    => $term_id,
				],
			],
		]
	);

	vtf_prime_posts( array_merge( array_map( 'get_post_thumbnail_id', $posts ), array_map( static fn( $post ) => (int) get_post_meta( $post->ID, 'packshot', true ), $posts ), array_map( static fn( $post ) => (int) get_post_meta( $post->ID, 'hover_image', true ), $posts ) ) );

	return $posts;
}

/**
 * Catalog categories for the categories block: picked ones, or all top-level
 * ones by «Order».
 *
 * @param int[] $ids Picked term IDs.
 * @return WP_Term[]
 */
function vtf_catalog_categories( array $ids = [] ): array {
	$args = [
		'taxonomy'   => 'vtf_product_cat',
		'hide_empty' => false,
	];

	if ( ! empty( $ids ) ) {
		$args['include'] = $ids;
		$args['orderby'] = 'include';
	} else {
		$args['parent']   = 0;
		$args['meta_key'] = 'order'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- a few terms.
		$args['orderby']  = 'meta_value_num';
		$args['order']    = 'ASC';
	}

	$terms = get_terms( $args );

	return is_array( $terms ) ? $terms : [];
}

/**
 * Volume as the filter value: "0,5 л" → "0.5".
 *
 * @param string $volume Volume text.
 */
function vtf_volume_value( string $volume ): string {
	return preg_match( '/(\d+(?:[.,]\d+)?)/', $volume, $match ) ? str_replace( ',', '.', $match[1] ) : '';
}

/**
 * Translated count word: 1 товар / 2 товари / 5 товарів.
 *
 * @param int    $count Number.
 * @param string $type  'products' | 'reviews'.
 */
function vtf_count_word( int $count, string $type = 'products' ): string {
	if ( 'reviews' === $type ) {
		return _n( 'review', 'reviews', $count, 'vitfit' );
	}

	return _n( 'product', 'products', $count, 'vitfit' );
}

/**
 * The three plural forms of the product word for app-catalog.js (the count
 * changes in the browser): 1 / 2 / 5.
 *
 * @return string[]
 */
function vtf_product_words(): array {
	return [ vtf_count_word( 1 ), vtf_count_word( 2 ), vtf_count_word( 5 ) ];
}

/**
 * Rows of a product page repeater, else Site Settings → Catalog defaults.
 * `{flavor}` in default texts = the product's flavour name.
 *
 * @param int    $product_id Product ID.
 * @param string $field      Product field: specs | perks | details.
 * @return array[]
 */
function vtf_product_rows( int $product_id, string $field ): array {
	$rows = get_field( $field, $product_id );

	if ( empty( $rows ) || ! is_array( $rows ) ) {
		$rows   = vtf_option( 'product_' . $field );
		$rows   = is_array( $rows ) ? $rows : [];
		$flavor = vtf_product_flavor( $product_id );
		$name   = $flavor ? $flavor->name : (string) get_the_title( $product_id );

		foreach ( $rows as &$row ) {
			foreach ( $row as &$value ) {
				if ( is_string( $value ) ) {
					$value = str_replace( '{flavor}', $name, $value );
				}
			}
		}
		unset( $row, $value );
	}

	return $rows;
}

/**
 * Published reviews of a product (newest first).
 *
 * @param int $product_id Product ID.
 * @return WP_Post[]
 */
function vtf_product_reviews( int $product_id ): array {
	static $cache = [];

	if ( ! isset( $cache[ $product_id ] ) ) {
		$cache[ $product_id ] = vtf_get_posts(
			[
				'post_type'              => 'vtf_review',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'orderby'                => 'date',
				'order'                  => 'DESC',
				'meta_query'             => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- reviews of one product.
					[
						'key'   => 'product',
						'value' => $product_id,
					],
				],
			]
		);
	}

	return $cache[ $product_id ];
}

/**
 * Rating of a product from its published reviews.
 *
 * @param int $product_id Product ID.
 * @return array{average:float,count:int,stars:array<int,int>}
 */
function vtf_product_rating( int $product_id ): array {
	$stars = [ 5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0 ];
	$sum   = 0;

	foreach ( vtf_product_reviews( $product_id ) as $review ) {
		$rate = min( 5, max( 1, (int) get_post_meta( $review->ID, 'rating', true ) ) );
		++$stars[ $rate ];
		$sum += $rate;
	}

	$count = array_sum( $stars );

	return [
		'average' => $count ? round( $sum / $count, 1 ) : 0.0,
		'count'   => $count,
		'stars'   => $stars,
	];
}

/**
 * Rating as the design prints it: "4,8" in Ukrainian, "4.8" in English.
 *
 * @param float $value Rating.
 */
function vtf_rating_text( float $value ): string {
	return number_format_i18n( $value, 1 );
}

/**
 * `<span class="stars" style="--rate: 96%" role="img" aria-label="…">`.
 *
 * @param float $value Rating 0–5.
 */
function vtf_stars( float $value ): void {
	/* translators: %s: rating, e.g. 4,8 */
	$label = sprintf( __( 'Rating %s out of 5', 'vitfit' ), vtf_rating_text( $value ) );

	printf( '<span class="stars" style="--rate: %s%%" role="img" aria-label="%s"></span>', esc_attr( (string) round( $value * 20 ) ), esc_attr( $label ) );
}

/**
 * The product shown on the current page (product singles only).
 */
function vtf_current_product(): int {
	return is_singular( 'vtf_product' ) ? (int) get_queried_object_id() : 0;
}

/**
 * Page whose builder blocks are shown on every product page.
 */
function vtf_product_template_id(): int {
	$id = (int) vtf_option( 'product_template' );

	return $id && get_post( $id ) ? $id : 0;
}

/**
 * Catalog page (Site Settings → Catalog).
 */
function vtf_catalog_page_id(): int {
	$id = (int) vtf_option( 'catalog_page' );

	return $id && 'publish' === get_post_status( $id ) ? $id : 0;
}

/**
 * Main catalog category of a product (the first one).
 *
 * @param int $product_id Product ID.
 */
function vtf_product_category( int $product_id ): ?WP_Term {
	$terms = get_the_terms( $product_id, 'vtf_product_cat' );

	return is_array( $terms ) && ! empty( $terms ) ? $terms[0] : null;
}

/**
 * Products for «Try also»: the picked ones, else other products (other
 * flavours first), without the current one.
 *
 * @param int $product_id Product ID.
 * @param int $limit      Max products.
 * @return WP_Post[]
 */
function vtf_related_products( int $product_id, int $limit = 8 ): array {
	$picked = vtf_ids( get_field( 'related', $product_id ) );

	if ( ! empty( $picked ) ) {
		return vtf_card_posts( 'vtf_product', array_diff( $picked, [ $product_id ] ) );
	}

	$flavor = vtf_product_flavor( $product_id );
	$posts  = vtf_get_posts(
		[
			'post_type'      => 'vtf_product',
			'post_status'    => 'publish',
			'posts_per_page' => $limit + 6,
			'post__not_in'   => [ $product_id ],
			'no_found_rows'  => true,
			'orderby'        => [
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			],
		]
	);

	// One card per flavour first (no "the same drink in a can" next to it), then the rest.
	$seen  = $flavor ? [ $flavor->term_id => true ] : [];
	$first = [];
	$rest  = [];

	foreach ( $posts as $post ) {
		$term = vtf_product_flavor( $post->ID );
		$id   = $term ? $term->term_id : 0;

		if ( $id && empty( $seen[ $id ] ) ) {
			$seen[ $id ] = true;
			$first[]     = $post;
		} else {
			$rest[] = $post;
		}
	}

	return array_slice( array_merge( $first, $rest ), 0, $limit );
}
