<?php
/**
 * Frozen content model helpers.
 *
 * ACF is the source of truth after Phase 3. Cuztom keys remain a fallback.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Map of ACF field name => legacy Cuztom meta key.
 */
function bio_project_legacy_map() {
	return array(
		'featured'           => '_settings_featured',
		'featured_order'     => '_settings_featured_order',
		'website'            => '_settings_website',
		'eshop'              => '_settings_eshop',
		'prizes'             => '_settings_prizes',
		'horizontal_icons'   => '_settings_horizontal_icons',
		'icons'              => '_icons',
		'client_name'        => '_client_name',
		'credits_html'       => '_credits_credits_html',
		'cast_html'          => '_credits_cast_html',
		'trailers'           => '_video_trailer',
		'press_files'        => '_press_file',
		'press_dropbox'      => '_press_dropbox',
		'bgvideo_mp4'        => '_bgvideo_mp4',
		'bgvideo_webm'       => '_bgvideo_webm',
		'bgvideo_ogv'        => '_bgvideo_ogv',
		'playlist'           => '_playlist',
		'complex_playlist'   => '_complex_playlist',
	);
}

/**
 * Read an ACF value only if it was saved (_{name} holds a field_* key).
 * Avoids get_field() colliding with Cuztom bundles stored at _{name}.
 *
 * @return mixed|null Null when the field has never been saved.
 */
function bio_acf_saved( $name, $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$ref     = get_post_meta( $post_id, '_' . $name, true );
	if ( ! is_string( $ref ) || 0 !== strpos( $ref, 'field_' ) ) {
		return null;
	}
	if ( ! function_exists( 'get_field' ) ) {
		return get_post_meta( $post_id, $name, true );
	}
	return get_field( $name, $post_id );
}

function bio_project_field( $name, $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$saved   = bio_acf_saved( $name, $post_id );
	if ( null !== $saved ) {
		return $saved;
	}

	$map = bio_project_legacy_map();
	if ( isset( $map[ $name ] ) ) {
		return get_post_meta( $post_id, $map[ $name ], true );
	}

	return '';
}

function bio_is_on( $value ) {
	return 'on' === $value || 1 === $value || true === $value || '1' === $value;
}

/**
 * Trailer host: ACF select, then ID-shape detection, then YouTube.
 * Numeric IDs are treated as Vimeo (legacy default). Everything else is YouTube.
 */
function bio_trailer_service( $post_id = null, $trailer_id = '' ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$saved   = bio_acf_saved( 'trailer_service', $post_id );

	if ( in_array( $saved, array( 'youtube', 'vimeo' ), true ) ) {
		return $saved;
	}

	// Unsaved posts: numeric IDs are Vimeo (legacy), everything else YouTube.
	$id = bio_normalize_video_id( $trailer_id );
	if ( $id && ctype_digit( $id ) ) {
		return 'vimeo';
	}

	return 'youtube';
}

function bio_normalize_video_id( $raw ) {
	$raw = trim( (string) $raw );
	if ( '' === $raw ) {
		return '';
	}

	if ( preg_match( '~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:embed/|watch\?v=)|vimeo\.com/(?:video/)?)([A-Za-z0-9_-]+)~', $raw, $m ) ) {
		return $m[1];
	}

	$raw = preg_replace( '/[?&#].*$/', '', $raw );
	return preg_replace( '/[^A-Za-z0-9_-]/', '', $raw );
}

function bio_trailer_embed_url( $trailer_id, $post_id = null ) {
	$trailer_id = bio_normalize_video_id( $trailer_id );
	if ( '' === $trailer_id ) {
		return '';
	}

	$service = bio_trailer_service( $post_id, $trailer_id );
	if ( 'vimeo' === $service ) {
		return 'https://player.vimeo.com/video/' . rawurlencode( $trailer_id );
	}

	return 'https://www.youtube.com/embed/' . rawurlencode( $trailer_id );
}

function bio_show_cinema_listings( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$saved   = bio_acf_saved( 'show_cinema_listings', $post_id );

	if ( true === $saved || 1 === $saved || '1' === $saved ) {
		return true;
	}
	if ( false === $saved || 0 === $saved || '0' === $saved ) {
		return false;
	}

	return in_array( $post_id, array( 2947, 2949 ), true );
}

function bio_hide_attached_gallery( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$saved   = bio_acf_saved( 'hide_attached_gallery', $post_id );

	if ( true === $saved || 1 === $saved || '1' === $saved ) {
		return true;
	}
	if ( false === $saved || 0 === $saved || '0' === $saved ) {
		return false;
	}

	return in_array( $post_id, array( 334, 341, 501 ), true );
}

function bio_featured_badge_url( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$saved   = bio_acf_saved( 'featured_badge', $post_id );

	if ( is_array( $saved ) && ! empty( $saved['url'] ) ) {
		return $saved['url'];
	}
	if ( is_string( $saved ) && $saved ) {
		return $saved;
	}

	$legacy = array(
		1051 => get_template_directory_uri() . '/img/emmy-2020-winner.svg',
		1039 => get_template_directory_uri() . '/img/emmy-2020-winner.svg',
		1035 => content_url( '/uploads/2021/07/ShortFest_STUDENT-AN-KO_4.png' ),
		1030 => content_url( '/uploads/2021/07/ShortFest_STUDENT-AN-KO_4.png' ),
		1398 => content_url( '/uploads/2022/06/vavriny-PSH-KVIFF.png' ),
		1397 => content_url( '/uploads/2022/06/audience-award-PSH-KVIFF.png' ),
	);

	return $legacy[ $post_id ] ?? '';
}

function bio_has_bg_video( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	return (
		bio_project_field( 'bgvideo_mp4', $post_id )
		|| bio_project_field( 'bgvideo_webm', $post_id )
		|| bio_project_field( 'bgvideo_ogv', $post_id )
	);
}

function bio_original_id( $post = null ) {
	$post = $post ? get_post( $post ) : get_post();
	if ( ! $post ) {
		return 0;
	}

	return bio_translate_id( $post->ID, $post->post_type, 'cs' );
}

function bio_post_thumbnail( $size = 'normal', $post_id = null, $attr = array() ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$orig_id = bio_original_id( $post_id );

	if ( 'normal' === $size ) {
		$size = 'bio-thumb';
	} elseif ( 0 !== strpos( $size, 'bio-' ) ) {
		$size = 'bio-thumb-' . $size;
	}

	if ( ! has_post_thumbnail( $orig_id ) ) {
		return;
	}

	$attr = wp_parse_args(
		$attr,
		array(
			'alt'     => get_the_title( $post_id ),
			'loading' => 'lazy',
			'decoding' => 'async',
		)
	);

	echo get_the_post_thumbnail( $orig_id, $size, $attr );
}

function bio_texturize( $text ) {
	if ( class_exists( 'Zalomeni' ) && method_exists( 'Zalomeni', 'texturize' ) ) {
		return Zalomeni::texturize( $text );
	}

	return $text;
}

function bio_project_icons( $post_id = null ) {
	$icons = bio_project_field( 'icons', $post_id );
	if ( ! is_array( $icons ) || ! $icons ) {
		return array();
	}

	if ( isset( $icons[0]['_icon'] ) ) {
		return array_filter( wp_list_pluck( $icons, '_icon' ) );
	}

	$urls = array();
	foreach ( $icons as $row ) {
		if ( ! empty( $row['icon']['url'] ) ) {
			$urls[] = $row['icon']['url'];
		} elseif ( ! empty( $row['icon'] ) && is_numeric( $row['icon'] ) ) {
			$src = wp_get_attachment_image_url( (int) $row['icon'], 'full' );
			if ( $src ) {
				$urls[] = $src;
			}
		} elseif ( ! empty( $row['icon'] ) && is_string( $row['icon'] ) ) {
			$urls[] = $row['icon'];
		}
	}

	return $urls;
}

add_filter( 'acf/load_value/name=trailer_service', 'bio_acf_load_trailer_service', 10, 2 );
function bio_acf_load_trailer_service( $value, $post_id ) {
	if ( in_array( $value, array( 'youtube', 'vimeo' ), true ) ) {
		return $value;
	}

	$trailers = get_post_meta( (int) $post_id, '_video_trailer', true );
	$first    = '';
	if ( is_array( $trailers ) ) {
		$row   = reset( $trailers );
		$first = is_array( $row ) ? ( $row['trailer_id'] ?? reset( $row ) ) : $row;
	} elseif ( is_string( $trailers ) || is_numeric( $trailers ) ) {
		$first = $trailers;
	}

	return bio_trailer_service( (int) $post_id, (string) $first );
}

add_filter( 'acf/load_value/name=show_cinema_listings', 'bio_acf_load_show_cinema', 10, 2 );
function bio_acf_load_show_cinema( $value, $post_id ) {
	$ref = get_post_meta( (int) $post_id, '_show_cinema_listings', true );
	if ( is_string( $ref ) && 0 === strpos( $ref, 'field_' ) ) {
		return $value;
	}
	return in_array( (int) $post_id, array( 2947, 2949 ), true ) ? 1 : 0;
}

add_filter( 'acf/load_value/name=hide_attached_gallery', 'bio_acf_load_hide_gallery', 10, 2 );
function bio_acf_load_hide_gallery( $value, $post_id ) {
	$ref = get_post_meta( (int) $post_id, '_hide_attached_gallery', true );
	if ( is_string( $ref ) && 0 === strpos( $ref, 'field_' ) ) {
		return $value;
	}
	return in_array( (int) $post_id, array( 334, 341, 501 ), true ) ? 1 : 0;
}
