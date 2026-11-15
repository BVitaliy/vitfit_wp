<?php
/**
 * Block «Catalog categories» (markup: template-parts/catalog-categories.php):
 * the page top (breadcrumbs, title, lead) + category cards. The grid adapts to
 * the number of cards in CSS (threes: one big + two, every second mirrored).
 * Card data: Products → Catalog categories (colour, up to three bottles, the
 * description as the text).
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_terms = vtf_catalog_categories( vtf_ids( $args['categories'] ?? [] ) );
$vtf_title = trim( (string) ( $args['title'] ?? '' ) );
$vtf_title = '' !== $vtf_title ? $vtf_title : (string) get_the_title( vtf_builder_source_id() );
?>
<section class="categories">
	<div class="container">
		<div class="page-top">
			<?php vtf_breadcrumbs(); ?>
			<div class="page-top__row">
				<h1 class="h2 page-top__title"><?php vtf_title_html( $vtf_title ); ?></h1>
				<?php if ( ! empty( $args['lead'] ) ) { ?>
					<p class="text-lg page-top__lead"><?php vtf_br( $args['lead'] ); ?></p>
				<?php } ?>
			</div>
		</div>

		<?php if ( ! empty( $vtf_terms ) ) { ?>
			<div class="categories__grid">
				<?php foreach ( $vtf_terms as $vtf_term ) { ?>
					<?php
					$vtf_packs = array_slice( array_filter( (array) get_field( 'packs', $vtf_term ), static fn( $pack ) => ! empty( $pack['flavor'] ) ), 0, 3 );
					$vtf_color = vtf_flavor_class( get_field( 'color', $vtf_term ) );
					?>
					<a class="cat-card<?php echo esc_attr( $vtf_color ); ?>" href="<?php echo esc_url( get_term_link( $vtf_term ) ); ?>">
						<span class="cat-card__media" aria-hidden="true">
							<span class="cat-card__disc"></span>
							<?php foreach ( array_values( $vtf_packs ) as $vtf_n => $vtf_pack ) { ?>
								<?php
								$vtf_flavor = get_term( (int) $vtf_pack['flavor'], 'vtf_flavor' );
								vtf_bottle(
									[
										'flavor' => $vtf_flavor,
										'pack'   => (string) ( $vtf_pack['pack'] ?? 'pet' ),
										'name'   => $vtf_flavor instanceof WP_Term ? $vtf_flavor->name : '',
										'image'  => $vtf_flavor instanceof WP_Term && ( $vtf_pack['pack'] ?? 'pet' ) === get_field( 'pack', $vtf_flavor ) ? get_field( 'packshot', $vtf_flavor ) : 0,
										'class'  => 'cat-card__pack cat-card__pack--' . ( $vtf_n + 1 ),
									]
								);
								?>
							<?php } ?>
						</span>
						<span class="cat-card__body">
							<span class="h3 cat-card__title"><?php echo esc_html( $vtf_term->name ); ?></span>
							<?php if ( '' !== trim( $vtf_term->description ) ) { ?>
								<span class="text-md cat-card__text"><?php echo esc_html( $vtf_term->description ); ?></span>
							<?php } ?>
						</span>
					</a>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</section>
