<?php
/**
 * Credits / cast block.
 *
 * @package bionaut
 */

$credits = bio_project_field( 'credits_html' );
$cast    = bio_project_field( 'cast_html' );
if ( ! $credits && ! $cast ) {
	return;
}
?>
<div class="credits-container">
	<?php
	if ( $credits ) {
		echo '<section class="section project-credits"><h2 class="section-title">Credits</h2>' . wp_kses_post( $credits ) . '</section>';
	}
	if ( $cast ) {
		echo '<section class="section project-cast"><h2 class="section-title">Cast</h2>' . wp_kses_post( $cast ) . '</section>';
	}
	?>
</div>
