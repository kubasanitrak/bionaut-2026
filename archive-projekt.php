<?php
/**
 * Projekt archive grouped by kategorie.
 *
 * @package bionaut
 */

get_header();
?>

<section id="primary" class="content-area">
	<div class="container">
		<div id="content" class="site-content" role="main">
			<header class="page-header">
				<h1 class="page-title visuallyhidden"><?php esc_html_e( 'Projekty', 'bionaut' ); ?></h1>
			</header>
			<?php get_template_part( 'parts/block', 'projects-archive' ); ?>
		</div>
	</div>
</section>

<?php
get_footer();
