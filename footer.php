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

<?php wp_footer(); ?>
</body>
</html>
