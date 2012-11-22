<?php

add_action( 'widgets_init', 'WorkingHoursWidget_load' );
function WorkingHoursWidget_load() {
	register_widget( 'WorkingHoursWidget' );
}


class WorkingHoursWidget extends WP_Widget {

	function WorkingHoursWidget() {

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
		wp_enqueue_script( 'jquery' );

		wp_register_script( 'BusinessHoursScript', plugins_url( 'script.js', __FILE__ ), array( 'jquery' ) );
		wp_enqueue_script( 'BusinessHoursScript' );

		wp_register_style( 'BusinessHoursStyle', plugins_url( 'style.css', __FILE__ ) );
		wp_enqueue_style( 'BusinessHoursStyle' );
	}


	function widget( $args, $instance ) {
		extract( $args );

		global $workinghours;

		$title = $instance['title'];


		$day = business_hours()->get_day_using_timezone();

		$id   = key( $day );
		$name = $day[$id];

		$open    = business_hours()->settings->get_setting( $id, "open" );
		$close   = business_hours()->settings->get_setting( $id, "close" );
		$working = business_hours()->settings->get_setting( $id, "working" );

		echo $before_widget;
		echo $before_title . $title . $after_title;

		if ( $working == "true" ) {
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

		if ( $instance['allweek'] == "1" ) {

			business_hours()->show_table();

		}

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title']           = $new_instance['title'];
		$instance['template_today']  = $new_instance['template_today'];
		$instance['template_hours']  = $new_instance['template_hours'];
		$instance['template_closed'] = $new_instance['template_closed'];
		$instance['allweek']         = $new_instance['allweek'];

		return $instance;

	}

	function form( $instance ) {

		$defaults = array( 'title'           => "Business Hours",
		                   'template_today'  => "<div class='working_hours_title'>" . __( "Business hours on", "business-hours" ) . " {{Day}}</div>",
		                   'template_hours'  => "<span class='working_hours_open'>{{Open}}</span> - <span class='working_hours_close'>{{Close}}</span>",
		                   'template_closed' => "<div class='working_hours_closed'>" . __( "Closed", "business-hours" ) . "</div>",
		                   'allweek'         => 0 );

		$instance = wp_parse_args( (array)$instance, $defaults );
		$pepe     = "lalalaal";
		include business_hours()->locate_view( 'widget-admin.php' );

	}
}