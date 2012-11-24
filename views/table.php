<?php if ( $collapsible_link ) { ?>
<div class="business_hours_collapsible_handler_container">
	<a class="business_hours_collapsible_handler" href="#"><?php _e( $collapsible_link_anchor, "business-hours" );?></a>
</div>
<div class="business_hours_collapsible">
<?php } ?>

	<table width='100%'>
		<tr>
			<th><?php _e( "Day", "business-hours" );?></th>
			<th class='business_hours_table_heading'><?php _e( "Open", "business-hours" );?></th>
			<th class='business_hours_table_heading'><?php _e( "Close", "business-hours" );?></th>
		</tr>
		<?php
		foreach ( $days as $id => $day ) {
			$this->_table_row( $id, $day );
		}
		?>

	</table>

<?php if ( $collapsible_link ) { ?>
</div>
<?php } ?>