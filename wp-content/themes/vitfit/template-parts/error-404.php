<?php
/**
 * 404 — big number with a bottle as the zero, text, ways back.
 * Empty settings fall back to the markup's texts (theme strings).
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_title = trim( (string) vtf_option( 'error_title' ) ) ?: __( 'Page *not found*', 'vitfit' );
$vtf_text  = trim( (string) vtf_option( 'error_text' ) ) ?: __( 'Looks like this bottle is already empty. Go back to the home page or pick your flavour in the catalog.', 'vitfit' );
$vtf_link  = vtf_option( 'error_link' );
?>
<section class="error-page">
	<div class="container error-page__inner">
		<p class="error-page__code" aria-hidden="true"><span>4</span><span class="bottle error-page__bottle fl--sweet-cherry"><svg viewBox="0 0 200 470"><use href="#bottle" /></svg><span class="bottle__name">404</span></span><span>4</span></p>
		<h1 class="h2"><?php vtf_title_html( $vtf_title, 'sweet-cherry' ); ?></h1>
		<p class="text-lg"><?php vtf_br( $vtf_text ); ?></p>
		<div class="error-page__actions">
			<a class="btn fl--sweet-cherry" href="<?php echo esc_url( vtf_home_url() ); ?>"><span class="btn__label"><?php esc_html_e( 'Home page', 'vitfit' ); ?></span><span class="btn__tile"><?php vtf_sprite( 'i-arrow' ); ?></span></a>
			<?php vtf_link_arrow( $vtf_link, 'link-arrow link-arrow--lg' ); ?>
		</div>
	</div>
</section>
