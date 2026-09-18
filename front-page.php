<?php
/**
 * Static front page: featured project mosaic.
 *
 * @package bionaut
 */

get_header();
?>

<div id="primary" class="content-area">
	<div class="container">
		<div id="content" class="site-content" role="main">
			<?php
			if ( have_posts() ) {
				the_post();
				the_content();
				if ( ! has_block( 'acf/bio-featured-projects' ) ) {
					get_template_part( 'parts/block', 'featured-projects' );
				}
			} else {
				get_template_part( 'parts/block', 'featured-projects' );
			}
			?>
		</div>
	</div>
</div>

<?php
get_footer();
