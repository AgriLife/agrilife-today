<?php
/**
 * The file that provides CSS and JS assets for the theme.
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/src/class-assets.php
 * @since      0.1.0
 * @package    agrilife-today
 * @subpackage agrilife-today/src
 */

namespace AgToday;

/**
 * Loads required theme assets
 *
 * @package agrilife-today
 * @since 0.1.0
 */
class Assets {

	/**
	 * Initialize the class
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function __construct() {

		// Register global scripts used in the theme.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_public_scripts' ) );

		// Enqueue global scripts.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );

		// Register global styles used in the theme.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_public_styles' ), 1 );

		// Enqueue global styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_styles' ), 1 );

		// Register global styles used in the theme.
		add_action( 'wp_enqueue_scripts', array( $this, 'register_external_styles' ) );

		// Enqueue global styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_external_styles' ) );

		// Remove unneeded default assets.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );

		// Remove default styles.
		add_action( 'wp_print_styles', array( $this, 'remove_child_theme_style' ) );
		add_action( 'wp_print_styles', array( $this, 'remove_default_gutenberg_styles' ), 100 );

		// Detect A&M Fonts.
		add_filter( 'style_loader_tag', array( $this, 'detect_cloudfonts_loaded' ) );

	}

	/**
	 * Registers globally used scripts
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function register_public_scripts() {

		wp_register_script(
			'foundation',
			AGTODAY_THEME_DIRURL . '/js/foundation.concat.js',
			array( 'jquery' ),
			filemtime( AGTODAY_THEME_DIRPATH . '/js/foundation.concat.js' ),
			true
		);

		wp_register_script(
			'agrilife-today-public',
			AGTODAY_THEME_DIRURL . '/js/public.min.js',
			array( 'jquery' ),
			filemtime( AGTODAY_THEME_DIRPATH . '/js/public.min.js' ),
			true
		);

	}

	/**
	 * Enqueues globally used scripts
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function enqueue_public_scripts() {

		wp_enqueue_script( 'foundation' );
		wp_enqueue_script( 'agrilife-today-public' );

	}

	/**
	 * Registers all styles used within the theme
	 *
	 * @since 0.1.6
	 * @return void
	 */
	public function register_public_styles() {

		wp_register_style(
			'today-default-styles',
			AGTODAY_THEME_DIRURL . '/css/style.css',
			array(),
			filemtime( AGTODAY_THEME_DIRPATH . '/css/style.css' ),
			'screen'
		);

	}

	/**
	 * Enqueues styles used globally
	 *
	 * @since 0.1.6
	 * @return void
	 */
	public function enqueue_public_styles() {

		wp_enqueue_style( 'today-default-styles' );

	}

	/**
	 * Registers third party styles
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function register_external_styles() {

		wp_register_style(
			'googlefonts',
			'https://fonts.googleapis.com/css?family=Oswald:300,400,700|Open+Sans:400,500,600,800',
			array(),
			'1.0.1',
			'all'
		);

		wp_register_style(
			'cloudfonts',
			'https://cloud.typography.com/6280314/7977592/css/fonts.css',
			array(),
			'1.0.0',
			'all'
		);

	}

	/**
	 * Enqueues third party styles
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function enqueue_external_styles() {

		wp_enqueue_style( 'googlefonts' );
		wp_enqueue_style( 'cloudfonts' );

	}

	/**
	 * Add attributes to stylesheet elements.
	 *
	 * @since 0.5.13
	 * @param string $tag HTML for the stylesheet element.
	 * @return string
	 */
	public function detect_cloudfonts_loaded( $tag ) {

		$tag = preg_replace( "/id='cloudfonts-css'/", "id='cloudfonts-css' onload='document.documentElement.className+=\" has-cloud-fonts\"'", $tag );

		return $tag;

	}

	/**
	 * Remove default style.css file
	 *
	 * @since 0.1.6
	 * @return void
	 */
	public function remove_child_theme_style() {

		wp_dequeue_style( 'agrilife-today' );
		wp_deregister_style( 'agrilife-today' );

	}

	/**
	 * Remove default gutenberg styles.
	 *
	 * @since 0.5.7
	 * @return void
	 */
	public function remove_default_gutenberg_styles() {
		wp_dequeue_style( 'wp-block-library' );
	}

}
