<?php
/*
 * XPressME - WordPress for XOOPS
 *
 * @copyright	XPressME Project http://www.toemon.com
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		toemon
 * @package		module::xpress
 */
if (!function_exists('wp_uid_to_xoops_uid')){
	function wp_uid_to_xoops_uid($wp_uid = '',$mydirname){
		global $xoopsDB,$xoops_db;
		if (empty($mydirname))
			$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
		
		$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);

		if (empty($xoops_db)) { // not load XPressME
			$xoops_user_tbl = $xoopsDB->prefix('users');
			$wp_user_tbl = $xoopsDB->prefix($wp_prefix . "_users");

			$wp_user_SQL ="SELECT user_login FROM $wp_user_tbl WHERE ID = $wp_uid ";
			$wp_user_Res = $xoopsDB->query($wp_user_SQL, 0, 0);
			if ($xoopsDB->getRowsNum($wp_user_Res)  > 0){
				$wp_user_Row = $xoopsDB->fetchArray($wp_user_Res);
				$wp_user_name = $wp_user_Row['user_login'];
			
				$xoops_user_SQL ="SELECT uid FROM $xoops_user_tbl WHERE uname LIKE '$wp_user_name'";
				$xoops_user_Res = $xoopsDB->query($xoops_user_SQL, 0, 0);
				if ($xoopsDB->getRowsNum($xoops_user_Res)  > 0){
					$xoops_user_Row = $xoopsDB->fetchArray($xoops_user_Res);
					$xoops_uid = $xoops_user_Row['uid'];	
					return $xoops_uid;
				}
			}
			return 0;
		} else { // load XPressME or not Load XOOPS
			$xoops_user_tbl = get_xoops_prefix() . 'users';
			$wp_user_tbl = get_wp_prefix() . 'users';
			$wp_user_name = $xoops_db->get_var("SELECT user_login FROM $wp_user_tbl WHERE ID = $wp_uid ");
			if (empty($wp_user_name)) return 0;
			$xoops_uid = $xoops_db->get_var("SELECT uid FROM $xoops_user_tbl WHERE uname LIKE '$wp_user_name'");
			if (!empty($xoops_uid)) return $xoops_uid; else return 0;
		}
	}
}

if (!function_exists('xoops_uid_to_wp_uid')){
	function xoops_uid_to_wp_uid($xoops_uid = '',$mydirname){
		global $xoopsDB,$xoops_db;
		if (empty($mydirname))
			$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

		$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);

		if (empty($xoops_db)) { // not load XPressME
			$xoops_user_tbl = $xoopsDB->prefix('users');
			$wp_user_tbl = $xoopsDB->prefix($wp_prefix . "_users");

			$xoops_user_SQL ="SELECT uname FROM $xoops_user_tbl WHERE uid =  $xoops_uid";
			$xoops_user_Res = $xoopsDB->query($xoops_user_SQL, 0, 0);
			if ($xoopsDB->getRowsNum($xoops_user_Res)  > 0){
				$xoops_user_Row = $xoopsDB->fetchArray($xoops_user_Res);
				$xoops_user_name = $xoops_user_Row['uname'];	

				$wp_user_SQL ="SELECT ID FROM $wp_user_tbl WHERE user_login LIKE '$xoops_user_name'";
				$wp_user_Res = $xoopsDB->query($wp_user_SQL, 0, 0);
				if ($xoopsDB->getRowsNum($wp_user_Res)  > 0){
					$wp_user_Row = $xoopsDB->fetchArray($wp_user_Res);
					$wp_user_id = $wp_user_Row['ID'];
					return $wp_user_id;
				}
			}
			return 0;
		} else { // load XPressME or not Load XOOPS
			$xoops_user_tbl = get_xoops_prefix() . 'users';
			$wp_user_tbl = get_wp_prefix() . 'users';
			$xoops_user_name = $xoops_db->get_var("SELECT uname FROM $xoops_user_tbl WHERE uid =  $xoops_uid");
			if (empty($xoops_user_name)) return 0;
			$wp_user_id = $xoops_db->get_var("SELECT ID FROM $wp_user_tbl WHERE user_login LIKE '$xoops_user_name'");
			if (!empty($wp_user_id)) return $wp_user_id; else return 0;
		}	
	}
}

// XOOPS user ID is get from the name and the mail address of the contributor of the comment that is not user_id. 
if (!function_exists('wp_comment_author_to_xoops_uid')){
	function wp_comment_author_to_xoops_uid($name = '',$email = ''){
		global $xoopsDB,$xoops_db;
		if (empty($name) || empty($email)) return 0;
		
		if (empty($xoops_db)) { // not load XPressME
			$xoops_user_tbl = $xoopsDB->prefix('users');
			$wp_user_tbl = $xoopsDB->prefix($wp_prefix . "_users");

			$xoops_uid = 0;
			$xoops_user_SQL ="SELECT uid FROM $xoops_user_tbl WHERE uname = '$name' AND email = '$email'";
			$xoops_user_Res = $xoopsDB->query($xoops_user_SQL, 0, 0);
			if ($xoopsDB->getRowsNum($xoops_user_Res)  > 0){
				$xoops_user_Row = $xoopsDB->fetchArray($xoops_user_Res);
				$xoops_uid = $xoops_user_Row['uid'];
			}	
			return $xoops_uid;
		} else { // load XPressME or not Load XOOPS
			$xoops_user_tbl = get_xoops_prefix() . 'users';
			$xoops_uid = $xoops_db->get_var("SELECT uid FROM $xoops_user_tbl WHERE uname = '$name' AND email = '$email'");
			if (empty($xoops_uid)) return 0;
			return $xoops_uid;
		}			
	}
}

// Get Multi Blog table list for WordPressMU 
if (!function_exists('get_table_list')){
	function get_table_list($wp_prefix = '',$table_name = ''){
		global $xoopsDB,$xoops_db;

		$table_list = array();
		$ret = array();
		$wp_prefix = preg_replace('/_$/', '',$wp_prefix);
		$pattern = $wp_prefix . '_' . $table_name . '|' . $wp_prefix . '_[0-9]*_' . $table_name;

		if (!empty($wp_prefix) && !empty($table_name)){
			$sql = "SHOW TABLES LIKE '" . $wp_prefix  . '%' . $table_name . "'";

			if (empty($xoops_db)) { // not load XPressME			
				if($result = $xoopsDB->queryF($sql)){
					while($row = $xoopsDB->fetchRow($result)){
						if(preg_match('/' . $pattern . '/' , $row[0])){
							$table_list[] = $row[0];
						}
					}
				}
			} else { // load XPressME or not Load XOOPS
				$rows = $xoops_db->get_results($sql, ARRAY_N);
				foreach ($rows as $row){
					if(preg_match('/' . $pattern . '/' , $row[0])){
						$table_list[] = $row[0];
					}
				}
			}			
		}
		return $table_list;
	}
}

// Get Middle Prefix from Table name for WordPressMU 
if (!function_exists('get_multi_mid_prefix')){
	function get_multi_mid_prefix($wp_prefix = '',$table_name = '' , $full_table_name){
		$pattern = '/' . $wp_prefix . '(.*)' . $table_name . '/';
		preg_match($pattern,$full_table_name,$match);
		return $match[1];
	}
}

// Get Prefix from Table name for WordPressMU 
if (!function_exists('get_multi_prefix')){
	function get_multi_prefix($full_table_name,$table_name = ''){
		$pattern = '/'. $table_name . '/';
		return preg_replace($pattern,'',$full_table_name);
	}
}
?>
