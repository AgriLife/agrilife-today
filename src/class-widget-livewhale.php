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
class Widget_LiveWhale extends WP_Widget {

	/**
	 * Default instance.
	 *
	 * @since 0.1.5
	 * @var array
	 */
	protected $default_instance = array(
		'title' => 'Events',
	);

	/**
	 * Construct the widget
	 *
	 * @since 0.1.5
	 * @return void
	 */
	public function __construct() {

		$widget_ops = array(
			'classname'                   => 'agt-livewhale',
			'description'                 => __( 'Show LiveWhale events' ),
			'customize_selective_refresh' => true,
		);

		$control_ops = array(
			'width'  => 400,
			'height' => 350,
		);
		parent::__construct( 'agt_livewhale', __( 'LiveWhale' ), $widget_ops, $control_ops );

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

		$title = '<div class="title-wrap">' . $args['before_title'] . $title . $args['after_title'] . '</div>';

		$args['before_widget'] = str_replace( 'class="widget-wrap', 'class="widget-wrap', $args['before_widget'] );

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $title );
		}
		echo '<div class="textwidget custom-html-widget">'; // The textwidget class is for theme styling compatibility.

		// Output LiveWhale events.
		$feed_json = wp_remote_get( 'https://calendar.tamu.edu/live/json/events/group/Texas%20A%26amp%3BM%20AgriLife/only_starred/true/' );

		if ( is_array( $feed_json ) && array_key_exists( 'body', $feed_json ) ) {
			$feed_array   = json_decode( $feed_json['body'], true );
			$l_events     = array_slice( $feed_array, 0, 3 ); // Choose number of events.
			$l_event_list = '';

			foreach ( $l_events as $event ) {

				$title      = $event['title'];
				$url        = $event['url'];
				$location   = $event['location'];
				$date       = $event['date_utc'];
				$time       = $event['date_time'];
				$date       = date_create( $date );
				$date_day   = date_format( $date, 'd' );
				$date_month = date_format( $date, 'M' );

				if ( array_key_exists( 'custom_room_number', $event ) && ! empty( $event['custom_room_number'] ) ) {

					$location = $event['custom_room_number'];

				}

				$l_event_list .= sprintf(
					'<div class="event"><div class="grid-x "><div class="cell date shrink"><div class="month h3">%s</div><div class="h2 day">%s</div></div><div class="cell title auto"><a href="%s" title="%s" class="event-title medium-truncate-lines medium-truncate-2-lines">%s</a><div class="location medium-truncate-lines medium-truncate-1-line">%s</div></div></div></div>',
					$date_month,
					$date_day,
					$url,
					$title,
					$title,
					$location
				);

			}

			echo wp_kses_post(
				sprintf(
					'%s<div class="events-all cell medium-shrink small-12"><a class="button gradient" href="http://calendar.tamu.edu/agrilife/" target="_blank"><span class="h3">All Events</span></a></div>',
					$l_event_list
				)
			);
		}

		// Close the widget.
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

		$output = '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" class="title widefat" value="%s"/></p>';

		echo wp_kses(
			sprintf(
				$output,
				esc_attr( $this->get_field_id( 'title' ) ),
				esc_attr_e( 'Title:', 'agrilife-today' ),
				esc_attr( $this->get_field_id( 'title' ) ),
				$this->get_field_name( 'title' ),
				esc_attr( $instance['title'] )
			),
			array(
				'p'     => array(),
				'label' => array(
					'for' => array(),
				),
				'input' => array(
					'type'  => array(),
					'id'    => array(),
					'name'  => array(),
					'class' => array(),
					'value' => array(),
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
		return $instance;

	}
}
