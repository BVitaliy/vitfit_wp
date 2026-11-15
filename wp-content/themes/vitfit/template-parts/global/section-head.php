<?php
/**
 * `.section-head`: title (with its marker colour) left, a text or a link right.
 *
 * @package vitfit
 *
 * @var array $args {
 *     @type array  $row   Builder row (title, title_tag, title_mark).
 *     @type string $style Title inline style, e.g. '--title-w: 9em'.
 *     @type string $text  Text on the right (.section-head__text).
 *     @type mixed  $link  Button group → .link-arrow on the right.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_row   = (array) ( $args['row'] ?? [] );
$vtf_title = trim( (string) ( $vtf_row['title'] ?? '' ) );
$vtf_text  = trim( (string) ( $args['text'] ?? '' ) );
$vtf_link  = $args['link'] ?? null;

if ( '' === $vtf_title && '' === $vtf_text && ! vtf_has_button( $vtf_link ) ) {
	return;
}

$vtf_tag   = vtf_title_tag( $vtf_row, 'h2' );
$vtf_style = (string) ( $args['style'] ?? '' );
?>
<div class="section-head">
	<?php if ( '' !== $vtf_title ) { ?>
		<div class="section-head__main">
			<<?php echo esc_html( $vtf_tag ); ?> class="h2 section-head__title"<?php echo '' !== $vtf_style ? ' style="' . esc_attr( $vtf_style ) . '"' : ''; ?>><?php vtf_title_html( $vtf_title, (string) ( $vtf_row['title_mark'] ?? '' ) ); ?></<?php echo esc_html( $vtf_tag ); ?>>
		</div>
	<?php } ?>
	<?php if ( '' !== $vtf_text ) { ?>
		<p class="text-lg section-head__text"><?php vtf_br( $vtf_text ); ?></p>
	<?php } ?>
	<?php vtf_link_arrow( $vtf_link ); ?>
</div>
