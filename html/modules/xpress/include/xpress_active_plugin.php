<?php
if ( !function_exists('xpress_pulugin_activation') ){
	function xpress_pulugin_activation($activation_plugin = '')
	{
		global $wpdb;
		if (empty($activation_plugin)) return false;
			
		$plugins = get_option('active_plugins');
		$is_active = false;
		if (!empty($plugins)){
			foreach($plugins as $plugin){
				if ($plugin == $activation_plugin) {
					$is_active = true;
					break;
				}
			}
		} else {
			$plugins = array();
		}
		if (!$is_active){
			array_push($plugins, $activation_plugin);
			update_option('active_plugins', $plugins);
			include_once(dirname(dirname(__FILE__) ) . '/wp-content/plugins/'.$activation_plugin);
			do_action('activate_'.$activation_plugin);
			return true;
		}
		return false;
	}
}