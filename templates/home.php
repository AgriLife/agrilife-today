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
remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
add_filter(
	'excerpt_length',
	function( $length ) {
		return 20;
	}
);

/**
 * Retrieve subheading from within post content.
 * Copied from class-genesis.php.
 *
 * @since 0.8.24
 * @param string $content Post content.
 * @return string
 */
function agt_get_subheading( $content ) {

	// Get first h2 in $content, sometimes wrapped by HTML comments by Gutenberg.
	$output  = '';
	$pattern = '/^((<!--(.|\s)*?-->)?([\r\n]*)?(<\s*?(h2){1}\b[^>]*>(.*?)<\/(h2){1}\b[^>]*>)([\r\n]*)?(<!--(.|\s)*?-->)?)/';
	preg_match( $pattern, $content, $subheading );

	if ( isset( $subheading[5] ) ) {
		$output = $subheading[5];
	}

	return $output;

}

/**
 * Output content of template.
 *
 * @since 0.1.7
 * @return void
 */
function agt_home_page() {

	$story_sections = get_field( 'stories' );
	$in_the_news    = get_field( 'in_the_news' );
	$subscribe      = get_field( 'subscribe' );
	$output         = '';
	$post_atts      = array(
		'odd'  => array(
			'thumb'   => 'medium-collapse-right medium-4-collapse-half',
			'content' => array(
				'thumb'    => 'has-image medium-collapse-left medium-8-collapse-half',
				'no-thumb' => 'auto collapse no-image',
			),
		),
		'even' => array(
			'thumb'   => 'medium-collapse-left medium-4-collapse-half medium-order-1',
			'content' => array(
				'thumb'    => 'has-image medium-collapse-right medium-8-collapse-half medium-order-2',
				'no-thumb' => 'auto collapse no-image',
			),
		),
	);

	// Story Sections.
	if ( ! empty( $story_sections ) ) {

		foreach ( $story_sections as $key => $section ) {

			$section_output = '<div class="story section"><div class="heading-sideline"><div class="grid-x"><div class="cell auto title-line"></div><h2 class="cell shrink">%s</h2><div class="cell auto title-line"></div></div></div><div class="section-content"><div class="grid-x">%s</div></div></div>';
			$stories_output = array();
			$eo             = 'odd';

			foreach ( $section['stories']['stories'] as $key => $story ) {

				switch ( $story['acf_fc_layout'] ) {

					case 'post':
						$post_obj     = $story['post'];
						$id           = $post_obj->ID;
						$has_thumb    = 'thumb';
						$auto_excerpt = '';

						// Get category button group.
						$post_categories  = wp_get_post_categories( $id );
						$post_cat_button  = '<a href="%s" class="button hollow">%s</a>';
						$post_cat_buttons = array();
						$cat_buttons      = '';

						foreach ( $post_categories as $cat_id ) {

							$cat = get_category( $cat_id );

							if ( false === strpos( $cat->slug, 'uncategorized' ) ) {

								$post_cat_buttons[] = sprintf(
									$post_cat_button,
									get_category_link( $cat_id ),
									$cat->name
								);

							}
						}

						if ( 0 < count( $post_cat_buttons ) ) {

							$cat_buttons = sprintf(
								'<div class="post-category">%s</div>',
								implode( '', $post_cat_buttons )
							);

						}

						// Get featured image.
						$image      = '';
						$post_image = get_the_post_thumbnail( $post_obj, 'home-story-image' );
						if ( ! empty( $post_image ) ) {

							$image = sprintf(
								'<div class="cell image medium-4 small-12-collapse small-collapse small-order-1 %s"><a href="%s" title="%s">%s</a></div>',
								$post_atts[ $eo ]['thumb'],
								get_permalink( $post_obj->ID ),
								$post_obj->post_title,
								$post_image
							);

						} else {

							$has_thumb = 'no-thumb';

						}

						// Get excerpt or post subtitle.
						$subheading = agt_get_subheading( $post_obj->post_content );
						$subheading = preg_replace( '/<(\/)?h2>/', '', $subheading );
						$excerpt    = '';

						if ( ! empty( $subheading ) ) {

							$excerpt = $subheading;

						} elseif ( ! empty( $story['description'] ) ) {

							$excerpt = $story['description'];

						} else {

							$auto_excerpt = ' has-auto-excerpt';
							$excerpt      = wp_trim_excerpt( '', $id );
							$excerpt      = preg_replace( '/<span class="read-more"><a [^>]+>[^<]+<\/a><\/span>/', '', $excerpt );

						}

						// Make post.
						$post = sprintf(
							'<article class="card post type-post entry af4-entry-compact%s" itemscope="" itemtype="https://schema.org/CreativeWork"><div class="grid-x center-y"><div class="cell text small-12-collapse small-collapse small-order-2 %s"><header class="entry-header"><h3 class="entry-title" itemprop="headline"><a href="%s" class="entry-link" rel="bookmark">%s</a></h3></header><div class="entry-content" itemprop="text">%s</div>%s</div>%s</div></article>',
							$auto_excerpt,
							$post_atts[ $eo ]['content'][ $has_thumb ],
							get_permalink( $id ),
							$post_obj->post_title,
							$excerpt,
							$cat_buttons,
							$image
						);

						// Add post to output.
						$stories_output[] = $post;

						// Switch the even/odd indicator.
						$eo = 'even' === $eo ? 'odd' : 'even';
						break;

					case 'quote':
						$post            = $story['post'];
						$post_link_open  = '';
						$post_link_close = '';
						$headings        = '';
						$cat_buttons     = '';
						$quote           = '<div class="quote-text%s">%s</div>';

						// Define post-dependent variables.
						if ( ! empty( $post ) ) {
							$post_link_open  = sprintf(
								'<a class="entry-title-link" rel="bookmark" href="%s">',
								get_permalink( $post->ID )
							);
							$post_link_close = '</a>';

							// Get post heading.
							$heading = "<h3>{$post->post_title}</h3>";

							// Get post quote, dependent on subheading.
							$subheading = agt_get_subheading( $post->post_content );
							$subheading = preg_replace( '/<(\/)?h2>/', '', $subheading );

							if ( ! empty( $subheading ) ) {

								$excerpt = sprintf( $quote, ' has-subheading', $subheading );

							} else {

								$excerpt = sprintf( $quote, ' no-subheading', $story['quote'] );
								$excerpt = str_replace( '<p', '<span', $excerpt );
								$excerpt = str_replace( '</p', '</span', $excerpt );

							}

							// Get all post categories as buttons.
							$post_categories  = wp_get_post_categories( $post->ID );
							$post_cat_button  = '<a href="%s" class="button hollow">%s</a>';
							$post_cat_buttons = array();

							foreach ( $post_categories as $cat_id ) {

								$cat = get_category( $cat_id );

								if ( false === strpos( $cat->slug, 'uncategorized' ) ) {

									$post_cat_buttons[] = sprintf(
										$post_cat_button,
										get_category_link( $cat_id ),
										$cat->name
									);

								}
							}

							if ( 0 < count( $post_cat_buttons ) ) {

								$cat_buttons = sprintf(
									'<div class="post-category">%s</div>',
									implode( '', $post_cat_buttons )
								);

							}
						};

						$stories_output[] = sprintf(
							'<article class="grid-x center-y card full-height" itemscope="" itemtype="https://schema.org/CreativeWork"><div class="cell item quote">%s%s%s%s%s</div></article>',
							$post_link_open,
							$heading,
							$post_link_close,
							$excerpt,
							$cat_buttons
						);

						break;

					default:
						break;
				}
			}

			$story_list = sprintf(
				'<div class="cell medium-8 small-12">%s%s</div><div class="cell auto tall">%s</div>',
				$stories_output[0],
				$stories_output[1],
				$stories_output[2]
			);

			$output .= sprintf(
				$section_output,
				$section['stories']['heading'],
				$story_list
			);

		}
	}

	// Subscribe section.
	if ( ! empty( $subscribe ) ) {

		$output .= sprintf(
			'<div class="subscribe alignfull section invert"><div class="heading-sideline grid-container"><div class="grid-x"><div class="cell auto title-line"></div><h2 class="cell shrink">Subscribe</h2><div class="cell auto title-line"></div></div></div><div class="grid-container"><div class="grid-x"><div class="cell auto">%s</div></div></div></div>',
			$subscribe
		);

	}

	// Latest Posts.
	$section_output = '<div class="latest-story section"><div class="heading-sideline"><div class="grid-x"><div class="cell auto title-line"></div><h2 class="cell shrink">Latest Stories</h2><div class="cell auto title-line"></div></div></div><div class="section-content"><div class="grid-x">%s</div></div></div>';
	$args           = array(
		'posts_per_page' => 3,
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
			'relation' => 'OR',
			array(
				'key'     => 'featured-post',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => 'featured-post',
				'value'   => '1',
				'compare' => '!=',
			),
		),
	);
	$query          = new WP_Query( $args );
	$stories_output = array();
	$eo             = 'odd';

	foreach ( $query->posts as $key => $story ) {
		$id           = $story->ID;
		$has_thumb    = 'thumb';
		$subheading   = agt_get_subheading( $story->post_content );
		$subheading   = preg_replace( '/<(\/)?h2>/', '', $subheading );
		$auto_excerpt = '';

		// Get category button group.
		$post_categories  = wp_get_post_categories( $id );
		$post_cat_button  = '<a href="%s" class="button hollow">%s</a>';
		$post_cat_buttons = array();
		$cat_buttons      = '';

		foreach ( $post_categories as $cat_id ) {

			$cat = get_category( $cat_id );

			if ( false === strpos( $cat->slug, 'uncategorized' ) ) {

				$post_cat_buttons[] = sprintf(
					$post_cat_button,
					get_category_link( $cat_id ),
					$cat->name
				);

			}
		}

		if ( 0 < $post_cat_buttons ) {

			$cat_buttons = sprintf(
				'<div class="post-category">%s</div>',
				implode( '', $post_cat_buttons )
			);

		}

		// Get featured image.
		$image      = '';
		$post_image = get_the_post_thumbnail( $id, 'home-story-image' );

		if ( ! empty( $post_image ) ) {

			$image = sprintf(
				'<span class="cell image medium-4 small-12-collapse small-collapse small-order-1 %s"><a href="%s" title="%s">%s</a></span>',
				$post_atts[ $eo ]['thumb'],
				get_permalink( $post_obj->ID ),
				$post_obj->post_title,
				$post_image
			);

		} else {

			$has_thumb = 'no-thumb';

		}

		// Get excerpt.
		if ( ! empty( $subheading ) ) {

			$excerpt = $subheading;

		} else {

			$auto_excerpt = ' has-auto-excerpt';
			$excerpt      = wp_trim_excerpt( '', $id );
			$excerpt      = preg_replace( '/<span class="read-more"><a [^>]+>[^<]+<\/a><\/span>/', '', $excerpt );

		}

		// Change content if in right column.
		$article_class          = $auto_excerpt;
		$content_class_modified = $post_atts[ $eo ]['content'][ $has_thumb ];

		if ( 2 === $key ) {
			$article_class           = ' center-y';
			$content_class_modified  = preg_replace( '/medium-8-collapse-half|medium-collapse-left/', '', $content_class_modified );
			$content_class_modified .= ' medium-auto medium-collapse';
		}

		// Combine into post.
		$post = sprintf(
			'<article class="card post type-post entry af4-entry-compact full-height" itemscope="" itemtype="https://schema.org/CreativeWork"><div class="grid-x%s full-height"><div class="cell text small-12-collapse small-order-2 %s"><header class="entry-header"><h3 class="entry-title" itemprop="headline"><a href="%s" class="entry-link full-height" rel="bookmark">%s</a></h3></header><div class="entry-content medium-truncate-lines medium-truncate-2-lines" itemprop="text">%s</div>%s</div>%s</div></article>',
			$article_class,
			$content_class_modified,
			get_permalink( $id ),
			$story->post_title,
			$excerpt,
			$cat_buttons,
			$image
		);

		// Add post to output.
		$stories_output[] = $post;

		// Switch the even/odd indicator.
		$eo = 'even' === $eo ? 'odd' : 'even';

	}

	$story_list = sprintf(
		'<div class="cell medium-8 small-12">%s%s</div><div class="cell auto tall">%s</div>',
		$stories_output[0],
		$stories_output[1],
		$stories_output[2]
	);

	$output .= sprintf(
		$section_output,
		$story_list
	);

	// View all posts.
	$output .= sprintf(
		'<div class="view-all section"><a class="button h3 big" href="%s">View All Posts</a></div>',
		get_permalink( get_option( 'page_for_posts' ) )
	);

	// LiveWhale Section.
	$feed_json = wp_remote_get( 'https://calendar.tamu.edu/live/json/events/group/Texas%20A%26amp%3BM%20AgriLife/only_starred/true/max/3/hide_repeats/true/' );

	if ( is_array( $feed_json ) && array_key_exists( 'body', $feed_json ) ) {
		$feed_array   = json_decode( $feed_json['body'], true );
		$l_events     = array_slice( $feed_array, 0, 3 ); // Choose number of events.
		$l_event_list = '';

		foreach ( $l_events as $event ) {

			$title      = $event['title'];
			$url        = $event['url'];
			$location   = $event['location'];
			$date       = $event['date_utc'];
			$time       = $event['date_time'];
			$date       = date_create( $date );
			$date_day   = date_format( $date, 'd' );
			$date_month = date_format( $date, 'M' );

			if ( array_key_exists( 'custom_room_number', $event ) && ! empty( $event['custom_room_number'] ) ) {

				$location = $event['custom_room_number'];

			}

			$l_event_list .= sprintf(
				'<div class="event cell medium-auto small-12"><div class="grid-x "><div class="cell date shrink"><div class="month h3">%s</div><div class="h2 day">%s</div></div><div class="cell title auto"><a href="%s" title="%s" class="event-title medium-truncate-lines medium-truncate-2-lines">%s</a><div class="location medium-truncate-lines medium-truncate-2-lines">%s</div></div></div></div>',
				$date_month,
				$date_day,
				$url,
				$title,
				$title,
				$location
			);

		}

		$output .= sprintf(
			'<div class="alignfull livewhale section invert"><div class="heading-sideline grid-container"><div class="grid-x"><div class="cell auto title-line"></div><h2 class="cell shrink">Events</h2><div class="cell auto title-line"></div></div></div><div class="grid-container"><div class="grid-x  padding-y">%s<div class="events-all cell medium-shrink small-12"><a class="button h3 big gradient" href="http://calendar.tamu.edu/agrilife/" target="_blank">All Events</a></div></div></div></div>',
			$l_event_list
		);
	}

	// In The News Section.
	if ( 'array' === gettype( $in_the_news ) && array_key_exists( 'stories', $in_the_news ) ) {

		$itn_list = '';

		foreach ( $in_the_news['stories'] as $key => $value ) {
			if ( $key > 2 ) {
				$itn_list .= '</div><div class="grid-x">';
			}

			$logo          = wp_get_attachment_image( $value['logo']['id'], 'medium', false );
			$link_open     = '';
			$link_close    = '';
			$no_link_class = ' nolink';
			if ( ! empty( $value['link'] ) ) {
				$link_open     = sprintf( '<a class="entry-title-link" href="%s" rel="nofollow" target="_blank">', $value['link'] );
				$link_close    = '</a>';
				$no_link_class = '';
			}

			$itn_list .= sprintf(
				'<div class="cell card medium-4 small-12%s">%s<div class="logo p">%s</div><h2 class="entry-title" itemprop="headline">%s</h2>%s%s</div>',
				$no_link_class,
				$link_open,
				$logo,
				$value['title'],
				$value['description'],
				$link_close
			);
		}

		$output .= sprintf(
			'<div class="in-the-news section"><div class="heading-sideline"><div class="grid-x"><div class="cell auto title-line"></div><h2 class="cell shrink">%s</h2><div class="cell auto title-line"></div></div></div><div class="section-content"><div class="grid-x">%s</div></div></div>',
			$in_the_news['heading'],
			$itn_list
		);

	}

	// Produce the entire page's output.
	$allowed_tags          = wp_kses_allowed_html( 'post' );
	$allowed_tags['form']  = array(
		'action'   => 1,
		'method'   => 1,
		'onsubmit' => 1,
		'target'   => 1,
	);
	$allowed_tags['input'] = array(
		'placeholder' => 1,
		'type'        => 1,
		'name'        => 1,
		'value'       => 1,
		'class'       => 1,
	);
	$allowed_tags['script'] = array(
		'id'    => 1,
		'src'   => 1,
		'async' => 1,
	);

	echo wp_kses( $output, $allowed_tags );

}

genesis();
