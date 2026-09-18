<?php
/**
 * News grid card.
 *
 * @package bionaut
 */

$link = function_exists( 'get_field' ) ? get_field( 'newsitem_link_to' ) : '';
$img  = function_exists( 'get_field' ) ? get_field( 'newsitem_img' ) : null;
?>
<div id="post-<?php the_ID(); ?>" class="news-grid--item">
	<?php if ( $link ) : ?>
		<a target="_blank" rel="noopener noreferrer" class="news-grid--link abs-link" href="<?php echo esc_url( $link ); ?>"></a>
	<?php endif; ?>
	<div class="news-grid--item_thumb">
		<?php
		if ( $img && ! empty( $img['ID'] ) ) {
			echo wp_get_attachment_image(
				$img['ID'],
				'bio-thumb-medium',
				false,
				array(
					'alt'      => get_the_title(),
					'loading'  => 'lazy',
					'decoding' => 'async',
				)
			);
		}
		?>
	</div>
	<div class="news-grid--item_header">
		<h2 class="news-grid--item_title"><?php the_title(); ?></h2>
	</div>
</div>
