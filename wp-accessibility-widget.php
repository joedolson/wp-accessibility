<?php

add_action( 'widgets_init', create_function( '', 'return register_widget("wp_accessibility_toolbar");' ) );
class wp_accessibility_toolbar extends WP_Widget {
	function __construct() {
		parent::__construct( false, $name = __( 'Accessibility Toolbar', 'wp-accessibility' ), array( 'customize_selective_refresh' => true ) );
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', ( empty( $instance['title'] ) ? false : $instance['title'] ), $instance, $args );
		echo $before_widget;
		echo ( $title ) ? $before_title . $title . $after_title : '';		
		echo wpa_toolbar_html();
		echo $after_widget;
	}

	function form( $instance ) {
		$title = ( isset( $instance['title'] ) ) ? esc_attr( $instance['title'] ) : '';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wp-accessibility' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php esc_attr_e( $title ); ?>"/>
		</p>
	<?php		
	}

	function update( $new_instance, $old_instance ) {
		$instance           = $old_instance;
		$instance['title']  = strip_tags( $new_instance['title'] );

		return $instance;		
	}
}