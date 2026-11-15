<?php
/**
 * Product list of a category (markup: template-parts/catalog-list.php).
 *
 * All products of the category are printed; js/app-catalog.js filters (pack /
 * volume / flavour), sorts (popular / new / name), counts, shows 12 at a time
 * and pre-filters from the URL (?pack=can). Filter options come from the
 * category's own products. ≥1200 a sticky aside, below a bottom sheet.
 *
 * @package vitfit
 *
 * @var array $args { term: WP_Term }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_term = $args['term'] ?? null;

if ( ! $vtf_term instanceof WP_Term ) {
	return;
}

$vtf_posts    = vtf_category_products( $vtf_term->term_id );
$vtf_products = array_map( static fn( $post ) => vtf_product_data( $post->ID ), $vtf_posts );
$vtf_count    = count( $vtf_products );
$vtf_words    = vtf_product_words();
$vtf_title    = trim( (string) get_field( 'title', $vtf_term ) );
$vtf_cats     = vtf_catalog_categories();

// «Popular» order: Popularity (higher first), then «Order».
$vtf_ranked = $vtf_products;
usort( $vtf_ranked, static fn( $a, $b ) => (int) get_field( 'rank', $b['id'] ) <=> (int) get_field( 'rank', $a['id'] ) );
$vtf_rank = array_flip( array_column( $vtf_ranked, 'id' ) );

// Filter options present in this category.
$vtf_packs   = [];
$vtf_volumes = [];
$vtf_flavors = [];

foreach ( $vtf_products as $vtf_product ) {
	if ( isset( VTF_PACKS[ $vtf_product['pack'] ] ) ) {
		$vtf_packs[ $vtf_product['pack'] ] = vtf_pack_label( $vtf_product['pack'] );
	}

	$vtf_volume = vtf_volume_value( $vtf_product['volume'] );

	if ( '' !== $vtf_volume ) {
		$vtf_volumes[ $vtf_volume ] = $vtf_product['volume'];
	}

	if ( $vtf_product['flavor'] ) {
		$vtf_flavors[ $vtf_product['flavor']->term_id ] = $vtf_product['flavor'];
	}
}

ksort( $vtf_volumes, SORT_NUMERIC );
$vtf_flavors = array_values( array_filter( vtf_flavors(), static fn( $flavor ) => isset( $vtf_flavors[ $flavor->term_id ] ) ) );
?>
<section class="catalog js-catalog" data-words="<?php echo esc_attr( wp_json_encode( $vtf_words ) ); ?>" data-remove-label="<?php esc_attr_e( 'Remove filter:', 'vitfit' ); ?>">
	<div class="container">
		<div class="page-top">
			<?php vtf_breadcrumbs(); ?>
			<h1 class="h2 page-top__title"><?php vtf_title_html( '' !== $vtf_title ? $vtf_title : $vtf_term->name, (string) get_field( 'title_mark', $vtf_term ) ); ?></h1>
		</div>

		<div class="catalog__layout">
			<aside class="filters js-filters" aria-label="<?php esc_attr_e( 'Filters', 'vitfit' ); ?>" data-lenis-prevent>
				<div class="filters__head">
					<p class="h4"><?php esc_html_e( 'Filters', 'vitfit' ); ?></p>
					<button class="btn-icon filters__close js-filters-close" type="button" aria-label="<?php esc_attr_e( 'Close filters', 'vitfit' ); ?>"><?php vtf_sprite( 'i-close' ); ?></button>
				</div>
				<?php if ( count( $vtf_cats ) > 1 ) { ?>
					<nav class="filters__group filters__cats" aria-label="<?php esc_attr_e( 'Categories', 'vitfit' ); ?>">
						<p class="filters__title"><?php esc_html_e( 'Category', 'vitfit' ); ?></p>
						<ul class="filters__links">
							<?php foreach ( $vtf_cats as $vtf_cat ) { ?>
								<?php $vtf_active = $vtf_cat->term_id === $vtf_term->term_id; ?>
								<li><a class="filter-link<?php echo $vtf_active ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $vtf_cat ) ); ?>"<?php echo $vtf_active ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $vtf_cat->name ); ?> <small><?php echo esc_html( (string) $vtf_cat->count ); ?></small></a></li>
							<?php } ?>
						</ul>
					</nav>
				<?php } ?>
				<form class="filters__form js-filters-form" action="<?php echo esc_url( get_term_link( $vtf_term ) ); ?>">
					<?php if ( count( $vtf_packs ) > 1 ) { ?>
						<fieldset class="filters__group">
							<legend class="filters__title"><?php esc_html_e( 'Pack', 'vitfit' ); ?></legend>
							<div class="filters__pills">
								<label class="filter-pill"><input type="radio" name="pack" value="" checked><span><?php esc_html_e( 'All', 'vitfit' ); ?></span></label>
								<?php foreach ( $vtf_packs as $vtf_value => $vtf_label ) { ?>
									<label class="filter-pill"><input type="radio" name="pack" value="<?php echo esc_attr( $vtf_value ); ?>"><span><?php echo esc_html( $vtf_label ); ?></span></label>
								<?php } ?>
							</div>
						</fieldset>
					<?php } ?>
					<?php if ( count( $vtf_volumes ) > 1 ) { ?>
						<fieldset class="filters__group">
							<legend class="filters__title"><?php esc_html_e( 'Volume', 'vitfit' ); ?></legend>
							<ul class="filters__list">
								<?php foreach ( $vtf_volumes as $vtf_value => $vtf_label ) { ?>
									<li><label class="filter-check"><input type="checkbox" name="volume" value="<?php echo esc_attr( (string) $vtf_value ); ?>"><span class="filter-check__box"><?php vtf_sprite( 'i-check' ); ?></span><?php echo esc_html( $vtf_label ); ?></label></li>
								<?php } ?>
							</ul>
						</fieldset>
					<?php } ?>
					<?php if ( count( $vtf_flavors ) > 1 ) { ?>
						<fieldset class="filters__group">
							<legend class="filters__title"><?php esc_html_e( 'Flavour', 'vitfit' ); ?></legend>
							<ul class="filters__list">
								<?php foreach ( $vtf_flavors as $vtf_flavor ) { ?>
									<li><label class="filter-check"><input type="checkbox" name="flavor" value="<?php echo esc_attr( $vtf_flavor->slug ); ?>"><span class="filter-check__box"><?php vtf_sprite( 'i-check' ); ?></span><span<?php echo vtf_flavor_attrs( 'filter-check__dot', $vtf_flavor, '--dot: var(--fl)' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>></span><?php echo esc_html( $vtf_flavor->name ); ?></label></li>
								<?php } ?>
							</ul>
						</fieldset>
					<?php } ?>
					<button class="filters__reset js-filters-reset" type="reset"><?php vtf_sprite( 'i-reset' ); ?><?php esc_html_e( 'Reset all filters', 'vitfit' ); ?></button>
				</form>
				<div class="filters__apply">
					<button class="btn js-filters-close" type="button"><span class="btn__label"><?php esc_html_e( 'Show', 'vitfit' ); ?> <b class="js-cat-count"><?php echo esc_html( (string) $vtf_count ); ?></b> <span class="js-cat-word"><?php echo esc_html( vtf_count_word( $vtf_count ) ); ?></span></span><span class="btn__tile"><?php vtf_sprite( 'i-arrow' ); ?></span></button>
				</div>
			</aside>
			<div class="filters__backdrop js-filters-close" aria-hidden="true"></div>

			<div class="catalog__main">
				<div class="catalog__bar">
					<button class="catalog__filter-btn js-filters-open" type="button"><?php vtf_sprite( 'i-filter' ); ?><?php esc_html_e( 'Filters', 'vitfit' ); ?> <b class="js-filters-badge" hidden></b></button>
					<p class="catalog__count"><b class="js-cat-count"><?php echo esc_html( (string) $vtf_count ); ?></b> <span class="js-cat-word"><?php echo esc_html( vtf_count_word( $vtf_count ) ); ?></span></p>
					<label class="catalog__sort">
						<span><?php esc_html_e( 'Sort:', 'vitfit' ); ?></span>
						<span class="field__select"><select class="field__input js-cat-sort" name="sort">
							<option value="popular" selected><?php esc_html_e( 'Popular', 'vitfit' ); ?></option>
							<option value="new"><?php esc_html_e( 'New', 'vitfit' ); ?></option>
							<option value="name"><?php esc_html_e( 'By name', 'vitfit' ); ?></option>
						</select></span>
					</label>
				</div>
				<div class="catalog__active js-cat-active" hidden></div>

				<div class="catalog__grid js-cat-grid">
					<?php foreach ( $vtf_products as $vtf_product ) { ?>
						<?php
						get_template_part(
							'template-parts/cards/product',
							null,
							[
								'product' => $vtf_product,
								'catalog' => [
									'pack'   => $vtf_product['pack'],
									'volume' => vtf_volume_value( $vtf_product['volume'] ),
									'flavor' => $vtf_product['flavor'] ? $vtf_product['flavor']->slug : '',
									'rank'   => (int) $vtf_rank[ $vtf_product['id'] ] + 1,
									'new'    => 'new' === $vtf_product['badge'] ? 1 : 0,
									'name'   => $vtf_product['name'],
								],
							]
						);
						?>
					<?php } ?>
				</div>

				<div class="catalog__empty js-cat-empty"<?php echo $vtf_count ? ' hidden' : ''; ?>>
					<span class="bottle fl--original catalog__empty-bottle"><svg viewBox="0 0 200 470"><use href="#bottle" /></svg></span>
					<p class="h4"><?php esc_html_e( 'No such kombucha yet', 'vitfit' ); ?></p>
					<p class="text-md"><?php esc_html_e( 'Try another flavour or pack.', 'vitfit' ); ?></p>
					<button class="btn js-filters-reset" type="button"><span class="btn__label"><?php esc_html_e( 'Reset filters', 'vitfit' ); ?></span><span class="btn__tile"><?php vtf_sprite( 'i-reset' ); ?></span></button>
				</div>

				<div class="catalog__more pager">
					<button class="btn js-cat-more" type="button"><span class="btn__label"><?php esc_html_e( 'Show more', 'vitfit' ); ?></span><span class="btn__tile"><?php vtf_sprite( 'i-plus' ); ?></span></button>
				</div>
			</div>
		</div>
	</div>
</section>
