<?php


class BusinessHoursWidget extends WP_Widget {

	function BusinessHoursWidget() {

		$widget_ops = array( 'classname'   => 'workinghourswidget',
		                     'description' => __( 'Shows your business hours by day', "business-hours" ) );

		$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'workinghourswidget' );

		$this->WP_Widget( 'workinghourswidget', __( 'Business Hours by Day', "business-hours" ), $widget_ops, $control_ops );

		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'scripts' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'scripts' ) );
		}

	}

	function scripts() {
		business_hours()->enqueue_resources();
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = esc_html( $instance['title'] );

		$day = business_hours()->get_day_using_timezone();

		$id   = key( $day );
		$name = $day[$id];

		$open          = esc_html( business_hours()->settings()->get_open_hour( $id ) );
		$close         = esc_html( business_hours()->settings()->get_close_hour( $id ) );
		$is_open_today = business_hours()->settings()->is_open( $id );

		$exceptions = BusinessHoursExceptions::instance()->get_exceptions_for_day_id( $id );
		if ( !empty( $exceptions ) ) {
			$open          = $exceptions['open'];
			$close         = $exceptions['close'];
			$is_open_today = !empty( $open ) && !empty( $close );
			$name          = BusinessHoursExceptions::instance()->get_localized_date_for_day_id( $id );
		}

		echo $before_widget;
		echo $before_title . $title . $after_title;

		if ( $is_open_today ) {
			$template = $instance['template_hours'];
			$template = str_replace( "{{Open}}", $open, $template );
			$template = str_replace( "{{Close}}", $close, $template );
		} else {
			$template = $instance['template_closed'];
		}

		if ( $instance['template_today'] != "" ) {
			$today    = str_replace( "{{Day}}", $name, $instance['template_today'] );
			$template = $today . $template;
		}

		echo $template;

		// To catch instances saved in old versions of the plugin
		$instance['collapsible'] = isset( $instance['collapsible'] ) ? $instance['collapsible'] : '1';

		if ( $instance['allweek'] === "1" )
			business_hours()->show_table( ( $instance['collapsible'] === '1' ) );


		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']           = $new_instance['title'];
		$instance['template_today']  = $new_instance['template_today'];
		$instance['template_hours']  = $new_instance['template_hours'];
		$instance['template_closed'] = $new_instance['template_closed'];
		$instance['allweek']         = isset( $new_instance['allweek'] ) ? '1' : '';
		$instance['collapsible']     = isset( $new_instance['collapsible'] ) ? '1' : '';

		return $instance;

	}

	function form( $instance ) {

		$closed_text = business_hours()->settings()->get_default_closed_text();

		$defaults = array( 'title'           => "Business Hours",
		                   'template_today'  => "<div class='working_hours_title'>" . __( "Business hours on", "business-hours" ) . " {{Day}}</div>",
		                   'template_hours'  => "<span class='working_hours_open'>{{Open}}</span> - <span class='working_hours_close'>{{Close}}</span>",
		                   'template_closed' => "<div class='working_hours_closed'>" . $closed_text . "</div>",
		                   'allweek'         => 0,
		                   'collapsible'     => 1 );

		$instance = wp_parse_args( (array)$instance, $defaults );

		include business_hours()->locate_view( 'widget-admin.php', false );

	}
}