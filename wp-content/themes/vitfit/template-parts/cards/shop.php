<?php
/**
 * Partner tile `.shop` (markup: home-where.php): logo or the name as a
 * wordmark, tile colour, arrow. Without a link the tile is not clickable.
 *
 * @package vitfit
 *
 * @var array $args { post: WP_Post }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_shop = $args['post'] ?? null;

if ( ! $vtf_shop instanceof WP_Post ) {
	return;
}

$vtf_name  = (string) get_the_title( $vtf_shop );
$vtf_logo  = vtf_image_id( get_field( 'logo', $vtf_shop->ID ) );
$vtf_url   = (string) get_field( 'link', $vtf_shop->ID );
$vtf_color = (string) get_field( 'color', $vtf_shop->ID );
$vtf_color = in_array( $vtf_color, [ 'yellow', 'pink', 'green', 'blue', 'purple' ], true ) ? $vtf_color : 'green';
$vtf_tag   = '' !== $vtf_url ? 'a' : 'span';
/* translators: %s: shop name */
$vtf_label = sprintf( __( 'Vit-Fit at %s', 'vitfit' ), $vtf_name );
?>
<<?php echo esc_html( $vtf_tag ); ?> class="shop" style="--shop: var(--c-<?php echo esc_attr( $vtf_color ); ?>-l)"<?php if ( '' !== $vtf_url ) { ?> href="<?php echo esc_url( $vtf_url ); ?>" target="_blank" rel="noopener"<?php echo vtf_aria_label( $vtf_label, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?><?php } ?>>
	<?php if ( $vtf_logo ) { ?>
		<?php vtf_image( $vtf_logo, [ 'class' => 'shop__logo', 'alt' => $vtf_name ], 'medium' ); ?>
	<?php } else { ?>
		<span class="shop__word"><?php echo esc_html( $vtf_name ); ?></span>
	<?php } ?>
	<?php if ( '' !== $vtf_url ) { ?>
		<?php vtf_sprite( 'i-arrow', '0 0 24 24', 'shop__arrow' ); ?>
	<?php } ?>
</<?php echo esc_html( $vtf_tag ); ?>>
