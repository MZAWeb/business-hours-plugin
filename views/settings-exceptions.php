<table class="form-table">
	<tr>
		<td>
			<div>
				<h3><?php _e( 'Exceptions', 'business-hours' );?></h3>

				<p>
					<?php _e( "Exceptions allow you to set different business hours for specific days (ie: Holidays, Vacations, etc).", 'business-hours' );?>
					<br/>
					<?php $this->_show_exceptions_instructions(); ?>

				</p>
			</div>
			<div id="exceptions_wrapper">
				<?php $this->_show_exceptions(); ?>
			</div>
			<label for="exception_add">&nbsp;
				<input type="button" name="exception_add" class="bh-button" id="exception_add"
				       value="<?php _e( 'Add exception', 'business-hours' );?>"/>
			</label>

			<div class="bh_clear"></div>
		</td>
	</tr>
</table>