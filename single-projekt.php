<?php
/**
 * Single projekt.
 *
 * @package bionaut
 */

get_header();

while ( have_posts() ) :
	the_post();

	$orig_id          = bio_original_id();
	$bg               = wp_get_attachment_image_src( get_post_thumbnail_id( $orig_id ), 'bio-background-img' );
	$prizes_url       = bio_project_field( 'prizes' );
	if ( is_array( $prizes_url ) ) {
		$prizes_url = $prizes_url['url'] ?? '';
	}
	$complex_playlist = bio_project_field( 'complex_playlist' );
	$horizontal_icons = bio_project_field( 'horizontal_icons' );
	$website          = bio_project_field( 'website' );
	$eshop            = bio_project_field( 'eshop' );
	$icons            = bio_project_icons();
	$prize_class      = ( $prizes_url || $icons ) ? 'has-prizes' : '';
	$trailers         = bio_project_field( 'trailers' );
	?>

	<div id="primary" class="content-area">
		<div class="container">
			<div id="content" class="site-content" role="main">
				<article
					id="post-<?php the_ID(); ?>"
					<?php post_class( $prize_class ); ?>
					<?php if ( ! empty( $bg[0] ) ) : ?>
						data-bio-bg-src="<?php echo esc_url( $bg[0] ); ?>"
					<?php endif; ?>
				>
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
					</header>

					<div class="clear">
						<?php if ( trim( get_the_content() ) ) : ?>
							<section class="section entry-content">
								<?php the_content(); ?>

								<?php
								if ( bio_show_cinema_listings() ) {
									echo bio_performance_list_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>

								<?php if ( $icons && bio_is_on( $horizontal_icons ) ) : ?>
									<div class="project__icons project__icons--horizontal">
										<?php foreach ( $icons as $icon ) : ?>
											<div class="project__icon">
												<img class="project__icon-img" src="<?php echo esc_url( $icon ); ?>" alt="" loading="lazy" decoding="async"/>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>

								<?php
								$url = bio_press_url( bio_project_field( 'press_files' ) );
								if ( $url ) {
									echo '<div class="presskit"><a href="' . esc_url( $url ) . '" class="presskit-link" target="_blank">' . esc_html( bio_string( 'Stáhnout presskit', 'Download presskit' ) ) . '</a></div>';
								}
								?>

								<?php if ( $website || $eshop ) : ?>
									<div class="project__links">
										<?php if ( $website ) : ?>
											<a href="<?php echo esc_url( $website ); ?>" target="_blank"><?php esc_html_e( 'Show website', 'bionaut' ); ?></a>
										<?php endif; ?>
										<?php if ( $eshop ) : ?>
											<a href="<?php echo esc_url( $eshop ); ?>" target="_blank"><?php esc_html_e( 'Go to store', 'bionaut' ); ?></a>
										<?php endif; ?>
									</div>
								<?php endif; ?>
							</section>
						<?php endif; ?>

						<?php if ( $prizes_url && is_string( $prizes_url ) ) : ?>
							<img class="prizes" src="<?php echo esc_url( $prizes_url ); ?>" alt="<?php esc_attr_e( 'Vavříny', 'bionaut' ); ?>" loading="lazy" decoding="async"/>
						<?php endif; ?>

						<?php if ( $icons && ! bio_is_on( $horizontal_icons ) ) : ?>
							<div class="project__icons project__icons--vertical">
								<?php foreach ( $icons as $icon ) : ?>
									<div class="project__icon">
										<img class="project__icon-img" width="200" src="<?php echo esc_url( $icon ); ?>" alt="" loading="lazy" decoding="async"/>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>

					<?php
					if ( is_array( $trailers ) && ! empty( $trailers[0] ) ) :
						$trailer_ids = array();
						foreach ( $trailers as $row ) {
							if ( is_array( $row ) && isset( $row['trailer_id'] ) ) {
								$trailer_ids[] = $row['trailer_id'];
							} elseif ( is_string( $row ) || is_numeric( $row ) ) {
								$trailer_ids[] = $row;
							}
						}
						$trailer_ids = array_filter( $trailer_ids );
						if ( $trailer_ids ) :
							?>
							<section class="section project-trailers">
								<h2 class="section-title"><?php echo esc_html( _n( 'Trailer', 'Trailery', count( $trailer_ids ), 'bionaut' ) ); ?></h2>
								<?php foreach ( $trailer_ids as $trailer_id ) : ?>
									<article class="trailer">
										<iframe
											width="1200"
											height="675"
											src="<?php echo esc_url( bio_trailer_embed_url( $trailer_id ) ); ?>"
											title="<?php esc_attr_e( 'Trailer', 'bionaut' ); ?>"
											loading="lazy"
											allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
											allowfullscreen
										></iframe>
									</article>
								<?php endforeach; ?>
							</section>
							<?php
						endif;
					endif;
					?>

					<?php
					if ( ! bio_hide_attached_gallery() ) :
						$images = get_children(
							array(
								'post_parent'    => $orig_id,
								'post_type'      => 'attachment',
								'post_mime_type' => 'image',
								'orderby'        => 'menu_order',
								'order'          => 'ASC',
								'posts_per_page' => -1,
								'post__not_in'   => array( get_post_thumbnail_id() ),
							)
						);
						if ( $images ) :
							?>
							<section class="section stills">
								<div class="gallery clear">
									<?php
									foreach ( $images as $image ) {
										$image_img_tag = wp_get_attachment_image( $image->ID, 'bio-thumb-small' );
										$image_big_url = wp_get_attachment_image_src( $image->ID, 'large' );
										if ( empty( $image_big_url[0] ) ) {
											continue;
										}
										if ( $prizes_url && $image_big_url[0] === $prizes_url ) {
											continue;
										}
										if ( in_array( $image_big_url[0], $icons, true ) ) {
											continue;
										}
										?>
										<div class="gallery-thumb">
											<a href="<?php echo esc_url( $image_big_url[0] ); ?>" data-lightbox="<?php echo esc_url( $image_big_url[0] ); ?>" rel="gallery">
												<?php echo $image_img_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											</a>
										</div>
										<?php
									}
									$dropbox = bio_project_field( 'press_dropbox' );
									if ( $dropbox ) {
										echo '<div class="presskit"><a href="' . esc_url( $dropbox ) . '" class="presskit-link" target="_blank">' . esc_html__( 'Press', 'bionaut' ) . '</a></div>';
									}
									?>
								</div>
							</section>
							<?php
						endif;
					endif;
					?>

					<?php
					$playlist = bio_project_field( 'playlist' );
					if ( is_array( $playlist ) && bio_row_value( $playlist[0] ?? array(), 'video_link' ) ) :
						?>
						<section class="playlist clear">
							<?php foreach ( $playlist as $item ) : ?>
								<a href="<?php echo esc_url( bio_row_value( $item, 'video_link' ) ); ?>" title="<?php echo esc_attr( bio_row_value( $item, 'video_title' ) ); ?>" class="playlist-item">
									<?php echo wp_get_attachment_image( (int) bio_row_value( $item, 'video_image' ), 'bio-thumb' ); ?>
									<span class="playlist-item-title"><?php echo esc_html( bio_texturize( bio_row_value( $item, 'video_title' ) ) ); ?></span>
								</a>
							<?php endforeach; ?>
						</section>
					<?php endif; ?>

					<?php if ( is_array( $complex_playlist ) && bio_row_value( $complex_playlist[0] ?? array(), 'video_description' ) ) : ?>
						<section class="complex-playlist clearfix">
							<?php foreach ( $complex_playlist as $item ) : ?>
								<article class="playlist-block clear">
									<div class="playlist-item-description">
										<?php if ( bio_row_value( $item, 'video_title' ) ) : ?>
											<h2><?php echo esc_html( bio_texturize( bio_row_value( $item, 'video_title' ) ) ); ?></h2>
										<?php endif; ?>
										<?php echo wp_kses_post( bio_texturize( bio_row_value( $item, 'video_description' ) ) ); ?>
									</div>
									<div class="playlist-item-media">
										<?php if ( bio_row_value( $item, 'video_link' ) ) : ?>
											<a href="<?php echo esc_url( bio_row_value( $item, 'video_link' ) ); ?>">
										<?php endif; ?>
										<?php echo wp_get_attachment_image( (int) bio_row_value( $item, 'video_image' ), 'bio-thumb-medium' ); ?>
										<?php if ( bio_row_value( $item, 'video_release_date' ) ) : ?>
											<strong class="playlist-item-release-date"><?php echo esc_html( bio_row_value( $item, 'video_release_date' ) ); ?></strong>
										<?php endif; ?>
										<?php if ( bio_row_value( $item, 'video_link' ) ) : ?>
											</a>
										<?php endif; ?>
									</div>
								</article>
							<?php endforeach; ?>
						</section>
					<?php endif; ?>

					<div class="credits-container">
						<?php
						$credits = bio_project_field( 'credits_html' );
						$cast    = bio_project_field( 'cast_html' );
						if ( $credits ) {
							echo '<section class="section project-credits"><h2 class="section-title">Credits</h2>' . wp_kses_post( $credits ) . '</section>';
						}
						if ( $cast ) {
							echo '<section class="section project-cast"><h2 class="section-title">Cast</h2>' . wp_kses_post( $cast ) . '</section>';
						}
						?>
					</div>
				</article>
			</div>
		</div>
	</div>
	<?php
endwhile;

get_footer();
