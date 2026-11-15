<?php
/**
 * Custom post types and taxonomies — all registered here, in one place.
 *
 * - vtf_product      Products (one item = flavour + pack): catalog, product page, cards.
 * - vtf_product_cat  Catalog categories (Класична, Новинки, Набори) → /catalog/{slug}/.
 * - vtf_flavor       Flavours: colour, notes, packshot — the "Flavours" block, filters.
 * - vtf_shop         Where to buy: partner chains / online shops (logo + link).
 * - vtf_shop_type    Shop groups for the "Where to buy" page tabs.
 * - vtf_faq          FAQ questions (title = question, editor = answer).
 * - vtf_review       Product reviews (title = author, editor = text): sent from the review
 *                    popup as «Pending», published by an editor (moderation).
 *
 * Order in lists = “Order” (menu_order) in the item's Page Attributes.
 * Labels are English sources, translated by languages/uk.po.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VTF_Post_Types {

	/**
	 * Types ordered by «Order» in the admin lists as on the site.
	 */
	public const ORDERED = [ 'vtf_product', 'vtf_shop', 'vtf_faq' ];

	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
		add_filter( 'enter_title_here', [ $this, 'title_placeholder' ], 10, 2 );
		add_action( 'pre_get_posts', [ $this, 'admin_order' ] );
	}

	/**
	 * Admin lists of the ordered types follow «Order», as the site does.
	 *
	 * @param WP_Query $query Query.
	 */
	public function admin_order( $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() || ! in_array( $query->get( 'post_type' ), self::ORDERED, true ) || ! empty( $_GET['orderby'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only list sorting.
			return;
		}

		$query->set(
			'orderby',
			[
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			]
		);
	}

	public function register(): void {
		register_taxonomy(
			'vtf_product_cat',
			[ 'vtf_product' ],
			[
				'labels'            => $this->taxonomy_labels( __( 'Catalog categories', 'vitfit' ), __( 'Catalog category', 'vitfit' ) ),
				'public'            => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => false,
				'rewrite'           => [
					'slug'       => 'catalog',
					'with_front' => false,
				],
			]
		);

		register_taxonomy(
			'vtf_flavor',
			[ 'vtf_product' ],
			[
				'labels'            => $this->taxonomy_labels( __( 'Flavours', 'vitfit' ), __( 'Flavour', 'vitfit' ) ),
				'public'            => false,
				'show_ui'           => true,
				'hierarchical'      => true,
				'meta_box_cb'       => false, // picked with the product's ACF «Flavour» field
				'show_admin_column' => true,
				'show_in_rest'      => false,
				'rewrite'           => false,
			]
		);

		register_taxonomy(
			'vtf_shop_type',
			[ 'vtf_shop' ],
			[
				'labels'            => $this->taxonomy_labels( __( 'Shop types', 'vitfit' ), __( 'Shop type', 'vitfit' ) ),
				'public'            => false,
				'show_ui'           => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => false,
				'rewrite'           => false,
			]
		);

		register_post_type(
			'vtf_product',
			[
				'labels'        => $this->type_labels( __( 'Products', 'vitfit' ), __( 'Product', 'vitfit' ), __( 'Add product', 'vitfit' ) ),
				'public'        => true,
				'has_archive'   => false, // the catalog is a page (builder) — stage "Catalog"
				'menu_icon'     => 'dashicons-carrot',
				'menu_position' => 20,
				'supports'      => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
				'show_in_rest'  => false,
				'rewrite'       => [
					'slug'       => 'product',
					'with_front' => false,
				],
			]
		);

		register_post_type(
			'vtf_shop',
			[
				'labels'              => $this->type_labels( __( 'Where to buy', 'vitfit' ), __( 'Shop', 'vitfit' ), __( 'Add shop', 'vitfit' ) ),
				'public'              => false,
				'show_ui'             => true,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-store',
				'menu_position'       => 21,
				'supports'            => [ 'title', 'page-attributes' ],
				'show_in_rest'        => false,
			]
		);

		register_post_type(
			'vtf_review',
			[
				'labels'              => $this->type_labels( __( 'Reviews', 'vitfit' ), __( 'Review', 'vitfit' ), __( 'Add review', 'vitfit' ) ),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type=vtf_product',
				'exclude_from_search' => true,
				'supports'            => [ 'title', 'editor' ],
				'show_in_rest'        => false,
			]
		);

		register_post_type(
			'vtf_faq',
			[
				'labels'              => $this->type_labels( __( 'FAQ', 'vitfit' ), __( 'Question', 'vitfit' ), __( 'Add question', 'vitfit' ) ),
				'public'              => false,
				'show_ui'             => true,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-editor-help',
				'menu_position'       => 22,
				'supports'            => [ 'title', 'editor', 'page-attributes' ],
				'show_in_rest'        => false,
			]
		);
	}

	/**
	 * @param string  $placeholder Default placeholder.
	 * @param WP_Post $post        Post.
	 */
	public function title_placeholder( $placeholder, $post ): string {
		$map = [
			'vtf_product' => __( 'Product name, e.g. Apple & Pear', 'vitfit' ),
			'vtf_shop'    => __( 'Shop name', 'vitfit' ),
			'vtf_faq'     => __( 'Question', 'vitfit' ),
			'vtf_review'  => __( 'Author name', 'vitfit' ),
		];

		return $map[ $post->post_type ] ?? (string) $placeholder;
	}

	/**
	 * @param string $plural   Plural name.
	 * @param string $singular Singular name.
	 * @param string $add      "Add …" label.
	 */
	private function type_labels( string $plural, string $singular, string $add ): array {
		return [
			'name'               => $plural,
			'singular_name'      => $singular,
			'menu_name'          => $plural,
			'add_new'            => $add,
			'add_new_item'       => $add,
			'edit_item'          => $singular,
			'new_item'           => $singular,
			'view_item'          => $singular,
			'all_items'          => $plural,
			'search_items'       => __( 'Search', 'vitfit' ),
			'not_found'          => __( 'Nothing found', 'vitfit' ),
			'not_found_in_trash' => __( 'Nothing found in Trash', 'vitfit' ),
		];
	}

	/**
	 * @param string $plural   Plural name.
	 * @param string $singular Singular name.
	 */
	private function taxonomy_labels( string $plural, string $singular ): array {
		return [
			'name'          => $plural,
			'singular_name' => $singular,
			'menu_name'     => $plural,
			'all_items'     => $plural,
			'edit_item'     => $singular,
			'add_new_item'  => $singular,
			'search_items'  => __( 'Search', 'vitfit' ),
			'not_found'     => __( 'Nothing found', 'vitfit' ),
		];
	}
}

new VTF_Post_Types();
