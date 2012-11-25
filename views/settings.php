<div class="wrap">
	<div id="icon-options-business-hours" class="icon32"><br></div>
	<h2><?php _e( 'Business Hours', 'business-hours' ) ?></h2>
	<br/>

	<div class="bh_main_container">
		<form id="bh-form" class="bh-form" action="" method="post">

			<input type='hidden' name='page' value='<?php echo BusinessHours::SLUG; ?>'/>
			<input type="hidden" name="action" value="update"/>

			<?php wp_nonce_field( BusinessHours::SLUG, 'bh_nonce' ); ?>

			<table class="form-table">
				<tr>
					<td>
						<div>
							<h3><?php _e( 'Business hours for each day of the week', 'business-hours' );?></h3>

							<p><?php _e( "Leave the fields empty for the days your business is closed.", 'business-hours' );?></p>
						</div>
						<div>
							<table id='business_hours_days_table'>
								<thead>
								<tr>
									<td><?php _e( 'Day', 'business-hours' ) ?></td>
									<td><?php _e( 'Open', 'business-hours' ) ?></td>
									<td><?php _e( 'Close', 'business-hours' ) ?></td>
								</tr>
								</thead>
								<tbody>
								<?php $this->_show_days_controls(); ?>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</table>
			<table class="form-table">
				<tr>
					<td>
						<div>
							<h3><?php _e( 'Exceptions', 'business-hours' );?></h3>

							<p>
								<?php _e( "Exceptions allow you to set different business hours for specific days (ie: Holidays, Vacations, etc).", 'business-hours' );?>
								<br/>
								<?php _e( "<b>Instructions:</b>", 'business-hours' );?><br/>
								<?php _e( "1) Select a day, month and / or year (i.e. To add an exception for every March 1st select day 1, month March and leave the year empty).", 'business-hours' );?>
								<br/>
								<?php _e( "2) Type the open and close hours for this exception. Leave empty if your business remains closed during this exception.", 'business-hours' );?>
								<br/>
								<?php _e( "3) If you want to add more exceptions, click the 'Add more exceptions' button and repeat this process in the new added row.", 'business-hours' );?>
								<br/>
								<?php _e( "Need help setting exceptions? <a href='https://github.com/MZAWeb/business-hours-plugin/issues'>Open a ticket in GitHub</a>", 'business-hours' );?>

							</p>
						</div>
						<div id="exceptions_wrapper">
							<div class="exception_date" id="exception_1">
								<label for="exception_day"><?php _e( 'Day:', 'business-hours' );?>
									<select name="exception_day" id='exception_day'>
										<?php $this->_show_exception_days(); ?>
									</select>
								</label>
								<label for="exception_month"><?php _e( 'Month:', 'business-hours' );?>
									<select name="exception_month" id='exception_month'>
										<?php $this->_show_exception_months(); ?>
									</select>
								</label>
								<label for="exception_month"><?php _e( 'Year:', 'business-hours' );?>
									<select name="exception_year" id='exception_year'>
										<?php $this->_show_exception_years(); ?>
									</select>
								</label>
								<label for="exception_open"><?php _e( 'Open:', 'business-hours' );?>
									<input name="exception_open" id="exception_open"
									       class="business_hours_exception_hour_field" type="text" value=""/>
								</label>

								<label for="exception_close"><?php _e( 'Close:', 'business-hours' );?>
									<input name="exception_close" id="exception_close"
									       class="business_hours_exception_hour_field" type="text" value=""/>
								</label>

								<label class="exception_remove_label" for="exception_remove">&nbsp;
									<input type="button" name="exception_remove" class="exception_remove bh-button"
									       data-id='1' value="<?php _e( 'Remove', 'business-hours' );?>"/>
								</label>

								<div class="bh_clear"></div>
							</div>
						</div>
						<label for="exception_add">&nbsp;
							<input type="button" name="exception_add" class="bh-button" id="exception_add"
							       value="<?php _e( 'Add another exception', 'business-hours' );?>"/>
						</label>

						<div class="bh_clear"></div>
					</td>
				</tr>
			</table>
			<table class="form-table">
				<tr>
					<td>

						<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
						                         value="<?php _e( 'Save Changes' );?>"></p>

						<div class="bh_clear"></div>
					</td>
				</tr>
			</table>
		</form>
	</div>

	<div class="bh_support_container">
		<?php $this->_show_support_form(); ?>
	</div>

	<div class="bh_clear"></div>


</div>
