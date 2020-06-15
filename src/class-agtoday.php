<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/src/class-agrilife-today.php
 * @since      0.1.0
 * @package    agrilife-today
 * @subpackage agrilife-today/src
 */

/**
 * The core plugin class
 *
 * @since 0.1.0
 */
class AgToday {

	/**
	 * File name
	 *
	 * @var file
	 */
	private static $file = __FILE__;

	/**
	 * Instance
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @return void
	 */
	private function __construct() {

		// Require classes.
		$this->require_classes();

		add_theme_support( 'html5', array() );

		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );

		add_action( 'init', array( $this, 'init' ) );

		// Add hooks for custom post type.
		add_filter( 'post_type_link', array( $this, 'in_the_news_title_link' ), 11, 2 );

		// Add Widgets.
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// Add Image Sizes.
		$this->add_image_sizes();
		add_filter( 'image_size_names_choose', array( $this, 'select_custom_image_sizes' ) );

		// Remove Related Posts.
		add_action( 'init', array( $this, 'rp4wp_example_remove_filter' ) );

		// Enable automatic responsive image attributes.
		add_filter( 'wp_kses_allowed_html', array( $this, 'post_allowed_tags' ), 11, 2 );

		// Change related post excerpt length.
		add_filter( 'rp4wp_general_post_excerpt_length', array( $this, 'rp4wp_change_post_excerpt_length' ) );

		// Speed up rss feed cache refresh.
		add_filter( 'wp_feed_cache_transient_lifetime', array( $this, 'rss_widget_refresh_interval' ) );

		// Make Feedzy use the smaller of the first two enclosure images in an RSS feed item.
		add_filter( 'feedzy_retrieve_image', array( $this, 'feedzy_retrieve_image' ), 11, 2 );

	}

	/**
	 * Initialize the various classes
	 *
	 * @since 1.4.0
	 * @return void
	 */
	private function require_classes() {

		// Initialize Genesis hooks.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-genesis.php';
		$agt_genesis = new \AgToday\Genesis();

		// Enqueue our assets.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-assets.php';
		$agt_assets = new \AgToday\Assets();

		// Make navigation changes.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-navigation.php';
		$agt_nav = new \AgToday\Navigation();

		// Include required elements.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-requireddom.php';
		$agt_required = new \AgToday\RequiredDOM();

		// Add meta boxes.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-meta-boxes.php';
		$meta_boxes = new \AgToday\Meta_Boxes();

		// Add post type classes.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-posttype.php';
		require_once AGTODAY_THEME_DIRPATH . '/src/class-posttemplates.php';
		require_once AGTODAY_THEME_DIRPATH . '/src/class-taxonomy.php';

	}

	/**
	 * Add theme support for wide page alignment
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function after_setup_theme() {

		add_theme_support( 'align-wide' );
		add_theme_support( 'responsive-embeds' );

	}

	/**
	 * Initialize the various classes
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function init() {

		// Include custom post types and their filters or action hooks.
		$this->register_post_types();

		// Add page template custom fields.
		if ( class_exists( 'acf' ) ) {
			require_once AGTODAY_THEME_DIRPATH . '/fields/home.php';
			require_once AGTODAY_THEME_DIRPATH . '/fields/news-post.php';
			require_once AGTODAY_THEME_DIRPATH . '/fields/in-the-news.php';
			require_once AGTODAY_THEME_DIRPATH . '/fields/news-source.php';
			require_once AGTODAY_THEME_DIRPATH . '/fields/tag.php';
		}

	}

	/**
	 * Initialize custom post types
	 *
	 * @since 1.4.0
	 * @return void
	 */
	public static function register_post_types() {

		// Add In The News Post Type.
		// Add taxonomies.
		new \AgToday\Taxonomy(
			'News Source',
			'news-source',
			'in-the-news',
			array(
				'labels' => array(
					'name' => 'News Source',
				),
			)
		);

		// Add custom post type.
		new \AgToday\PostType(
			array(
				'singular' => 'In The News',
				'plural'   => 'In The News',
			),
			AGTODAY_THEME_TEMPLATE_PATH,
			'in-the-news',
			'agrilife-today',
			array( 'news-source' ),
			'dashicons-admin-site',
			array( 'title', 'editor' ),
			array( 'archive' => 'archive-in-the-news.php' )
		);

	}

	/**
	 * Add attributes for entry title link.
	 *
	 * @since 1.4.0
	 * @param string  $post_link The post's permalink.
	 * @param WP_Post $post      The post in question.
	 * @return array
	 */
	public function in_the_news_title_link( $post_link, $post ) {

		if ( 'in-the-news' === $post->post_type ) {

			$field     = get_field( 'in_the_news_details', $post->ID );
			$post_link = '#';

			if (
				$field &&
				array_key_exists( 'link', $field ) &&
				! empty( $field['link'] )
			) {

				$post_link = $field['link'];

			}
		}

			return $post_link;

	}

	/**
	 * Initialize page templates
	 *
	 * @since 0.1.6
	 * @return void
	 */
	private function register_templates() {

		require_once AGTODAY_THEME_DIRPATH . '/src/class-pagetemplate.php';
		$home = new \AgToday\PageTemplate( AGTODAY_THEME_TEMPLATE_PATH, 'home.php', 'Home Page' );
		$home->register();

	}

	/**
	 * Add custom image sizes
	 *
	 * @since 0.4.7
	 * @return void
	 */
	private function add_image_sizes() {

		// Archive page.
		add_image_size( 'archive', 400, 225, true );
		add_image_size( 'archive_medium', 640, 360, true );

		// Home page.
		add_image_size( 'medium_cropped', 300, 300, true );
		add_image_size( 'thumb-three-two', 300, 200, true );
		add_image_size( 'home-story-image', 400, 300, true );

		// Post headings at 16:7 aspect ratio.
		add_image_size( 'post-heading-small', 640, 480, true );
		add_image_size( 'post-heading-medium', 960, 420, true );
		add_image_size( 'post-heading-medium_large', 1366, 598, true );
		add_image_size( 'post-heading-large', 1920, 840, true );

	}

	/**
	 * Make custom image sizes selectable in media dashboard
	 *
	 * @since 0.4.7
	 * @param array $sizes Current image size options.
	 * @return array
	 */
	public function select_custom_image_sizes( $sizes ) {

		return array_merge(
			$sizes,
			array(

				'post-heading-small'        => __( 'Post Heading - Small' ),
				'post-heading-medium'       => __( 'Post Heading - Medium' ),
				'post-heading-medium-large' => __( 'Post Heading - Medium Large' ),
				'post-heading-large'        => __( 'Post Heading - Large' ),

			)
		);

	}

	/**
	 * Autoloads any classes called within the theme
	 *
	 * @since 0.1.0
	 * @param string $classname The name of the class.
	 * @return void
	 */
	public static function autoload( $classname ) {

		$filename = dirname( __FILE__ ) .
			DIRECTORY_SEPARATOR .
			str_replace( '_', DIRECTORY_SEPARATOR, $classname ) .
			'.php';
		if ( file_exists( $filename ) ) {
			require $filename;
		}

	}

	/**
	 * Return instance of class
	 *
	 * @since 0.1.0
	 * @return object.
	 */
	public static function get_instance() {

		return null === self::$instance ? new self() : self::$instance;

	}

	/**
	 * Register widgets
	 *
	 * @since 0.1.5
	 * @return void
	 */
	public function register_widgets() {

		require_once AGTODAY_THEME_DIRPATH . '/src/class-widget-subscribe.php';
		register_widget( 'Widget_Subscribe' );

	}

	/**
	 * Remove automatic Related Post Plugin content after a post.
	 *
	 * @since 0.4.2
	 * @return void
	 */
	public function rp4wp_example_remove_filter() {
		if ( class_exists( 'RP4WP_Manager_Filter' ) ) {
			$filter_instance = RP4WP_Manager_Filter::get_filter_object( 'RP4WP_Filter_After_Post' );
			if ( $filter_instance ) {
				remove_filter( 'the_content', array( $filter_instance, 'run' ), $filter_instance->get_priority() );
			}
		}
	}

	/**
	 * Change allowed HTML tags for wp_kses_post()
	 *
	 * @since 0.7.0
	 * @param array  $allowedposttags Allowed HTML elements and attributes.
	 * @param string $context The filter context within the current instance.
	 * @return array
	 */
	public function post_allowed_tags( $allowedposttags, $context ) {

		if ( 'post' === $context ) {
			$allowedposttags['img']['srcset'] = true;
			$allowedposttags['img']['sizes']  = true;
		}

		return $allowedposttags;

	}

	/**
	 * Remove related post excerpt
	 *
	 * @since 0.8.13
	 * @param string $excerpt_length The excerpt length.
	 * @return string
	 */
	public function rp4wp_change_post_excerpt_length( $excerpt_length ) {
		return '0';
	}

	/**
	 * Speed up widget refresh interval.
	 *
	 * @since 1.3.4
	 * @param int $seconds The current refresh rate in seconds.
	 * @return int
	 */
	public function rss_widget_refresh_interval( $seconds ) {

		return 600;

	}

	/**
	 * Retrive image from the item object
	 *
	 * @since   1.3.6
	 *
	 * @param   string $the_thumbnail The thumbnail url.
	 * @param   object $item The item object.
	 *
	 * @return  string
	 */
	public function feedzy_retrieve_image( $the_thumbnail, $item ) {

		$data = $item->data;

		if ( array_key_exists( 'child', $data ) ) {

			$child = array_values( $data['child'] )[0];

			if ( count( $child['enclosure'] ) > 1 ) {

				$enclosure_a = array_values( $child['enclosure'][0]['attribs'] )[0];
				$enclosure_b = array_values( $child['enclosure'][1]['attribs'] )[0];
				$length_a    = $enclosure_a['length'];
				$length_b    = $enclosure_b['length'];

				if ( $length_b < $length_a ) {

					$the_thumbnail = $enclosure_b['url'];

				}
			}
		}

		return $the_thumbnail;

	}

}
