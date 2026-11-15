<?php
/**
 * All popups: one wrapper, panels by data-modal (markup: inc/_modals.php).
 * Open: [data-modal-open="name"] / VitModal.open(name).
 *
 * Content: Site Settings → Popups. A form popup without its CF7 form is left
 * out, so a button pointing at it does nothing rather than open an empty box.
 * The review popup is printed on product pages only (Site Settings → Catalog).
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_partner_form = (int) vtf_option( 'partner_form' );
$vtf_contact_form = (int) vtf_option( 'contact_form' );
$vtf_perks        = vtf_lines( vtf_option( 'partner_perks' ) );
$vtf_promo_on     = ! empty( vtf_option( 'promo_enabled' ) ) && vtf_image_id( vtf_option( 'promo_image' ) );
$vtf_toast_on     = ! empty( vtf_option( 'toast_enabled' ) ) && '' !== trim( (string) vtf_option( 'toast_title' ) );
$vtf_close        = __( 'Close', 'vitfit' );
?>
<div class="modal js-modal" aria-hidden="true">
	<div class="modal__backdrop js-modal-close"></div>
	<div class="modal__scroll" data-lenis-prevent>

		<?php if ( vtf_has_form( $vtf_partner_form ) ) { ?>
			<div class="modal__panel modal__panel--form" data-modal="partner" role="dialog" aria-modal="true" aria-labelledby="m-partner">
				<button class="btn-icon modal__close js-modal-close" type="button" aria-label="<?php echo esc_attr( $vtf_close ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
				<div class="modal__aside">
					<p class="h3" id="m-partner"><?php vtf_br( vtf_option( 'partner_title' ) ); ?></p>
					<?php if ( ! empty( $vtf_perks ) ) { ?>
						<ul class="modal__perks">
							<?php foreach ( $vtf_perks as $vtf_perk ) { ?>
								<li><?php vtf_sprite( 'i-check' ); ?> <?php echo esc_html( $vtf_perk ); ?></li>
							<?php } ?>
						</ul>
					<?php } ?>
					<?php vtf_flavor_bottle( vtf_option( 'partner_flavor' ), 'modal__bottle' ); ?>
				</div>
				<div class="modal__form">
					<?php vtf_form( $vtf_partner_form ); ?>
				</div>
			</div>
		<?php } ?>

		<?php if ( vtf_has_form( $vtf_contact_form ) ) { ?>
			<div class="modal__panel modal__panel--form" data-modal="contact" role="dialog" aria-modal="true" aria-labelledby="m-contact">
				<button class="btn-icon modal__close js-modal-close" type="button" aria-label="<?php echo esc_attr( $vtf_close ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
				<div class="modal__aside">
					<p class="h3" id="m-contact"><?php vtf_br( vtf_option( 'contact_title' ) ); ?></p>
					<?php if ( '' !== trim( (string) vtf_option( 'contact_text' ) ) ) { ?>
						<p class="text-md"><?php vtf_br( vtf_option( 'contact_text' ) ); ?></p>
					<?php } ?>
					<?php if ( ! empty( vtf_socials() ) ) { ?>
						<div class="modal__socials">
							<?php vtf_social_buttons( 'btn-icon btn-icon--solid' ); ?>
						</div>
					<?php } ?>
					<?php vtf_flavor_bottle( vtf_option( 'contact_flavor' ), 'modal__bottle' ); ?>
				</div>
				<div class="modal__form">
					<?php vtf_form( $vtf_contact_form ); ?>
				</div>
			</div>
		<?php } ?>

		<?php $vtf_review_form = (int) vtf_option( 'review_form' ); ?>
		<?php if ( vtf_current_product() && vtf_has_form( $vtf_review_form ) ) { ?>
			<?php $vtf_reviewed = vtf_product_data( vtf_current_product() ); ?>
			<div class="modal__panel modal__panel--form" data-modal="review" role="dialog" aria-modal="true" aria-labelledby="m-review">
				<button class="btn-icon modal__close js-modal-close" type="button" aria-label="<?php echo esc_attr( $vtf_close ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
				<div class="modal__aside">
					<p class="h3" id="m-review"><?php echo esc_html( sprintf( /* translators: %s: product name */ __( 'How do you like %s?', 'vitfit' ), $vtf_reviewed['name'] ) ); ?></p>
					<?php if ( '' !== trim( (string) vtf_option( 'review_text' ) ) ) { ?>
						<p class="text-md"><?php vtf_br( vtf_option( 'review_text' ) ); ?></p>
					<?php } ?>
					<?php
					vtf_bottle(
						[
							'flavor' => $vtf_reviewed['flavor'],
							'pack'   => $vtf_reviewed['pack'],
							'name'   => $vtf_reviewed['name'],
							'image'  => $vtf_reviewed['packshot'],
							'class'  => 'modal__bottle',
						]
					);
					?>
				</div>
				<div class="modal__form">
					<?php vtf_form( $vtf_review_form ); ?>
				</div>
			</div>
		<?php } ?>

		<div<?php echo vtf_flavor_attrs( 'modal__panel modal__panel--thanks', vtf_option( 'thanks_flavor' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?> data-modal="thanks" role="dialog" aria-modal="true" aria-labelledby="m-thanks">
			<button class="btn-icon modal__close js-modal-close" type="button" aria-label="<?php echo esc_attr( $vtf_close ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
			<?php vtf_flavor_bottle( vtf_option( 'thanks_flavor' ), 'modal__thanks-bottle' ); ?>
			<p class="h3" id="m-thanks"><?php echo esc_html( (string) ( vtf_option( 'thanks_title' ) ?: __( 'Thank you!', 'vitfit' ) ) ); ?></p>
			<?php if ( '' !== trim( (string) vtf_option( 'thanks_text' ) ) ) { ?>
				<p class="text-lg"><?php vtf_br( vtf_option( 'thanks_text' ) ); ?></p>
			<?php } ?>
			<button class="btn js-modal-close" type="button"><span class="btn__label"><?php echo esc_html( (string) ( vtf_option( 'thanks_button' ) ?: __( 'Great', 'vitfit' ) ) ); ?></span><span class="btn__tile"><?php vtf_sprite( 'i-arrow' ); ?></span></button>
		</div>

		<?php if ( $vtf_promo_on ) { ?>
			<div class="modal__panel modal__panel--promo<?php echo esc_attr( vtf_flavor_class( vtf_option( 'promo_color' ) ) ); ?>" data-modal="promo" data-auto-open="<?php echo esc_attr( (string) ( max( 0, (int) vtf_option( 'promo_delay' ) ) * 1000 ) ); ?>" role="dialog" aria-modal="true" aria-labelledby="m-promo">
				<button class="btn-icon modal__close js-modal-close" type="button" aria-label="<?php echo esc_attr( $vtf_close ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
				<?php vtf_picture( vtf_option( 'promo_image' ), null, [], 'large', [ 'class' => 'modal__promo-art' ] ); ?>
				<div class="modal__promo-body">
					<?php get_template_part( 'template-parts/global/badge', null, [ 'type' => (string) vtf_option( 'promo_badge' ) ] ); ?>
					<p class="h3" id="m-promo"><?php vtf_br( vtf_option( 'promo_title' ) ); ?></p>
					<?php if ( '' !== trim( (string) vtf_option( 'promo_text' ) ) ) { ?>
						<p class="text-md"><?php vtf_br( vtf_option( 'promo_text' ) ); ?></p>
					<?php } ?>
					<?php vtf_button( vtf_option( 'promo_button' ) ); ?>
				</div>
			</div>
		<?php } ?>

		<div class="modal__panel modal__panel--video" data-modal="video" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Video', 'vitfit' ); ?>">
			<button class="btn-icon btn-icon--solid modal__close js-modal-close" type="button" aria-label="<?php echo esc_attr( $vtf_close ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
			<video class="modal__video js-modal-video" controls playsinline></video>
		</div>

	</div>
</div>

<?php if ( $vtf_toast_on ) { ?>
	<?php $vtf_toast_flavor = vtf_option( 'toast_flavor' ); ?>
	<div<?php echo vtf_flavor_attrs( 'toast js-toast', $vtf_toast_flavor ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?> data-delay="<?php echo esc_attr( (string) ( max( 0, (int) vtf_option( 'toast_delay' ) ) * 1000 ) ); ?>" role="status">
		<button class="toast__close js-toast-close" type="button" aria-label="<?php echo esc_attr( $vtf_close ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
		<span class="toast__media"><?php vtf_flavor_bottle( $vtf_toast_flavor, '', false ); ?></span>
		<span class="toast__body">
			<span class="toast__title"><?php echo esc_html( (string) vtf_option( 'toast_title' ) ); ?></span>
			<?php if ( '' !== trim( (string) vtf_option( 'toast_text' ) ) ) { ?>
				<span class="toast__text"><?php echo esc_html( (string) vtf_option( 'toast_text' ) ); ?></span>
			<?php } ?>
			<?php vtf_link_arrow( vtf_option( 'toast_link' ) ); ?>
		</span>
	</div>
<?php } ?>
