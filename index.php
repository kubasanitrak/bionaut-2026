<?php
/**
 * Posts index (not the homepage). The mosaic lives on the static front page.
 *
 * @package bionaut
 */

get_header();
?>

<div id="primary" class="content-area">
	<div class="container">
		<div id="content" class="site-content" role="main">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<header class="entry-header">
							<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						</header>
					</article>
					<?php
				endwhile;
			endif;
			?>
		</div>
	</div>
</div>

<?php
get_footer();
