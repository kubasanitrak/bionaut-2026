<?php
/**
 * News grid. Loaded by page-news.php.
 *
 * @package bionaut
 */

get_header();
?>

<section id="primary" class="content-area">
	<div class="container">
		<div id="content" class="site-content" role="main">
			<header class="page-header">
				<h1 class="page-title visuallyhidden"><?php echo esc_html( bio_string( 'Aktuality', 'News' ) ); ?></h1>
			</header>
			<?php
			the_post();
			the_content();
			if ( ! has_block( 'acf/bio-news-grid' ) ) {
				get_template_part( 'parts/block', 'news-grid' );
			}
			?>
		</div>
	</div>
</section>

<?php
get_footer();
