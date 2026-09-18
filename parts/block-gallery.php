<?php
/**
 * Attached stills gallery block.
 *
 * @package bionaut
 */

if ( bio_hide_attached_gallery() ) {
	return;
}

$orig_id    = bio_original_id();
$prizes_url = bio_project_field( 'prizes' );
$icons      = bio_project_icons();
$images     = get_children(
	array(
		'post_parent'    => $orig_id,
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'posts_per_page' => -1,
		'post__not_in'   => array( get_post_thumbnail_id() ),
	)
);
if ( ! $images ) {
	return;
}
?>
<section class="section stills">
	<div class="gallery clear">
		<?php
		foreach ( $images as $image ) {
			$image_img_tag = wp_get_attachment_image( $image->ID, 'bio-thumb-small' );
			$image_big_url = wp_get_attachment_image_src( $image->ID, 'large' );
			if ( empty( $image_big_url[0] ) ) {
				continue;
			}
			if ( $prizes_url && $image_big_url[0] === $prizes_url ) {
				continue;
			}
			if ( in_array( $image_big_url[0], $icons, true ) ) {
				continue;
			}
			?>
			<div class="gallery-thumb">
				<a href="<?php echo esc_url( $image_big_url[0] ); ?>" data-lightbox="<?php echo esc_url( $image_big_url[0] ); ?>" rel="gallery">
					<?php echo $image_img_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</div>
			<?php
		}
		$dropbox = bio_project_field( 'press_dropbox' );
		if ( $dropbox ) {
			echo '<div class="presskit"><a href="' . esc_url( $dropbox ) . '" class="presskit-link" target="_blank">' . esc_html__( 'Press', 'bionaut' ) . '</a></div>';
		}
		?>
	</div>
</section>
