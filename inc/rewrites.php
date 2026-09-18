<?php
/**
 * Keep slugless projekt permalinks: /film-name/ not /projekt/film-name/.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Custom menu items store root-relative paths ("/projekty"). Those resolve
 * against the host, so on a subdirectory install they 404. Prefix them with
 * home_url() before WordPress marks the current item.
 *
 * @param array $items Menu items.
 * @return array
 */
function bio_home_relative_menu_items( $items ) {
	if ( ! is_array( $items ) ) {
		return $items;
	}

	foreach ( $items as $item ) {
		if ( ! is_object( $item ) || empty( $item->url ) || ! is_string( $item->url ) ) {
			continue;
		}
		if ( isset( $item->type ) && 'custom' !== $item->type ) {
			continue;
		}
		if ( ! preg_match( '#^/[^/]#', $item->url ) ) {
			continue;
		}
		$item->url = home_url( $item->url );
	}

	return $items;
}
add_filter( 'wp_get_nav_menu_items', 'bio_home_relative_menu_items' );

add_filter( 'post_type_link', 'bio_remove_cpt_slug', 10, 2 );
function bio_remove_cpt_slug( $post_link, $post ) {
	if ( ! $post instanceof WP_Post ) {
		return $post_link;
	}

	if ( 'projekt' !== $post->post_type || 'publish' !== $post->post_status ) {
		return $post_link;
	}

	return str_replace( '/' . $post->post_type . '/', '/', $post_link );
}

add_action( 'parse_query', 'bio_prefer_language_page', 5 );
function bio_prefer_language_page( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! function_exists( 'pll_current_language' ) ) {
		return;
	}

	$lang = pll_current_language();
	if ( ! $lang ) {
		return;
	}

	$slug = $query->get( 'pagename' );
	if ( ! $slug ) {
		$slug = $query->get( 'name' );
	}
	$slug = trim( (string) $slug, '/' );
	if ( '' === $slug || false !== strpos( $slug, '/' ) ) {
		return;
	}

	$pages = get_posts(
		array(
			'name'             => $slug,
			'post_type'        => 'page',
			'post_status'      => 'publish',
			'posts_per_page'   => 20,
			'suppress_filters' => true,
		)
	);
	foreach ( $pages as $page ) {
		if ( pll_get_post_language( $page->ID ) === $lang ) {
			$query->set( 'page_id', (int) $page->ID );
			$query->set( 'pagename', '' );
			$query->set( 'name', '' );
			$query->set( 'page', '' );
			break;
		}
	}
}

add_action( 'template_redirect', 'bio_legacy_redirects', 1 );
function bio_legacy_redirects() {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( $home && '/' !== $home && 0 === strpos( $path, untrailingslashit( $home ) ) ) {
		$path = substr( $path, strlen( untrailingslashit( $home ) ) );
	}
	$path = trim( (string) $path, '/' );
	if ( '' === $path ) {
		return;
	}

	if ( preg_match( '#^(en/)?projekt/([^/]+)/?$#', $path, $m ) ) {
		$prefix = ! empty( $m[1] ) ? '/en/' : '/';
		wp_safe_redirect( home_url( $prefix . $m[2] . '/' ), 301 );
		exit;
	}
}

add_action( 'pre_get_posts', 'bio_parse_request_cpt_slug' );
function bio_parse_request_cpt_slug( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( $query->get( 'page_id' ) ) {
		return;
	}

	$q = $query->query;
	unset( $q['lang'] );
	foreach ( array_keys( $q ) as $key ) {
		if ( 0 === strpos( (string) $key, 'pll_' ) ) {
			unset( $q[ $key ] );
		}
	}

	if ( 2 !== count( $q ) || ! isset( $q['page'] ) ) {
		return;
	}

	if ( ! empty( $query->query['name'] ) ) {
		if ( function_exists( 'pll_current_language' ) ) {
			$lang = pll_current_language();
			if ( $lang ) {
				$query->set( 'lang', $lang );
			}
		}
		$query->set( 'post_type', array( 'post', 'projekt', 'page', 'person', 'newsitem' ) );
	}
}
