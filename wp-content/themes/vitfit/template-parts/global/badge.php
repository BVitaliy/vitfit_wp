<?php
/**
 * Product badge: «Хіт» (yellow, star) / «Новинка» (green, sparkle) — always with an icon.
 *
 * @package vitfit
 *
 * @var array $args { type: hit|new, class: extra classes, style: inline style }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_type = (string) ( $args['type'] ?? '' );

if ( ! in_array( $vtf_type, [ 'hit', 'new' ], true ) ) {
	return;
}

$vtf_class = trim( 'badge badge--' . $vtf_type . ' ' . ( $args['class'] ?? '' ) );
$vtf_style = (string) ( $args['style'] ?? '' );
?>
<span class="<?php echo esc_attr( $vtf_class ); ?>"<?php echo '' !== $vtf_style ? ' style="' . esc_attr( $vtf_style ) . '"' : ''; ?>><?php vtf_sprite( 'hit' === $vtf_type ? 'i-star' : 'i-sparkle' ); ?><?php echo 'hit' === $vtf_type ? esc_html__( 'Hit', 'vitfit' ) : esc_html__( 'New', 'vitfit' ); ?></span>
