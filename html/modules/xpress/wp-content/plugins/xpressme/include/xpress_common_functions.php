<?php
global $xoops_config;
if (!is_object($xoops_config)){ // is call other modules
	require_once dirname(dirname(dirname(dirname(dirname( __FILE__ ))))) .'/class/config_from_xoops.class.php' ;
	$xoops_config = new ConfigFromXoops;
}

$dummy = __('After Blog address (URL) is set, it is necessary to set the permalink again.','xpressme');
$dummy = __('Can not access WordPress address (URL).','xpressme');
$dummy = __('WordPress Blog address (URL) is different from access URL.','xpressme');


function get_xoops_config($config_name,$module_dir){
	global $xoops_db;
	
	$modules_db = get_xoops_prefix() . 'modules';
	$config_db = get_xoops_prefix() . 'config';

	$moduleID = $xoops_db->get_var("SELECT mid FROM $modules_db WHERE dirname = '$module_dir'");
	if (empty($moduleID)) return null;
	$conf_value = $xoops_db->get_var("SELECT conf_value FROM $config_db WHERE (conf_modid = $moduleID) AND (conf_name = '$config_name')");
	if (empty($conf_value)) return null;
	return  $conf_value;
}

// xoops db
function get_xpress_dir_path()
{
	return ABSPATH;
}

function get_xpress_dir_name()
{
	return basename(ABSPATH);
}

function get_wp_prefix_only()
{
	$dir_name = get_xpress_dir_name();
	$prefix = preg_replace('/wordpress/','wp',$dir_name);
	
	$prefix = $prefix . '_';
	return $prefix;
}

function get_xoops_prefix()
{
	global $xoops_config;
	$ret =$xoops_config->xoops_db_prefix . '_';
	return $ret;
}

function get_xoops_trust_path()
{
	global $xoops_config;
	$ret =$xoops_config->xoops_trust_path;
	return $ret;
}

function get_xoops_root_path()
{
	global $xoops_config;
	$ret =$xoops_config->xoops_root_path;
	return $ret;
}

function get_wp_prefix()
{
	$prefix = get_xoops_prefix() . get_wp_prefix_only();
	return $prefix;
}
function get_xoops_url()
{
	global $xoops_config;
	$ret =$xoops_config->xoops_url ;
	return $ret;
}

function get_xpress_url()
{
	global $xoops_config;
	$ret =$xoops_config->module_url ;
	return $ret;
}

function get_xpress_modid()
{
	global $xoops_db;
	
	$modulename = get_xpress_dir_name();	
	$sql = "SELECT mid FROM " . get_xoops_prefix() . "modules WHERE dirname = '$modulename'";
	$mid = $xoops_db->get_var($sql);
	return $mid;	
}

function get_xpress_db_version()
{
	include get_xpress_dir_path() . '/wp-includes/version.php';
	return $wp_db_version;
}

function is_xpress_mobile()
{
	//ktai_style
 	if (function_exists('is_ktai')){
 		if (is_ktai()) {
 //			$file_path = $GLOBALS['xoopsModuleConfig']["ktai_style_tmpdir"] . '/comments.php';
			return true;
		}
	}
	
	//mobg
	if (function_exists('is_mobile')) {
		if (is_mobile()){
			return true;
		}
	}
	if (
	  preg_match("/DoCoMo/", $_SERVER['HTTP_USER_AGENT']) ||
	  preg_match("/softbank/", $_SERVER['HTTP_USER_AGENT']) ||
	  preg_match("/vodafone/", $_SERVER['HTTP_USER_AGENT']) ||
	  preg_match("/J-PHONE/", $_SERVER['HTTP_USER_AGENT']) ||
	  preg_match("/UP\.Browser/", $_SERVER['HTTP_USER_AGENT']) ||
	  preg_match("/ASTEL/", $_SERVER['HTTP_USER_AGENT']) ||
	  preg_match("/PDXGW/", $_SERVER['HTTP_USER_AGENT'])
	) 
	{
		return true;
	} else {
		return false;
	}
}

function block_cache_refresh() 
{ 
	global $xoops_db; 
	$mid = get_xpress_modid(); 
	$sql = "SELECT bid,options,func_file FROM " . get_xoops_prefix() . "newblocks WHERE mid = $mid"; 
	$blocks = $xoops_db->get_results($sql); 
	$mydirname = get_xpress_dir_name(); 
	require_once get_xpress_dir_path() . '/include/xpress_block_render.php'; 

	foreach($blocks as $block){ 
		$func_file = $block->func_file; 
		
		// Avoid the failure of the operation when switch_to_blog() and other plugin code is called on the admin page.
		$excludes = 'global_recent_posts_list_block\.php|enhanced_block\.php|global_recent_comments_block\.php|global_popular_posts_block\.php';
		if (preg_match('/' . $excludes . '/' , $func_file)){
			continue;
		}
		
		$call_theme_function_name = str_replace(".php", "", $func_file); 
		$inc_theme_file_name = str_replace(".php", "", $func_file) . '_theme.php'; 
		$cache_title = str_replace(".php", "", $func_file); 
		$blockID = $block->bid; 
		$options = explode("|", $block->options); 

		$block_theme_file = get_block_file_path($mydirname,$inc_theme_file_name); 
		require_once $block_theme_file['file_path']; 
		$block_render = $call_theme_function_name($options);            //The block name and the called function name should be assumed to be the same name.                     
		$xml['block'] = $block_render; 
		$xml['block']['options'] = $block->options; 
		xpress_block_cache_write($mydirname,$cache_title. $blockID, $xml); 
	} 
}
function is_wordpress_style()
{
	global $xpress_config;
	
	if ($xpress_config->viewer_type == 'wordpress') return true;
	if ($xpress_config->viewer_type == 'xoops') return false;
	
	// user select
	$get_style = isset($_GET["style"]) ? $_GET["style"] : '';
	$cookie_style = isset($_COOKIE["xpress_style"]) ? $_COOKIE["xpress_style"] : '';
	
	// set style
	if (!empty($get_style)){
		$style = $get_style;
	} else {
		if (!empty($cookie_style)){
			$style = $cookie_style;
		} else {
			$style = 'x';
		}
	}
	
	// set cookie
	if (empty($cookie_style)){
		setcookie("xpress_style", $style);
		$_COOKIE["xpress_style"] = $style;
	} else {
		if ($style != $cookie_style) {
			setcookie("xpress_style", $style);
			$_COOKIE["xpress_style"] = $style;
		}
	}
	if ($style == 'w') {
		return true;
	} else { 
		return false;
	}
}

function wp_meta_add_xpress_menu()
{
	global $xpress_config;
	if ($xpress_config->viewer_type == 'user_select'){
		echo disp_mode_set();
	}
	if (function_exists('wp_theme_switcher') ) {	
		echo '<li>' . __('Themes') . ':';
		wp_theme_switcher('dropdown');
	 	echo '</li>';
	}
}

function disp_mode_set(){
	global $xpress_config;
	
	$select ="";
	if ($xpress_config->viewer_type == 'user_select'){
		$style = isset($_GET["style"]) ? $_GET["style"] : (isset($_COOKIE["xpress_style"]) ? $_COOKIE["xpress_style"] : "");

		switch($style) {
		case 'w':
			$select ='<li><a href="'.get_settings('siteurl').'/?style=x" title="'. __('Switch to XOOPS mode','xpressme').'">'.__('Switch to XOOPS mode','xpressme').'</a></li>';
//			$select.='<img src="'. get_settings('siteurl').'/images/external.png" alt="'.__('Switch to XOOPS mode','xpressme') . '"></a></li>';
			break;
		case 'x':
			$select='<li><a href="'.get_settings('siteurl').'/?style=w" title="'.__('Switch to WordPress mode','xpressme').'">'.__('Switch to WordPress mode','xpressme').'</a></li>';
			break;
		default:
			$select='<li><a href="'.get_settings('siteurl').'/?style=w" title="'.__('Switch to WordPress mode','xpressme').'">'.__('Switch to WordPress mode','xpressme').'</a></li>';
			break;
		}
	}
	return $select;
}

function xpress_comment_count( $count ) {
        global $id;
        $post_comments =get_comments('status=approve&post_id=' . $id);
        $comments_by_type = &separate_comments($post_comments);
        return count($comments_by_type['comment']);
}

function xpress_set_author_cookie($query_vars)
{
	global $wp , $wpdb;
	
	if (is_admin()) return $query_vars;
	
	$author_cookie = 'select_' . get_xpress_dir_name() . "_author" ;
	if(xpress_is_multi_user()){
		if (!empty($_GET)){
			$auth = intval( @$_GET["author"] );
			if ($auth > 0){
				setcookie($author_cookie, $auth, time()+3600, COOKIEPATH);
				$_COOKIE[$author_cookie] = $auth;
			}
		} else {
			if(xpress_is_wp_version('<','2.1')){  // Maybe, I think that it is ver2.1 or less.
				if (!empty($wp->matched_query) ){
					if (strpos($wp->matched_query,'author_name') !== false ){
						$pattern = "author_name\s*=\s*(.*)\s*";
						if ( preg_match ( "/".$pattern."/i", $wp->matched_query, $match ) ){
							$author_name = "$match[1]";
							$auth = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$author_name'");

							setcookie($author_cookie, $auth, time()+3600, COOKIEPATH);
							$_COOKIE[$author_cookie] = $auth;
						}
					}
				} else {
					setcookie($author_cookie, 0, time()+3600, COOKIEPATH);
					$_COOKIE[$author_cookie] = 0;
				}
			} else {
				if (!empty($wp->query_vars) ){
					if (!empty($wp->query_vars['author_name']) ){
						$author_name = $wp->query_vars['author_name'];
						$auth = $wpdb->get_var("SELECT ID FROM $wpdb->users WHERE user_login = '$author_name'");

						setcookie($author_cookie, $auth, time()+3600, COOKIEPATH);
						$_COOKIE[$author_cookie] = $auth;
					}
				} else {
					setcookie($author_cookie, 0, time()+3600, COOKIEPATH);
					$_COOKIE[$author_cookie] = 0;
				}
			}
		}
	}else{
	//	$GLOBALS["wp_xoops_author"] = null;
		setcookie($author_cookie, 0, time()+3600, COOKIEPATH);
		$_COOKIE[$author_cookie] = 0;
	}
	return $query_vars;
}

function xpress_query_filter($query)
{
	if (is_admin()) return $query;

	$author_cookie = 'select_' . get_xpress_dir_name() . "_author" ;
	
	if (strpos($query,'SELECT') === false)  return $query;

	$select_pattern = "SELECT(.*)post_author(.*)FROM";
	if (preg_match ( "/".$select_pattern."/i", $query, $select_match ))
		return $query;

	$query = preg_replace('/\s\s+/', ' ', $query);
	if (!empty($_COOKIE[$author_cookie])){
		if(xpress_is_wp_version('<','2.1')){
			$pattern = "WHERE.*AND\s?\(*post_author\s*=";
			if ( preg_match ( "/".$pattern."/i", $query, $match ) ){
				return $query;
			}
			$pattern = "WHERE\s?post_author\s*="; // get_usernumposts()
			if ( preg_match ( "/".$pattern."/i", $query, $match ) ){
				return $query;
			}
			$pattern = "WHERE.*post_status\s*=\s*'publish'\s*\)?";
			if ( preg_match ( "/".$pattern."/i", $query, $match ) ){
				
				$where_str = "$match[0]";
				$where_arry = split(' ',$where_str);
				$post_prefix = '';
				foreach ($where_arry as $p){
					if ( preg_match ( "/post_status/", $p, $match3 ) ){
						$post_prefix = preg_replace("/post_status/", "", $p);
						$post_prefix = preg_replace("/\(/", "", $post_prefix);
						break;
					}
				}
				$patern = 'WHERE';				
				$replace = "WHERE {$post_prefix}post_author = " . intval($_COOKIE[$author_cookie]) . " AND ";
				$query = preg_replace("/$patern/", $replace, $query);
			}
		} else {
			$pattern = "WHERE.*post_type\s*=\s*'post'\s*\)?";			
			if ( preg_match ( "/".$pattern."/i", $query, $match ) ){
				$where_str = "$match[0]";
				$where_arry = split(' ',$where_str);
				$post_prefix = '';
				foreach ($where_arry as $p){
					if ( preg_match ( "/post_type/", $p, $match3 ) ){
						$post_prefix = preg_replace("/post_type/", "", $p);
						$post_prefix = preg_replace("/\(/", "", $post_prefix);
						break;
					}
				}
				preg_match ( "/post_type(.*)/", $where_str, $p_match );
				$patern_s = $p_match[0];
				$patern = preg_replace('/\)/', '\)', $patern_s);
				
				$replace = $patern_s . " AND {$post_prefix}post_author = " . intval($_COOKIE[$author_cookie]) . " ";

				$query = preg_replace("/$patern/", $replace, $query);
			}
		}
	}
//	xpress_show_sql_quary($query);
	return $query;
}

function get_block_file_path($mydirname,$file_name)
{
	global $xoops_config, $xpress_config;
	$mydirpath = $xoops_config->xoops_root_path . '/modules/' . $mydirname;
	$select_theme = xpress_ThemeTemplate(get_xpress_theme_name($mydirname));
	$xpress_default_theme = 'xpress_default';
	$select_block = '/wp-content/themes/' . $select_theme . '/blocks/' . $file_name;
	$default_block = '/wp-content/themes/xpress_default/blocks/' . $file_name;
	$select_block_path = $mydirpath . $select_block;
	$default_block_path =  $mydirpath . $default_block;

	$block_file_data = array();
	$block_file_data['file_path'] = $default_block_path;
	$block_file_data['error'] = '';

	if($select_theme != $xpress_default_theme){
		if (file_exists($select_block_path)){
			$select_block_version = get_block_version($select_block_path);
			$default_block_version = get_block_version($default_block_path);
			if (version_compare($select_block_version,$default_block_version, "<")){
				$block_file_data['file_path'] = $default_block_path;
				if ($xpress_config->is_block_error_display){
					$error_str = '<div style="color:red">';
					$error_str .= sprintf(__('Block file %1$s is an old version %2$s.<br />used block file %3$s of new version %4$s.','xpressme'),$select_block,$select_block_version,$default_block,$default_block_version);
					$error_str .= '</div>';
					$block_file_data['error'] = $error_str;
				}
			} else {
				$block_file_data['file_path'] = $select_block_path;
				$block_file_data['error'] = '';
			}
		}
	}
	return $block_file_data;
}

function get_block_version($file_path = ''){
	$array_file = file($file_path);
	$pattern = '^[\s|\/]*[B|b]lock\s+[V|v]ersion\s*[:|;]\s*([0-9|.]*)';
	$version = '0.1';
	if (empty($file_path)) return $version;
	if (!file_exists($file_path)) return $version;
	if (count($array_file) > 5) $file_count = 5; else $file_count = count($array_file);
	for ($i = 0 ; $i < $file_count ; $i++){
		if (preg_match('/' . $pattern . '/' ,$array_file[$i],$matchs)){
			$version = $matchs[1];
			break;
		}
	}
	return $version;
}

function icon_exists($str = '')
{
	global $xpress_config;
	if (empty($str)) return false;
	$root_path = $_SERVER['DOCUMENT_ROOT'];
	$root_pattern = str_replace(".","\.",$root_path);
	$root_pattern = '/'. str_replace("/","\/",$root_pattern) . '/';
	$host = str_replace(".","\.",$_SERVER['SERVER_NAME']);
	$pattern = "/https?:\/\/{$host}/";
	if (preg_match($pattern,$str)){
		$str = preg_replace($pattern,$root_path,$str);
	} else if (!preg_match($root_pattern,$str)){
		$str = $root_path.$str;
	}
	return file_exists($str);
}
?>