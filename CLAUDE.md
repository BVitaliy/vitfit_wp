# Project Rules

## Project context
- WordPress **custom theme** (`wp-content/themes/vitfit`), ACF Pro, no page-builder plugins
- VIT-FIT — live unpasteurized kombucha brand site (ТОВ «Живий Квас», Ukrproduct Group). No online
  sales in stage one; the catalog is built so WooCommerce can come later
- Built by integrating a static PHP markup project into WordPress
- Markup source of truth: `/Users/app_dev/Desktop/sites/vitfit/app/` (+ its `CLAUDE.md`, `BRIEF.md` —
  component list, adaptive rules, JS modules). When a template looks wrong, diff it against the
  original markup before "fixing" the CSS
- Conventions copied from `/Applications/MAMP/htdocs/reatiss_wp` (same markup stack); team checklist =
  Google Doc «Back End (DEV)» (see memory `wp-backend-checklist`)
- Local site: http://localhost:8888/vitfit_wp (DB `vitfit_wp` on 127.0.0.1:3306, table prefix `vtf_`).
  wp-cli: `~/bin/wpc --skip-plugins=qtranslate-xt-3.16.1 …` (qTranslate fatals in CLI without a URL)
- **Languages: UA (main, no URL prefix) + EN (`/en/`) via qTranslate-XT** (team rule for multilingual
  sites). Browser-language redirect is off. Theme strings are English sources in the `vitfit` text
  domain; `languages/uk.po` → `uk.mo` + `uk.l10n.php` gives the Ukrainian site and the Ukrainian admin
  (ACF labels go through `acf/settings/l10n_textdomain`). Content (posts, ACF text fields, terms,
  menus) is translated in the admin with qTranslate's language tabs. **New strings: write them in
  English, add the Ukrainian to uk.po, then `wp i18n make-mo uk.po . && wp i18n make-php uk.po .` in
  `languages/`.**
- WP core was copied from reatiss_wp (Ukrainian build) — fine here, UA is the main language.
- SEO via Yoast (qTranslate's wp-seo module is on). Site closed from indexing (Settings + robots.txt).

## Integration plan (stages)
1. Infrastructure — WP, plugins, security, theme skeleton, assets (local fonts), header (menu with
   dropdown + promo, language switcher, live search) / footer / popups (CF7 partner + contact,
   thanks, promo, corner toast, video), Site Settings, menus, text pages, 404 ✅
2. Page builder «Конструктор» + Home (14 blocks) + products / flavours / shops / FAQ data ✅
3. Catalog: categories page (`catalog-categories`), product list with filters / sort / show more
   (GET + AJAX, `app-catalog.js`), product page (gallery, specs, taste, perks, accordion, reviews
   with moderation + review popup, ask form, related)
4. Inner pages on the builder: About (team popup), What is kombucha, Production (ferment scene,
   gallery, certs), Where to buy (shop tabs), Partners, Contacts (map), FAQ page
5. Blog: list (categories, pagination / show more), article (nav «У цій статті», slider, video,
   table, CTA, share, related)
6. Search results page, forms polish (reCAPTCHA keys / Turnstile at launch), mails layout
7. EN content, Yoast, performance, team checklist pass, adaptive QA, admin manual

## Project structure
```
functions.php     — constants + require bootstrap only
functions/        — feature classes and helpers (init, security, admin, media, forms, builder…)
languages/        — uk.po / uk.mo / uk.l10n.php — Ukrainian site + admin
acf-json/         — ACF field groups (source of truth — see .claude/acf-flexible-content.md)
templates/        — page templates (builder.php = «Page builder / Конструктор»)
template-parts/   — blocks/ (one per builder layout), cards/, global/ (header, footer, modals,
                    sprite, search, badge, section-head)
js/               — markup JS as delivered + WP-only files (app-cf7.js, app-search.js, app-wp.js)
css/              — compiled CSS (never edit; `gulp styles` from scss/)
scss/             — SCSS from the markup; WP-only additions in scss/style/_wp.scss
img/ fonts/       — theme assets only (logo variants, redstone.svg). Content photos / videos /
                    shop logos live in the media library
logs/             — custom logs (vtf_log()), gitignored
```
`node_modules` in the theme is a symlink to the markup project's (gitignored) — `npx gulp styles`.

## Deviations from the markup (keep the list short and current)
- Fonts are local: Noto Sans Display variable woff2 subsets (`scss/main/_fonts.scss`), preloaded.
- `js/app-header.js`: the demo search filter over hardcoded rows is removed — live suggestions are
  `js/app-search.js` → `wp_ajax_vtf_live_search` (products + posts, 5 rows).
- Forms are CF7 (`form.vtf-form.form`), not `.js-form`: errors / «fill in all fields» / busy button /
  "thanks" popup in `js/app-cf7.js`. Field HTML lives in the CF7 editor
  (`label.field > span.field__label + [text* name class:field__input placeholder "…"]`); theme tags
  print the parts CF7 would wrap differently: `[vtf_select* name "Placeholder" "Option"…]`,
  `[vtf_topics name "A" "B"…]`, `[vtf_consent* agree]` (privacy page link), `[vtf_submit "Label"]`.
- Bottles: the SVG placeholder (`#bottle` / `#can` / `#glass` in the flavour colours) until a packshot
  is uploaded (product or flavour «Packshot») → `<img>` in the same `.bottle` box (`.bottle--img`).
- Hero slides are bottle slides as in the markup (no background video option yet — not styled in the
  markup).
- Titles: `*word*` in a title field = the colour marker `<span class="mark fl--{title_mark}">`
  (hero: `*line*` = accent line; b2b: `*text*` = `.b2b__brand`).

## Data model
- `vtf_product` (Products, `/product/{slug}/`): one item = flavour + pack. Fields: flavour (taxonomy,
  saves terms), pack pet|can|glass, volume, pack size, badge hit|new, rank, short description,
  packshot, card hover (photo / video + poster). «Order» = list order.
- `vtf_flavor` (taxonomy, no front pages): colours = preset slug of `scss/style/_flavors.scss` or own
  colours (printed as the same CSS vars inline — `vtf_flavor_attrs()`), order, bottle shape,
  packshot, formats text (empty = from its products), «Learn more» product.
- `vtf_product_cat` (`/catalog/{slug}/`), `vtf_shop` + `vtf_shop_type`, `vtf_faq` (title = question).
- Posts: card colour + phone image (side box).

## Page builder («Конструктор»)
- ACF flexible content `page_builder` (group `group_vtf_page_builder`) on the front page and pages on
  the «Page builder» template (`templates/builder.php`); there the classic editor is hidden.
  Default template = text page (title + editor + «line under the title»).
- Layout `foo_bar` → `template-parts/blocks/foo-bar.php` with the row as `$args`
  (`functions/builder.php`). First sub-field `{layout}_hide`. Blocks print only what is filled.
- Spacers follow the markup's index.php: `.spacer-xl` after every block, exceptions in
  `VTF_LAYOUT_SPACERS` / `VTF_PAIR_SPACERS` (hero→tapes none, tapes → lg, reel→flavors none,
  faq→seo lg). Hidden rows print nothing, so no gaps.
- **A new block with JS → add it to `VTF_Theme::conditions()` (`functions/init.php`).**
- «Add block» previews: `img/admin/blocks/{layout-with-dashes}.webp` (840 px wide, screenshots of
  the block on the site). **A new block → add its preview.**
- ACF JSON keys are deterministic (`field_` + sha1 of the field path, first 16 hex; layouts `layout_`…).
  Edit `acf-json/` directly, give new fields keys the same way; never change an existing key / name.
- Buttons / links: group { label, type: none|page|custom|modal, page, url, modal: contact|partner,
  new_tab } → `vtf_button()` / `vtf_link_arrow()` / `vtf_link()` + `vtf_link_attrs()`.

## Prefixes and text domain
- Text domain: `vitfit`; PHP functions/globals `vtf_`; classes `VTF_`; constants `VTF_UPPER_SNAKE`
- CSS classes come from the markup — **do not rename them**

## Language of code
- Code, comments, variable names, commit messages in **English**
- Front-end / admin strings: English source wrapped for translation + Ukrainian in `languages/uk.po`

## Escaping ACF output — no exceptions
`get_field()` / `get_sub_field()` / `vtf_option()` return unescaped data. Escape at output.

## PHP
- `if ( ! defined( 'ABSPATH' ) ) { exit; }` in every PHP file; hooks in class constructors
- Prefer `! empty( $var )`; curly braces only (no `endif`)
- **rspanel sorts posts by menu_order** (its `pre_get_posts`): date-ordered lists set `orderby=date`
  explicitly and use `vtf_get_posts()` (WP_Query without `suppress_filters`).

## Build
`npx gulp styles` in the theme dir (scss → css). **Never edit `css/*.css` directly.**
`js/vendors/` is refreshed by `gulp updateJS` — never hand-edit those files.

## No debug code in commits
Remove all `var_dump()`, `console.log()`, `error_log()` before committing.

---

## Additional rules
- [PHP Standards](.claude/php-standards.md)
- [JavaScript Standards](.claude/js-standards.md)
- [Security](.claude/security.md)
- [ACF Flexible Content](.claude/acf-flexible-content.md)
