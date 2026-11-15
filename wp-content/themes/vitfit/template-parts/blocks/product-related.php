<?php
/**
 * Block «Product: Try also» (markup: template-parts/product-related.php) — on
 * product pages only: the product's picked «Try also» products, else other
 * products of the catalog (one per flavour first), without the current one.
 * Cards = the home hits; a slider on every width.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_product = vtf_current_product();

if ( ! $vtf_product ) {
	return;
}

$vtf_limit    = (int) ( $args['limit'] ?? 0 );
$vtf_products = vtf_related_products( $vtf_product, $vtf_limit > 0 ? $vtf_limit : 8 );

if ( empty( $vtf_products ) ) {
	return;
}

$vtf_tag = vtf_title_tag( $args, 'h2' );
?>
<section class="hits related">
	<div class="container">
		<div class="section-head">
			<?php if ( ! empty( $args['title'] ) ) { ?>
				<div class="section-head__main">
					<<?php echo esc_html( $vtf_tag ); ?> class="h2 section-head__title"><?php vtf_title_html( (string) $args['title'], (string) ( $args['title_mark'] ?? '' ) ); ?></<?php echo esc_html( $vtf_tag ); ?>>
				</div>
			<?php } ?>
			<div class="section-head__side">
				<div class="slider-nav">
					<button class="btn-icon js-slider-prev" type="button" aria-label="<?php esc_attr_e( 'Previous products', 'vitfit' ); ?>"><svg viewBox="0 0 24 24" style="transform: scaleX(-1)"><use href="#i-arrow" /></svg></button>
					<button class="btn-icon js-slider-next" type="button" aria-label="<?php esc_attr_e( 'Next products', 'vitfit' ); ?>"><?php vtf_sprite( 'i-arrow' ); ?></button>
				</div>
				<?php vtf_link_arrow( $args['link'] ?? null ); ?>
			</div>
		</div>
		<div class="swiper grid-slider related__slider js-slider">
			<div class="swiper-wrapper">
				<?php foreach ( $vtf_products as $vtf_post ) { ?>
					<?php vtf_product_card( $vtf_post->ID, 'swiper-slide' ); ?>
				<?php } ?>
			</div>
			<div class="grid-slider__pagination"></div>
		</div>
	</div>
</section>
