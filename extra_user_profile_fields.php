<?php  

$account_expires = esc_attr(get_user_meta($user->ID, 'account_expires', true)) ? "checked='yes'" : '';
$expiry_date = date('Y-m-d', esc_attr(get_user_meta($user->ID, 'expiry_date', true)));

?>


<h3><?php _e("Account Expiry", "blank"); ?></h3>
 
<table class="form-table">
	<tr>
		<th><label for="province"><?php _e("Account Expires?"); ?></label></th>
		<td>
			<input type="checkbox" name="account_expires" id="account_expires" value="" <?php echo $account_expires ?> class="regular-text" /><br />
			<span class="description"><?php _e("Does this account expire?"); ?></span>
		</td>
	</tr>
	<tr>
		<th><label for="postalcode"><?php _e("Expiry Date"); ?></label></th>
		<td>
			<input type="text" name="expiry_date" id="expiry_date" value="<?php echo $expiry_date ?>" class="datepicker" /><br />
			<span class="description"><?php _e("Please enter the Expiry Date of this account (if applicable)."); ?></span>
		</td>
	</tr>
</table>