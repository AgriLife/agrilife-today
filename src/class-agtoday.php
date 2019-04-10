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
		}

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

}
