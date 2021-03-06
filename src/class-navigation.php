<?php
/**
 * The file that provides navigation DOM changes for the Foundation primary nav menu.
 *
 * A class definition that inserts or changes DOM where needed in the theme
 * to provide a correctly formatted navigation menu for the Foundation framework
 * to interact with.
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/src/class-navigation.php
 * @since      0.1.1
 * @package    agrilife-today
 * @subpackage agrilife-today/src
 */

namespace AgToday;

/**
 * Add Required DOM elements and changes for the primary navigation menu.
 *
 * @package agrilife-today
 * @since 0.1.1
 */
class Navigation {

	/**
	 * Initialize the class
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function __construct() {

		// // Use a custom walker for the primary nav.
		add_filter( 'genesis_do_nav', array( $this, 'custom_nav_walker' ), 10, 3 );

		// Remove span tags from nav link elements.
		add_filter( 'wp_nav_menu_args', array( $this, 'custom_nav_attributes' ) );

	}

	/**
	 * Add HTML attributes to the primary navigation menu.
	 *
	 * @since 0.1.1
	 * @param array $atts Attributes of each registered navigation menu.
	 * @return array
	 */
	public function custom_nav_attributes( $atts ) {

		if ( 'primary' === $atts['theme_location'] ) {
			$atts['link_before'] = '';
			$atts['link_after']  = '';
		}

		return $atts;
	}

	/**
	 * Add HTML elements to the primary navigation menu container and modify each
	 * navigation menu item with required attributes for the Foundation framework.
	 *
	 * @since 0.1.1
	 * @param string $nav_output The full output of the navigation menu.
	 * @param string $nav The output of the navigation menu itself.
	 * @param array  $args The list of arguments for the navigation menu.
	 * @return array
	 */
	public function custom_nav_walker( $nav_output, $nav, $args ) {

		$args['menu_class'] = $args['menu_class'] . ' accordion vertical medium-horizontal';
		require_once AGTODAY_THEME_DIRPATH . '/src/class-customnavigationwalker.php';
		$args['walker']     = new \AgToday\CustomNavigationWalker();
		$args['items_wrap'] = '<ul id="%s" class="%s" data-responsive-menu="accordion medium-dropdown" data-dropdown-menu>%s</ul>';

		$nav_menu = wp_nav_menu( $args );

		$title_bars = array(
			'wrap_open'  => '<div class="title-bars hide-for-print title-bar-right cell small-3 show-for-small-only">',
			'wrap_close' => '</div>',
			'inside'     => '<div class="title-bar title-bar-search" data-responsive-toggle="header-widgets"><button class="search-icon" type="button" data-toggle="header-widgets"></button><div class="title-bar-title">Search</div></div><div class="title-bar title-bar-navigation" data-responsive-toggle="nav-menu-primary"><button class="menu-icon" type="button" data-toggle="nav-menu-primary"></button><div class="title-bar-title" data-toggle="nav-menu-primary">Menu</div></div>',
		);

		$title_bars['all'] = $title_bars['wrap_open'] . $title_bars['inside'] . $title_bars['wrap_close'];

		$before_nav = apply_filters( 'agt_before_nav', $title_bars['all'], $title_bars['wrap_open'], $title_bars['wrap_close'], $title_bars['inside'] );

		$nav = sprintf(
			'<div class="top-bar" id="nav-menu-primary"><section class="top-bar-left">%s</section></div>',
			$nav_menu
		);

		$nav_markup_open = genesis_markup(
			array(
				'html5'   => '<nav id="genesis-nav-primary" class="nav-primary hide-for-print cell medium-12-collapse small-12-collapse" role="navigation">',
				'xhtml'   => '<div id="genesis-nav-primary" class="nav-primary hide-for-print cell medium-12-collapse small-12-collapse">',
				'context' => 'nav-primary',
				'echo'    => false,
			)
		);

		$nav_markup_open  .= genesis_get_structural_wrap( 'menu-primary', 'open' );
		$nav_markup_close  = genesis_get_structural_wrap( 'menu-primary', 'close' );
		$nav_markup_close .= genesis_html5() ? '</nav>' : '</div>';
		$nav_output        = $before_nav . $nav_markup_open . $nav . $nav_markup_close;

		return $nav_output;

	}

	/**
	 * Get the instance of this class
	 *
	 * @since 0.1.1
	 * @return object
	 */
	public static function get_instance() {

		return null === self::$instance ? new self() : self::$instance;

	}

}
