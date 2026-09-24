<?php
/**
 * Theme header.
 *
 * @package bionaut
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<?php
$body_class = '';
if ( is_singular() && bio_has_bg_video() ) {
	$body_class .= 'has-video ';
}
?>
<body <?php body_class( $body_class ); ?> data-bio-lang="<?php echo esc_attr( bio_current_language() ); ?>">
<script>document.body.classList.add('js');</script>
<div id="page" class="hfeed site clear">
	<header id="masthead" class="site-header" role="banner">
		<!-- <div class="site-header__inner container"> -->
		<div class="site-header__inner ">
			<a href="<?php echo esc_url( bio_home_url() ); ?>" class="logo logo--<?php echo esc_attr( sanitize_title( get_bloginfo( 'name', 'display' ) ) ); ?>" rel="home">
				<?php
				$logo = function_exists( 'get_field' ) ? get_field( 'bio_logo', 'option' ) : null;
				if ( $logo && ! empty( $logo['url'] ) ) :
					?>
					<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" fetchpriority="high" decoding="async"/>
				<?php else : ?>
					<img src="<?php echo esc_url( get_template_directory_uri() . '/img/logo-bionaut-films-2.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" width="158" height="53" fetchpriority="high" decoding="async" />
				<?php endif; ?>
			</a>

			<input type="checkbox" id="menuBtnID" class="nav-switch" aria-controls="site-header-menu">
			<label for="menuBtnID" class="hamburger">
				<span class="visuallyhidden"><?php echo esc_html( bio_string( 'Menu', 'Menu' ) ); ?></span>
				<span class="hamburger__icon" aria-hidden="true"></span>
			</label>

			<div class="menu-container" id="site-header-menu">
				<nav id="site-navigation" class="navigation-main" role="navigation">
					<div class="visuallyhidden skip-link">
						<a href="#content"><?php echo esc_html( bio_string( 'Přeskočit na obsah', 'Skip to content' ) ); ?></a>
					</div>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
						)
					);
					?>
				</nav>
				<div class="lang-switcher">
					<?php bio_language_switcher(); ?>
				</div>
			</div>
		</div>
	</header>

	<div id="main" class="site-main">
