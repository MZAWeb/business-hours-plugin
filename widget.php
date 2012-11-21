<?php

add_action( 'widgets_init', 'WorkingHoursWidget_load' );
function WorkingHoursWidget_load() {
    register_widget( 'WorkingHoursWidget' );
}

class WorkingHoursWidget extends WP_Widget{

    	function WorkingHoursWidget() {

            $widget_ops = array( 'classname' => 'workinghourswidget', 'description' => __('Shows your business hours by day',"business-hours") );

            $control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'workinghourswidget' );

            $this->WP_Widget( 'workinghourswidget', __('Business Hours by Day', "business-hours"), $widget_ops, $control_ops );

	        if ( is_active_widget(false, false, $this->id_base) ){
                add_action( 'wp_enqueue_scripts', array(&$this, 'scripts') );
            }

	    }


	    function scripts(){
	        wp_enqueue_script( 'jquery' );

            wp_register_script('BusinessHoursScript', plugins_url('script.js', __FILE__), array('jquery'));
            wp_enqueue_script('BusinessHoursScript');

	        wp_register_style('BusinessHoursStyle', plugins_url('style.css', __FILE__));
	        wp_enqueue_style('BusinessHoursStyle');
	    }


	    function widget( $args, $instance ) {
            extract( $args );

            global $workinghours;

            $title = $instance['title'];


            $day = $workinghours->get_day_using_timezone() ;

			$id = key($day);
            $name = $day[$id];

            $open = $workinghours->settings->get_setting($id,"open");
            $close = $workinghours->settings->get_setting($id,"close");
            $working = $workinghours->settings->get_setting($id,"working");

            echo $before_widget;
            echo $before_title . $title . $after_title;

            if ($working == "true"){
                $template = $instance['template_hours'];
                $template = str_replace("{{Open}}", $open, $template);
                $template = str_replace("{{Close}}", $close, $template);
            } else {
                 $template = $instance['template_closed'];
            }

            if ($instance['template_today'] != ""){
                $today = str_replace("{{Day}}", $name, $instance['template_today']);
                $template = $today.$template;
            }

            echo $template;

            if ($instance['allweek'] == "1"){
                
                $workinghours->show_table();

            }

            echo $after_widget;
            
        }

        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;

            $instance['title'] = $new_instance['title'];
            $instance['template_today'] = $new_instance['template_today'];
            $instance['template_hours'] = $new_instance['template_hours'];
            $instance['template_closed'] = $new_instance['template_closed'];
            $instance['allweek'] = $new_instance['allweek'];

            return $instance;

        }

        function form( $instance ) {
                  
            $defaults = array(
                'title' => "Business Hours",
                'template_today' => "<div class='working_hours_title'>" . __("Business hours on", "business-hours" ) . " {{Day}}</div>",
                'template_hours' => "<span class='working_hours_open'>{{Open}}</span> - <span class='working_hours_close'>{{Close}}</span>",
                'template_closed' => "<div class='working_hours_closed'>" . __("Closed", "business-hours" ) . "</div>",
                'allweek' => 0
            );

            $instance = wp_parse_args( (array) $instance, $defaults );
            ?>
            <p>
                <small>Click <a href="<?php echo admin_url("options-general.php?page=working-hours-settings"); ?>">here</a> to setup your business hours or get support.</small>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e("Title", "business-hours" );?>:</label><br/>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance["title"]; ?>" type="text" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'template_today' ); ?>"><?php _e("Template for heading", "business-hours" );?>:</label><br/>
                <textarea class="widefat" id="<?php echo $this->get_field_id( 'template_today' ); ?>" name="<?php echo $this->get_field_name( 'template_today' ); ?>" type="text" rows="6" ><?php echo esc_textarea($instance["template_today"]); ?></textarea>
                <small>The tag {{Day}} will be replaced with the weekday name.</small>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'template_hours' ); ?>"><?php _e("Template for working hours", "business-hours" );?>:</label><br/>
                <textarea class="widefat" id="<?php echo $this->get_field_id( 'template_hours' ); ?>" name="<?php echo $this->get_field_name( 'template_hours' ); ?>" type="text" rows="6" ><?php echo esc_textarea($instance["template_hours"]); ?></textarea>
                <small><?php _e("The tags {{Open}} and {{Close}} will be replaced with the correct value.", "business-hours" );?></small>
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'template_closed' ); ?>"><?php _e("Template for \"closed\" text", "business-hours" );?> :</label><br/>
                <textarea class="widefat" id="<?php echo $this->get_field_id( 'template_closed' ); ?>" name="<?php echo $this->get_field_name( 'template_closed' ); ?>" type="text" rows="6" ><?php echo esc_textarea($instance["template_closed"]); ?></textarea>
            </p>

             <p>
                <label for="<?php echo $this->get_field_id( 'allweek' ); ?>"><?php _e("Show also a collapsible list with the business hours for each weekday:", "business-hours" );?> </label>
                <input type="checkbox" id="<?php echo $this->get_field_id( 'allweek' ); ?>" value="1" <?php checked($instance["allweek"] == "1");  ?> name="<?php echo $this->get_field_name( 'allweek' ); ?>"/>
             </p>



        	<?php
	    }
    }

?>