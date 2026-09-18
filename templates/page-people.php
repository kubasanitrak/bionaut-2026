<?php
/**
 * People / Kontakt layout. Loaded by page-lide.php.
 *
 * @package bionaut
 */

get_header();
?>

<section id="primary" class="content-area">
	<div class="container">
		<div id="content" class="site-content" role="main">
			<?php
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
			if ( ! has_block( 'acf/bio-people' ) ) {
				get_template_part( 'parts/block', 'people' );
			}
			?>
		</div>
	</div>
</section>

<?php
get_footer();
