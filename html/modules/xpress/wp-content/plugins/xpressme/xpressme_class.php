<?php

load_plugin_textdomain('xpressme', 'wp-content/plugins/xpressme/language');

class XPressME_Class{
	var $pluginName = 'xpressme';	
	var $is_use_xoops_upload_path;
	var $is_theme_sidebar_disp;
	var $is_save_post_revision;
	var $is_postnavi_title_disp;
	var $is_left_postnavi_old;
	var $old_post_link_text;
	var $newer_post_link_text;
	var $is_left_page_navi_old;
	var $old_page_link_text;
	var $newer_page_link_text;
	var $is_author_view_count;
	var $is_sql_debug;
	var $groupe_role;
	var $is_use_d3forum;
	var $d3forum_module_dir;
	var $d3forum_forum_id;
	var $d3forum_external_link_format;
	var $is_content_excerpt;
	var $ascii_judged_rate;
	var $excerpt_length_word;
	var $excerpt_length_character;
	var $excerpt_more_link_text;
	var $more_link_text;
	var $viewer_type;
	var $is_multi_user;
	var $meta_keyword_type;
	var $meta_description_type;
	var $meta_robot_type;
	var $is_dashboard_blog_disp;
	var $is_dashboard_forum_disp;
	var $theme_select;
	var $is_block_error_display;
	var $admin_set_all_blog_admin;
	var $post_left_arrow_image_link;
	var $post_right_arrow_image_link;
	var $page_left_arrow_image_link;
	var $page_right_arrow_image_link;
	
	//constructor
	function XPressME_Class()
	{
		global $xoops_db;
		
		$this->setdefault();    //not setting propaty load
		$this->SettingValueRead();
	}
	
	//Set Default Value	
	function setDefault()
	{
		global $xoops_config;
		$this->is_use_xoops_upload_path = true;
		$this->is_use_xoops_upload_path = true;
		$this->is_theme_sidebar_disp = false;
		$this->is_save_post_revision = true;
		$this->is_postnavi_title_disp = true;
		$this->is_left_postnavi_old = true;
		$this->old_post_link_text = __('Older Post', 'xpressme');
		$this->newer_post_link_text = __('Newer Post', 'xpressme');
		$this->is_left_page_navi_old = true;
		$this->old_page_link_text = __('Older Entries', 'xpressme');
		$this->newer_page_link_text = __('Newer Entries', 'xpressme');
		$this->is_author_view_count = false;
		$this->is_sql_debug = false;
		$this->is_use_d3forum = false;
		$this->d3forum_module_dir = '';
		$this->d3forum_forum_id = '';
		$this->d3forum_external_link_format = get_xpress_dir_name() . '::xpressD3commentContent';
		$this->is_d3forum_flat = true;
		$this->is_d3forum_desc = true;
		$this->d3forum_views_num = 10;
		$this->is_content_excerpt = true;
		$this->ascii_judged_rate = 90;
		$this->excerpt_length_word = 40;
		$this->excerpt_length_character = 120;
		$this->excerpt_more_link_text = __('Read the rest of this entry &raquo;', 'xpressme');
		$this->more_link_text = __('Read the rest of this entry &raquo;', 'xpressme');
		$this->viewer_type = 'xoops';
		$this->is_multi_user = false;
		$this->meta_keyword_type = 'xoops';
		$this->meta_description_type = 'xoops';
		$this->meta_robot_type = 'xoops';	
		$this->is_dashboard_blog_disp = true;
		$this->is_dashboard_forum_disp = true;
		$this->theme_select = 'use_wordpress_select';
		$this->is_block_error_display = true;
		$this->admin_set_all_blog_admin = false;
		$this->post_left_arrow_image_link = '';
		$this->post_right_arrow_image_link = '';
		$this->page_left_arrow_image_link = '';
		$this->page_right_arrow_image_link = '';
	}
	
	function SettingValueRead()
	{
		global $xoops_db;
		$options = get_option('xpressme_option');
		if (!$options) {
			$this->setDefault();
			$this->SettingValueWrite('add_new');
		} else {
			foreach ($options as $option_name => $option_value){
	        		$this-> {$option_name} = $option_value;
			}
		}
		if (!empty($xoops_db))	// at install trap
			$this->GroupeRoleRead();
	}
	
		// mode 0:add  1:update	
	function SettingValueWrite($mode)
	{
		global $xoops_config;

		$write_options = array (
			'is_use_xoops_upload_path' => $this->is_use_xoops_upload_path ,
			'is_theme_sidebar_disp' => $this->is_theme_sidebar_disp ,
			'is_save_post_revision' => $this->is_save_post_revision ,
			'is_postnavi_title_disp' => $this->is_postnavi_title_disp ,
			'is_left_postnavi_old' => $this->is_left_postnavi_old ,
			'old_post_link_text' => $this->old_post_link_text ,
			'newer_post_link_text' => $this->newer_post_link_text,
			'is_left_page_navi_old' => $this->is_left_page_navi_old ,
			'old_page_link_text' => $this->old_page_link_text ,
			'newer_page_link_text' => $this->newer_page_link_text,
			'is_author_view_count' => $this->is_author_view_count,
			'is_sql_debug' => $this->is_sql_debug,
			'is_use_d3forum' =>	$this->is_use_d3forum,
			'd3forum_module_dir' => $this->d3forum_module_dir,
			'd3forum_forum_id' => $this->d3forum_forum_id,
			'd3forum_external_link_format' => $this->d3forum_external_link_format,
			'is_d3forum_flat' => $this->is_d3forum_flat,
			'is_d3forum_desc' => $this->is_d3forum_desc,
			'd3forum_views_num' =>$this->d3forum_views_num,
			'is_content_excerpt' => $this->is_content_excerpt,
			'ascii_judged_rate' => $this->ascii_judged_rate,
			'excerpt_length_word' => $this->excerpt_length_word,
			'excerpt_length_character' => $this->excerpt_length_character,
			'excerpt_more_link_text' => $this->excerpt_more_link_text,
			'more_link_text' => $this->more_link_text,
			'viewer_type' => $this->viewer_type,
			'is_multi_user' => $this->is_multi_user,
			'meta_keyword_type' => $this->meta_keyword_type,
			'meta_description_type' => $this->meta_description_type,
			'meta_robot_type' => $this->meta_robot_type,
			'is_dashboard_blog_disp' => $this->is_dashboard_blog_disp,
			'is_dashboard_forum_disp' => $this->is_dashboard_forum_disp,
			'theme_select' => $this->theme_select,
			'is_block_error_display' => $this->is_block_error_display,
			'admin_set_all_blog_admin' => $this->admin_set_all_blog_admin,
			'post_left_arrow_image_link' => $this->post_left_arrow_image_link,
			'post_right_arrow_image_link' => $this->post_right_arrow_image_link,
			'page_left_arrow_image_link' => $this->page_left_arrow_image_link,
			'page_right_arrow_image_link' => $this->page_right_arrow_image_link
		);
		if ($mode == 'add_new') {
			add_option('xpressme_option', $write_options);
		} else {			
			update_option("xpressme_option", $write_options);
		}
	}
	
	function get_current_setting_option($option_name)
	{
		if (empty($option_name)) return null;
		if (defined('BLOG_ID_CURRENT_SITE')){
				$id = BLOG_ID_CURRENT_SITE;
		} else {
				$id = 1;
		}
		if (xpress_is_multiblog() && !xpress_is_multiblog_root()){
			switch_to_blog($id);
			$options = get_option('xpressme_option');
			restore_current_blog();
		} else {
			$options = get_option('xpressme_option');
		}
		$ret = $options[$option_name];
		return $ret;
	}
	
	function admin_select_groupe_role() {
		if (xpress_is_multiblog_root()) return false;
		return !$this->get_current_setting_option('admin_set_all_blog_admin');
	}
	
	function GroupeRoleRead() {
 		global $xoops_db, $blog_id;
		
		if (empty($blog_id)) {
			if (defined(BLOG_ID_CURRENT_SITE)){
				$blog_id = BLOG_ID_CURRENT_SITE;
			} else {
				$blog_id = 1;
			}
		}
		$table = get_wp_prefix() . 'group_role';
		
		$sql=  "SELECT * FROM $table WHERE blog_id = $blog_id ORDER BY groupid";
		$this->groupe_role =  $xoops_db->get_results($sql);
	}

	function GroupeRoleCheck($blog_id = 0) {
 		global $xoops_db;
		
		if (empty($blog_id)) {
			if (defined('BLOG_ID_CURRENT_SITE')){
				$blog_id = BLOG_ID_CURRENT_SITE;
			} else {
				$blog_id = 1;
			}
		}
		
		if ( xpress_is_multiblog() && $blog_id == BLOG_ID_CURRENT_SITE){
			$set_blog_admin = true;
		} else {
			$set_blog_admin = !$this->admin_select_groupe_role();
		}

		$module_id = get_xpress_modid();
		
		$group_role_table = get_wp_prefix() . 'group_role';
		$xoops_group_table = get_xoops_prefix() . 'groups';
		$xoops_group_permission_table = get_xoops_prefix() . 'group_permission';
		
		$sql =  "SELECT *  FROM $xoops_group_permission_table WHERE gperm_itemid = $module_id";
		$gperms = $xoops_db->get_results($sql);
		
		$sql =  "SELECT * FROM $xoops_group_table WHERE group_type <> 'Anonymous' ORDER BY groupid";
		$groupes = $xoops_db->get_results($sql);

		// list of groups registered with XOOPS
		$xoops_groupid_list = '';
		foreach ($groupes as $groupe) {
			if (!empty($xoops_groupid_list)) $xoops_groupid_list .= ',';
			$xoops_groupid_list .= $groupe->groupid;
		}
		
		// delete the group deleted by the XOOPS group from a group role database
		if (!empty($xoops_groupid_list)){
			$del_sql = "DELETE FROM $group_role_table WHERE groupid NOT IN ($xoops_groupid_list)";
			$xoops_db->query($del_sql);
		}
		
		$sql =  "SELECT *  FROM $group_role_table WHERE blog_id = $blog_id";
		$groupes_role = $xoops_db->get_results($sql);
		
		foreach ($groupes as $groupe) {
			//get group parmission
			$group_type = '';
			foreach ($gperms as $gperm) {
				if ($gperm->gperm_groupid == $groupe->groupid){
					$group_type = $gperm->gperm_name;
					if ($group_type == 'module_admin') break;
				}
			}
			if (empty($group_type)) $group_type = 'module_inhibit';
			
			$found = false;
			foreach ($groupes_role as $groupe_role) {
				if ($groupe_role->groupid == $groupe->groupid){
					$role = $groupe_role->role;
					if ($group_type == 'module_admin' && $set_blog_admin) $role = 'administrator';
					if ($group_type == 'module_inhibit') $role = '';
					$edit_sql = "UPDATE $group_role_table SET group_type='$group_type',role='$role' WHERE groupid = $groupe->groupid AND blog_id = $blog_id";
					$found = true;
					break;
				}
			}
			if(!$found){
				$role = '';
				if ($group_type == 'module_admin') $role = 'administrator';

				$edit_sql  = "INSERT INTO  $group_role_table ";
				$edit_sql .= "(groupid , blog_id , name , description , group_type , role , login_all) ";
				$edit_sql .= "VALUES (";
				$edit_sql .= $groupe->groupid . ', ';
				$edit_sql .= $blog_id . ', ';
				$edit_sql .= "'" . $groupe->name . "' , ";
				$edit_sql .= "'" . $groupe->description . "' , ";
				$edit_sql .= "'" . $group_type . "' , ";
				$edit_sql .= "'" . $role . "' , '";
				$edit_sql .= $login_all . "')";
			}
			$xoops_db->query($edit_sql);
		}
		$this->GroupeRoleRead();
	}
	
	function get_groupe_perm_for_modules($module_id ,$group_id)
	{
		$parmsql =  "SELECT *  FROM $xoops_group_permission WHERE gperm_itemid = $module_id AND gperm_groupid = $group_id";
		$gperms = $xoops_db->get_results($parmsql);
		$parmission = '';
		foreach ($gperms as $gperm) {
				$parmission = $gperm->gperm_name;
				if ($parmission == 'module_admin') break;
		}
		return $parmission;
	}
	
	function D3Forum_old_Link_clear($value = null){
		global $xpress_config,$xoops_db;
		if ($this->is_use_d3forum){
			$d3forum_forum_tbl = get_xoops_prefix() . $this->d3forum_module_dir ."_forums";
			$d3forum_external_link_format = '';
			$d3f_forum_id = $this->d3forum_forum_id;
			
			if ($value === 'none'){
				$xoops_db->query( "UPDATE ".$d3forum_forum_tbl ." SET forum_external_link_format='' WHERE forum_id= $d3f_forum_id" ) ;
				$this->D3forum_user_access_set($this->d3forum_module_dir,$this->d3forum_forum_id, 0);
			} else {
				$d3f_set = explode('|', $value);
				if ($this->d3forum_module_dir !== $d3f_set[1] || $this->d3forum_forum_id !== $d3f_set[2]){
					$xoops_db->query( "UPDATE ".$d3forum_forum_tbl ." SET forum_external_link_format='' WHERE forum_id= $d3f_forum_id" ) ;
					$this->D3forum_user_access_set($this->d3forum_module_dir,$this->d3forum_forum_id, 0);
				}
			}
		}
	}
	function D3Forum_link_set(){
		global $xoops_db;

		if (empty($this->is_use_d3forum)) return;
		$d3forum_forum_tbl = get_xoops_prefix() . $this->d3forum_module_dir ."_forums";
		$d3f_forum_id = $this->d3forum_forum_id;
		$forum_external_link_format = addslashes($this->d3forum_external_link_format);
		$xoops_db->query( "UPDATE ".$d3forum_forum_tbl ." SET forum_external_link_format='".$forum_external_link_format."' WHERE forum_id= $d3f_forum_id" ) ;
		$this->D3forum_user_access_set($this->d3forum_module_dir,$this->d3forum_forum_id,1);

	}
	
	function D3forum_user_access_set($forum_module_dir,$forum_id,$accsess = 0){
		global $xoops_db ,$user_login;
		
		$user_id = get_xoops_user_id($user_login);
		$d3forum_forum_access_tbl = get_xoops_prefix() . $this->d3forum_module_dir ."_forum_access";
		if (!$accsess){
			$sql  = "DELETE FROM $d3forum_forum_access_tbl WHERE forum_id = $forum_id AND uid = $user_id";
			$xoops_db->query($sql);
		} else {
			$sql = "SELECT * FROM $d3forum_forum_access_tbl WHERE forum_id = $forum_id AND uid = $user_id";
			$row = $xoops_db->get_row($sql);
			if (!$row){
				$sql  = "INSERT INTO $d3forum_forum_access_tbl ";
				$sql .=    "(forum_id, uid, can_post, can_edit, can_delete, post_auto_approved, is_moderator) ";
				$sql .=  "VALUES ";
				$sql .=    "($forum_id, $user_id, 1, 1, 1, 1, 1)";
				$xoops_db->query($sql);
			}
		}
	}
	function D3Forum_create_new($d3forum_module_dir,$cat_id,$title){
		global $xoops_db;
		$d3forum_forum_tbl = get_xoops_prefix() . $d3forum_module_dir ."_forums";
		$d3forum_forum_access_tbl = get_xoops_prefix() . $d3forum_module_dir ."_forum_access";
		$sql  = "INSERT INTO $d3forum_forum_tbl ";
		$sql .=    "(cat_id, forum_desc, forum_title,forum_options) ";
		$sql .=  "VALUES ";
		$sql .=    "('$cat_id', '', '$title','a:0:{}')";
		$xoops_db->query($sql);
		$insert_forum_id = mysql_insert_id();
		$sql  = "INSERT INTO $d3forum_forum_access_tbl ";
		$sql .=    "(forum_id, groupid, can_post, can_edit, can_delete, post_auto_approved, is_moderator) ";
		$sql .=  "VALUES ";
		$sql .=    "($insert_forum_id, 1, 1, 1, 1, 1, 1)";
		$xoops_db->query($sql);
		return $insert_forum_id;
	}
	
	function ReadPostData($post_data = null)
	{
		global $xoops_db, $blog_id;
		
		if (empty($blog_id)) {
			if (defined(BLOG_ID_CURRENT_SITE)){
				$blog_id = BLOG_ID_CURRENT_SITE;
			} else {
				$blog_id = 1;
			}
		}
		foreach ( (array) $post_data as $index_key => $value ){
			if (preg_match('/^ch_/',$index_key)){  // case ch_
				$indedx = preg_replace('/^ch_/', '', $index_key);
				$set_value = stripslashes(trim($value));
				// post d3forum
				if ($indedx === 'd3forum') {
					$this->D3Forum_old_Link_clear($value);
					if ($value == 'none'){
						$this->is_use_d3forum = false;
						$this->d3forum_module_dir = '';
						$this->d3forum_forum_id = '';
						$this->d3forum_external_link_format = get_xpress_dir_name() . '::xpressD3commentContent';
					} else {
						$d3f_set = explode('|', $value);
						$this->is_use_d3forum = true;
						$this->d3forum_module_dir = $d3f_set[1];
						if (preg_match('/Create New In Cat=([0-9]*)/',$d3f_set[2],$matchs)){
							$cat_id = $matchs[1];
							$title = get_option('blogname');
							$this->d3forum_forum_id = $this->D3Forum_create_new($this->d3forum_module_dir,$cat_id,$title);
						} else {
							$this->d3forum_forum_id = $d3f_set[2];
						}
						$this->d3forum_external_link_format = get_xpress_dir_name() . '::xpressD3commentContent';
						$this->D3Forum_link_set();
					}
				} else { //post other
					if(empty($set_value)){
						switch ($indedx) {
							case 'old_post_link_text':
								$set_value = __('Older Post', 'xpressme');
								break;
							case 'newer_post_link_text':
								$set_value = __('Newer Post', 'xpressme');
								break;
							case 'old_page_link_text':
								$set_value = __('Older Entries', 'xpressme');
								break;
							case 'newer_page_link_text':
								$set_value = __('Newer Entries', 'xpressme');
								break;
							case 'excerpt_more_link_text':
								$set_value = __('Read the rest of this entry &raquo;', 'xpressme');
								break;
							case 'theme_select':
								$set_value = 'use_wordpress_select';
								break;
								
							default:
						}
					}
					$this->$indedx = $value;
				}
			} // end of case 'ch_'
		} // end of loop

		global $xoops_config;

		$table = get_wp_prefix() . 'group_role';	
//		$sql=  "SELECT * FROM $table";	
//		$this->groupe_role =  $xoops_db->get_results($sql);  // before Read
		
		foreach ($this->groupe_role as $groupe) {
			$post_role_gid = 'role_gid_' . $groupe->groupid;
			$login_all_gid = 'login_all_gid_' . $groupe->groupid;
			if (isset($post_data[$post_role_gid])){
				$role = stripslashes(trim($post_data[$post_role_gid]));
				$login_all = stripslashes(trim($post_data[$login_all_gid]));
				if (empty($login_all)) $login_all = '0';
				$groupe->role = $role;
				$groupe->login_all = $login_all;
				$update_sql  = "UPDATE  $table ";
				$update_sql .= 'SET ';
				$update_sql .= "role  = '$role' , ";
				$update_sql .= "login_all  = $login_all ";
				$update_sql .= "WHERE (groupid = '$groupe->groupid' AND blog_id = $blog_id)";
				$xoops_db->query($update_sql);	
			}		
		}
	}
	
	function yes_no_radio_option($option_name,$option_desc,$yes = '',$no= '' , $disible=false){
		if (empty( $yes ))  $yes = __('YES','xpressme') ;
		if (empty( $no ))  $no = __('NO','xpressme') ;
		$value = $this->{$option_name};
		$ans_name = 'ch_' . $option_name;
		
		$form  =  "<tr>\n";
		$form .=  '<th><label for="images_to_link">' . $option_desc . "</label></th>\n";
		$form .=  "<td>\n";
		$form .=  $this->yes_no_radio_option_sub($option_name,$yes,$no,$disible);
		$form .=  "</td>\n";
		$form .=  "</tr>\n";
			
	    return $form;
	
	}
	function yes_no_radio_option_sub($option_name,$yes = '',$no= '',$disible=false){
		if ($disible) $disible_str = ' disabled="disabled"'; else $disible_str = '';

		if (empty( $yes ))  $yes = __('YES','xpressme') ;
		if (empty( $no ))  $no = __('NO','xpressme') ;
		$value = $this->{$option_name};
		$ans_name = 'ch_' . $option_name;
		if ($value){
			$form .= "<label><input type='radio' name='". $ans_name . "' value='1' checked='checked' $disible_str />" . $yes ."</label><br />\n";
			$form .= "<label><input type='radio' name='". $ans_name . "' value='0' $disible_str />". $no . "</label>\n";
		}else{
			$form .= "<label><input type='radio' name='". $ans_name . "' value='1' $disible_str />" . $yes . "</label><br />\n";
			$form .= "<label><input type='radio' name='". $ans_name . "' value='0' checked='checked' $disible_str />". $no ."</label>\n";
		}
	    return $form;
	}


	function text_option($option_name,$option_desc,$size = 25,$maxlength = 50){
		$value = $this->{$option_name};
		$ans_name = 'ch_' . $option_name;
		
		$form  =  "<tr>\n";
		$form .=  '<th><label for="images_to_link">' . $option_desc . "</label></th>\n";
		$form .=  "<td>\n";
		$form .= $this->text_option_sub($option_name,$size,$maxlength);
		$form .=  "</td>\n";
		$form .=  "</tr>\n";
			
	    return $form;
	
	}
	
	function text_option_sub($option_name,$size = 25,$maxlength = 50){
		$value = $this->{$option_name};
		$ans_name = 'ch_' . $option_name;
		
		$form = '<label> <input name="'. $ans_name . '" type="text" size="'.$size.'" maxlength="'.$maxlength.'" value="'  . $value . '" /></label>'."\n";
	    return $form;
	
	}

	
	function single_post_navi_option(){
		$form = '';
		$form .= '<tr><th><label for="single_page_navi">' .__('Single Post Navi Setting', 'xpressme') . '</label></th>';
		$form .= "<td>\n";
		$form .= "<table>\n";
		$form .= "<tr>\n";
		
		$form .= "<td>" . __('Adjustment of Navi link display position','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->yes_no_radio_option_sub('is_left_postnavi_old',
												__("'Old Post Link' is displayed in the left, and 'Newer Post Link' is displayed in the right",'xpressme'),
												__("'Newer Post Link' is displayed in the left, and 'Old Post Link' is displayed in the right",'xpressme')
												);
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('Select Display name of PostNavi Link','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->yes_no_radio_option_sub('is_postnavi_title_disp',
												__('Title of post','xpressme'),
												__('Title of Navi','xpressme')
												);
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('Display Navi Title of Old Post Link','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('old_post_link_text');
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('Display Navi Title of Newer Post Link','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('newer_post_link_text');
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('Left arrow image src','xpressme');		
		if(!empty($this->post_left_arrow_image_link)){
			if (icon_exists($this->post_left_arrow_image_link))
				$form .= "&emsp;<img src=\"$this->post_left_arrow_image_link\" align=\"absmiddle\"/>";
			else
				$form .= "&emsp;<span style=\"color: red\">(" . __('Not Found','xpressme') .")</span>";				
		}
		$form .= "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('post_left_arrow_image_link',60,120);
		$form .= "</td>\n";
		$form .= "</tr>\n";
		$form .= "<tr>\n";

		$form .= "<tr>\n";
		$form .= "<td>" . __('Right arrow image src','xpressme');		
		if(!empty($this->post_right_arrow_image_link)){
			if (icon_exists($this->post_right_arrow_image_link))
				$form .= "&emsp;<img src=\"$this->post_right_arrow_image_link\" align=\"absmiddle\"/>";
			else
				$form .= "&emsp;<span style=\"color: red\">(" . __('Not Found','xpressme') .")</span>";				
		}

		$form .= "<td>\n";
		$form .=  $this->text_option_sub('post_right_arrow_image_link',60,120);
		$form .= "</td>\n";
		$form .= "</tr>\n";
		$form .= "<tr>\n";

		$form .= "</table></td></tr>\n";
	    return $form;

	}

	function posts_page_navi_option(){
		$form = '';
		$form .= '<tr><th><label for="posts_page_navi">' .__('Posts List Page Navi Setting', 'xpressme') . '</label></th>';
		$form .= "<td>\n";
		$form .= "<table>\n";
		$form .= "<tr>\n";
		
		$form .= "<td>" . __('Adjustment of Navi link display position','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->yes_no_radio_option_sub('is_left_page_navi_old',
												__("'Old Page Link' is displayed in the left, and 'Newer Page Link' is displayed in the right",'xpressme'),
												__("'Newer Page Link' is displayed in the left, and 'Old Page Link' is displayed in the right",'xpressme')
												);
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('Display Navi Title of Old Page Link','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('old_page_link_text');
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('Display Navi Title of Newer Page Link','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('newer_page_link_text');
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('Left arrow image src','xpressme');		
		if(!empty($this->page_left_arrow_image_link)){
			if (icon_exists($this->page_left_arrow_image_link))
				$form .= "&emsp;<img src=\"$this->page_left_arrow_image_link\" align=\"absmiddle\"/>";
			else
				$form .= "&emsp;<span style=\"color: red\">(" . __('Not Found','xpressme') .")</span>";				
		}
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('page_left_arrow_image_link',60,120);
		$form .= "</td>\n";
		$form .= "</tr>\n";

		$form .= "<tr>\n";
		$form .= "<td>" . __('Right arrow image src','xpressme');		
		if(!empty($this->page_right_arrow_image_link)){
			if (icon_exists($this->page_right_arrow_image_link))
				$form .= "&emsp;<img src=\"$this->page_right_arrow_image_link\" align=\"absmiddle\"/>";
			else
				$form .= "&emsp;<span style=\"color: red\">(" . __('Not Found','xpressme') .")</span>";				
		}
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('page_right_arrow_image_link',60,120);
		$form .= "</td>\n";
		$form .= "</tr>\n";

		$form .= "</table></td></tr>\n";
	    return $form;

	}
	
	function dashboard_display_option(){
		$form = '';
		$form .= '<tr><th><label for="posts_page_navi">' .__('Dashboard feed Display Setting', 'xpressme') . '</label></th>';
		$form .= "<td>\n";
		$form .= "<table>\n";
		
		$form .= "<tr>\n";
		
		$form .= "<td>" . __('Display XPressMe Integration Kit Blog','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->yes_no_radio_option_sub('is_dashboard_blog_disp',
												__('YES','xpressme'),
												__('NO','xpressme')
												);
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		
		$form .= "<td>" . __('Display XPressMe Integration Kit Forum','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->yes_no_radio_option_sub('is_dashboard_forum_disp',
												__('YES','xpressme'),
												__('NO','xpressme')
												);
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "</table></td></tr>\n";
	    return $form;
	}
	
	function groupe_role_option($disible=false){
		global $wp_roles , $xoops_db;
		
		if ($disible) $disible_str = ' disabled="disabled"'; else $disible_str = '';
		$form = '';
		$form .= '<tr><th><label for="role">' .__('Role Setting at Login', 'xpressme') . '</label></th>';
		$form .= '<td>';
		$form .= "<table>\n";
		$form .= "<tr>\n";
		
		if (xpress_is_multiblog_root()){
			$form .= "<td>" . __('XOOPS administrators role is set as all blog administrators.','xpressme') . "</td>\n";		
			$form .= "<td>\n";
			$form .=  $this->yes_no_radio_option_sub('admin_set_all_blog_admin',
													__('YES','xpressme'),
													__('NO','xpressme')
													);
			$form .= "</td>\n";
			$form .= "</tr>\n";
		}
		$form .= '<tr><td>' . __('XOOPS Groupe', 'xpressme') . '</td><td>' . __('WordPress Role', 'xpressme') . '</td><td>' . __('Role is set at each login', 'xpressme') . "</td></tr>\n";
		foreach ($this->groupe_role as $groupe) {
			if ($groupe->group_type == 'module_inhibit'){
				$form .= "<tr>";
				$form .= "<td> $groupe->name </td>";
				$form .= "<td>" .  __('module cannot be read', 'xpressme') . "</td>";
				$form .= "</tr>\n";	
				continue;
			}
			$form .= "<tr>";
			$form .= "<td> $groupe->name </td>";
			$form .= "<td>\n" . '<select name="role_gid_'.$groupe->groupid . '" id="role_gid_' . $groupe->groupid . '"' . $disible_str . '>' . "\n";;
			$role_list = '';
			$group_has_role = false;
		
			$select_value = $groupe->role;


				
			foreach($wp_roles->role_names as $role => $name) {
				if(function_exists('translate_user_role')){
					$name = translate_user_role($name);
				} else {
					$name = translate_with_context($name);
				}
				if ( $role == $select_value) {
					$selected = ' selected="selected"';
					$group_has_role = true;
				} else {
					$selected = '';
				}
				
				$admin_select_role = $this->admin_select_groupe_role();
				
				if ($admin_select_role || $groupe->group_type != 'module_admin'|| !empty($selected)) {
					$role_list .= "<option value=\"{$role}\"{$selected}>{$name}</option>\n";
				}
				if (!$admin_select_role && $groupe->group_type == 'module_admin'){
					if ($role == 'administrator'){
						$role_list .= "<option value=\"{$role}\" selected=\"selected\">{$name}</option>\n";
					}
				}
			}
			
			if ($this->admin_select_groupe_role() ||$groupe->group_type != 'module_admin') {
				if ( $group_has_role ) {
					$role_list .= '<option value="default">' . __('Default Role of WordPress', 'xpressme') . "</option>\n";
					$role_list .= '<option value="">' . __('Group User Doesn\'t Register', 'xpressme') . "</option>\n";
				} else {
					if ($select_value == 'default'){
						$role_list .= '<option value="default" selected="selected">' . __('Default Role of WordPress', 'xpressme') . "</option>\n";	
						$role_list .= '<option value="">' . __('Group User Doesn\'t Register', 'xpressme') . "</option>\n";
					} else {
						$role_list .= '<option value="default">' . __('Default Role of WordPress', 'xpressme') . "</option>\n";					
						$role_list .= '<option value="" selected="selected">' . __('Group User Doesn\'t Register', 'xpressme') . "</option>\n";
					}
				}
			}
			$form .= $role_list . "</select>\n</td>";
			if ($groupe->login_all){
				$form .= '<td> <input type="checkbox" name="login_all_gid_' . $groupe->groupid . '" value="1" checked ></td>';
			} else {
				$form .= '<td> <input type="checkbox" name="login_all_gid_' . $groupe->groupid . '" value="1"></td>';
			}
			$form .= "</tr>\n";	
		}
		if ($disible)
			$form .= '<tr><p>' . __('Only the Admin can set Group Role Setting','xpressme') . "</p></tr>\n";
		$form .= "</table></td></tr>\n";
	    return $form;

	}
	
	function d3forum_option($do_message = ''){
		global $xoops_db,$xoops_config;
		
		$multi_blog_use_d3forum = true;
		
		$d3frum_list = array();
		$module_dir_path = get_xoops_root_path();
		
		$forum_list  = '<select name="ch_d3forum">' . "\n";
		
		if ($this->is_use_d3forum != true)
			$selected = ' selected="selected"';
		else
			$selected = '';
		
		if (xpress_is_multiblog() && !$multi_blog_use_d3forum) {
			$forum_list .= '<option value="none"' . $selected . '>' . __('WordPress MultiBlog cannot integrate the comments.', 'xpressme') . "</option>\n";
		} else {	
			$forum_list .= '<option value="none"' . $selected . '>' . __('Do Not Comment Integration.', 'xpressme') . "</option>\n";

			// Form making for forum selection of D3forum
			$modules_table = get_xoops_prefix() .'modules';
			$sql = "SELECT mid,name,isactive,dirname FROM $modules_table WHERE isactive = 1";
			$modules = $xoops_db->get_results($sql);
			foreach ($modules as $module) {
				$file_path = $module_dir_path . '/modules/' . $module->dirname . '/mytrustdirname.php';			
				if (! file_exists($file_path)) continue;
				$array_files = file($file_path);
				// It is checked whether there is character string "$mytrustdirname ='d3forum'"in the file.
				foreach ($array_files as $aeey_file){
					if( preg_match( "/\s*(mytrustdirname)\s*(=)\s*([\"'])(d3forum)([\"'])/", $aeey_file ) ) {
						$forums_tb = get_xoops_prefix() . $module->dirname . '_forums';
						$cat_tb = get_xoops_prefix() . $module->dirname . '_categories';
						$cat_sql = "SELECT * FROM $cat_tb";
						$cats = $xoops_db->get_results($cat_sql);
						foreach ($cats as $cat) {
							$sql= "SELECT * FROM $forums_tb WHERE $forums_tb.cat_id = $cat->cat_id";
							$forums = $xoops_db->get_results($sql);
							foreach ($forums as $forum) {
								if (($module->dirname == $this->d3forum_module_dir) &&  ($forum->forum_id == $this->d3forum_forum_id)){
									$selected = ' selected="selected"';
									$forum_div = 'forum|' . $module->dirname . '|' .  $forum->forum_id;
									$forum_select = "$module->name($module->dirname) $cat->cat_title-$forum->forum_title(ID=$forum->forum_id)";
									$forum_list .= '<option value="' . $forum_div . '" ' . $selected . '>' . $forum_select . "</option>\n";
								} else if (empty($forum->forum_external_link_format)){
									$selected = '';
									$forum_div = 'forum|' . $module->dirname . '|' .  $forum->forum_id;
									$forum_select = "$module->name($module->dirname) $cat->cat_title-$forum->forum_title(ID=$forum->forum_id)";
									$forum_list .= '<option value="' . $forum_div . '" ' . $selected . '>' . $forum_select . "</option>\n";
								}
							}
							$selected = '';
							$forum_div = 'forum|' . $module->dirname . '|Create New In Cat=' .  $cat->cat_id;
							$forum_select = "$module->name($module->dirname) $cat->cat_title-" . __('Create New Forum', 'xpressme');
							$forum_list .= '<option value="' . $forum_div . '" ' . $selected . '>' . $forum_select . "</option>\n";
						}
						break;
					}
				}
				$forum_list .= '<br>';			
			}
		}
		$forum_list .= '</select>' . "\n";

		$form  = '<tr>' . "\n";
		$form .= '<th><label for="d3forum">' .__('Comment integration with D3Forum', 'xpressme') . '</label></th>' . "\n";
		$form .=  "<td>\n";
		$form .=  __('Select the forum of D3Forum that does the comment integration from the following lists.', 'xpressme') ."<br />\n";
		$form .=  $forum_list."\n";
		$form .= '<br /><br />';
		if ($this->is_use_d3forum) {
			if ($this->is_use_d3forum)  $disible = ''; else $disible = 'disabled';
			$form .=  __('Select the Type of display of D3Forum comment.', 'xpressme') . " \n&emsp";
			if ($this->is_d3forum_flat){
				$form .= "&ensp<label><input type='radio' name='ch_is_d3forum_flat' value='1' checked='checked' />" . __('Flat','xpressme') ."</label>\n";
				$form .= "&ensp<label><input type='radio' name='ch_is_d3forum_flat' value='0' />". __('Threaded','xpressme') . "</label>\n";
			}else{
				$form .= "&ensp<label><input type='radio' name='ch_is_d3forum_flat' value='1' />" . __('Flat','xpressme') . "</label>\n";
				$form .= "&ensp<label><input type='radio' name='ch_is_d3forum_flat' value='0' checked='checked' />". __('Threaded','xpressme') ."</label>\n";
			}
			$form .= '<br />';
			$form .=  __('Select the order of display of D3Forum comment.', 'xpressme') . " \n&emsp";
			if ($this->is_d3forum_desc){
				$form .= "&ensp<label><input type='radio' name='ch_is_d3forum_desc' value='1' checked='checked' />" . __('DESC','xpressme') ."</label>\n";
				$form .= "&ensp<label><input type='radio' name='ch_is_d3forum_desc' value='0' />". __('ASC','xpressme') . "</label>\n";
			}else{
				$form .= "&ensp<label><input type='radio' name='ch_is_d3forum_desc' value='1' />" . __('DESC','xpressme') . "</label>\n";
				$form .= "&ensp<label><input type='radio' name='ch_is_d3forum_desc' value='0' checked='checked' />". __('ASC','xpressme') ."</label>\n";
			}
			$form .= '<br />';
			$form .=  __('Number of displays of D3Forum comments.', 'xpressme') ." \n";
			$form .= '&emsp<label> <input name="ch_d3forum_views_num" type="text" size="3" maxlength="3" value="'  . $this->d3forum_views_num . '" /></label>'."\n";
			$form .= '<div class="submit">'."\n";		
			$form .=  __('The import and the export between Wordpress Comments and the D3Forum Posts can be done. ', 'xpressme') ."<br />\n";
			$form .= '<input type="submit" value= "' . __('Export to D3Forum', 'xpressme') . '" name="export_d3f" ' . $disible . ' >' ."\n";
			$form .= '<input type="submit" value= "' . __('Import from D3Forum', 'xpressme') . '" name="inport_d3f" ' . $disible . ' >' ."<br />\n";
			$form .= '</div>'."\n";
			if (!empty($do_message)){
				$form .= '<div>' . $do_message . '</div>';
			}
		}
		$form .=  "</td>\n";
		$form .=  "</tr><tr>\n";
		return $form;
	}
	
	function excerpt_option(){
		$form = '';
		$form .= '<tr><th><label for="excerpt">' .__('Contents Excerpt Setting', 'xpressme') . '</label></th>';
		$form .= "<td>\n";
		$form .= "<table>\n";
		$form .= "<tr>\n";
		
		$form .= "<td>" . __('Is the excerpt display done with the archive of contents?','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->yes_no_radio_option_sub('is_content_excerpt');
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('When ASCII character more than the set ratio is included, it is judged ASCII contents. ','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('ascii_judged_rate');
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('Excerpt length of word for ASCII contents','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('excerpt_length_word');
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .= "<td>" . __('Excerpt length of character for multibyte contents','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('excerpt_length_character');
		$form .= "</td>\n";
		$form .= "</tr>\n";

		$form .= "<tr>\n";
		$form .= "<td>" . __('This text is displayed in the link that reads contents not excerpted.(Is not displayed for the blank.)','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('excerpt_more_link_text');
		$form .= "</td>\n";
		$form .= "</tr>\n";

		$form .= "<tr>\n";
		$form .= "<td>" . __('This text is displayed in the link that more tag (&lt;!--more--&gt;). ','xpressme') . "</td>\n";		
		$form .= "<td>\n";
		$form .=  $this->text_option_sub('more_link_text');
		$form .= "</td>\n";
		$form .= "</tr>\n";

		$form .= "</table></td></tr>\n";
	    return $form;
	}

	function viewer_type_option(){
		$form  = "<tr>\n";
		$form .= '<th><label for="viewer_type">' .__('Display Mode Setting', 'xpressme') . '</label></th>';
		$form .= "<td>\n";
		
		$form .=  __('Select the XPressME Display Mode.', 'xpressme') ."\n";
		$form .= '<select name="ch_viewer_type">' . "\n";
		
		$form .= '<option value="xoops" ';
		if ($this->viewer_type == 'xoops') $form .= ' selected="selected"';
		$form .= '>'.__('Xoops Mode', 'xpressme') ."</option>\n";

		$form .= '<option value="wordpress" ';
		if ($this->viewer_type == 'wordpress') $form .= ' selected="selected"';
		$form .= '>'.__('WordPress Mode', 'xpressme') ."</option>\n";
		
		$form .= '<option value="user_select" ';
		if ($this->viewer_type == 'user_select') $form .= ' selected="selected"';
		$form .= '>'.__('User select', 'xpressme') ."</option>\n";

		$form .= "</select><br />\n";
		
		// Theme Select
		$form .=  __('Select the theme used in the XOOPS Mode.', 'xpressme') ."\n";
		$form .= '<select name="ch_theme_select">' . "\n";
		
		$form .= '<option value="use_wordpress_select" ';
		if ($this->theme_select == 'use_wordpress_select') $form .= ' selected="selected"';
		$form .= '>'.__('Use WordPress Selected Themes', 'xpressme') ."</option>\n";
		
		$themes = get_themes();
		$theme_names = array_keys($themes);
		natcasesort($theme_names);
		foreach ($theme_names as $theme_name) {
			if ($theme_name == 'My Themes') continue;
			$form .= '<option value="' . $theme_name .'" ';
			if ($this->theme_select == $theme_name) $form .= ' selected="selected"';
			$form .= '>'.$theme_name ."</option>\n";
		}
		$form .= "</select><br />\n";
		$form .= "</td></tr>\n";
	    return $form;
	}
	
	function header_meta_option(){
		$form  = "<tr>\n";
		$form .= '<th><label for="header_type">' .__('Header Meta Option', 'xpressme') . '</label></th>';
		$form .= "<td>\n";
		$form .= "<table>\n";
		$form .= "<tr>\n";
		
		$form .=  "<td>" . __('Select the Header keyword.', 'xpressme')  . "</td>\n";
		$form .= "<td>\n";
		$form .= '<select name="ch_meta_keyword_type">' . "\n";		
		$form .= '<option value="xoops" ';
		if ($this->meta_keyword_type == 'xoops') $form .= ' selected="selected"';
		$form .= '>'.__('Xoops KeyWord', 'xpressme') ."</option>\n";
		$form .= '<option value="wordpress" ';
		if ($this->meta_keyword_type == 'wordpress') $form .= ' selected="selected"';
		$form .= '>'.__('WordPress KeyWord', 'xpressme') ."</option>\n";		
		$form .= '<option value="wordpress_xoops" ';
		if ($this->meta_keyword_type == 'wordpress_xoops') $form .= ' selected="selected"';
		$form .= '>'.__('WordPress & Xoops KeyWord', 'xpressme') ."</option>\n";
		$form .= "</select><br />\n";
		$form .= "</td>\n";
		$form .= "</tr>\n";
		
		$form .= "<tr>\n";
		$form .=  "<td>" . __('Select the Header Description.', 'xpressme') . "</td>\n";
		$form .= "<td>\n";
		$form .= '<select name="ch_meta_description_type">' . "\n";
		$form .= '<option value="xoops" ';
		if ($this->meta_description_type == 'xoops') $form .= ' selected="selected"';
		$form .= '>'.__('Xoops Description', 'xpressme') ."</option>\n";
		$form .= '<option value="wordpress" ';
		if ($this->meta_description_type == 'wordpress') $form .= ' selected="selected"';
		$form .= '>'.__('WordPress Description', 'xpressme') ."</option>\n";
		$form .= '<option value="wordpress_xoops" ';
		if ($this->meta_description_type == 'wordpress_xoops') $form .= ' selected="selected"';
		$form .= '>'.__('WordPress & Xoops Description', 'xpressme') ."</option>\n";
		$form .= "</select><br />\n";
		$form .= "</td>\n";
		$form .= "</tr>\n";

		$form .= "<tr>\n";
		$form .=  "<td>" . __('Select the Header Robots Index.', 'xpressme') . "</td>\n";
		$form .= "<td>\n";
		$form .= '<select name="ch_meta_robot_type">' . "\n";
		$form .= '<option value="xoops" ';
		if ($this->meta_robot_type == 'xoops') $form .= ' selected="selected"';
		$form .= '>'.__('Xoops Robots Index', 'xpressme') ."</option>\n";
		$form .= '<option value="wordpress" ';
		if ($this->meta_robot_type == 'wordpress') $form .= ' selected="selected"';
		$form .= '>'.__('WordPress Robots Index', 'xpressme') ."</option>\n";
		$form .= "</select><br />\n";
		$form .= "</td>\n";
		$form .= "</tr>\n";

		$form .= "</table>\n";
		
		$form .= "</tr>\n";
	    return $form;
	}

	function xpress_upload_filter($uploads)
	{
		global $xoops_config;
		global $blog_id,$blogname;
		
		if ($this->is_use_xoops_upload_path){
			$wordpress_dir = ABSPATH ;
			$xoops_dir = $xoops_config->xoops_upload_path . '/';
			if (xpress_is_multiblog() && $blog_id <> BLOG_ID_CURRENT_SITE){
				$wordpress_base_url = $xoops_config->module_url;
			} else {
				$wordpress_base_url = get_option( 'siteurl' );
			}
			$xoops_upload_url = $xoops_config->xoops_upload_url;
			// @rmdir($uploads[path]);  //remove wordpress side uploads_dir 
			
			$uploads[path] =  str_replace ($wordpress_dir, $xoops_dir, $uploads[path]);
			$uploads[basedir] = str_replace ($wordpress_dir, $xoops_dir, $uploads[basedir]);
			$uploads[url] = str_replace ($wordpress_base_url, $xoops_upload_url, $uploads[url]);
			$uploads[baseurl] = str_replace ($wordpress_base_url, $xoops_upload_url, $uploads[baseurl]);
			
			if (xpress_is_multiblog() && $blog_id <> BLOG_ID_CURRENT_SITE){
				$pat = str_replace ($xoops_dir, '', $uploads[path]);
				$pat = preg_replace('/files.*/', '', $pat);
				$pat = str_replace ('/', '\/', $pat);
				$uploads[path] = preg_replace('/' . $pat . '/',  $blogname . '/',$uploads[path]);
				
				$pat = str_replace ($xoops_dir, '', $uploads[basedir]);
				$pat = preg_replace('/files.*/', '', $pat);
				$pat = str_replace ('/', '\/', $pat);
				$uploads[basedir] = preg_replace('/' . $pat . '/',  $blogname . '/',$uploads[basedir]);
			}
			
			// Make sure we have an uploads dir
			if ( ! wp_mkdir_p( $uploads[path] ) ) {
				$message = sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), $uploads[path] );
				return array( 'error' => $message );
			}
		}
		return $uploads;
	}

	// SQL DEBUG TEST
	function is_sql_debug_permission()
	{
		global $current_user;

		if (!is_object($current_user)) return false;
		if ($this->is_sql_debug && ($current_user->user_level >= 10))
			return true;
		else
			return false;
	}
	
	function xpress_sql_debug($query_strings)
	{
		if ($this->is_sql_debug_permission()){
			if (empty($GLOBALS['XPress_SQL_Query'])) $GLOBALS['XPress_SQL_Query'] = '';
			$GLOBALS['XPress_SQL_Query'] .= $query_strings . '<br /><br />';
		}
		return $query_strings;
	}
	
	function displayDebugLog()
	{
		if ($this->is_sql_debug_permission()){
			$content = '';
			$content .= '<html><head><meta http-equiv="content-type" content="text/html; charset='._CHARSET.'" />';
			$content .= '<meta http-equiv="content-language" content="'._LANGCODE.'" />' ;
			$content .= '<title>XPressME SQL DEBUG</title>' ;
			$content .= '</head><body>';
			$content .= $GLOBALS['XPress_SQL_Query'];
			$content .= '<div style="text-align:center;"><input class="formButton" value="CLOSE" type="button" onclick="javascript:window.close();" /></div></body></html>';

			echo '<script type="text/javascript">
				<!--//
				xpress_debug_window = window.open("", "xpress_debug", "width=680 , height=600 ,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no");
				xpress_debug_window.document.clear();
				xpress_debug_window.focus();
				';
			$lines = preg_split("/(\r\n|\r|\n)( *)/", $content);
			foreach ($lines as $line) {
				echo 'xpress_debug_window.document.writeln("'.str_replace('"', '\"', $line).'");';
			}
			echo '
				xpress_debug_window.document.close();
				//-->
			</script>';
		}
	}
}
?>