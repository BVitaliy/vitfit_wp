<?php
/**
 * Block «Product: question form» (markup: template-parts/product-ask.php):
 * title + text on the left, the CF7 form in the beige box. On a product page
 * [vtf_product product] adds the product to the mail; elsewhere the form works
 * as a general question form.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_form = (int) ( $args['form'] ?? 0 );

if ( ! vtf_has_form( $vtf_form ) ) {
	return;
}
?>
<section class="ask">
	<div class="container">
		<div class="ask__box">
			<div class="ask__text">
				<?php if ( ! empty( $args['title'] ) ) { ?>
					<?php $vtf_tag = vtf_title_tag( $args, 'h2' ); ?>
					<<?php echo esc_html( $vtf_tag ); ?> class="h2 ask__title"><?php vtf_title_html( (string) $args['title'], (string) ( $args['title_mark'] ?? '' ) ); ?></<?php echo esc_html( $vtf_tag ); ?>>
				<?php } ?>
				<?php if ( ! empty( $args['text'] ) ) { ?>
					<p class="text-lg"><?php vtf_br( $args['text'] ); ?></p>
				<?php } ?>
			</div>
			<div class="ask__form">
				<?php vtf_form( $vtf_form ); ?>
			</div>
		</div>
	</div>
</section>
