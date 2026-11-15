<?php
/**
 * Page builder ("Конструктор", ACF flexible content `page_builder`).
 *
 * Every layout renders template-parts/blocks/{layout-with-dashes}.php with
 * the row as $args. The first sub-field of every layout is `{layout}_hide`;
 * hidden rows render nothing — not even their spacer, so removing or hiding a
 * block leaves no gap (team checklist #37, #39).
 *
 * Spacing follows the markup's index.php: a `.spacer-xl` after every
 * section, with the exceptions below.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Spacer after a layout, where it differs from `spacer-xl`.
 */
const VTF_LAYOUT_SPACERS = [
	'tapes' => 'spacer-lg',
];

/**
 * Spacer between two particular neighbours ("first>second"), overriding both
 * rules above: the USP tapes sit right under the hero, the flavours follow
 * the fullscreen video without a gap, FAQ → SEO is the markup's spacer-lg.
 */
const VTF_PAIR_SPACERS = [
	'hero>tapes'    => '',
	'reel>flavors'  => '',
	'faq>seo'       => 'spacer-lg',
	'page_top>hero' => '',
];

/**
 * Page whose builder renders on the current view: the queried page, or — on a
 * product page — the shared «Product page blocks» page (Site Settings → Catalog).
 */
function vtf_builder_source_id(): int {
	if ( is_singular( 'vtf_product' ) ) {
		return vtf_product_template_id();
	}

	return is_singular() ? (int) get_queried_object_id() : 0;
}

/**
 * Visible builder rows of a post.
 *
 * @param int $post_id Post ID; defaults to the current view's source (vtf_builder_source_id()).
 * @return array[] Rows with `acf_fc_layout`.
 */
function vtf_builder_rows( int $post_id = 0 ): array {
	static $cache = [];

	$post_id = $post_id ? $post_id : vtf_builder_source_id();

	if ( ! $post_id ) {
		return [];
	}

	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	// Every image / file / post of the page in two queries instead of two per item.
	vtf_prime_posts( vtf_meta_ids( $post_id ) );

	$rows = function_exists( 'get_field' ) ? get_field( 'page_builder', $post_id ) : null;

	$cache[ $post_id ] = array_values(
		array_filter(
			is_array( $rows ) ? $rows : [],
			static function ( $row ) {
				$layout = $row['acf_fc_layout'] ?? '';

				return '' !== $layout && empty( $row[ $layout . '_hide' ] );
			}
		)
	);

	return $cache[ $post_id ];
}

/**
 * Spacer class after a row, given the next one.
 *
 * @param string $layout Current layout.
 * @param string $next   Next layout ('' = the last block on the page).
 */
function vtf_spacer_after( string $layout, string $next ): string {
	$pair = $layout . '>' . $next;

	if ( array_key_exists( $pair, VTF_PAIR_SPACERS ) ) {
		return VTF_PAIR_SPACERS[ $pair ];
	}

	return VTF_LAYOUT_SPACERS[ $layout ] ?? 'spacer-xl';
}

/**
 * Renders the page builder.
 *
 * @param int $post_id Post ID; defaults to the queried object.
 */
function vtf_render_builder( int $post_id = 0 ): void {
	$rows  = vtf_builder_rows( $post_id );
	$count = count( $rows );

	foreach ( $rows as $index => $row ) {
		$layout = $row['acf_fc_layout'];
		$next   = $index + 1 < $count ? $rows[ $index + 1 ]['acf_fc_layout'] : '';

		$row['index'] = $index;

		get_template_part( 'template-parts/blocks/' . str_replace( '_', '-', $layout ), null, $row );

		$spacer = vtf_spacer_after( $layout, $next );

		if ( '' !== $spacer ) {
			printf( '<div class="%s"></div>', esc_attr( $spacer ) );
		}
	}
}

/**
 * Does the current page's builder contain any of these layouts?
 *
 * @param string[] $layouts Layout names.
 */
function vtf_has_layout( array $layouts ): bool {
	$used = array_column( vtf_builder_rows(), 'acf_fc_layout' );

	return (bool) array_intersect( $layouts, $used );
}

/**
 * Header modifier (markup: `$header_class`). Default header everywhere;
 * pages opening with a photo banner get `header--light` (stage "Inner pages").
 */
function vtf_header_class(): string {
	return (string) apply_filters( 'vtf_header_class', '' );
}
