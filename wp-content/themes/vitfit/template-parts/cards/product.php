<?php
/**
 * Product card (markup: `.product.js-product` in home-hits.php / catalog).
 * Hover media: a photo or a video of the moment of consumption; phones get a
 * blank source for the photo, so they never download it (as in the markup).
 *
 * @package vitfit
 *
 * @var array $args { product: vtf_product_data(), class: extra classes }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_product = (array) ( $args['product'] ?? [] );

if ( empty( $vtf_product['id'] ) ) {
	return;
}

$vtf_class = trim( ( $args['class'] ?? '' ) . ' product js-product' );
$vtf_pack  = vtf_product_format( $vtf_product );
$vtf_size  = trim( (string) $vtf_product['pack_size'] );
?>
<a<?php echo vtf_flavor_attrs( $vtf_class, $vtf_product['flavor'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?> href="<?php echo esc_url( $vtf_product['url'] ); ?>">
	<span class="product__media">
		<span class="product__bubbles" aria-hidden="true"><i></i><i></i><i></i></span>
		<?php if ( 'video' === $vtf_product['hover_type'] && '' !== $vtf_product['hover_video'] ) { ?>
			<video class="product__moment js-product-video" muted loop playsinline preload="none"<?php echo $vtf_product['hover_poster'] ? ' poster="' . esc_url( vtf_image_url( $vtf_product['hover_poster'], 'large' ) ) . '"' : ''; ?> data-src="<?php echo esc_url( $vtf_product['hover_video'] ); ?>"></video>
		<?php } elseif ( 'image' === $vtf_product['hover_type'] && $vtf_product['hover_image'] ) { ?>
			<picture class="product__moment"><source media="(max-width: 1199.98px)" srcset="data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw=="><?php vtf_image( $vtf_product['hover_image'], [ 'alt' => (string) get_post_meta( $vtf_product['hover_image'], '_wp_attachment_image_alt', true ) ], 'large' ); ?></picture>
		<?php } ?>
		<?php
		vtf_bottle(
			[
				'flavor' => $vtf_product['flavor'],
				'pack'   => $vtf_product['pack'],
				'name'   => $vtf_product['name'],
				'image'  => $vtf_product['packshot'],
				'class'  => 'product__bottle',
				'size'   => 'large',
			]
		);
		get_template_part( 'template-parts/global/badge', null, [ 'type' => $vtf_product['badge'], 'class' => 'product__badge' ] );
		?>
	</span>
	<span class="product__info">
		<?php if ( '' !== $vtf_pack || '' !== $vtf_size ) { ?>
			<span class="product__meta"><?php if ( '' !== $vtf_pack ) { ?><span><?php echo esc_html( $vtf_pack ); ?></span><?php } ?><?php if ( '' !== $vtf_size ) { ?><span><?php echo esc_html( sprintf( /* translators: %s: pack size, e.g. "6 шт" */ __( 'Pack · %s', 'vitfit' ), $vtf_size ) ); ?></span><?php } ?></span>
		<?php } ?>
		<span class="h5 product__name"><?php echo esc_html( $vtf_product['name'] ); ?></span>
		<?php if ( '' !== $vtf_product['desc'] ) { ?>
			<span class="text-md product__desc"><?php echo esc_html( $vtf_product['desc'] ); ?></span>
		<?php } ?>
	</span>
</a>
