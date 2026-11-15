<?php
/**
 * FAQ: questions are posts of `vtf_faq` (title = question, editor = answer,
 * «Order» = position). The FAQ block shows the picked ones or all of them.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Questions for a FAQ block.
 *
 * @param array $picked Picked question IDs ([] = all, by «Order»).
 * @param int   $limit  Max questions when automatic (-1 = all).
 * @return array<int, array{question:string,answer:string}>
 */
function vtf_faq_items( array $picked = [], int $limit = -1 ): array {
	$args = [
		'post_type'              => 'vtf_faq',
		'post_status'            => 'publish',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	];

	if ( ! empty( $picked ) ) {
		$args['post__in']       = array_map( 'intval', $picked );
		$args['orderby']        = 'post__in';
		$args['posts_per_page'] = count( $picked );
	} else {
		$args['posts_per_page'] = $limit;
		$args['orderby']        = [
			'menu_order' => 'ASC',
			'ID'         => 'ASC',
		];
	}

	$items = [];

	foreach ( vtf_get_posts( $args ) as $post ) {
		$items[] = [
			'question' => (string) get_the_title( $post ),
			'answer'   => (string) apply_filters( 'the_content', $post->post_content ),
		];
	}

	return $items;
}

/**
 * An answer inside `.faq__inner`: plain paragraphs as in the markup
 * (`<p class="text-md">`), richer editor content (lists, tables, images) in
 * the padded `.faq__content.rich-text` wrapper of the product accordion.
 *
 * @param string $html Answer HTML (the_content).
 */
function vtf_faq_answer( string $html ): void {
	$html = trim( $html );

	if ( '' === $html ) {
		return;
	}

	if ( preg_match( '/<(ul|ol|table|h[1-6]|blockquote|img|figure|iframe)\b/i', $html ) ) {
		echo '<div class="faq__content rich-text text-md">' . wp_kses_post( $html ) . '</div>';

		return;
	}

	echo wp_kses_post( preg_replace( '/<p>/', '<p class="text-md">', $html ) );
}
