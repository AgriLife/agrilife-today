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

/**
 * Output content of template.
 *
 * @since 0.1.7
 * @return void
 */
function agt_home_page() {

	$story_sections = get_field( 'stories' );
	$in_the_news    = get_field( 'in_the_news' );
	$output         = '';

	// Story Sections.
	if ( ! empty( $story_sections ) ) {

		foreach ( $story_sections as $key => $section ) {

			$section_output = '<div class="story section"><div class="heading-sideline"><div class="grid-x"><div class="cell auto title-line"></div><h2 class="cell shrink">%s</h2><div class="cell auto title-line"></div></div></div><div class="section-content"><div class="grid-x">%s</div></div></div>';
			$stories_output = array();
			$eo             = 'odd';

			foreach ( $section['stories']['stories'] as $key => $story ) {

				switch ( $story['acf_fc_layout'] ) {

					case 'post':
						$id        = $story['post']->ID;
						$post_obj  = $story['post'];
						$post_atts = array(
							'even' => array(
								'thumb'   => 'collapse-left',
								'content' => 'collapse-right',
							),
							'odd'  => array(
								'thumb'   => 'small-collapse-left medium-collapse-right medium-order-2',
								'content' => 'small-collapse-right medium-collapse-left medium-order-1',
							),
						);

						// Get category button group.
						$post_categories  = wp_get_post_categories( $id );
						$post_cat_button  = '<a href="%s" class="button">%s</a>';
						$post_cat_buttons = array();

						foreach ( $post_categories as $cat_id ) {

							$cat = get_category( $cat_id );

							$post_cat_buttons[] = sprintf(
								$post_cat_button,
								get_category_link( $cat_id ),
								$cat->name
							);

						}

						// Get featured image.
						$image      = '';
						$post_image = get_the_post_thumbnail( $post_obj, 'medium_cropped' );
						if ( ! empty( $post_image ) ) {
							$image = sprintf(
								'<div class="cell medium-3 small-3 %s"><a class="entry-image-link" href="%s" aria-hidden="true" tabindex="-1">%s</a></div>',
								$post_atts[ $eo ]['thumb'],
								get_permalink( $id ),
								$post_image
							);
						}

						// Make post.
						$post = sprintf(
							'<article class="card post type-post entry af4-entry-compact" itemscope="" itemtype="https://schema.org/CreativeWork"><div class="grid-x">%s<div class="cell medium-auto small-auto %s"><div class="post-category">%s</div><header class="entry-header"><h2 class="entry-title" itemprop="headline"><a class="entry-title-link" rel="bookmark" href="%s">%s</a></h2></header><div class="entry-content" itemprop="text"><p>%s</p></div></div></div></article>',
							$image,
							$post_atts[ $eo ]['content'],
							implode( '', $post_cat_buttons ),
							get_permalink( $id ),
							$post_obj->post_title,
							$story['description']
						);

						// Add post to output.
						$stories_output[] = $post;

						// Switch the even/odd indicator.
						$eo = 'even' === $eo ? 'odd' : 'even';
						break;

					case 'quote':
						$quote           = $story['quote'];
						$post            = $story['post'];
						$post_link_open  = '';
						$post_link_close = '';
						$cat_buttons     = '';
						// Define post-dependent variables.
						if ( ! empty( $post ) ) {
							$post_link_open  = sprintf(
								'<a class="entry-title-link" rel="bookmark" href="%s">',
								get_permalink( $post->ID )
							);
							$post_link_close = '</a>';

							// Get all post categories as buttons.
							$post_categories  = wp_get_post_categories( $post->ID );
							$post_cat_button  = '<a href="%s" class="button">%s</a>';
							$post_cat_buttons = array();

							foreach ( $post_categories as $cat_id ) {

								$cat = get_category( $cat_id );

								$post_cat_buttons[] = sprintf(
									$post_cat_button,
									get_category_link( $cat_id ),
									$cat->name
								);

							}
							$cat_buttons = implode( '', $post_cat_buttons );

						}

						$quote = str_replace( '<p', '<span', $quote );
						$quote = str_replace( '</p', '</span', $quote );

						$stories_output[] = sprintf(
							'<div class="item quote card"><div class="cat-buttons">%s</div>%s%s%s</div>',
							$cat_buttons,
							$post_link_open,
							$quote,
							$post_link_close
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

	// Latest Posts.
	$section_output = '<div class="latest-story section"><div class="heading-sideline"><div class="grid-x"><div class="cell auto title-line"></div><h2 class="cell shrink">Latest Stories</h2><div class="cell auto title-line"></div></div></div><div class="section-content"><div class="grid-x">%s</div></div></div>';
	$args           = array(
		'numberposts' => 3,
		'post_status' => 'publish',
	);
	$posts          = wp_get_recent_posts( $args, ARRAY_A );
	$stories_output = array();
	$eo             = 'odd';

	foreach ( $posts as $key => $story ) {

		$id        = $story['ID'];
		$post_atts = array(
			'even' => array(
				'thumb'   => 'collapse-left',
				'content' => 'collapse-right',
			),
			'odd'  => array(
				'thumb'   => 'small-collapse-left medium-collapse-right medium-order-2',
				'content' => 'small-collapse-right medium-collapse-left medium-order-1',
			),
		);

		// Get category button group.
		$post_categories  = wp_get_post_categories( $id );
		$post_cat_button  = '<a href="%s" class="button">%s</a>';
		$post_cat_buttons = array();

		foreach ( $post_categories as $cat_id ) {

			$cat = get_category( $cat_id );

			$post_cat_buttons[] = sprintf(
				$post_cat_button,
				get_category_link( $cat_id ),
				$cat->name
			);

		}

		// Get featured image.
		$image      = '';
		$post_image = get_the_post_thumbnail( $story['ID'], 'medium_cropped' );
		if ( ! empty( $post_image ) ) {
			$image = sprintf(
				'<div class="cell medium-3 small-3 %s"><a class="entry-image-link" href="%s" aria-hidden="true" tabindex="-1">%s</a></div>',
				$post_atts[ $eo ]['thumb'],
				get_permalink( $id ),
				$post_image
			);
		}

		// Make post.
		$excerpt = $story['post_excerpt'];

		if ( empty( $excerpt ) ) {
			$content = strip_shortcodes( $story['post_content'] );
			$excerpt = wp_trim_words( $content, 55 );
		}

		$post = sprintf(
			'<article class="card post type-post entry af4-entry-compact" itemscope="" itemtype="https://schema.org/CreativeWork"><div class="grid-x">%s<div class="cell medium-auto small-auto %s"><div class="post-category">%s</div><header class="entry-header"><h2 class="entry-title" itemprop="headline"><a class="entry-title-link" rel="bookmark" href="%s">%s</a></h2></header><div class="entry-content" itemprop="text"><p>%s</p></div></div></div></article>',
			$image,
			$post_atts[ $eo ]['content'],
			implode( '', $post_cat_buttons ),
			get_permalink( $id ),
			$story['post_title'],
			$excerpt
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

	// LiveWhale Section.
	$feed_json    = wp_remote_get( 'https://calendar.tamu.edu/live/json/events/group/Texas%20A%26amp%3BM%20AgriLife/only_starred/true/' );
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
		'<div class="alignfull livewhale section invert"><div class="heading-sideline grid-container"><div class="grid-x"><div class="cell auto title-line"></div><h2 class="cell shrink">Events</h2><div class="cell auto title-line"></div></div></div><div class="grid-container"><div class="grid-x  padding-y">%s<div class="events-all cell medium-shrink small-12"><a class="h3 arrow-right" href="#">All Events</a></div></div></div></div>',
		$l_event_list
	);

	// In The News Section.
	$itn_list = '';
	foreach ( $in_the_news['stories'] as $key => $value ) {
		$logo          = wp_get_attachment_image( $value['logo']['id'], 'medium', false, array( 'class' => 'p' ) );
		$link_open     = '';
		$link_close    = '';
		$no_link_class = ' nolink';
		if ( ! empty( $value['link'] ) ) {
			$link_open     = sprintf( '<a class="entry-title-link" href="%s" rel="nofollow" target="_blank">', $value['link'] );
			$link_close    = '</a>';
			$no_link_class = '';
		}

		$itn_list .= sprintf(
			'<div class="cell card medium-4 small-12%s">%s%s<h2 class="entry-title" itemprop="headline">%s</h2>%s</div>',
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

	// Produce the entire page's output.
	echo wp_kses_post( $output );

}

genesis();
