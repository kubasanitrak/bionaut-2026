<?php
/**
 * Cinema listings from the disfilm API.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bio_performance_list_markup() {
	$begin = gmdate( 'Y-m-d' );
	$end   = gmdate( 'Y-12-31', strtotime( '+1 year' ) );
	$url   = 'https://app.disfilm.cz/api/performances.ashx?key=bzg3D&begin=' . rawurlencode( $begin ) . '&end=' . rawurlencode( $end );

	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 8,
		)
	);

	if ( is_wp_error( $response ) ) {
		return '';
	}

	$body = wp_remote_retrieve_body( $response );
	if ( ! $body ) {
		return '';
	}

	$xml = simplexml_load_string( $body );
	if ( ! $xml ) {
		return '';
	}

	$array = json_decode( wp_json_encode( $xml ), true );
	if ( ! is_array( $array ) ) {
		return '';
	}

	$markup = '<div class="project__links">';
	foreach ( $array as $arr_item ) {
		if ( ! is_array( $arr_item ) ) {
			continue;
		}
		foreach ( $arr_item as $perf ) {
			if ( empty( $perf['date'] ) ) {
				continue;
			}
			$markup .= '<div class="proj-item">';
			$markup .= '<p class="title-date">';
			$markup .= esc_html( date_i18n( 'j. n. Y', strtotime( $perf['date'] ) ) );
			$markup .= ' ';
			if ( ! empty( $perf['cinemaTown'] ) ) {
				$markup .= '<span class="cinema-address">' . esc_html( $perf['cinemaTown'] ) . '</span> ';
			}
			if ( ! empty( $perf['cinemaName'] ) ) {
				$markup .= '<span class="cinema-name">' . esc_html( $perf['cinemaName'] ) . '</span>';
			}
			$markup .= '</p></div>';
		}
	}
	$markup .= '</div>';

	return $markup;
}
