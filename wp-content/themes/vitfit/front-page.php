<?php
/**
 * Front page — blocks from the page builder (markup: index.php, home V3).
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="home-v3">
	<?php vtf_render_builder(); ?>
</main>

<?php
get_footer();
