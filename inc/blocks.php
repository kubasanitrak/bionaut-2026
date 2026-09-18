<?php
/**
 * ACF Gutenberg blocks.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'block_categories_all', 'bio_block_categories' );
function bio_block_categories( $categories ) {
	array_unshift(
		$categories,
		array(
			'slug'  => 'bionaut',
			'title' => 'Bionaut',
		)
	);
	return $categories;
}

add_action( 'acf/init', 'bio_register_blocks' );
function bio_register_blocks() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	$blocks = array(
		array(
			'name'            => 'bio-featured-projects',
			'title'           => __( 'Featured projects', 'bionaut' ),
			'description'     => __( 'Homepage featured project mosaic.', 'bionaut' ),
			'render_template' => 'parts/block-featured-projects.php',
			'icon'            => 'star-filled',
		),
		array(
			'name'            => 'bio-projects-archive',
			'title'           => __( 'Projects by category', 'bionaut' ),
			'description'     => __( 'Projekt archive grouped by kategorie.', 'bionaut' ),
			'render_template' => 'parts/block-projects-archive.php',
			'icon'            => 'screenoptions',
		),
		array(
			'name'            => 'bio-news-grid',
			'title'           => __( 'News grid', 'bionaut' ),
			'description'     => __( 'Newsitem cards.', 'bionaut' ),
			'render_template' => 'parts/block-news-grid.php',
			'icon'            => 'megaphone',
		),
		array(
			'name'            => 'bio-people',
			'title'           => __( 'People sections', 'bionaut' ),
			'description'     => __( 'Kontakt / people repeater.', 'bionaut' ),
			'render_template' => 'parts/block-people.php',
			'icon'            => 'groups',
		),
		array(
			'name'            => 'bio-trailers',
			'title'           => __( 'Project trailers', 'bionaut' ),
			'description'     => __( 'YouTube / Vimeo trailers from the current project.', 'bionaut' ),
			'render_template' => 'parts/block-trailers.php',
			'icon'            => 'video-alt3',
		),
		array(
			'name'            => 'bio-gallery',
			'title'           => __( 'Project stills', 'bionaut' ),
			'description'     => __( 'Attached stills gallery.', 'bionaut' ),
			'render_template' => 'parts/block-gallery.php',
			'icon'            => 'format-gallery',
		),
		array(
			'name'            => 'bio-cinema',
			'title'           => __( 'Cinema listings', 'bionaut' ),
			'description'     => __( 'disfilm cinema dates.', 'bionaut' ),
			'render_template' => 'parts/block-cinema.php',
			'icon'            => 'tickets-alt',
		),
		array(
			'name'            => 'bio-book',
			'title'           => __( 'Book of Projects', 'bionaut' ),
			'description'     => __( 'PDF embed.', 'bionaut' ),
			'render_template' => 'parts/block-book.php',
			'icon'            => 'book',
		),
		array(
			'name'            => 'bio-credits',
			'title'           => __( 'Credits and cast', 'bionaut' ),
			'description'     => __( 'Project credits / cast HTML.', 'bionaut' ),
			'render_template' => 'parts/block-credits.php',
			'icon'            => 'id',
		),
	);

	foreach ( $blocks as $block ) {
		acf_register_block_type(
			array_merge(
				$block,
				array(
					'category'        => 'bionaut',
					'mode'            => 'preview',
					'supports'        => array(
						'align'  => false,
						'anchor' => true,
						'mode'   => true,
					),
					'enqueue_assets'  => '',
					'keywords'        => array( 'bionaut' ),
				)
			)
		);
	}
}

add_filter( 'acf/blocks/default_block_version', function() {
    return 3;
});
