<?php
/**
 * Block «Fullscreen video» (markup: template-parts/home-reel.php): a muted loop
 * (desktop / phone files + posters) that plays only in view (app-reel.js),
 * a two-line title and the button that opens the full video in a popup.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_video     = (string) ( $args['video'] ?? '' );
$vtf_video_mob = (string) ( $args['video_mobile'] ?? '' );
$vtf_popup     = (string) ( $args['popup_video'] ?? '' );
$vtf_line_1    = trim( (string) ( $args['title'] ?? '' ) );
$vtf_line_2    = trim( (string) ( $args['title_2'] ?? '' ) );

if ( '' === $vtf_video && '' === $vtf_line_1 ) {
	return;
}

$vtf_poster     = vtf_image_url( $args['poster'] ?? 0, 'full' );
$vtf_poster_mob = vtf_image_url( $args['poster_mobile'] ?? 0, 'large' );
$vtf_tag        = vtf_title_tag( $args, 'p' );
?>
<section class="reel js-reel">
	<div class="reel__pin">
		<div class="reel__media js-reel-media">
			<?php if ( '' !== $vtf_video ) { ?>
				<video class="reel__video js-reel-video" muted loop playsinline preload="none"<?php echo '' !== $vtf_poster ? ' poster="' . esc_url( $vtf_poster ) . '"' : ''; ?><?php echo '' !== $vtf_poster_mob ? ' data-poster-mob="' . esc_url( $vtf_poster_mob ) . '"' : ''; ?> data-src="<?php echo esc_url( $vtf_video ); ?>"<?php echo '' !== $vtf_video_mob ? ' data-src-mob="' . esc_url( $vtf_video_mob ) . '"' : ''; ?>></video>
			<?php } ?>
			<span class="reel__shade"></span>
		</div>
		<div class="reel__content container js-reel-content">
			<?php if ( '' !== $vtf_line_1 || '' !== $vtf_line_2 ) { ?>
				<<?php echo esc_html( $vtf_tag ); ?> class="reel__title">
					<?php if ( '' !== $vtf_line_1 ) { ?>
						<span class="reel__line"><?php echo esc_html( $vtf_line_1 ); ?></span>
					<?php } ?>
					<?php if ( '' !== $vtf_line_2 ) { ?>
						<span class="reel__line reel__line--out"><?php echo esc_html( $vtf_line_2 ); ?></span>
					<?php } ?>
				</<?php echo esc_html( $vtf_tag ); ?>>
			<?php } ?>
			<?php if ( ! empty( $args['text'] ) ) { ?>
				<p class="text-lg reel__text"><?php vtf_br( $args['text'] ); ?></p>
			<?php } ?>
			<?php if ( '' !== $vtf_popup ) { ?>
				<button class="reel__play" type="button" data-video-open="<?php echo esc_url( $vtf_popup ); ?>">
					<span class="reel__play-icon"><?php vtf_sprite( 'i-play' ); ?></span>
					<span><?php echo esc_html( (string) ( $args['button_label'] ?? '' ) ?: __( 'Watch the video', 'vitfit' ) ); ?></span>
				</button>
			<?php } ?>
		</div>
	</div>
</section>
