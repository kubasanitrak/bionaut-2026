<?php
/**
 * REST endpoints for paginated archive / news grids.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'bio_register_rest_routes' );
function bio_register_rest_routes() {
	register_rest_route(
		'bionaut/v1',
		'/projects',
		array(
			'methods'             => 'GET',
			'callback'            => 'bio_rest_projects',
			'permission_callback' => '__return_true',
			'args'                => array(
				'term'   => array(
					'required'          => true,
					'sanitize_callback' => 'absint',
				),
				'offset' => array(
					'default'           => BIO_ARCHIVE_VISIBLE,
					'sanitize_callback' => 'absint',
				),
				'lang'   => array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_key',
				),
			),
		)
	);

	register_rest_route(
		'bionaut/v1',
		'/news',
		array(
			'methods'             => 'GET',
			'callback'            => 'bio_rest_news',
			'permission_callback' => '__return_true',
			'args'                => array(
				'news_page' => array(
					'default'           => 2,
					'sanitize_callback' => 'absint',
				),
				'lang'      => array(
					'default'           => '',
					'sanitize_callback' => 'sanitize_key',
				),
			),
		)
	);
}

function bio_rest_projects( WP_REST_Request $request ) {
	$term_id = (int) $request['term'];
	$offset  = (int) $request['offset'];
	$lang    = (string) $request['lang'];

	$query = new WP_Query(
		bio_lang_query_args(
			array(
				'post_type'      => 'projekt',
				'post_status'    => 'publish',
				'posts_per_page' => 100,
				'offset'         => $offset,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'tax_query'      => array(
					array(
						'taxonomy' => 'kategorie',
						'field'    => 'term_id',
						'terms'    => $term_id,
					),
				),
			),
			$lang
		)
	);

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'parts/project', 'card' );
	}
	wp_reset_postdata();
	$html = ob_get_clean();

	$loaded = $offset + $query->post_count;
	$total  = (int) $query->found_posts;

	return new WP_REST_Response(
		array(
			'html'     => $html,
			'loaded'   => $loaded,
			'total'    => $total,
			'has_more' => $loaded < $total,
		),
		200
	);
}

function bio_rest_news( WP_REST_Request $request ) {
	$page  = max( 2, (int) $request['news_page'] );
	$lang  = (string) $request['lang'];
	$query = bio_news_query( $page, $lang );

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'parts/news', 'card' );
	}
	wp_reset_postdata();
	$html = ob_get_clean();

	return new WP_REST_Response(
		array(
			'html'     => $html,
			'page'     => $page,
			'total'    => (int) $query->found_posts,
			'has_more' => $page < (int) $query->max_num_pages,
		),
		200
	);
}
