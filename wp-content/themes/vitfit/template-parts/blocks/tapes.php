<?php
/**
 * Block «USP tapes» (markup: template-parts/home-tapes.php): two crossing
 * marquee tapes — the back one repeats a line, the front one lists the USPs.
 *
 * @package vitfit
 *
 * @var array $args Builder row.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_items = vtf_lines( $args['items'] ?? '' );
$vtf_back  = trim( (string) ( $args['back_text'] ?? '' ) );

if ( empty( $vtf_items ) ) {
	return;
}
?>
<section class="tapes" aria-label="<?php esc_attr_e( 'Our advantages', 'vitfit' ); ?>">
	<?php if ( '' !== $vtf_back ) { ?>
		<div class="tape tape--back" aria-hidden="true">
			<div class="tape__track"><span><?php echo esc_html( $vtf_back ); ?>&nbsp;</span><span><?php echo esc_html( $vtf_back ); ?>&nbsp;</span></div>
		</div>
	<?php } ?>
	<div class="tape tape--front">
		<div class="tape__track">
			<?php for ( $vtf_copy = 0; $vtf_copy < 2; $vtf_copy++ ) { ?>
				<ul<?php echo $vtf_copy ? ' aria-hidden="true"' : ''; ?>>
					<?php foreach ( $vtf_items as $vtf_item ) { ?>
						<li><?php vtf_sprite( 'i-star' ); ?><?php echo esc_html( $vtf_item ); ?></li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>
	</div>
</section>
