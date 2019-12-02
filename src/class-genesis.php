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

		// Declare default Genesis settings for this theme.
		add_action( 'after_switch_theme', array( $this, 'genesis_default_theme_settings' ) );

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

		// Modify body class.
		add_filter( 'body_class', array( $this, 'today_archive_class' ) );

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
		add_filter( 'genesis_structural_wrap-site-inner', array( $this, 'class_site_inner_wrap' ) );
		add_filter( 'genesis_attr_content-sidebar-wrap', array( $this, 'content_sidebar_wrap_attr' ) );
		add_filter( 'genesis_attr_content', array( $this, 'content_attr' ) );
		add_filter( 'genesis_attr_entry-content', array( $this, 'truncate_lines_attr' ) );

		// Add featured post class.
		// add_filter( 'genesis_attr_entry', array( $this, 'af4_featured_post_class' ) );
		// Modify the post page output.
		genesis_register_sidebar(
			array(
				'name'        => __( 'Post - Share Buttons', 'agrilife-today' ),
				'id'          => 'post-share',
				'description' => __( 'This is a widget area for the share buttons of a single post.', 'agrilife-today' ),
			)
		);
		genesis_register_sidebar(
			array(
				'name'        => __( 'Post - After Entry', 'agrilife-today' ),
				'id'          => 'post-after-entry',
				'description' => __( 'This is a widget area for after single post content.', 'agrilife-today' ),
			)
		);
		add_action( 'genesis_after_header', array( $this, 'news_post_header_image' ) );
		add_action( 'genesis_before_content', array( $this, 'genesis_get_sidebar_post' ) );
		add_action( 'genesis_before', array( $this, 'post_move_hooks' ) );
		add_action( 'genesis_before', array( $this, 'move_subheading' ) );
		add_filter( 'term_links-category', array( $this, 'add_button_class_to_tax_links' ) );
		add_filter( 'term_links-region_category', array( $this, 'add_button_class_to_tax_links' ) );
		add_filter( 'term_links-agency_category', array( $this, 'add_button_class_to_tax_links' ) );
		add_action( 'genesis_entry_footer', array( $this, 'post_meta_footer' ), 5 );
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

		// Change widgets and sidebars.
		add_filter( 'genesis_attr_sidebar-primary', array( $this, 'sidebar_attr' ) );
		add_filter( 'dynamic_sidebar_params', array( $this, 'add_widget_class' ) );

		// Footer.
		genesis_register_sidebar(
			array(
				'name'        => __( 'Footer - Menu', 'agrilife-today' ),
				'id'          => 'footer-1',
				'description' => __( 'This is the first widget area for the site footer.', 'agrilife-today' ),
			)
		);

		genesis_register_sidebar(
			array(
				'name'        => __( 'Footer - Social', 'agrilife-today' ),
				'id'          => 'footer-2',
				'description' => __( 'This is the second widget area for the site footer.', 'agrilife-today' ),
			)
		);

		genesis_register_sidebar(
			array(
				'name'        => __( 'Footer - Contact', 'agrilife-today' ),
				'id'          => 'footer-3',
				'description' => __( 'This is the third widget area for the site footer.', 'agrilife-today' ),
			)
		);

		add_action( 'genesis_before_footer', array( $this, 'genesis_footer_widget_area' ) );
		add_filter( 'genesis_structural_wrap-footer', array( $this, 'footer_wrap' ) );

		// Add taxonomies.
		$this->create_agency_taxonomy();
		$this->create_region_taxonomy();
		$this->create_research_center_taxonomy();

		// Customize archive pages.
		add_action( 'wp', array( $this, 'archive_customizations' ) );

		// Change thumbnail size for related posts.
		add_filter( 'rp4wp_thumbnail_size', array( $this, 'rp4wp_thumbnail_size' ) );

		// Replace nonbreaking spaces in excerpt.
		add_filter( 'the_excerpt', array( $this, 'replace_nbsp' ) );

		// Prevent WordPress from adding 10px to inline width of caption shortcode content.
		add_filter( 'img_caption_shortcode_width', array( $this, 'reduce_caption_shortcode_width' ), 11, 3 );

		// Add hide-for-print class.
		add_filter( 'genesis_attr_site-footer', array( $this, 'attr_hide_for_print' ) );

	}

	/**
	 * Retrieve subheading from within post content.
	 *
	 * @since 0.8.24
	 * @param string $content Post content.
	 * @return string
	 */
	private function get_subheading( $content ) {

		// Get first h2 in $content, sometimes wrapped by HTML comments by Gutenberg.
		$output  = '';
		$pattern = '/^((<!--(.*?)-->)?[\r\n]*(<div[^>]*>)?[\r\n]*(<\s*?(h2){1}\b[^>]*>(.*?)<\/(h2){1}\b[^>]*>))/';
		preg_match( $pattern, $content, $subheading );

		if ( isset( $subheading[5] ) ) {
			$output = $subheading[5];
		}

		return $output;

	}

	/**
	 * Remove subheading from within post content.
	 *
	 * @since 0.8.24
	 * @param string $content Post content.
	 * @return string
	 */
	public function remove_subheading_from_content( $content ) {

		$subheading = $this->get_subheading( $content );

		if ( ! empty( $subheading ) ) {

			$content = str_replace( $subheading, '', $content );

		}

		return $content;

	}

	/**
	 * Add default theme setting values.
	 *
	 * @since 0.6.3
	 * @return void
	 */
	public function genesis_default_theme_settings() {

		if ( ! function_exists( 'genesis_update_settings' ) ) {
			return;
		}

		$settings = array(
			'site_layout'               => 'content-sidebar',
			'content_archive'           => 'excerpts',
			'content_archive_thumbnail' => 1,
			'image_size'                => 'archive',
			'image_alignment'           => '',
			'posts_nav'                 => 'numeric',
		);

		genesis_update_settings( $settings );

	}

	/**
	 * Add grid-container class name
	 *
	 * @since 0.1.0
	 * @param string $output The wrap HTML.
	 * @return string
	 */
	public function class_site_inner_wrap( $output ) {

		$output = str_replace( 'class="', 'class="grid-container ', $output );

		return $output;

	}

	/**
	 * Generate responsive image sizes given an image array.
	 *
	 * @since 0.3.8
	 * @param array $image Image array.
	 * @return string
	 */
	public function get_img_sizes( $image ) {

		$widths = array();
		$sizes  = $image['sizes'];

		foreach ( $sizes as $key => $value ) {
			if ( strpos( $key, '-width' ) && $value > 150 ) {
				$widths[] = sprintf( '(max-width: %1$dpx) %1$dpx', $value );
			}
		}

		$widths[] = "{$image['width']}px";

		return implode( ', ', $widths );

	}

	/**
	 * Generate responsive image srcset given an image array.
	 *
	 * @since 0.3.8
	 * @param array $image Image array.
	 * @return string
	 */
	public function get_img_srcset( $image ) {

		$srcset  = wp_get_attachment_image_srcset( $image['ID'] );
		$srcset .= ", {$image['url']} {$image['width']}w";

		return $srcset;

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

		$header_widgets = array(
			'open'   => '<div id="header-widgets" class="hide-for-print"><div class="grid-container">',
			'close'  => '</div></div>',
			'inside' => get_search_form( false ),
		);

		$header_widgets['inside'] = str_replace( '<form class="search-form', '<form class="search-form grid-x', $header_widgets['inside'] );
		$header_widgets['inside'] = str_replace( 'search-form-input', 'search-form-input cell auto medium-collapse small-collapse', $header_widgets['inside'] );
		$header_widgets['inside'] = str_replace( 'search-form-submit', 'search-form-submit search-icon cell shrink small-collapse', $header_widgets['inside'] );
		$header_widgets['inside'] = str_replace( 'placeholder="Search this website"', 'placeholder="Search"', $header_widgets['inside'] );

		$header_widgets_output = $header_widgets['open'] . $header_widgets['inside'] . $header_widgets['close'];

		$output = preg_replace( '/<div class="wrap"/', '<div class="wrap" data-sticky-container><div class="wrap" data-sticky data-options="stickyOn:small;marginTop:0;"><div class="grid-x"', $output );
		$output = preg_replace( '/<\/div>$/', '</div>' . $header_widgets_output . '</div></div>', $output );

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

		if ( ! is_archive() ) {

			$more = '... <span class="read-more"><a href="' . get_permalink() . '">' .
			__( 'Read More &rarr;', 'agrilife-today' ) . '</a></span>';

		} else {

			$more = '...';

		}

		return $more;

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

		$logo       = sprintf( '<img class="hide-for-print" src="%s">', AGTODAY_THEME_DIRURL . '/images/logo-light.svg' );
		$logo_print = sprintf( '<img class="show-for-print" src="%s">', AGTODAY_THEME_DIRURL . '/images/logo-dark-color.svg' );
		$home       = trailingslashit( home_url() );

		$new_inside = sprintf(
			'<a href="%s" class="logo" title="Texas A&M AgriLife">%s%s</a>',
			$home,
			$logo,
			$logo_print
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

		$open = str_replace( 'title-area', 'title-area cell small-9 medium-12-collapse', $open );

		return $open;

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
		if ( is_singular( 'post' ) ) {
			$attributes['class'] .= ' medium-auto small-12';
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

		if ( 'post-share' === $params[0]['id'] ) {
			return $params;
		}

		// Add class to outer widget container.
		$str = $params[0]['before_widget'];
		preg_match( '/class="([^"]+)"/', $str, $match );
		$classes = explode( ' ', $match[1] );

		if ( in_array( 'widget', $classes, true ) ) {

			// Apply card classes.
			if (
				! in_array( $params[0]['id'], array( 'footer-1', 'footer-2', 'footer-3', 'header-subscribe', 'post-after-entry' ), true )
				|| false !== strpos( $params[0]['widget_id'], 'agt_subscribe' )
			) {
				$classes[] = 'card';
			}

			// Add class to LiveWhale widget.
			if ( false !== strpos( $params[0]['widget_id'], 'agt_livewhale' ) ) {
				$classes[] = 'livewhale invert';

				if ( 'post-after-entry' === $params[0]['id'] ) {
					$classes[] = 'hide-for-print';
				}
			}

			// Invert footer widgets.
			if ( in_array( $params[0]['id'], array( 'footer-1', 'footer-2', 'footer-3' ), true ) ) {
				$classes[] = 'invert no-bg';
			}

			// Hide widget for print.
			if ( 'footer-1' === $params[0]['id'] ) {
				$classes[] = 'hide-for-print';
			}

			$class_output               = implode( ' ', $classes );
			$params[0]['before_widget'] = str_replace( $match[0], "class=\"{$class_output}\"", $params[0]['before_widget'] );
		}

		// Add class to widget title.
		if ( false === strpos( $params[0]['widget_id'], 'agt_subscribe' ) && false === strpos( $params[0]['widget_id'], 'agt_livewhale' ) ) {
			$params[0]['before_title'] = str_replace( 'widget-title', 'widget-title card-heading', $params[0]['before_title'] );
			$params[0]['after_title'] .= '<hr />';
		}

		// Remove blank space from between Add To Any widgets for styling purposes.
		if ( in_array( $params[0]['widget_name'], array( 'AddToAny Share', 'AddToAny Follow' ), true ) ) {
			$params[0]['after_widget'] = preg_replace( '/\s/', '', $params[0]['after_widget'] );
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

		return str_replace( 'class="wrap"', 'class="wrap grid-container grid-x"', $output );

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
		if ( is_singular( 'post' ) ) {

			genesis_widget_area(
				'post-share',
				array(
					'before' => '<div class="widgets-post-share page-widget cell medium-shrink small-12 hide-for-print" data-sticky-container><div class="wrap" data-sticky data-options="stickyOn:medium;marginTop:9;anchor:genesis-content">',
					'after'  => '</div></div>',
				)
			);

		}

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
			remove_action( 'genesis_after_content', 'genesis_get_sidebar' );
			add_action( 'genesis_before_content_sidebar_wrap', 'genesis_entry_header_markup_open', 5 );
			add_action( 'genesis_before_content_sidebar_wrap', 'genesis_entry_header_markup_close', 15 );
			add_action( 'genesis_before_content_sidebar_wrap', array( $this, 'custom_post_category_button' ), 6 );
			add_action( 'genesis_before_content_sidebar_wrap', 'genesis_do_post_title', 11 );
			add_action( 'genesis_before_content_sidebar_wrap', array( $this, 'custom_post_info' ), 11 );
			add_filter( 'genesis_attr_entry-header_output', array( $this, 'add_gutter_lr_class' ), 11, 2 );
			add_action(
				'genesis_before_footer',
				function() {

					genesis_widget_area(
						'post-after-entry',
						array(
							'before' => '<div class="widgets-post-after-entry page-widget alignfull invert">',
							'after'  => '</div>',
						)
					);
				},
				9
			);
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

			if ( false === strpos( $cat->slug, 'uncategorized' ) ) {

				$cat_output .= sprintf(
					'<a href="%s" class="button hollow gradient-hover">%s</a>',
					get_term_link( $cat->term_id ),
					$cat->name
				);

			}
		}

		echo sprintf( '<div class="post-category hide-for-print">%s</div>', wp_kses_post( $cat_output ) );

	}

	/**
	 * Add post meta
	 *
	 * @since 0.3.2
	 * @return void
	 */
	public function custom_post_info() {

		global $post;

		// Find the first h2 element, sometimes between two HTML comments created by Gutenberg.
		$content    = $post->post_content;
		$subheading = $this->get_subheading( $content );

		// Add first h2 element below post title.
		$output = $subheading;

		// Add post meta.
		$output .= '<p class="entry-meta">';
		$output .= do_shortcode( '[post_date]' );
		$output .= '</p>';

		echo wp_kses_post( $output );

	}

	/**
	 * Add post credits below post title
	 *
	 * @since 0.3.3
	 * @param string $content The post content.
	 * @return string
	 */
	public function move_post_credits( $content ) {
		$output  = '';
		$content = strip_shortcodes( get_the_content() );
		$pattern = '/^(?:<[^>]+>|\W|&nbsp;|\b)*([A-Z]{2,}|[A-Z][a-z][A-Z]{2,})([\w\W]*)/m';
		preg_match( $pattern, $content, $portions );
		if ( count( $portions ) > 1 ) {
			// Pattern matched.
			$excerpt = $portions[1] . $portions[2];
		} else {
			// Probably not a news-related post.
			$excerpt = $output;
		}

		return $content;
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

	/**
	 * Adds a "button" class for taxonomy links.
	 *
	 * @since 0.3.6
	 * @param string[] $links An array of term links.
	 * @return array
	 */
	public function add_button_class_to_tax_links( $links ) {
		foreach ( $links as $key => $link ) {
			if ( strpos( $link, ' class=' ) ) {
				$links[ $key ] = str_replace( 'class="', 'class="button ', $link );
			} else {
				$links[ $key ] = str_replace( '<a ', '<a class="button" ', $link );
			}
		}
		return $links;
	}

	/**
	 * Add post meta to the footer
	 *
	 * @since 0.3.6
	 * @return void
	 */
	public function post_meta_footer() {

		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$output = '';

		// Contacts.
		$contacts = get_field( 'contact_group' )['contacts'];

		if ( 1 < count( $contacts ) || ( 1 === count( $contacts ) && ! empty( $contacts[0]['name'] ) ) ) {

			$output .= '<div id="post-media-contact-group" data-toggler=".active" class="media-contact-group clear-both"><button class="alignright" data-toggle="post-media-contact-group" type="button">Media Inquiries</button><div class="meta aligncenter"><div>';

			// Remove empty values from contacts.
			foreach ( $contacts as $key => $value ) {

				$value = array_filter( $value );

				if ( empty( $value ) ) {

					unset( $contacts[ $key ] );

				}
			}

			// Print contacts.
			if ( count( $contacts ) > 0 ) {

				$contact_list = array();

				foreach ( $contacts as $contact ) {

					$contact_output = array();

					if ( ! empty( $contact['name'] ) ) {

						$contact_output[] = $contact['name'];

					}

					if ( ! empty( $contact['email'] ) ) {

						$contact_output[] = sprintf(
							'<a href="mailto:%s">%s</a>',
							$contact['email'],
							$contact['email']
						);

					}

					if ( ! empty( $contact['phone'] ) ) {

						$contact_output[] = sprintf(
							'<a href="tel:+1%s">%s</a>',
							$contact['phone'],
							$contact['phone']
						);

					}

					$contact_list[] = implode( '<br>', $contact_output );

				}

				$output .= implode( $contact_list, '; ' );

			}

			$output .= '</div></div></div>';

		}

		// Post author.
		$output .= sprintf(
			'<div class="p grid-x"><div class="author-photo cell shrink collapse-left">%s</div><div class="author-info cell auto collapse-right"><div class="author-name">%s</div><div><a href="tel:+1%s">%s</a></div><div><a href="mailto:%s">%s</a></div></div></div>',
			get_avatar( get_the_author_meta( 'user_email' ) ),
			get_the_author(),
			get_the_author_meta( 'phone' ),
			get_the_author_meta( 'phone' ),
			get_the_author_meta( 'email' ),
			get_the_author_meta( 'user_email' )
		);

		// Post taxonomy.
		$categories = get_the_term_list(
			get_the_ID(),
			'category',
			'',
			'',
			''
		);

		// Remove uncategorized term.
		preg_match( '/<a [^>]+>[^uU]*Uncategorized[^<]*<\/a>/', $categories, $uncategorized );
		$categories = str_replace( $uncategorized[0], '', $categories );

		// Add to output.
		if ( 'string' === gettype( $categories ) && ! empty( $categories ) ) {

			$output    .= '';
			$categories = sprintf(
				'<div class="news-taxonomy p"><p class="grid-x"><span class="cell shrink cell-valign-center">Category:</span><span class="cell auto">%s</span></p></div>',
				$categories
			);
			$output    .= $categories;

		}

		// Related Posts.
		if ( function_exists( 'rp4wp_children' ) ) {

			$output .= '<div class="related_posts hide-for-print">';

			$related_posts = rp4wp_children( get_the_ID(), false );
			$related_posts = str_replace( '<ul>', '', $related_posts );
			$related_posts = str_replace( '<li', '<div class="grid-x"', $related_posts );
			$related_posts = str_replace( '</li', '</div', $related_posts );
			$related_posts = str_replace( 'rp4wp-related-post-image', 'rp4wp-related-post-image cell collapse-left medium-4 small-4', $related_posts );
			$related_posts = str_replace( 'rp4wp-related-post-content', 'rp4wp-related-post-content cell medium-auto small-auto', $related_posts );
			$related_posts = str_replace( '</ul>', '', $related_posts );

			$output .= $related_posts;
			$output .= '</div>';

		}

		echo wp_kses_post( $output );

	}

	/**
	 * Create agency taxonomy
	 *
	 * @since 0.3.6
	 * @return void
	 */
	public function create_agency_taxonomy() {

		$labels = array(
			'name'              => _x( 'Agency', 'taxonomy general name' ),
			'singular_name'     => _x( 'Agency', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Agency Categories' ),
			'all_items'         => __( 'All Agency Categories' ),
			'parent_item'       => __( 'Parent Agency' ),
			'parent_item_colon' => __( 'Parent Agency:' ),
			'edit_item'         => __( 'Edit Agency' ),
			'update_item'       => __( 'Update Agency' ),
			'add_new_item'      => __( 'Add New Agency' ),
			'new_item_name'     => __( 'New Agency Name' ),
		);

		register_taxonomy(
			'agency_category',
			array( 'post' ),
			array(
				'hierarchical' => true,
				'labels'       => $labels, /* NOTICE: Here is where the $labels variable is used */
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => 'agency' ),
				'show_in_rest' => true,
			)
		);

	}

	/**
	 * Create agency taxonomy
	 *
	 * @since 0.3.6
	 * @return void
	 */
	public function create_research_center_taxonomy() {

		$labels = array(
			'name'          => _x( 'Research Center', 'taxonomy general name' ),
			'singular_name' => _x( 'Research Center', 'taxonomy singular name' ),
			'search_items'  => __( 'Search Research Centers' ),
			'all_items'     => __( 'All Research Centers' ),
			'edit_item'     => __( 'Edit Research Center' ),
			'update_item'   => __( 'Update Research Center' ),
			'add_new_item'  => __( 'Add New Research Center' ),
			'new_item_name' => __( 'New Research Center Name' ),
		);

		register_taxonomy(
			'research_center',
			array( 'post' ),
			array(
				'labels'       => $labels, /* NOTICE: Here is where the $labels variable is used */
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => 'research-center' ),
				'show_in_rest' => true,
			)
		);

	}

	/**
	 * Create region taxonomy
	 *
	 * @since 0.3.6
	 * @return void
	 */
	public function create_region_taxonomy() {

		$labels = array(
			'name'              => _x( 'Region', 'taxonomy general name' ),
			'singular_name'     => _x( 'Region', 'taxonomy singular name' ),
			'search_items'      => __( 'Search Region Categories' ),
			'all_items'         => __( 'All Region Categories' ),
			'parent_item'       => __( 'Parent Region' ),
			'parent_item_colon' => __( 'Parent Region:' ),
			'edit_item'         => __( 'Edit Region' ),
			'update_item'       => __( 'Update Region' ),
			'add_new_item'      => __( 'Add New Region' ),
			'new_item_name'     => __( 'New Agency Region Name' ),
		);

		register_taxonomy(
			'region_category',
			array( 'post' ),
			array(
				'hierarchical' => true,
				'labels'       => $labels,
				'show_ui'      => true,
				'query_var'    => true,
				'rewrite'      => array( 'slug' => 'region' ),
				'show_in_rest' => true,
			)
		);

	}

	/**
	 * Add header group image above content
	 *
	 * @since 0.3.8
	 * @return void
	 */
	public function news_post_header_image() {

		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$header = get_field( 'header_group' );

		if ( $header && array_key_exists( 'image', $header ) ) {

			$image = $header['image'];

			if ( $image ) {

				$size_atts = array(
					'post-heading-small'        => 640,
					'post-heading-medium'       => 960,
					'post-heading-medium_large' => 1366,
					'post-heading-large'        => 1920,
				);

				$srcset       = array();
				$sizes        = array();
				$active_sizes = array();
				$largest      = 'post-heading-small';

				foreach ( $size_atts as $key => $value ) {

					$current_size = $image['sizes'][ "{$key}-width" ];

					if ( ! in_array( $current_size, $active_sizes, true ) && $current_size === $value ) {

						$active_sizes[] = $current_size;
						$srcset[]       = sprintf(
							'%s %sw',
							$image['sizes'][ $key ],
							$current_size
						);
						$sizes[]        = sprintf(
							'(max-width: %spx) %spx',
							$current_size,
							$current_size
						);
						$largest        = $key;

					}
				}

				$sizes[] = $image['sizes'][ "{$largest}-width" ] . 'px';
				$srcset  = implode( ', ', $srcset );
				$sizes   = implode( ', ', $sizes );

				echo sprintf(
					'<div class="content-heading-image"><img src="%s" srcset="%s" sizes="%s" alt="%s"></div>',
					esc_url( $image['sizes'][ $largest ] ),
					esc_attr( $srcset ),
					esc_attr( $sizes ),
					esc_attr( $image['alt'] )
				);

			}
		}

	}

	/**
	 * Add post left sidebar
	 *
	 * @since 0.3.2
	 * @return void
	 */
	public function genesis_footer_widget_area() {

		$logo = sprintf(
			'<a href="%s" title="Texas A&M AgriLife"><img src="%s"></a>',
			trailingslashit( home_url() ),
			AGTODAY_THEME_DIRURL . '/images/logo-agrilife.png'
		);

		$footer_open  = '<div class="footer-widgets"><div class="wrap grid-container grid-x">';
		$footer_close = '</div></div>';

		echo wp_kses_post( $footer_open );

		genesis_widget_area(
			'footer-1',
			array(
				'before' => '<div class="widgets-footer-1 cell small-12 medium-12"><div class="logo hide-for-print">' . $logo . '</div>',
				'after'  => '</div>',
			)
		);

		genesis_widget_area(
			'footer-2',
			array(
				'before' => '<div class="widgets-footer-2 heading-sideline cell small-12 medium-12 hide-for-print"><div class="grid-x"><div class="cell auto title-line"></div><div class="cell shrink social-media">',
				'after'  => '</div><div class="cell auto title-line"></div></div></div>',
			)
		);

		genesis_widget_area(
			'footer-3',
			array(
				'before' => '<div class="widgets-footer-3 cell small-12 medium-12">',
				'after'  => '</div>',
			)
		);

		echo wp_kses_post( $footer_close );

	}

	/**
	 * Filter only post_date for post meta.
	 *
	 * @since 0.5.14
	 * @param string $info Current post meta with shortcodes.
	 * @return string
	 */
	public function date_only( $info ) {

		return '[post_date] [post_edit]';

	}

	/**
	 * Customize archive pages
	 *
	 * @since 0.5.14
	 * @return void
	 */
	public function archive_customizations() {

		if ( is_archive() || ( ! is_front_page() && is_home() ) ) {

			add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );
			add_filter( 'get_term_metadata', array( $this, 'archive_title' ), 10, 4 );

			remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
			remove_action( 'genesis_post_content', 'genesis_do_post_image' );
			remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
			remove_action( 'genesis_after_post_content', 'genesis_post_meta' );
			remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
			remove_action( 'genesis_before_post_content', 'genesis_post_info' );

			add_filter( 'genesis_attr_entry', array( $this, 'af4_entry_compact_class' ) );
			add_action( 'genesis_archive_title_descriptions', array( $this, 'archive_title_open' ), 9 );
			add_action( 'genesis_archive_title_descriptions', array( $this, 'archive_title_close' ), 11 );
			add_action( 'genesis_entry_header', array( $this, 'archive_column_left_open' ), 1 );
			add_action( 'genesis_entry_header', 'genesis_do_post_image', 1 );
			add_action( 'genesis_entry_header', array( $this, 'archive_column_left_close' ), 3 );
			add_action( 'genesis_entry_header', array( $this, 'archive_column_right_open' ), 3 );
			add_action( 'genesis_entry_content', array( $this, 'archive_subheading' ), 9 );
			add_filter( 'get_the_excerpt', array( $this, 'archive_excerpts' ), 10 );
			add_action( 'genesis_entry_footer', 'genesis_post_info' );
			add_action( 'genesis_entry_footer', array( $this, 'custom_post_category_button' ), 11 );
			add_action( 'genesis_entry_footer', array( $this, 'archive_column_right_close' ), 11 );
			add_filter( 'genesis_post_info', array( $this, 'date_only' ) );
			add_filter( 'genesis_prev_link_text', array( $this, 'prev_link_text' ) );
			add_filter( 'genesis_next_link_text', array( $this, 'next_link_text' ) );

		}

		if ( ! is_front_page() && is_home() ) {

			add_action( 'genesis_before_loop', array( $this, 'blog_posts_page_heading' ), 15 );

		}

	}

	/**
	 * Output page title for blog posts page.
	 *
	 * @since 0.9.3
	 * @return void
	 */
	public function blog_posts_page_heading() {

		$heading = get_the_title( get_option( 'page_for_posts', true ) );
		do_action( 'genesis_archive_title_descriptions', $heading, '', 'taxonomy-archive-description' );

	}

	/**
	 * Add af4-entry-compact class to archive posts.
	 *
	 * @since 0.7.13
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function af4_entry_compact_class( $attributes ) {

		$attributes['class'] .= ' af4-entry-compact';

		return $attributes;

	}

	/**
	 * Open archive title grid wrapper.
	 *
	 * @since 0.7.13
	 * @return void
	 */
	public function archive_title_open() {

		echo wp_kses_post( '<div class="grid-x"><div class="cell auto title-line"></div>' );

	}

	/**
	 * Close archive title grid wrapper.
	 *
	 * @since 0.7.13
	 * @return void
	 */
	public function archive_title_close() {

		echo wp_kses_post( '<div class="cell auto title-line"></div></div>' );

	}

	/**
	 * Add subheading below title.
	 *
	 * @since 0.8.24
	 * @return void
	 */
	public function archive_subheading() {

		global $post;
		$subheading = $this->get_subheading( $post->post_content );

		if ( ! is_singular( 'post' ) ) {

			$subheading = str_replace( 'h2', 'p', $subheading );

		}

		if ( ! empty( $subheading ) ) {

			echo wp_kses_post( $subheading );

		}

	}

	/**
	 * Add subheading below title.
	 *
	 * @since 0.8.24
	 * @param string $excerpt The post excerpt.
	 * @return string
	 */
	public function archive_excerpts( $excerpt ) {

		global $post;
		$subheading = $this->get_subheading( $post->post_content );

		if ( ! empty( $subheading ) ) {
			$excerpt = '';
		}

		return $excerpt;

	}

	/**
	 * Open right column of archive content.
	 *
	 * @since 0.5.17
	 * @return void
	 */
	public function archive_column_left_open() {

		$output = '<div class="grid-x grid-margin-x">';

		if ( ! is_singular() && genesis_get_option( 'content_archive_thumbnail' ) ) {

			$img = genesis_get_image(
				array(
					'format'  => 'html',
					'size'    => genesis_get_option( 'image_size' ),
					'context' => 'archive',
					'attr'    => genesis_parse_attr( 'entry-image', array() ),
				)
			);

			if ( ! empty( $img ) ) {

				$output .= '<div class="archive-post-image image cell medium-3 small-12">';

			}
		}

		echo wp_kses_post( $output );

	}

	/**
	 * Close right column of archive content.
	 *
	 * @since 0.5.17
	 * @return void
	 */
	public function archive_column_left_close() {

		if ( ! is_singular() && genesis_get_option( 'content_archive_thumbnail' ) ) {

			$img = genesis_get_image(
				array(
					'format'  => 'html',
					'size'    => genesis_get_option( 'image_size' ),
					'context' => 'archive',
					'attr'    => genesis_parse_attr( 'entry-image', array() ),
				)
			);

			if ( ! empty( $img ) ) {

				echo wp_kses_post( '</div>' );

			}
		}
	}

	/**
	 * Open right column of archive content.
	 *
	 * @since 0.5.17
	 * @return void
	 */
	public function archive_column_right_open() {

		echo wp_kses_post( '<div class="archive-post-content cell small-auto medium-9">' );

	}

	/**
	 * Close right column of archive content.
	 *
	 * @since 0.5.17
	 * @return void
	 */
	public function archive_column_right_close() {

		echo wp_kses_post( '</div></div>' );

	}

	/**
	 * Make meta filter for headline fall back to the taxonomy term's name value.
	 *
	 * @since 0.5.16
	 * @param string $value    Current term metadata value.
	 * @param int    $term_id  Term ID.
	 * @param string $meta_key Meta key.
	 * @param bool   $single   Whether to return only the first value of the specified $meta_key.
	 * @return string
	 */
	public function archive_title( $value, $term_id, $meta_key, $single ) {

		if ( ( is_category() || is_tag() || is_tax() ) && 'headline' === $meta_key && ! is_admin() ) {

			// Grab the current value, be sure to remove and re-add the hook to avoid infinite loops.
			remove_action( 'get_term_metadata', array( $this, 'archive_title' ), 10 );
			$value = get_term_meta( $term_id, 'headline', true );
			add_action( 'get_term_metadata', array( $this, 'archive_title' ), 10, 4 );

			// Use term name if empty.
			if ( empty( $value ) ) {
				$term  = get_queried_object();
				$value = $term->name;
			}
		}

		return $value;

	}

	/**
	 * Customize pagination previous link text.
	 *
	 * @since 0.5.14
	 * @return string
	 */
	public function prev_link_text() {
		return '<';
	}

	/**
	 * Customize pagination next link text.
	 *
	 * @since 0.5.14
	 * @return string
	 */
	public function next_link_text() {
		return '>';
	}

	/**
	 * Add today-archive class to multiple page types we want to display as an archive.
	 *
	 * @since 0.8.6
	 * @param array $classes Current body classes.
	 * @return array
	 */
	public function today_archive_class( $classes ) {

		if ( is_archive() || ( ! is_front_page() && is_home() ) ) {
			$classes[] = 'today-archive';
		}

		return $classes;

	}

	/**
	 * Add af4-entry-compact class to archive posts.
	 *
	 * @since 0.8.9
	 * @param array $attributes HTML attributes.
	 * @return array
	 */
	public function af4_featured_post_class( $attributes ) {

		preg_match( '/post-([\d]+)/i', $attributes['class'], $post_id );

		if ( is_array( $post_id ) && count( $post_id ) > 1 ) {

			$featured = get_post_meta( intval( $post_id[1] ), 'featured-post', true );

			if ( '1' === $featured ) {
				$attributes['class'] .= ' featured-post';
			}
		}

		return $attributes;

	}


	/**
	 * Add af4-entry-compact class to archive posts.
	 *
	 * @since 0.8.13
	 * @param string $thumb_size The current thumbnail size slug.
	 * @return string
	 */
	public function rp4wp_thumbnail_size( $thumb_size ) {

		return 'archive';

	}

	/**
	 * Replace nonbreaking spaces in given string.
	 * The editors that some story authors used added non-standard space
	 * characters, which WordPress converts to non-breaking spaces.
	 *
	 * @since 0.9.7
	 * @param string $str String which has nonbreaking spaces.
	 * @return string
	 */
	public function replace_nbsp( $str ) {

		$excerpt = preg_replace( '/\xc2\xa0/', ' ', $str );
		$excerpt = preg_replace( '/\s+/', ' ', $excerpt );

		return $excerpt;

	}

	/**
	 * Handle subheadings for posts and not pages.
	 *
	 * @since 0.9.13
	 * @return void
	 */
	public function move_subheading() {

		if ( is_singular( 'post' ) || is_front_page() ) {

			add_filter( 'the_content', array( $this, 'remove_subheading_from_content' ) );

		}

	}

	/**
	 * Truncate lines to 3.
	 *
	 * @since 1.0.0
	 * @param array $attr Attributes of the entry-content element.
	 * @return array
	 */
	public function truncate_lines_attr( $attr ) {

		if ( is_archive() ) {

			$attr['class'] .= ' truncate-lines';

		}

		return $attr;

	}

	/**
	 * Filters the width of an image's caption.
	 *
	 * By default, the caption is 10 pixels greater than the width of the image,
	 * to prevent post content from running up against a floated image.
	 *
	 * @since 1.0.1
	 *
	 * @see img_caption_shortcode()
	 *
	 * @param int    $width    Width of the caption in pixels. To remove this inline style,
	 *                         return zero.
	 * @param array  $atts     Attributes of the caption shortcode.
	 * @param string $content  The image element, possibly wrapped in a hyperlink.
	 */
	public function reduce_caption_shortcode_width( $width, $atts, $content ) {

		return $atts['width'];

	}

	/**
	 * Hide element for print.
	 *
	 * @since 1.1.0
	 * @param array $attr Attributes of the entry-content element.
	 * @return array
	 */
	public function attr_hide_for_print( $attr ) {

		$attr['class'] .= ' hide-for-print';
		return $attr;

	}

}
