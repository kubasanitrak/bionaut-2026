<?php
/**
 * Register projekt CPT and kategorie taxonomy in PHP so slugs stay stable
 * even before ACF JSON is synced. person + newsitem stay in ACF JSON.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'bio_register_content_types' );

function bio_register_content_types() {
	register_post_type(
		'projekt',
		array(
			'labels'       => array(
				'name'          => __( 'Projekty', 'bionaut' ),
				'singular_name' => __( 'Projekt', 'bionaut' ),
			),
			'public'       => true,
			'has_archive'  => 'projekty',
			'hierarchical' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-video-alt2',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes', 'custom-fields' ),
			'rewrite'      => array(
				'slug'       => 'projekt',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'kategorie',
		'projekt',
		array(
			'labels'            => array(
				'name'          => __( 'Kategorie', 'bionaut' ),
				'singular_name' => __( 'Kategorie', 'bionaut' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array(
				'slug' => 'kategorie',
			),
		)
	);
}
