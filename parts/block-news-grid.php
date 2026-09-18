<?php
/**
 * Newsitem grid (News page + block).
 *
 * @package bionaut
 */

$news_query = bio_news_query();
if ( ! $news_query->have_posts() ) {
	return;
}
?>
<div class="news-grid" data-bio-news data-page="1" data-has-more="<?php echo $news_query->max_num_pages > 1 ? '1' : '0'; ?>">
	<?php
	while ( $news_query->have_posts() ) :
		$news_query->the_post();
		get_template_part( 'parts/news', 'card' );
	endwhile;
	wp_reset_postdata();
	?>
</div>
<?php if ( $news_query->max_num_pages > 1 ) : ?>
	<p class="news-grid-more">
		<button type="button" class="news-grid-more__btn" data-bio-news-more>
			<?php echo esc_html( 'en' === bio_current_language() ? 'More news' : 'Další aktuality' ); ?>
		</button>
	</p>
<?php endif; ?>
