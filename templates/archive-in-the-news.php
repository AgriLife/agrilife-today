<?php
/**
 * The file that renders archive In The News posts
 *
 * A custom post template for archive In The News post views
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/templates/archive-in-the-news.php
 * @since      1.4.0
 * @package    agrilife-today
 * @subpackage agrilife-today/templates
 */

add_filter( 'genesis_get_image', 'news_source_thumbnail', 11, 2 );

/**
 * Initialize the various classes
 *
 * @since 1.4.0
 * @param string $output The current post image output.
 * @param array  $args Image query arguments. Keys: post_id, format, size, num, attr, fallback, context.
 * @return string
 */
function news_source_thumbnail( $output, $args ) {

	if ( 'in-the-news' === get_post_type() && 'archive' === $args['context'] ) {

		$news_source_term = get_the_terms( get_the_ID(), 'news-source' );

		if ( is_array( $news_source_term ) ) {

			$img = wp_get_attachment_image( get_field( 'image', $news_source_term[0] ), 'medium' );

			$output = wp_kses_post( $img );

		}
	}

	return $output;

}

get_header();
genesis();
