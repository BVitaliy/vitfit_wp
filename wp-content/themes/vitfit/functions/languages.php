<?php
/**
 * Languages (qTranslate-XT): UA is the main language (no URL prefix),
 * EN lives under /en/.
 *
 * Theme strings are English sources in the `vitfit` text domain; qTranslate
 * switches the locale per language, so languages/uk.mo gives the Ukrainian
 * site (and the Ukrainian admin) while the English site shows the sources.
 * Editor content (posts, ACF fields, menus, terms) is translated in the admin
 * with qTranslate's language tabs.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Short labels of the language switcher (the design shows "UA" for Ukrainian).
 */
const VTF_LANGUAGE_LABELS = [
	'uk' => 'UA',
	'en' => 'EN',
];

/**
 * Current language code ('uk' when qTranslate is off).
 */
function vtf_language(): string {
	return function_exists( 'qtranxf_getLanguage' ) ? (string) qtranxf_getLanguage() : 'uk';
}

/**
 * Enabled languages with the current page's URL in each.
 *
 * @return array<int, array{code:string,label:string,url:string,current:bool}>
 */
function vtf_languages(): array {
	global $q_config;

	if ( ! function_exists( 'qtranxf_convertURL' ) || empty( $q_config['enabled_languages'] ) ) {
		return [];
	}

	$current = vtf_language();
	$list    = [];

	foreach ( (array) $q_config['enabled_languages'] as $code ) {
		$list[] = [
			'code'    => (string) $code,
			'label'   => VTF_LANGUAGE_LABELS[ $code ] ?? strtoupper( (string) $code ),
			'url'     => (string) qtranxf_convertURL( '', $code, false, true ),
			'current' => $code === $current,
		];
	}

	return $list;
}

/**
 * Language links: `<a class="is-active" href="…">UA</a><a href="…">EN</a>`.
 */
function vtf_language_links(): void {
	foreach ( vtf_languages() as $language ) {
		printf(
			'<a%1$s href="%2$s" hreflang="%3$s" lang="%3$s">%4$s</a>',
			$language['current'] ? ' class="is-active" aria-current="true"' : '',
			esc_url( $language['url'] ),
			esc_attr( 'uk' === $language['code'] ? 'uk' : $language['code'] ),
			esc_html( $language['label'] )
		);
	}
}

/**
 * Label of the current language for the switcher button.
 */
function vtf_language_label(): string {
	return VTF_LANGUAGE_LABELS[ vtf_language() ] ?? strtoupper( vtf_language() );
}
