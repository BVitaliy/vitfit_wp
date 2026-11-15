<?php
/**
 * Block «Blog posts» (markup: template-parts/home-blog.php): the picked posts
 * or the three latest. Below 992 a Swiper.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_posts = vtf_card_posts( 'post', $args['posts'] ?? [], 3 );

if ( empty( $vtf_posts ) ) {
	return;
}
?>
<section class="blog">
	<div class="container">
		<?php get_template_part( 'template-parts/global/section-head', null, [ 'row' => $args, 'link' => $args['link'] ?? null ] ); ?>
		<div class="swiper grid-slider js-grid-slider" data-slider-below="992">
			<div class="swiper-wrapper blog__grid">
				<?php foreach ( $vtf_posts as $vtf_post ) { ?>
					<?php vtf_post_card( $vtf_post, 'swiper-slide' ); ?>
				<?php } ?>
			</div>
			<div class="grid-slider__pagination"></div>
		</div>
	</div>
</section>
