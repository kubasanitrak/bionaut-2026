<?php
/**
 * People sections from the current page repeater.
 *
 * @package bionaut
 */

if ( empty( $post_id ) || ! is_numeric( $post_id ) ) {
	if ( ! empty( $block['context']['postId'] ) ) {
		$post_id = (int) $block['context']['postId'];
	} else {
		$post_id = (int) get_the_ID();
	}
} else {
	$post_id = (int) $post_id;
}

if ( $post_id < 1 || ! function_exists( 'have_rows' ) || ! have_rows( 'personel_sections_container', $post_id ) ) {
	return;
}
?>
<div class="customcols-container">
	<?php
	while ( have_rows( 'personel_sections_container', $post_id ) ) :
		the_row();
		$section_title = get_sub_field( 'personel_section_title' );
		$rows          = get_sub_field( 'personel_section_row' );
		$cols_count    = is_array( $rows ) ? count( $rows ) : 0;
		$cls           = $cols_count ? ' customcols-row-colscount_' . $cols_count : '';
		if ( ! have_rows( 'personel_section_row' ) ) {
			continue;
		}
		?>
		<div class="customcols-row<?php echo esc_attr( $cls ); ?>" data-colscount="<?php echo esc_attr( (string) $cols_count ); ?>">
			<div class="customcols-row--header">
				<h2 class="customcols-row--title"><?php echo esc_html( $section_title ); ?></h2>
			</div>
			<div class="customcols-row--content">
				<?php
				while ( have_rows( 'personel_section_row' ) ) :
					the_row();
					$person = get_sub_field( 'person_obj' );
					if ( ! $person ) {
						continue;
					}
					$person_id = is_object( $person ) ? $person->ID : (int) $person;
					$img       = get_field( 'person_img', $person_id );
					$email     = get_field( 'person_mail_to', $person_id );
					$email2    = get_field( 'person_mail_to_secondary', $person_id );
					?>
					<div class="customcols-col--item">
						<div class="customcols-col--item_header">
							<h3 class="customcols-col--item_title"><?php echo esc_html( get_the_title( $person_id ) ); ?></h3>
							<p class="customcols-col--item_position minor"><?php echo esc_html( get_sub_field( 'person_position' ) ); ?></p>
							<?php if ( $email ) : ?>
								<p class="customcols-col--item_email minor">
									<a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( str_replace( '@', '(at)', $email ) ); ?></a>
								</p>
							<?php endif; ?>
							<?php if ( $email2 ) : ?>
								<p class="customcols-col--item_email minor">
									<a href="mailto:<?php echo esc_attr( $email2 ); ?>"><?php echo esc_html( str_replace( '@', '(at)', $email2 ) ); ?></a>
								</p>
							<?php endif; ?>
						</div>
						<?php if ( $img && ! empty( $img['ID'] ) ) : ?>
							<div class="customcols-col--item_img">
								<?php
								echo wp_get_attachment_image(
									(int) $img['ID'],
									'full',
									false,
									array(
										'alt'      => get_the_title( $person_id ),
										'loading'  => 'lazy',
										'decoding' => 'async',
									)
								);
								?>
							</div>
						<?php elseif ( $img && ! empty( $img['url'] ) ) : ?>
							<div class="customcols-col--item_img">
								<img src="<?php echo esc_url( $img['url'] ); ?>" alt="<?php echo esc_attr( get_the_title( $person_id ) ); ?>" loading="lazy" decoding="async" />
							</div>
						<?php endif; ?>
					</div>
				<?php endwhile; ?>
			</div>
		</div>
	<?php endwhile; ?>
</div>
