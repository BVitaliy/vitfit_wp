<?php
/**
 * Block «Benefits» (markup: template-parts/home-benefits.php): bento — one
 * photo card + tiles. No hover on purpose (manager). ≥1200 grid, below Swiper.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_big   = (array) ( $args['big'] ?? [] );
$vtf_items = array_filter( (array) ( $args['items'] ?? [] ), static fn( $item ) => ! empty( $item['title'] ) );
$vtf_photo = vtf_image_id( $vtf_big['image'] ?? 0 );

if ( ! $vtf_photo && empty( $vtf_items ) ) {
	return;
}
?>
<section class="benefits">
	<div class="container">
		<?php get_template_part( 'template-parts/global/section-head', null, [ 'row' => $args, 'style' => '--title-w: 9em', 'text' => $args['text'] ?? '' ] ); ?>

		<div class="swiper grid-slider benefits__slider js-grid-slider" data-slider-below="1200">
			<div class="swiper-wrapper benefits__grid">
				<?php if ( $vtf_photo ) { ?>
					<article class="swiper-slide benefit benefit--big<?php echo esc_attr( vtf_flavor_class( $vtf_big['color'] ?? '' ) ); ?>">
						<?php vtf_picture( $vtf_photo, $vtf_big['image_mobile'] ?? null, [ 'alt' => (string) get_post_meta( $vtf_photo, '_wp_attachment_image_alt', true ) ], 'large', [ 'class' => 'benefit__photo' ] ); ?>
						<div class="benefit__body">
							<?php if ( ! empty( $vtf_big['title'] ) ) { ?>
								<h3 class="h4"><?php echo esc_html( $vtf_big['title'] ); ?></h3>
							<?php } ?>
							<?php if ( ! empty( $vtf_big['text'] ) ) { ?>
								<p class="text-md"><?php vtf_br( $vtf_big['text'] ); ?></p>
							<?php } ?>
						</div>
					</article>
				<?php } ?>
				<?php foreach ( $vtf_items as $vtf_item ) { ?>
					<article class="swiper-slide benefit<?php echo esc_attr( vtf_flavor_class( $vtf_item['color'] ?? '' ) ); ?>">
						<?php if ( ! empty( $vtf_item['icon'] ) ) { ?>
							<span class="benefit__icon"><?php vtf_feature_icon( $vtf_item['icon'] ); ?></span>
						<?php } ?>
						<h3 class="h5"><?php echo esc_html( $vtf_item['title'] ); ?></h3>
						<?php if ( ! empty( $vtf_item['text'] ) ) { ?>
							<p class="text-md"><?php vtf_br( $vtf_item['text'] ); ?></p>
						<?php } ?>
					</article>
				<?php } ?>
			</div>
			<div class="grid-slider__pagination"></div>
		</div>
	</div>
</section>
