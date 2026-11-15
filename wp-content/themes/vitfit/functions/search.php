<?php
/**
 * Search: products + blog posts.
 *
 * - The header overlay suggests while typing (`wp_ajax_vtf_live_search`,
 *   js/app-search.js): products first, then posts, 5 rows at most.
 * - Enter submits ?s= to the search results page (stage "Search").
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post types the site search covers.
 */
const VTF_SEARCH_TYPES = [ 'vtf_product', 'post' ];

class VTF_Search {

	/**
	 * Suggestions in the overlay (markup: SUGGEST_MAX in app-header.js).
	 */
	private const LIVE_LIMIT = 5;

	public function __construct() {
		add_action( 'wp_ajax_vtf_live_search', [ $this, 'live' ] );
		add_action( 'wp_ajax_nopriv_vtf_live_search', [ $this, 'live' ] );
		add_action( 'pre_get_posts', [ $this, 'main_query' ] );
	}

	/**
	 * The search page lists products and posts only, by relevance — set
	 * explicitly: rspanel would sort by menu_order.
	 *
	 * @param WP_Query $query Query.
	 */
	public function main_query( $query ): void {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
			return;
		}

		$query->set( 'post_type', VTF_SEARCH_TYPES );
		$query->set( 'orderby', 'relevance' );
		$query->set( 'order', 'DESC' );
	}

	/**
	 * AJAX: suggestion rows for the overlay.
	 */
	public function live(): void {
		check_ajax_referer( 'vtf_nonce', 'nonce' );

		$term = sanitize_text_field( wp_unslash( $_POST['s'] ?? '' ) );

		if ( mb_strlen( $term ) < 2 ) {
			wp_send_json_success( [ 'html' => '' ] );
		}

		$posts = [];

		foreach ( VTF_SEARCH_TYPES as $type ) {
			$left = self::LIVE_LIMIT - count( $posts );

			if ( $left < 1 ) {
				break;
			}

			$posts = array_merge(
				$posts,
				vtf_get_posts(
					[
						'post_type'      => $type,
						'post_status'    => 'publish',
						's'              => $term,
						'posts_per_page' => $left,
						'orderby'        => 'relevance',
						'order'          => 'DESC',
						'no_found_rows'  => true,
					]
				)
			);
		}

		ob_start();

		foreach ( $posts as $post ) {
			get_template_part( 'template-parts/global/search-result', null, [ 'post' => $post ] );
		}

		wp_send_json_success( [ 'html' => (string) ob_get_clean() ] );
	}
}

new VTF_Search();
