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

		// Add user profile fields.
		add_action( 'show_user_profile', array( $this, 'extra_user_profile_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'extra_user_profile_fields' ) );
		add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields' ) );

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
			array( $this, 'featured_custom_box' ),
			'post',
			'side'
		);
	}

	/**
	 * Render the featured post field
	 *
	 * @since 0.8.7
	 * @param object $post WP Post object.
	 * @return void
	 */
	public function featured_custom_box( $post ) {

		// Use nonce for verification.
		wp_nonce_field( plugin_basename( __FILE__ ), 'featured-post_noncename' );

		// The actual fields for data entry.
		echo '<label for="featured-post">';
		echo '</label> ';
		echo '<input type="checkbox" name="featured-post" id="featured-post" ', ( intval( get_post_meta( $post->ID, 'featured-post', true ) ) === 1 ? ' checked="checked"' : '' ), ' />';
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
		// verify if this is an auto save routine.
		// If it is our form has not been submitted, so we dont want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times.
		if (
			isset( $_POST['featured-post_noncename'] )
			&& wp_verify_nonce( sanitize_key( $_POST['featured-post_noncename'] ), plugin_basename( __FILE__ ) )
		) {

			// Check permissions.
			if (
				isset( $_POST['post_type'] )
				&& 'post' === $_POST['post_type']
				&& current_user_can( 'edit_post', $post_id )
			) {

				// OK, we're authenticated: we need to find and save the data.
				// Update The Value.
				if ( isset( $_POST['featured-post'] ) && 'on' === $_POST['featured-post'] ) {
					$value = 1;
				} else {
					$value = 0;
				}

				update_post_meta( $post_id, 'featured-post', $value );

			}
		}
	}

	/**
	 * Show user profile fields on dashboard page.
	 *
	 * @since 1.2.0
	 * @param WP_User $user User object.
	 * @return void
	 */
	public function extra_user_profile_fields( $user ) {

		// Use nonce for verification.
		wp_nonce_field( plugin_basename( __FILE__ ), 'user-phone_noncename' );

		?>
		<h3>Extra profile information</h3>
		<table class="form-table">
		<tr>
		<th><label for="phone">Phone</label></th>
		<td>
		<input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" class="regular-text" /><br />
		<span class="description">Please enter your phone number.</span>
		</td>
		</tr>
		</table>

		<?php
	}

	/**
	 * Save user meta.
	 *
	 * @since 1.2.0
	 * @param int $user_id User ID.
	 * @return boolean
	 */
	public function save_extra_user_profile_fields( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {

			return false;

		}

		if (
			isset( $_POST['user-phone_noncename'] )
			&& wp_verify_nonce( sanitize_key( $_POST['user-phone_noncename'] ), plugin_basename( __FILE__ ) )
		) {

			if ( isset( $_POST['phone'] ) ) {

				$phone = sanitize_text_field( wp_unslash( $_POST['phone'] ) );

				update_user_meta( $user_id, 'phone', $phone );

			}
		}
	}
}
