<?php
/**
 * Search overlay (markup: the `.search` part of inc/_header.php). Suggestions
 * while typing come from `wp_ajax_vtf_live_search` (js/app-search.js, 5 rows
 * at most — template-parts/global/search-result.php); Enter → the search page.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="search js-search" aria-hidden="true">
	<div class="search__backdrop js-search-close"></div>
	<div class="search__panel" data-lenis-prevent>
		<div class="container">
			<form class="search__head" action="<?php echo esc_url( vtf_home_url() ); ?>" role="search">
				<label class="search__field">
					<?php vtf_sprite( 'i-search' ); ?>
					<input class="js-search-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Find a flavour…', 'vitfit' ); ?>" autocomplete="off" aria-label="<?php esc_attr_e( 'Search', 'vitfit' ); ?>">
					<button class="search__clear js-search-clear" type="button" aria-label="<?php esc_attr_e( 'Clear', 'vitfit' ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
				</label>
				<button class="btn-icon js-search-close" type="button" aria-label="<?php esc_attr_e( 'Close search', 'vitfit' ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
			</form>
			<div class="search__results js-search-results" hidden>
				<div class="js-search-list"></div>
				<p class="search__empty js-search-empty" hidden><?php esc_html_e( 'Nothing found. Try another word.', 'vitfit' ); ?></p>
			</div>
		</div>
	</div>
</div>
