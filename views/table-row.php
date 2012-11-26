<tr class='<?php echo $class;?>'>
	<td class='business_hours_table_day'><?php echo ucwords( $day_name );?></td>
	<?php if ( $is_open_today ) { ?>
		<td class='business_hours_table_open'><?php echo ucwords( $open );?></td>
		<td class='business_hours_table_close'><?php echo ucwords( $close );?></td>
	<?php } else { ?>
		<td class='business_hours_table_closed' colspan='2' align='center'><?php echo $closed_text; ?></td>
<?php } ?>
</tr>