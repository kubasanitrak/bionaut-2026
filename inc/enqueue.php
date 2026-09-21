<?php
/**
 * Front-end assets. Block library CSS stays loaded. No jQuery on the front.
 *
 * @package bionaut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', 'bio_enqueue_assets' );

function bio_enqueue_assets() {
	$css_rel  = '/assets/css/main.css';
	$css_path = get_template_directory() . $css_rel;
	$js_rel   = '/assets/js/main.js';
	$js_path  = get_template_directory() . $js_rel;

	wp_enqueue_style(
		'bionaut-main',
		get_template_directory_uri() . $css_rel,
		array(),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : BIO_THEME_VERSION
	);

	wp_enqueue_script(
		'bionaut-main',
		get_template_directory_uri() . $js_rel,
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : BIO_THEME_VERSION,
		true
	);

	wp_localize_script(
		'bionaut-main',
		'bioFront',
		array(
			'rest' => esc_url_raw( rest_url( 'bionaut/v1/' ) ),
			'lang' => bio_current_language(),
		)
	);

	if ( bio_needs_gallery_lightbox() ) {
		bio_enqueue_gallery_lightbox();
	}
}

add_action( 'enqueue_block_assets', 'bio_enqueue_editor_mosaic_css' );
function bio_enqueue_editor_mosaic_css() {
	if ( ! is_admin() ) {
		return;
	}

	wp_register_style( 'bionaut-editor-mosaic', false, array(), BIO_THEME_VERSION );
	wp_enqueue_style( 'bionaut-editor-mosaic' );
	wp_add_inline_style(
		'bionaut-editor-mosaic',
		'.hp-mosaic--editor{position:relative;min-height:8rem}'
		. '.hp-mosaic__editor-shield{display:block;position:absolute;inset:0;z-index:20;cursor:pointer}'
		. '.hp-mosaic--editor a,.hp-mosaic--editor .hp-tile{pointer-events:none}'
	);
}

/**
 * Whether stills gallery markup (and LiteLight) should load on this request.
 */
function bio_needs_gallery_lightbox() {
	if ( is_singular( 'projekt' ) && ! bio_hide_attached_gallery() ) {
		return bio_has_stills_attachments();
	}

	if ( is_singular() && has_block( 'acf/bio-gallery' ) && ! bio_hide_attached_gallery() ) {
		return bio_has_stills_attachments();
	}

	return false;
}

/**
 * True when the project (or translation original) has image attachments for stills.
 */
function bio_has_stills_attachments( $post_id = null ) {
	$orig_id = bio_original_id( $post_id );
	if ( ! $orig_id ) {
		return false;
	}

	$images = get_children(
		array(
			'post_parent'    => $orig_id,
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'numberposts'    => 1,
			'post__not_in'   => array( (int) get_post_thumbnail_id( $orig_id ) ),
		)
	);

	return ! empty( $images );
}

/**
 * Conditionally load LiteLight + theme init for .gallery thumbs.
 */
function bio_enqueue_gallery_lightbox() {
	$base     = get_template_directory();
	$base_uri = get_template_directory_uri();
	$css_rel  = '/assets/vendor/litelight/lite-light.min.css';
	$lib_rel  = '/assets/vendor/litelight/lite-light.umd.min.js';
	$init_rel = '/assets/js/gallery-lightbox.js';

	wp_enqueue_style(
		'bionaut-litelight',
		$base_uri . $css_rel,
		array( 'bionaut-main' ),
		file_exists( $base . $css_rel ) ? (string) filemtime( $base . $css_rel ) : BIO_THEME_VERSION
	);

	wp_enqueue_script(
		'bionaut-litelight',
		$base_uri . $lib_rel,
		array(),
		file_exists( $base . $lib_rel ) ? (string) filemtime( $base . $lib_rel ) : BIO_THEME_VERSION,
		true
	);

	wp_enqueue_script(
		'bionaut-gallery-lightbox',
		$base_uri . $init_rel,
		array( 'bionaut-litelight' ),
		file_exists( $base . $init_rel ) ? (string) filemtime( $base . $init_rel ) : BIO_THEME_VERSION,
		true
	);
}

add_action( 'wp_head', 'bio_tracking_head', 20 );
function bio_tracking_head() {
	if ( is_admin() ) {
		return;
	}

	$cmplz = defined( 'CMPLZ_VERSION' );
	$type  = $cmplz ? 'text/plain' : 'text/javascript';
	$cat   = $cmplz ? ' data-category="marketing"' : '';
	?>
	<meta name="facebook-domain-verification" content="w2hg8g8ac02wk8wma44q93sm5ci5rp" />
	<script type="<?php echo esc_attr( $type ); ?>"<?php echo $cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute string ?>>
	!function(f,b,e,v,n,t,s) {if(f.fbq)return;n=f.fbq=function(){n.callMethod? n.callMethod.apply(n,arguments):n.queue.push(arguments)}; if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0'; n.queue=[];t=b.createElement(e);t.async=!0; t.src=v;s=b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t,s)}(window, document,'script', 'https://connect.facebook.net/en_US/fbevents.js'); fbq('init', '636065345767132'); fbq('track', 'PageView');
	</script>
	<?php if ( ! $cmplz ) : ?>
	<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=636065345767132&ev=PageView&noscript=1" /></noscript>
	<?php endif; ?>
	<script type="<?php echo esc_attr( $type ); ?>"<?php echo $cat; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute string ?>>
	!function (w, d, t) {w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie","holdConsent","revokeConsent","grantConsent"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var r="https://analytics.tiktok.com/i18n/pixel/events.js",o=n&&n.partner;ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=r,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};n=document.createElement("script") ;n.type="text/javascript",n.async=!0,n.src=r+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)}; ttq.load('D5N455BC77U85R08NVH0'); ttq.page(); }(window, document, 'ttq');
	</script>
	<?php
}
