<?php
/**
 * VIT-FIT theme bootstrap.
 *
 * This file only defines constants and pulls in feature files.
 * Put logic in functions/, never here.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VTF_URI', get_template_directory_uri() );
define( 'VTF_DIR', get_template_directory() );

/**
 * Theme version. Asset URLs are versioned by file modification time
 * (vtf_asset_ver()); this is only the fallback.
 */
define( 'VTF_VER', '1.0.0' );

/**
 * All custom logs go to this one folder.
 */
define( 'VTF_LOG_DIR', VTF_DIR . '/logs' );

require_once VTF_DIR . '/functions/class-svg-sanitizer.php';
require_once VTF_DIR . '/functions/helpers.php';
require_once VTF_DIR . '/functions/languages.php';
require_once VTF_DIR . '/functions/images.php';
require_once VTF_DIR . '/functions/performance.php';
require_once VTF_DIR . '/functions/menus.php';
require_once VTF_DIR . '/functions/builder.php';
require_once VTF_DIR . '/functions/post-types.php';
require_once VTF_DIR . '/functions/products.php';
require_once VTF_DIR . '/functions/catalog.php';
require_once VTF_DIR . '/functions/cards.php';
require_once VTF_DIR . '/functions/faq.php';
require_once VTF_DIR . '/functions/search.php';

require_once VTF_DIR . '/functions/init.php';
require_once VTF_DIR . '/functions/admin.php';
require_once VTF_DIR . '/functions/admin-cleanup.php';
require_once VTF_DIR . '/functions/admin-editor.php';

if ( is_admin() ) {
	require_once VTF_DIR . '/functions/admin-duplicate.php';
	require_once VTF_DIR . '/functions/admin-block-previews.php';
	require_once VTF_DIR . '/functions/admin-reviews.php';
}

require_once VTF_DIR . '/functions/class-admin-bar.php';
require_once VTF_DIR . '/functions/media.php';
require_once VTF_DIR . '/functions/security.php';
require_once VTF_DIR . '/functions/forms.php';

// ACF 6.8.7+ rejects SVG in image fields; this scopes an exemption to icon fields.
if ( class_exists( 'ACF' ) ) {
	require_once VTF_DIR . '/functions/class-svg-acf.php';
}
