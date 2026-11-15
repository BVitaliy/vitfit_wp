<?php
/**
 * Image output helpers.
 *
 * The markup's <picture> has a desktop and a mobile slot. In WordPress the
 * generated sizes are already WebP (functions/media.php), so the picture keeps
 * only the mobile <source> (optional crop) and an <img> with srcset.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attachment ID from an ACF image value (array or ID).
 *
 * @param mixed $image ACF image array or attachment ID.
 */
function vtf_image_id( $image ): int {
	return is_array( $image ) ? (int) ( $image['ID'] ?? 0 ) : (int) $image;
}

/**
 * <img> for an ACF image — srcset, intrinsic size, lazy by default.
 *
 * @param mixed  $image ACF image array or attachment ID.
 * @param array  $attrs Extra attributes: class, alt, loading, sizes…
 * @param string $size  Registered size.
 */
function vtf_image( $image, array $attrs = [], string $size = 'full' ): void {
	$id = vtf_image_id( $image );

	if ( ! $id ) {
		return;
	}

	echo wp_get_attachment_image(
		$id,
		$size,
		false,
		array_merge(
			[
				'loading'  => 'lazy',
				'decoding' => 'async',
			],
			$attrs
		)
	);
}

/**
 * URL of an ACF image in a given size.
 *
 * @param mixed  $image ACF image array or attachment ID.
 * @param string $size  Registered size.
 */
function vtf_image_url( $image, string $size = 'full' ): string {
	$id = vtf_image_id( $image );

	return $id ? (string) wp_get_attachment_image_url( $id, $size ) : '';
}

/**
 * The markup's <picture>: optional mobile crop (≤767px) + the main image.
 *
 * @param mixed  $image         Main image (ACF array or ID).
 * @param mixed  $mobile        Mobile crop; falls back to the main image.
 * @param array  $attrs         Attributes for the <img> (class, alt, loading, fetchpriority…).
 * @param string $size          Registered size for the main image.
 * @param array  $picture_attrs Attributes for the <picture> (class, data-parallax-target…).
 */
function vtf_picture( $image, $mobile = null, array $attrs = [], string $size = 'full', array $picture_attrs = [] ): void {
	if ( ! vtf_image_id( $image ) ) {
		return;
	}

	$mobile_url = vtf_image_url( $mobile, 'large' );
	$html       = '';

	foreach ( $picture_attrs as $name => $value ) {
		$html .= true === $value ? ' ' . esc_attr( $name ) : sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( (string) $value ) );
	}
	?>
	<picture<?php echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>>
		<?php if ( '' !== $mobile_url ) { ?>
			<source srcset="<?php echo esc_url( $mobile_url ); ?>" media="(max-width: 767px)">
		<?php } ?>
		<?php vtf_image( $image, $attrs, $size ); ?>
	</picture>
	<?php
}

/**
 * Inline an uploaded SVG (sanitized) so CSS can recolour it via currentColor;
 * raster icons fall back to <img>.
 *
 * @param mixed $image ACF image array or attachment ID.
 * @param array $attrs Attributes for the <img> fallback.
 */
function vtf_inline_icon( $image, array $attrs = [] ): void {
	$id = vtf_image_id( $image );

	if ( ! $id ) {
		return;
	}

	if ( 'image/svg+xml' === get_post_mime_type( $id ) ) {
		$path = get_attached_file( $id );
		$raw  = $path && is_readable( $path ) ? file_get_contents( $path ) : false; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local upload.

		if ( false !== $raw ) {
			$clean = ( new VTF_Svg_Sanitizer() )->sanitize( $raw );

			if ( '' !== $clean ) {
				echo preg_replace( '/<svg\b/', '<svg focusable="false" aria-hidden="true"', $clean, 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized against an allow-list.

				return;
			}
		}
	}

	vtf_image( $id, $attrs, 'thumbnail' );
}

/**
 * Site logo: the uploaded one from Site Settings → Header (an SVG is inlined,
 * so currentColor takes the header / footer colour); empty — the markup's
 * text logo (`.logo__mark` + `.logo__tag`). The surrounding link carries the label.
 *
 * @param bool $lazy Lazy-load a raster logo (the footer one).
 */
function vtf_logo( bool $lazy = false ): void {
	$id = vtf_image_id( vtf_option( 'logo' ) );

	if ( ! $id ) {
		?>
		<span class="logo__mark">VIT-<br>FIT</span>
		<span class="logo__tag"><?php echo wp_kses( __( 'Live<br>kombucha', 'vitfit' ), [ 'br' => [] ] ); ?></span>
		<?php
		return;
	}

	if ( 'image/svg+xml' === get_post_mime_type( $id ) ) {
		vtf_inline_icon( $id );

		return;
	}

	vtf_image(
		$id,
		[
			'alt'     => '',
			'loading' => $lazy ? 'lazy' : 'eager',
		]
	);
}
