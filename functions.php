<?php
/**
 * Bionaut 2026 theme bootstrap.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BIO_THEME_VERSION', '2026.1.0' );

require get_template_directory() . '/inc/setup.php';
require get_template_directory() . '/inc/i18n.php';
require get_template_directory() . '/inc/cpt.php';
require get_template_directory() . '/inc/meta.php';
require get_template_directory() . '/inc/queries.php';
require get_template_directory() . '/inc/blocks.php';
require get_template_directory() . '/inc/rewrites.php';
require get_template_directory() . '/inc/enqueue.php';
require get_template_directory() . '/inc/cinema.php';
require get_template_directory() . '/inc/ajax.php';
require get_template_directory() . '/inc/performance.php';
require get_template_directory() . '/inc/migrate-cuztom.php';
require get_template_directory() . '/inc/migrate-wpml.php';
require get_template_directory() . '/inc/migrate-phase6-pages.php';
require get_template_directory() . '/inc/migrate-hp-mosaic.php';
