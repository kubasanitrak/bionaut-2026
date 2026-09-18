<?php
/**
 * Archive grid card.
 *
 * @package bionaut
 */

$orig_id = bio_original_id();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'grid-item' ); ?>>
	<a href="<?php the_permalink(); ?>">
		<div class="entry-thumbnail">
			<?php if ( has_post_thumbnail( $orig_id ) ) : ?>
				<?php bio_post_thumbnail( 'normal' ); ?>
			<?php else : ?>
				<span class="thumb-placeholder"></span>
			<?php endif; ?>
		</div>
		<header class="entry-header">
			<h2 class="entry-title"><?php the_title(); ?></h2>
			<?php
			$client = bio_project_field( 'client_name' );
			if ( $client ) {
				echo '<h3 class="client-name">' . esc_html( $client ) . '</h3>';
			}
			?>
		</header>
	</a>
</article>
