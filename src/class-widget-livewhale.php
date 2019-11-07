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

		$instance  = array_merge( $this->default_instance, $instance );
		$title     = $instance['title'];
		$direction = array_key_exists( 'direction', $instance ) ? $instance['direction'] : 'vertical';

		if ( 'horizontal' === $direction ) {

			$args['before_widget'] = str_replace( 'class="widget-wrap', 'class="widget-wrap grid-container', $args['before_widget'] );
			$args['before_title']  = str_replace( 'widget-title', 'widget-title cell shrink', $args['before_title'] );
			$args['before_title']  = '<div class="grid-x"><div class="cell auto title-line"></div>' . $args['before_title'];
			$args['after_title']   = $args['after_title'] . '<div class="cell auto title-line"></div></div>';

		}

		$title = '<div class="title-wrap heading-sideline">' . $args['before_title'] . $title . $args['after_title'] . '</div>';

		echo wp_kses_post( $args['before_widget'] );
		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $title );
		}

		// Begin events list.
		$grid   = 'horizontal' === $direction ? ' grid-x' : '';
		$cell   = 'horizontal' === $direction ? ' cell medium-auto small-12' : '';
		$output = "<div class=\"textwidget custom-html-widget {$direction}{$grid}\">";

		// Output LiveWhale events.
		$feed_json = wp_remote_get( 'https://calendar.tamu.edu/live/json/events/group/Texas%20A%26amp%3BM%20AgriLife/only_starred/true/max/3/hide_repeats/true/' );

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

				$output .= sprintf(
					'<div class="event%s"><div class="grid-x"><div class="cell date shrink"><div class="month h3">%s</div><div class="h2 day">%s</div></div><div class="cell title auto"><a href="%s" title="%s" class="event-title medium-truncate-lines medium-truncate-2-lines">%s</a><div class="location medium-truncate-lines medium-truncate-1-line">%s</div></div></div></div>',
					$cell,
					$date_month,
					$date_day,
					$url,
					$title,
					$title,
					$location
				);

			}

			$output .= '<div class="events-all cell medium-shrink small-12"><a class="button gradient" href="http://calendar.tamu.edu/agrilife/" target="_blank"><span class="h3">All Events</span></a></div>';
		}

		// Close the widget.
		$output .= '</div>';

		echo wp_kses_post(
			$output
		);

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

		$title_output = '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" class="title widefat" value="%s"/></p>';

		echo wp_kses(
			sprintf(
				$title_output,
				esc_attr( $this->get_field_id( 'title' ) ),
				esc_attr( 'Title:', 'agrilife-today' ),
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

		$direction_output = '<p><label for="%s">%s</label><select id="%s" name="%s" class="direction widefat"><option value="vertical"%s>Vertical</option><option value="horizontal"%s>Horizontal</option></select></p>';

		if ( array_key_exists( 'direction', $instance ) ) {

			$is_vertical   = 'vertical' === $instance['direction'] ? ' selected' : '';
			$is_horizontal = 'horizontal' === $instance['direction'] ? ' selected' : '';

		} else {

			$is_vertical   = ' selected';
			$is_horizontal = '';

		}

		echo wp_kses(
			sprintf(
				$direction_output,
				esc_attr( $this->get_field_id( 'direction' ) ),
				esc_attr( 'Direction:', 'agrilife-today' ),
				esc_attr( $this->get_field_id( 'direction' ) ),
				$this->get_field_name( 'direction' ),
				$is_vertical,
				$is_horizontal
			),
			array(
				'p'      => array(),
				'label'  => array(
					'for' => 1,
				),
				'select' => array(
					'id'    => 1,
					'name'  => 1,
					'class' => 1,
					'value' => 1,
				),
				'option' => array(
					'value'    => 1,
					'selected' => 1,
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

		$instance              = array_merge( $this->default_instance, $old_instance );
		$instance['title']     = sanitize_text_field( $new_instance['title'] );
		$instance['direction'] = sanitize_text_field( $new_instance['direction'] );
		return $instance;

	}
}
