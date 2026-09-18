<?php
/**
 * Language helpers. Polylang first, WPML shims as fallback.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bio_current_language() {
	if ( function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language();
		if ( $lang ) {
			return $lang;
		}
	}

	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		return ICL_LANGUAGE_CODE;
	}

	return 'cs';
}

/**
 * Pick a Czech or English string from the current language.
 *
 * @param string $cs Czech copy.
 * @param string $en English copy.
 * @return string
 */
function bio_string( $cs, $en ) {
	return 'en' === bio_current_language() ? $en : $cs;
}

function bio_gdpr_url() {
	$page = get_page_by_path( 'gdpr' );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}

	return home_url( '/gdpr/' );
}

function bio_home_url() {
	if ( function_exists( 'pll_home_url' ) ) {
		return pll_home_url();
	}

	if ( function_exists( 'icl_get_home_url' ) ) {
		return icl_get_home_url();
	}

	return home_url( '/' );
}

function bio_translate_id( $id, $type = 'post', $lang = 'cs' ) {
	$id = (int) $id;

	$is_term = ( 0 === strpos( (string) $type, 'tax_' ) ) || taxonomy_exists( (string) $type );
	if ( $is_term && function_exists( 'pll_get_term' ) ) {
		$translated = pll_get_term( $id, $lang );
		return $translated ? (int) $translated : $id;
	}

	if ( function_exists( 'pll_get_post' ) ) {
		$translated = pll_get_post( $id, $lang );
		return $translated ? (int) $translated : $id;
	}

	if ( function_exists( 'icl_object_id' ) ) {
		$translated = icl_object_id( $id, $type, true, $lang );
		return $translated ? (int) $translated : $id;
	}

	return $id;
}

function bio_language_switcher() {
	if ( function_exists( 'pll_the_languages' ) ) {
		echo '<ul class="lang-switcher__list">';
		pll_the_languages(
			array(
				'show_flags' 	=> 0,
				'show_names' 	=> 1,
				'echo'       	=> 1,
				'hide_current'  => 1,
			)
		);
		echo '</ul>';
		return;
	}

	do_action( 'icl_language_selector' );
}
