<?php
/**
 * Block «Where to buy» (markup: template-parts/home-where.php): partner logos
 * → the Vit-Fit page in each chain (new tab). Shops: «Where to buy» in the
 * admin (logo, link, tile colour); the block shows the picked ones or all.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_shops = vtf_card_posts( 'vtf_shop', $args['shops'] ?? [] );

if ( empty( $vtf_shops ) ) {
	return;
}

// All logos in one query instead of one per tile.
vtf_prime_posts( array_map( static fn( $shop ) => (int) get_post_meta( $shop->ID, 'logo', true ), $vtf_shops ) );
?>
<section class="where" id="where">
	<div class="container">
		<?php get_template_part( 'template-parts/global/section-head', null, [ 'row' => $args, 'style' => '--title-w: 8em', 'link' => $args['link'] ?? null ] ); ?>
		<ul class="where__grid">
			<?php foreach ( $vtf_shops as $vtf_shop ) { ?>
				<li><?php get_template_part( 'template-parts/cards/shop', null, [ 'post' => $vtf_shop ] ); ?></li>
			<?php } ?>
		</ul>
	</div>
</section>
