<?php
/**
 * Block «About the brand» (markup: template-parts/home-about.php).
 *
 * Title in three offset rows with two media «pills» (ref. euduco.ua): a video
 * loop before the second row's text, a photo after the third row's text.
 * Photo with a rotating sticker, lead, text, count-up stats, button.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_rows   = [ trim( (string) ( $args['row_1'] ?? '' ) ), trim( (string) ( $args['row_2'] ?? '' ) ), trim( (string) ( $args['row_3'] ?? '' ) ) ];
$vtf_photo  = vtf_image_id( $args['image'] ?? 0 );
$vtf_stats  = array_filter( (array) ( $args['stats'] ?? [] ), static fn( $stat ) => '' !== trim( (string) ( $stat['number'] ?? '' ) ) );
$vtf_video  = (string) ( $args['pill_video'] ?? '' );
$vtf_pill   = vtf_image_id( $args['pill_image'] ?? 0 );
$vtf_tag    = vtf_title_tag( $args, 'h2' );
$vtf_ring   = trim( (string) ( $args['sticker_text'] ?? '' ) );
$vtf_circle = 'about-circle-' . (int) ( $args['index'] ?? 0 );

if ( '' === implode( '', $vtf_rows ) && ! $vtf_photo && empty( $args['lead'] ) ) {
	return;
}
?>
<section class="about">
	<div class="container">
		<?php if ( '' !== implode( '', $vtf_rows ) ) { ?>
			<<?php echo esc_html( $vtf_tag ); ?> class="h2 about__title" data-title-anim="off">
				<?php if ( '' !== $vtf_rows[0] ) { ?>
					<span class="about__row about__row--1"><?php echo esc_html( $vtf_rows[0] ); ?></span>
				<?php } ?>
				<span class="about__row about__row--2"><?php if ( '' !== $vtf_video ) { ?><span class="title-pill title-pill--video<?php echo esc_attr( vtf_flavor_class( $args['pill_video_color'] ?? '' ) ); ?>"><video muted loop playsinline autoplay preload="metadata"<?php echo vtf_image_id( $args['pill_video_poster'] ?? 0 ) ? ' poster="' . esc_url( vtf_image_url( $args['pill_video_poster'], 'medium_large' ) ) . '"' : ''; ?> src="<?php echo esc_url( $vtf_video ); ?>"></video><?php if ( ! empty( $args['pill_video_tag'] ) ) { ?><i class="title-pill__tag"><?php echo esc_html( $args['pill_video_tag'] ); ?></i><?php } ?></span> <?php } ?><?php echo esc_html( $vtf_rows[1] ); ?></span>
				<span class="about__row about__row--3"><?php echo esc_html( $vtf_rows[2] ); ?><?php if ( $vtf_pill ) { ?> <span class="title-pill<?php echo esc_attr( vtf_flavor_class( $args['pill_image_color'] ?? '' ) ); ?>"><?php vtf_image( $vtf_pill, [ 'alt' => '' ], 'medium_large' ); ?><?php if ( ! empty( $args['pill_image_tag'] ) ) { ?><i class="title-pill__tag"><?php echo esc_html( $args['pill_image_tag'] ); ?></i><?php } ?></span><?php } ?></span>
			</<?php echo esc_html( $vtf_tag ); ?>>
		<?php } ?>

		<div class="about__grid">
			<?php if ( $vtf_photo ) { ?>
				<div class="about__media" data-parallax>
					<?php vtf_picture( $vtf_photo, $args['image_mobile'] ?? null, [ 'alt' => (string) get_post_meta( $vtf_photo, '_wp_attachment_image_alt', true ) ], 'large', [ 'data-parallax-target' => true ] ); ?>
					<?php if ( '' !== $vtf_ring ) { ?>
						<div class="about__sticker js-spin" aria-hidden="true">
							<svg viewBox="0 0 200 200">
								<defs><path id="<?php echo esc_attr( $vtf_circle ); ?>" d="M100 100m-72 0a72 72 0 1 1 144 0a72 72 0 1 1-144 0" /></defs>
								<text><textPath href="#<?php echo esc_attr( $vtf_circle ); ?>"><?php echo esc_html( $vtf_ring ); ?></textPath></text>
							</svg>
							<span><?php echo esc_html( (string) ( $args['sticker_label'] ?? '' ) ); ?></span>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

			<div class="about__body">
				<?php if ( ! empty( $args['lead'] ) ) { ?>
					<p class="h4 about__lead"><?php vtf_br( $args['lead'] ); ?></p>
				<?php } ?>
				<?php if ( ! empty( $args['text'] ) ) { ?>
					<p class="text-lg"><?php vtf_br( $args['text'] ); ?></p>
				<?php } ?>
				<?php if ( ! empty( $vtf_stats ) ) { ?>
					<ul class="about__stats js-stats">
						<?php foreach ( $vtf_stats as $vtf_stat ) { ?>
							<?php
							$vtf_number = trim( (string) $vtf_stat['number'] );
							$vtf_count  = is_numeric( $vtf_number ) ? ' data-count="' . esc_attr( $vtf_number ) . '"' : '';
							?>
							<li><b<?php echo $vtf_count; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>><?php echo esc_html( $vtf_number ); ?></b><?php if ( ! empty( $vtf_stat['suffix'] ) ) { ?><i><?php echo esc_html( $vtf_stat['suffix'] ); ?></i><?php } ?><?php if ( ! empty( $vtf_stat['label'] ) ) { ?><span><?php echo esc_html( $vtf_stat['label'] ); ?></span><?php } ?></li>
						<?php } ?>
					</ul>
				<?php } ?>
				<?php vtf_button( $args['button'] ?? null ); ?>
			</div>
		</div>
	</div>
</section>
