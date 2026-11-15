<?php
/**
 * Theme setup and asset loading.
 *
 * Scripts and styles follow the markup's inc/_top.php and inc/_bottom.php in
 * the same order: every app module bails out by itself when its block is not
 * on the page, exactly as in the static build — and is only enqueued where
 * its block is (team checklist #53).
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VTF_Theme {

	/**
	 * Vendor scripts (inc/_bottom.php order) => where they are needed
	 * ('all' or a condition key of conditions()). The theme's own jQuery build
	 * replaces the core one — the markup was written and tested against it.
	 */
	private const VENDORS = [
		'jquery'             => [ 'js/vendors/jquery.min.js', 'all' ],
		'vtf-swiper'         => [ 'js/vendors/swiper-bundle.min.js', 'swiper' ],
		'vtf-inputmask'      => [ 'js/vendors/jquery.inputmask.min.js', 'all' ], // popup forms are on every page
		'vtf-gsap'           => [ 'js/vendors/gsap.min.js', 'all' ],
		'vtf-scroll-trigger' => [ 'js/vendors/ScrollTrigger.min.js', 'all' ],
		'vtf-split-text'     => [ 'js/vendors/SplitText.min.js', 'all' ],
		'vtf-lenis'          => [ 'js/vendors/lenis.min.js', 'all' ],
		'vtf-fancybox'       => [ 'js/vendors/fancybox.umd.js', 'fancybox' ],
	];

	/**
	 * App scripts (inc/_bottom.php order) => where they are needed.
	 */
	private const SCRIPTS = [
		'vtf-global'       => [ 'js/app-global.js', 'all' ],
		'vtf-modals'       => [ 'js/app-modals.js', 'all' ],
		'vtf-header'       => [ 'js/app-header.js', 'all' ],
		'vtf-search'       => [ 'js/app-search.js', 'all' ],
		'vtf-form'         => [ 'js/app-form.js', 'all' ],
		'vtf-cf7'          => [ 'js/app-cf7.js', 'all' ],
		'vtf-select'       => [ 'js/app-select.js', 'all' ],
		'vtf-faq'          => [ 'js/app-faq.js', 'faq' ],
		'vtf-more-text'    => [ 'js/app-more-text.js', 'more_text' ],
		'vtf-swiper-init'  => [ 'js/app-swiper.js', 'swiper' ],
		'vtf-drag-cursor'  => [ 'js/app-drag-cursor.js', 'swiper' ],
		'vtf-hero'         => [ 'js/app-hero.js', 'hero' ],
		'vtf-story'        => [ 'js/app-story.js', 'story' ],
		'vtf-flavors'      => [ 'js/app-flavors.js', 'flavors' ],
		'vtf-reel'         => [ 'js/app-reel.js', 'all' ], // the video popup player lives here
		'vtf-product'      => [ 'js/app-product.js', 'products' ],
		'vtf-parallax'     => [ 'js/app-parallax.js', 'parallax' ],
		'vtf-titles'       => [ 'js/app-titles.js', 'all' ],
		'vtf-animations'   => [ 'js/app-animations.js', 'all' ],
		'vtf-wp'           => [ 'js/app-wp.js', 'all' ],
	];

	/**
	 * Fonts preloaded in <head>.
	 */
	private const PRELOAD_FONTS = [
		'fonts/Ermilov-Bold.woff2',
		'fonts/NotoSansDisplay-cyrillic.woff2',
	];

	public function __construct() {
		add_action( 'after_setup_theme', [ $this, 'setup' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		add_action( 'wp_head', [ $this, 'head_meta' ], 1 );
		add_action( 'init', [ $this, 'cleanup_head' ] );

		// Classic widgets — the design has no widget areas edited with blocks.
		add_filter( 'use_widgets_block_editor', '__return_false' );

		// The [gallery] shortcode is styled by .rich-text; WP's inline <style> fights it.
		add_filter( 'use_default_gallery_style', '__return_false' );

		// ACF reads / writes field groups in the theme's acf-json/ by itself.
		// Labels and instructions go through __( …, 'vitfit' ), so uk.po
		// localises the admin per user language.
		add_filter(
			'acf/settings/l10n_textdomain',
			static function () {
				return 'vitfit';
			}
		);
		add_action( 'acf/init', [ $this, 'options_page' ] );
	}

	/**
	 * Site Settings (ACF options page).
	 */
	public function options_page(): void {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}

		acf_add_options_page(
			[
				'page_title' => __( 'Site Settings', 'vitfit' ),
				'menu_title' => __( 'Site Settings', 'vitfit' ),
				'menu_slug'  => 'vtf-site-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
				'icon_url'   => 'dashicons-admin-generic',
				'position'   => 59,
			]
		);
	}

	public function setup(): void {
		load_theme_textdomain( 'vitfit', VTF_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'style', 'script' ] );
		add_theme_support( 'responsive-embeds' );

		register_nav_menus(
			[
				'header_menu'   => __( 'Main menu (header and mobile menu; sub-items = dropdown)', 'vitfit' ),
				'footer_menu_1' => __( 'Footer — column 1 (title = menu name)', 'vitfit' ),
				'footer_menu_2' => __( 'Footer — column 2 (title = menu name)', 'vitfit' ),
				'footer_menu_3' => __( 'Footer — column 3 (title = menu name)', 'vitfit' ),
				'legal_menu'    => __( 'Footer — legal links', 'vitfit' ),
			]
		);
	}

	/**
	 * Meta, favicon and the pre-paint script from the markup's inc/_top.php.
	 */
	public function head_meta(): void {
		?>
		<meta name="theme-color" content="#16110e">
		<?php if ( ! has_site_icon() ) { ?>
			<link rel="icon" href="<?php echo esc_url( vtf_asset( 'favicon.svg' ) ); ?>" type="image/svg+xml">
		<?php } ?>
		<?php foreach ( self::PRELOAD_FONTS as $font ) { ?>
			<link rel="preload" href="<?php echo esc_url( vtf_asset( $font ) ); ?>" as="font" type="font/woff2" crossorigin>
		<?php } ?>
		<script>
			if ("scrollRestoration" in history) history.scrollRestoration = "manual";
			document.documentElement.classList.add("js");
		</script>
		<?php
	}

	/**
	 * Strips head output this site never uses.
	 */
	public function cleanup_head(): void {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	}

	/**
	 * Which optional scripts the current view needs — from its builder blocks
	 * and its template.
	 *
	 * @return array<string, bool>
	 */
	private function conditions(): array {
		$blocks = array_column( vtf_builder_rows(), 'acf_fc_layout' );
		$has    = static function ( array $layouts ) use ( $blocks ): bool {
			return (bool) array_intersect( $layouts, $blocks );
		};

		return [
			'swiper'    => $has( [ 'hero', 'benefits', 'hits', 'flavors', 'blog' ] ),
			'fancybox'  => false,
			'faq'       => $has( [ 'faq' ] ),
			'more_text' => $has( [ 'seo' ] ),
			'hero'      => $has( [ 'hero' ] ),
			'story'     => $has( [ 'story' ] ),
			'flavors'   => $has( [ 'flavors' ] ),
			'products'  => $has( [ 'hits' ] ),
			'parallax'  => $has( [ 'about', 'b2b' ] ),
		];
	}

	public function enqueue_assets(): void {
		// The block library CSS is dead weight — this theme renders no blocks.
		wp_dequeue_style( 'wp-block-library' );
		wp_deregister_style( 'wp-block-library' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );

		$need = $this->conditions();

		// --- Styles (inc/_top.php order) ---
		$main_deps = [];

		if ( $need['swiper'] ) {
			wp_enqueue_style( 'vtf-swiper', vtf_asset( 'css/vendors/swiper-bundle.min.css' ), [], null );
			$main_deps[] = 'vtf-swiper';
		}

		if ( $need['fancybox'] ) {
			wp_enqueue_style( 'vtf-fancybox', vtf_asset( 'css/vendors/fancybox.css' ), [], null );
			$main_deps[] = 'vtf-fancybox';
		}

		wp_enqueue_style( 'vtf-main', vtf_asset( 'css/main.css' ), $main_deps, vtf_asset_ver( 'css/main.css' ) );
		wp_enqueue_style( 'vtf-style', vtf_asset( 'css/style.css' ), [ 'vtf-main' ], vtf_asset_ver( 'css/style.css' ) );

		$args = [
			'strategy'  => 'defer',
			'in_footer' => true,
		];

		// --- Vendor scripts ---
		wp_deregister_script( 'jquery' );

		$deps = [];
		foreach ( self::VENDORS as $handle => $vendor ) {
			if ( 'all' === $vendor[1] || ! empty( $need[ $vendor[1] ] ) ) {
				wp_enqueue_script( $handle, vtf_asset( $vendor[0] ), [], null, $args );
				$deps[] = $handle;
			}
		}

		// --- App scripts ---
		foreach ( self::SCRIPTS as $handle => $script ) {
			if ( 'all' === $script[1] || ! empty( $need[ $script[1] ] ) ) {
				wp_enqueue_script( $handle, vtf_asset( $script[0] ), $deps, vtf_asset_ver( $script[0] ), $args );
			}
		}

		wp_localize_script(
			'vtf-global',
			'vitfit',
			[
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'vtf_nonce' ),
				'homeUrl' => vtf_home_url(),
				'lang'    => vtf_language(),
				'i18n'    => [
					'formError' => __( 'Please fill in all the required fields', 'vitfit' ),
				],
			]
		);
	}
}

new VTF_Theme();
