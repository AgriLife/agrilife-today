<?php
/**
 * The file that initializes Genesis features and changes for this child theme.
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/src/class-genesis.php
 * @since      0.1.1
 * @package    agrilife-today
 * @subpackage agrilife-today/src
 */

namespace AgToday;

/**
 * Sets up Genesis Framework to our needs
 *
 * @package agrilife-today
 * @since 0.1.1
 */
class Genesis {

	/**
	 * Initialize the class
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function __construct() {

		// Add the responsive viewport.
		$this->add_responsive_viewport();

		// Add the responsive viewport.
		$this->add_accessibility();

		// Force IE out of compatibility mode.
		add_action( 'genesis_meta', array( $this, 'fix_compatibility_mode' ) );

		// Specify the favicon location.
		add_filter( 'genesis_pre_load_favicon', array( $this, 'add_favicon' ) );

		// Create the structural wraps.
		$this->add_structural_wraps();

		// Clean up the comment area.
		add_filter( 'comment_form_defaults', array( $this, 'cleanup_comment_text' ) );

		// Remove profile fields.
		add_action( 'admin_init', array( $this, 'remove_profile_fields' ) );

		// Remove unneeded layouts.
		$this->remove_genesis_layouts();

		// Remove unneeded sidebars.
		$this->remove_genesis_sidebars();

		// Remove site description.
		remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

		// Move Genesis in-post SEO box to a lower position.
		remove_action( 'admin_menu', 'genesis_add_inpost_seo_box' );
		add_action( 'admin_menu', array( $this, 'move_inpost_seo_box' ) );

		// Move Genesis in-post layout box to a lower position.
		remove_action( 'admin_menu', 'genesis_add_inpost_layout_box' );
		add_action( 'admin_menu', array( $this, 'move_inpost_layout_box' ) );

		// Remove some Genesis settings metaboxes.
		add_action( 'genesis_theme_settings_metaboxes', array( $this, 'remove_genesis_metaboxes' ) );

		// Sticky Header.
		add_filter( 'genesis_structural_wrap-header', array( $this, 'sticky_header' ) );
		remove_action( 'wp_head', 'genesis_custom_header_style' );

		// Add Read More excerpt link.
		add_filter( 'excerpt_more', array( $this, 'agriflex_auto_excerpt_more' ), 11 );

		// Relocate primary navigation menu.
		remove_action( 'genesis_after_header', 'genesis_do_nav' );
		add_action( 'genesis_header', 'genesis_do_nav' );

		// Modify header.
		add_filter( 'genesis_seo_title', array( $this, 'add_logo' ), 10, 3 );
		add_filter( 'genesis_markup_title-area_open', array( $this, 'title_area' ) );

		// Add classes for CSS grid presentation.
		add_filter( 'genesis_attr_site-inner', array( $this, 'add_layout_container_class' ) );
		add_filter( 'genesis_attr_content-sidebar-wrap', array( $this, 'content_sidebar_wrap_attr' ) );
		add_filter( 'genesis_attr_content', array( $this, 'content_attr' ) );
		add_filter( 'genesis_attr_site-footer', array( $this, 'add_layout_container_class' ) );

		// Modify the post page output.
		genesis_register_sidebar(
			array(
				'name'        => __( 'Post - Left', 'agrilife-today' ),
				'id'          => 'post-left',
				'description' => __( 'This is the widget area for the left side of a single post.', 'agrilife-today' ),
			)
		);
		add_action( 'genesis_before_content', array( $this, 'genesis_get_sidebar_post' ) );
		add_action( 'genesis_before', array( $this, 'post_move_hooks' ) );

		// Change widgets and sidebars.
		add_filter( 'genesis_attr_sidebar-primary', array( $this, 'sidebar_attr' ) );
		add_filter( 'dynamic_sidebar_params', array( $this, 'add_widget_class' ) );

		// Footer.
		add_filter( 'genesis_structural_wrap-footer', array( $this, 'footer_wrap' ) );

	}

	/**
	 * Adds the responsive viewport meta tag
	 *
	 * @since 0.1.1
	 * @return void
	 */
	private function add_responsive_viewport() {

		add_theme_support( 'genesis-responsive-viewport' );

	}

	/**
	 * Adds the responsive viewport meta tag
	 *
	 * @since 0.1.1
	 * @return void
	 */
	private function add_accessibility() {

		add_theme_support( 'genesis-accessibility', array( 'search-form', 'skip-links' ) );

		// Move skip links.
		remove_action( 'genesis_before_header', 'genesis_skip_links', 5 );
		add_action( 'genesis_before', 'genesis_skip_links', 1 );

	}

	/**
	 * Forces IE out of compatibility mode
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function fix_compatibility_mode() {

		echo '<meta http-equiv="X-UA-Compatible" content="IE=Edge">';

	}

	/**
	 * Changes the Genesis default favicon location
	 *
	 * @since 0.1.1
	 * @param string $favicon_url The default favicon location.
	 * @return string
	 */
	public function add_favicon( $favicon_url ) {

		return AGTODAY_THEME_DIRURL . '/images/favicon.ico';

	}

	/**
	 * Adds structural wraps to the specified elements
	 *
	 * @since 0.1.1
	 * @return void
	 */
	private function add_structural_wraps() {

		add_theme_support(
			'genesis-structural-wraps',
			array(
				'header',
				'menu-primary',
				'site-inner',
				'footer',
			)
		);

	}


	/**
	 * Remove unneeded user profile fields
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function remove_profile_fields() {

		remove_action( 'show_user_profile', 'genesis_user_options_fields' );
		remove_action( 'edit_user_profile', 'genesis_user_options_fields' );
		remove_action( 'show_user_profile', 'genesis_user_archive_fields' );
		remove_action( 'edit_user_profile', 'genesis_user_archive_fields' );
		remove_action( 'show_user_profile', 'genesis_user_seo_fields' );
		remove_action( 'edit_user_profile', 'genesis_user_seo_fields' );
		remove_action( 'show_user_profile', 'genesis_user_layout_fields' );
		remove_action( 'edit_user_profile', 'genesis_user_layout_fields' );

	}

	/**
	 * Removes any layouts that we don't need
	 *
	 * @since 0.1.1
	 * @return void
	 */
	private function remove_genesis_layouts() {

		genesis_unregister_layout( 'sidebar-content' );
		genesis_unregister_layout( 'content-sidebar-sidebar' );
		genesis_unregister_layout( 'sidebar-sidebar-content' );
		genesis_unregister_layout( 'sidebar-content-sidebar' );

	}

	/**
	 * Removes any default sidebars that we don't need
	 *
	 * @since 0.1.1
	 * @return void
	 */
	private function remove_genesis_sidebars() {

		unregister_sidebar( 'sidebar-alt' );
		unregister_sidebar( 'header-right' );

	}

	/**
	 * Cleans up the default comments text
	 *
	 * @since 0.1.1
	 * @param array $args The default arguments.
	 * @return array The new arguments
	 */
	public function cleanup_comment_text( $args ) {

		$args['comment_notes_before'] = '';
		$args['comment_notes_after']  = '';

		return $args;

	}

	/**
	 * Moves the Genesis in-post SEO box to a lower position
	 *
	 * @since 0.1.1
	 * @author Bill Erickson
	 * @return void
	 */
	public function move_inpost_seo_box() {

		if ( genesis_detect_seo_plugins() ) {
			return;
		}

		foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {
			if ( post_type_supports( $type, 'genesis-seo' ) ) {
				add_meta_box( 'genesis_inpost_seo_box', __( 'Theme SEO Settings', 'agrilife-today' ), 'genesis_inpost_seo_box', $type, 'normal', 'default' );
			}
		}

	}

	/**
	 * Moves the Genesis in-post layout box to a lower postion
	 *
	 * @since 0.1.1
	 * @return void
	 */
	public function move_inpost_layout_box() {

		if ( ! current_theme_supports( 'genesis-inpost-layouts' ) ) {
			return;
		}

		foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {
			if ( post_type_supports( $type, 'genesis-layouts' ) ) {
				add_meta_box( 'genesis_inpost_layout_box', __( 'Layout Settings', 'genesis' ), 'genesis_inpost_layout_box', $type, 'normal', 'default' );
			}
		}

	}

	/**
	 * Adds attributes for sticky navigation and add wrap for header layout requirements
	 *
	 * @since 0.1.1
	 * @param string $_genesis_theme_settings_pagehook The hook name for the genesis theme setting.
	 * @return void
	 */
	public function remove_genesis_metaboxes( $_genesis_theme_settings_pagehook ) {

		if ( ! is_super_admin() ) {
			remove_meta_box( 'genesis-theme-settings-version', $_genesis_theme_settings_pagehook, 'main' );
		}

		remove_meta_box( 'genesis-theme-settings-nav', $_genesis_theme_settings_pagehook, 'main' );
		remove_meta_box( 'genesis-theme-settings-scripts', $_genesis_theme_settings_pagehook, 'main' );

	}

	/**
	 * Adds attributes for sticky navigation and add wrap for header layout requirements
	 *
	 * @since 0.1.1
	 * @param string $output The output of the Genesis header wrap.
	 * @return string
	 */
	public function sticky_header( $output ) {

		$output = preg_replace( '/<div class="wrap"/', '<div class="wrap" data-sticky-container><div class="wrap" data-sticky data-options="stickyOn:small;marginTop:0;"><div class="grid-x"', $output );
		$output = preg_replace( '/<\/div>$/', '</div></div></div>', $output );

		return $output;

	}

	/**
	 * Add close wrap to enable desired header layout
	 *
	 * @since 0.1.1
	 * @param string $output The output of the header extra wrap close.
	 * @return string
	 */
	public function header_extra_wrap_close( $output ) {

		return $output;

	}

	/**
	 * Adds the Read More link to post excerpts
	 *
	 * @since 0.1.1
	 * @param string $more The current "more" text.
	 * @return string
	 */
	public function agriflex_auto_excerpt_more( $more ) {

		return '... <span class="read-more"><a href="' . get_permalink() . '">' .
		__( 'Read More &rarr;', 'agrilife-today' ) . '</a></span>';

	}

	/**
	 * Initialize the class
	 *
	 * @since 0.1.1
	 * @param string $title Genesis SEO title html.
	 * @param string $inside The inner HTML of the title.
	 * @param string $wrap The tag name of the seo title wrap element.
	 * @return string
	 */
	public function add_logo( $title, $inside, $wrap ) {

		$logo = sprintf( '<img src="%s">', AGTODAY_THEME_DIRURL . '/images/logo.png' );
		$home = trailingslashit( home_url() );

		$new_inside = sprintf(
			'<a href="%s" class="logo" title="Texas A&M AgriLife">%s</a>',
			$home,
			$logo
		);

		$title = str_replace( $inside, $new_inside, $title );

		return $title;

	}

	/**
	 * Change the title area class name
	 *
	 * @since 0.3.0
	 * @param string $open HTML for the open tag.
	 * @return string
	 */
	public function title_area( $open ) {

		$open = str_replace( 'title-area', 'title-area cell small-9 medium-12', $open );

		return $open;

	}

	/**
	 * Add class name to site-inner element
	 *
	 * @since 0.1.4
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function add_layout_container_class( $attributes ) {
		$attributes['class'] .= ' layout-container';
		return $attributes;
	}

	/**
	 * Add class name to content-sidebar-wrap element
	 *
	 * @since 0.1.4
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function content_sidebar_wrap_attr( $attributes ) {
		$attributes['class'] .= ' grid-x cell-gutter-lr';
		return $attributes;
	}

	/**
	 * Add class names to content element
	 *
	 * @since 0.1.4
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function content_attr( $attributes ) {
		$attributes['class'] .= ' cell';
		$site_layout          = genesis_site_layout();
		if ( is_singular( 'post' ) && in_array( $site_layout, array( 'content-sidebar', 'sidebar-content' ), true ) ) {
			$attributes['class'] .= ' medium-6 small-12';
		} elseif ( in_array( $site_layout, array( 'content-sidebar', 'sidebar-content' ), true ) ) {
			$attributes['class'] .= ' medium-8 small-12';
		} else {
			$attributes['class'] .= ' medium-12 small-12';
		}
		return $attributes;
	}

	/**
	 * Add class names to sidebar element
	 *
	 * @since 0.1.4
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function sidebar_attr( $attributes ) {
		$attributes['class'] .= ' cell medium-4 small-12';
		return $attributes;
	}

	/**
	 * Add class name to widget elements
	 *
	 * @since 0.1.9
	 * @param array $params Widget parameters.
	 * @return array
	 */
	public function add_widget_class( $params ) {

		// Add class to outer widget container.
		$str = $params[0]['before_widget'];
		preg_match( '/class="([^"]+)"/', $str, $match );
		$classes = explode( ' ', $match[1] );
		if ( in_array( 'widget', $classes, true ) ) {
			$classes[]                  = 'card';
			$class_output               = implode( ' ', $classes );
			$params[0]['before_widget'] = str_replace( $match[0], "class=\"{$class_output}\"", $params[0]['before_widget'] );
		}

		// Add class to widget title.
		if ( false === strpos( $params[0]['widget_id'], 'agt_subscribe' ) ) {
			$params[0]['before_title'] = str_replace( 'widget-title', 'widget-title card-heading', $params[0]['before_title'] );
			$params[0]['after_title'] .= '<hr />';
		}

		return $params;

	}

	/**
	 * Add class to footer wrap
	 *
	 * @since 0.3.0
	 * @param string $output HTML output for the footer wrap.
	 * @return string
	 */
	public function footer_wrap( $output ) {

		return str_replace( 'class="wrap"', 'class="wrap grid-x"', $output );

	}

	/**
	 * Add post left sidebar
	 *
	 * @since 0.3.2
	 * @return void
	 */
	public function genesis_get_sidebar_post() {

		$site_layout = genesis_site_layout();

		// Don't load sidebar-alt on pages that don't need it.
		if ( ! is_singular( 'post' ) || ! in_array( $site_layout, array( 'content-sidebar', 'sidebar-content' ), true ) ) {
			return;
		}
		genesis_widget_area(
			'post-left',
			array(
				'before' => '<div class="page-widget cell medium-2 small-12"><div class="wrap">',
				'after'  => '</div></div>',
			)
		);

	}

	/**
	 * Move hooks on single posts
	 *
	 * @since 0.3.2
	 * @return void
	 */
	public function post_move_hooks() {

		if ( is_singular( 'post' ) ) {
			remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
			remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
			remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
			remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
			remove_action( 'genesis_before_post_content', 'genesis_post_info' );
			add_action( 'genesis_before_content_sidebar_wrap', 'genesis_entry_header_markup_open', 5 );
			add_action( 'genesis_before_content_sidebar_wrap', 'genesis_entry_header_markup_close', 15 );
			add_action( 'genesis_before_content_sidebar_wrap', array( $this, 'custom_post_category_button' ), 6 );
			add_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_post_title', 11 );
			add_action( 'genesis_before_content_sidebar_wrap', array( $this, 'custom_post_info' ), 11 );
			add_filter( 'genesis_attr_entry-header_output', array( $this, 'add_gutter_lr_class' ), 11, 2 );
		}

	}

	/**
	 * Add post category button
	 *
	 * @since 0.3.2
	 * @return void
	 */
	public function custom_post_category_button() {

		$cats       = wp_get_post_terms( get_the_ID(), 'category' );
		$cat_output = '';

		foreach ( $cats as $cat ) {
			$cat_output .= sprintf(
				'<a href="%s" class="button">%s</a>',
				get_term_link( $cat->term_id ),
				$cat->name
			);
		}

		echo sprintf( '<div class="post-category">%s</div>', wp_kses_post( $cat_output ) );

	}

	/**
	 * Add post meta
	 *
	 * @since 0.3.2
	 * @return void
	 */
	public function custom_post_info() {
		echo do_shortcode( '<p class="entry-meta">[post_date]</p>' );
	}


	/**
	 * Add gutter left and right class to element
	 *
	 * @since 0.3.3
	 * @param string $output The element output.
	 * @param array  $attributes Element attributes.
	 * @return array
	 */
	public function add_gutter_lr_class( $output, $attributes ) {
		$oldclass = $attributes['class'];
		$newclass = $oldclass . ' cell-gutter-lr-x2';
		return str_replace( $oldclass, $newclass, $output );
	}

}
