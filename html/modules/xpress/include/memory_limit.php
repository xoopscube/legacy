<?php
	// Set XPressME memory limit
	function xpress_set_memory_limmit(){
		global $xoops_config;
		global $xoopsDB;
		
		$module_id = '';
		$memory = '';
		
		if (!is_object($xoops_config)){
			require_once dirname(dirname( __FILE__ )).'/class/config_from_xoops.class.php' ;
			$xoops_config = new ConfigFromXoops;
		}
		
		$has_xoops_db = (!empty($xoopsDB));
		if (!$has_xoops_db) {
			$cn = mysql_connect($xoops_config->xoops_db_host, $xoops_config->xoops_db_user, $xoops_config->xoops_db_pass);
			if ($cn){
				$db_selected = mysql_select_db($xoops_config->xoops_db_name, $cn);
			}
		}

		// get module ID
		$module_table = $xoops_config->xoops_db_prefix . '_modules';
		$module_sql = "SELECT mid FROM $module_table WHERE `dirname` = '$xoops_config->module_name'";
		if ($has_xoops_db) {
			if ($result = $xoopsDB->query($module_sql, 0, 0)){
				$row = $xoopsDB->fetchArray($result);
				$module_id = $row['mid'];
			}	
		} else {
			if ($db_selected){
				if($result = mysql_query($module_sql)){
					$row = mysql_fetch_assoc($result);
					$module_id = $row['mid'];
				}
			}
		}
		if (!empty($module_id)){
			// get memory_limit
			$config_table = $xoops_config->xoops_db_prefix . '_config';
			$config_sql = "SELECT conf_value FROM $config_table WHERE `conf_modid` = $module_id AND `conf_name` = 'memory_limit'";
			if ($has_xoops_db) {
				if ($result = $xoopsDB->query($config_sql, 0, 0)){
					$row = $xoopsDB->fetchArray($result);
					$memory = $row['conf_value'];
				}
			} else {
				if ($db_selected){
					if($result = mysql_query($config_sql)){
						$row = mysql_fetch_assoc($result);
						$memory = $row['conf_value'];
					}
				}
			}
		}
		if (!$has_xoops_db) {
			mysql_close($cn);
		}
		
		if (empty($memory)) return;
 		if ( !defined('WP_MEMORY_LIMIT') )
			define('WP_MEMORY_LIMIT', $memory . 'M');
		if ( function_exists('memory_get_usage') && ( (int) @ini_get('memory_limit') < abs(intval(WP_MEMORY_LIMIT)) ) )
			@ini_set('memory_limit', WP_MEMORY_LIMIT);
	}
?>