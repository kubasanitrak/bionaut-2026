<?php
/**
 * Book of Projects PDF. Loaded by page-bop.php.
 *
 * @package bionaut
 */

get_header();
?>

<section id="primary" class="content-area">
	<div class="container">
		<div id="content" class="site-content" role="main">
			<?php
			the_post();
			the_content();
			if ( ! has_block( 'acf/bio-book' ) ) {
				get_template_part( 'parts/block', 'book' );
			}
			?>
		</div>
	</div>
</section>

<?php
get_footer();
