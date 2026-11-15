<?php
/**
 * Fallback template. Every real view gets its own template in later stages;
 * this one only keeps WordPress happy and lists whatever the query returned.
 *
 * @package vitfit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="page">
	<section class="text-page">
		<div class="container">
			<div class="rich-text text-md">
				<?php
				while ( have_posts() ) {
					the_post();
					printf( '<h2><a href="%s">%s</a></h2>', esc_url( get_permalink() ), esc_html( get_the_title() ) );
				}
				?>
			</div>
		</div>
	</section>
	<div class="spacer-xl"></div>
</main>

<?php
get_footer();
