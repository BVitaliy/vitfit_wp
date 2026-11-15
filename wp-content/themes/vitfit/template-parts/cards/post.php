<?php
/**
 * Blog card `.post` (markup: home-blog.php / blog-list.php): photo, date, title.
 *
 * @package vitfit
 *
 * @var array $args { post: WP_Post, class: extra classes }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$vtf_post = $args['post'] ?? null;

if ( ! $vtf_post instanceof WP_Post ) {
	return;
}

$vtf_class = trim( ( $args['class'] ?? '' ) . ' post' . vtf_flavor_class( vtf_post_flavor( $vtf_post ) ) );
$vtf_thumb = (int) get_post_thumbnail_id( $vtf_post );
?>
<a class="<?php echo esc_attr( $vtf_class ); ?>" href="<?php echo esc_url( get_permalink( $vtf_post ) ); ?>">
	<span class="post__media">
		<?php vtf_picture( $vtf_thumb, get_field( 'thumb_mobile', $vtf_post->ID ), [ 'alt' => '' ], 'large' ); ?>
	</span>
	<span class="post__meta"><?php echo esc_html( vtf_post_date( $vtf_post ) ); ?></span>
	<span class="h5 post__title"><?php echo esc_html( get_the_title( $vtf_post ) ); ?></span>
</a>
