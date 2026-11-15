<?php
/**
 * Catalog category = the product list (markup: category.php → catalog-list.php
 * + seo-block.php). Texts: Products → Catalog categories → the category.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_term = get_queried_object();

get_header();
?>

<main class="page">
	<?php get_template_part( 'template-parts/catalog/list', null, [ 'term' => $vtf_term ] ); ?>
	<div class="spacer-xl"></div>
	<?php
	$vtf_seo = [
		'title'   => (string) get_field( 'seo_title', $vtf_term ),
		'visible' => (string) get_field( 'seo_visible', $vtf_term ),
		'hidden'  => (string) get_field( 'seo_hidden', $vtf_term ),
		'index'   => 0,
	];

	if ( '' !== trim( $vtf_seo['visible'] . $vtf_seo['hidden'] ) ) {
		get_template_part( 'template-parts/blocks/seo', null, $vtf_seo );
		echo '<div class="spacer-xl"></div>';
	}
	?>
</main>

<?php
get_footer();
