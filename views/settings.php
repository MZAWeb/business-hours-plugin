<div class="wrap">
	<div id="icon-options-business-hours" class="icon32"><br></div>
	<h2><?php _e( 'Business Hours', 'business-hours' ) ?></h2>
	<br/>

	<?php $this->_maybe_show_updated_notice(); ?>

	<div class="bh_main_container">
		<form id="bh-form" class="bh-form" action="" method="post">

			<input type='hidden' name='page' value='<?php echo BusinessHours::SLUG; ?>'/>
			<input type="hidden" name="action" value="update"/>

			<?php wp_nonce_field( BusinessHours::SLUG, 'bh_nonce' ); ?>

			<?php do_action( 'business-hours-settings-page' ) ?>

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
