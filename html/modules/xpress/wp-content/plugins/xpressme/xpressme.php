<?php
/*
Plugin Name: Plugin for XPressME
Plugin URI: http://ja.xpressme.info
Description: Plugin for XPressME (custom function,filter,action)
Author: toemon
Version: 1.0
Author URI: http://ja.xpressme.info
*/
require_once('xpressme_class.php');

require_once dirname( __FILE__ ).'/include/custom_functions.php' ;		// XPressME functions for themes
require_once dirname( __FILE__ ).'/include/xpress_common_functions.php' ;

$xoops_db = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);
$xoops_db->prefix = get_xoops_prefix();
$xoops_db->tables = array('modules', 'newblocks', 'users');

$xpress_config = new XPressME_Class();

require_once dirname( __FILE__ ).'/include/pluggable-override.php' ;
require_once dirname( __FILE__ ).'/include/functions_for_wp_old.php' ;

if (!is_wordpress_style() && ( !empty($xpress_config->theme_select) || $xpress_config->theme_select != 'use_wordpress_select') ){
	add_filter('stylesheet', 'xpress_Stylesheet');
	add_filter('template', 'xpress_ThemeTemplate');
}
function xpress_Stylesheet($stylesheet) {
	global $xpress_config;
	$theme = $xpress_config->theme_select;
    $theme = get_theme($theme);

    if (empty($theme)) {
        return $stylesheet;
    }
    return $theme['Stylesheet'];
}

function xpress_ThemeTemplate($template) {
	global $xpress_config;
	$theme = $xpress_config->theme_select;
    $theme = get_theme($theme);

    if (empty($theme)) {
        return $template;
    }
    return $theme['Template'];
}

function my_plugin_menu()
{
	global $xpress_config,$xoops_config;
	
	$plugin_url = WP_PLUGIN_URL."/xpressme/";

	// Add a new top-level menu:
	add_menu_page('XPressME','XPressME', 8, __FILE__, 'display_option_page' , $plugin_url.'/images/menu_icon.png');
	// Add submenus to the custom top-level menu:
	add_submenu_page(__FILE__, __('Display Settings', 'xpressme'), __('Display Settings', 'xpressme'), 8, __FILE__, 'display_option_page');
	add_submenu_page(__FILE__, __('Integration Settings', 'xpressme'), __('Integration Settings', 'xpressme'), 8, 'integration_option_page', 'integration_option_page');
	add_submenu_page(__FILE__, __('Other Settings', 'xpressme'), __('Other Settings', 'xpressme'), 8, 'other_option_page', 'other_option_page');
//	if (function_exists('wp_remote_get'))
	if(!xpress_is_multiblog() || xpress_is_multiblog_root()){
		add_submenu_page(__FILE__, __('Upgrade', 'xpressme'), __('Upgrade', 'xpressme'), 8, 'upgrade_page', 'upgrade_page');
		add_submenu_page(__FILE__, __('to Modules Admin', 'xpressme'), __('to Modules Admin', 'xpressme'), 8,  'redirect_xoops_admin', 'redirect_xoops_admin');
	}
}

function  blog_charset_check()
{
	$lang= WPLANG;
	$blog_charset = get_option('blog_charset');
	switch ($lang) {
		case 'ja_EUC':
			if ($blog_charset !=='EUC-JP') update_option('blog_charset', 'EUC-JP' );
			break;
		case 'ja_UTF':
		case 'ja':
			if ($blog_charset !=='UTF-8') update_option('blog_charset', 'UTF-8' );
			break;
		default:
 	}
}

// enable multibyte username
if( ! function_exists( 'sanitize_user_multibyte_at_update' ) ){
	function sanitize_user_multibyte_at_update($username, $raw_username, $strict){
		if (isset($_POST['action']) && $_POST['action'] == 'update'){
			if ($raw_username !== "" && $username !== $raw_username){
				return $raw_username;
			} 
		}
		return $username;
	}
}
add_filter('sanitize_user', "sanitize_user_multibyte_at_update" ,10,3);

add_action('admin_menu', 'my_plugin_menu');

add_filter("upload_dir",array(&$xpress_config, 'xpress_upload_filter'),	1);		// Change wp-include/wp_upload_dir()
if (!$xpress_config->is_save_post_revision){
	remove_action( 'pre_post_update', 'wp_save_post_revision' );			// Not Save Post Revision
}
add_action("wp_meta" , "wp_meta_add_xpress_menu");			// add xpress menu  in wp_meta

//XOOPS Bloack Cache Refresh
add_action("comment_post",	"block_cache_refresh");
add_action("edit_comment",	"block_cache_refresh");
add_action("wp_set_comment_status","block_cache_refresh"); //wp_delete_comment() at deleted
add_action("deleted_post",	"block_cache_refresh");
add_action("publish_post",	"block_cache_refresh");
add_action("edit_post",		"block_cache_refresh");
add_action("private_to_published",	"block_cache_refresh");
add_action("transition_post_status", "block_cache_refresh");

add_action("the_content",	"set_post_views_count");

// blog charset check
add_action("init",	"blog_charset_check");


//XOOPS notifiction
require_once dirname( __FILE__ ).'/include/notify_functions.php' ;
add_action("transition_post_status",	"onaction_publish_post_notify" ,10 , 3);
//	add_action("edit_post",	"onaction_edit_post_notify");
add_action("comment_post",	"onaction_comment_notify");
//	add_action("approve_comment" , "onaction_comment_apobe_notify");
add_action("wp_set_comment_status" , "onaction_comment_apobe_notify");

// user data sync  user_sync_to_xoops($user_id)
require_once dirname( __FILE__ ).'/include/user_sync_xoops.php' ;
add_action('profile_update', 'user_sync_to_xoops');
add_action('user_register', 'user_sync_to_xoops');
add_action('delete_blog', 'blog_group_role_delete',10,2);	//at multi blog delete
add_action('wpmu_new_blog', 'blog_group_role_add',10,2);	//at multi blog delete

//require_once('../include/custom_functions.php');

//D3Forum Comment Integration
if ($xpress_config->is_use_d3forum){
	require_once dirname( __FILE__ ).'/include/d3forum_comment_synchro.php' ;
	add_action("comment_post",	"onaction_comment_post");
	add_action("edit_comment",	"onaction_edit_comment");
	add_action("delete_comment","onaction_delete_comment");
	add_action("delete_post",	"onaction_delete_post");
	add_action("wp_set_comment_status" , "onaction_comment_apobe");
	add_action("publish_post",	"onaction_comment_close");
	
	// comment trashed untrashed action
	add_action("trashed_post_comments",	"onaction_trashed_post_comments");
	add_action("untrashed_post_comments",	"onaction_untrashed_post_comments");
	add_action("trashed_comment",	"onaction_trashed_comment");
	add_action("untrashed_comment",	"onaction_untrashed_comment");

	add_filter('comments_template', "disp_d3forum_comments" );
}

//The trackback and the pingback are excluded from the count of the comment. 
add_filter('get_comments_number', 'xpress_comment_count', 0);

// Query filter for  MultiUser
add_filter('query','xpress_query_filter');
//add_action("init", "xpress_set_author_cookie");
if(xpress_is_wp_version('<','2.1')){
	// It is called before parse_request() makes $GET. 
	add_action("query_vars", "xpress_set_author_cookie");
} else {
	// It is called at the end of parse_request(). 
	add_filter('request', 'xpress_set_author_cookie');
}

// SQL debug windows
add_filter('query', array(&$xpress_config, 'xpress_sql_debug'));
add_action('admin_footer', array(&$xpress_config, 'displayDebugLog'));
add_action('wp_footer', array(&$xpress_config, 'displayDebugLog'));

// Multi Blog default Themes
function my_new_blog_template($blog_id) {
	$default_theme = 'xpress_default';
	update_blog_option($blog_id, 'template',$default_theme);
	update_blog_option($blog_id, 'stylesheet', $default_theme);
}
add_action('wpmu_new_blog','my_new_blog_template',0,1);

function redirect_xoops_admin()
{
	global $xoops_config,$xpress_config;
	$xoops_admin_url = $xoops_config->module_url . '/admin/index.php';
	wp_redirect($xoops_admin_url);
}

function display_option_page()
{
	global $xoops_config,$xpress_config;
	
		$xoops_admin_url = $xoops_config->module_url . '/admin/index.php';

		$do_message ='';
		if (!empty($_POST['submit_update'])) {
			$xpress_config->ReadPostData($_POST);
			$xpress_config->SettingValueWrite('update');
		} else if (isset($_POST['submit_reset'])) {
			$xpress_config->setDefault();
			$xpress_config->SettingValueWrite('update');
		}
		
		echo	'<div class="wrap">'."\n";
		echo		'<div id="icon-options-general" class="icon32"><br /></div>'."\n";
		echo		'<h2>' . __('XPressME Display Setting', 'xpressme') . "</h2><br>\n";
//		echo 		'<div align="right"><a href="' . $xoops_admin_url . '"><h3>'. __('to XOOPS Modules Admin Page', 'xpressme') . '</h3></a></div>';
		echo		'<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">'."\n" ;
		echo			'<table class="form-table">'."\n";
		echo				$xpress_config->viewer_type_option();
		echo				$xpress_config->yes_no_radio_option('is_theme_sidebar_disp',
												__('Thema Sidebar Display','xpressme'),
												__('YES','xpressme'),
												__('NO','xpressme')
												);
		echo 				$xpress_config->single_post_navi_option();
		echo 				$xpress_config->posts_page_navi_option();
		echo 				$xpress_config->excerpt_option();
		echo 				$xpress_config->dashboard_display_option();
		echo			"</table>\n";
		
		echo		'<p class="submit">'."\n";
		echo		'<input type="submit" value= "' . __('Update Config', 'xpressme') . '" name="submit_update" />' ."\n";
		echo		'<input type="submit" value= "' . __('Preset Config', 'xpressme') . '" name="submit_reset" />' ."\n";
		echo		"</p>\n";

		echo		"</form>\n" ;
		echo	"</div>\n";
}

function integration_option_page()
{
	global $xoops_config,$xpress_config,$blog_id;
	
		$xoops_admin_url = $xoops_config->module_url . '/admin/index.php';

		$do_message ='';
		if (!empty($_POST['submit_update'])) {
			$xpress_config->ReadPostData($_POST);
			$xpress_config->SettingValueWrite('update');
		} else if (isset($_POST['submit_reset'])) {
			$xpress_config->setDefault();
			$xpress_config->SettingValueWrite('update');
		} else if (isset($_POST['export_d3f'])) {
			$do_message  = 'export(' . $xpress_config->d3forum_module_dir . '--ID=' . $xpress_config->d3forum_forum_id . ')................';
			$do_message .= wp_to_d3forum($xpress_config->d3forum_forum_id, $xpress_config->d3forum_module_dir);
			$do_message .= '....END';
		} else if (isset($_POST['inport_d3f'])) {
			$do_message  = 'Import(' . $xpress_config->d3forum_module_dir . '--ID=' . $xpress_config->d3forum_forum_id . ')................';
			$do_message .= d3forum_to_wp($xpress_config->d3forum_forum_id, $xpress_config->d3forum_module_dir);
			$do_message .= '....END';
		} 		
		
		$xpress_config->GroupeRoleCheck($blog_id);
		echo	'<div class="wrap">'."\n";
		echo		'<div id="icon-options-general" class="icon32"><br /></div>'."\n";
		echo		'<h2>' . __('XPressME Integration Setting', 'xpressme') . "</h2><br>\n";
//		echo 		'<div align="right"><a href="' . $xoops_admin_url . '"><h3>'. __('to XOOPS Modules Admin Page', 'xpressme') . '</h3></a></div>';
		echo		'<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">'."\n" ;
		echo			'<table class="form-table">'."\n";
		$upload_title = __('Media Upload Base Path','xpressme');
		echo				$xpress_config->yes_no_radio_option('is_use_xoops_upload_path',
											$upload_title,
											__('Use XOOPS UPLOAD PATH','xpressme'),
											__('USE WordPress BASE_PATH','xpressme'),
											false
											);
//		$lock = ($xoops_config->module_url != get_bloginfo('url'));
		$lock = false;
		echo				$xpress_config->groupe_role_option($lock);	
		echo				$xpress_config->d3forum_option($do_message);		
		echo			"</table>\n";
		
		echo		'<p class="submit">'."\n";
		echo		'<input type="submit" value= "' . __('Update Config', 'xpressme') . '" name="submit_update" />' ."\n";
		echo		'<input type="submit" value= "' . __('Preset Config', 'xpressme') . '" name="submit_reset" />' ."\n";
		echo		"</p>\n";

		echo		"</form>\n" ;
		echo	"</div>\n";
}

function other_option_page()
{
	global $xoops_config,$xpress_config;
	
		$xoops_admin_url = $xoops_config->module_url . '/admin/index.php';

		$do_message ='';
		if (!empty($_POST['submit_update'])) {
			$xpress_config->ReadPostData($_POST);
			$xpress_config->SettingValueWrite('update');
		} else if (isset($_POST['submit_reset'])) {
			$xpress_config->setDefault();
			$xpress_config->SettingValueWrite('update');
		}
		
		echo	'<div class="wrap">'."\n";
		echo		'<div id="icon-options-general" class="icon32"><br /></div>'."\n";
		echo		'<h2>' . __('XPressME Other Setting', 'xpressme') . "</h2><br>\n";
//		echo 		'<div align="right"><a href="' . $xoops_admin_url . '"><h3>'. __('to XOOPS Modules Admin Page', 'xpressme') . '</h3></a></div>';
		echo		'<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">'."\n" ;
		echo			'<table class="form-table">'."\n";
		echo				$xpress_config->yes_no_radio_option('is_save_post_revision',
												__('The change tracking of the post is preserved','xpressme'),
												__('YES','xpressme'),
												__('NO','xpressme')
												);
		
		echo				$xpress_config->yes_no_radio_option('is_multi_user',
												__('Select Multi user mode','xpressme'),
												__('YES','xpressme'),
												__('NO','xpressme')
												);
		echo				$xpress_config->yes_no_radio_option('is_author_view_count',
												__('Is the posts author views counted?','xpressme'),
												__('YES','xpressme'),
												__('NO','xpressme')		
												);
		echo 				$xpress_config->header_meta_option();
		echo				$xpress_config->yes_no_radio_option('is_sql_debug',
												__('Is SQL debugging window displayed?','xpressme'),
												__('YES','xpressme'),
												__('NO','xpressme')		
												);
		echo				$xpress_config->yes_no_radio_option('is_block_error_display',
												__('Select warning display of block file version check','xpressme'),
												__('Do display','xpressme'),
												__('Do not display','xpressme')		
												);
		echo			"</table>\n";
		
		echo		'<p class="submit">'."\n";
		echo		'<input type="submit" value= "' . __('Update Config', 'xpressme') . '" name="submit_update" />' ."\n";
		echo		'<input type="submit" value= "' . __('Preset Config', 'xpressme') . '" name="submit_reset" />' ."\n";
		echo		"</p>\n";

		echo		"</form>\n" ;
		echo	"</div>\n";
}

include_once dirname( __FILE__ ).'/include/xpress_upgrade.php' ;
add_action( 'admin_notices', 'xpress_update_check', 3 );

include_once dirname( __FILE__ ).'/include/dashboard_feed.php' ;
if(xpress_is_wp_version('>=','2.8')){
	include_once dirname( __FILE__ ).'/xpressme_widget_class.php' ;
}
?>