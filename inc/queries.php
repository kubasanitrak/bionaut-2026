<?php
/**
 * Shared front queries used by templates and ACF blocks.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BIO_ARCHIVE_VISIBLE', 4 );
define( 'BIO_NEWS_PER_PAGE', 12 );

function bio_lang_query_args( $args = array(), $lang = null ) {
	if ( function_exists( 'pll_current_language' ) ) {
		if ( ! $lang ) {
			$lang = pll_current_language();
		}
		if ( $lang ) {
			$args['lang'] = $lang;
		}
	}
	return $args;
}

function bio_featured_projects_query() {
	$meta_key = get_option( 'bio_cuztom_migrated' ) ? 'featured_order' : '_settings_featured_order';
	$featured = get_option( 'bio_cuztom_migrated' )
		? array(
			'key'     => 'featured',
			'value'   => '1',
			'compare' => '=',
		)
		: array(
			'key'     => '_settings_featured',
			'value'   => 'on',
			'compare' => '=',
		);

	return new WP_Query(
		bio_lang_query_args(
			array(
				'post_type'      => array( 'projekt', 'page' ),
				'posts_per_page' => 40,
				'orderby'        => 'meta_value_num',
				'meta_key'       => $meta_key,
				'order'          => 'ASC',
				'meta_query'     => array( $featured ),
			)
		)
	);
}

/**
 * Resolve a mosaic Post Object value to a projekt ID in the current language.
 *
 * @param mixed $raw Post ID or WP_Post.
 * @return int
 */
function bio_mosaic_project_id( $raw ) {
	$id = 0;
	if ( $raw instanceof WP_Post ) {
		$id = (int) $raw->ID;
	} elseif ( is_numeric( $raw ) ) {
		$id = (int) $raw;
	}
	if ( $id < 1 ) {
		return 0;
	}

	$post = get_post( $id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		return 0;
	}

	$type = $post->post_type ? $post->post_type : 'projekt';
	$translated = bio_translate_id( $id, $type, bio_current_language() );

	return $translated ? (int) $translated : $id;
}

/**
 * Normalize one Flexible Content row into a render structure.
 *
 * @param array $row ACF flexible row.
 * @return array<string, mixed>|null
 */
function bio_mosaic_normalize_row( $row ) {
	if ( ! is_array( $row ) ) {
		return null;
	}

	$layout = isset( $row['acf_fc_layout'] ) ? (string) $row['acf_fc_layout'] : '';

	if ( 'three_singles' === $layout ) {
		$tiles = array();
		foreach ( array( 'left', 'middle', 'right' ) as $position ) {
			$id = bio_mosaic_project_id( $row[ $position ] ?? 0 );
			if ( ! $id ) {
				continue;
			}
			$tiles[] = array(
				'id'       => $id,
				'width'    => 'single',
				'position' => $position,
			);
		}
		if ( ! $tiles ) {
			return null;
		}
		return array(
			'type'  => 'three_singles',
			'tiles' => $tiles,
		);
	}

	if ( 'double_stack' === $layout ) {
		$position = ( isset( $row['double_position'] ) && 'right' === $row['double_position'] ) ? 'right' : 'left';
		$double   = bio_mosaic_project_id( $row['double'] ?? 0 );
		$stack    = array();
		foreach ( array( 'stack_1', 'stack_2' ) as $key ) {
			$id = bio_mosaic_project_id( $row[ $key ] ?? 0 );
			if ( $id ) {
				$stack[] = $id;
			}
		}
		if ( ! $double && ! $stack ) {
			return null;
		}
		return array(
			'type'     => 'double_stack',
			'position' => $position,
			'double'   => $double,
			'stack'    => $stack,
		);
	}

	return null;
}

/**
 * Mosaic rows from the current ACF block. Empty until editors save at least one layout.
 *
 * @return array<int, array<string, mixed>>
 */
function bio_mosaic_rows_from_block() {
	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$rows = get_field( 'mosaic_rows' );
	if ( ! is_array( $rows ) || ! $rows ) {
		return array();
	}

	$out = array();
	foreach ( $rows as $row ) {
		$normalized = bio_mosaic_normalize_row( $row );
		if ( $normalized ) {
			$out[] = $normalized;
		}
	}

	return $out;
}

/**
 * Fallback mosaic: featured_order query packed as rows of three singles.
 *
 * @return array<int, array<string, mixed>>
 */
function bio_mosaic_fallback_rows() {
	$query = bio_featured_projects_query();
	if ( ! $query->have_posts() ) {
		return array();
	}

	$ids = array();
	foreach ( $query->posts as $post ) {
		$ids[] = (int) $post->ID;
	}

	$rows      = array();
	$positions = array( 'left', 'middle', 'right' );
	foreach ( array_chunk( $ids, 3 ) as $chunk ) {
		$tiles = array();
		foreach ( $chunk as $i => $id ) {
			$tiles[] = array(
				'id'       => $id,
				'width'    => 'single',
				'position' => $positions[ $i ],
			);
		}
		$rows[] = array(
			'type'  => 'three_singles',
			'tiles' => $tiles,
		);
	}

	return $rows;
}

/**
 * Rows to render: saved mosaic, or featured query until the block has content.
 *
 * @return array<int, array<string, mixed>>
 */
function bio_mosaic_rows() {
	$saved = bio_mosaic_rows_from_block();
	if ( $saved ) {
		return $saved;
	}
	return bio_mosaic_fallback_rows();
}

function bio_news_query( $paged = 1, $lang = null ) {
	return new WP_Query(
		bio_lang_query_args(
			array(
				'post_type'      => 'newsitem',
				'post_status'    => 'publish',
				'posts_per_page' => BIO_NEWS_PER_PAGE,
				'paged'          => max( 1, (int) $paged ),
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
			),
			$lang
		)
	);
}

function bio_row_value( $row, $key ) {
	if ( ! is_array( $row ) ) {
		return '';
	}
	if ( isset( $row[ $key ] ) && '' !== $row[ $key ] && null !== $row[ $key ] ) {
		return $row[ $key ];
	}
	$legacy = '_' . $key;
	return $row[ $legacy ] ?? '';
}

function bio_press_url( $press ) {
	if ( ! $press ) {
		return '';
	}
	if ( is_string( $press ) ) {
		return $press;
	}
	$row = isset( $press[0] ) ? $press[0] : $press;
	if ( ! is_array( $row ) ) {
		return '';
	}
	if ( ! empty( $row['file']['url'] ) ) {
		return $row['file']['url'];
	}
	if ( ! empty( $row['url'] ) ) {
		return $row['url'];
	}
	if ( ! empty( $row['file'] ) && is_string( $row['file'] ) ) {
		return $row['file'];
	}
	if ( ! empty( $row[0] ) && is_string( $row[0] ) ) {
		return $row[0];
	}
	return '';
}
