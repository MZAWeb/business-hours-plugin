<p>
	<small><?php echo sprintf( __( 'Go to the <a href="%s">settings</a> to setup your business hours.', 'business-hours' ), admin_url( "options-general.php?page=working-hours-settings" ) ); ?></small>
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( "Widget title", "business-hours" );?>
		:</label><br/>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
	       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $instance["title"] ); ?>"
	       type="text"/>
</p>
<p>
	<input type="checkbox"
	       id="<?php echo $this->get_field_id( 'allweek' ); ?>"
	       value="1" <?php checked( $instance["allweek"] == "1" );  ?>
	       name="<?php echo $this->get_field_name( 'allweek' ); ?>"/>
	<label
		for="<?php echo $this->get_field_id( 'allweek' ); ?>"><?php _e( "Show a table with the business hours for all weekdays", "business-hours" );?> </label>
</p>
<p>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'collapsible' ); ?>"
	       value="1" <?php checked( $instance["collapsible"] == "1" );  ?>
	       name="<?php echo $this->get_field_name( 'collapsible' ); ?>"/>
	<label
		for="<?php echo $this->get_field_id( 'collapsible' ); ?>"><?php _e( "Make the list collapsible", "business-hours" );?> </label>

</p>
<p>
	<a href='#'
	   class="business_hours_collapsible_handler"><?php _e( 'Toggle templating options', 'business-hours' );?></a>
</p>
<div class="business_hours_collapsible">
	<p>
		<label
			for="<?php echo $this->get_field_id( 'template_today' ); ?>"><?php _e( "Template for heading", "business-hours" );?>
			:</label><br/>
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'template_today' ); ?>"
		          name="<?php echo $this->get_field_name( 'template_today' ); ?>" type="text"
		          rows="6"><?php echo esc_textarea( $instance["template_today"] ); ?></textarea>
		<small>The tag {{Day}} will be replaced with the weekday name.</small>
	</p>
	<p>
		<label
			for="<?php echo $this->get_field_id( 'template_hours' ); ?>"><?php _e( "Template for working hours", "business-hours" );?>
			:</label><br/>
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'template_hours' ); ?>"
		          name="<?php echo $this->get_field_name( 'template_hours' ); ?>" type="text"
		          rows="6"><?php echo esc_textarea( $instance["template_hours"] ); ?></textarea>
		<small><?php _e( "The tags {{Open}} and {{Close}} will be replaced with the correct values.", "business-hours" );?></small>
	</p>
	<p>
		<label
			for="<?php echo $this->get_field_id( 'template_closed' ); ?>"><?php _e( "Template for \"closed\" text", "business-hours" );?>
			:</label><br/>
		<textarea class="widefat" id="<?php echo $this->get_field_id( 'template_closed' ); ?>"
		          name="<?php echo $this->get_field_name( 'template_closed' ); ?>" type="text"
		          rows="6"><?php echo esc_textarea( $instance["template_closed"] ); ?></textarea>
	</p>
</div>