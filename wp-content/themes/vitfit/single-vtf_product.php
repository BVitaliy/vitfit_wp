<?php
/**
 * Product page (markup: product.php): the product top
 * (template-parts/product/hero.php) + the blocks of the shared «Product page
 * blocks» builder page (Site Settings → Catalog): benefits, reviews, where to
 * buy, FAQ, the question form, «Try also».
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="page product-page">
	<?php
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/product/hero', null, [ 'product' => vtf_product_data( get_the_ID() ) ] );
	}
	?>
	<div class="spacer-xl"></div>
	<?php vtf_render_builder(); ?>
</main>

<?php
get_footer();
