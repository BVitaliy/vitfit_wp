<?php
/**
 * Builder “Add block” menu: hovering a block name shows a screenshot of how
 * that block looks on the site. Screenshots: img/admin/blocks/{layout}.webp.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VTF_Admin_Block_Previews {

	private const DIR = 'img/admin/blocks';

	public function __construct() {
		add_action( 'acf/input/admin_enqueue_scripts', [ $this, 'enqueue' ] );
	}

	public function enqueue(): void {
		$screen = get_current_screen();

		if ( ! $screen || 'post' !== $screen->base || 'page' !== $screen->post_type ) {
			return;
		}

		$previews = [];

		foreach ( glob( VTF_DIR . '/' . self::DIR . '/*.webp' ) ?: [] as $file ) {
			$layout              = str_replace( '-', '_', basename( $file, '.webp' ) );
			$previews[ $layout ] = vtf_asset( self::DIR . '/' . basename( $file ) ) . '?ver=' . filemtime( $file );
		}

		if ( empty( $previews ) ) {
			return;
		}

		wp_enqueue_script( 'vtf-admin-block-previews', vtf_asset( 'js/admin-block-previews.js' ), [ 'acf-input' ], vtf_asset_ver( 'js/admin-block-previews.js' ), true );
		wp_localize_script( 'vtf-admin-block-previews', 'vtfBlockPreviews', $previews );
	}
}

new VTF_Admin_Block_Previews();
