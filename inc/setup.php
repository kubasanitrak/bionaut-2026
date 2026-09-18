<?php
/**
 * Theme supports, menus, image sizes, ACF JSON paths.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', 'bio_setup' );

function bio_setup() {
	load_theme_textdomain( 'bionaut', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'automatic-feed-links' );
	add_editor_style( 'assets/css/main.css' );

	add_image_size( 'bio-thumb-small', 200, 115, true );
	add_image_size( 'bio-thumb', 400, 225, true );
	add_image_size( 'bio-thumb-medium', 600, 400, true );
	add_image_size( 'bio-thumb-big', 800, 450, true );
	add_image_size( 'bio-background-img', 1680, 1080, true );
	add_image_size( 'bio-contact-thumb', 250, 165, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'bionaut' ),
			'fcbk-ig' => __( 'Social links', 'bionaut' ),
		)
	);

	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => __( 'Theme Settings', 'bionaut' ),
				'menu_title' => __( 'Theme Settings', 'bionaut' ),
				'menu_slug'  => 'acf-options-theme-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
			)
		);
	}
}

add_filter( 'acf/settings/save_json', 'bio_acf_json_save_point' );
add_filter( 'acf/settings/load_json', 'bio_acf_json_load_point' );

function bio_acf_json_save_point() {
	return get_template_directory() . '/acf-json';
}

function bio_acf_json_load_point( $paths ) {
	unset( $paths[0] );
	$paths[] = get_template_directory() . '/acf-json';
	return $paths;
}

add_action( 'after_switch_theme', 'bio_after_switch_theme' );
function bio_after_switch_theme() {
	flush_rewrite_rules();
}

add_filter( 'document_title_parts', 'bio_front_page_document_title' );
function bio_front_page_document_title( $parts ) {
	if ( is_front_page() ) {
		unset( $parts['title'] );
	}
	return $parts;
}

add_filter( 'wpseo_title', 'bio_front_page_seo_title' );
function bio_front_page_seo_title( $title ) {
	if ( is_front_page() ) {
		return get_bloginfo( 'name' );
	}
	return $title;
}
