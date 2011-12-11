<?php
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( ' function xoops_module_update_'.$mydirname.'( $module ) { return xpress_onupdate_base( $module , "'.$mydirname.'" ) ; } ' ) ;


if( ! function_exists( 'xpress_onupdate_base' ) ) :
function xpress_onupdate_base( $module , $mydirname )
{
	// transations on module update

	global $msgs ; // TODO :-D
	// for Cube 2.1
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Success', 'xpress_message_append_onupdate' ) ;
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Fail', 'xpress_message_append_onupdate' ) ;
		$msgs = array() ;
	} else {
		if( ! is_array( $msgs ) ) $msgs = array() ;
	}

	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;
	


//XPressME Update
	global $wpdb,$wp_rewrite, $wp_queries, $table_prefix, $wp_db_version, $wp_roles,$wp_query;
	global $xoops_db;
	$mydirpath = XOOPS_ROOT_PATH . '/modules/' . $mydirname;
	$path = $mydirpath . '/';

// permission and wordpress files check
	require_once ($path . 'include/pre_check.php');
	if(! xp_permission_check($mydirname, $mydirpath)){
		$msgs = $GLOBALS["err_log"];
		return false;
	}

//Site_url and home of an optional table are repaired. 
	$site_url= XOOPS_URL."/modules/".$mydirname;
	xpress_put_siteurl($mydirname,$site_url);
	$home = get_xpress_option($mydirname,'home');
	$home_check = 'home option is right';
	if (strcmp($site_url,$home) !== 0 ){
		if (!@fclose(@fopen($home . '/xoops_version.php', "r"))){
			xpress_put_home($mydirname,$site_url);
			$home_check = 'Change home option $home to $site_url';
		}
	}
	$msgs[] = $home_check;
// XPressME orignal table update
	$t_mess = xpress_table_make($module , $mydirname);
	$msgs = array_merge($msgs,$t_mess);

// make templates
	include_once XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/include/xpress_templates_make.php' ;
	$mod_version = $module->getVar('version') ;

	$t_mess = xpress_clean_templates_file($mydirname,$mod_version);
	$msgs = array_merge($msgs,$t_mess);
	
	$t_mess = xpress_templates_make($mid,$mydirname);
	$msgs = array_merge($msgs,$t_mess);

// The activation processing of the XPressME plugin is omitted. 
// Because the XPressME plugin is done with wp-config in activation

	/* activate the xpressme plugin */
//	require_once dirname( __FILE__ ).'/xpress_active_plugin.php';
//	if (xpress_pulugin_activation('xpressme/xpressme.php')){
//		$msgs[] = 'The xpressme plug-in was activated.';
//	}
	
	return true ;
}
endif;

if( ! function_exists( 'xpress_put_siteurl' ) ) :
function xpress_put_siteurl($mydirname,$url){
		global $xoopsModule;
		$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);
		$xoopsDB =& Database::getInstance();
		$db_xpress_option = $xoopsDB->prefix($wp_prefix . '_options');

		$sql = "UPDATE $db_xpress_option SET option_value = '$url' WHERE option_name = 'siteurl'";
		$res = $xoopsDB->queryF($sql, 0, 0);
}
endif;

if( ! function_exists( 'xpress_put_home' ) ) :
function xpress_put_home($mydirname,$url){
		global $xoopsModule;
		$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);
		$xoopsDB =& Database::getInstance();
		$db_xpress_option = $xoopsDB->prefix($wp_prefix . '_options');

		$sql = "UPDATE $db_xpress_option SET option_value = '$url' WHERE option_name = 'home'";
		$res = $xoopsDB->queryF($sql, 0, 0);
}
endif;

if( ! function_exists( 'get_xpress_option' ) ) {
	function get_xpress_option($mydirname,$option_name){
		global $xoopsModule;
		$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);
		$xoopsDB =& Database::getInstance();
		$option_table = $xoopsDB->prefix($wp_prefix . '_options');

		$sql = "SELECT option_value FROM $option_table WHERE option_name = '" . $option_name . "'";
		
		$result =  $xoopsDB->query($sql, 0, 0);
		if ($xoopsDB->getRowsNum($result)  > 0){
			$row = $xoopsDB->fetchArray($result);
			return $row['option_value'];
		}
		return 0;
	}
}

if( ! function_exists( 'xpress_message_append_onupdate' ) ) :
function xpress_message_append_onupdate( &$module_obj , &$log )
{
	if( is_array( @$GLOBALS['msgs'] ) ) {
		foreach( $GLOBALS['msgs'] as $message ) {
			$log->add( strip_tags( $message ) ) ;
		}
	}

	// use mLog->addWarning() or mLog->addError() if necessary
}
endif;

if( ! function_exists( 'get_db_version' ) ) :
function get_db_version($mydirname){
		global $xoopsModule;
		$wp_prefix = preg_replace('/wordpress/','wp',$mydirname);
		$xoopsDB =& Database::getInstance();
		$db_xpress_option = $xoopsDB->prefix($wp_prefix . '_options');

		$sql = "SELECT option_value FROM $db_xpress_option WHERE option_name = 'db_version'";
		$res = $xoopsDB->query($sql, 0, 0);
		if ($res === false){
			return false;
		} else {
			$row = $xoopsDB->fetchArray($res);
			return $row['option_value'];
		}
}
endif;

if( ! function_exists( 'xpress_block_check' ) ) :
function xpress_block_check($mydirname){
	include_once(dirname(dirname(__FILE__)) . '/class/check_blocks_class.php');

	$xoops_block_check =& xoops_block_check::getInstance();

	if ( !$xoops_block_check->is_admin() )
	{
		$cont = 'Block Check Pass';
		return cont;
	}

	switch ( $xoops_block_check->get_op() ) 
	{
		case "remove_block":
			$cont = $xoops_block_check->remove_block();
			break;

		default:
			$cont = $xoops_block_check->check_blocks($mydirname);
			break;
	}
	return $cont;
}
endif;

if( ! function_exists( 'xpress_table_make' ) ) :
function xpress_table_make($module, $mydirname)
{
	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;

// XPressME orignal table update
	$xp_prefix = preg_replace('/wordpress/','wp',$mydirname);
	$msgs = array();

	$views_table = XOOPS_DB_PREFIX . '_' . $xp_prefix .'_views' ;
	if (! enhanced_table_check($mydirname,'views')){
		$queries ="CREATE TABLE $views_table (
  		blog_id bigint(20) unsigned NOT NULL default '0',
  		post_id bigint(20) unsigned NOT NULL default '0',
  		post_views bigint(20) unsigned NOT NULL default '0',
  		KEY post_id (post_id)
		) ENGINE=MyISAM";
		$db->queryF( $queries ) ;
		$msgs[] = "$views_table table of XPressME was made.";
	} else {
		if (!is_found_table_column($views_table,'blog_id')){
			$queries ="ALTER TABLE $views_table ADD blog_id bigint(20)  FIRST";
			$db->queryF( $queries ) ;
			$msgs[] = "$views_table  ADD blog_id .";
		}
		
		// The table is repaired.
		$non_blogid_sql ="SELECT * FROM $views_table WHERE blog_id IS NULL OR blog_id < 1";
		$non_blogid_res = $db->query($non_blogid_sql, 0, 0);
		while($row = $db->fetchArray($non_blogid_res)){
			$total_view = $row['post_views'];
			$post_id = $row['post_id'];
			$new_blogid_sql ="SELECT SUM(post_views) as post_views_sum FROM $views_table WHERE post_id = $post_id AND blog_id = 1 GROUP BY post_id";
			$new_blogid_res = $db->query($new_blogid_sql, 0, 0);
			if ($db->getRowsNum($new_blogid_res)  > 0){
				$new_row = $db->fetchArray($new_blogid_res);
				$total_view = $total_view + $new_row['post_views_sum'];
				$del_sql = "DELETE FROM $views_table WHERE post_id = $post_id AND blog_id = 1";
				$db->queryF( $del_sql ) ;
			}
			$update_sql = "UPDATE $views_table SET post_views = $total_view , blog_id = 1 WHERE post_id = $post_id AND (blog_id IS NULL OR blog_id < 1)";
			$db->queryF( $update_sql ) ;
		}
	}
	
	$d3forum_link = XOOPS_DB_PREFIX . '_' . $xp_prefix .'_d3forum_link' ;
	if (! enhanced_table_check($mydirname,'d3forum_link')){
		$queries ="CREATE TABLE $d3forum_link (
	  		comment_ID bigint(20) unsigned NOT NULL default '0',
	  		post_id int(10) unsigned NOT NULL default '0' ,
	  		wp_post_ID bigint(20) unsigned NOT NULL default '0',
			forum_id bigint(20) unsigned NOT NULL default '0',
			blog_id bigint(20) unsigned NOT NULL default '0',
	  		KEY post_id (post_id)
			)";
		$db->queryF( $queries ) ;
		$msgs[] = "$d3forum_link table of XPressME was made.";
	} else {
		if (!is_found_table_column($d3forum_link,'forum_id')){
			$queries ="ALTER TABLE $d3forum_link ADD forum_id bigint(20) unsigned NOT NULL default '0' AFTER wp_post_ID";
			$db->queryF( $queries ) ;
			$msgs[] = "$d3forum_link  ADD forum_id .";
			// The table is repaired.
//			$update_sql = "UPDATE $d3forum_link SET forum_id = 1 WHERE(forum_id IS NULL OR forum_id < 1)";
//			$db->queryF( $update_sql ) ;
		}
		if (!is_found_table_column($d3forum_link,'blog_id')){
			$queries ="ALTER TABLE $d3forum_link ADD blog_id bigint(20)  unsigned NOT NULL default '0' AFTER forum_id";
			$db->queryF( $queries ) ;
			$msgs[] = "$d3forum_link  ADD blog_id .";
			// The table is repaired.
			$update_sql = "UPDATE $d3forum_link SET blog_id = 1 WHERE(blog_id IS NULL OR blog_id < 1)";
			$db->queryF( $update_sql ) ;
		}
	}

	$group_role = XOOPS_DB_PREFIX . '_' . $xp_prefix .'_group_role' ;
	if (! enhanced_table_check($mydirname,'group_role')){
		$queries ="CREATE TABLE $group_role (
	  		groupid smallint(5) unsigned NOT NULL default '0',
  			blog_id bigint(20) unsigned NOT NULL default '0',
	  		name varchar(50)  NOT NULL default '' ,
	  		description text  NOT NULL default '',
	  		group_type varchar(50)  NOT NULL default '' ,
			role varchar(20)  NOT NULL default '' ,
			login_all smallint(5) unsigned NOT NULL default '0' ,
	  		KEY groupid (groupid)
			)";
		$db->queryF( $queries ) ;
		$sql = "INSERT INTO $group_role (groupid, role) VALUES (1, 'administrator')";
		$db->queryF( $sql ) ;
		$msgs[] = "$group_role table of XPressME was made.";
	} else {
		if (!is_found_table_column($group_role,'blog_id')){
			$queries ="ALTER TABLE $group_role ADD blog_id bigint(20)  AFTER groupid";
			$db->queryF( $queries ) ;
			$msgs[] = "$group_role  ADD blog_id .";
		}
		// The table is repaired.
		$update_sql = "UPDATE $group_role SET blog_id = 1 WHERE(blog_id IS NULL OR blog_id < 1)";
		$db->queryF( $update_sql ) ;
	}
	
	if (! enhanced_table_check($mydirname,'notify_reserve')){
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
			)";
		$db->queryF( $queries ) ;
		$msgs[] = "$notify_reserve table of XPressME was made.";
	}
	return $msgs;
}
endif;

if( ! function_exists( 'enhanced_table_check' ) ) :
function enhanced_table_check($mydirname,$table_name){
		global $xoopsModule;
		
		$xoopsDB =& Database::getInstance();
		$xpress_prefix = $xoopsDB->prefix(preg_replace('/wordpress/','wp',$mydirname) . '_');
		$db_enhanced = $xpress_prefix . $table_name;

		$sql = "show tables like '$db_enhanced'";
		$res = $xoopsDB->query($sql, 0, 0);
		if ($res === false){
			return false;
		} else {
			if ($xoopsDB->getRowsNum($res)  > 0)
				return true;
			else
				return false;
		}
}
endif;

if( ! function_exists( 'is_found_table_column' ) ) :
function is_found_table_column($table,$column){
		global $xoopsModule;
		$xoopsDB =& Database::getInstance();

		$sql = "DESCRIBE $table $column";
		$res = $xoopsDB->queryF($sql, 0, 0);
		if ($res === false){
			return false;
		} else {
			if ($xoopsDB->getRowsNum($res)  > 0)
				return true;
			else
				return false;
		}
}
endif;


?>