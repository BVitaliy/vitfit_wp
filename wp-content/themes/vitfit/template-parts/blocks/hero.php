<?php
/**
 * Block «Hero slider» (markup: template-parts/home-hero.php).
 *
 * Each slide = one flavour: its colours tint the section (app-hero.js reads
 * data-flavor), its packshot stands in front, a second flavour behind.
 * Title lines: one per line, *line* = the accent colour. The first slide's
 * title is the page <h1>, the others are <p> (as in the markup).
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_slides = array_values(
	array_filter(
		(array) ( $args['slides'] ?? [] ),
		static fn( $slide ) => ! empty( $slide['title'] )
	)
);

if ( empty( $vtf_slides ) ) {
	return;
}
?>
<section class="hero js-hero" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Main banner', 'vitfit' ); ?>">
	<div class="hero__bg" aria-hidden="true">
		<span class="hero__blob hero__blob--1"></span>
		<span class="hero__blob hero__blob--2"></span>
	</div>

	<div class="swiper hero__slider js-hero-slider">
		<div class="swiper-wrapper">
			<?php foreach ( $vtf_slides as $vtf_n => $vtf_slide ) { ?>
				<?php
				$vtf_flavor = get_term( (int) ( $vtf_slide['flavor'] ?? 0 ), 'vtf_flavor' );
				$vtf_flavor = $vtf_flavor instanceof WP_Term ? $vtf_flavor : null;
				$vtf_colors = vtf_flavor_colors( $vtf_flavor );
				$vtf_tag    = 0 === $vtf_n ? vtf_title_tag( $args, 'h1' ) : 'p';
				$vtf_label  = trim( (string) ( $vtf_slide['tag_label'] ?? '' ) );
				$vtf_text   = trim( (string) ( $vtf_slide['tag_text'] ?? '' ) );
				?>
				<div<?php echo vtf_flavor_attrs( 'swiper-slide hero__slide', $vtf_flavor ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?> data-flavor="<?php echo esc_attr( $vtf_colors['slug'] ); ?>">
					<div class="container hero__grid">
						<div class="hero__content">
							<<?php echo esc_html( $vtf_tag ); ?> class="h1 hero__title">
								<?php foreach ( vtf_lines( str_replace( [ '<br />', '<br>' ], "\n", (string) $vtf_slide['title'] ) ) as $vtf_line ) { ?>
									<?php $vtf_accent = (bool) preg_match( '/^\*(.+)\*$/u', $vtf_line, $vtf_match ); ?>
									<span class="hero__line"><span<?php echo $vtf_accent ? ' class="hero__accent"' : ''; ?>><?php echo esc_html( $vtf_accent ? $vtf_match[1] : $vtf_line ); ?></span></span>
								<?php } ?>
							</<?php echo esc_html( $vtf_tag ); ?>>
							<?php if ( ! empty( $vtf_slide['text'] ) ) { ?>
								<p class="text-lg hero__text"><?php vtf_br( $vtf_slide['text'] ); ?></p>
							<?php } ?>
							<?php if ( vtf_has_button( $vtf_slide['button'] ?? null ) || vtf_has_button( $vtf_slide['link'] ?? null ) ) { ?>
								<div class="hero__btns">
									<?php vtf_button( $vtf_slide['button'] ?? null ); ?>
									<?php vtf_link_arrow( $vtf_slide['link'] ?? null, 'link-arrow link-arrow--lg' ); ?>
								</div>
							<?php } ?>
						</div>
						<div class="hero__visual">
							<span class="hero__halo"></span>
							<span class="hero__bubble" style="--x:10%;--y:24%;--s:1;--d:0s"></span>
							<span class="hero__bubble" style="--x:84%;--y:18%;--s:.7;--d:-2s"></span>
							<span class="hero__bubble" style="--x:88%;--y:70%;--s:1.2;--d:-4s"></span>
							<?php if ( ! empty( $vtf_slide['back_flavor'] ) ) { ?>
								<span class="hero__bottle-wrap hero__bottle-wrap--back">
									<?php vtf_flavor_bottle( (int) $vtf_slide['back_flavor'], 'hero__bottle', true, 0 === $vtf_n ); ?>
								</span>
							<?php } ?>
							<span class="hero__bottle-wrap">
								<?php vtf_flavor_bottle( $vtf_flavor, 'hero__bottle', true, 0 === $vtf_n ); ?>
							</span>
							<?php if ( '' !== $vtf_label || '' !== $vtf_text ) { ?>
								<span class="hero__tag"><?php if ( '' !== $vtf_label ) { ?><b><?php echo esc_html( $vtf_label ); ?></b> <?php } ?><?php echo esc_html( $vtf_text ); ?></span>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>

	<?php if ( count( $vtf_slides ) > 1 ) { ?>
		<div class="container hero__bottom">
			<div class="hero__pagination js-hero-pagination" role="tablist">
				<?php foreach ( $vtf_slides as $vtf_n => $vtf_slide ) { ?>
					<?php $vtf_flavor = get_term( (int) ( $vtf_slide['flavor'] ?? 0 ), 'vtf_flavor' ); ?>
					<button class="hero__dot<?php echo 0 === $vtf_n ? ' is-active' : ''; ?>" type="button" aria-label="<?php echo esc_attr( $vtf_flavor instanceof WP_Term ? $vtf_flavor->name : vtf_num( $vtf_n + 1 ) ); ?>"><i></i></button>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
</section>
