<?php
/**
 * SPARK / Pošli projekt layout. Loaded by page-spark.php.
 *
 * @package bionaut
 */

get_header();
?>

<div id="primary" class="content-area single-page page-about">
	<div class="container clear-none">
		<div id="content" class="site-content clear-none" role="main">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article class="page-about--col page-about--col_content" id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<!-- <div class="entry-content"> -->
						<?php the_content(); ?>
					<!-- </div> -->
				</article>
			<?php endwhile; ?>
			<?php get_template_part( 'parts/planet', 'bionaut' ); ?>
		</div>
	</div>
</div>

<?php
get_footer();
