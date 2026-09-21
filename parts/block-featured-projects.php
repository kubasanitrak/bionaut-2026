<?php
/**
 * Featured projects mosaic (homepage ACF block).
 *
 * @package bionaut
 */

$is_preview = ! empty( $is_preview );
$rows       = bio_mosaic_rows();

if ( ! $rows && ! $is_preview ) {
	return;
}

$tile_index = 0;

/**
 * Render one mosaic tile and advance the eager-load counter.
 *
 * @param int    $post_id  Projekt ID.
 * @param string $width    single|double.
 * @param string $position left|middle|right or empty.
 * @param int    $index    Tile index, passed by reference.
 */
$bio_render_tile = static function ( $post_id, $width, $position, &$index ) {
	$post_id = (int) $post_id;
	if ( $post_id < 1 ) {
		return;
	}
	get_template_part(
		'parts/project',
		'tile',
		array(
			'post_id'  => $post_id,
			'width'    => $width,
			'position' => $position,
			'loading'  => $index < 3 ? 'eager' : 'lazy',
		)
	);
	++$index;
};
$preview_capped = false;
if ( $is_preview && count( $rows ) > 2 ) {
	$rows           = array_slice( $rows, 0, 2 );
	$preview_capped = true;
}
?>
<section class="section projects hp-mosaic<?php echo $is_preview ? ' hp-mosaic--editor' : ''; ?>">
	<?php if ( $is_preview ) : ?>
		<div class="hp-mosaic__editor-shield" aria-hidden="true"></div>
	<?php endif; ?>
	<h1 class="visuallyhidden"><?php echo esc_html( bio_string( 'Projekty', 'Projects' ) ); ?></h1>
	<?php if ( ! $rows ) : ?>
		<p class="hp-mosaic__placeholder"><?php echo esc_html( bio_string( 'Přidejte řádky mozaiky v bočním panelu bloku.', 'Add mosaic rows in the block sidebar.' ) ); ?></p>
	<?php endif; ?>
	<?php foreach ( $rows as $row ) : ?>
		<?php
		$type = $row['type'] ?? '';
		if ( 'double_stack' === $type ) :
			$position  = ( isset( $row['position'] ) && 'right' === $row['position'] ) ? 'right' : 'left';
			$row_class = 'hp-mosaic__row hp-mosaic__row--double hp-mosaic__row--double-' . $position;
			$double_id = isset( $row['double'] ) ? (int) $row['double'] : 0;
			$stack_ids = isset( $row['stack'] ) && is_array( $row['stack'] ) ? $row['stack'] : array();
			?>
			<div class="<?php echo esc_attr( $row_class ); ?>">
				<?php
				$render_stack = static function () use ( $stack_ids, $bio_render_tile, &$tile_index ) {
					if ( ! $stack_ids ) {
						return;
					}
					echo '<div class="hp-mosaic__stack">';
					foreach ( $stack_ids as $stack_id ) {
						$bio_render_tile( (int) $stack_id, 'single', '', $tile_index );
					}
					echo '</div>';
				};

				if ( 'right' === $position ) {
					$render_stack();
					$bio_render_tile( $double_id, 'double', $position, $tile_index );
				} else {
					$bio_render_tile( $double_id, 'double', $position, $tile_index );
					$render_stack();
				}
				?>
			</div>
		<?php else : ?>
			<div class="hp-mosaic__row hp-mosaic__row--singles">
				<?php
				foreach ( $row['tiles'] as $tile ) {
					$bio_render_tile(
						(int) $tile['id'],
						'single',
						isset( $tile['position'] ) ? (string) $tile['position'] : '',
						$tile_index
					);
				}
				?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php if ( $preview_capped ) : ?>
		<p class="hp-mosaic__placeholder"><?php echo esc_html( bio_string( 'Náhled zkrácen — na webu se zobrazí všechny řádky.', 'Preview truncated — the front end shows every row.' ) ); ?></p>
	<?php endif; ?>
</section>
