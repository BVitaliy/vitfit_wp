<?php
/**
 * Block «Instagram» (markup: template-parts/home-instagram.php): posts 4:5 in
 * two rows (phones: two rows scrolling sideways) + «Follow» button.
 * Photos are uploaded in the block; a post without its own link opens the profile.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_items   = array_filter( (array) ( $args['items'] ?? [] ), static fn( $item ) => vtf_image_id( $item['image'] ?? 0 ) );
$vtf_button  = $args['button'] ?? null;
$vtf_profile = vtf_link( $vtf_button );
$vtf_profile = $vtf_profile ? $vtf_profile['url'] : '';

if ( empty( $vtf_items ) ) {
	return;
}

$vtf_label = __( 'Instagram post', 'vitfit' );
?>
<section class="insta">
	<div class="container">
		<div class="section-head">
			<?php if ( ! empty( $args['title'] ) ) { ?>
				<div class="section-head__main">
					<?php $vtf_tag = vtf_title_tag( $args, 'h2' ); ?>
					<<?php echo esc_html( $vtf_tag ); ?> class="h2 section-head__title"><?php vtf_title_html( (string) $args['title'], (string) ( $args['title_mark'] ?? '' ) ); ?></<?php echo esc_html( $vtf_tag ); ?>>
				</div>
			<?php } ?>
			<?php vtf_button( $vtf_button, 'btn', 'i-instagram' ); ?>
		</div>
	</div>
	<ul class="insta__grid" data-lenis-prevent-touch>
		<?php foreach ( $vtf_items as $vtf_item ) { ?>
			<?php $vtf_url = trim( (string) ( $vtf_item['link'] ?? '' ) ) ?: $vtf_profile; ?>
			<li class="insta__item">
				<?php if ( '' !== $vtf_url ) { ?><a href="<?php echo esc_url( $vtf_url ); ?>" target="_blank" rel="noopener"<?php echo vtf_aria_label( $vtf_label, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php } ?>
					<?php vtf_picture( $vtf_item['image'], $vtf_item['image_mobile'] ?? null, [ 'alt' => '' ], 'medium_large' ); ?>
					<span class="insta__icon"><?php vtf_sprite( 'i-instagram' ); ?></span>
				<?php if ( '' !== $vtf_url ) { ?></a><?php } ?>
			</li>
		<?php } ?>
	</ul>
</section>
