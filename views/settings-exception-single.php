<div class="exception_date" id="exception_<?php echo $exception_number;?>">
	<label for="exception_day"><?php _e( 'Day:', 'business-hours' );?>
		<select name="exception_day[]" id='exception_day'>
			<?php $this->_show_exception_days(); ?>
		</select>
	</label>
	<label for="exception_month"><?php _e( 'Month:', 'business-hours' );?>
		<select name="exception_month[]" id='exception_month'>
			<?php $this->_show_exception_months(); ?>
		</select>
	</label>
	<label for="exception_month"><?php _e( 'Year:', 'business-hours' );?>
		<select name="exception_year[]" id='exception_year'>
			<?php $this->_show_exception_years(); ?>
		</select>
	</label>
	<label for="exception_open"><?php _e( 'Open:', 'business-hours' );?>
		<input name="exception_open[]" id="exception_open"
		       class="business_hours_exception_hour_field" type="text" value=""/>
	</label>

	<label for="exception_close"><?php _e( 'Close:', 'business-hours' );?>
		<input name="exception_close[]" id="exception_close"
		       class="business_hours_exception_hour_field" type="text" value=""/>
	</label>

	<label class="exception_remove_label" for="exception_remove">&nbsp;
		<input type="button" name="exception_remove" class="exception_remove bh-button"
		       data-id='1' value="<?php _e( 'Remove', 'business-hours' );?>"/>
	</label>

	<input type="hidden" name="exception_number" class="exception_number" value="<?php echo $exception_number;?>"/>

	<div class="bh_clear"></div>
</div>