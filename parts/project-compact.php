<?php
/**
 * Compact featured project card (homepage).
 *
 * @package bionaut
 */

$thumbnail_size = 'normal';
$project_class  = 'project grid-item grid-item-compact';
$badge_url      = bio_featured_badge_url();

global $projects_query;
$loading = 'lazy';
if ( $projects_query instanceof WP_Query && $projects_query->current_post < 3 ) {
	$loading = 'eager';
}
if ( $projects_query instanceof WP_Query && in_array( $projects_query->current_post, array( 0, 8, 12 ), true ) ) {
	$thumbnail_size = 'big';
	$project_class  = 'project grid-item grid-item-compact grid-item-big';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $project_class ); ?>>
	<a href="<?php the_permalink(); ?>">
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php if ( $badge_url ) : ?>
				<img
					src="<?php echo esc_url( $badge_url ); ?>"
					style="position: absolute; right: 24px; top: 24px; width: 15%; height: auto;"
					alt=""
					loading="lazy"
					decoding="async"
				>
			<?php endif; ?>
		</header>
		<div class="entry-thumbnail">
			<?php
			$thumb_attr = array( 'loading' => $loading, 'decoding' => 'async' );
			if ( 'eager' === $loading ) {
				$thumb_attr['fetchpriority'] = 'high';
			}
			bio_post_thumbnail( $thumbnail_size, null, $thumb_attr );
			?>
		</div>
	</a>
</article>
