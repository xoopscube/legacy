<?php
/*
 * Get The level from the capabillities name.
 */
function get_role_level($capabillities){
	if ($capabillities == 'default') $capabillities = get_option('default_role');;
	switch($capabillities) {
		case 'administrator':
			return 5;
		case 'editor':
			return 4;
		case 'author':
			return 3;
		case 'contributor':
			return 2;
		case 'subscriber':
			return 1;
		default:
			return 0;
	}
}

/*
 * The highest authority and the accompanying data
 * in the WordPress authority given to the group to which the user belongs are obtained. 
*/
function get_xoops_group_role($uid=0){
	if ($uid == 0){
		return '';
	}
	
	global $xoops_db;
	
	$db_groups_users_link = get_xoops_prefix() . 'groups_users_link';
	$db_group_role = get_wp_prefix() . 'group_role';
	$db_groups = get_xoops_prefix() . 'groups';

	$blog_id_list = array();
	
	if (function_exists('is_multisite') && is_multisite()){
		$blog_id_sql = "SELECT blog_id FROM $db_group_role GROUP BY blog_id";
		$blog_id_list = $xoops_db->get_results($blog_id_sql);
	} else {
		$std = new stdClass();
		$std->blog_id = 1;
		$blog_id_list[] = $std;
	}
	
	$is_multiblog = xpress_is_multiblog();
	if (defined('BLOG_ID_CURRENT_SITE')){
		$root_blog_id = BLOG_ID_CURRENT_SITE;
	} else {
		$root_blog_id = 1;
	}
	
	$ans_array = array();
	foreach($blog_id_list as $blog_id){
		$ans = array();
		$sql  = "SELECT * ";
		$sql .= "FROM $db_groups_users_link ";
		$sql .= "LEFT JOIN $db_group_role ON $db_groups_users_link.groupid = $db_group_role.groupid ";
		$sql .= "LEFT JOIN $db_groups ON $db_groups_users_link.groupid = $db_groups.groupid ";
		$sql .= "WHERE  uid = $uid AND $db_group_role.blog_id = $blog_id->blog_id";
		$user_groups = $xoops_db->get_results($sql);
		
		//default value set
		$ans['blog_id'] = $blog_id->blog_id;
		$ans['capabillities'] = '';
		$ans['allway_update'] = 0;
		
		//get maximum role
		foreach($user_groups as $user_group){
			$is_blog_root = (!$is_multiblog || ($user_group->blog_id == $root_blog_id));
			if ($user_group->group_type == 'Admin' && $is_blog_root){
				$ans['capabillities'] = 'administrator';
				if ($user_group->groupid =1){
					//It always rewrites it as WordPress adninistrator for an initial admin group of XOOPS.	
					$ans['allway_update'] = 1;
				} else {
					// admin groups other than initial admin group of XOOPS 
					// It group rewrites group_type of the data base in Admin.
					$ans['allway_update'] = $user_group->login_all; 
				}
				break;
			}
			$before_level = get_role_level($ans['capabillities']);
			
			$now_level = get_role_level($user_group->role);
			if ($now_level > $before_level){
				$ans['capabillities'] = $user_group->role;
				$ans['allway_update'] = $user_group->login_all;
			}
		}
		$ans_array[] = $ans;
	}
	return $ans_array;
}

// for Multi Blog group_role delete
function blog_group_role_delete($blog_id,$drop = false) {
	global $xoops_db;
	
	$db_group_role = get_wp_prefix() . 'group_role';
	if ($drop){
		$delsql = "DELETE FROM $db_group_role WHERE blog_id = $blog_id";
		$xoops_db->query($delsql);
	}
}

// for Multi Blog group_role add
function blog_group_role_add($blog_id,$uid = 0) {
	global $xoops_db,$xpress_config;
	$xpress_config->GroupeRoleCheck($blog_id);
}

// check user has groupe role
function has_group_role($uid = 0) {
	if (empty($uid)) return false;
	$user_roles = get_xoops_group_role($uid);
	foreach ($user_roles as $user_role){
		if (!empty($user_role['capabillities']))
			return true;
	}
	return false;
}

/*
 * WP User ID exists is checked.. 
*/
function set_user_role($uid=0,$roles,$new_user = false){
	
	foreach($roles as $role){
		$b_id = $role['blog_id'];
		$capabillities_name = $role['capabillities'];
		$allway_update = $role['allway_update'];
		if ($allway_update || $new_user ){
			$sycc_user = new WP_User($uid);
			if (function_exists('is_multisite') && is_multisite()){
				$sycc_user->for_blog($b_id);	// for Multi blog
			}
			// check user role is admin
			$is_blog_admin = false;
			$user_roles = $sycc_user->roles;
			foreach ($user_roles as $user_role){
				if (strcmp($user_role , 'administrator') == 0){
					$is_blog_admin = true;
					break;
				}
			}
			if (!$is_blog_admin){	// admin not change role
				$sycc_user->set_role($capabillities_name);
			}
		}
//		$message .= '...UPDATE ' . $xoops_user->uname . '(' . $capabillities_name . ')';
//		$message .= '...INSERT ' . $user_login_name . '(' . $capabillities_name . ')';
	}

}

/*
 * Get User ID of WordPress from the login name. 
*/
function get_wp_user_id($login_name){
	global $xoops_db;
	$sql = "SELECT ID FROM " . get_wp_prefix() . "users WHERE user_login = '$login_name'";
	$uid = $xoops_db->get_var($sql);
	return $uid;
}

/*
 * WP User ID exists is checked.. 
*/
function is_used_wp_user_id($uid){
	global $xoops_db;
	$sql = "SELECT ID FROM " . get_wp_prefix() . "users WHERE ID = $uid";
	$uid = $xoops_db->get_var($sql);
	if (empty($uid))
		return false;
	else
		return true;
}

/*
 * The user data of XOOPS is written in the WordPress user data. 
 * If $sync_uid is 0, all users are written. 
*/
function user_sync_to_wordpress($sync_uid = 0, &$message){
	global $xoops_db;

	$db_xoops_users = get_xoops_prefix() . 'users';
	$db_xpress_users = get_wp_prefix() . 'users';

	$message = 'Do Sync';

	if ($sync_uid == 0) {
		$xu_sql  = "SELECT uid ,name ,uname ,pass ,email, url, user_regdate, user_aim, user_yim FROM $db_xoops_users";
	} else {
		$xu_sql  = "SELECT uid ,name ,uname ,pass ,email, url, user_regdate, user_aim, user_yim FROM $db_xoops_users WHERE uid = $sync_uid";
	}

	$xoops_users = $xoops_db->get_results($xu_sql);
	
	if (empty($xoops_users)){
		$message .= '...ERR ('. $xu_sql . ')';
		return false;
	}

	foreach($xoops_users as $xoops_user){
		
		$wp_user_id = get_wp_user_id($xoops_user->uname) ;

		$roles = get_xoops_group_role($xoops_user->uid);

		$has_role = has_group_role($xoops_user->uid);
		if (!$has_role){
			if ($sync_uid != 0){
				if ($wp_user_id) {
					if ($allway_update){
						$message .= '...NOT XPRESS USER ' . $xoops_user->uname;
						return false;
					}
				} else {
					$message .= '...NOT XPRESS USER ' . $xoops_user->uname;
					return false;
				}
			} else {
				$message .= "...PASS '" . $xoops_user->uname ."'[uid=".$xoops_user->uid ."](not xpress user)";
				continue;
			}
		}

		$user_regist_time = date('Y-m-d H:i:s' , $xoops_user->user_regdate);
		$user_status = 0;
		$user_display_name =empty($xoops_user->name) ? $xoops_user->uname :$xoops_user->name ;

		$is_update = false;
		
		if ($wp_user_id){	
			$new_user =  false;
			$wu_sql  = 	"UPDATE $db_xpress_users ";
			$wu_sql .= 	'SET ';
			$wu_sql .=		"user_pass  = '$xoops_user->pass', ";
			$wu_sql .=		"user_email = '$xoops_user->email', ";
			$wu_sql .=		"user_url = '$xoops_user->url', ";
			$wu_sql .=		"user_nicename = '$xoops_user->uname', ";
			$wu_sql .=		"user_registered = '$user_regist_time', ";
			$wu_sql .=		"user_status = 0 ";
			$wu_sql .=	"WHERE (user_login = '$xoops_user->uname' )";

			$xoops_db->query($wu_sql);
			
			$message .= set_user_role($wp_user_id,$roles,$new_user);

			if (!check_user_meta_prefix($wp_user_id)){
				repair_user_meta_prefix();
			}

			$is_update = true;
		}else{
			$new_user =  true;
			if (is_used_wp_user_id($xoops_user->uid) ) { // WP User ID has already been used. 
				$wu_sql  =	"INSERT INTO $db_xpress_users ";
				$wu_sql .=  	"(user_login , user_pass ,user_email , user_url , user_nicename " ;
				$wu_sql .=		" , user_registered , user_status , display_name) ";
				$wu_sql .=	"VALUES ";
				$wu_sql .=		"('$xoops_user->uname', '$xoops_user->pass', '$xoops_user->email', '$xoops_user->url', '$xoops_user->uname' ";
				$wu_sql .=		" , '$user_regist_time', $user_status, '$user_display_name')";
				$xoops_db->query($wu_sql);
				$wp_user_id = mysql_insert_id();
			} else {	 // WP User ID has not been used yet. 
				$wu_sql  =	"INSERT INTO $db_xpress_users ";
				$wu_sql .=  	"(ID , user_login , user_pass ,user_email , user_url , user_nicename " ;
				$wu_sql .=		" , user_registered , user_status , display_name) ";
				$wu_sql .=	"VALUES ";
				$wu_sql .=		"('$xoops_user->uid', '$xoops_user->uname', '$xoops_user->pass', '$xoops_user->email', '$xoops_user->url', '$xoops_user->uname' ";
				$wu_sql .=		" , '$user_regist_time', $user_status, '$user_display_name')";
				$xoops_db->query($wu_sql);
				$wp_user_id = $xoops_user->uid;
			}
			$message .= set_user_role($wp_user_id,$roles,$new_user);
		}
		
		$user_nickname =	empty($xoops_user->name) ? $xoops_user->uname :$xoops_user->name ;
		$user_rich_editing = 'true';
		$user_first_name = 	$xoops_user->uname;
		$user_last_name = 	'';
		$user_description = '';
		$user_jabber = 		'';

		update_usermeta( $wp_user_id,'nickname',$user_nickname);
		update_usermeta( $wp_user_id,'first_name',$user_first_name);
		update_usermeta( $wp_user_id,'last_name',$user_last_name);
		update_usermeta( $wp_user_id,'description',$user_description);
		update_usermeta( $wp_user_id,'jabber',$user_jabber);
		update_usermeta( $wp_user_id,'aim',$xoops_user->user_aim);
		update_usermeta( $wp_user_id,'yim',$xoops_user->user_yim);
		if ($is_update === false ) {
			update_usermeta( $wp_user_id,'rich_editing',$user_rich_editing);
		} 
					
	}
	$message .= "...END";
	return true;
}

/*
 * Get User ID of XOOPS from the login name. 
*/
function get_xoops_user_id($login_name){
	global $xoops_db;
	$sql = "SELECT uid FROM " . get_xoops_prefix() . "users WHERE uname = '$login_name'";
	$uid = $xoops_db->get_var($sql);
	return $uid;	
}

/*
 *The user data of wordpress is written in the xoops user data. 
*/
function user_sync_to_xoops($user_ID){
	global $xoops_db;
	
	$user_info = get_userdata($user_ID);
	$xoops_uid = get_xoops_user_id($user_info->user_login);
	$db_xoops_users = get_xoops_prefix() . "users";
	$user_regdate  = strtotime($user_info->user_registered);
	$aim = get_usermeta($user_ID,'aim');
	$yim = get_usermeta($user_ID,'yim');	
	
	if ($xoops_uid){
		$wu_sql  = 	"UPDATE $db_xoops_users ";
		$wu_sql .= 	'SET ';
		$wu_sql .=		"uname  = '$user_info->user_login', ";
		$wu_sql .=		"pass = '$user_info->user_pass', ";
		$wu_sql .=		"email = '$user_info->user_email', ";
		$wu_sql .=		"url = '$user_info->user_url', ";
		$wu_sql .=		"name = '$user_info->display_name', ";
		$wu_sql .=		"user_aim = '$aim', ";
		$wu_sql .=		"user_yim = '$yim', ";
		$wu_sql .=		"user_regdate = $user_regdate ";
		$wu_sql .=	"WHERE (uid = $xoops_uid )";
		$xoops_db->query($wu_sql);
	}else{
		$wu_sql  =	"INSERT INTO $db_xoops_users ";
		$wu_sql .=  	"(uname , pass ,email , url , name , user_aim , user_yim , user_regdate) " ;
		$wu_sql .=	"VALUES ";
		$wu_sql .=		"('$user_info->user_login', '$user_info->user_pass', '$user_info->user_email', '$user_info->user_url', '$user_info->display_name' ";
		$wu_sql .=		" , '$aim' , '$yim' , $user_regdate )";
		$xoops_db->query($wu_sql);
		// get xoops users default groupe ID
		$db_xoops_group = get_xoops_prefix() . 'groups';
		$default_xoops_group_id = $xoops_db->get_var("SELECT groupid FROM $db_xoops_group WHERE group_type = 'User'");
		// get insert users  ID
		$db_xoops_group = get_xoops_prefix() . 'users';
		$user_id = $xoops_db->get_var("SELECT uid FROM $db_xoops_group WHERE uname = '$user_info->user_login'");
		// insert groups_users_link 
		$db_xoops_group_users_link = get_xoops_prefix() . 'groups_users_link';
		$default_xoops_group_id = $xoops_db->get_var("INSERT INTO $db_xoops_group_users_link (groupid , uid ) VALUES ($default_xoops_group_id , $user_id)");
	}
}


/*
 * When I changed the pre-fix of the database in XOOPS Protector Module, user authority data of WordPress read it and cannot do it. 
 * This is because a meta_key pre-fix of the usermeta table of WordPress is not changed.
 * and  user_roles option_name pre-fix of the option table of WordPress is not changed.
*/
function check_user_meta_prefix($uid){
	global $xoops_db;
	$db_xpress_usermeta = get_wp_prefix() . 'usermeta';
	$user_meta_prefix = get_wp_prefix();

	$sql = "SELECT * FROM $db_xpress_usermeta WHERE user_id = $uid AND meta_key = '" . $user_meta_prefix ."user_level'" ;

	$user_level = $xoops_db->get_results($sql);
	if (empty($user_level)) return false ;

	$sql = "SELECT * FROM $db_xpress_usermeta WHERE user_id = $uid AND meta_key = '" . $user_meta_prefix ."capabilities'" ;
	$capabilities = $xoops_db->get_results($sql);
	if (empty($capabilities)) return false ;
	
	return true;	
}

function repair_user_meta_prefix(){
	global $xoops_db;
	// repair usermeta db
	$db_xpress_usermeta = get_wp_prefix() . 'usermeta';
	$wp_prefix_only = get_wp_prefix_only();
	$user_meta_prefix = get_wp_prefix();
	$sql = "SELECT * FROM $db_xpress_usermeta WHERE meta_key LIKE '%_" . $wp_prefix_only . "%'" ;
	$user_metas = $xoops_db->get_results($sql);
	if(!empty($user_metas)){
		foreach($user_metas as $user_meta){
			if (strpos($user_meta->meta_key,$user_meta_prefix) === false) {
				$new_meta_key = '';
				if (strpos($user_meta->meta_key,$wp_prefix_only.'user_level'))
					$new_meta_key = $user_meta_prefix . 'user_level';
				if (strpos($user_meta->meta_key,$wp_prefix_only.'capabilities'))
					$new_meta_key = $user_meta_prefix . 'capabilities';
				if (strpos($user_meta->meta_key,$wp_prefix_only.'autosave_draft_ids'))
					$new_meta_key = $user_meta_prefix . 'autosave_draft_ids';
				if (strpos($user_meta->meta_key,$wp_prefix_only.'usersettings')){
					if (strpos($user_meta->meta_key,$wp_prefix_only.'usersettingstime'))
						$new_meta_key = $user_meta_prefix . 'usersettingstime';
					else
						$new_meta_key = $user_meta_prefix . 'usersettings';
				}
				if (!empty($new_meta_key)){
					$repair_sql  = 	"UPDATE $db_xpress_usermeta ";
					$repair_sql .= 	'SET ';
					$repair_sql .=	"meta_key = '$new_meta_key' ";
					$repair_sql .=	"WHERE (umeta_id = $user_meta->umeta_id )";
					$xoops_db->query($repair_sql);
				}
			}
		}
	}
	
	// repair option db user_roles
	include_once (ABSPATH . '/include/general_functions.php');

	$prefix = get_wp_prefix();
	$option_tables = get_table_list($prefix,'options');
	if(!empty($option_tables)){
		foreach( $option_tables as $option_table){
			$mid_prefix = get_multi_mid_prefix($prefix,'options' , $option_table);

			$new_option_name = $prefix .$mid_prefix . 'user_roles';
			$sql = "SELECT option_id , option_name FROM $option_table WHERE option_name LIKE '%_user_roles'" ;
			$option= $xoops_db->get_row($sql);
			if ($option->option_name != $new_option_name){
				$repair_sql  = 	"UPDATE $db_wp_option ";
				$repair_sql .= 	'SET ';
				$repair_sql .=	"option_name = '$new_option_name' ";
				$repair_sql .=	"WHERE (option_id = $option->option_id )";
				$xoops_db->query($repair_sql);
			}
		}
	}
}
?>