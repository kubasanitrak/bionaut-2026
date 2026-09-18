<?php
/**
 * WPML table → Polylang mapping. Does not load WPML (3.9.3 is not PHP 8.2 safe).
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bio_pll_post_types( $types, $is_settings = false ) {
	$types['projekt']  = 'projekt';
	$types['newsitem'] = 'newsitem';
	$types['person']   = 'person';
	return $types;
}

function bio_pll_taxonomies( $taxes, $is_settings = false ) {
	$taxes['kategorie'] = 'kategorie';
	return $taxes;
}

add_filter( 'pll_get_post_types', 'bio_pll_post_types', 10, 2 );
add_filter( 'pll_get_taxonomies', 'bio_pll_taxonomies', 10, 2 );

function bio_deactivate_classic_editor() {
	if ( ! function_exists( 'deactivate_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
		deactivate_plugins( 'classic-editor/classic-editor.php' );
	}
}

function bio_ensure_polylang_languages() {
	if ( ! function_exists( 'PLL' ) ) {
		return array( 'error' => 'Polylang is not loaded.' );
	}

	$existing = function_exists( 'pll_languages_list' ) ? (array) pll_languages_list() : array();
	$model    = PLL()->model;

	if ( ! in_array( 'cs', $existing, true ) ) {
		$model->add_language(
			array(
				'name'       => 'Čeština',
				'slug'       => 'cs',
				'locale'     => 'cs_CZ',
				'rtl'        => 0,
				'term_group' => 0,
				'flag'       => 'cz',
			)
		);
	}

	if ( ! in_array( 'en', $existing, true ) ) {
		$model->add_language(
			array(
				'name'       => 'English',
				'slug'       => 'en',
				'locale'     => 'en_US',
				'rtl'        => 0,
				'term_group' => 0,
				'flag'       => 'us',
			)
		);
	}

	$options = get_option( 'polylang', array() );
	$options['default_lang']  = 'cs';
	$options['force_lang']    = 1;
	$options['hide_default']  = 1;
	$options['rewrite']       = 1;
	$options['redirect_lang'] = 0;
	$options['browser']       = 0;
	$options['media_support'] = 0;
	$options['post_types']    = array( 'post', 'page', 'projekt', 'newsitem', 'person' );
	$options['taxonomies']    = array( 'category', 'kategorie' );
	update_option( 'polylang', $options );

	if ( method_exists( $model, 'clean_languages_cache' ) ) {
		$model->clean_languages_cache();
	}

	return array( 'languages' => pll_languages_list() );
}

function bio_migrate_wpml_to_polylang() {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		return array( 'error' => 'Polylang API missing.' );
	}

	bio_ensure_polylang_languages();

	global $wpdb;
	$table = $wpdb->prefix . 'icl_translations';
	if ( ! $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) ) {
		return array( 'error' => 'icl_translations table not found.' );
	}

	$allowed = array( 'cs', 'en' );
	$posts   = $wpdb->get_results( "SELECT element_id, element_type, language_code, trid FROM {$table} WHERE element_type LIKE 'post_%' AND element_id > 0" );
	$by_trid = array();
	$set     = 0;

	foreach ( $posts as $row ) {
		if ( ! in_array( $row->language_code, $allowed, true ) ) {
			continue;
		}
		$id = (int) $row->element_id;
		if ( ! get_post( $id ) ) {
			continue;
		}
		pll_set_post_language( $id, $row->language_code );
		$by_trid[ (int) $row->trid ][ $row->language_code ] = $id;
		++$set;
	}

	$linked = 0;
	foreach ( $by_trid as $pair ) {
		if ( count( $pair ) > 1 ) {
			pll_save_post_translations( $pair );
			++$linked;
		}
	}

	$terms   = $wpdb->get_results( "SELECT element_id, element_type, language_code, trid FROM {$table} WHERE element_type LIKE 'tax_%' AND element_id > 0" );
	$t_trid  = array();
	$t_set   = 0;
	foreach ( $terms as $row ) {
		if ( ! in_array( $row->language_code, $allowed, true ) ) {
			continue;
		}
		$id = (int) $row->element_id;
		if ( ! get_term( $id ) ) {
			continue;
		}
		pll_set_term_language( $id, $row->language_code );
		$t_trid[ (int) $row->trid ][ $row->language_code ] = $id;
		++$t_set;
	}

	$t_linked = 0;
	foreach ( $t_trid as $pair ) {
		if ( count( $pair ) > 1 ) {
			pll_save_term_translations( $pair );
			++$t_linked;
		}
	}

	$untranslated = get_posts(
		array(
			'post_type'      => array( 'projekt', 'newsitem', 'person', 'page', 'post' ),
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	$assigned_default = 0;
	foreach ( $untranslated as $id ) {
		if ( ! pll_get_post_language( (int) $id ) ) {
			pll_set_post_language( (int) $id, 'cs' );
			++$assigned_default;
		}
	}

	$options = get_option( 'polylang', array() );
	$options['nav_menus']['bionaut-2026']['primary']['cs']  = 2;
	$options['nav_menus']['bionaut-2026']['primary']['en']  = 18;
	$options['nav_menus']['bionaut-2026']['fcbk-ig']['cs']  = 36;
	$options['nav_menus']['bionaut-2026']['fcbk-ig']['en']  = 37;
	update_option( 'polylang', $options );

	$mods = get_option( 'theme_mods_bionaut-2026', array() );
	if ( ! is_array( $mods ) ) {
		$mods = array();
	}
	$mods['nav_menu_locations']['primary'] = 2;
	$mods['nav_menu_locations']['fcbk-ig'] = 36;
	update_option( 'theme_mods_bionaut-2026', $mods );

	flush_rewrite_rules();

	$result = array(
		'posts_languaged'    => $set,
		'post_pairs'         => $linked,
		'terms_languaged'    => $t_set,
		'term_pairs'         => $t_linked,
		'defaulted_to_cs'    => $assigned_default,
		'languages'          => pll_languages_list(),
		'time'               => gmdate( 'c' ),
	);
	update_option( 'bio_wpml_migrated', 1, false );
	update_option( 'bio_wpml_migrated_log', $result, false );

	return $result;
}
