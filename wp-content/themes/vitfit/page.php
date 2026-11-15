<?php
/**
 * Default page template: a text page (privacy policy, cookies…) — markup:
 * privacy.php → template-parts/text-page.php. Pages built from blocks use the
 * “Page builder” template.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="page">
	<?php
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/text-page' );
	}
	?>
	<div class="spacer-xl"></div>
</main>

<?php
get_footer();
