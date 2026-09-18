<?php
/**
 * Homepage mosaic tile (16:9, linked to project detail).
 *
 * @package bionaut
 *
 * @var array $args {
 *     @type int    $post_id
 *     @type string $width    single|double
 *     @type string $position left|middle|right
 *     @type string $loading  eager|lazy
 * }
 */

$post_id  = isset( $args['post_id'] ) ? (int) $args['post_id'] : 0;
$width    = ( isset( $args['width'] ) && 'double' === $args['width'] ) ? 'double' : 'single';
$position = isset( $args['position'] ) ? sanitize_html_class( (string) $args['position'] ) : '';
$loading  = ( isset( $args['loading'] ) && 'eager' === $args['loading'] ) ? 'eager' : 'lazy';

if ( $post_id < 1 ) {
	return;
}

$thumb_size = 'double' === $width ? 'big' : 'normal';
$classes    = array( 'hp-tile' );
if ( 'double' === $width ) {
	$classes[] = 'hp-tile--double';
}
if ( $position ) {
	$classes[] = 'hp-tile--' . $position;
}

$badge_url  = bio_featured_badge_url( $post_id );
$thumb_attr = array(
	'loading'  => $loading,
	'decoding' => 'async',
);
if ( 'eager' === $loading ) {
	$thumb_attr['fetchpriority'] = 'high';
}
?>
<article id="post-<?php echo esc_attr( (string) $post_id ); ?>" <?php post_class( $classes, $post_id ); ?>>
	<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
		<header class="entry-header">
			<h2 class="entry-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
			<?php if ( $badge_url ) : ?>
				<img
					class="hp-tile__badge"
					src="<?php echo esc_url( $badge_url ); ?>"
					alt=""
					loading="lazy"
					decoding="async"
				>
			<?php endif; ?>
		</header>
		<div class="entry-thumbnail">
			<?php bio_post_thumbnail( $thumb_size, $post_id, $thumb_attr ); ?>
		</div>
	</a>
</article>
