<?php
/**
 * A page template for Home Pages
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/templates/home.php
 * @since      0.1.7
 * @package    agrilife-today
 * @subpackage agrilife-today/templates
 */

/**
 * Template Name: Home Page
 */
add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar' );
add_action( 'genesis_after_entry', 'af4_service_landing_page' );

/**
 * Output content of template.
 *
 * @since 0.1.7
 * @return void
 */
function af4_service_landing_page() {

	$top_story = get_field( 'top_group' );
	$items     = get_field( 'stories_group' );
	$output    = '';

	echo wp_kses_post( $output );
}

genesis();
