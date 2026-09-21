<?php
/**
 * News grid card.
 *
 * Pass the newsitem ID into get_field(). Inside an ACF block, a bare
 * get_field() reads the block, not the looped post.
 *
 * @package bionaut
 */

$post_id = (int) get_the_ID();
$link    = '';
$img_id  = 0;

if ( function_exists( 'get_field' ) ) {
	$link = get_field( 'newsitem_link_to', $post_id );
	$img  = get_field( 'newsitem_img', $post_id );
	if ( is_array( $img ) ) {
		$img_id = (int) ( $img['ID'] ?? $img['id'] ?? 0 );
	} elseif ( is_numeric( $img ) ) {
		$img_id = (int) $img;
	}
}

if ( ! $link ) {
	$link = get_post_meta( $post_id, 'newsitem_link_to', true );
}
if ( ! $img_id ) {
	$img_id = (int) get_post_meta( $post_id, 'newsitem_img', true );
}
?>
<div id="post-<?php the_ID(); ?>" class="news-grid--item">
	<?php if ( $link ) : ?>
		<a target="_blank" rel="noopener noreferrer" class="news-grid--link abs-link" href="<?php echo esc_url( $link ); ?>"></a>
	<?php endif; ?>
	<div class="news-grid--item_thumb">
		<?php
		if ( $img_id ) {
			echo wp_get_attachment_image(
				$img_id,
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
