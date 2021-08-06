<?php
/**
 * AgriLife Today
 *
 * @package agrilife-today
 * @since 0.1.0
 * @copyright Copyright (c) 2019, Texas A&M AgriLife Communications
 * @author Texas A&M AgriLife Communications
 * @license GPL-2.0+
 */

// Initialize Genesis.
require_once get_template_directory() . '/lib/init.php';

// Define some useful constants.
define( 'AGTODAY_THEME_DIRNAME', 'agrilife-today' );
define( 'AGTODAY_THEME_DIRPATH', get_stylesheet_directory() );
define( 'AGTODAY_THEME_DIRURL', get_stylesheet_directory_uri() );
define( 'AGTODAY_THEME_TEXTDOMAIN', 'agrilife-today' );
define( 'AGTODAY_THEME_TEMPLATE_PATH', AGTODAY_THEME_DIRPATH . '/templates' );

// Autoload all classes.
require_once AGTODAY_THEME_DIRPATH . '/src/class-agtoday.php';
spl_autoload_register( 'AgToday::autoload' );
AgToday::get_instance();

/* Activation hooks */
register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
register_activation_hook( __FILE__, 'agrilife_today_activation' );

/**
 * Helper option flag to indicate rewrite rules need flushing
 *
 * @since 1.4.0
 * @return void
 */
function agrilife_today_activation() {

	// Register post types and flush rewrite rules.
	AgToday::register_post_types();
	flush_rewrite_rules();

	// Check for missing dependencies.
	$theme  = wp_get_theme();
	$plugin = is_plugin_active( 'advanced-custom-fields-pro/acf.php' );
	if ( false === $plugin ) {
		$error = sprintf(
			/* translators: %s: URL for plugins dashboard page */
			__(
				'Plugin NOT activated: The <strong>AgriLife Today</strong> theme needs the <strong>Advanced Custom Fields Pro</strong> plugin to be installed and activated first. <a href="%s">Back to plugins page</a>',
				'agrilife-today'
			),
			get_admin_url( null, '/plugins.php' )
		);
		wp_die( wp_kses_post( $error ) );
	}

}
// adding a header widget area
function wpb_widgets_init() {
	register_sidebar( array(
	'name' => 'Header Widget',
	'id' => 'header-widget',
	'before_widget' => '<div class="hw-widget">',
	'after_widget' => '</div>',
	'before_title' => '<h2 class="hw-title">',
	'after_title' => '</h2>',
	) );
	
	}
	add_action( 'widgets_init', 'wpb_widgets_init' );