<?php
/**
 * Block «Product cards» (markup: template-parts/home-hits.php — «Хіти сезону»):
 * the picked products in their order, or the first four by «Order».
 * Hover = the moment of consumption (app-product.js).
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_products = vtf_card_posts( 'vtf_product', $args['products'] ?? [], 4 );

if ( empty( $vtf_products ) ) {
	return;
}
?>
<section class="hits">
	<div class="container">
		<?php get_template_part( 'template-parts/global/section-head', null, [ 'row' => $args, 'link' => $args['link'] ?? null ] ); ?>
		<div class="swiper grid-slider hits__slider js-grid-slider" data-slider-below="1200">
			<div class="swiper-wrapper hits__grid">
				<?php foreach ( $vtf_products as $vtf_product ) { ?>
					<?php vtf_product_card( $vtf_product->ID, 'swiper-slide' ); ?>
				<?php } ?>
			</div>
			<div class="grid-slider__pagination"></div>
		</div>
	</div>
</section>
