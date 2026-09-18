<?php
/**
 * Project trailers block.
 *
 * @package bionaut
 */

$trailers    = bio_project_field( 'trailers' );
$trailer_ids = array();
if ( is_array( $trailers ) ) {
	foreach ( $trailers as $row ) {
		if ( is_array( $row ) && isset( $row['trailer_id'] ) ) {
			$trailer_ids[] = $row['trailer_id'];
		} elseif ( is_string( $row ) || is_numeric( $row ) ) {
			$trailer_ids[] = $row;
		}
	}
}
$trailer_ids = array_filter( $trailer_ids );
if ( ! $trailer_ids ) {
	return;
}
?>
<section class="section project-trailers">
	<h2 class="section-title"><?php echo esc_html( _n( 'Trailer', 'Trailery', count( $trailer_ids ), 'bionaut' ) ); ?></h2>
	<?php foreach ( $trailer_ids as $trailer_id ) : ?>
		<article class="trailer">
			<iframe
				width="1200"
				height="675"
				src="<?php echo esc_url( bio_trailer_embed_url( $trailer_id ) ); ?>"
				title="<?php esc_attr_e( 'Trailer', 'bionaut' ); ?>"
				loading="lazy"
				allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
				allowfullscreen
			></iframe>
		</article>
	<?php endforeach; ?>
</section>
