<?php
/**
 * Block «FAQ» (markup: template-parts/home-faq.php): title, text, buttons on
 * the left; the accordion on the right, all closed (app-faq.js).
 * Questions: «FAQ» in the admin — the picked ones, or all by «Order».
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_limit = (int) ( $args['limit'] ?? 0 );
$vtf_items = vtf_faq_items( vtf_ids( $args['questions'] ?? [] ), $vtf_limit > 0 ? $vtf_limit : -1 );

if ( empty( $vtf_items ) ) {
	return;
}
?>
<section class="faq-block" id="faq">
	<div class="container faq-block__grid">
		<div class="faq-block__aside">
			<?php vtf_title( $args, 'h2', 'h2' ); ?>
			<?php if ( ! empty( $args['text'] ) ) { ?>
				<p class="text-lg"><?php vtf_br( $args['text'] ); ?></p>
			<?php } ?>
			<?php if ( vtf_has_button( $args['button'] ?? null ) || vtf_has_button( $args['link'] ?? null ) ) { ?>
				<div class="faq-block__btns">
					<?php vtf_button( $args['button'] ?? null, 'btn' . vtf_flavor_class( $args['button_color'] ?? '' ) ); ?>
					<?php vtf_link_arrow( $args['link'] ?? null, 'link-arrow link-arrow--lg' ); ?>
				</div>
			<?php } ?>
		</div>
		<div class="faq">
			<?php foreach ( $vtf_items as $vtf_item ) { ?>
				<div class="faq__item"><button class="faq__head js-faq-head" type="button" aria-expanded="false"><span class="h5"><?php echo esc_html( $vtf_item['question'] ); ?></span><span class="faq__icon"><?php vtf_sprite( 'i-plus' ); ?></span></button><div class="faq__body"><div class="faq__inner"><?php vtf_faq_answer( $vtf_item['answer'] ); ?></div></div></div>
			<?php } ?>
		</div>
	</div>
</section>
