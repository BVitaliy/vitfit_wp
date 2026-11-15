<?php
/**
 * Block «Flavours» (markup: template-parts/home-flavors.php): every flavour
 * (or the picked ones) — ≥1200 a pinned horizontal track that colours the
 * section by the centred flavour, below a Swiper (app-flavors.js).
 *
 * Flavour data (colour, notes, packshot, formats, link): Products → Flavours.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_flavors = vtf_flavors( vtf_ids( $args['flavors'] ?? [] ) );

if ( empty( $vtf_flavors ) ) {
	return;
}

$vtf_total = vtf_num( count( $vtf_flavors ) );
$vtf_more  = (string) ( $args['link_label'] ?? '' ) ?: __( 'Learn more', 'vitfit' );
?>
<section class="flavors js-flavors" id="flavors">
	<div class="flavors__pin">
		<div class="flavors__head container">
			<div class="section-head__main">
				<?php vtf_title( $args, 'h2', 'h2' ); ?>
			</div>
			<div class="flavors__side">
				<?php if ( ! empty( $args['text'] ) ) { ?>
					<p class="text-lg"><?php vtf_br( $args['text'] ); ?></p>
				<?php } ?>
				<div class="flavors__counter"><b class="js-flavors-current">01</b> / <?php echo esc_html( $vtf_total ); ?></div>
			</div>
		</div>
		<div class="flavors__viewport">
			<div class="swiper flavors__slider js-flavors-slider">
				<div class="swiper-wrapper flavors__track js-flavors-track">
					<?php foreach ( $vtf_flavors as $vtf_n => $vtf_flavor ) { ?>
						<?php
						$vtf_colors  = vtf_flavor_colors( $vtf_flavor );
						$vtf_formats = vtf_flavor_formats( $vtf_flavor );
						$vtf_url     = vtf_flavor_url( $vtf_flavor );
						?>
						<article<?php echo vtf_flavor_attrs( 'swiper-slide flavor', $vtf_flavor ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?> data-flavor="<?php echo esc_attr( $vtf_colors['slug'] ); ?>">
							<span class="flavor__num"><?php echo esc_html( vtf_num( $vtf_n + 1 ) ); ?><small>/<?php echo esc_html( $vtf_total ); ?></small></span>
							<span class="flavor__stage">
								<span class="flavor__ring"></span>
								<?php vtf_flavor_bottle( $vtf_flavor, 'flavor__bottle' ); ?>
							</span>
							<span class="flavor__body">
								<?php if ( '' !== $vtf_formats ) { ?>
									<span class="flavor__formats"><?php echo esc_html( $vtf_formats ); ?></span>
								<?php } ?>
								<h3 class="h3 flavor__name"><?php echo esc_html( $vtf_flavor->name ); ?></h3>
								<?php if ( '' !== trim( $vtf_flavor->description ) ) { ?>
									<p class="text-md flavor__notes"><?php echo esc_html( $vtf_flavor->description ); ?></p>
								<?php } ?>
								<?php if ( '' !== $vtf_url ) { ?>
									<a class="link-arrow flavor__link" href="<?php echo esc_url( $vtf_url ); ?>"<?php echo vtf_aria_label( $vtf_more . ' — ' . $vtf_flavor->name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><span class="link-arrow__text"><?php echo esc_html( $vtf_more ); ?></span><span class="link-arrow__icon"><?php vtf_sprite( 'i-arrow' ); ?></span></a>
								<?php } ?>
							</span>
						</article>
					<?php } ?>
				</div>
			</div>
			<div class="flavors__bar"><i class="js-flavors-bar"></i></div>
		</div>
	</div>
</section>
