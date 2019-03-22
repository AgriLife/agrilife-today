<?php
/**
 * AgriLife Today
 *
 * @package AgriLife Today
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
