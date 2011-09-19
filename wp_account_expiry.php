<?php
/*
Plugin Name: Wordpress Account Expiry
Plugin URI: https://github.com/standardtoaster/wp_account_expiry
Description: Allows accounts to have expiry dates. Expired accounts cannot be logged in
Version: 1.0
Author: Andrew Preece
Author URI: http://www.latefortea.com
License: CC0
*/

new WP_Account_Expiry;

class WP_Account_Expiry {

	function WP_Account_Expiry()
	{
		$this->__construct();
	}

	function __construct()
	{
		add_action('admin_init', array( &$this, 'admin_init' ));
		add_action('admin_footer', array( &$this, 'admin_footer'));
		
		add_action('show_user_profile', array(&$this,'extra_user_profile_fields'));
		add_action('edit_user_profile', array(&$this,'extra_user_profile_fields'));
		
		#add_action('personal_options_update', array(&$this,'save_extra_user_profile_fields'));
		add_action('edit_user_profile_update', array(&$this,'save_extra_user_profile_fields'));

		add_filter('manage_users_columns', array(&$this,'add_expiry_column'));
		add_filter('manage_users_custom_column', array(&$this,'manage_expiry_column'), 10, 3);

		add_filter('authenticate', array(&$this,'ensure_not_expired'), 21, 3);
		add_filter('shake_error_codes', array(&$this,'custom_error_shake'));   
	}
	
	function admin_init()
	{
		$pluginfolder = get_bloginfo('url') . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker', $pluginfolder . '/jquery.ui.datepicker.min.js', array('jquery', 'jquery-ui-core') );
		wp_enqueue_style('jquery.ui.theme', $pluginfolder . '/smoothness/jquery-ui-1.8.16.custom.css');
	}
	
	function admin_footer() {
		echo <<<END
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('.datepicker').datepicker({
				dateFormat : 'yy-mm-dd'
			});
		});
		</script>
END;
	}

	function extra_user_profile_fields($user)
	{
		require_once('extra_user_profile_fields.php');
	}

	function save_extra_user_profile_fields($user_id) {
		if (!current_user_can('administrator'))
			return false;
			
		$account_expires = array_key_exists('account_expires', $_POST) ? true : false;
		update_user_meta( $user_id, 'account_expires', $account_expires);
		update_user_meta( $user_id, 'expiry_date', strtotime($_POST['expiry_date']));
	}

	function add_expiry_column($columns) {
        $cb_col = array_slice($columns, 0, 4);
        $new_col    = array('expires' => 'Expires');
        $columns = array_merge($cb_col, $new_col, $columns);
        return $columns;
	}
	
	function manage_expiry_column($default, $column_name, $user_id) {
		if( $column_name == 'expires' )
		{
			if (!get_user_meta( $user_id, 'account_expires', true))
				return 'Never';
			return date('Y-m-d', (int)esc_attr(get_user_meta($user_id, 'expiry_date', true)));		
		}
		return $default;
	}

	function ensure_not_expired( $user, $username, $password ){
			if (is_a($user, 'WP_User')) {
			if (get_user_meta( $user->ID, 'account_expires', true)) {
				if (get_user_meta($user->ID, 'expiry_date', true) < time())
					$user = new WP_Error( 'denied', __("<strong>ERROR</strong>: Your account has expired.") );
			}
 		}
		 return $user;
	}

	function custom_error_shake( $shake_codes ){
		 $shake_codes[] = 'denied';
		 return $shake_codes;
	}

}
?>
