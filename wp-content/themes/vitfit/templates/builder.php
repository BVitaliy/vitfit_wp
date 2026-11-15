<?php
/**
 * Template Name: Page builder
 *
 * «Конструктор»: a page assembled from the builder blocks — every section of
 * every page of the site is available here, in any order, hideable.
 * Pages on the default template are text pages (title + editor).
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="page">
	<?php vtf_render_builder(); ?>
</main>

<?php
get_footer();
