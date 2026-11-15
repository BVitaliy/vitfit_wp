<?php
/**
 * Block «B2B» (markup: template-parts/home-b2b.php): dark box with perks and
 * the button that opens the partner popup; photo on the right.
 * Title: *text* = the brand accent (`.b2b__brand`, never wraps).
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_title = trim( (string) ( $args['title'] ?? '' ) );
$vtf_perks = array_filter( (array) ( $args['perks'] ?? [] ), static fn( $perk ) => ! empty( $perk['title'] ) || ! empty( $perk['text'] ) );
$vtf_photo = vtf_image_id( $args['image'] ?? 0 );

if ( '' === $vtf_title && empty( $args['text'] ) ) {
	return;
}

$vtf_tag = vtf_title_tag( $args, 'h2' );
?>
<section class="b2b">
	<div class="container">
		<div class="b2b__box">
			<div class="b2b__body">
				<?php if ( '' !== $vtf_title ) { ?>
					<<?php echo esc_html( $vtf_tag ); ?> class="h2 clr-white b2b__title"><?php echo preg_replace( '/\*([^*]+)\*/u', '<span class="b2b__brand nowrap">$1</span>', wp_kses( $vtf_title, [ 'br' => [] ] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses'd, accent added by the theme. ?></<?php echo esc_html( $vtf_tag ); ?>>
				<?php } ?>
				<?php if ( ! empty( $args['text'] ) ) { ?>
					<p class="text-lg b2b__text"><?php vtf_br( $args['text'] ); ?></p>
				<?php } ?>
				<?php if ( ! empty( $vtf_perks ) ) { ?>
					<ul class="b2b__perks">
						<?php foreach ( $vtf_perks as $vtf_perk ) { ?>
							<li><span><?php vtf_feature_icon( $vtf_perk['icon'] ?? '' ); ?></span><?php if ( ! empty( $vtf_perk['title'] ) ) { ?><b><?php echo esc_html( $vtf_perk['title'] ); ?></b><?php } ?><?php echo esc_html( (string) ( $vtf_perk['text'] ?? '' ) ); ?></li>
						<?php } ?>
					</ul>
				<?php } ?>
				<?php vtf_button( $args['button'] ?? null, 'btn' . vtf_flavor_class( $args['button_color'] ?? '' ) ); ?>
			</div>
			<?php if ( $vtf_photo ) { ?>
				<div class="b2b__media">
					<span class="b2b__circle"></span>
					<?php vtf_picture( $vtf_photo, null, [ 'alt' => (string) get_post_meta( $vtf_photo, '_wp_attachment_image_alt', true ) ], 'large', [ 'data-parallax-target' => true ] ); ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
