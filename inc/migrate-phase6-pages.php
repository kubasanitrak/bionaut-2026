<?php
/**
 * One-shot Phase 6 page conversion: Gutenberg blocks, templates, local SEO copy.
 * Run from CLI. Does not deploy.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bio_phase6_paragraph_block( $html ) {
	$html = trim( $html );
	if ( '' === $html || '&nbsp;' === $html ) {
		return '';
	}
	if ( ! preg_match( '/^<p[\s>]/i', $html ) ) {
		$html = '<p>' . $html . '</p>';
	}
	return "<!-- wp:paragraph -->\n{$html}\n<!-- /wp:paragraph -->\n";
}

function bio_phase6_classic_to_blocks( $html ) {
	$html = trim( (string) $html );
	if ( '' === $html ) {
		return '';
	}
	if ( has_blocks( $html ) && false !== strpos( $html, '<!-- wp:' ) ) {
		return $html;
	}

	$parts  = preg_split( '/\r\n\r\n|\n\n/', $html );
	$blocks = array();
	foreach ( $parts as $part ) {
		$part = trim( $part );
		if ( '' === $part || '&nbsp;' === $part ) {
			continue;
		}
		if ( preg_match( '/^<(h[1-6]|ul|ol|div|table|blockquote|iframe)/i', $part ) ) {
			$blocks[] = "<!-- wp:html -->\n{$part}\n<!-- /wp:html -->";
		} else {
			$blocks[] = trim( bio_phase6_paragraph_block( $part ) );
		}
	}

	return implode( "\n\n", $blocks ) . "\n";
}

function bio_phase6_keep_acf_plus_html( $content ) {
	if ( ! preg_match( '/<!-- wp:acf\/[^\n]*-->/', $content, $m, PREG_OFFSET_CAPTURE ) ) {
		return $content;
	}

	$block = $m[0][0];
	$rest  = trim( substr( $content, $m[0][1] + strlen( $m[0][0] ) ) );
	$rest  = preg_replace( '/^\[flipbook[^\]]*\]/s', '', $rest );
	$rest  = preg_replace( '/obsah se automaticky načítá[^<]*/u', '', $rest );
	$rest  = trim( $rest, " \t\n\r\0\x0B\xC2\xA0" );
	$rest  = preg_replace( '/^(&nbsp;|\s)+|(&nbsp;|\s)+$/u', '', $rest );

	$out = $block . "\n";
	if ( '' !== $rest && '&nbsp;' !== $rest ) {
		$out .= "\n<!-- wp:html -->\n<div class=\"customcols-container\">\n{$rest}\n</div>\n<!-- /wp:html -->\n";
	}

	return $out;
}

function bio_phase6_spark_blocks() {
	return <<<'HTML'
<!-- wp:heading {"level":3,"className":"spark-h3"} -->
<h3 class="wp-block-heading spark-h3"><span class="color-yellow">Máte nápad, námět, předlohu nebo rovnou scénář, který vám nedá&nbsp;spát?</span> Máte chuť ho nastartovat na&nbsp;planetě Bionaut? Pošlete nám&nbsp;ho.</h3>
<!-- /wp:heading -->

<!-- wp:heading {"level":3,"className":"spark-h3"} -->
<h3 class="wp-block-heading spark-h3">Co hledáme?</h3>
<!-- /wp:heading -->

<!-- wp:list {"className":"custom-list custom-list--bullet"} -->
<ul class="wp-block-list custom-list custom-list--bullet">
<li>originální náměty na celovečerní filmy a&nbsp;seriály všech&nbsp;žánrů</li>
<li>silné příběhy a&nbsp;náměty na dokumentární&nbsp;filmy</li>
<li>literární díla vhodná pro audiovizuální&nbsp;zpracování</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":4,"className":"spark-h4"} -->
<h4 class="wp-block-heading spark-h4">Své náměty a&nbsp;scénáře pošlete na <a href="mailto:spark@bionaut.cz" class="color-yellow">spark@bionaut.cz</a>.</h4>
<!-- /wp:heading -->

<!-- wp:heading {"level":4,"className":"spark-h4"} -->
<h4 class="wp-block-heading spark-h4">Ke všem projektům se vyjádříme nejpozději do&nbsp;<span class="color-yellow">60 dní</span>.</h4>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"spark-plain"} -->
<p class="spark-plain">Ideálně připojte i&nbsp;krátké shrnutí projektu v&nbsp;rozsahu jedné strany a&nbsp;autorskou explikaci. U&nbsp;dokumentárních námětů, literárních předloh a&nbsp;příběhů podle skutečných událostí i&nbsp;krátkou informaci o&nbsp;zajištění práv.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":4,"className":"spark-h4"} -->
<h4 class="wp-block-heading spark-h4">Cesta ke hvězdám začíná jiskrou! ✨</h4>
<!-- /wp:heading -->

<!-- wp:buttons {"className":"cta-container"} -->
<div class="wp-block-buttons cta-container"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="mailto:spark@bionaut.cz">Poslat projekt</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
HTML;
}

function bio_phase6_videonavody_blocks() {
	return <<<'HTML'
<!-- wp:heading {"className":"video-tut-label"} -->
<h2 class="wp-block-heading video-tut-label">Login</h2>
<!-- /wp:heading -->

<!-- wp:html -->
<div style="padding:56.25% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/878732855?h=e4c3551f4f&amp;badge=0&amp;autopause=0&amp;quality_selector=1&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;" title="01-Bionaut-admin-login" loading="lazy"></iframe></div>
<!-- /wp:html -->

<!-- wp:heading {"className":"video-tut-label"} -->
<h2 class="wp-block-heading video-tut-label">News admin</h2>
<!-- /wp:heading -->

<!-- wp:html -->
<div style="padding:56.25% 0 0 0;position:relative;"><iframe src="https://player.vimeo.com/video/878732883?h=b46c907fbe&amp;badge=0&amp;autopause=0&amp;quality_selector=1&amp;player_id=0&amp;app_id=58479" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" style="position:absolute;top:0;left:0;width:100%;height:100%;" title="02-Bionaut-news-aktuality" loading="lazy"></iframe></div>
<!-- /wp:html -->
HTML;
}

function bio_migrate_phase6_pages( $force = false ) {
	if ( ! $force && get_option( 'bio_phase6_pages' ) ) {
		return get_option( 'bio_phase6_pages_log', array( 'skipped' => true ) );
	}

	$log = array();

	$about_cs = get_post( 537 );
	$about_en = get_post( 532 );
	if ( $about_cs ) {
		wp_update_post(
			array(
				'ID'           => 537,
				'post_content' => bio_phase6_classic_to_blocks( $about_cs->post_content ),
			)
		);
		$log[537] = 'about-cs';
	}
	if ( $about_en ) {
		wp_update_post(
			array(
				'ID'           => 532,
				'post_content' => bio_phase6_classic_to_blocks( $about_en->post_content ),
			)
		);
		$log[532] = 'about-en';
	}

	foreach ( array( 1656, 1653 ) as $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => "<!-- wp:acf/bio-news-grid {\"name\":\"acf/bio-news-grid\",\"mode\":\"preview\"} /-->\n",
			)
		);
		$log[ $id ] = 'news';
	}

	foreach ( array( 2014, 2022 ) as $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => bio_phase6_keep_acf_plus_html( $post->post_content ),
			)
		);
		$log[ $id ] = 'kontakt';
	}

	$book_block = "<!-- wp:acf/bio-book {\"name\":\"acf/bio-book\",\"data\":{\"pdf_url\":\"https://bionaut.cz/wp-content/uploads/2024/02/Bionaut-book-v2024.pdf\"},\"mode\":\"preview\"} /-->\n";
	foreach ( array( 1568, 1570 ) as $id ) {
		if ( ! get_post( $id ) ) {
			continue;
		}
		wp_update_post(
			array(
				'ID'           => $id,
				'post_content' => $book_block,
			)
		);
		update_post_meta( $id, '_wp_page_template', 'page-bop.php' );
		$log[ $id ] = 'bop';
	}

	if ( get_post( 3744 ) ) {
		wp_update_post(
			array(
				'ID'           => 3744,
				'post_content' => bio_phase6_spark_blocks(),
			)
		);
		update_post_meta( 3744, '_wp_page_template', 'page-spark.php' );
		$log[3744] = 'spark';
	}

	if ( get_post( 1666 ) ) {
		wp_update_post(
			array(
				'ID'           => 1666,
				'post_content' => bio_phase6_videonavody_blocks(),
			)
		);
		$log[1666] = 'videonavody';
	}

	$gdpr = get_post( 948 );
	if ( $gdpr && false === strpos( $gdpr->post_content, '<!-- wp:' ) ) {
		wp_update_post(
			array(
				'ID'           => 948,
				'post_content' => bio_phase6_classic_to_blocks( $gdpr->post_content ),
			)
		);
		$log[948] = 'gdpr';
	}

	update_post_meta( 2022, 'personel_sections_container_0_personel_section_title', 'Producers' );

	$en_desc = 'Bionaut is a leading Czech film and TV production company, awarded an International Emmy, Czech Lion and Czech Film Critics Award, founded in 1999.';
	update_post_meta( 2022, '_yoast_wpseo_metadesc', $en_desc );
	update_post_meta( 2022, '_yoast_wpseo_opengraph-description', $en_desc );
	update_post_meta( 2022, '_yoast_wpseo_twitter-description', $en_desc );
	update_post_meta( 2022, '_yoast_wpseo_focuskw', 'Bionaut Contact' );

	update_post_meta( 62, '_menu_item_url', '/en/projekty' );

	if ( function_exists( 'PLL' ) && PLL() && isset( PLL()->options ) && method_exists( PLL()->options, 'set' ) ) {
		$nav_err = PLL()->options->set(
			'nav_menus',
			array(
				'bionaut-2026' => array(
					'primary' => array(
						'cs' => 2,
						'en' => 18,
					),
					'fcbk-ig' => array(
						'cs' => 36,
						'en' => 37,
					),
				),
			)
		);
		if ( ! is_wp_error( $nav_err ) || ! $nav_err->has_errors() ) {
			PLL()->options->save();
			$log['menus'] = 'polylang-nav-assigned';
		} else {
			$log['menus'] = $nav_err->get_error_message();
		}
	}

	$draft = get_post( 3724 );
	if ( $draft && 'draft' === $draft->post_status && '' === $draft->post_name ) {
		wp_trash_post( 3724 );
		$log[3724] = 'trashed-draft-kontakt';
	}

	$result = array(
		'log'  => $log,
		'time' => gmdate( 'c' ),
	);

	global $wpdb;
	$shared_slugs = array(
		2014 => 'kontakt',
		2022 => 'kontakt',
		1656 => 'news',
		1653 => 'news',
		1568 => 'bop-v2024',
		1570 => 'bop-v2024',
	);
	foreach ( $shared_slugs as $id => $slug ) {
		$wpdb->update( $wpdb->posts, array( 'post_name' => $slug ), array( 'ID' => $id ) );
		clean_post_cache( $id );
	}

	update_option( 'bio_phase6_pages', 1, false );
	update_option( 'bio_phase6_pages_log', $result, false );

	return $result;
}
