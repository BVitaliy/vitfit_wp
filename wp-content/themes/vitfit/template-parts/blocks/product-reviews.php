<?php
/**
 * Block «Product: reviews» (markup: template-parts/product-reviews.php) — on
 * product pages only: the summary (average, count, 5→1 bars) + review cards in
 * a slider + «Leave a review» (the review popup; saved for moderation).
 * Without reviews: the summary gives way to a short text and the button.
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

$vtf_reviews = vtf_product_reviews( $vtf_product );
$vtf_rating  = vtf_product_rating( $vtf_product );
$vtf_format  = vtf_product_format( vtf_product_data( $vtf_product ) );
$vtf_button  = trim( (string) ( $args['button_label'] ?? '' ) ) ?: __( 'Leave a review', 'vitfit' );
$vtf_can     = vtf_has_form( (int) vtf_option( 'review_form' ) );
$vtf_title   = trim( (string) ( $args['title'] ?? '' ) ) ?: __( 'Reviews', 'vitfit' );
?>
<section class="reviews" id="reviews">
	<div class="container">
		<div class="section-head">
			<div class="section-head__main">
				<h2 class="h2 section-head__title"><?php echo esc_html( $vtf_title ); ?></h2>
			</div>
			<div class="section-head__side">
				<?php if ( count( $vtf_reviews ) > 1 ) { ?>
					<div class="slider-nav">
						<button class="btn-icon js-slider-prev" type="button" aria-label="<?php esc_attr_e( 'Previous reviews', 'vitfit' ); ?>"><svg viewBox="0 0 24 24" style="transform: scaleX(-1)"><use href="#i-arrow" /></svg></button>
						<button class="btn-icon js-slider-next" type="button" aria-label="<?php esc_attr_e( 'Next reviews', 'vitfit' ); ?>"><?php vtf_sprite( 'i-arrow' ); ?></button>
					</div>
				<?php } ?>
				<?php if ( $vtf_can ) { ?>
					<button class="btn section-head__btn" type="button" data-modal-open="review"><span class="btn__label"><?php echo esc_html( $vtf_button ); ?></span><span class="btn__tile"><?php vtf_sprite( 'i-arrow' ); ?></span></button>
				<?php } ?>
			</div>
		</div>

		<?php if ( empty( $vtf_reviews ) ) { ?>
			<p class="text-lg"><?php echo esc_html( trim( (string) ( $args['empty_text'] ?? '' ) ) ?: __( 'No reviews yet — be the first to share your impression.', 'vitfit' ) ); ?></p>
		<?php } else { ?>
			<div class="reviews__layout">
				<div class="reviews__summary">
					<p class="reviews__score"><?php echo esc_html( vtf_rating_text( $vtf_rating['average'] ) ); ?></p>
					<?php vtf_stars( $vtf_rating['average'] ); ?>
					<p class="reviews__count"><?php echo esc_html( $vtf_rating['count'] . ' ' . vtf_count_word( $vtf_rating['count'], 'reviews' ) ); ?></p>
					<ul class="reviews__bars">
						<?php foreach ( $vtf_rating['stars'] as $vtf_star => $vtf_count ) { ?>
							<li><span><?php echo esc_html( (string) $vtf_star ); ?></span><span class="reviews__bar" style="--p: <?php echo esc_attr( (string) round( $vtf_count / $vtf_rating['count'] * 100 ) ); ?>%"></span><span><?php echo esc_html( (string) $vtf_count ); ?></span></li>
						<?php } ?>
					</ul>
				</div>

				<div class="swiper grid-slider reviews__slider js-slider">
					<div class="swiper-wrapper reviews__list">
						<?php foreach ( $vtf_reviews as $vtf_review ) { ?>
							<?php
							$vtf_name = (string) get_the_title( $vtf_review );
							$vtf_rate = min( 5, max( 1, (int) get_post_meta( $vtf_review->ID, 'rating', true ) ) );
							$vtf_meta = trim( (string) get_post_meta( $vtf_review->ID, 'meta', true ) ) ?: $vtf_format;
							?>
							<article class="swiper-slide review">
								<div class="review__head">
									<span class="review__avatar"><?php echo esc_html( mb_strtoupper( mb_substr( $vtf_name, 0, 1 ) ) ); ?></span>
									<span><b class="review__name"><?php echo esc_html( $vtf_name ); ?></b><span class="review__date"><?php echo esc_html( vtf_post_date( $vtf_review ) ); ?></span></span>
								</div>
								<?php vtf_stars( $vtf_rate ); ?>
								<p class="text-md"><?php echo esc_html( wp_strip_all_tags( $vtf_review->post_content ) ); ?></p>
								<?php if ( '' !== $vtf_meta ) { ?>
									<span class="review__meta"><?php echo esc_html( $vtf_meta ); ?></span>
								<?php } ?>
							</article>
						<?php } ?>
					</div>
					<div class="grid-slider__pagination"></div>
				</div>
			</div>
		<?php } ?>
	</div>
</section>
