<?php
/**
 * Top bar + sticky header + mobile menu + search (markup: inc/_header.php).
 *
 * Menu: Appearance → Menus → «Main menu»; a top item with sub-items opens the
 * dropdown (desktop) / accordion (mobile). Texts, button, socials, the
 * dropdown promo: Site Settings → Header / Contacts.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_menu       = vtf_menu_tree( 'header_menu' );
$vtf_topbar     = trim( (string) vtf_option( 'topbar_text' ) );
$vtf_topbar_add = trim( (string) vtf_option( 'topbar_more' ) );
$vtf_topbar_btn = vtf_option( 'topbar_link' );
$vtf_cta        = vtf_option( 'header_cta' );
$vtf_cta_link   = vtf_has_button( $vtf_cta ) ? vtf_link( $vtf_cta ) : null;
$vtf_promo_id   = (int) vtf_option( 'menu_promo_product' );
$vtf_promo      = $vtf_promo_id && 'publish' === get_post_status( $vtf_promo_id ) ? vtf_product_data( $vtf_promo_id ) : null;
$vtf_promo_text = trim( (string) vtf_option( 'menu_promo_title' ) );
$vtf_languages  = vtf_languages();
$vtf_socials    = array_filter( vtf_socials(), static fn( $social ) => 'instagram' === $social['icon'] || 'tiktok' === $social['icon'] );
?>
<?php if ( '' !== $vtf_topbar ) { ?>
	<div class="topbar js-topbar">
		<p class="topbar__text"><span class="topbar__spark">✦</span> <span><?php echo esc_html( $vtf_topbar ); ?><?php if ( '' !== $vtf_topbar_add ) { ?><span class="topbar__more"> <?php echo esc_html( $vtf_topbar_add ); ?></span><?php } ?></span><?php if ( vtf_has_button( $vtf_topbar_btn ) ) { ?> <a class="topbar__link"<?php echo vtf_link_attrs( vtf_link( $vtf_topbar_btn ), $vtf_topbar_btn['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php echo esc_html( $vtf_topbar_btn['label'] ); ?></a><?php } ?></p>
	</div>
<?php } ?>

<header class="header js-header <?php echo esc_attr( vtf_header_class() ); ?>">
	<div class="header__wrap container">
		<div class="header__bar">
			<a class="logo" href="<?php echo esc_url( vtf_home_url() ); ?>" aria-label="<?php esc_attr_e( 'VIT-FIT — home', 'vitfit' ); ?>">
				<?php vtf_logo(); ?>
			</a>

			<?php if ( ! empty( $vtf_menu ) ) { ?>
				<nav class="header__nav" aria-label="<?php esc_attr_e( 'Main menu', 'vitfit' ); ?>">
					<ul class="header__menu">
						<?php foreach ( $vtf_menu as $vtf_item ) { ?>
							<?php if ( empty( $vtf_item['children'] ) ) { ?>
								<li><a<?php echo vtf_menu_link_attrs( $vtf_item, 'header__link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php echo esc_html( $vtf_item['title'] ); ?></a></li>
							<?php } else { ?>
								<li class="dropdown">
									<a<?php echo vtf_menu_link_attrs( $vtf_item, 'header__link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php echo esc_html( $vtf_item['title'] ); ?> <?php vtf_sprite( 'i-chevron', '0 0 12 8' ); ?></a>
									<div class="dropdown__panel">
										<ul class="dropdown__list">
											<?php foreach ( $vtf_item['children'] as $vtf_child ) { ?>
												<li><a<?php echo vtf_menu_link_attrs( $vtf_child, 'dropdown__item' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php echo esc_html( $vtf_child['title'] ); ?> <?php vtf_sprite( 'i-arrow' ); ?></a></li>
											<?php } ?>
										</ul>
										<?php if ( $vtf_promo ) { ?>
											<a class="dropdown__promo" href="<?php echo esc_url( $vtf_promo['url'] ); ?>">
												<?php
												vtf_bottle(
													[
														'flavor' => $vtf_promo['flavor'],
														'pack'   => $vtf_promo['pack'],
														'name'   => $vtf_promo['name'],
														'image'  => $vtf_promo['packshot'],
														'size'   => 'medium_large',
													]
												);
												get_template_part( 'template-parts/global/badge', null, [ 'type' => $vtf_promo['badge'], 'style' => 'position:relative;align-self:flex-start;margin-bottom:10px' ] );
												?>
												<span class="dropdown__promo-title"><?php echo esc_html( '' !== $vtf_promo_text ? $vtf_promo_text : $vtf_promo['name'] ); ?></span>
											</a>
										<?php } ?>
									</div>
								</li>
							<?php } ?>
						<?php } ?>
					</ul>
				</nav>
			<?php } ?>

			<div class="header__actions">
				<?php if ( $vtf_cta_link ) { ?>
					<a class="btn btn--plain header__cta"<?php echo vtf_link_attrs( $vtf_cta_link, $vtf_cta['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><span class="btn__label"><?php echo esc_html( $vtf_cta['label'] ); ?></span></a>
				<?php } ?>
				<button class="btn-icon js-search-open" type="button" aria-label="<?php esc_attr_e( 'Search', 'vitfit' ); ?>"><?php vtf_sprite( 'i-search' ); ?></button>
				<?php if ( count( $vtf_languages ) > 1 ) { ?>
					<div class="lang">
						<button class="lang__btn" type="button" aria-label="<?php esc_attr_e( 'Language', 'vitfit' ); ?>"><?php vtf_sprite( 'i-globe' ); ?> <?php echo esc_html( vtf_language_label() ); ?></button>
						<div class="lang__list"><?php vtf_language_links(); ?></div>
					</div>
				<?php } ?>
				<?php foreach ( $vtf_socials as $vtf_social ) { ?>
					<a class="btn-icon header__social" href="<?php echo esc_url( $vtf_social['url'] ); ?>" target="_blank" rel="noopener"<?php echo vtf_aria_label( $vtf_social['label'], true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php vtf_sprite( 'i-' . $vtf_social['icon'] ); ?></a>
				<?php } ?>
				<button class="btn-icon burger header__burger js-burger" type="button" aria-label="<?php esc_attr_e( 'Menu', 'vitfit' ); ?>" aria-expanded="false"><span></span></button>
			</div>
		</div>
	</div>
</header>

<div class="mobile-menu js-mobile-menu" data-lenis-prevent>
	<?php if ( ! empty( $vtf_menu ) ) { ?>
		<ul class="mobile-menu__list">
			<?php foreach ( $vtf_menu as $vtf_item ) { ?>
				<?php if ( empty( $vtf_item['children'] ) ) { ?>
					<li><a<?php echo vtf_menu_link_attrs( $vtf_item, 'mobile-menu__link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php echo esc_html( $vtf_item['title'] ); ?></a></li>
				<?php } else { ?>
					<li>
						<button class="mobile-menu__link js-mm-toggle" type="button"><?php echo esc_html( $vtf_item['title'] ); ?> <?php vtf_sprite( 'i-chevron', '0 0 12 8' ); ?></button>
						<div class="mobile-menu__sub">
							<div>
								<?php foreach ( $vtf_item['children'] as $vtf_child ) { ?>
									<a<?php echo vtf_menu_link_attrs( $vtf_child ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php echo esc_html( $vtf_child['title'] ); ?></a>
								<?php } ?>
							</div>
						</div>
					</li>
				<?php } ?>
			<?php } ?>
		</ul>
	<?php } ?>
	<div class="mobile-menu__bottom">
		<?php vtf_button( $vtf_cta ); ?>
		<?php foreach ( $vtf_socials as $vtf_social ) { ?>
			<a class="btn-icon" href="<?php echo esc_url( $vtf_social['url'] ); ?>" target="_blank" rel="noopener"<?php echo vtf_aria_label( $vtf_social['label'], true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>><?php vtf_sprite( 'i-' . $vtf_social['icon'] ); ?></a>
		<?php } ?>
		<?php if ( count( $vtf_languages ) > 1 ) { ?>
			<div class="mobile-menu__langs"><?php vtf_language_links(); ?></div>
		<?php } ?>
	</div>
</div>

<?php get_template_part( 'template-parts/global/search' ); ?>
