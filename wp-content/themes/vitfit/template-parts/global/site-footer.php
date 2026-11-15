<?php
/**
 * Footer (markup: inc/_footer.php).
 *
 * Columns = Appearance → Menus → «Footer — column 1…3» (column title = the
 * menu's name), legal links = «Footer — legal links». Lead, contacts, button,
 * socials, copyright: Site Settings → Footer / Contacts. Empty parts are left out.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_lead      = trim( (string) vtf_option( 'footer_lead' ) );
$vtf_phone     = trim( (string) vtf_option( 'contact_phone' ) );
$vtf_email     = trim( (string) vtf_option( 'contact_email' ) );
$vtf_cta       = vtf_option( 'footer_cta' );
$vtf_copyright = str_replace( '{year}', wp_date( 'Y' ), trim( (string) vtf_option( 'copyright' ) ) );
$vtf_legal     = vtf_menu_tree( 'legal_menu' );
$vtf_columns   = [];

foreach ( [ 'footer_menu_1', 'footer_menu_2', 'footer_menu_3' ] as $vtf_location ) {
	$vtf_items = vtf_menu_tree( $vtf_location );

	if ( ! empty( $vtf_items ) ) {
		$vtf_columns[] = [
			'title' => vtf_menu_name( $vtf_location ),
			'items' => $vtf_items,
		];
	}
}
?>
<footer class="footer">
	<div class="container">
		<div class="footer__top">
			<div class="footer__brand">
				<a class="logo" href="<?php echo esc_url( vtf_home_url() ); ?>" aria-label="<?php esc_attr_e( 'VIT-FIT — home', 'vitfit' ); ?>">
					<?php vtf_logo( true ); ?>
				</a>
				<?php if ( '' !== $vtf_lead ) { ?>
					<p class="footer__lead"><?php echo esc_html( $vtf_lead ); ?></p>
				<?php } ?>
				<?php if ( '' !== $vtf_phone || '' !== $vtf_email ) { ?>
					<ul class="footer__contacts">
						<?php if ( '' !== $vtf_phone ) { ?>
							<li><a href="<?php echo esc_attr( vtf_phone_href( $vtf_phone ) ); ?>"><?php echo esc_html( $vtf_phone ); ?></a></li>
						<?php } ?>
						<?php if ( '' !== $vtf_email ) { ?>
							<li><a href="<?php echo esc_attr( 'mailto:' . antispambot( $vtf_email ) ); ?>"><?php echo esc_html( antispambot( $vtf_email ) ); ?></a></li>
						<?php } ?>
					</ul>
				<?php } ?>
				<?php vtf_button( $vtf_cta, 'btn fl--gold-apricot' ); ?>
				<?php if ( ! empty( vtf_socials() ) ) { ?>
					<div class="footer__socials">
						<?php vtf_social_buttons( 'btn-icon btn-icon--light' ); ?>
					</div>
				<?php } ?>
			</div>
			<?php foreach ( $vtf_columns as $vtf_column ) { ?>
				<div>
					<?php if ( '' !== $vtf_column['title'] ) { ?>
						<p class="footer__title"><?php echo esc_html( $vtf_column['title'] ); ?></p>
					<?php } ?>
					<ul class="footer__list">
						<?php foreach ( $vtf_column['items'] as $vtf_item ) { ?>
							<li><a<?php echo vtf_menu_link_attrs( $vtf_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php echo esc_html( $vtf_item['title'] ); ?></a></li>
						<?php } ?>
					</ul>
				</div>
			<?php } ?>
		</div>
		<div class="footer__word js-footer-word" aria-hidden="true"><span>V</span><span>I</span><span>T</span><span>-</span><span>F</span><span>I</span><span>T</span></div>
		<div class="footer__bottom">
			<?php if ( '' !== $vtf_copyright ) { ?>
				<p><?php echo esc_html( $vtf_copyright ); ?></p>
			<?php } ?>
			<?php if ( ! empty( $vtf_legal ) ) { ?>
				<nav aria-label="<?php esc_attr_e( 'Legal information', 'vitfit' ); ?>">
					<?php foreach ( $vtf_legal as $vtf_item ) { ?>
						<a<?php echo vtf_menu_link_attrs( $vtf_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php echo esc_html( $vtf_item['title'] ); ?></a>
					<?php } ?>
				</nav>
			<?php } ?>
			<a class="footer__dev" href="https://redstone.agency/" target="_blank" rel="noopener"<?php echo vtf_aria_label( __( 'Website development: Redstone', 'vitfit' ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>>
				<?php esc_html_e( 'Website development:', 'vitfit' ); ?>
				<img src="<?php echo esc_url( vtf_asset( 'img/redstone.svg' ) ); ?>" alt="Redstone" width="83" height="12" loading="lazy">
			</a>
		</div>
	</div>
</footer>
