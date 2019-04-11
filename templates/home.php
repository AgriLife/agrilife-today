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
add_action( 'genesis_after_entry', 'agt_home_page' );

/**
 * Output content of template.
 *
 * @since 0.1.7
 * @return void
 */
function agt_home_page() {

	$top_story = get_field( 'top_group' );
	$items     = get_field( 'stories_group' );
	$output    = '';

	if ( $top_story ) {

		$top_post   = $top_story['story'];
		$thumb      = get_the_post_thumbnail( $top_post, 'large', 'style=max-width:100%;height:auto;' );
		$permalink  = get_permalink( $top_post );
		$cats       = wp_get_post_terms( $top_post->ID, 'category' );
		$cat_output = '';

		foreach ( $cats as $cat ) {
			$cat_output .= sprintf(
				'<a href="%s" class="button">%s</a>',
				get_term_link( $cat->term_id ),
				$cat->name
			);
		}

		$top_output = sprintf(
			'<div class="top-story"><a href="%s" aria-hidden="true" role="presentation">%s</a><div class="cats">%s</div><h4><a href="%s">%s</a></h4></div>',
			$permalink,
			$thumb,
			$cat_output,
			$permalink,
			$top_post->post_title
		);

		$output .= $top_output;

	}

	if ( $items ) {

		$item_output = '';

		foreach ( $items as $item ) {

			switch ( $item['acf_fc_layout'] ) {

				case 'post':
					$cat        = $item['category'];
					$post_query = new WP_Query(
						array(
							'cat'            => $cat->term_id,
							'posts_per_page' => 3,
						)
					);
					$posts      = $post_query->posts;

					$post_1 = sprintf(
						'<div class="post-1"><a href="%s" aria-hidden="true" role="presentation">%s</a><div class="cats"><a class="button" href="%s" aria-hidden="true" role="presentation">%s</a></div><h5><a href="%s">%s</a></h5></div>',
						get_permalink( $posts[0] ),
						get_the_post_thumbnail( $posts[0], 'medium_large' ),
						get_term_link( $cat->term_id ),
						$cat->name,
						get_permalink( $posts[0] ),
						$posts[0]->post_title
					);

					$post_2 = sprintf(
						'<div class="post-2"><hr /><a href="%s">%s<h5>%s</h5></a><hr /></div>',
						get_permalink( $posts[1] ),
						get_the_post_thumbnail( $posts[1], 'thumbnail' ),
						$posts[1]->post_title
					);

					$post_3 = sprintf(
						'<div class="post-3"><a href="%s">%s<h5>%s</h5></a><hr /></div>',
						get_permalink( $posts[2] ),
						get_the_post_thumbnail( $posts[2], 'thumbnail' ),
						$posts[2]->post_title
					);

					$item_output .= sprintf(
						'<div class="item post-cat"><h4>%s</h4>%s<div class="show-for-small-only">%s%s<div class="text-center"><a href="%s" class="button hollow">All %s</a></div></div></div>',
						$cat->name,
						$post_1,
						$post_2,
						$post_3,
						get_term_link( $cat->term_id ),
						$cat->name
					);

					break;

				case 'podcast':
					$thumb = $item['image'] ? $item['image']['url'] : AGTODAY_THEME_DIRURL . '/images/podcast.jpg';

					$item_output .= sprintf(
						'<div class="item podcast"><a href="%s"><img src="%s/images/podcast-title.png"><img src="%s"></a></div>',
						$item['page'],
						AGTODAY_THEME_DIRURL,
						$thumb
					);

					break;

				case 'quote':
					$cat = $item['category'];

					$item_output .= sprintf(
						'<div class="item quote hide-for-small"><a href="%s" class="button">%s</a>%s</div>',
						get_term_link( $cat->term_id ),
						$cat->name,
						$item['quote']
					);

					break;

				default:
					break;
			}
		}

		$output .= $item_output;

	}

	echo wp_kses_post( $output );
}

genesis();
