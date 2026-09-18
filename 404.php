<?php
/**
 * 404 template.
 *
 * @package bionaut
 */

get_header();
?>

<div id="primary" class="content-area">
	<div class="container">
		<div id="content" class="site-content" role="main">
			<article class="hentry">
				<header class="entry-header">
					<h1 class="entry-title"><?php esc_html_e( 'Stránka nenalezena', 'bionaut' ); ?></h1>
				</header>
			</article>
		</div>
	</div>
</div>

<?php
get_footer();
