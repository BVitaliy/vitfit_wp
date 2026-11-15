<?php
/**
 * Products and flavours: colours, packs, the bottle.
 *
 * A flavour (vtf_flavor term) carries the colours of everything drawn in it:
 * a preset from scss/style/_flavors.scss (`.fl--{slug}`) or, for a new
 * flavour, its own colours printed as the same CSS variables inline.
 *
 * The bottle is the markup's SVG placeholder recoloured per flavour; once the
 * client's packshot is uploaded (product → «Packshot», flavour → «Packshot»)
 * the photo replaces it.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Colour presets — the keys of $flavors in scss/style/_flavors.scss.
 */
const VTF_FLAVOR_PRESETS = [ 'original', 'apple-pear', 'sweet-cherry', 'berry', 'elderberry', 'strawberry-lime', 'grape-apple', 'mojito', 'gold-apricot' ];

/**
 * Packs: SVG symbol in the sprite + bottle modifier.
 */
const VTF_PACKS = [
	'pet'   => [
		'symbol'   => 'bottle',
		'modifier' => '',
	],
	'can'   => [
		'symbol'   => 'can',
		'modifier' => 'bottle--can',
	],
	'glass' => [
		'symbol'   => 'glass',
		'modifier' => 'bottle--glass',
	],
];

/**
 * Translated pack names.
 *
 * @param string $pack Pack key.
 */
function vtf_pack_label( string $pack ): string {
	$labels = [
		'pet'   => __( 'PET', 'vitfit' ),
		'can'   => __( 'Can', 'vitfit' ),
		'glass' => __( 'Glass', 'vitfit' ),
	];

	return $labels[ $pack ] ?? '';
}

/**
 * Flavour of a product (first vtf_flavor term), cached.
 *
 * @param int $product_id Product ID.
 */
function vtf_product_flavor( int $product_id ): ?WP_Term {
	$terms = get_the_terms( $product_id, 'vtf_flavor' );

	return is_array( $terms ) && ! empty( $terms ) ? $terms[0] : null;
}

/**
 * Colour data of a flavour: preset class or own colours.
 *
 * @param WP_Term|int|null $term Flavour term.
 * @return array{class:string,style:string,slug:string}
 */
function vtf_flavor_colors( $term ): array {
	static $cache = [];

	$term = is_numeric( $term ) ? get_term( (int) $term, 'vtf_flavor' ) : $term;

	if ( ! $term instanceof WP_Term ) {
		return [
			'class' => '',
			'style' => '',
			'slug'  => '',
		];
	}

	if ( isset( $cache[ $term->term_id ] ) ) {
		return $cache[ $term->term_id ];
	}

	$preset = function_exists( 'get_field' ) ? (string) get_field( 'color', $term ) : '';
	$data   = [
		'class' => '',
		'style' => '',
		'slug'  => '',
	];

	if ( in_array( $preset, VTF_FLAVOR_PRESETS, true ) ) {
		$data['class'] = 'fl--' . $preset;
		$data['slug']  = $preset;
	} elseif ( 'custom' === $preset ) {
		$vars = [];
		$map  = [
			'--fl'       => 'color_main',
			'--fl-l'     => 'color_light',
			'--b-liquid' => 'color_liquid',
			'--b-label'  => 'color_label',
			'--b-fl'     => 'color_main',
			'--b-leaf'   => 'color_leaf',
		];

		foreach ( $map as $var => $field ) {
			$value = sanitize_hex_color( (string) get_field( $field, $term ) );

			if ( $value ) {
				$vars[] = $var . ':' . $value;
			}
		}

		$data['style'] = implode( ';', $vars );
	}

	$cache[ $term->term_id ] = $data;

	return $data;
}

/**
 * ` class="… fl--slug" style="--fl:…"` for an element coloured by a flavour.
 *
 * @param string           $class Base classes.
 * @param WP_Term|int|null $term  Flavour term.
 * @param string           $style Extra inline style.
 */
function vtf_flavor_attrs( string $class, $term, string $style = '' ): string {
	$colors = vtf_flavor_colors( $term );
	$class  = trim( $class . ' ' . $colors['class'] );
	$style  = trim( $colors['style'] . ( '' !== $colors['style'] && '' !== $style ? ';' : '' ) . $style );

	return ' class="' . esc_attr( $class ) . '"' . ( '' !== $style ? ' style="' . esc_attr( $style ) . '"' : '' );
}

/**
 * The flavour select (preset slug) used by blocks for a fixed colour:
 * ` fl--slug` or '' — append to a class list.
 *
 * @param mixed $slug Preset slug from a block field.
 */
function vtf_flavor_class( $slug ): string {
	return in_array( (string) $slug, VTF_FLAVOR_PRESETS, true ) ? ' fl--' . $slug : '';
}

/**
 * The bottle: the packshot photo when there is one, else the SVG placeholder
 * of the pack in the flavour's colours with the name on the label.
 *
 * @param array $args {
 *     @type WP_Term|int|null $flavor   Flavour term.
 *     @type string           $pack     pet | can | glass.
 *     @type string           $name     Name on the label (SVG only; '' = none).
 *     @type mixed            $image    Packshot (ACF image / ID).
 *     @type string           $class    Extra classes, e.g. 'product__bottle'.
 *     @type string           $size     Image size for the packshot.
 *     @type bool             $eager    Load the packshot eagerly (first screen).
 * }
 */
function vtf_bottle( array $args ): void {
	$pack  = isset( VTF_PACKS[ $args['pack'] ?? '' ] ) ? $args['pack'] : 'pet';
	$image = vtf_image_id( $args['image'] ?? 0 );
	$class = trim( 'bottle ' . VTF_PACKS[ $pack ]['modifier'] . ' ' . ( $args['class'] ?? '' ) . ( $image ? ' bottle--img' : '' ) );
	$name  = (string) ( $args['name'] ?? '' );
	?>
	<span<?php echo vtf_flavor_attrs( $class, $args['flavor'] ?? null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>>
		<?php if ( $image ) { ?>
			<?php
			vtf_image(
				$image,
				[
					'alt'     => $name,
					'loading' => ! empty( $args['eager'] ) ? 'eager' : 'lazy',
				],
				(string) ( $args['size'] ?? 'large' )
			);
			?>
		<?php } else { ?>
			<svg viewBox="0 0 200 470"><use href="#<?php echo esc_attr( VTF_PACKS[ $pack ]['symbol'] ); ?>" /></svg><?php if ( '' !== $name ) { ?><span class="bottle__name"><?php echo esc_html( $name ); ?></span><?php } ?>
		<?php } ?>
	</span>
	<?php
}

/**
 * A product's card data in one place (cards, search results, hero tags…).
 *
 * @param int $product_id Product ID.
 * @return array{id:int,name:string,url:string,flavor:?WP_Term,pack:string,volume:string,pack_size:string,desc:string,badge:string,packshot:int,hover_type:string,hover_image:int,hover_video:string,hover_poster:int}
 */
function vtf_product_data( int $product_id ): array {
	static $cache = [];

	if ( isset( $cache[ $product_id ] ) ) {
		return $cache[ $product_id ];
	}

	$field = static function ( string $name ) use ( $product_id ) {
		return function_exists( 'get_field' ) ? get_field( $name, $product_id ) : null;
	};

	$cache[ $product_id ] = [
		'id'           => $product_id,
		'name'         => (string) get_the_title( $product_id ),
		'url'          => (string) get_permalink( $product_id ),
		'flavor'       => vtf_product_flavor( $product_id ),
		'pack'         => (string) $field( 'pack' ),
		'volume'       => (string) $field( 'volume' ),
		'pack_size'    => (string) $field( 'pack_size' ),
		'desc'         => (string) $field( 'short_description' ),
		'badge'        => (string) $field( 'badge' ),
		'packshot'     => vtf_image_id( $field( 'packshot' ) ),
		'hover_type'   => (string) $field( 'hover_type' ),
		'hover_image'  => vtf_image_id( $field( 'hover_image' ) ),
		'hover_video'  => (string) $field( 'hover_video' ),
		'hover_poster' => vtf_image_id( $field( 'hover_poster' ) ),
	];

	return $cache[ $product_id ];
}

/**
 * "ПЕТ · 0,5 л" for a product.
 *
 * @param array $product From vtf_product_data().
 */
function vtf_product_format( array $product ): string {
	return implode( ' · ', array_filter( [ vtf_pack_label( $product['pack'] ), $product['volume'] ] ) );
}

/**
 * Formats a flavour comes in: "ПЕТ 0,5 · Банка 0,33" — from its published
 * products, unless the flavour sets its own text.
 *
 * @param WP_Term $term Flavour term.
 */
function vtf_flavor_formats( WP_Term $term ): string {
	$own = function_exists( 'get_field' ) ? trim( (string) get_field( 'formats', $term ) ) : '';

	if ( '' !== $own ) {
		return $own;
	}

	$list = [];

	foreach ( vtf_flavor_products( $term->term_id ) as $product_id ) {
		$product = vtf_product_data( $product_id );
		$label   = trim( vtf_pack_label( $product['pack'] ) . ' ' . preg_replace( '/\s*л$/u', '', $product['volume'] ) );

		if ( '' !== $label ) {
			$list[ $label ] = $label;
		}
	}

	return implode( ' · ', $list );
}

/**
 * Published product IDs of a flavour, by «Order».
 *
 * @param int $term_id Flavour term ID.
 * @return int[]
 */
function vtf_flavor_products( int $term_id ): array {
	static $cache = [];

	if ( ! isset( $cache[ $term_id ] ) ) {
		$query = new WP_Query(
			[
				'post_type'              => 'vtf_product',
				'post_status'            => 'publish',
				'posts_per_page'         => 20,
				'fields'                 => 'ids',
				'orderby'                => [
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				],
				'no_found_rows'          => true,
				'update_post_term_cache' => false,
				'tax_query'              => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small catalog.
					[
						'taxonomy' => 'vtf_flavor',
						'terms'    => $term_id,
					],
				],
			]
		);

		$cache[ $term_id ] = array_map( 'intval', $query->posts );
	}

	return $cache[ $term_id ];
}

/**
 * Link of a flavour: its «Product page» field, else its first product.
 *
 * @param WP_Term $term Flavour term.
 */
function vtf_flavor_url( WP_Term $term ): string {
	$product = function_exists( 'get_field' ) ? (int) get_field( 'product', $term ) : 0;

	if ( ! $product ) {
		$products = vtf_flavor_products( $term->term_id );
		$product  = $products[0] ?? 0;
	}

	return $product && 'publish' === get_post_status( $product ) ? (string) get_permalink( $product ) : '';
}

/**
 * Flavour terms in their admin order (term_order via «Order» field).
 *
 * @param int[] $ids Picked term IDs ([] = all).
 * @return WP_Term[]
 */
function vtf_flavors( array $ids = [] ): array {
	$args = [
		'taxonomy'   => 'vtf_flavor',
		'hide_empty' => false,
		'meta_key'   => 'order', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- a dozen terms.
		'orderby'    => 'meta_value_num',
		'order'      => 'ASC',
	];

	if ( ! empty( $ids ) ) {
		$args['include'] = array_map( 'intval', $ids );
		$args['orderby'] = 'include';
		unset( $args['meta_key'] );
	}

	$terms = get_terms( $args );

	return is_array( $terms ) ? $terms : [];
}

/**
 * The bottle of a flavour (popups, hero, the flavours block): the flavour's
 * packshot or the PET placeholder in its colours.
 *
 * @param WP_Term|int|null $term  Flavour term.
 * @param string           $class Extra classes, e.g. 'modal__bottle'.
 * @param bool             $name  Print the name on the label.
 * @param bool             $eager Load the packshot eagerly.
 */
function vtf_flavor_bottle( $term, string $class = '', bool $name = true, bool $eager = false ): void {
	$term = is_numeric( $term ) ? get_term( (int) $term, 'vtf_flavor' ) : $term;

	if ( ! $term instanceof WP_Term ) {
		return;
	}

	vtf_bottle(
		[
			'flavor' => $term,
			'pack'   => function_exists( 'get_field' ) ? (string) get_field( 'pack', $term ) : 'pet',
			'name'   => $name ? $term->name : '',
			'image'  => function_exists( 'get_field' ) ? get_field( 'packshot', $term ) : 0,
			'class'  => $class,
			'size'   => 'large',
			'eager'  => $eager,
		]
	);
}
