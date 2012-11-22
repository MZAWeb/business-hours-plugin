<?php if ( $collapsible_link ) { ?>
<a class="business_hours_collapsible_handler" href="#"><?php _e( $collapsible_link_anchor, "business-hours" );?></a>
<div class="business_hours_collapsible">
<?php } ?>

	<table width='100%'>
		<tr>
			<th><?php _e( "Day", "business-hours" );?></th>
			<th class='business_hours_table_heading'><?php _e( "Open", "business-hours" );?></th>
			<th class='business_hours_table_heading'><?php _e( "Close", "business-hours" );?></th>
		</tr>
		<?php
		foreach ( $days as $day ) {
			$this->_table_row( $day );
		}
		?>

	</table>

<?php if ( $collapsible_link ) { ?>
</div>
<?php } ?>