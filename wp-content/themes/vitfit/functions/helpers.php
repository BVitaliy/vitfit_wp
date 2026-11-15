<?php
/**
 * Template helpers.
 *
 * These are called from templates rather than hooked, so they stay plain
 * functions.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Popups a link / button may open ([data-modal-open], template-parts/global/modals.php).
 */
const VTF_LINK_MODALS = [ 'contact', 'partner' ];

/**
 * Site home URL in the current language (qTranslate filters home_url()).
 */
function vtf_home_url(): string {
	return home_url( '/' );
}

/**
 * Theme asset URL.
 *
 * @param string $path Path relative to the theme root, e.g. 'img/redstone.svg'.
 */
function vtf_asset( string $path ): string {
	return VTF_URI . '/' . ltrim( $path, '/' );
}

/**
 * Cache-busting version for a theme asset: its modification time, so every
 * edit to a CSS/JS file changes the URL and browsers fetch the new copy.
 * Falls back to VTF_VER if the file is missing.
 *
 * @param string $path Path relative to the theme root.
 */
function vtf_asset_ver( string $path ): string {
	$file = VTF_DIR . '/' . ltrim( $path, '/' );

	return is_readable( $file ) ? (string) filemtime( $file ) : VTF_VER;
}

/**
 * Inline an SVG from the theme's img/ directory (the markup's
 * `include 'img/logo/…svg'`). Files are read once per request.
 *
 * @param string $path Path relative to img/, e.g. 'logo/logo-v1.svg'.
 */
function vtf_icon( string $path ): void {
	static $cache = [];

	if ( ! isset( $cache[ $path ] ) ) {
		if ( str_contains( $path, '..' ) ) {
			return;
		}

		$file = VTF_DIR . '/img/' . ltrim( $path, '/' );

		if ( ! is_readable( $file ) || 'svg' !== strtolower( pathinfo( $file, PATHINFO_EXTENSION ) ) ) {
			$cache[ $path ] = '';
		} else {
			$cache[ $path ] = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme asset.
		}
	}

	if ( ! empty( $cache[ $path ] ) ) {
		echo $cache[ $path ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted theme SVG file.
	}
}

/**
 * A symbol from the inline sprite (template-parts/global/sprite.php):
 * `<svg viewBox="…"><use href="#i-arrow" /></svg>`, exactly as in the markup.
 *
 * @param string $id      Symbol id, e.g. 'i-arrow'.
 * @param string $viewbox viewBox of the symbol (icons are 24, feature icons 32).
 * @param string $class   Optional class on the <svg>.
 */
function vtf_sprite( string $id, string $viewbox = '0 0 24 24', string $class = '' ): void {
	printf(
		'<svg%s viewBox="%s"><use href="#%s" /></svg>',
		'' !== $class ? ' class="' . esc_attr( $class ) . '"' : '',
		esc_attr( $viewbox ),
		esc_attr( $id )
	);
}

/**
 * The design's button: `.btn` = label + arrow tile (markup: `.btn__label` + `.btn__tile`).
 * Prints nothing without a label or a usable link.
 *
 * @param mixed  $btn   Button group field value ({ label, type, page, url, modal, new_tab }).
 * @param string $class Classes on the <a>, e.g. 'btn fl--gold-apricot'.
 * @param string $icon  Sprite id inside the tile.
 */
function vtf_button( $btn, string $class = 'btn', string $icon = 'i-arrow' ): void {
	$label = is_array( $btn ) ? trim( (string) ( $btn['label'] ?? '' ) ) : '';
	$link  = vtf_link( $btn );

	if ( '' === $label || ! $link ) {
		return;
	}
	?>
	<a class="<?php echo esc_attr( $class ); ?>"<?php echo vtf_link_attrs( $link, $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in vtf_link_attrs(). ?>><span class="btn__label"><?php echo esc_html( $label ); ?></span><span class="btn__tile"><?php vtf_sprite( $icon ); ?></span></a>
	<?php
}

/**
 * The design's secondary action: `.link-arrow` (text + arrow icon).
 *
 * @param mixed  $btn   Button group field value.
 * @param string $class Classes on the <a>, e.g. 'link-arrow link-arrow--lg'.
 */
function vtf_link_arrow( $btn, string $class = 'link-arrow' ): void {
	$label = is_array( $btn ) ? trim( (string) ( $btn['label'] ?? '' ) ) : '';
	$link  = vtf_link( $btn );

	if ( '' === $label || ! $link ) {
		return;
	}
	?>
	<a class="<?php echo esc_attr( $class ); ?>"<?php echo vtf_link_attrs( $link, $label ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in vtf_link_attrs(). ?>><span class="link-arrow__text"><?php echo esc_html( $label ); ?></span><span class="link-arrow__icon"><?php vtf_sprite( 'i-arrow' ); ?></span></a>
	<?php
}

/**
 * Would vtf_button() print anything? Lets a template skip an empty wrapper.
 *
 * @param mixed $btn Button group field value.
 */
function vtf_has_button( $btn ): bool {
	return is_array( $btn ) && '' !== trim( (string) ( $btn['label'] ?? '' ) ) && null !== vtf_link( $btn );
}

/**
 * ` aria-label="…"` for a link, escaped; a link opening a new tab says so.
 * Empty label — nothing.
 *
 * @param string $label   Accessible name — keep the visible text in it.
 * @param bool   $new_tab The link opens a new tab.
 */
function vtf_aria_label( string $label, bool $new_tab = false ): string {
	$label = trim( wp_strip_all_tags( str_replace( [ '<br>', '<br/>', '<br />' ], ' ', $label ) ) );

	if ( '' === $label ) {
		return '';
	}

	if ( $new_tab ) {
		/* translators: %s: link text */
		$label = sprintf( __( '%s (opens in a new tab)', 'vitfit' ), $label );
	}

	return ' aria-label="' . esc_attr( $label ) . '"';
}

/**
 * Tag of a block's title: the block's “Title tag” (title_tag) or the design's.
 *
 * @param array  $args    Block row.
 * @param string $default Tag from the markup.
 */
function vtf_title_tag( array $args, string $default = 'h2' ): string {
	$tag = (string) ( $args['title_tag'] ?? '' );

	return in_array( $tag, [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p' ], true ) ? $tag : $default;
}

/**
 * A block's title in its picked tag (vtf_title_tag()); nothing when empty.
 * Titles allow <br> and the colour marker `<span class="mark fl--slug">`
 * (vtf_title_html()).
 *
 * @param array       $args    Block row.
 * @param string      $default Tag from the markup.
 * @param string      $class   Classes.
 * @param string      $style   Inline style, e.g. '--title-w: 9em'.
 * @param string|null $text    Text other than $args['title'].
 */
function vtf_title( array $args, string $default, string $class, string $style = '', ?string $text = null ): void {
	$text = $text ?? (string) ( $args['title'] ?? '' );

	if ( '' === trim( $text ) ) {
		return;
	}

	$tag = vtf_title_tag( $args, $default );

	printf( '<%1$s class="%2$s"%3$s>', $tag, esc_attr( $class ), '' !== $style ? ' style="' . esc_attr( $style ) . '"' : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag from an allow-list.
	vtf_title_html( $text );
	printf( '</%s>', $tag ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag from an allow-list.
}

/**
 * Title text with its markers: line breaks (saved as <br>) and `*word*` →
 * the colour marker `<span class="mark fl--{flavour}">word</span>`.
 * The flavour comes from the block's “Marker colour” (title_mark).
 *
 * @param string $text   Title text.
 * @param string $flavor Flavour slug for the marker ('' = the block's own colour).
 */
function vtf_title_html( string $text, string $flavor = '' ): void {
	$html = wp_kses( $text, [ 'br' => [] ] );

	if ( str_contains( $html, '*' ) ) {
		$class = 'mark' . ( '' !== $flavor ? ' fl--' . sanitize_html_class( $flavor ) : '' );
		$html  = (string) preg_replace( '/\*([^*]+)\*/u', '<span class="' . esc_attr( $class ) . '">$1</span>', $html );
	}

	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses'd above, markers added by the theme.
}

/**
 * Site settings value (ACF options page), cached per request.
 *
 * @param string $name Field name.
 * @return mixed Field value, or null when ACF is inactive.
 */
function vtf_option( string $name ) {
	static $cache = [];

	if ( ! array_key_exists( $name, $cache ) ) {
		$cache[ $name ] = function_exists( 'get_field' ) ? get_field( $name, 'option' ) : null;
	}

	return $cache[ $name ];
}

/**
 * tel: link for a human-formatted phone number.
 *
 * @param string $phone E.g. '+38 (044) 232-96-02'.
 */
function vtf_phone_href( string $phone ): string {
	return 'tel:' . preg_replace( '/[^\d+]/', '', $phone );
}

/**
 * Splits a textarea value into trimmed, non-empty lines.
 *
 * @param mixed $value Textarea field value.
 * @return string[]
 */
function vtf_lines( $value ): array {
	if ( empty( $value ) || ! is_string( $value ) ) {
		return [];
	}

	// /u matters: without it \R also matches byte 0x85 inside UTF-8 sequences.
	return array_values( array_filter( array_map( 'trim', preg_split( '/\R/u', $value ) ) ) );
}

/**
 * Hash-only links ("#where") point at front-page blocks. Off the front page
 * they must lead back to it, otherwise the click does nothing.
 *
 * @param string $url Link from a menu item or a text field.
 */
function vtf_resolve_anchor( string $url ): string {
	if ( ! str_starts_with( $url, '#' ) || '#' === $url || is_front_page() ) {
		return $url;
	}

	return vtf_home_url() . $url;
}

/**
 * Appends a line to a theme log in VTF_LOG_DIR.
 *
 * @param string $channel Log file name without extension, e.g. 'forms'.
 * @param string $message Message; context is appended as JSON.
 * @param array  $context Extra data.
 */
function vtf_log( string $channel, string $message, array $context = [] ): void {
	if ( ! wp_mkdir_p( VTF_LOG_DIR ) ) {
		return;
	}

	$line = sprintf(
		"[%s] %s%s\n",
		wp_date( 'Y-m-d H:i:s' ),
		$message,
		! empty( $context ) ? ' ' . wp_json_encode( $context, JSON_UNESCAPED_UNICODE ) : ''
	);

	file_put_contents( VTF_LOG_DIR . '/' . sanitize_file_name( $channel ) . '.log', $line, FILE_APPEND | LOCK_EX ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- append-only local log.
}

/**
 * Resolves a "link" field group: { type: none|page|custom|modal, page, url, modal, new_tab }.
 *
 * Shared by every editor-configurable link and button: a page picked from the
 * site, any typed address (another site, an #anchor), or a popup.
 *
 * @param mixed $link Group field value.
 * @return array{url:string,new_tab:bool,modal:string}|null Null when there is no usable link.
 */
function vtf_link( $link ): ?array {
	if ( ! is_array( $link ) || empty( $link['type'] ) ) {
		return null;
	}

	$url   = '';
	$modal = '';

	if ( 'page' === $link['type'] && ! empty( $link['page'] ) ) {
		$url = (string) $link['page'];
	} elseif ( 'custom' === $link['type'] && ! empty( $link['url'] ) ) {
		$url = vtf_resolve_anchor( trim( (string) $link['url'] ) );
	} elseif ( 'modal' === $link['type'] ) {
		$picked = (string) ( $link['modal'] ?? '' );
		$url    = '#';
		$modal  = in_array( $picked, VTF_LINK_MODALS, true ) ? $picked : 'contact';
	}

	if ( '' === $url ) {
		return null;
	}

	return [
		'url'     => $url,
		'new_tab' => 'custom' === $link['type'] && ! empty( $link['new_tab'] ),
		'modal'   => $modal,
	];
}

/**
 * href + target / popup attributes for a resolved link (vtf_link()), escaped.
 *
 * @param array  $link  Resolved link.
 * @param string $label Accessible name (the visible text) → aria-label.
 */
function vtf_link_attrs( array $link, string $label = '' ): string {
	$html = ' href="' . esc_url( $link['url'] ) . '"';

	if ( ! empty( $link['new_tab'] ) ) {
		$html .= ' target="_blank" rel="noopener noreferrer"';
	}

	$html .= vtf_aria_label( $label, ! empty( $link['new_tab'] ) );

	if ( ! empty( $link['modal'] ) ) {
		$html .= ' data-modal-open="' . esc_attr( $link['modal'] ) . '"';
	}

	return $html;
}

/**
 * Filled social links from Site Settings → Contacts (custom networks included).
 * Empty links are left out (team checklist #35); any number of rows (#34).
 *
 * @return array<int, array{icon:string,url:string,label:string,image:int}>
 */
function vtf_socials(): array {
	static $list = null;

	if ( null !== $list ) {
		return $list;
	}

	$labels = [
		'instagram' => 'Instagram',
		'tiktok'    => 'TikTok',
		'facebook'  => 'Facebook',
		'telegram'  => 'Telegram',
		'viber'     => 'Viber',
	];
	$list   = [];
	$rows   = vtf_option( 'socials' );

	foreach ( is_array( $rows ) ? $rows : [] as $row ) {
		$icon  = (string) ( $row['icon'] ?? '' );
		$image = 'custom' === $icon ? vtf_image_id( $row['custom_icon'] ?? 0 ) : 0;

		if ( empty( $row['link'] ) || ( ! isset( $labels[ $icon ] ) && ! $image ) ) {
			continue;
		}

		$list[] = [
			'icon'  => $icon,
			'url'   => (string) $row['link'],
			'label' => $image ? (string) ( $row['label'] ?? '' ) : $labels[ $icon ],
			'image' => $image,
		];
	}

	return $list;
}

/**
 * Social icon buttons (`.btn-icon` + sprite icon / uploaded icon).
 *
 * @param string $class Button classes, e.g. 'btn-icon btn-icon--light'.
 */
function vtf_social_buttons( string $class = 'btn-icon' ): void {
	foreach ( vtf_socials() as $social ) {
		?>
		<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $social['url'] ); ?>" target="_blank" rel="noopener"<?php echo vtf_aria_label( $social['label'], true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>>
			<?php
			if ( $social['image'] ) {
				vtf_inline_icon( $social['image'], [ 'alt' => '' ] );
			} else {
				vtf_sprite( 'i-' . $social['icon'] );
			}
			?>
		</a>
		<?php
	}
}

/**
 * Prints a textarea value whose line breaks were saved as <br> (ACF
 * `new_lines: br`) — escaped, keeping only the <br>s.
 *
 * @param mixed $text Field value.
 */
function vtf_br( $text ): void {
	echo wp_kses( (string) $text, [ 'br' => [] ] );
}

/**
 * Two-digit number as in the design: 1 → "01".
 *
 * @param int $number Number.
 */
function vtf_num( int $number ): string {
	return str_pad( (string) $number, 2, '0', STR_PAD_LEFT );
}

/**
 * Editor (WYSIWYG) HTML for output: kses'd, shortcodes and oEmbeds run.
 *
 * @param mixed $html Field value.
 */
function vtf_editor_content( $html ): void {
	$html = (string) $html;

	if ( '' === trim( $html ) ) {
		return;
	}

	echo wp_kses_post( do_shortcode( $html ) );
}

/**
 * Feature icons (32×32 sprite symbols) a block row can pick.
 */
const VTF_FEATURE_ICONS = [ 'tea', 'drop', 'leaf', 'heart', 'clock', 'bubbles', 'pack', 'snow', 'trend', 'truck', 'megaphone' ];

/**
 * A feature icon picked in a block (`<svg viewBox="0 0 32 32"><use href="#i-tea" /></svg>`).
 *
 * @param mixed $name Icon name from VTF_FEATURE_ICONS.
 */
function vtf_feature_icon( $name ): void {
	$name = (string) $name;

	if ( in_array( $name, VTF_FEATURE_ICONS, true ) ) {
		vtf_sprite( 'i-' . $name, '0 0 32 32' );
	}
}

/**
 * Breadcrumbs (markup: `.breadcrumbs` in the page tops): Home → parents → current.
 * Later stages extend the trail for products, posts and categories.
 *
 * @param string $class Extra class, e.g. 'breadcrumbs--light'.
 */
function vtf_breadcrumbs( string $class = '' ): void {
	$trail = [
		[
			'title' => __( 'Home', 'vitfit' ),
			'url'   => vtf_home_url(),
		],
	];

	if ( is_singular() ) {
		foreach ( array_reverse( get_post_ancestors( get_queried_object_id() ) ) as $ancestor ) {
			$trail[] = [
				'title' => get_the_title( $ancestor ),
				'url'   => (string) get_permalink( $ancestor ),
			];
		}

		$trail[] = [
			'title' => get_the_title( get_queried_object_id() ),
			'url'   => '',
		];
	}
	?>
	<nav class="<?php echo esc_attr( trim( 'breadcrumbs ' . $class ) ); ?>" aria-label="<?php esc_attr_e( 'Breadcrumbs', 'vitfit' ); ?>">
		<ol>
			<?php foreach ( $trail as $crumb ) { ?>
				<?php if ( '' !== $crumb['url'] ) { ?>
					<li><a href="<?php echo esc_url( $crumb['url'] ); ?>"><?php echo esc_html( $crumb['title'] ); ?></a></li>
				<?php } else { ?>
					<li><span aria-current="page"><?php echo esc_html( $crumb['title'] ); ?></span></li>
				<?php } ?>
			<?php } ?>
		</ol>
	</nav>
	<?php
}

/**
 * IDs from a relationship / taxonomy field value (false / '' / null → []).
 *
 * @param mixed $value Field value.
 * @return int[]
 */
function vtf_ids( $value ): array {
	return is_array( $value ) ? array_values( array_filter( array_map( 'intval', $value ) ) ) : [];
}
