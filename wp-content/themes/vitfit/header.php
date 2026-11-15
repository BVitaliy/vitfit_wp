<?php
/**
 * Document head and the site header (markup: inc/_top.php + inc/_header.php).
 * The SVG sprite and the popups open the body, as in the markup.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
wp_body_open();

get_template_part( 'template-parts/global/sprite' );
get_template_part( 'template-parts/global/modals' );
get_template_part( 'template-parts/global/site-header' );
