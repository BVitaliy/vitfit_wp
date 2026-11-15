<?php
/**
 * One live-search suggestion (markup: `.search__result`): a product with its
 * bottle and formats, or a post with its photo.
 *
 * @package vitfit
 *
 * @var array $args { post: WP_Post }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_post = $args['post'] ?? null;

if ( ! $vtf_post instanceof WP_Post ) {
	return;
}

$vtf_is_product = 'vtf_product' === $vtf_post->post_type;
$vtf_product    = $vtf_is_product ? vtf_product_data( $vtf_post->ID ) : null;
?>
<a class="search__result" href="<?php echo esc_url( get_permalink( $vtf_post ) ); ?>">
	<?php if ( $vtf_product ) { ?>
		<span<?php echo vtf_flavor_attrs( 'search__result-media', $vtf_product['flavor'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>>
			<?php
			vtf_bottle(
				[
					'flavor' => $vtf_product['flavor'],
					'pack'   => $vtf_product['pack'],
					'image'  => $vtf_product['packshot'],
					'size'   => 'thumbnail',
				]
			);
			?>
		</span>
		<span class="search__result-body"><span class="search__result-name"><?php echo esc_html( $vtf_product['name'] ); ?></span><span class="search__result-meta"><?php echo esc_html( vtf_product_format( $vtf_product ) ); ?></span></span>
	<?php } else { ?>
		<span class="search__result-media"><?php vtf_image( get_post_thumbnail_id( $vtf_post ), [ 'alt' => '' ], 'thumbnail' ); ?></span>
		<span class="search__result-body"><span class="search__result-name"><?php echo esc_html( get_the_title( $vtf_post ) ); ?></span><span class="search__result-meta"><?php esc_html_e( 'Blog', 'vitfit' ); ?></span></span>
	<?php } ?>
</a>
