<?php
/**
 * The file that creates custom widgets.
 *
 * @link       https://github.com/AgriLife/agrilife-today/blob/master/src/class-widgets.php
 * @since      0.1.5
 * @package    agrilife-today
 * @subpackage agrilife-today/src
 */

/**
 * Loads theme widgets
 *
 * @package agrilife-today
 * @since 0.1.5
 */
class Widget_Subscribe extends WP_Widget {

	/**
	 * Default instance.
	 *
	 * @since 0.1.5
	 * @var array
	 */
	protected $default_instance = array(
		'title'   => 'Don\'t Miss A Story',
		'content' => '<form class="subscribe" action="https://feedburner.google.com/fb/a/mailverify" method="post" onsubmit="window.open(\'https://feedburner.google.com/fb/a/mailverify?uri=AgrilifeToday\', \'popupwindow\', \'scrollbars=yes,width=550,height=520\');return true" target="popupwindow"><input value="e.g. you@gmail.com" type="email" name="email" /> <input type="hidden" name="uri" value="AgrilifeToday" /> <input type="hidden" name="loc" value="en_US" /> <span class="submit-wrap"><input type="submit" value="Subscribe" /></span></form>',
	);

	/**
	 * Construct the widget
	 *
	 * @since 0.1.5
	 * @return void
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'                   => 'agt-sub',
			'description'                 => __( 'A subscription form for AgriLife Today' ),
			'customize_selective_refresh' => true,
		);

		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'agt_subscribe', __( 'Subscribe to AgriLife Today' ), $widget_ops, $control_ops );

	}

	/**
	 * Echoes the widget content
	 *
	 * @since 0.1.5
	 * @param array $args Display arguments including 'before_title', 'after_title', 'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$instance = array_merge( $this->default_instance, $instance );
		$title    = $instance['title'];
		$content  = $instance['content'];

		$title = '<div class="title-wrap cell medium-12 small-4-collapse-half small-collapse-left">' . $args['before_title'] . $title . $args['after_title'] . '</div>';

		$args['before_widget'] = str_replace( 'class="widget-wrap', 'class="grid-x widget-wrap', $args['before_widget'] );

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $title );
		}
		echo '<div class="textwidget custom-html-widget cell medium-12">'; // The textwidget class is for theme styling compatibility.
		echo wp_kses(
			$content,
			array(
				'form'  => array(
					'class'    => array(),
					'action'   => array(),
					'method'   => array(),
					'onsubmit' => array(),
					'target'   => array(),
				),
				'label' => array(),
				'input' => array(
					'value' => array(),
					'type'  => array(),
					'name'  => array(),
				),
			)
		);
		echo '</div>';
		echo wp_kses_post( $args['after_widget'] );

	}

	/**
	 * Outputs the settings update form
	 *
	 * @since 0.1.5
	 * @param array $instance Current settings.
	 * @return void
	 */
	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, $this->default_instance );

		$output = '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" class="title widefat" value="%s"/></p><p><textarea id="%s" rows="8" name="%s" class="content widefat">%s</textarea></p>';

		echo wp_kses(
			sprintf(
				$output,
				esc_attr( $this->get_field_id( 'title' ) ),
				esc_attr_e( 'Title:', 'agrilife-today' ),
				esc_attr( $this->get_field_id( 'title' ) ),
				$this->get_field_name( 'title' ),
				esc_attr( $instance['title'] ),
				$this->get_field_id( 'content' ),
				$this->get_field_name( 'content' ),
				esc_textarea( $instance['content'] )
			),
			array(
				'p'        => array(),
				'label'    => array(
					'for' => array(),
				),
				'input'    => array(
					'type'  => array(),
					'id'    => array(),
					'name'  => array(),
					'class' => array(),
					'value' => array(),
				),
				'textarea' => array(
					'id'    => array(),
					'rows'  => array(),
					'name'  => array(),
					'class' => array(),
				),
			)
		);

	}

	/**
	 * Updates a particular instance of a widget
	 *
	 * @since 0.1.5
	 * @param array $new_instance New settings for this instance as input by the user via WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance          = array_merge( $this->default_instance, $old_instance );
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['content'] = $new_instance['content'];
		} else {
			$instance['content'] = wp_kses_post( $new_instance['content'] );
		}
		return $instance;

	}
}
