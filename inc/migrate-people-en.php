<?php
/**
 * English twins for Czech person posts, then wire them into the English Kontakt repeater.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string,mixed>
 */
function bio_migrate_people_en( $force = false ) {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) || ! function_exists( 'pll_get_post' ) ) {
		return array( 'error' => 'Polylang API missing.' );
	}
	if ( ! function_exists( 'update_field' ) ) {
		return array( 'error' => 'ACF is not available.' );
	}
	if ( ! $force && get_option( 'bio_people_en' ) ) {
		return get_option( 'bio_people_en_log', array( 'skipped' => true ) );
	}

	$cs_posts = get_posts(
		array(
			'post_type'      => 'person',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'lang'           => 'cs',
			'orderby'        => 'ID',
			'order'          => 'ASC',
		)
	);

	$map     = array();
	$created = 0;
	$updated = 0;

	foreach ( $cs_posts as $cs ) {
		$cs_id = (int) $cs->ID;
		$en_id = (int) pll_get_post( $cs_id, 'en' );

		if ( $en_id && 'person' !== get_post_type( $en_id ) ) {
			$en_id = 0;
		}

		if ( ! $en_id ) {
			$en_id = wp_insert_post(
				array(
					'post_type'   => 'person',
					'post_status' => 'publish',
					'post_title'  => $cs->post_title,
					'post_name'   => $cs->post_name,
				),
				true
			);
			if ( is_wp_error( $en_id ) ) {
				continue;
			}
			$en_id = (int) $en_id;
			pll_set_post_language( $en_id, 'en' );
			pll_save_post_translations(
				array(
					'cs' => $cs_id,
					'en' => $en_id,
				)
			);
			wp_update_post(
				array(
					'ID'        => $en_id,
					'post_name' => $cs->post_name,
				)
			);
			++$created;
		} else {
			++$updated;
		}

		update_field( 'person_img', (int) get_post_meta( $cs_id, 'person_img', true ), $en_id );
		update_field( 'person_mail_to', (string) get_post_meta( $cs_id, 'person_mail_to', true ), $en_id );
		update_field( 'person_mail_to_secondary', (string) get_post_meta( $cs_id, 'person_mail_to_secondary', true ), $en_id );
		$map[ $cs_id ] = $en_id;
	}

	$linked = 0;
	$cs_page = 2014;
	$en_page = 2022;
	if ( get_post( $cs_page ) && get_post( $en_page ) ) {
		$meta = get_post_meta( $cs_page );
		foreach ( $meta as $key => $values ) {
			if ( ! preg_match( '/^personel_sections_container_\d+_personel_section_row_\d+_person_obj$/', $key ) ) {
				continue;
			}
			$cs_person = (int) ( $values[0] ?? 0 );
			if ( ! $cs_person || empty( $map[ $cs_person ] ) ) {
				continue;
			}
			update_post_meta( $en_page, $key, $map[ $cs_person ] );
			++$linked;
		}
	}

	$result = array(
		'created' => $created,
		'updated' => $updated,
		'linked'  => $linked,
		'map'     => $map,
		'time'    => gmdate( 'c' ),
	);
	update_option( 'bio_people_en', 1, false );
	update_option( 'bio_people_en_log', $result, false );

	return $result;
}

add_action( 'init', 'bio_maybe_migrate_people_en', 40 );
function bio_maybe_migrate_people_en() {
	if ( function_exists( 'wp_installing' ) && wp_installing() ) {
		return;
	}
	if ( ! get_option( 'bio_people_en' ) ) {
		bio_migrate_people_en();
	}
}
