<tr>
	<td><?php echo esc_html($name); ?></td>
	<td><input name="open_<?php echo esc_attr( $id ); ?>" class="business_hours_hour_field open" type="text" value="<?php echo esc_attr($open) ;?>"/></td>
	<td><input name="close_<?php echo esc_attr( $id ); ?>" class="business_hours_hour_field close" type="text" value="<?php echo esc_attr($close) ;?>"/></td>
</tr>