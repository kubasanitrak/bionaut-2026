<?php
/**
 * Cinema listings block.
 *
 * @package bionaut
 */

if ( ! bio_show_cinema_listings() ) {
	return;
}

echo bio_performance_list_markup(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
