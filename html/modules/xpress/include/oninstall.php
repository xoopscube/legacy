<?php
$mydirpath = dirname(dirname(__FILE__));
$mydirname = basename($mydirpath);
$lang = @$GLOBALS["xoopsConfig"]['language'];
global $wp_db_version,$wp_rewrite;
include_once $mydirpath .'/wp-includes/version.php' ;

// language file (modinfo.php)

if( file_exists( $mydirpath .'/language/'.$lang.'/modinfo.php' ) ) {
	include_once $mydirpath .'/language/'.$lang.'/modinfo.php' ;
} else if( file_exists(  $mydirpath .'/language/english/modinfo.php' ) ) {
	include_once $mydirpath .'/language/english/modinfo.php' ;
}


eval( ' function xoops_module_install_'.$mydirname.'( $module ) { return xpress_oninstall_base( $module , "'.$mydirname.'" ) ; } ' ) ;


if( ! function_exists( 'xpress_oninstall_base' ) ) :
function xpress_oninstall_base( $module , $mydirname )
{
	// transations on module install

	global $ret ; // TODO :-D

	// for Cube 2.1
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleInstall.' . ucfirst($mydirname) . '.Success' , 'xpress_message_append_oninstall' ) ;
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleInstall.' . ucfirst($mydirname) . '.Fail' , 'xpress_message_append_oninstall_err' ) ;
		$ret = array() ;
	} else {
		if( ! is_array( $ret ) ) $ret = array() ;
	}

	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;

	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$ret[] = "********************************* Install Log ********************************<br />";
	} else {
		$ret[] = '<h4 style="border-bottom: 1px dashed rgb(0, 0, 0); text-align: left; margin-bottom: 0px;">Install Log</h4>';
    }

//xpress
	global $wpdb,$wp_rewrite, $wp_queries, $table_prefix, $wp_db_version, $wp_roles, $wp_query,$wp_embed;
	global $xoops_config;
		
	define("WP_INSTALLING", true);
	define('WP_FIRST_INSTALL', true); // For WPMU2.8
	
	$site_url= XOOPS_URL."/modules/".$mydirname;
	$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
	$path = $mydirpath . '/';
	$site_name = ucfirst($mydirname) . ' ' . _MI_XP2_NAME;
	
// permission and wordpress files check
	require_once ($path . 'include/pre_check.php');
	if(! xp_permission_check($mydirname, $mydirpath)){
		if( ! defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$ret = $GLOBALS["err_log"];
			$ret[] = "<br /><span style=\"color:#ff0000;\">The uninstallation of the module is executed now. </span><br />";
			$ret[] = xoops_module_uninstall($mydirname);
		}
		return false;
	}
	
// install WordPress
	if (file_exists($path . 'wp-load.php')) {
		require_once $path . 'wp-load.php';
	} else {
		require_once $path . 'wp-config.php';
	}
	include_once($mydirpath . '/wp-admin/upgrade-functions.php');
	wp_cache_flush();
	make_db_current_silent();
	$ret[] = "The data base of wordpress was made by prefix $table_prefix.<br />";
	
	$option_desc = __('WordPress web address');
	$wpdb->query("INSERT INTO $wpdb->options (blog_id, option_name,option_value, autoload) VALUES ('0', 'siteurl','$site_url', 'yes')");	
	$wpdb->query("INSERT INTO $wpdb->options (blog_id, option_name,option_value, autoload) VALUES ('0', 'home','$site_url', 'yes')");

	populate_options();
	populate_roles();
	
// create XPressME table
	$xp_prefix = preg_replace('/wordpress/','wp',$mydirname);
	$views_table = XOOPS_DB_PREFIX . '_' . $xp_prefix .'_views' ;

	$charset_collate = '';
	if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}
	$views_queries ="CREATE TABLE $views_table (
  		blog_id bigint(20) unsigned NOT NULL default '0',
  		post_id bigint(20) unsigned NOT NULL default '0',
  		post_views bigint(20) unsigned NOT NULL default '0',
  		KEY post_id (post_id)
		)$charset_collate;";
	dbDelta($views_queries);
	$ret[] = "$views_table table of XPressME was made.<br />";
	
	$d3forum_link = XOOPS_DB_PREFIX . '_' . $xp_prefix .'_d3forum_link' ;
	$views_queries ="CREATE TABLE $d3forum_link (
  		comment_ID bigint(20) unsigned NOT NULL default '0',
  		post_id int(10) unsigned NOT NULL default '0' ,
  		wp_post_ID bigint(20) unsigned NOT NULL default '0',
  		forum_id bigint(20) unsigned NOT NULL default '0',
  		blog_id bigint(20) unsigned NOT NULL default '0',
  		KEY post_id (post_id)
		)$charset_collate;";
	dbDelta($views_queries);
	$ret[] = "$d3forum_link table of XPressME was made.<br />";
	
	$group_role = XOOPS_DB_PREFIX . '_' . $xp_prefix .'_group_role' ;
	$views_queries ="CREATE TABLE $group_role (
  		groupid smallint(5) unsigned NOT NULL default '0',
  		blog_id bigint(20) unsigned NOT NULL default '0',
  		name varchar(50)  NOT NULL default '' ,
  		description text  NOT NULL default '',
  		group_type varchar(50)  NOT NULL default '' ,
		role varchar(20)  NOT NULL default '' ,
		login_all smallint(5) unsigned NOT NULL default '0' ,
  		KEY groupid (groupid)
		)$charset_collate;";
	dbDelta($views_queries);
	$ret[] = "$group_role table of XPressME was made.<br />";
	
	$notify_reserve = XOOPS_DB_PREFIX . '_' . $xp_prefix .'_notify_reserve' ;
	$queries ="CREATE TABLE $notify_reserve (
  		notify_reserve_id bigint(20) NOT NULL AUTO_INCREMENT ,
  		notify_reserve_status varchar(20)  NOT NULL default '' ,
  		category text  NOT NULL default '',
  		item_id bigint(20) unsigned NOT NULL default '0',
		event varchar(20) NOT NULL default '',
		extra_tags_arry longtext NOT NULL default '' ,
		user_list_arry longtext NOT NULL default '' ,
  		module_id smallint(5) unsigned NOT NULL default '0' ,
  		omit_user_id varchar(20) NOT NULL default '' ,
  		KEY notify_reserve_id (notify_reserve_id)
		)ENGINE=MyISAM";
	dbDelta($queries);
	$ret[] = "$notify_reserve table of XPressME was made.<br />";

	$sql = "INSERT INTO $group_role (groupid, role) VALUES (1, 'administrator')";
	$wpdb->query($sql);
	
// make templates
	include_once XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/include/xpress_templates_make.php' ;
	$t_mess = xpress_templates_make($mid,$mydirname);
	
// Admin User Data write
	// Change uid field
	$wpdb->query("ALTER TABLE $wpdb->posts CHANGE `post_author` `post_author` mediumint(8) NOT NULL DEFAULT '0'");
	$user_name = is_object($GLOBALS["xoopsUser"])?$GLOBALS["xoopsUser"]->getVar("uname"):'admin';
	$email = is_object($GLOBALS["xoopsUser"])?$GLOBALS["xoopsUser"]->getVar("email"):'foo@exsample.com';
	$pass_md5 = is_object($GLOBALS["xoopsUser"])?$GLOBALS["xoopsUser"]->getVar("pass"):'';
	
	add_filter('sanitize_user', "sanitize_user_multibyte" ,10,3);
	
	if (!function_exists('username_exists')){
		require_once($mydirpath . '/wp-includes/registration-functions.php');
	}
	$user_id = username_exists($user_name);
	if ( !$user_id ) {
		$random_password = 'admin';
		$user_id = wp_create_user($user_name, $random_password, $email);
	} else {
		$random_password = __('User already exists.  Password inherited.');
	}

	$user = new WP_User($user_id);
	$user->set_role('administrator');
	'User ' . $user_name . ' of the administrator was made.';
	// over write xoops md5 password 
	$sql = "UPDATE $wpdb->users SET user_pass ='$pass_md5' WHERE ID = $user_id";
	$wpdb->query($sql);
	$ret[] = 'The password of XOOPS was copied.<br />';
	
	
// Set Default data
	// make WordPress Default data	
	if (function_exists('wp_install_defaults')){
		wp_install_defaults($user_id);
	} else {
		wp_install_old_defaults($user_id);
	}
	
	$ret[] = 'The first sample post & comment was written.<br />';
	
	// Rewrite Option for Xpress
	$xoops_config_tbl = XOOPS_DB_PREFIX . '_config' ;
	$sql = "SELECT conf_value FROM  $xoops_config_tbl WHERE `conf_name` = 'default_TZ'";
	$xoops_default_TZ = $wpdb->get_var($sql);
	update_option('gmt_offset', $xoops_default_TZ);	

	if (WPLANG == 'ja_EUC') {
		$setup_charset = 'EUC-JP';
	} elseif(WPLANG == 'ja_SJIS') {
		$setup_charset = 'Shift_JIS';
	} else {
		$setup_charset = 'UTF-8';
	}
	update_option("blog_charset", $setup_charset);

	update_option('blogname', $site_name );	
	update_option('blogdescription', 'WordPress for XOOPS');
	update_option("admin_email", $GLOBALS["xoopsConfig"]['adminmail']);
	update_option("ping_sites", "http://rpc.pingomatic.com/\nhttp://ping.xoopsforge.com/");
	update_option("home", $site_url);
	update_option("siteurl", $site_url);
	update_option("what_to_show", "posts");
	update_option('default_pingback_flag', 0);
	$ret[] = 'The initial data was written in the data base of wordpress.<br />';
	
	update_option("template", "xpress_default");
	update_option("stylesheet", "xpress_default");
	$ret[] = 'The default theme of wordpress was set to xpress_default.<br />';
//	update_option('uploads_use_yearmonth_folders', 1);
	update_option('upload_path', 'wp-content/uploads');
			
// activate the xpressme plugin
	require_once dirname( __FILE__ ).'/xpress_active_plugin.php';
	if (xpress_pulugin_activation('xpressme/xpressme.php')){
		$ret[] = 'The xpressme plug-in was activated.<br />';
	} else {
		$GLOBALS["err_log"][] =  '<span style="color:#ff0000;">failed in the activation of xpressme plug-in.</span><br />';
		return false;
	}

	$ret = array_merge($ret,$t_mess);

	return true ;
}
endif;

if( ! function_exists( 'xpress_message_append_oninstall' ) ) :
function xpress_message_append_oninstall( &$module_obj , &$log )
{
	if( is_array( @$GLOBALS['ret'] ) ) {
		foreach( $GLOBALS['ret'] as $message ) {
			$log->add( strip_tags( $message ) ) ;
		}
	}

	// use mLog->addWarning() or mLog->addError() if necessary
}
endif;

if( ! function_exists( 'xpress_message_append_oninstall_err' ) ) :
function xpress_message_append_oninstall_err( &$module_obj , &$log )
{
	if( is_array( @$GLOBALS["err_log"] ) ) {
		foreach( $GLOBALS["err_log"] as $message ) {
			$log->add( strip_tags($message)) ;
		}
	}

	// use mLog->addWarning() or mLog->addError() if necessary
}
endif;


if( ! function_exists( 'wp_install_old_defaults' ) ) :
function wp_install_old_defaults($user_id) {
	global $wpdb;

	// Now drop in some default links
	$wpdb->query("INSERT INTO $wpdb->linkcategories (cat_id, cat_name) VALUES (1, '".$wpdb->escape(__('Blogroll'))."')");
	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://blogs.linux.ie/xeer/', 'Donncha', 1, 'http://blogs.linux.ie/xeer/feed/', '');");
	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://zengun.org/weblog/', 'Michel', 1, 'http://zengun.org/weblog/feed/', '');");
	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://boren.nu/', 'Ryan', 1, 'http://boren.nu/feed/', '');");
	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://photomatt.net/', 'Matt', 1, 'http://xml.photomatt.net/feed/', '');");
	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://zed1.com/journalized/', 'Mike', 1, 'http://zed1.com/journalized/feed/', '');");
	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://www.alexking.org/', 'Alex', 1, 'http://www.alexking.org/blog/wp-rss2.php', '');");
	$wpdb->query("INSERT INTO $wpdb->links (link_url, link_name, link_category, link_rss, link_notes) VALUES ('http://dougal.gunters.org/', 'Dougal', 1, 'http://dougal.gunters.org/feed/', '');");

	// Default category
	$wpdb->query("INSERT INTO $wpdb->categories (cat_ID, cat_name, category_nicename, category_count, category_description) VALUES ('0', '".$wpdb->escape(__('Uncategorized'))."', '".sanitize_title(__('Uncategorized'))."', '1', '')");

	// First post
	$now = date('Y-m-d H:i:s');
	$now_gmt = gmdate('Y-m-d H:i:s');
	$wpdb->query("INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt, post_content, post_excerpt, post_title, post_category, post_name, post_modified, post_modified_gmt, comment_count, to_ping, pinged, post_content_filtered) VALUES ('1', '$now', '$now_gmt', '".$wpdb->escape(__('Welcome to WordPress. This is your first post. Edit or delete it, then start blogging!'))."', '', '".$wpdb->escape(__('Hello world!'))."', '0', '".$wpdb->escape(__('hello-world'))."', '$now', '$now_gmt', '1', '', '', '')");

	$wpdb->query( "INSERT INTO $wpdb->post2cat (`rel_id`, `post_id`, `category_id`) VALUES (1, 1, 1)" );

	// Default comment
	$wpdb->query("INSERT INTO $wpdb->comments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_date, comment_date_gmt, comment_content) VALUES ('1', '".$wpdb->escape(__('Mr WordPress'))."', '', 'http://wordpress.org/', '$now', '$now_gmt', '".$wpdb->escape(__('Hi, this is a comment.<br />To delete a comment, just log in and view the post&#039;s comments. There you will have the option to edit or delete them.'))."')");

	// First Page

	$wpdb->query("INSERT INTO $wpdb->posts (post_author, post_date, post_date_gmt, post_content, post_excerpt, post_title, post_category, post_name, post_modified, post_modified_gmt, post_status, to_ping, pinged, post_content_filtered) VALUES ('1', '$now', '$now_gmt', '".$wpdb->escape(__('This is an example of a WordPress page, you could edit this to put information about yourself or your site so readers know where you are coming from. You can create as many pages like this one or sub-pages as you like and manage all of your content inside of WordPress.'))."', '', '".$wpdb->escape(__('About'))."', '0', '".$wpdb->escape(__('about'))."', '$now', '$now_gmt', 'static', '', '', '')");
}
endif;
if( ! function_exists( 'sanitize_user_multibyte' ) ) :
function sanitize_user_multibyte($username, $raw_username, $strict){
	if ($raw_username !== "" && $username !== $raw_username){
		return $raw_username;
	}
	return $username;
}
endif;
?>