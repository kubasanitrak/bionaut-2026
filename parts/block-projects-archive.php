<?php
/**
 * Projects grouped by kategorie (archive + block).
 *
 * @package bionaut
 */

$term_args = array(
	'taxonomy'   => 'kategorie',
	'order'      => 'ASC',
	'hide_empty' => true,
);
if ( function_exists( 'pll_current_language' ) ) {
	$lang = pll_current_language();
	if ( $lang ) {
		$term_args['lang'] = $lang;
	}
}
$categories = get_terms( $term_args );

if ( is_wp_error( $categories ) || ! $categories ) {
	return;
}

foreach ( $categories as $category ) :
	$projects_query = new WP_Query(
		bio_lang_query_args(
			array(
				'post_type'      => 'projekt',
				'posts_per_page' => BIO_ARCHIVE_VISIBLE,
				'orderby'        => 'menu_order',
				'order'          => 'ASC',
				'tax_query'      => array(
					array(
						'taxonomy' => 'kategorie',
						'field'    => 'term_id',
						'terms'    => $category->term_id,
					),
				),
			)
		)
	);
	if ( ! $projects_query->have_posts() ) {
		continue;
	}
	$total     = (int) $projects_query->found_posts;
	$remaining = max( 0, $total - BIO_ARCHIVE_VISIBLE );
	?>
	<section
		id="<?php echo esc_attr( $category->slug ); ?>"
		class="section category category-<?php echo esc_attr( $category->slug ); ?> clear project-overview"
		data-term="<?php echo esc_attr( (string) $category->term_id ); ?>"
		data-offset="<?php echo esc_attr( (string) BIO_ARCHIVE_VISIBLE ); ?>"
		data-remaining="<?php echo esc_attr( (string) $remaining ); ?>"
	>
		<header class="category-header project-overview--header">
			<h2 class="section-title category-title project-overview--headline"><?php echo esc_html( $category->name ); ?></h2>
			<?php if ( $category->description ) : ?>
				<p class="category-description"><?php echo esc_html( $category->description ); ?></p>
			<?php endif; ?>
		</header>
		<?php
		while ( $projects_query->have_posts() ) :
			$projects_query->the_post();
			get_template_part( 'parts/project', 'card' );
		endwhile;
		wp_reset_postdata();
		if ( $remaining ) :
			?>
			<button class="project-overview--toggle" type="button">
				<span class="counter"><?php echo esc_html( (string) $remaining ); ?></span>
				<?php esc_html_e( 'more', 'bionaut' ); ?>
			</button>
		<?php endif; ?>
	</section>
	<?php
endforeach;
