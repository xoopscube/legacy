<?php
/*
 * XPressME - WordPress for XOOPS
 *
 * @copyright	XPressME Project http://www.toemon.com
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		toemon
 * @package		module::xpress
 */

/* 
 * There is a function that detects the kind of the page that the access is here. 
 */
function check_page_call($check_file =''){
	global $xoops_config;	// not object at install
	if (empty($check_file)) return false;
	$xpress_page = 	basename(dirname(dirname(__FILE__))) . '/' . $check_file;
	$php_script_name = $_SERVER['SCRIPT_NAME'];
	$php_query_string = $_SERVER['QUERY_STRING'];
	if (strstr($php_script_name,$xpress_page) === false) return false;
	if ($check_file !== 'index.php' ) return true;
	// index.php check
	if (strstr($php_query_string,'preview') === false) {
		if (strstr($php_query_string,'feed') === false) {
			// Because the judgment is difficult, the feed to which the permalink is set is confirmed here by the after processing. 
			return true;
		}
	}
	return false;
}

function is_xpress_comments_post_call(){
	return check_page_call('wp-comments-post.php');
}

function is_xpress_index_page_call(){
	return check_page_call('index.php');
}

function is_admin_page_call(){
	return check_page_call('wp-admin');
}

function is_media_upload_page_call(){
	return check_page_call('wp-admin/async-upload.php');
}

function is_wp_cron_page_call(){
	return check_page_call('wp-cron.php');
}

function is_admin_post_call(){
	return check_page_call('wp-admin/post.php');
}

function is_xmlrpc_call(){
	$ret =  check_page_call('xmlrpc.php');

	$xmlrpc_debug = 0;
	if ($xmlrpc_debug && $ret) {
		xpress_debug_message('is_xmlrpc_call()'. "\n" . sprint_r($_SERVER) );
	}
	return $ret;
}

function is_xpress_install_call(){
	$action = 'action=ModuleInstall&dirname=';
	$php_script_name = $_SERVER['SCRIPT_NAME'];
	$php_query_string = $_SERVER['QUERY_STRING'];
	if (strstr($php_query_string,$action) !== false) return true;
	return false;
}
?>
