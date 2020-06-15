<?php
/**
 * The file that defines a custom taxonomy
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/src/class-taxonomy.php
 * @since      1.4.0
 * @package    agrilife-today
 * @subpackage agrilife-today/src
 */

namespace AgToday;

/**
 * Builds and registers a custom taxonomy.
 *
 * @package agrilife-today
 * @since 1.4.0
 */
class Taxonomy {

	/**
	 * Taxonomy slug
	 *
	 * @since  1.4.0
	 * @access protected
	 * @var    slug $slug Stores taxonomy slug
	 */
	protected $slug;

	/**
	 * Post type slug this taxonomy is registered to
	 *
	 * @since  1.4.0
	 * @access protected
	 * @var    slug $post_slug Stores post type slug value
	 */
	protected $post_slug;

	/**
	 * Singular name this taxonomy is given
	 *
	 * @since  1.4.0
	 * @access protected
	 * @var    name $singular_name Stores taxonomy singular name
	 */
	protected $singular_name;

	/**
	 * Taxonomy meta boxes
	 *
	 * @since  1.4.0
	 * @access protected
	 * @var    meta $meta_boxes Stores taxonomy meta boxes
	 */
	protected $meta_boxes = array();

	/**
	 * Taxonomy template file path for the archive page
	 *
	 * @since  1.4.0
	 * @access protected
	 * @var    file $template Stores taxonomy archive template file path
	 */
	protected $template;

	/**
	 * Builds and registers the custom taxonomy.
	 *
	 * @param string $name The taxonomy name.
	 * @param string $slug The taxonomy slug.
	 * @param string $post_slug The slug of the post type where the taxonomy will be added.
	 * @param array  $user_args The arguments for taxonomy registration. Accepts $args from
	 *                         the WordPress core register_taxonomy function.
	 * @param array  $meta Array (single or multidimensional) of custom fields to add to a
	 *                    taxonomy item edit page. Requires 'name', 'slug', and 'type'.
	 * @param string $template The template file path for the taxonomy archive page.
	 * @return void
	 */
	public function __construct( $name, $slug, $post_slug = null, $user_args = array(), $meta = array(), $template = '' ) {

		$this->slug          = $slug;
		$this->post_slug     = $post_slug;
		$this->singular_name = $name;
		$singular            = $name;
		$plural              = $name . 's';

		// Taxonomy labels.
		$labels = array(
			'name'              => $plural,
			'singular_name'     => $singular,
			'search_items'      => __( 'Search', 'agrilife-today' ) . " $plural",
			'all_items'         => __( 'All', 'agrilife-today' ) . " $plural",
			'parent_item'       => __( 'Parent', 'agrilife-today' ) . " $singular",
			'parent_item_colon' => __( 'Parent', 'agrilife-today' ) . " {$singular}:",
			'edit_item'         => __( 'Edit', 'agrilife-today' ) . " $singular",
			'update_item'       => __( 'Update', 'agrilife-today' ) . " $singular",
			'add_new_item'      => __( 'Add New', 'agrilife-today' ) . " $singular",
			/* translators: placeholder is the singular taxonomy name */
			'new_item_name'     => sprintf( esc_html__( 'New %d Name', 'agrilife-today' ), $singular ),
			'menu_name'         => $plural,
		);

		// Taxonomy arguments.
		$args = array_merge(
			array(
				'labels'             => $labels,
				'show_ui'            => true,
				'show_admin_column'  => true,
				'rewrite'            => array( 'slug' => $slug ),
				'show_in_rest'       => true,
				'show_in_quick_edit' => true,
				'show_admin_column'  => true,
			),
			$user_args
		);

		// Register the Type taxonomy.
		register_taxonomy( $slug, $post_slug, $args );

		// Create taxonomy custom fields.
		// Ensure meta is an array of one or more arrays.
		if ( ! empty( $meta ) ) {
			if ( ! array_key_exists( 0, $meta ) ) {
				$this->meta_boxes[] = $meta;
			} else {
				foreach ( $meta as $key => $value ) {
					$this->meta_boxes[] = $value;
				}
			}
			$this->add_meta_box();
		}

		// Add custom template for post taxonomies.
		if ( ! empty( $template ) ) {
			$this->template = $template;
			add_filter( 'template_include', array( $this, 'custom_template' ) );
		}

		// Make taxonomy sortable.
		add_filter( "manage_edit-{$post_slug}_sortable_columns", array( $this, 'register_sortable_columns' ) );
		add_filter( 'posts_orderby', array( $this, 'taxonomy_orderby' ), 10, 2 );

		// Allow commas in taxonomy term by using '--' instead of ', ' upon creation.
		if ( ! is_admin() ) {

			add_filter( 'get_' . $post_slug, array( $this, 'comma_taxonomy_filter' ) );
			add_filter( 'get_the_taxonomies', array( $this, 'comma_taxonomies_filter' ) );
			add_filter( 'get_terms', array( $this, 'comma_taxonomies_filter' ) );
			add_filter( 'get_the_terms', array( $this, 'comma_taxonomies_filter' ) );

		}
	}

	/**
	 * Add actions to render and save custom fields
	 *
	 * @return void
	 */
	public function add_meta_box() {
		add_action( "{$this->slug}_edit_form_fields", array( $this, 'taxonomy_edit_meta_field' ), 10, 2 );
		add_action( "edited_{$this->slug}", array( $this, 'save_taxonomy_custom_meta' ), 10, 2 );
	}

	/**
	 * Render custom fields
	 *
	 * @param object $tag      Current taxonomy term object.
	 * @param string $taxonomy Current taxonomy slug.
	 * @return void
	 */
	public function taxonomy_edit_meta_field( $tag, $taxonomy ) {

		// put the term ID into a variable.
		$t_id = $tag->term_id;

		foreach ( $this->meta_boxes as $key => $meta ) {
			// Retrieve the existing value(s) for this meta field. This returns an array.
			$slug = $meta['slug'];

			?><tr class="form-field term-<?php echo esc_attr( $slug ); ?>-wrap">
				<th scope="row" valign="top"><label for="term_meta_<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $meta['name'] ); ?></label></th>
				<td>
					<?php

					// Make sure the form request comes from WordPress.
					wp_nonce_field( basename( __FILE__ ), "term_meta_{$slug}_nonce" );

					// Output the form field.
					switch ( $meta['type'] ) {
						case 'full':
							$term_meta = get_term_meta( $t_id, "term_meta_{$slug}", true );
							$value     = $term_meta ? stripslashes( $term_meta ) : '';
							$value     = html_entity_decode( $value );
							wp_editor(
								$value,
								'term_meta_' . $slug,
								array(
									'textarea_name' => 'term_meta_' . $slug,
									'wpautop'       => false,
								)
							);
							break;

						case 'link':
							$term_meta = get_term_meta( $t_id, "term_meta_{$slug}", true );
							$value     = $term_meta ? stripslashes( $term_meta ) : '';
							$value     = html_entity_decode( $value );
							$output    = "<input type=\"url\" name=\"term_meta_{$slug}\" id=\"term_meta_{$slug}\" value=\"{$value}\" placeholder=\"https://example.com\" pattern=\"http[s]?://.*\"><p class=\"description\"" . esc_html_e( 'Enter a value for this field', 'agrilife-degree-programs' ) . '</p>';
							echo wp_kses(
								$output,
								array(
									'input' => array(
										'type'        => array(),
										'name'        => array(),
										'id'          => array(),
										'value'       => array(),
										'placeholder' => array(),
										'pattern'     => array(),
									),
									'p'     => array(
										'class' => array(),
									),
								)
							);
							break;

						case 'checkbox':
							$term_meta = get_term_meta( $t_id, "term_meta_{$slug}" );
							$value     = ! empty( $term_meta ) && 'on' === $term_meta[0] ? 'checked' : '';
							$output    = "<input type=\"checkbox\" name=\"term_meta_{$slug}\" id=\"term_meta_{$slug}\" {$value}>";
							echo wp_kses(
								$output,
								array(
									'input' => array(
										'type'    => array(),
										'name'    => array(),
										'id'      => array(),
										'checked' => array(),
									),
								)
							);
							break;

						default:
							$term_meta = get_term_meta( $t_id, "term_meta_{$slug}", true );
							$value     = $term_meta ? stripslashes( $term_meta ) : '';
							$value     = html_entity_decode( $value );
							$output    = "<input type=\"text\" name=\"term_meta_{$slug}\" id=\"term_meta_{$slug}\" value=\"{$value}\"><p class=\"description\"" . esc_html_e( 'Enter a value for this field', 'agrilife-degree-programs' ) . '</p>';
							echo wp_kses(
								$output,
								array(
									'input' => array(
										'type'  => array(),
										'name'  => array(),
										'id'    => array(),
										'value' => array(),
									),
									'p'     => array(
										'class' => array(),
									),
								)
							);
							break;
					}

					?>
				</td>
			</tr>
			<?php
		}
	}

	/**
	 * Save custom fields
	 *
	 * @param int $term_id The term ID.
	 * @param int $tt_id   The term taxonomy ID.
	 * @return void
	 */
	public function save_taxonomy_custom_meta( $term_id, $tt_id ) {

		foreach ( $this->meta_boxes as $key => $meta ) {

			$slug = $meta['slug'];
			$key  = sanitize_key( "term_meta_$slug" );
			$nkey = isset( $_POST[ $key . '_nonce' ] ) ? sanitize_key( $_POST[ $key . '_nonce' ] ) : null;

			if (
				! isset( $nkey )
				|| ! wp_verify_nonce( $nkey, basename( __FILE__ ) )
			) {
				continue;
			}

			switch ( $meta['type'] ) {
				case 'checkbox':
					$value = isset( $_POST[ $key ] ) ? sanitize_key( wp_unslash( $_POST[ $key ] ) ) : null;
					break;
				default:
					$post_meta = wp_kses_post( wp_unslash( $_POST[ $key ] ) );
					$t_id      = $term_id;
					$value     = sanitize_text_field( htmlentities( $post_meta ) );
					break;
			}

			// Save the option array.
			update_term_meta( $term_id, $key, $value );

		}

	}

	/**
	 * Use custom template file if on the taxonomy archive page
	 *
	 * @param string $template The path of the template to include.
	 * @return string
	 */
	public function custom_template( $template ) {

		if ( is_tax( $this->slug ) ) {

			return $this->template;

		}

		return $template;
	}

	/**
	 * Make this taxonomy sortable from the post type dashboard list page.
	 *
	 * @param array $columns The list of taxonomy columns sortable on this post type's list page.
	 * @return array
	 */
	public function register_sortable_columns( $columns ) {

		$columns[ "taxonomy-{$this->slug}" ] = "taxonomy-{$this->slug}";

		return $columns;
	}


	/**
	 * Sort this taxonomy in the dashboard by the taxonomy text value.
	 *
	 * @param string $orderby The SQL query which orders posts.
	 * @param object $wp_query The query object.
	 * @return array
	 */
	public function taxonomy_orderby( $orderby, $wp_query ) {

		global $wpdb;

		// If this taxonomy is the orderby parameter, then update the SQL query.
		if ( isset( $wp_query->query['orderby'] ) && "taxonomy-{$this->slug}" === $wp_query->query['orderby'] ) {

			$orderby  = "(
	      SELECT GROUP_CONCAT(name ORDER BY name ASC)
	      FROM $wpdb->term_relationships
	      INNER JOIN $wpdb->term_taxonomy USING (term_taxonomy_id)
	      INNER JOIN $wpdb->terms USING (term_id)
	      WHERE $wpdb->posts.ID = object_id
	      AND taxonomy = '{$this->slug}'
	      GROUP BY object_id
	    ) ";
			$orderby .= ( 'ASC' === strtoupper( $wp_query->get( 'order' ) ) ) ? 'ASC' : 'DESC';

		}

		return $orderby;

	}

	/**
	 * Replace two hyphens with a comma and a space in taxonomy names.
	 *
	 * @param array $tag_arr Existing tag array.
	 * @return array
	 */
	public function comma_taxonomy_filter( $tag_arr ) {

		$tag_arr_new = $tag_arr;

		if ( $tag_arr->taxonomy === $this->slug && strpos( $tag_arr->name, '--' ) ) {

			$tag_arr_new->name = str_replace( '--', ', ', $tag_arr->name );

		}

		return $tag_arr_new;

	}

	/**
	 * Replace two hyphens in array of taxonomies with a comma and a space.
	 *
	 * @param array $tags_arr Array of tags.
	 * @return array
	 */
	public function comma_taxonomies_filter( $tags_arr ) {

		foreach ( $tags_arr as $key => $tag_arr ) {

			if (
				'object' === gettype( $tag_arr ) &&
				property_exists( $tag_arr, 'taxonomy' ) &&
				$this->slug === $tag_arr->taxonomy
			) {

				$tags_arr[ $key ] = $this->comma_taxonomy_filter( $tag_arr );
				break;

			}
		}

		return $tags_arr;

	}

}
