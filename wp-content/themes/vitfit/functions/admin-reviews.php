<?php
/**
 * Admin list of reviews (Products → Reviews): rating and product columns,
 * pending reviews counted in the menu so moderation is not missed.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VTF_Admin_Reviews {

	public function __construct() {
		add_filter( 'manage_vtf_review_posts_columns', [ $this, 'columns' ] );
		add_action( 'manage_vtf_review_posts_custom_column', [ $this, 'column' ], 10, 2 );
		add_action( 'admin_menu', [ $this, 'pending_count' ], 99 );
	}

	/**
	 * @param array $columns Columns.
	 */
	public function columns( array $columns ): array {
		$date = $columns['date'] ?? '';
		unset( $columns['date'] );

		$columns['vtf_rating']  = __( 'Rating', 'vitfit' );
		$columns['vtf_product'] = __( 'Product', 'vitfit' );
		$columns['date']        = $date;

		return $columns;
	}

	/**
	 * @param string $column  Column.
	 * @param int    $post_id Review ID.
	 */
	public function column( string $column, int $post_id ): void {
		if ( 'vtf_rating' === $column ) {
			echo esc_html( str_repeat( '★', min( 5, max( 0, (int) get_post_meta( $post_id, 'rating', true ) ) ) ) );
		} elseif ( 'vtf_product' === $column ) {
			$product = (int) get_post_meta( $post_id, 'product', true );

			if ( $product ) {
				printf( '<a href="%s">%s</a> <span style="color:#787c82">%s</span>', esc_url( (string) get_edit_post_link( $product ) ), esc_html( get_the_title( $product ) ), esc_html( vtf_product_format( vtf_product_data( $product ) ) ) );
			}
		}
	}

	/**
	 * «Reviews 3» bubble in the Products menu.
	 */
	public function pending_count(): void {
		global $submenu;

		$pending = (int) wp_count_posts( 'vtf_review' )->pending;
		$parent  = 'edit.php?post_type=vtf_product';

		if ( ! $pending || empty( $submenu[ $parent ] ) ) {
			return;
		}

		foreach ( $submenu[ $parent ] as &$item ) {
			if ( 'edit.php?post_type=vtf_review' === $item[2] ) {
				$item[0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $pending . '</span></span>';
			}
		}
	}
}

new VTF_Admin_Reviews();
