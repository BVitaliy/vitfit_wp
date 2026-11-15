<?php
/**
 * Text page (privacy policy, cookies…): page top + a column of editor output
 * (markup: template-parts/text-page.php). The lead under the title is the
 * page's excerpt-like «Lead» field; empty — the last update date.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_lead = function_exists( 'get_field' ) ? trim( (string) get_field( 'lead' ) ) : '';

if ( '' === $vtf_lead ) {
	/* translators: %s: date */
	$vtf_lead = sprintf( __( 'Updated %s', 'vitfit' ), get_the_modified_date( 'j F Y' ) );
}
?>
<section class="text-page">
	<div class="container">
		<div class="page-top">
			<?php vtf_breadcrumbs(); ?>
			<h1 class="h2 page-top__title"><?php the_title(); ?></h1>
			<p class="text-md page-top__lead"><?php echo esc_html( $vtf_lead ); ?></p>
		</div>
		<div class="rich-text text-lg text-page__body">
			<?php the_content(); ?>
		</div>
	</div>
</section>
