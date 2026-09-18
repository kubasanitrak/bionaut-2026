<?php
/**
 * SPARK / Pošli projekt layout. Loaded by page-spark.php.
 *
 * @package bionaut
 */

get_header();
?>

<div id="primary" class="content-area single-page page-spark">
	<div class="container">
		<div id="content" class="site-content" role="main">
			<?php get_template_part( 'parts/spark', 'decoration' ); ?>
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</div>

<?php
get_footer();
