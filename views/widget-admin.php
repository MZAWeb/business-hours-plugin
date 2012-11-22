<p>
	<small>Click <a href="<?php echo admin_url( "options-general.php?page=working-hours-settings" ); ?>">here</a> to
		setup your business hours or get support.
	</small>
</p>
<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( "Title", "business-hours" );?>:</label><br/>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
	       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance["title"]; ?>"
	       type="text"/>
</p>

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
	<small><?php _e( "The tags {{Open}} and {{Close}} will be replaced with the correct value.", "business-hours" );?></small>
</p>
<p>
	<label
		for="<?php echo $this->get_field_id( 'template_closed' ); ?>"><?php _e( "Template for \"closed\" text", "business-hours" );?>
		:</label><br/>
	<textarea class="widefat" id="<?php echo $this->get_field_id( 'template_closed' ); ?>"
	          name="<?php echo $this->get_field_name( 'template_closed' ); ?>" type="text"
	          rows="6"><?php echo esc_textarea( $instance["template_closed"] ); ?></textarea>
</p>

<p>
	<label
		for="<?php echo $this->get_field_id( 'allweek' ); ?>"><?php _e( "Show also a collapsible list with the business hours for each weekday:", "business-hours" );?> </label>
	<input type="checkbox" id="<?php echo $this->get_field_id( 'allweek' ); ?>"
	       value="1" <?php checked( $instance["allweek"] == "1" );  ?>
	       name="<?php echo $this->get_field_name( 'allweek' ); ?>"/>
</p>