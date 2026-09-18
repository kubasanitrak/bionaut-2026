<?php
/**
 * One-time Cuztom → ACF migration for projekt posts.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bio_attachment_id_from_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return 0;
	}

	$candidates = array_unique(
		array(
			$url,
			str_replace( 'https://bionaut.cz', home_url(), $url ),
			str_replace( 'http://bionaut.cz', home_url(), $url ),
			preg_replace( '#-\d+x\d+(?=\.[a-z]+$)#i', '', $url ),
		)
	);

	foreach ( $candidates as $candidate ) {
		$id = attachment_url_to_postid( $candidate );
		if ( $id ) {
			return (int) $id;
		}
	}

	$filename = basename( (string) wp_parse_url( $url, PHP_URL_PATH ) );
	if ( ! $filename ) {
		return 0;
	}

	global $wpdb;
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s LIMIT 1",
			'%' . $wpdb->esc_like( $filename )
		)
	);
}

function bio_migrate_nonempty_rows( $rows, $required_keys ) {
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$out = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$keep = false;
		foreach ( $required_keys as $key ) {
			$val = $row[ $key ] ?? $row[ '_' . $key ] ?? '';
			if ( '' !== $val && null !== $val && array() !== $val ) {
				$keep = true;
				break;
			}
		}
		if ( $keep ) {
			$out[] = $row;
		}
	}

	return $out;
}

function bio_migrate_one_projekt( $post_id ) {
	$post_id = (int) $post_id;
	$log     = array();

	$featured = get_post_meta( $post_id, '_settings_featured', true );
	update_field( 'field_bio26_featured', bio_is_on( $featured ) ? 1 : 0, $post_id );

	$order = get_post_meta( $post_id, '_settings_featured_order', true );
	update_field( 'field_bio26_featured_order', (int) $order, $post_id );

	$website = get_post_meta( $post_id, '_settings_website', true );
	if ( $website ) {
		update_field( 'field_bio26_website', $website, $post_id );
	}

	$eshop = get_post_meta( $post_id, '_settings_eshop', true );
	if ( $eshop ) {
		update_field( 'field_bio26_eshop', $eshop, $post_id );
	}

	$client = get_post_meta( $post_id, '_client_name', true );
	if ( $client ) {
		update_field( 'field_bio26_client', $client, $post_id );
	}

	$prizes = get_post_meta( $post_id, '_settings_prizes', true );
	if ( is_array( $prizes ) ) {
		$prizes = $prizes[0] ?? '';
	}
	$prize_id = $prizes ? bio_attachment_id_from_url( $prizes ) : 0;
	if ( $prize_id ) {
		update_field( 'field_bio26_prizes', $prize_id, $post_id );
	}

	$horizontal = get_post_meta( $post_id, '_settings_horizontal_icons', true );
	update_field( 'field_bio26_horizontal_icons', bio_is_on( $horizontal ) ? 1 : 0, $post_id );

	$icon_rows = bio_migrate_nonempty_rows( get_post_meta( $post_id, '_icons', true ), array( 'icon', '_icon' ) );
	if ( $icon_rows ) {
		$acf_icons = array();
		foreach ( $icon_rows as $row ) {
			$url = $row['_icon'] ?? $row['icon'] ?? '';
			$id  = is_numeric( $url ) ? (int) $url : bio_attachment_id_from_url( $url );
			if ( $id ) {
				$acf_icons[] = array( 'icon' => $id );
			}
		}
		if ( $acf_icons ) {
			update_field( 'field_bio26_icons', $acf_icons, $post_id );
			$log[] = 'icons:' . count( $acf_icons );
		}
	}

	$trailers_raw = get_post_meta( $post_id, '_video_trailer', true );
	$trailer_ids  = array();
	if ( is_array( $trailers_raw ) ) {
		foreach ( $trailers_raw as $row ) {
			$id = is_array( $row ) ? ( $row['trailer_id'] ?? reset( $row ) ) : $row;
			$id = bio_normalize_video_id( $id );
			if ( $id ) {
				$trailer_ids[] = $id;
			}
		}
	} elseif ( is_string( $trailers_raw ) && '' !== $trailers_raw ) {
		$id = bio_normalize_video_id( $trailers_raw );
		if ( $id ) {
			$trailer_ids[] = $id;
		}
	}
	if ( $trailer_ids ) {
		$acf_trailers = array();
		foreach ( $trailer_ids as $tid ) {
			$acf_trailers[] = array( 'trailer_id' => $tid );
		}
		update_field( 'field_bio26_trailers', $acf_trailers, $post_id );
		update_field( 'field_bio26_trailer_service', bio_trailer_service( $post_id, $trailer_ids[0] ), $post_id );
		$log[] = 'trailers:' . count( $trailer_ids );
	} else {
		update_field( 'field_bio26_trailer_service', 'youtube', $post_id );
	}

	update_field( 'field_bio26_show_cinema', in_array( $post_id, array( 2947, 2949 ), true ) ? 1 : 0, $post_id );
	update_field( 'field_bio26_hide_gallery', in_array( $post_id, array( 334, 341, 501 ), true ) ? 1 : 0, $post_id );

	$badges = array(
		1051 => 1176,
		1039 => 1176,
		1035 => 1222,
		1030 => 1222,
		1398 => 1436,
		1397 => 1439,
	);
	if ( isset( $badges[ $post_id ] ) ) {
		update_field( 'field_bio26_featured_badge', $badges[ $post_id ], $post_id );
		$log[] = 'badge';
	}

	$credits = get_post_meta( $post_id, '_credits_credits_html', true );
	if ( $credits ) {
		update_field( 'field_bio26_credits', $credits, $post_id );
	}
	$cast = get_post_meta( $post_id, '_credits_cast_html', true );
	if ( $cast ) {
		update_field( 'field_bio26_cast', $cast, $post_id );
	}

	$press = get_post_meta( $post_id, '_press_file', true );
	$press_urls = array();
	if ( is_array( $press ) ) {
		$press_urls = $press;
	} elseif ( is_string( $press ) && $press ) {
		$press_urls = array( $press );
	}
	$acf_press = array();
	foreach ( $press_urls as $url ) {
		if ( is_array( $url ) ) {
			$url = $url['url'] ?? $url['file'] ?? reset( $url );
		}
		$file_id = bio_attachment_id_from_url( $url );
		if ( $file_id ) {
			$acf_press[] = array( 'file' => $file_id );
		}
	}
	if ( $acf_press ) {
		update_field( 'field_bio26_press_files', $acf_press, $post_id );
	}

	$dropbox = get_post_meta( $post_id, '_press_dropbox', true );
	if ( $dropbox ) {
		update_field( 'field_bio26_press_dropbox', $dropbox, $post_id );
	}

	foreach ( array( 'mp4' => 'field_bio26_bg_mp4', 'webm' => 'field_bio26_bg_webm', 'ogv' => 'field_bio26_bg_ogv' ) as $ext => $key ) {
		$url = get_post_meta( $post_id, '_bgvideo_' . $ext, true );
		$id  = $url ? bio_attachment_id_from_url( $url ) : 0;
		if ( $id ) {
			update_field( $key, $id, $post_id );
		}
	}

	$playlist = bio_migrate_nonempty_rows( get_post_meta( $post_id, '_playlist', true ), array( 'video_title', 'video_link', 'video_image' ) );
	if ( $playlist ) {
		$acf_pl = array();
		foreach ( $playlist as $item ) {
			$img = $item['_video_image'] ?? $item['video_image'] ?? '';
			$acf_pl[] = array(
				'video_title' => $item['_video_title'] ?? $item['video_title'] ?? '',
				'video_link'  => html_entity_decode( $item['_video_link'] ?? $item['video_link'] ?? '' ),
				'video_image' => is_numeric( $img ) ? (int) $img : bio_attachment_id_from_url( $img ),
			);
		}
		update_field( 'field_bio26_playlist', $acf_pl, $post_id );
		$log[] = 'playlist:' . count( $acf_pl );
	}

	$complex = bio_migrate_nonempty_rows(
		get_post_meta( $post_id, '_complex_playlist', true ),
		array( 'video_title', 'video_description', 'video_link', 'video_image' )
	);
	if ( $complex ) {
		$acf_cpl = array();
		foreach ( $complex as $item ) {
			$img = $item['_video_image'] ?? $item['video_image'] ?? '';
			$acf_cpl[] = array(
				'video_title'        => $item['_video_title'] ?? $item['video_title'] ?? '',
				'video_description'  => $item['_video_description'] ?? $item['video_description'] ?? '',
				'video_link'         => html_entity_decode( $item['_video_link'] ?? $item['video_link'] ?? '' ),
				'video_image'        => is_numeric( $img ) ? (int) $img : bio_attachment_id_from_url( $img ),
				'video_release_date' => $item['_video_release_date'] ?? $item['video_release_date'] ?? '',
				'video_service'      => $item['_video_service'] ?? $item['video_service'] ?? 'youtube',
			);
		}
		update_field( 'field_bio26_complex_playlist', $acf_cpl, $post_id );
		$log[] = 'complex:' . count( $acf_cpl );
	}

	return $log;
}

function bio_migrate_cuztom( $force = false ) {
	if ( ! function_exists( 'update_field' ) ) {
		return array( 'error' => 'ACF is not available.' );
	}

	if ( ! $force && get_option( 'bio_cuztom_migrated' ) ) {
		return get_option( 'bio_cuztom_migrated_log', array( 'skipped' => true ) );
	}

	$ids = get_posts(
		array(
			'post_type'      => 'projekt',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	$migrated = 0;
	$details  = array();
	foreach ( $ids as $id ) {
		$log = bio_migrate_one_projekt( (int) $id );
		++$migrated;
		if ( $log ) {
			$details[ $id ] = $log;
		}
	}

	$result = array(
		'count'   => $migrated,
		'details' => $details,
		'time'    => gmdate( 'c' ),
	);
	update_option( 'bio_cuztom_migrated', 1, false );
	update_option( 'bio_cuztom_migrated_log', $result, false );

	return $result;
}

function bio_inject_page_blocks() {
	$blocks = array(
		1653 => "<!-- wp:acf/bio-news-grid {\"name\":\"acf/bio-news-grid\",\"mode\":\"preview\"} /-->\n",
		1656 => "<!-- wp:acf/bio-news-grid {\"name\":\"acf/bio-news-grid\",\"mode\":\"preview\"} /-->\n",
		2014 => "<!-- wp:acf/bio-people {\"name\":\"acf/bio-people\",\"mode\":\"preview\"} /-->\n",
		2022 => "<!-- wp:acf/bio-people {\"name\":\"acf/bio-people\",\"mode\":\"preview\"} /-->\n",
		1568 => "<!-- wp:acf/bio-book {\"name\":\"acf/bio-book\",\"data\":{\"pdf_url\":\"https://bionaut.cz/wp-content/uploads/2024/02/Bionaut-book-v2024.pdf\"},\"mode\":\"preview\"} /-->\n",
		1570 => "<!-- wp:acf/bio-book {\"name\":\"acf/bio-book\",\"data\":{\"pdf_url\":\"https://bionaut.cz/wp-content/uploads/2024/02/Bionaut-book-v2024.pdf\"},\"mode\":\"preview\"} /-->\n",
	);

	foreach ( $blocks as $id => $markup ) {
		$post = get_post( $id );
		if ( ! $post ) {
			continue;
		}
		if ( has_blocks( $post->post_content ) && false !== strpos( $post->post_content, 'acf/bio-' ) ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => $markup . $post->post_content,
			)
		);
	}
}
