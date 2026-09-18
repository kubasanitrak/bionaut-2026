<?php
/**
 * Local performance cleanup: heavy plugins, dead cron, lighter front assets.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'bio_performance_cleanup', 20 );
function bio_performance_cleanup() {
	if ( function_exists( 'opcache_reset' ) && ! get_option( 'bio_p5_opcache_reset' ) ) {
		opcache_reset();
		update_option( 'bio_p5_opcache_reset', 1, false );
	}
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$local_off = array(
		'wordfence/wordfence.php',
		'wp-super-cache/wp-cache.php',
		'duplicator-pro/duplicator-pro.php',
		'classic-editor/classic-editor.php',
	);
	foreach ( $local_off as $plugin ) {
		if ( is_plugin_active( $plugin ) ) {
			deactivate_plugins( $plugin, true );
		}
	}

	wp_clear_scheduled_hook( 'pp_import_playlists_hook' );

	if ( ! is_plugin_active( 'wordfence/wordfence.php' ) ) {
		wp_clear_scheduled_hook( 'wordfence_hourly_cron' );
		wp_clear_scheduled_hook( 'wordfence_daily_cron' );
		wp_clear_scheduled_hook( 'wordfence_daily_autoUpdate' );
		wp_clear_scheduled_hook( 'wordfence_ls_ntp_cron' );
	}
}

add_action( 'wp_enqueue_scripts', 'bio_dequeue_front_noise', 100 );
function bio_dequeue_front_noise() {
	if ( is_admin() ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
	wp_dequeue_style( 'global-styles' );
	wp_deregister_script( 'wp-embed' );
}

add_action( 'init', 'bio_disable_emoji', 1 );
function bio_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}

add_filter( 'wp_lazy_loading_enabled', '__return_true' );

add_filter( 'heartbeat_settings', 'bio_slow_heartbeat' );
function bio_slow_heartbeat( $settings ) {
	$settings['interval'] = 60;
	return $settings;
}
