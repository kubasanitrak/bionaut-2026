<?php
/**
 * Theme footer.
 *
 * @package bionaut
 */

$template_directory = get_template_directory_uri();
?>
	</div>

	<footer id="rozcestnik" class="site-footer" role="contentinfo">
		<div class="container">
			<div class="footer-wrap">
				<div class="section section-footer clear">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'fcbk-ig',
							'container'      => '',
							'menu_class'     => 'footnav-list nav-list list-none plain',
						)
					);
					?>
				</div>

				<div class="section clear">
					<h2 class="bionaut-planet taC"><?php esc_html_e( 'Planeta Bionaut', 'bionaut' ); ?></h2>
				</div>

				<div class="footer-nav clear">
					<ul class="links">
						<li class="links__item<?php echo 'https://bionaut.cz' === untrailingslashit( site_url() ) ? ' links__item--active' : ''; ?>">
							<a href="https://bionaut.cz/"><img src="<?php echo esc_url( $template_directory . '/img/logo-bionaut.png' ); ?>" alt="Bionaut" loading="lazy" decoding="async"></a>
						</li>
						<li class="links__item">
							<a href="https://raketa.watch"><img src="<?php echo esc_url( $template_directory . '/img/logo-raketa.png' ); ?>" alt="Raketa" loading="lazy" decoding="async"></a>
						</li>
						<li class="links__item">
							<a href="https://kosmonaut.watch"><img src="<?php echo esc_url( $template_directory . '/img/logo-kosmonaut.png' ); ?>" alt="Kosmonaut" loading="lazy" decoding="async"></a>
						</li>
						<li class="links__item">
							<a href="https://bionaut.works/"><img src="<?php echo esc_url( $template_directory . '/img/BionautWorks-logo.png' ); ?>" alt="Bionaut Works" loading="lazy" decoding="async"></a>
						</li>
						<li class="links__item">
							<a href="https://audionaut.cz/"><img src="https://bionaut.cz/wp-content/uploads/2021/12/logo-audionaut.png" alt="Audionaut" loading="lazy" decoding="async"></a>
						</li>
						<li class="links__item">
							<a href="https://animation.bionaut.cz/"><img src="<?php echo esc_url( $template_directory . '/img/Bionaut-Animation-logo.png' ); ?>" alt="Bionaut Animation" loading="lazy" decoding="async"></a>
						</li>
						<li class="links__item">
							<a href="https://planetdark.tv/"><img src="https://bionaut.cz/wp-content/uploads/2021/12/logo-planetdark.png" alt="Planet Dark" loading="lazy" decoding="async"></a>
						</li>
					</ul>
				</div>

				<div class="site-info">
					<div class="copy">
						<p>© <?php echo esc_html( date_i18n( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
					</div>
					<div class="site-author">
						<p><a href="<?php echo esc_url( bio_gdpr_url() ); ?>"><?php echo esc_html( bio_string( 'Zásady ochrany osobních údajů', 'Privacy policy' ) ); ?></a></p>
					</div>
				</div>
			</div>
		</div>
	</footer>
</div>

<div class="page-bg"></div>

<?php if ( is_singular() && bio_has_bg_video() ) : ?>
	<div class="video-container">
		<video class="video-bg" width="720" height="404" preload="metadata" muted>
			<?php
			$webm = bio_project_field( 'bgvideo_webm' );
			$ogv  = bio_project_field( 'bgvideo_ogv' );
			$mp4  = bio_project_field( 'bgvideo_mp4' );
			if ( $webm ) {
				echo '<source src="' . esc_url( $webm ) . '" type="video/webm">';
			}
			if ( $ogv ) {
				echo '<source src="' . esc_url( $ogv ) . '" type="video/ogv">';
			}
			if ( $mp4 ) {
				echo '<source src="' . esc_url( $mp4 ) . '" type="video/mp4">';
			}
			?>
		</video>
	</div>
<?php endif; ?>

<?php if ( defined( 'CMPLZ_VERSION' ) ) : ?>
	<button type="button" id="manageconsent" class="bio-manage-consent" aria-label="<?php echo esc_attr( bio_string( 'Spravovat souhlas s cookies', 'Manage cookie consent' ) ); ?>">
		<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="currentColor" aria-hidden="true" focusable="false">
			<path d="M25.787 52.216a7.057 7.057 0 1 0 0-14.113 7.057 7.057 0 0 0 0 14.113M45.65 30.546a4.31 4.31 0 0 0-4.313-4.312 4.31 4.31 0 0 0-4.312 4.312 4.31 4.31 0 0 0 4.312 4.312 4.31 4.31 0 0 0 4.313-4.312M48.198 46.717a4.717 4.717 0 0 0-4.715 4.715 4.717 4.717 0 0 0 4.715 4.715 4.717 4.717 0 0 0 4.715-4.715 4.717 4.717 0 0 0-4.715-4.715M30.557 74.769a5.401 5.401 0 1 0 0-10.803 5.401 5.401 0 0 0 0 10.803M67.275 77.923a6.947 6.947 0 1 0-9.825-9.825 6.947 6.947 0 0 0 9.825 9.825M77.12 28.249a4.813 4.813 0 1 0-1.541-9.502 4.813 4.813 0 0 0 1.542 9.502M96.33 32.702a3.235 3.235 0 1 0 0-6.47 3.235 3.235 0 0 0 0 6.47M84.17 7.034a4.933 4.933 0 1 0-9.115-3.776 4.933 4.933 0 0 0 9.115 3.776"/>
			<path d="M99.858 46.38a2.73 2.73 0 0 0-1.295-2.124 2.71 2.71 0 0 0-2.483-.185 12.52 12.52 0 0 1-14.244-3.3 2.72 2.72 0 0 0-2.995-.751 14.7 14.7 0 0 1-5.129.936c-8.167 0-14.81-6.642-14.81-14.799 0-2.287.523-4.508 1.557-6.588a2.715 2.715 0 0 0-1.11-3.594A10.19 10.19 0 0 1 54.1 7.08c0-1.133.196-2.276.588-3.376a2.71 2.71 0 0 0-.316-2.429A2.72 2.72 0 0 0 52.238.076a51 51 0 0 0-2.243-.054C22.433 0 0 22.433 0 49.995s22.433 49.994 49.995 49.994 49.994-22.432 49.994-49.994c0-1.09-.043-2.243-.142-3.626zM49.995 94.554c-24.568 0-44.55-19.982-44.55-44.55 0-24.566 19.307-43.874 43.297-44.538a16 16 0 0 0-.087 1.6c0 4.836 2.243 9.355 5.979 12.284a20 20 0 0 0-1.176 6.795c0 11.162 9.081 20.244 20.254 20.244 1.786 0 3.561-.24 5.304-.718a17.95 17.95 0 0 0 12.218 4.813 18 18 0 0 0 3.321-.305c-.098 24.49-20.048 44.375-44.55 44.375z"/>
		</svg>
	</button>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
