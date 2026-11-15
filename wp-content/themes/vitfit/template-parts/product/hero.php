<?php
/**
 * Product top (markup: template-parts/product-hero.php): sticky gallery
 * (photos → packshots → video; Swiper + thumbs + Fancybox, js/app-pdp.js) and
 * the info column: rating (published reviews → #reviews), lead,
 * characteristics, taste profile (the flavour's), perks, the «About the
 * product» accordion, actions. Empty groups are left out.
 *
 * Without photos / packshots the gallery shows the drawn bottle three times
 * (front / turned / label), as in the markup.
 *
 * @package vitfit
 *
 * @var array $args { product: vtf_product_data() }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_product = (array) ( $args['product'] ?? [] );

if ( empty( $vtf_product['id'] ) ) {
	return;
}

$vtf_id      = (int) $vtf_product['id'];
$vtf_flavor  = $vtf_product['flavor'];
$vtf_rating  = vtf_product_rating( $vtf_id );
$vtf_lead    = trim( (string) get_field( 'lead', $vtf_id ) ) ?: $vtf_product['desc'];
$vtf_specs   = array_filter( vtf_product_rows( $vtf_id, 'specs' ), static fn( $row ) => '' !== trim( (string) ( $row['value'] ?? '' ) ) );
$vtf_perks   = array_filter( vtf_product_rows( $vtf_id, 'perks' ), static fn( $row ) => '' !== trim( (string) ( $row['text'] ?? '' ) ) );
$vtf_details = array_filter( vtf_product_rows( $vtf_id, 'details' ), static fn( $row ) => '' !== trim( (string) ( $row['title'] ?? '' ) ) );
$vtf_taste   = $vtf_flavor ? array_filter( (array) get_field( 'taste', $vtf_flavor ), static fn( $row ) => ! empty( $row['label'] ) ) : [];
$vtf_photos  = (array) get_field( 'gallery_photos', $vtf_id );
$vtf_packs   = (array) get_field( 'gallery_packs', $vtf_id );
$vtf_video   = (string) get_field( 'video', $vtf_id );
$vtf_poster  = vtf_image_id( get_field( 'video_poster', $vtf_id ) );
$vtf_name    = $vtf_product['name'];

// Slides: [ type, image ID / bottle modifier, zoom URL ].
$vtf_slides = [];

foreach ( array_filter( $vtf_photos ) as $vtf_image ) {
	$vtf_slides[] = [ 'photo', vtf_image_id( $vtf_image ), vtf_image_url( $vtf_image ) ];
}

if ( ! empty( array_filter( $vtf_packs ) ) ) {
	foreach ( array_filter( $vtf_packs ) as $vtf_image ) {
		$vtf_slides[] = [ 'pack', vtf_image_id( $vtf_image ), vtf_image_url( $vtf_image ) ];
	}
} elseif ( $vtf_product['packshot'] ) {
	$vtf_slides[] = [ 'pack', $vtf_product['packshot'], vtf_image_url( $vtf_product['packshot'] ) ];
} else {
	foreach ( [ '', 'pdp__bottle--turn', 'pdp__bottle--label' ] as $vtf_modifier ) {
		$vtf_slides[] = [ 'svg', $vtf_modifier, '' ];
	}
}

$vtf_bottle = static function ( string $class, int $image = 0, bool $name = true ) use ( $vtf_product, $vtf_flavor, $vtf_name ): void {
	vtf_bottle(
		[
			'flavor' => $vtf_flavor,
			'pack'   => $vtf_product['pack'],
			'name'   => $name ? $vtf_name : '',
			'image'  => $image,
			'class'  => $class,
			'size'   => 'large',
			'eager'  => true,
		]
	);
};
?>
<section class="pdp js-pdp">
	<div class="container">
		<?php vtf_breadcrumbs( 'pdp__crumbs' ); ?>

		<div class="pdp__grid">
			<div class="pdp__gallery">
				<div class="swiper pdp__main js-pdp-main">
					<div class="swiper-wrapper">
						<?php foreach ( $vtf_slides as $vtf_n => [ $vtf_type, $vtf_item, $vtf_zoom ] ) { ?>
							<?php if ( 'photo' === $vtf_type ) { ?>
								<div class="swiper-slide pdp__slide pdp__slide--photo" data-zoom="<?php echo esc_url( $vtf_zoom ); ?>">
									<?php vtf_image( $vtf_item, [ 'loading' => 0 === $vtf_n ? 'eager' : 'lazy' ], 'large' ); ?>
								</div>
							<?php } elseif ( 'pack' === $vtf_type ) { ?>
								<div class="swiper-slide pdp__slide pdp__slide--pack" data-zoom="<?php echo esc_url( $vtf_zoom ); ?>">
									<?php $vtf_bottle( 'pdp__bottle', (int) $vtf_item ); ?>
								</div>
							<?php } else { ?>
								<div class="swiper-slide pdp__slide pdp__slide--pack">
									<?php $vtf_bottle( trim( 'pdp__bottle ' . $vtf_item ) ); ?>
								</div>
							<?php } ?>
						<?php } ?>
						<?php if ( '' !== $vtf_video ) { ?>
							<div class="swiper-slide pdp__slide pdp__slide--video js-pdp-video-slide" data-zoom="<?php echo esc_url( $vtf_video ); ?>" data-zoom-type="html5video">
								<video class="js-pdp-video" playsinline preload="none"<?php echo $vtf_poster ? ' poster="' . esc_url( vtf_image_url( $vtf_poster, 'large' ) ) . '"' : ''; ?> src="<?php echo esc_url( $vtf_video ); ?>"></video>
								<button class="pdp__play js-pdp-play" type="button" aria-label="<?php esc_attr_e( 'Play the video', 'vitfit' ); ?>"><?php vtf_sprite( 'i-play' ); ?></button>
							</div>
						<?php } ?>
					</div>
					<?php get_template_part( 'template-parts/global/badge', null, [ 'type' => $vtf_product['badge'], 'class' => 'pdp__badge' ] ); ?>
					<div class="pdp__arrows">
						<button class="btn-icon js-pdp-prev" type="button" aria-label="<?php esc_attr_e( 'Previous photo', 'vitfit' ); ?>"><svg viewBox="0 0 24 24" style="transform: scaleX(-1)"><use href="#i-arrow" /></svg></button>
						<button class="btn-icon js-pdp-next" type="button" aria-label="<?php esc_attr_e( 'Next photo', 'vitfit' ); ?>"><?php vtf_sprite( 'i-arrow' ); ?></button>
					</div>
				</div>

				<div class="swiper pdp__thumbs js-pdp-thumbs">
					<div class="swiper-wrapper">
						<?php foreach ( $vtf_slides as [ $vtf_type, $vtf_item ] ) { ?>
							<?php if ( 'photo' === $vtf_type ) { ?>
								<div class="swiper-slide pdp__thumb pdp__thumb--photo"><?php vtf_image( $vtf_item, [ 'alt' => '' ], 'thumbnail' ); ?></div>
							<?php } elseif ( 'pack' === $vtf_type ) { ?>
								<div class="swiper-slide pdp__thumb"><?php $vtf_bottle( '', (int) $vtf_item, false ); ?></div>
							<?php } else { ?>
								<div class="swiper-slide pdp__thumb"><?php $vtf_bottle( $vtf_item, 0, false ); ?></div>
							<?php } ?>
						<?php } ?>
						<?php if ( '' !== $vtf_video ) { ?>
							<div class="swiper-slide pdp__thumb pdp__thumb--photo pdp__thumb--video"><?php vtf_image( $vtf_poster, [ 'alt' => '' ], 'thumbnail' ); ?><?php vtf_sprite( 'i-play' ); ?></div>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="pdp__info">
				<h1 class="h2 pdp__title"><?php echo esc_html( $vtf_name ); ?></h1>
				<?php if ( $vtf_rating['count'] ) { ?>
					<a class="pdp__rating" href="#reviews">
						<?php vtf_stars( $vtf_rating['average'] ); ?>
						<b><?php echo esc_html( vtf_rating_text( $vtf_rating['average'] ) ); ?></b><span><?php echo esc_html( $vtf_rating['count'] . ' ' . vtf_count_word( $vtf_rating['count'], 'reviews' ) ); ?></span>
					</a>
				<?php } ?>
				<?php if ( '' !== $vtf_lead ) { ?>
					<p class="text-md pdp__lead"><?php vtf_br( $vtf_lead ); ?></p>
				<?php } ?>

				<?php if ( ! empty( $vtf_specs ) ) { ?>
					<div class="pdp__group">
						<p class="pdp__label"><?php esc_html_e( 'Characteristics', 'vitfit' ); ?></p>
						<dl class="specs">
							<?php foreach ( $vtf_specs as $vtf_row ) { ?>
								<div><dt><?php echo esc_html( (string) ( $vtf_row['label'] ?? '' ) ); ?></dt><dd><?php echo esc_html( (string) $vtf_row['value'] ); ?></dd></div>
							<?php } ?>
						</dl>
					</div>
				<?php } ?>

				<?php if ( ! empty( $vtf_taste ) ) { ?>
					<div class="pdp__group taste">
						<p class="pdp__label"><?php esc_html_e( 'Taste profile', 'vitfit' ); ?></p>
						<ul class="taste__list">
							<?php foreach ( $vtf_taste as $vtf_row ) { ?>
								<?php $vtf_value = min( 5, max( 0, (int) ( $vtf_row['value'] ?? 0 ) ) ); ?>
								<li><span><?php echo esc_html( $vtf_row['label'] ); ?></span><span class="taste__bar" data-v="<?php echo esc_attr( (string) $vtf_value ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: value 0–5 */ __( '%d out of 5', 'vitfit' ), $vtf_value ) ); ?>"><i></i><i></i><i></i><i></i><i></i></span></li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>

				<?php if ( ! empty( $vtf_perks ) ) { ?>
					<div class="pdp__group">
						<p class="pdp__label"><?php esc_html_e( 'Perks', 'vitfit' ); ?></p>
						<ul class="perks">
							<?php foreach ( $vtf_perks as $vtf_row ) { ?>
								<li><span class="perks__icon"><?php vtf_feature_icon( $vtf_row['icon'] ?? '' ); ?></span><?php echo esc_html( $vtf_row['text'] ); ?></li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>

				<?php if ( ! empty( $vtf_details ) ) { ?>
					<div class="pdp__group">
						<p class="pdp__label"><?php esc_html_e( 'About the product', 'vitfit' ); ?></p>
						<div class="faq pdp__faq">
							<?php foreach ( $vtf_details as $vtf_row ) { ?>
								<div class="faq__item"><button class="faq__head js-faq-head" type="button" aria-expanded="false"><span class="h5"><?php echo esc_html( $vtf_row['title'] ); ?></span><span class="faq__icon"><?php vtf_sprite( 'i-plus' ); ?></span></button><div class="faq__body"><div class="faq__inner"><div class="faq__content rich-text text-md"><?php vtf_editor_content( $vtf_row['content'] ?? '' ); ?></div></div></div></div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>

				<div class="pdp__actions">
					<a class="btn" href="#where"><span class="btn__label"><?php esc_html_e( 'Where to buy', 'vitfit' ); ?></span><span class="btn__tile"><?php vtf_sprite( 'i-arrow' ); ?></span></a>
					<button class="link-arrow link-arrow--lg" type="button" data-modal-open="partner"><span class="link-arrow__text"><?php esc_html_e( 'Wholesale for business', 'vitfit' ); ?></span><span class="link-arrow__icon"><?php vtf_sprite( 'i-arrow' ); ?></span></button>
				</div>
			</div>
		</div>
	</div>
</section>
