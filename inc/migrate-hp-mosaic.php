<?php
/**
 * One-shot: static CS/EN front pages with the featured-projects mosaic block.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bio_hp_mosaic_block_markup() {
	return "<!-- wp:acf/bio-featured-projects {\"name\":\"acf/bio-featured-projects\",\"mode\":\"edit\"} /-->\n";
}

/**
 * Find a page by slug across languages.
 *
 * @param string $slug Page slug.
 * @return WP_Post|null
 */
function bio_hp_mosaic_find_page( $slug ) {
	$found = get_posts(
		array(
			'name'           => $slug,
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => 1,
			'lang'           => '',
		)
	);
	return $found ? $found[0] : null;
}

/**
 * Create or reuse a published page with the mosaic block.
 *
 * @param string $title Page title.
 * @param string $slug  Page slug.
 * @param string $lang  Polylang language slug.
 * @return int
 */
function bio_hp_mosaic_ensure_page( $title, $slug, $lang ) {
	$existing = bio_hp_mosaic_find_page( $slug );
	if ( $existing instanceof WP_Post ) {
		$page_id = (int) $existing->ID;
		if ( false === strpos( (string) $existing->post_content, 'acf/bio-featured-projects' ) ) {
			wp_update_post(
				array(
					'ID'           => $page_id,
					'post_content' => bio_hp_mosaic_block_markup(),
					'post_status'  => 'publish',
				)
			);
		}
	} else {
		$inserted = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => bio_hp_mosaic_block_markup(),
			),
			true
		);
		if ( is_wp_error( $inserted ) ) {
			return 0;
		}
		$page_id = (int) $inserted;
		if ( $page_id < 1 ) {
			return 0;
		}
	}

	if ( $lang && function_exists( 'pll_set_post_language' ) ) {
		pll_set_post_language( $page_id, $lang );
	}

	return $page_id;
}

function bio_migrate_hp_mosaic_pages( $force = false ) {
	if ( ! $force && get_option( 'bio_hp_mosaic_pages' ) ) {
		return get_option( 'bio_hp_mosaic_pages_log', array( 'skipped' => true ) );
	}

	$cs_id = bio_hp_mosaic_ensure_page( 'Domů', 'home', 'cs' );
	$en_id = bio_hp_mosaic_ensure_page( 'Home', 'home-en', 'en' );

	if ( $cs_id && $en_id && function_exists( 'pll_save_post_translations' ) ) {
		pll_save_post_translations(
			array(
				'cs' => $cs_id,
				'en' => $en_id,
			)
		);
	}

	if ( $cs_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $cs_id );
	}

	$result = array(
		'cs'   => $cs_id,
		'en'   => $en_id,
		'time' => gmdate( 'c' ),
	);
	update_option( 'bio_hp_mosaic_pages', 1, false );
	update_option( 'bio_hp_mosaic_pages_log', $result, false );

	bio_hp_mosaic_enable_lang_front();

	return $result;
}

/**
 * Use / and /en/ as language homepages instead of /en/home-en/.
 */
function bio_hp_mosaic_enable_lang_front() {
	if ( function_exists( 'PLL' ) && PLL() && isset( PLL()->options ) ) {
		PLL()->options['redirect_lang'] = true;
	} else {
		$pll = get_option( 'polylang', array() );
		if ( is_array( $pll ) ) {
			$pll['redirect_lang'] = 1;
			update_option( 'polylang', $pll );
		}
	}

	$log = get_option( 'bio_hp_mosaic_pages_log', array() );
	$cs_id = isset( $log['cs'] ) ? (int) $log['cs'] : (int) get_option( 'page_on_front' );
	$en_id = isset( $log['en'] ) ? (int) $log['en'] : 0;

	if ( $cs_id ) {
		wp_update_post(
			array(
				'ID'         => $cs_id,
				'post_title' => 'Domů',
			)
		);
	}
	if ( $en_id ) {
		wp_update_post(
			array(
				'ID'         => $en_id,
				'post_title' => 'Home',
			)
		);
	}

	if ( function_exists( 'PLL' ) && PLL() && isset( PLL()->model ) && method_exists( PLL()->model, 'clean_languages_cache' ) ) {
		PLL()->model->clean_languages_cache();
	}

	flush_rewrite_rules( false );
	update_option( 'bio_hp_mosaic_pll', 1, false );
}

add_action( 'init', 'bio_maybe_migrate_hp_mosaic_pages', 40 );
function bio_maybe_migrate_hp_mosaic_pages() {
	if ( function_exists( 'wp_installing' ) && wp_installing() ) {
		return;
	}
	if ( ! get_option( 'bio_hp_mosaic_pages' ) ) {
		bio_migrate_hp_mosaic_pages();
	}
	if ( ! get_option( 'bio_hp_mosaic_pll' ) ) {
		bio_hp_mosaic_enable_lang_front();
	}
	if ( ! get_option( 'bio_hp_mosaic_block_edit_mode' ) ) {
		bio_hp_mosaic_switch_block_to_edit_mode();
	}
}

/**
 * Saved homepage blocks used preview mode; tile links swallowed Gutenberg clicks.
 */
function bio_hp_mosaic_switch_block_to_edit_mode() {
	$log = get_option( 'bio_hp_mosaic_pages_log', array() );
	$ids = array_filter( array( (int) ( $log['cs'] ?? 0 ), (int) ( $log['en'] ?? 0 ) ) );

	foreach ( $ids as $id ) {
		$post = get_post( $id );
		if ( ! $post instanceof WP_Post ) {
			continue;
		}
		if ( false === strpos( $post->post_content, 'acf/bio-featured-projects' ) ) {
			continue;
		}
		if ( false === strpos( $post->post_content, '"mode":"preview"' ) ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => str_replace( '"mode":"preview"', '"mode":"edit"', $post->post_content ),
			)
		);
	}

	update_option( 'bio_hp_mosaic_block_edit_mode', 1, false );
}
