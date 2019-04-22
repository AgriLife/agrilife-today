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
add_action( 'wp_enqueue_scripts', 'agt_enqueue_home_scripts' );

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
			'<div class="grid-x"><div class="top-story card cell medium-12 small-12"><h2 class="card-heading show-for-small-only">Featured Stories</h2><p><a href="%s" aria-hidden="true" role="presentation">%s</a></p><div class="cats">%s</div><h3 class="small no-margin"><a href="%s">%s<span class="show-for-small-only"> &mdash;&nbsp;%s</span></a></h3></div></div>',
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

		$item_output = '<div class="grid-x grid-masonry">';
		$post_cat_eo = 'even';

		foreach ( $items as $key => $item ) {

			$eo = $key % 2 > 0 ? 'right' : 'left';

			switch ( $item['acf_fc_layout'] ) {

				case 'post':
					$cat = $item['category'];

					if ( $cat ) {

						$post_cat_eo  = 'even' === $post_cat_eo ? 'odd' : 'even';
						$post_query   = new WP_Query(
							array(
								'cat'            => $cat->term_id,
								'posts_per_page' => 3,
							)
						);
						$posts        = $post_query->posts;
						$post_1_atts  = array(
							'even' => array(
								'thumb' => 'medium-6-collapse-half medium-collapse-left',
								'meta'  => 'medium-6-collapse-half medium-collapse-right',
							),
							'odd'  => array(
								'thumb' => 'medium-6-collapse-half medium-collapse-right medium-order-2',
								'meta'  => 'medium-6-collapse-half medium-collapse-left medium-order-1',
							),
						);
						$post_23_atts = array(
							'wrap'       => '<span class="cell small-4-collapse-half small-collapse-left">%s</span>',
							'title_cols' => 'cell small-8-collapse-half small-collapse-right',
						);

						// Post 1.
						$post_1 = sprintf(
							'<div class="post-1 grid-x"><p class="cell small-12-collapse %s"><a href="%s" aria-hidden="true" role="presentation">%s%s</a></p><div class="cell small-12-collapse %s"><div class="date button hollow hide-for-small-only">%s</div><div class="hide-for-medium"><a class="button" href="%s" aria-hidden="true" role="presentation">%s</a></div><h3 class="small no-margin"><a href="%s">%s<span class="show-for-small-only"> &mdash;&nbsp;%s</span></a></h3></div></div>',
							$post_1_atts[ $post_cat_eo ]['thumb'],
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
							$post_1_atts[ $post_cat_eo ]['meta'],
							get_the_date( 'F j', $posts[0] ),
							get_term_link( $cat->term_id ),
							$cat->name,
							get_permalink( $posts[0] ),
							$posts[0]->post_title,
							str_replace( ' ', '&nbsp;', get_the_date( 'F j, Y', $posts[0] ) )
						);

						// Post 2.
						$thumbnail   = get_the_post_thumbnail( $posts[1], 'medium' );
						$title_class = 'cell small-12-collapse';
						if ( ! empty( $thumbnail ) ) {
							$thumbnail   = sprintf( $post_23_atts['wrap'], $thumbnail );
							$title_class = $post_23_atts['title_cols'];
						}
						$post_2 = sprintf(
							'<div class="post-2"><hr /><a class="grid-x" href="%s">%s<span class="%s"><h3 class="small no-margin">%s<span class="show-for-small-only"> &mdash;&nbsp;%s</span></h3></span></a><hr /></div>',
							get_permalink( $posts[1] ),
							$thumbnail,
							$title_class,
							$posts[1]->post_title,
							str_replace( ' ', '&nbsp;', get_the_date( 'F j, Y', $posts[1] ) )
						);

						// Post 3.
						$thumbnail   = get_the_post_thumbnail( $posts[2], 'medium' );
						$title_class = 'cell small-12-collapse';
						if ( ! empty( $thumbnail ) ) {
							$thumbnail   = sprintf( $post_23_atts['wrap'], $thumbnail );
							$title_class = $post_23_atts['title_cols'];
						}

						$post_3 = sprintf(
							'<div class="post-3"><a class="grid-x" href="%s">%s<span class="%s"><h3 class="small no-margin">%s<span class="show-for-small-only"> &mdash;&nbsp;%s</span></h3></span></a><hr /></div>',
							get_permalink( $posts[2] ),
							$thumbnail,
							$title_class,
							$posts[2]->post_title,
							str_replace( ' ', '&nbsp;', get_the_date( 'F j, Y', $posts[2] ) )
						);

						// Item output.
						$item_output .= sprintf(
							'<div class="item post-cat card cell masonry-item medium-6 small-12"><h2 class="card-heading">%s</h2>%s<div class="show-for-small-only">%s%s<div class="text-center"><a href="%s" class="button hollow no-margin">All %s</a></div></div></div>',
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
						'<div class="item podcast card card-no-padding cell masonry-item medium-6 small-12">%s<img src="%s/images/podcast-title.png"><img class="hide-for-small-only" src="%s">%s</div>',
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
						'<div class="item quote card cell masonry-item medium-6 hide-for-small-only">%s%s</div>',
						$cat_button,
						$item['quote']
					);

					break;

				default:
					break;
			}
		}

		$item_output .= '</div>';
		$output      .= $item_output;

	}

	echo wp_kses_post( $output );
}

/**
 * Add script for home page functionality
 *
 * @since 0.2.0
 * @return void
 */
function agt_enqueue_home_scripts() {

	wp_register_script(
		'agt-masonry',
		AGTODAY_THEME_DIRURL . '/js/public.masonry.min.js',
		array( 'jquery', 'masonry' ),
		filemtime( AGTODAY_THEME_DIRPATH . '/js/public.masonry.min.js' ),
		true
	);

	wp_enqueue_script( 'agt-masonry' );

}

genesis();
