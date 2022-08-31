<?php
/**
 * WP Accessibility toolbar widget
 *
 * @category Widgets
 * @package  WP Accessibility
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/wp-accessibility/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Accessibility toolbar widget class.
 *
 * @category  Widgets
 * @package   WP Accessibility
 * @author    Joe Dolson
 * @copyright 2012
 * @license   GPLv2 or later
 * @version   1.0
 */
class Wp_Accessibility_Toolbar extends WP_Widget {
	/**
	 * Construct widget.
	 */
	function __construct() {
		parent::__construct(
			false,
			$name = __( 'Accessibility Toolbar', 'wp-accessibility' ),
			array( 'customize_selective_refresh' => true )
		);
	}

	/**
	 * Widget output.
	 *
	 * @param array $args Theme widget arguments.
	 * @param array $instance Widget settings.
	 */
	function widget( $args, $instance ) {
		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];

		$title = apply_filters( 'widget_title', ( empty( $instance['title'] ) ? false : $instance['title'] ), $instance, $args );
		echo $before_widget;
		echo ( $title ) ? wp_kses_post( $before_title . $title . $after_title ) : '';
		echo wpa_toolbar_html();
		echo $after_widget;
	}

	/**
	 * Form to construct widget settings.
	 *
	 * @param array $instance Current widget settings.
	 */
	function form( $instance ) {
		$title = ( isset( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-accessibility' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"/>
		</p>
		<?php
	}

	/**
	 * Update widget settings.
	 *
	 * @param array $new_instance New settings.
	 * @param array $old_instance Old settings.
	 *
	 * @return array updated settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}
}
