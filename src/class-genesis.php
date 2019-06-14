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

		// Modify the post page output.
		genesis_register_sidebar(
			array(
				'name'        => __( 'Post - Share Buttons', 'agrilife-today' ),
				'id'          => 'post-share',
				'description' => __( 'This is a widget area for the share buttons of a single post.', 'agrilife-today' ),
			)
		);
		add_action( 'genesis_after_header', array( $this, 'news_post_header_image' ) );
		add_action( 'genesis_before_content', array( $this, 'genesis_get_sidebar_post' ) );
		add_action( 'genesis_before', array( $this, 'post_move_hooks' ) );
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
				'name'        => __( 'Footer - 1', 'agrilife-today' ),
				'id'          => 'footer-1',
				'description' => __( 'This is the first widget area for the site footer.', 'agrilife-today' ),
			)
		);
		genesis_register_sidebar(
			array(
				'name'        => __( 'Footer - 2', 'agrilife-today' ),
				'id'          => 'footer-2',
				'description' => __( 'This is the second widget area for the site footer.', 'agrilife-today' ),
			)
		);
		genesis_register_sidebar(
			array(
				'name'        => __( 'Footer - 3', 'agrilife-today' ),
				'id'          => 'footer-3',
				'description' => __( 'This is the third widget area for the site footer.', 'agrilife-today' ),
			)
		);
		add_action( 'genesis_before_footer', array( $this, 'genesis_footer_widget_area' ) );
		add_filter( 'genesis_structural_wrap-footer', array( $this, 'footer_wrap' ) );

		// Add taxonomies.
		$this->create_agency_taxonomy();
		$this->create_region_taxonomy();

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

		$logo = sprintf( '<img src="%s">', AGTODAY_THEME_DIRURL . '/images/logo.svg' );
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

		$open = str_replace( 'title-area', 'title-area cell small-9 medium-12-collapse', $open );

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

			// Invert footer widgets.
			if ( in_array( $params[0]['id'], array( 'footer-1', 'footer-2', 'footer-3' ), true ) ) {
				$classes[] = 'invert';
			}

			// Apply card classes.
			if (
				! in_array( $params[0]['id'], array( 'footer-1', 'footer-2', 'footer-3' ), true )
				|| false !== strpos( $params[0]['widget_id'], 'agt_subscribe' )
			) {
				$classes[] = 'card';
			}

			$class_output               = implode( ' ', $classes );
			$params[0]['before_widget'] = str_replace( $match[0], "class=\"{$class_output}\"", $params[0]['before_widget'] );
		}

		// Add class to widget title.
		if ( false === strpos( $params[0]['widget_id'], 'agt_subscribe' ) ) {
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

		return str_replace( 'class="wrap"', 'class="wrap layout-container grid-x"', $output );

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
			'post-share',
			array(
				'before' => '<div class="widgets-post-share page-widget cell medium-shrink small-12" data-sticky-container><div class="wrap medium-card" data-sticky data-options="stickyOn:medium;marginTop:7;anchor:genesis-content"><h4 class="widget-title card-heading widgettitle">Share</h4><hr />',
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
		} elseif ( ! is_front_page() ) {
			remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
			add_action( 'genesis_entry_content', array( $this, 'custom_post_category_button' ), 10 );
			add_action( 'genesis_entry_footer', array( $this, 'custom_post_info' ), 11 );
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

		$output = '<p class="entry-meta">';

		// Post Date.
		$output .= do_shortcode( '<strong>[post_date]</strong>' );

		// Contacts.
		$contacts = get_field( 'contact_group' )['contacts'];

		if ( ! empty( $contacts ) ) {
			// Remove empty values from contacts.
			foreach ( $contacts as $key => $value ) {
				$value = array_filter( $value );
				if ( empty( $value ) ) {
					unset( $contacts[ $key ] );
				}
			}
			// Print contacts.
			if ( count( $contacts ) > 0 ) {

				$output      .= '&nbsp; Media contact: ';
				$contact_list = array();

				foreach ( $contacts as $contact ) {

					$contact_output = array();

					if ( ! empty( $contact['name'] ) ) {
						$contact_output[] = $contact['name'];
					}

					if ( ! empty( $contact['phone'] ) ) {
						$contact_output[] = sprintf(
							'<a href="tel:+1%s">%s</a>',
							$contact['phone'],
							$contact['phone']
						);
					}

					if ( ! empty( $contact['email'] ) ) {
						$contact_output[] = sprintf(
							'<a href="mailto:%s">%s</a>',
							$contact['email'],
							$contact['email']
						);
					}

					$contact_list[] = implode( ', ', $contact_output );
				}

				$output .= implode( $contact_list, '; ' );
			}
		}

		$output .= '</p>';

		if ( ! is_singular( 'post' ) ) {
			$output .= '<hr />';
		}

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

		$output = sprintf( '<span class="button hollow">%s</span>', get_the_date( 'F j, Y' ) );

		// Post author.
		$output .= sprintf(
			'<div class="p grid-x"><div class="cell shrink collapse-left">%s</div><div class="cell auto collapse-right"><div><strong>%s</strong></div><div><a href="tel:+1%s">%s</a></div><div><a href="mailto:%s">%s</a></div></div></div>',
			get_avatar( get_the_author_meta( 'user_email' ) ),
			get_the_author(),
			get_the_author_meta( 'phone' ),
			get_the_author_meta( 'phone' ),
			get_the_author_meta( 'email' ),
			get_the_author_meta( 'user_email' )
		);

		// Post taxonomy.
		$output .= '<div class="news-taxonomy">';

		// Categories.
		$categories = get_the_term_list(
			get_the_ID(),
			'category',
			'<p class="grid-x"><span class="cell shrink cell-valign-center">Category:</span><span class="cell auto">',
			'',
			'</span></p>'
		);
		if ( 'string' === gettype( $categories ) ) {
			$output .= $categories;
		}

		// Regions.
		$region_terms = get_the_term_list(
			get_the_ID(),
			'region_category',
			'<p class="grid-x"><span class="cell shrink cell-valign-center">Region:</span><span class="cell auto">',
			'',
			'</span></p>'
		);
		if ( 'string' === gettype( $region_terms ) ) {
			$output .= $region_terms;
		}

		// Agencies.
		$agency_terms = get_the_term_list(
			get_the_ID(),
			'agency_category',
			'<p class="grid-x"><span class="cell shrink cell-valign-center">Agency:</span><span class="cell auto">',
			'',
			'</span></p>'
		);
		if ( 'string' === gettype( $agency_terms ) ) {
			$output .= $agency_terms;
		}

		$output .= '</div>';

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
			AGTODAY_THEME_DIRURL . '/images/logo-light.svg'
		);

		$footer_open  = '<div class="footer-widgets"><div class="wrap layout-container grid-x">';
		$footer_close = '</div></div>';

		echo wp_kses_post( $footer_open );

		genesis_widget_area(
			'footer-1',
			array(
				'before' => '<div class="widgets-footer-1 cell small-12 small-order-1 medium-shrink"><div class="table"><div class="tr"><div class="td logo">' . $logo . '</div></div><div class="tr"><div class="td social-media">',
				'after'  => '</div></div></div></div>',
			)
		);

		genesis_widget_area(
			'footer-2',
			array(
				'before' => '<div class="widgets-footer-2 cell small-12 small-order-2 medium-shrink">',
				'after'  => '</div>',
			)
		);

		genesis_widget_area(
			'footer-3',
			array(
				'before' => '<div class="widgets-footer-3 cell small-12 small-order-3 medium-auto">',
				'after'  => '</div>',
			)
		);

		echo wp_kses_post( $footer_close );

	}

}
