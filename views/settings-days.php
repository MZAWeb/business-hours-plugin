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