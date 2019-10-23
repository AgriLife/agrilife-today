<?php
/**
 * The file that provides CSS and JS assets for the theme.
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/src/class-meta-boxes.php
 * @since      0.8.7
 * @package    agrilife-today
 * @subpackage agrilife-today/src
 */

namespace AgToday;

/**
 * Loads required theme assets
 *
 * @package agrilife-today
 * @since 0.8.7
 */
class Meta_Boxes {

	/**
	 * Initialize the class
	 *
	 * @since 0.8.7
	 * @return void
	 */
	public function __construct() {

		// Add meta boxes.
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_postdata' ) );

	}

	/**
	 * Initialize the class
	 *
	 * @since 0.8.7
	 * @return void
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'agrilife_featured_post',
			__( 'Featured Post', 'agrilife-today' ),
			array( $this, 'inner_custom_box' ),
			'post',
			'side'
		);

	}

	/**
	 * Initialize the class
	 *
	 * @since 0.8.7
	 * @param object $post WP Post object.
	 * @return void
	 */
	public function inner_custom_box( $post ) {

		// Use nonce for verification.
		wp_nonce_field( plugin_basename( __FILE__ ), 'today_noncename' );

		// The actual fields for data entry.
		echo '<label for="agrilife_featured_post">';
		echo '</label> ';
		echo '<input type="checkbox" name="agrilife_featured_post" id="agrilife_featured_post" ', ( get_post_meta( $post->ID, 'featured-post', true ) === '1' ? ' checked="checked"' : '' ), ' />';
		echo ' <span>';
		esc_html_e( 'Feature this on archive pages', 'agrilife-today' );
		echo '</span>';

	}

	/**
	 * Initialize the class
	 *
	 * @since 0.8.7
	 * @param int $post_id Post ID.
	 * @return void
	 */
	public function save_postdata( $post_id ) {

		// Verify if this is an auto save routine.
		// If it is our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		/*
		Verify this came from the our screen and with proper authorization,
		because save_post can be triggered at other times.
		*/
		if (
			isset( $_POST['featured_post'], $_POST['featured_post_nonce'] )
			&& wp_verify_nonce( sanitize_key( $_POST['featured_post_nonce'] ) )
		) {

			// Check permissions.
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$featured = sanitize_key( wp_unslash( $_POST['featured_post'] ) );

			// OK, we're authenticated: we need to find and save the data.
			// Update the value.
			update_post_meta( $post_id, 'featured_post', ( 'on' === $featured ? 1 : 0 ) );

		}

	}
}
