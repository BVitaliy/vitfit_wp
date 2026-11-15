<?php
/**
 * Block «SEO text» (markup: template-parts/seo-block.php): title + editor
 * text; the second editor field is hidden behind «Read more» (app-more-text.js).
 * No hidden text → no toggle. Below 1200 the first paragraph is clamped.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_visible = trim( (string) ( $args['visible'] ?? '' ) );
$vtf_hidden  = trim( (string) ( $args['hidden'] ?? '' ) );

if ( '' === $vtf_visible && '' === $vtf_hidden ) {
	return;
}

$vtf_id = 'seo-more-' . (int) ( $args['index'] ?? 0 );
?>
<section class="seo-block">
	<div class="container">
		<div class="seo-block__box">
			<?php vtf_title( $args, 'h2', 'seo-block__title h4' ); ?>

			<div class="more-text<?php echo '' !== $vtf_hidden ? ' more-text--clamp' : ''; ?> js-more-text">
				<div class="more-text__visible rich-text text-md">
					<?php vtf_editor_content( $vtf_visible ); ?>
				</div>

				<?php if ( '' !== $vtf_hidden ) { ?>
					<div class="more-text__hidden" id="<?php echo esc_attr( $vtf_id ); ?>">
						<div class="more-text__hidden-inner rich-text text-md">
							<?php vtf_editor_content( $vtf_hidden ); ?>
						</div>
					</div>

					<button class="more-text__toggle link-arrow js-more-text-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $vtf_id ); ?>">
						<span class="link-arrow__text"><span class="more-text__more"><?php esc_html_e( 'Read more', 'vitfit' ); ?></span><span class="more-text__less"><?php esc_html_e( 'Collapse', 'vitfit' ); ?></span></span>
						<span class="link-arrow__icon"><?php vtf_sprite( 'i-arrow' ); ?></span>
					</button>
				<?php } ?>
			</div>
		</div>
	</div>
</section>
