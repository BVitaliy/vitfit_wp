<?php
/**
 * 404 (markup: 404.php → template-parts/error-404.php). Texts: Site Settings → 404.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="page">
	<?php get_template_part( 'template-parts/error-404' ); ?>
	<div class="spacer-xl"></div>
</main>

<?php
get_footer();
