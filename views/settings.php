<div class="wrap">
	<div id="icon-options-business-hours" class="icon32"><br></div>
	<h2><?php _e( 'Business Hours', 'business-hours' ) ?></h2>
	<br/>

	<form id="bh-form" class="bh-form" action="" method="post">

		<input type='hidden' name='page' value='<?php echo BusinessHours::SLUG; ?>'/>
		<input type="hidden" name="action" value="update"/>

		<?php wp_nonce_field( BusinessHours::SLUG, 'bh_nonce' ); ?>

		<table class="form-table">
			<tr>
				<td>
					<div>
						<h1><?php _e( 'Business hours for each day of the week', 'business-hours' );?></h1>

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
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
		                         value="<?php _e( 'Save Changes' );?>"></p>
	</form>
</div>
