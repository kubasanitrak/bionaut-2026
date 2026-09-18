<?php
/**
 * Book of Projects PDF block.
 *
 * @package bionaut
 */

$pdf = function_exists( 'get_field' ) ? get_field( 'pdf_url' ) : '';
if ( ! $pdf ) {
	$pdf = 'https://bionaut.cz/wp-content/uploads/2024/02/Bionaut-book-v2024.pdf';
}
?>
<div class="pdf-embed" style="width:100%; height: 100dvh; display: block;">
	<?php if ( shortcode_exists( 'flipbook' ) ) : ?>
		<?php
		echo do_shortcode(
			sprintf(
				'[flipbook pdf="%s" same_height_as=".pdf-embed"]',
				esc_url( $pdf )
			)
		);
		?>
	<?php else : ?>
		<object data="<?php echo esc_url( $pdf ); ?>" type="application/pdf" width="100%" height="100%">
			<p>
				<?php echo esc_html( bio_string( 'Váš prohlížeč nepodporuje PDF.', 'Your browser does not support PDFs.' ) ); ?>
				<a href="<?php echo esc_url( $pdf ); ?>"><?php echo esc_html( bio_string( 'Stáhnout PDF', 'Download the PDF' ) ); ?></a>
			</p>
		</object>
	<?php endif; ?>
</div>
