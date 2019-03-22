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
 * @package AgriFlex4
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
		add_action( 'wp_enqueue_scripts', array( $this, 'register_public_styles' ) );

		// Enqueue global styles.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_styles' ) );

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
			false,
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
	 * @since 0.1.0
	 * @return void
	 */
	public function register_public_styles() {

		wp_register_style(
			'agrilife-today-default-styles',
			AGTODAY_THEME_DIRURL . '/css/default.css',
			array(),
			filemtime( AGTODAY_THEME_DIRPATH . '/css/default.css' ),
			'screen'
		);

	}

	/**
	 * Enqueues styles used globally
	 *
	 * @since 0.1.0
	 * @global $wp_styles
	 * @return void
	 */
	public function enqueue_public_styles() {

		wp_enqueue_style( 'agrilife-today-default-styles' );

	}


}
