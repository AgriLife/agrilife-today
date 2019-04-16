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
add_action( 'genesis_entry_content', 'agt_home_page' );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

/**
 * Output content of template.
 *
 * @since 0.1.7
 * @return void
 */
function agt_home_page() {

	$top_story = get_field( 'top_group' );
	$items     = get_field( 'stories_group' );
	$output    = '<div class="row">';

	if ( $top_story['story'] ) {

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
			'<div class="top-story card cell medium-12-collapse small-12-collapse"><h2 class="card-heading show-for-small-only">Featured Stories</h2><p><a href="%s" aria-hidden="true" role="presentation">%s</a></p><div class="cats">%s</div><h3 class="small no-margin"><a href="%s">%s<span class="show-for-small-only"> &mdash;&nbsp;%s</span></a></h3></div>',
			$permalink,
			$thumb,
			$cat_output,
			$permalink,
			$top_post->post_title,
			str_replace( ' ', '&nbsp;', get_the_date( 'F j, Y', $top_post ) )
		);

		$output .= $top_output;

	}

	if ( $items ) {

		$item_output = '';

		foreach ( $items as $key => $item ) {

			$eo = $key % 2 > 0 ? 'right' : 'left';

			switch ( $item['acf_fc_layout'] ) {

				case 'post':
					$cat = $item['category'];

					if ( $cat ) {

						$post_query = new WP_Query(
							array(
								'cat'            => $cat->term_id,
								'posts_per_page' => 3,
							)
						);
						$posts      = $post_query->posts;

						// Post 1.
						$post_1 = sprintf(
							'<div class="post-1 row"><p class="cell small-12-collapse medium-6-collapse medium-order-2"><a href="%s" aria-hidden="true" role="presentation">%s%s</a></p><div class="cell small-12-collapse medium-6-collapse-left medium-order-1"><div class="date button hollow hide-for-small-only">%s</div><p class="cats hide-for-medium"><a class="button" href="%s" aria-hidden="true" role="presentation">%s</a></p><h3 class="small no-margin"><a href="%s">%s<span class="show-for-small-only"> &mdash;&nbsp;%s</span></a></h3></div></div>',
							get_permalink( $posts[0] ),
							get_the_post_thumbnail(
								$posts[0],
								'medium_large',
								array(
									'class' => 'hide-for-medium',
								)
							),
							get_the_post_thumbnail(
								$posts[0],
								'medium',
								array(
									'class' => 'hide-for-small-only',
								)
							),
							get_the_date( 'F j', $posts[0] ),
							get_term_link( $cat->term_id ),
							$cat->name,
							get_permalink( $posts[0] ),
							$posts[0]->post_title,
							str_replace( ' ', '&nbsp;', get_the_date( 'F j, Y', $posts[0] ) )
						);

						// Post 2.
						$thumbnail  = get_the_post_thumbnail( $posts[1], 'medium' );
						$title_cols = '12-collapse';
						if ( ! empty( $thumbnail ) ) {
							$thumbnail  = '<span class="cell small-4-collapse">' . $thumbnail . '</span>';
							$title_cols = '8';
						}
						$post_2 = sprintf(
							'<div class="post-2"><hr /><a class="row" href="%s">%s<span class="cell small-%s"><h3 class="small">%s<span class="show-for-small-only"> &mdash;&nbsp;%s</span></h3></span></a><hr /></div>',
							get_permalink( $posts[1] ),
							$thumbnail,
							$title_cols,
							$posts[1]->post_title,
							str_replace( ' ', '&nbsp;', get_the_date( 'F j, Y', $posts[1] ) )
						);

						// Post 3.
						$thumbnail  = get_the_post_thumbnail( $posts[2], 'medium' );
						$title_cols = '12-collapse';
						if ( ! empty( $thumbnail ) ) {
							$thumbnail  = '<span class="cell small-4-collapse">' . $thumbnail . '</span>';
							$title_cols = '8';
						}

						$post_3 = sprintf(
							'<div class="post-3"><a class="row" href="%s">%s<span class="cell small-%s"><h3 class="small">%s<span class="show-for-small-only"> &mdash;&nbsp;%s</span></h3></span></a><hr /></div>',
							get_permalink( $posts[2] ),
							$thumbnail,
							$title_cols,
							$posts[2]->post_title,
							str_replace( ' ', '&nbsp;', get_the_date( 'F j, Y', $posts[2] ) )
						);

						// Item output.
						$item_output .= sprintf(
							'<div class="item post-cat card cell medium-6-collapse-%s-half small-12-collapse"><h2 class="card-heading">%s</h2>%s<div class="show-for-small-only">%s%s<div class="text-center"><a href="%s" class="button hollow">All %s</a></div></div></div>',
							$eo,
							$cat->name,
							$post_1,
							$post_2,
							$post_3,
							get_term_link( $cat->term_id ),
							$cat->name
						);

					}

					break;

				case 'podcast':
					$thumb        = $item['image'] ? $item['image']['url'] : AGTODAY_THEME_DIRURL . '/images/podcast.jpg';
					$link_open    = $item['page'] ? "<a href=\"{$item['page']}\">" : '';
					$link_close   = $item['page'] ? '</a>' : '';
					$item_output .= sprintf(
						'<div class="item podcast card card-no-padding cell medium-6-collapse-%s-half small-12-collapse">%s<img src="%s/images/podcast-title.png"><img class="hide-for-small-only" src="%s">%s</div>',
						$eo,
						$link_open,
						AGTODAY_THEME_DIRURL,
						$thumb,
						$link_close
					);

					break;

				case 'quote':
					$cat        = $item['category'];
					$cat_button = '';
					if ( $item['category'] ) {
						$cat_button = sprintf(
							'<a href="%s" class="button">%s</a>',
							get_term_link( $cat->term_id ),
							$cat->name
						);
					}

					$item_output .= sprintf(
						'<div class="item quote card cell medium-6-collapse-%s-half hide-for-small-only">%s%s</div>',
						$eo,
						$cat_button,
						$item['quote']
					);

					break;

				default:
					break;
			}
		}

		$output .= $item_output;

	}

	$output .= '</div>';

	echo wp_kses_post( $output );
}

genesis();
