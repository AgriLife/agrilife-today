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

		add_theme_support( 'html5', array() );

		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );

		add_action( 'init', array( $this, 'init' ) );

		// Add Widgets.
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		// Add Image Sizes.
		$this->add_image_sizes();
		add_filter( 'image_size_names_choose', array( $this, 'select_custom_image_sizes' ) );

		// Remove Related Posts.
		add_action( 'init', array( $this, 'rp4wp_example_remove_filter' ) );

		// Enable automatic responsive image attributes.
		add_filter( 'wp_kses_allowed_html', array( $this, 'post_allowed_tags' ), 11, 2 );

	}

	/**
	 * Add theme support for wide page alignment
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function after_setup_theme() {

		add_theme_support( 'align-wide' );

	}

	/**
	 * Initialize the various classes
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function init() {

		// Enqueue our assets.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-genesis.php';
		$agt_genesis = new \AgToday\Genesis();

		// Enqueue our assets.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-assets.php';
		$agt_assets = new \AgToday\Assets();

		// Enqueue our assets.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-navigation.php';
		$agt_nav = new \AgToday\Navigation();

		// Enqueue our assets.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-requireddom.php';
		$agt_required = new \AgToday\RequiredDOM();

		// Add page template custom fields.
		if ( class_exists( 'acf' ) ) {
			require_once AGTODAY_THEME_DIRPATH . '/fields/home.php';
			require_once AGTODAY_THEME_DIRPATH . '/fields/news-post.php';
		}

		// Add meta boxes.
		require_once AGTODAY_THEME_DIRPATH . '/src/class-meta-boxes.php';
		$meta_boxes = new \AgToday\Meta_Boxes();

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

		require_once AGTODAY_THEME_DIRPATH . '/src/class-widget-livewhale.php';
		register_widget( 'Widget_LiveWhale' );

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

}
