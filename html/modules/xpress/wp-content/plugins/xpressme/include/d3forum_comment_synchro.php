<?PHP
// $Id: xoops_version.php,v 0.4 2007/07/21 01:35:02 toemon $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: toemon                                                      //
// URL:http://www.toemon.com                           //
// ------------------------------------------------------------------------- //
function onaction_comment_post($comment_ID  = "")
{
	global $wpdb;
	$comment_type = $wpdb->get_var("SELECT comment_type FROM $wpdb->comments WHERE comment_ID = $comment_ID");
	if (!empty($comment_type)) return ;	
	return wp_comment_sync_to_d3forum($comment_ID,'post');
}
function onaction_edit_comment($comment_ID  = "")
{
	global $wpdb;
	$comment_type = $wpdb->get_var("SELECT comment_type FROM $wpdb->comments WHERE comment_ID = $comment_ID");
	if (!empty($comment_type)) return ;	
	return wp_comment_sync_to_d3forum($comment_ID,'edit');
}
function onaction_delete_comment($comment_ID  = "")
{
	global $wpdb;
	$comment_type = $wpdb->get_var("SELECT comment_type FROM $wpdb->comments WHERE comment_ID = $comment_ID");
	if (!empty($comment_type)) return ;	
	return wp_comment_sync_to_d3forum($comment_ID,'delete');
}
function onaction_delete_post($post_id)
{
	wp_post_delete_sync($post_id);
}

function onaction_comment_close($post_id)
{
	global $wpdb;
	$status = $wpdb->get_var("SELECT comment_status FROM $wpdb->posts WHERE ID = $post_id");
	
	if ($status =='open') 
		$lock = 0; 
	else
		$lock = 1;
	d3forum_topic_rock($post_id,$lock);
}

function onaction_comment_apobe($comment_ID){
	global $wpdb;
	$comment_type = $wpdb->get_var("SELECT comment_type FROM $wpdb->comments WHERE comment_ID = $comment_ID");
	$status = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_ID = $comment_ID");
	if(is_null($status)) return;
	//	$status = wp_get_comment_status($comment_ID);
	switch($status){
		case 'approved':
		case 1:
			if (empty($comment_type)) onaction_edit_comment($comment_ID);
			break;
		case 'unapproved':
		case 0:
			if (empty($comment_type)) onaction_edit_comment($comment_ID);
			break;
		default:
			break;
	}
}

function onaction_trashed_post_comments($post_id){
	global $wpdb;
	set_d3f_topic_invisible($post_id,1);
}
function onaction_untrashed_post_comments($post_id){
	global $wpdb;
	set_d3f_topic_invisible($post_id,0);
}
function onaction_trashed_comment($comment_ID  = ""){
	global $wpdb;
	$comment_type = $wpdb->get_var("SELECT comment_type FROM $wpdb->comments WHERE comment_ID = $comment_ID");
	if (!empty($comment_type)) return ;	
	set_d3f_post_invisible($comment_ID,1);
}
function onaction_untrashed_comment($comment_ID  = ""){
	global $wpdb;
	$comment_type = $wpdb->get_var("SELECT comment_type FROM $wpdb->comments WHERE comment_ID = $comment_ID");
	if (!empty($comment_type)) return ;	
	set_d3f_post_invisible($comment_ID,0);
}

function set_d3f_topic_invisible($topic_external_link_id,$invisible){
	global $wpdb,$xoops_db,$blog_id,$xpress_config;
	
	if (empty($blog_id)) $blog_id =1;
	$d3forum_prefix = get_xoops_prefix() . $xpress_config->d3forum_module_dir . '_';
	$d3f_topic = $d3forum_prefix . 'topics';
	$d3f_forum_id = $xpress_config->d3forum_forum_id;
	//xdb_d3forum_posts  where topic_external_link_id = topic_external_link_id update topic_invisible
	
	$sql  = "UPDATE $d3f_topic ";
	$sql .= "SET topic_invisible = $invisible ";
	$sql .= "WHERE topic_external_link_id = $topic_external_link_id AND forum_id = $d3f_forum_id" ;
	$xoops_db->query($sql);
}

function set_d3f_post_invisible($wp_comment_ID, $invisible){
	global $wpdb,$xoops_db,$blog_id,$xpress_config;
	
	if (empty($blog_id)) $blog_id =1;
	$d3forum_prefix = get_xoops_prefix() . $xpress_config->d3forum_module_dir . '_';
	$d3f_posts = $d3forum_prefix . 'posts';	// delete key topic_id

	$d3f_post_id = get_d3forum_post_ID($wp_comment_ID);
	//xdb_d3forum_posts  where post_id update invisible
	if ($d3f_post_id){
		$sql  =	"UPDATE  $d3f_posts SET invisible = $invisible WHERE post_id =$d3f_post_id";
		$xoops_db->query($sql);
	}
}

function disp_d3forum_comments($template_dir="", $file_name="")
{
	if (is_xpress_mobile()) return $file_path;
	$file_path = dirname(__FILE__) . '/d3forum_comment_disp.php';
	return $file_path;
}

function d3f_module_found($forum_dir ='d3forum'){		//use admin/admin_enhanced.php
	return file_exists( get_xoops_root_path() . '/modules/' . $forum_dir);
}

function d3f_forum_id_found($forum_id = 1 , $forum_dir ='d3forum'){  //use admin/admin_enhanced.php
	global $xoops_db;

	$d3f_forums = get_xoops_prefix() . $forum_dir . '_forums';

	$sql  = "SELECT forum_id ";
	$sql .= "FROM $d3f_forums ";
	$sql .= "WHERE ( forum_id = $forum_id )";

	$forum_id =  $xoops_db->get_var($sql);
	if (empty($forum_id)){
		return false;
	}
	return $forum_id;
}

function get_d3forum_post_ID($wp_comment_ID){
	global $xoops_db,$blog_id;
	
	if (empty($blog_id)) $blog_id =1;

	$wp_d3forum_link = get_wp_prefix() . 'd3forum_link';
	
	$sql  =	"SELECT post_id FROM $wp_d3forum_link WHERE comment_ID = $wp_comment_ID AND blog_id = $blog_id";
	$post_id = $xoops_db->get_var($sql);
	return $post_id;
}		

function get_wp_comment_ID($d3forum_post_ID){
	global $xoops_db,$blog_id;
	
	if (empty($blog_id)) $blog_id =1;

	$wp_d3forum_link = get_wp_prefix() . 'd3forum_link';
	
	$sql  =	"SELECT comment_ID FROM $wp_d3forum_link WHERE post_id = $d3forum_post_ID AND blog_id = $blog_id";
	$comment_ID = $xoops_db->get_var($sql);
	return $comment_ID;
}				

// Next, auto increment ID value used is acquired. 
function get_next_auto_increment_id($table_name,$id_name){
	global $xoops_db;
		$sql = "SELECT MAX($id_name) as last_id FROM $table_name";
		$get_id = $xoops_db->get_var($sql);
		if (empty($get_id)){
			return 1;
		} else {
			return  $get_id + 1;
		}
}

function is_d3forum_setting(){
	global $xpress_config,$xoops_db;

	$use_d3f = $xpress_config->is_use_d3forum;
	if (empty($use_d3f)) {
		return false;
	}
	$d3f_forum_id = $xpress_config->d3forum_forum_id;
	$d3f_forum_dir  = $xpress_config->d3forum_module_dir;
	if (! d3f_module_found($d3f_forum_dir)) die( "D3Forum Directory ($d3f_forum_dir) not found" ) ;	
	if (! d3f_forum_id_found($d3f_forum_id , $d3f_forum_dir)) die( "D3Forum ForumID($d3f_forum_id) not found" ) ;
	
	$xoops_db->query( "UPDATE ".get_xoops_prefix() . $d3f_forum_dir."_forums" ." SET forum_external_link_format='".addslashes($xpress_config->d3forum_external_link_format)."' WHERE forum_id= $d3f_forum_id" ) ;
	
	return true;
}

function d3forum_topic_rock($wp_post_id,$lock = '0')
{
	global $xpress_config,$xoops_db;
	
	$d3f_forum_id = $xpress_config->d3forum_forum_id;
	
	if (!is_d3forum_setting()) die('The setting of the D3Forum comment integration is wrong. ');
	$d3f_forum_dir  = $xpress_config->d3forum_module_dir;
	
	$d3f_topic = get_xoops_prefix() . $d3f_forum_dir . '_topics';

		$sql  = "UPDATE $d3f_topic ";
		$sql .= "SET topic_locked = $lock ";
		$sql .= "WHERE topic_external_link_id = $wp_post_id AND forum_id = $d3f_forum_id" ;
		$xoops_db->query($sql);
}

// All comments of WordPress are exported to the D3Forum comment. 
function wp_to_d3forum($forum_id = 1, $d3f_prefix = 'd3forum'){
	global $xpress_config,$xoops_db,$wpdb;
	global $blog_id;
	if (empty($blog_id)) $blog_id =1;

	if (!is_d3forum_setting()) die('The setting of the D3Forum comment integration is wrong. ');
	
	$d3forum_prefix = get_xoops_prefix() . $d3f_prefix . '_';
	$xpress_prefix = get_wp_prefix();
	
	$wp_comments = $wpdb->comments;
	$wp_d3forum_link = $xpress_prefix . 'd3forum_link';
	$wp_posts = $wpdb->posts;
	$d3f_topic = $d3forum_prefix . 'topics';	// delete key forum_id
	$d3f_forums = $d3forum_prefix . 'forums';	// delete key forum_id
	$d3f_posts = $d3forum_prefix . 'posts';	// delete key topic_id
	$d3f_users2topics = $d3forum_prefix . 'users2topics';	// delete key topic_id
	$d3f_post_histories = $d3forum_prefix . 'post_histories';	// delete key post_id
	$d3f_post_post_votes = $d3forum_prefix . 'post_votes';	// delete key post_id

	//DELETE D3FORUM_TOPIC & D3FORUM_POSTS
	$topics = $xoops_db->get_results("SELECT topic_id FROM $d3f_topic WHERE forum_id = $forum_id");
	foreach($topics as $topic){
		$now_topic_id = $topic->topic_id;
		$posts = $xoops_db->get_results("SELECT post_id FROM $d3f_posts WHERE topic_id = $now_topic_id");
		foreach($posts as $post){
			$now_post_id = $post->post_id;
			$xoops_db->query("DELETE FROM $d3f_post_histories WHERE post_id = $now_post_id");
			$xoops_db->query("DELETE FROM $d3f_post_post_votes WHERE post_id = $now_post_id");
		}
		$xoops_db->query("DELETE FROM $d3f_posts WHERE topic_id = $now_topic_id");
		$xoops_db->query("DELETE FROM $d3f_users2topics WHERE topic_id = $now_topic_id");
	}
	$sql  = "UPDATE $d3f_forums ";
	$sql .= "SET forum_topics_count = 0,forum_posts_count = 0,forum_last_post_id = 0,forum_last_post_time = 0 ";
	$sql .= "WHERE forum_id = $forum_id" ;
	$xoops_db->query($sql);
	
	$xoops_db->query("DELETE FROM $d3f_topic WHERE forum_id = $forum_id");
	
	$next_id = get_next_auto_increment_id($d3f_topic,'topic_id');
	$xoops_db->query("ALTER TABLE $d3f_topic AUTO_INCREMENT = $next_id");
	
	$next_id = get_next_auto_increment_id($d3f_posts,'post_id');
	$xoops_db->query("ALTER TABLE $d3f_posts AUTO_INCREMENT = $next_id");
	
	$next_id = get_next_auto_increment_id($d3f_post_histories,'history_id');
	$xoops_db->query("ALTER TABLE $d3f_post_histories AUTO_INCREMENT = $next_id");

	$next_id = get_next_auto_increment_id($d3f_post_post_votes,'vote_id');
	$xoops_db->query("ALTER TABLE $d3f_post_post_votes AUTO_INCREMENT = $next_id");
	
	//All the records in the wp_d3forum_link table are deleted.  
	$xoops_db->query("DELETE FROM $wp_d3forum_link WHERE blog_id = $blog_id");

	//The comment is copied from the wordpress comment.
	$sql  = "SELECT comment_ID ";
	$sql .= "FROM $wp_comments ";
	$sql .=	"WHERE (comment_approved NOT LIKE 'spam') AND (comment_type = '') ";
	$sql .= "ORDER BY comment_ID";

	$comment_count = 0;
	$comments = $wpdb->get_results($sql);
	foreach($comments as $comment){
		$comment_ID = $comment->comment_ID;
		wp_comment_sync_to_d3forum($comment_ID ,'insert');
		$comment_count++;
	}

	$return_str = "...Export $comment_count Comment OK ";

	return $return_str;
}


// All comments of D3Forum are import to the WordPress comment. 
function d3forum_to_wp($forum_id = 1, $d3f_prefix = 'd3forum'){
	global $xpress_config,$xoops_db,$wpdb;
	global $blog_id;
	if (empty($blog_id)) $blog_id =1;
	if (!is_d3forum_setting()) die('The setting of the D3Forum comment integration is wrong. ');
	
	$d3forum_prefix = get_xoops_prefix() . $d3f_prefix . '_';
	$xpress_prefix = get_wp_prefix() ;
	
	$wp_comments = $wpdb->comments;
	$wp_d3forum_link = $xpress_prefix . 'd3forum_link';
	$wp_dummy = $xpress_prefix . 'dummy';
	
	$d3f_topic = $d3forum_prefix . 'topics';
	$d3f_posts = $d3forum_prefix . 'posts';
	
	$db_xoops_users = get_xoops_prefix() . 'users';

	// The track back data is taken out of the comment table, and it returns it to the initialized comment table. 	
	//copies it in the dummy table excluding a usual comment. 
	$xoops_db->query("CREATE TABLE $wp_dummy SELECT * FROM $wp_comments WHERE comment_type != ''");
	//comment_ID of the dummy table is adjusted to all 0.
	$xoops_db->query("UPDATE $wp_dummy SET `comment_ID` = 0");
	//All the records in the comment table are deleted.  
	$xoops_db->query("DELETE FROM $wp_comments WHERE 1");
	//The auto increment value of the comment table is reset in '1'.
	$xoops_db->query("ALTER TABLE $wp_comments AUTO_INCREMENT =1");
	//The content of dummy table is returned to the comment table. 
	$xoops_db->query("INSERT INTO $wp_comments SELECT * FROM $wp_dummy");;
	//The dummy table is deleted. 
	$xoops_db->query("DROP TABLE $wp_dummy");
	//All the records in the wp_d3forum_link table are deleted.  
	$xoops_db->query("DELETE FROM $wp_d3forum_link WHERE  blog_id = $blog_id");

	//All wp post comment count clear
	$wp_posts = $wpdb->posts;
	$xoops_db->query("UPDATE $wp_posts SET  comment_count = 0 WHERE 1 ");
		
//The comment is copied from the d3forum comment.
	$d3f_sql  =	"SELECT $d3f_topic.forum_id, $d3f_topic.topic_external_link_id, $d3f_topic.topic_id, $d3f_posts.post_id, $d3f_posts.pid ";
	$d3f_sql .=	"FROM $d3f_topic LEFT JOIN $d3f_posts ON $d3f_topic.topic_id = $d3f_posts.topic_id ";
	$d3f_sql .=	"WHERE $d3f_topic.forum_id=$forum_id ";
	$d3f_sql .= "ORDER BY $d3f_posts.post_id";

	$d3f_res = $xoops_db->get_results($d3f_sql);
	$import_count = 0;
	foreach($d3f_res as $d3f_row){
		$link_id = $d3f_row->topic_external_link_id;
		$forum_id = $d3f_row->forum_id;
		$topic_id = $d3f_row->topic_id;
		$post_id = $d3f_row->post_id;
					
		if(empty($link_id)){ echo "<p><font color='#FF0000'>PASS: empty topic.topic_external_link_id in topic_id($topic_id)</font></p>" ; continue;}
		if(empty($post_id)){ echo "<p><font color='#FF0000'>PASS: empty topic_id=$topic_id in $d3f_posts</font></p>" ; continue;}
		
		if ($d3f_row->pid == 0){
			$mode = 'newtopic';
		}else{
			$mode = 'reply';
		}
		d3forum_sync_to_wp_comment( $mode , $link_id , $forum_id , $topic_id , $post_id);
		$import_count++;
	}
	$return_str = "...Import $import_count Comment OK ";
	return $return_str;
}

//When post of wordpress is deleted, the comment on relating d3forum is deleted. 

function wp_post_delete_sync($post_id){
	global $xpress_config,$xoops_db;

	if (!is_d3forum_setting()) die('The setting of the D3Forum comment integration is wrong. ');
	$d3forum_dirname = $xpress_config->d3forum_module_dir;
	$d3forum_prefix = get_xoops_prefix() . $d3forum_dirname . '_';
	$d3forum_forum_id = $xpress_config->d3forum_forum_id;

	$post_id = intval( $post_id ) ;

	$d3f_topics = $d3forum_prefix . 'topics';
	
	$sql = "SELECT topic_id,topic_first_post_id FROM $d3f_topics WHERE topic_external_link_id = $post_id AND forum_id = $d3forum_forum_id";
	$row = $xoops_db->get_row($sql) ;
	
	if(empty($row)) return ;
	$topic_id = $row->topic_id;
	$topic_first_post_id = $row->topic_first_post_id;
	wp_d3forum_delete_post_recursive( $d3forum_dirname , $topic_first_post_id ,true);
//	wp_d3forum_delete_topic( $d3forum_dirname , $topic_id );
	wp_d3forum_sync_topic( $d3forum_dirname , $topic_id ) ;
}

//  The content is reflected in the WordPress comment when there is a change in the D3Forum comment. 

function d3forum_sync_to_wp_comment( $mode , $link_id , $forum_id , $topic_id , $post_id = 0 ){
	global $xpress_config,$xoops_db,$wpdb,$blog_id;

	if (!is_d3forum_setting()) die('The setting of the D3Forum comment integration is wrong. ');

	if (empty($blog_id)) $blog_id =1;
	$d3f_forum_id = $xpress_config->d3forum_forum_id;

	$d3forum_prefix = get_xoops_prefix() . $xpress_config->d3forum_module_dir . '_';
	$xpress_prefix = get_wp_prefix();
	
	$wp_comments = $wpdb->comments;
	$wp_posts = $wpdb->posts;
	$wp_d3forum_link = $xpress_prefix . 'd3forum_link';
	
	$d3f_posts = $d3forum_prefix . 'posts';
	$d3f_topics = $d3forum_prefix . 'topics';
	$d3f_users2topics  = $d3forum_prefix . 'users2topics';
	$d3f_post_votes = $d3forum_prefix . 'post_votes';

	$db_xoops_users = get_xoops_prefix() . 'users';
	
	$comment_post_ID = $link_id;

	$d3f_sql  =	"SELECT $d3f_posts.guest_name, ";
	$d3f_sql .=	"$d3f_posts.guest_email, $d3f_posts.guest_url, $d3f_posts.poster_ip, $d3f_posts.post_time, ";
	$d3f_sql .=	"$d3f_posts.post_text, $d3f_posts.approval, $d3f_posts.uid ,$d3f_posts.pid ";
	$d3f_sql .=	"FROM $d3f_posts ";
	$d3f_sql .=	"WHERE $d3f_posts.post_id = $post_id";

	$d3f_row = $xoops_db->get_row($d3f_sql) ;
	if (empty($d3f_row)) die('...Err. OPEN D3Forum Data (' .  $d3f_sql . ')');
	$uid = $d3f_row->uid;
	if (!empty($uid)) {
		$xu_sql  = "SELECT uid ,name ,uname ,email , url FROM $db_xoops_users WHERE uid = $uid";
		$xu_row =  $xoops_db->get_row($xu_sql);
		if (empty($xu_row)){
			$user_display_name = '';
		}else {
			if (empty($xu_row->name)){
				$user_display_name = $xu_row->uname;
			} else {
				$user_display_name = $xu_row->name;
			}
			$comment_author_email = "'" . $xu_row->email . "'";
			$comment_author_url = "'" . $xu_row->url . "'";
		}
		$comment_author = "'" . addSlashes($user_display_name) . "'";
	} else {						
		$comment_author = "'" . addSlashes($d3f_row->guest_name) . "'";
		$comment_author_email = "'" . $d3f_row->guest_email . "'";
		$comment_author_url = "'" . $d3f_row->guest_url . "'";
	}
	$comment_author_IP = "'" . $d3f_row->poster_ip . "'";
	$gmt_offset = get_option('gmt_offset');
	$local_timestamp = $d3f_row->post_time + ($gmt_offset * 3600);
	$comment_date = "'" . date('Y-m-d H:i:s' , $local_timestamp) . "'";
	$comment_content = "'" . addSlashes($d3f_row->post_text) . "'";
	$comment_approved = "'" . $d3f_row->approval . "'";
	$user_ID = $d3f_row->uid;
	$comment_date_gmt = "'" . gmdate('Y-m-d H:i:s' , $d3f_row->post_time) . "'";
	$comment_type = '';
	if ($d3f_row->pid > 0) {
		$comment_parent = get_wp_comment_ID($d3f_row->pid);
	} else {
		$comment_parent = 0 ;
	}


		switch($mode){				
			case 'reply':
			case 'newtopic' :				
				$wp_sql  = "INSERT INTO $wp_comments ";
				$wp_sql .=    "(comment_post_ID , comment_author , comment_author_email , comment_author_url , comment_author_IP , ";
				$wp_sql .=    "comment_date , comment_content , comment_approved , user_id , comment_date_gmt, comment_parent) ";
				$wp_sql .=  "VALUES ";
				$wp_sql .=    "($comment_post_ID, $comment_author, $comment_author_email, $comment_author_url, $comment_author_IP, ";
				$wp_sql .=    "$comment_date, $comment_content, $comment_approved, $user_ID, $comment_date_gmt, $comment_parent)";

				$wp_res = 	$xoops_db->query($wp_sql);
				if ($wp_res === false) die( '...Err. INSERT' . $wp_comments . '(' . $wp_sql . ')');
				$comment_ID = mysql_insert_id();
				$wp_sql  = "UPDATE $wp_posts SET  comment_count = comment_count +1 WHERE ID = $comment_post_ID";
				$xoops_db->query($wp_sql);
				$wp_sql  = "INSERT INTO $wp_d3forum_link ";
				$wp_sql .=    "(comment_ID , post_id, wp_post_ID, forum_id, blog_id) ";
				$wp_sql .=  "VALUES ";
				$wp_sql .=    "($comment_ID, $post_id, $link_id, $d3f_forum_id, $blog_id)";		
				$xoops_db->query($wp_sql);				
				if ($comment_approved ==0)	do_CommentWaiting($comment_ID, $post_id);
				break;
			case 'edit':
				$comment_ID = "SELECT comment_ID FROM $wp_d3forum_link WHERE post_id = $post_id";
				$comment_ID = $xoops_db->get_var("SELECT comment_ID FROM $wp_d3forum_link WHERE post_id = $post_id");
				if (empty($comment_ID)) die('...Err. EDIT' . $wp_comments . '(' . $wp_sql . ')');
				$wp_sql  = "UPDATE $wp_comments SET comment_content = $comment_content , comment_date_gmt = $comment_date_gmt WHERE comment_ID = $comment_ID";
				$wp_res = $xoops_db->query($wp_sql);
				if (empty($wp_res)) die( '...Err. UPDATE' . $wp_comments . '(' . $wp_sql . ')');
				break;
			case 'delete':
				// wordpress comments delete
				$comment_ID = get_wp_comment_ID($post_id);
				if ($comment_ID > 0){
					$sql= "SELECT comment_type FROM $wp_comments WHERE comment_ID = $comment_ID";
					$comment_type= $xoops_db->get_var("SELECT comment_type FROM $wp_comments WHERE comment_ID = $comment_ID");
					if (!empty($comment_type)) break;
					$xoops_db->query("DELETE FROM $wp_comments WHERE comment_ID = $comment_ID");				
					$xoops_db->query("DELETE FROM $wp_d3forum_link WHERE post_id = $post_id");				
					$xoops_db->query("UPDATE $wp_posts SET  comment_count = comment_count -1 WHERE ID = $comment_post_ID");				
				}
				break;
			default :
		}				
		
	return true ;
}

//  The content is reflected in the D3Forum comment when there is a change in the WordPress comment. 
function wp_comment_sync_to_d3forum($comment_ID = 0,$sync_mode){
	global $xpress_config,$xoops_db,$xoops_config,$wpdb,$blog_id;
	
	if (empty($blog_id)) $blog_id =1;
	
	if (!is_d3forum_setting()) die('The setting of the D3Forum comment integration is wrong. ');
	$mydirname = $xoops_config->module_name;

	$d3f_forum_id = $xpress_config->d3forum_forum_id;
	$d3f_forum_dir  = $xpress_config->d3forum_module_dir;
	
	$d3forum_prefix = get_xoops_prefix() . $d3f_forum_dir . '_';
	$xpress_prefix = get_wp_prefix();
	$wp_comments =  $wpdb->comments;
//	$wp_comments = $xpress_prefix . 'comments';
	$wp_posts = $wpdb->posts;
//	$wp_posts = $xpress_prefix . 'posts';
	$wp_d3forum_link = $xpress_prefix . 'd3forum_link';
	$d3f_topic = $d3forum_prefix . 'topics';
	$d3f_posts = $d3forum_prefix . 'posts';

	$sql  =	"SELECT $wp_comments.comment_ID,$wp_comments.comment_post_ID, ";
	$sql .=		"$wp_comments.comment_author, $wp_comments.comment_author_email,  $wp_comments.comment_date, $wp_comments.comment_date_gmt, ";
	$sql .=		"$wp_comments.comment_author_url, $wp_comments.comment_author_IP, ";
	$sql .=		"$wp_comments.comment_content, $wp_comments.comment_karma, ";
	$sql .=		"$wp_comments.comment_approved, $wp_comments.comment_agent, ";
	$sql .=		"$wp_comments.comment_type, $wp_comments.comment_parent, $wp_comments.user_id, ";
	$sql .=		"$wp_posts.post_title ,$wp_posts.comment_count ";
	$sql .=	"FROM $wp_comments INNER JOIN  $wp_posts ON $wp_comments.comment_post_ID = $wp_posts.ID ";
	$sql .=	"WHERE (comment_ID = $comment_ID) AND ($wp_comments.comment_approved NOT LIKE 'spam') ";

//	$row = $xoops_db->get_row($sql) ;
	$row = $wpdb->get_row($sql) ;
	if(empty($row)) die( 'READ ' . $wp_comments . '_NG...' .$sql);
	if (! empty($row->comment_type)) return;
	
	$forum_id = $d3f_forum_id;
	$d3forum_dirname =$d3f_forum_dir;
	$topic_external_link_id = $row->comment_post_ID; //There is information on WP post_ID in topic_external_link_id of D3Forum
	$topic_title = 'Re.' . addSlashes($row->post_title);
	$post_time = strtotime($row->comment_date_gmt);	
	$modified_time = strtotime($row->comment_date_gmt);
	require_once (get_xpress_dir_path() . 'include/general_functions.php');
	if (empty($row->user_id)){
		$uid = wp_comment_author_to_xoops_uid($row->comment_author,$row->comment_author_email);
	} else {
		$uid = wp_uid_to_xoops_uid($row->user_id,$mydirname);
	}
	$poster_ip = "'" . addslashes($row->comment_author_IP ). "'";
	$modifier_ip = "'" . addslashes($row->comment_author_IP) . "'";
	$subject = "'" . $topic_title . "'";
    $post_text = "'" . addSlashes($row->comment_content) . "'";
	$guest_name = "'" . addSlashes($row->comment_author) . "'";
    $guest_email = "'" . $row->comment_author_email . "'";
    $guest_url = "'" . $row->comment_author_url . "'";
    $approval = $row->comment_approved;
    $comment_count = $row->comment_count;
    $comment_parent = $row->comment_parent;
    
	if ($sync_mode == 'delete'){
		$mode = 'delete';
		$delete_post_id = $xoops_db->get_var("SELECT post_id FROM $wp_d3forum_link WHERE comment_ID = $comment_ID AND  blog_id = $blog_id");
		if (empty($delete_post_id)) return;
		$topic_id = $xoops_db->get_var("SELECT topic_id FROM $d3f_topic WHERE topic_external_link_id = $topic_external_link_id AND forum_id = $forum_id");
		if (empty($topic_id)) return;
	}else{
		// Does the first comment (= topic) on the post exist?
		$sql  =	"SELECT * FROM $d3f_topic WHERE topic_external_link_id = $topic_external_link_id AND forum_id = $forum_id";
		$row = $xoops_db->get_row($sql) ;
		$topic_first_post_id = $row->topic_first_post_id;
		if (empty($row)){
			$mode = $mode = 'newtopic';
		} else {
			$topic_id = $row->topic_id;
			
			// if comment on same ID exists then edits comment else reply comment
			$row = $xoops_db->get_row("SELECT * FROM $wp_d3forum_link WHERE comment_ID = $comment_ID AND blog_id = $blog_id" ) ;
			
			if (!empty($row)){
				$mode = $mode = 'edit';
				$edit_post_id = $row->post_id;
			} else {
				$mode = $mode = 'reply';
				$reply_pid = 0;
				if ($comment_parent > 0) {
					$reply_pid = get_d3forum_post_ID($comment_parent);
				}
				if ($reply_pid == 0) {
					$reply_pid = $topic_first_post_id; //reply_first_comment
				}
			}
		}
	}
	$modified_time = $post_time;
	
	// make set part of INSERT or UPDATE (refalence d3forum main/post.php)
	$set4sql = "modified_time= $modified_time , modifier_ip= $modifier_ip " ;
	$set4sql .= ",subject= $subject " ;
	$set4sql .= ",post_text= $post_text " ;
    	
	if($uid == 0) {
		@list( $guest_name , $trip_base ) = explode( '#' , $guest_name , 2 ) ;
		if( ! trim( @$guest_name ) ) $guest_name = get_xoops_config('anonymous_name',$d3f_forum_dir) ;
		if( ! empty( $trip_base ) && function_exists( 'crypt' ) ) {
			$salt = strtr( preg_replace( '/[^\.-z]/' , '.' , substr( $trip_base . 'H.' , 1 , 2 ) ) , ':;<=>?@[\]^_`' , 'ABCDEFGabcdef' ) ;
			$guest_trip = substr( crypt( $trip_base , $salt ) , -10 ) ;
		} else {
			$guest_trip = '' ;
		}
		$guest_url = preg_match( '#^https?\://#' , $guest_url ) ? $guest_url : '' ;
		foreach( array('guest_name','guest_email','guest_url','guest_trip') as $key ) {
			$set4sql .= ",$key='".addslashes($$key)."'" ;
		}
		if( ! empty( $guest_pass ) ) {
			$set4sql .= ",guest_pass_md5='".md5($guest_pass.'d3forum')."'" ;
		}
	}

	$hide_uid = get_xoops_config('allow_hideuid',$d3f_forum_dir);
	
	switch($mode){
		case 'edit':
			$edit_post = $xoops_db->get_row("SELECT * FROM $d3f_posts WHERE post_id= $edit_post_id ");
			if(empty($edit_post)) die( 'READ ' . $d3forum_comments . '_NG...' .$sql);
			// approval
			if( $approval ) {
				$set4sql .= ',approval=1' ;
				$topic_invisible = 0 ;
				$need_notify = true ;
			} else {
				$set4sql .= ',approval=0' ;
				$topic_invisible = 0 ;
				$need_admin_notify = true ;
			}
			// hide_uid
			if( $hide_uid ) {
				$set4sql .= ",uid=0,uid_hidden='$uid'" ;
			} else {
				$set4sql .= ",uid='$uid',uid_hidden=0" ;
			}

			// update post specified post_id
			wp_d3forum_transact_make_post_history( $d3forum_dirname , $edit_post_id ) ;
			$sql = "UPDATE ".$d3f_posts." SET $set4sql WHERE post_id=$edit_post_id";
			$xoops_db->query($sql);
			$xoops_db->query($sql) ;
			if ($edit_post_pid == 0){
				$sql = "UPDATE ".$d3f_topic." SET topic_invisible=$topic_invisible WHERE topic_id=$topic_id";
				$xoops_db->query($sql);
			}
			wp_d3forum_sync_topic( $d3forum_dirname , $topic_id , true , ! $edit_post_pid ) ;
			break;
			
		case 'reply' :
			// approval
			if( $approval ) {
				$set4sql .= ',approval=1' ;
				$need_notify = true ;
			} else {
				$set4sql .= ',approval=0' ;
				$need_admin_notify = true ;
			}

			// hide_uid
			if( $hide_uid ) {
				$set4sql .= ",uid=0,uid_hidden='$uid'" ;
			} else {
				$set4sql .= ",uid='$uid',uid_hidden=0" ;
			}

			// create post under specified post_id
			$sql = "INSERT INTO ".$d3f_posts." SET $set4sql,pid=$reply_pid,topic_id=$topic_id,post_time=$post_time,poster_ip=$poster_ip";
			$xoops_db->query($sql) ;
			$post_id = mysql_insert_id();
			wp_d3forum_sync_topic( $d3forum_dirname , $topic_id ) ;
			
			$wp_sql  = "INSERT INTO $wp_d3forum_link ";
			$wp_sql .=    "(comment_ID , post_id, wp_post_ID, forum_id, blog_id) ";
			$wp_sql .=  "VALUES ";
			$wp_sql .=    "($comment_ID, $post_id, $topic_external_link_id, $d3f_forum_id, $blog_id)";		
			$xoops_db->query($wp_sql);
			
			break;

		case 'newtopic':
			// approval
			if( $approval ) {
				$set4sql .= ',approval=1' ;
				$topic_invisible = 0 ;
				$need_notify = true ;
			} else {
				$set4sql .= ',approval=0' ;
				$topic_invisible = 0 ;
				$need_admin_notify = true ;
			}

			// hide_uid
			if( $hide_uid ) {
				$set4sql .= ",uid=0,uid_hidden='$uid'" ;
			} else {
				$set4sql .= ",uid='$uid',uid_hidden=0" ;
			}

			// create topic and get a new topic_id
			$sql = "INSERT INTO ".$d3f_topic." SET forum_id=$forum_id,topic_invisible=$topic_invisible,topic_external_link_id='".addslashes($topic_external_link_id)."'";
			$xoops_db->query($sql) ;
			$topic_id = mysql_insert_id();
			// create post in the topic
			$sql = "INSERT INTO ".$d3f_posts." SET $set4sql,topic_id=$topic_id,post_time=$post_time,poster_ip=$poster_ip";
			$xoops_db->query($sql) ;
			$post_id = mysql_insert_id();
			wp_d3forum_sync_topic( $d3forum_dirname , $topic_id , true , true ) ;
			
			$wp_sql  = "INSERT INTO $wp_d3forum_link ";
			$wp_sql .=    "(comment_ID , post_id , wp_post_ID, forum_id, blog_id) ";
			$wp_sql .=  "VALUES ";
			$wp_sql .=    "($comment_ID, $post_id, $topic_external_link_id, $d3f_forum_id, $blog_id)";		
			$xoops_db->query($wp_sql);

			break;
		case 'delete':
			wp_d3forum_delete_post_recursive( $d3forum_dirname , $delete_post_id );
			wp_d3forum_sync_topic( $d3forum_dirname , $topic_id ) ;
			break;
		default:				
	}
	
	// increment post
	if( is_object( @$xoopsUser ) && $mode != 'edit' ) {
		$xoopsUser->incrementPost() ;
	}
	// set u2t_marked
	$allow_mark = get_xoops_config('allow_mark',$d3f_forum_dir);

	if( $uid && $allow_mark) {
		$u2t_marked = empty( $_POST['u2t_marked'] ) ? 0 : 1 ;
		$sql = "UPDATE ".$d3forum_prefix."users2topics"." SET u2t_marked=$u2t_marked,u2t_time=UNIX_TIMESTAMP() WHERE uid=$uid AND topic_id=$topic_id" ;
		if( ! $xoops_db->query($sql)){
			$sql = "INSERT INTO ".$d3forum_prefix."users2topics"." SET uid=$uid,topic_id=$topic_id,u2t_marked=$u2t_marked,u2t_time=UNIX_TIMESTAMP()" ;
			$xoops_db->query($sql);
		}
	}
}


// ********************** refrence by d3forum *********************************************
// delete posts recursively
function wp_d3forum_delete_post_recursive( $d3forum_dirname , $post_id ,$isChild = false)
{
	global $wpdb,$blog_id;
	global $xpress_config,$xoops_db;
	$post_id = intval( $post_id ) ; // post_id is d3forum post(comments) id.
	if (empty($blog_id)) $blog_id =1;

	
	$d3forum_prefix = get_xoops_prefix() . $d3forum_dirname . '_';
	$xpress_prefix = get_wp_prefix();
	
	$topic_id = $xoops_db->get_var("SELECT topic_id FROM ".$d3forum_prefix."posts WHERE post_id=$post_id");
	
	//It deletes it if there is a child comment. 
	$sql = "SELECT post_id FROM ".$d3forum_prefix ."posts"." WHERE pid=$post_id" ;
	$child_comments = $xoops_db->get_results("SELECT post_id FROM ".$d3forum_prefix ."posts"." WHERE pid=$post_id"); 
	foreach($child_comments as $child_comment){
			wp_d3forum_delete_post_recursive( $d3forum_dirname , $child_comment->post_id ,true) ;
	}
	wp_d3forum_transact_make_post_history( $d3forum_dirname , $post_id , true ) ;
	$xoops_db->query( "DELETE FROM ".$d3forum_prefix."posts WHERE post_id=$post_id" ) ;
	$xoops_db->query( "DELETE FROM ".$d3forum_prefix."post_votes WHERE post_id=$post_id" ) ;
	
	$wp_comments = $wpdb->comments;
	$wp_posts = $wpdb->posts;
	$wp_d3forum_link = $xpress_prefix . 'd3forum_link';

	$comment_ID = get_wp_comment_ID($post_id);  // get wordpress comment ID
	if ($comment_ID > 0){
		$comment_post_ID = $wpdb->get_var("SELECT comment_post_ID FROM $wp_comments WHERE comment_ID = $comment_ID");
		if ($isChild){		//The first comment is deleted on the WordPress side. 
			$wpdb->query("DELETE FROM $wp_comments WHERE comment_ID = $comment_ID");
			if (!empty($comment_post_ID)){
				$wpdb->query("UPDATE $wp_posts SET  comment_count = comment_count -1 WHERE ID = $comment_post_ID");
			}
		}	
		$xoops_db->query("DELETE FROM $wp_d3forum_link WHERE post_id = $post_id AND blog_id = $blog_id");
	}
}


// delete a topic 
function wp_d3forum_delete_topic( $d3forum_dirname , $topic_id , $delete_also_posts = true )
{
	global $xpress_config,$xoops_db;
	$d3forum_prefix = get_xoops_prefix() . $d3forum_dirname . '_';

	$topic_id = intval( $topic_id ) ;

	// delete posts
	if( $delete_also_posts ) {
		$posts = $xoops_db->query("SELECT post_id FROM ".$d3forum_prefix."posts WHERE topic_id=$topic_id");
		if( !empty($posts)) {
			foreach($posts as $post){
				wp_d3forum_delete_post_recursive( $d3forum_dirname , $post->post_id ) ;
			}
		}
	}

	// delete notifications about this topic

	// delete topic
	$xoops_db->query( "DELETE FROM ".$d3forum_prefix."topics WHERE topic_id=$topic_id" );
	// delete u2t
	$xoops_db->query( "DELETE FROM ".$d3forum_prefix."users2topics WHERE topic_id=$topic_id" );
}


// store redundant informations to a topic from its posts
// and rebuild tree informations (depth, order_in_tree)
function wp_d3forum_sync_topic( $d3forum_dirname , $topic_id , $sync_also_forum = true , $sync_topic_title = false )
{
	global $xpress_config,$xoops_db;
	$d3forum_prefix = get_xoops_prefix() . $d3forum_dirname . '_';
	$xpress_prefix = get_wp_prefix();

	$topic_id = intval( $topic_id ) ;

	$forum_id = $xoops_db->get_var("SELECT forum_id FROM ".$d3forum_prefix."topics WHERE topic_id=$topic_id");

	// get first_post_id
	$first_post_id = $xoops_db->get_var("SELECT post_id FROM ".$d3forum_prefix."posts WHERE topic_id=$topic_id AND pid=0");

	// get last_post_id and total_posts
	$sql = "SELECT MAX(post_id) as last_post_id,COUNT(post_id) as total_posts FROM ".$d3forum_prefix."posts WHERE topic_id=$topic_id" ;
	$row = $xoops_db->get_row($sql);
	$last_post_id = $row->last_post_id;
	$total_posts = $row->total_posts;

	if( empty( $total_posts ) ) {
		// this is empty topic should be removed
		wp_d3forum_delete_topic( $d3forum_dirname , $topic_id ,0) ;

	} else {

		// update redundant columns in topics table
		$row = $xoops_db->get_row( "SELECT post_time,uid,subject,unique_path FROM ".$d3forum_prefix."posts WHERE post_id=$first_post_id" ) ;
		$first_post_time = $row->post_time;
		$first_uid = $row->uid;
		$first_subject = $row->subject;
		$unique_path = $row->unique_path;
		$row = $xoops_db->get_row( "SELECT post_time,uid FROM ".$d3forum_prefix."posts WHERE post_id=$last_post_id" ) ;
		$last_post_time = $row->post_time;
		$last_uid = $row->uid;
		// sync topic_title same as first post's subject if specified
		$topictitle4set = $sync_topic_title ? "topic_title='".addslashes($first_subject)."'," : "" ;

		$xoops_db->query( "UPDATE ".$d3forum_prefix."topics SET {$topictitle4set} topic_posts_count=$total_posts, topic_first_uid=$first_uid, topic_first_post_id=$first_post_id, topic_first_post_time=$first_post_time, topic_last_uid=$last_uid, topic_last_post_id=$last_post_id, topic_last_post_time=$last_post_time WHERE topic_id=$topic_id" );

		// rebuild tree informations
		$tree_array = wp_d3forum_maketree_recursive( $d3forum_prefix."posts" , intval( $first_post_id ) , 'post_id' , array() , 0 , empty( $unique_path ) ? '.1' : $unique_path ) ;
		if( ! empty( $tree_array ) ) {
			foreach( $tree_array as $key => $val ) {
				$xoops_db->query( "UPDATE ".$d3forum_prefix."posts SET depth_in_tree=".$val['depth'].", order_in_tree=".($key+1).", unique_path='".addslashes($val['unique_path'])."' WHERE post_id=".$val['post_id'] ) ;
			}
		}
	}

	if( $sync_also_forum ) 
		return wp_d3forum_sync_forum( $d3forum_dirname , $forum_id ) ;
	else 
		return true ;
}

function wp_d3forum_maketree_recursive( $tablename , $post_id , $order = 'post_id' , $parray = array() , $depth = 0 , $unique_path = '.1' )
{
	global $xpress_config,$xoops_db;

	$parray[] = array( 'post_id' => $post_id , 'depth' => $depth , 'unique_path' => $unique_path ) ;

	$sql = "SELECT post_id,unique_path FROM $tablename WHERE pid=$post_id ORDER BY $order" ;
	if( ! $result = $xoops_db->get_results( $sql )) {
		return $parray ;
	}
	$new_post_ids = array() ;
	$max_count_of_last_level = 0 ;
	foreach($result as $row){
		$new_post_id = $row->post_id;
		$new_unique_path = $row->unique_path;
		$new_post_ids[ intval( $new_post_id ) ] = $new_unique_path ;
		if( ! empty( $new_unique_path ) ) {
			$count_of_last_level = intval( substr( strrchr( $new_unique_path , '.' ) , 1 ) ) ;
			if( $max_count_of_last_level < $count_of_last_level ) {
				$max_count_of_last_level = $count_of_last_level ;
			}
		}
	}
	foreach( $new_post_ids as $new_post_id => $new_unique_path ) {
		if( empty( $new_unique_path ) ) {
			$new_unique_path = $unique_path . '.' . ++ $max_count_of_last_level ;
		}
		$parray = wp_d3forum_maketree_recursive( $tablename , $new_post_id , $order , $parray , $depth + 1 , $new_unique_path ) ;
	}
	return $parray ;
}

function wp_d3forum_makecattree_recursive( $tablename , $cat_id , $order = 'cat_weight' , $parray = array() , $depth = 0 , $cat_title = '' )
{
	global $xoops_db;

	$parray[] = array( 'cat_id' => $cat_id , 'depth' => $depth , 'cat_title' => $cat_title ) ;

	$sql = "SELECT cat_id,cat_title FROM $tablename WHERE pid=$cat_id ORDER BY $order" ;
	$results = $xoops_db->get_results( $sql ) ;
	if( empty($results) ) {
		return $parray ;
	}
	foreach($result as $row){
		$new_cat_id = $row->cat_id;
		$new_cat_title = $row->cat_title;
		$parray = wp_d3forum_makecattree_recursive( $tablename , $new_cat_id , $order , $parray , $depth + 1 , $new_cat_title ) ;
	}
	return $parray ;
}


// store redundant informations to a forum from its topics
function wp_d3forum_sync_forum( $d3forum_dirname , $forum_id , $sync_also_category = true )
{
	global $xoops_db;
	$d3forum_prefix = get_xoops_prefix() . $d3forum_dirname . '_';

	$forum_id = intval( $forum_id ) ;

	$sql = "SELECT cat_id FROM ".$d3forum_prefix."forums WHERE forum_id=$forum_id" ;
	if( ! $cat_id = $xoops_db->get_var( $sql ) ) die( "ERROR SELECT forum in sync forum" ) ;

	$sql = "SELECT MAX(topic_last_post_id) as last_post_id ,MAX(topic_last_post_time) as last_post_time ,COUNT(topic_id) as topics_count,SUM(topic_posts_count) as posts_count FROM ".$d3forum_prefix."topics WHERE forum_id=$forum_id" ;
	if( ! $row = $xoops_db->get_row( $sql ) ) die( "ERROR SELECT topics in sync forum" ) ;
	$last_post_id = $row->last_post_id;
	$last_post_time = $row->last_post_time;
	$topics_count = $row->topics_count;
	$posts_count = $row->posts_count;

	$xoops_db->query( "UPDATE ".$d3forum_prefix."forums SET forum_topics_count=".intval($topics_count).",forum_posts_count=".intval($posts_count).", forum_last_post_id=".intval($last_post_id).", forum_last_post_time=".intval($last_post_time)." WHERE forum_id=$forum_id" ) ;

	if( $sync_also_category ) return wp_d3forum_sync_category( $d3forum_dirname , $cat_id ) ;
	else return true ;
}

function get_d3forum_all_child_catid($d3forum_prefix,$sel_id, $order="", $idarray = array())
{
	global $xoops_db;
	$sql = "SELECT * FROM ".$d3forum_prefix."categories WHERE pid =".$sel_id."";
	if ( $order != "" ) {
		$sql .= " ORDER BY $order";
	}
	$categories =$xoops_db->get_results($sql);
	if ( empty($cat_ids)) {
		return $idarray;
	}
	foreach( categories as $categorie ) {
		$r_id = $categorie->cat_id;
		array_push($idarray, $r_id);
		$idarray = get_d3forum_all_child_catid($d3forum_prefix, $r_id,$order,$idarray);
	}
	return $idarray;
}

// store redundant informations to a category from its forums
function wp_d3forum_sync_category( $d3forum_dirname , $cat_id )
{
	global $xoops_db;
	$d3forum_prefix = get_xoops_prefix() . $d3forum_dirname . '_';

	$cat_id = intval( $cat_id ) ;

	// get children
	$children = get_d3forum_all_child_catid( $d3forum_prefix."categories" , $cat_id ) ;
	$children[] = $cat_id ;
	$children = array_map( 'intval' , $children ) ;

	// topics/posts information belonging this category directly
	$sql = "SELECT MAX(forum_last_post_id) as last_post_id,MAX(forum_last_post_time) as last_post_time,SUM(forum_topics_count) as topics_count,SUM(forum_posts_count) as posts_count FROM ".$d3forum_prefix."forums WHERE cat_id=$cat_id" ;
	if( ! $row = $xoops_db->get_row( $sql ) ) die( "ERROR SELECT forum in sync category" ) ;
	$last_post_id = $row->last_post_id ;
	$last_post_time = $row->last_post_time ;
	$topics_count = $row->topics_count ;
	$posts_count = $row->posts_count ;

	// topics/posts information belonging this category and/or subcategories
	$sql = "SELECT MAX(forum_last_post_id) as last_post_id_in_tree,MAX(forum_last_post_time) as last_post_time_in_tree,SUM(forum_topics_count) as topics_count_in_tree,SUM(forum_posts_count) as posts_count_in_tree FROM ".$d3forum_prefix."forums WHERE cat_id IN (".implode(",",$children).")" ;
	if( ! $row = $xoops_db->get_row( $sql ) ) die( "ERROR SELECT forum in sync category" ) ;
	$last_post_id_in_tree = $row->last_post_id_in_tree ;
	$last_post_time_in_tree = $row->last_post_time_in_tree ;
	$topics_count_in_tree = $row->topics_count_in_tree ;
	$posts_count_in_tree = $row->posts_count_in_tree ;

	// update query
	$xoops_db->query( "UPDATE ".$d3forum_prefix."categories SET cat_topics_count=".intval($topics_count).",cat_posts_count=".intval($posts_count).", cat_last_post_id=".intval($last_post_id).", cat_last_post_time=".intval($last_post_time).",cat_topics_count_in_tree=".intval($topics_count_in_tree).",cat_posts_count_in_tree=".intval($posts_count_in_tree).", cat_last_post_id_in_tree=".intval($last_post_id_in_tree).", cat_last_post_time_in_tree=".intval($last_post_time_in_tree)." WHERE cat_id=$cat_id" );

	// do sync parents
	$pid = $xoops_db->get_var( "SELECT pid FROM ".$d3forum_prefix."categories WHERE cat_id=$cat_id" )  ;
	if( $pid != $cat_id && $pid > 0 ) {
		wp_d3forum_sync_category( $d3forum_dirname , $pid ) ;
	}

	return true ;
}

// make a new history entry for a post
function wp_d3forum_transact_make_post_history( $d3forum_dirname , $post_id , $full_backup = false )
{
	global $xoops_db;
	$d3forum_prefix = get_xoops_prefix() . $d3forum_dirname . '_';

	$post_id = intval( $post_id ) ;

	$results = $xoops_db->get_results( "SELECT * FROM ".$d3forum_prefix."posts WHERE post_id=$post_id" ) ;
	if(empty($results)) return ;
	$post_row = $results ;
	$data = array() ;
	$indexes = $full_backup ? array_keys( $post_row ) : array( 'subject' , 'post_text' ) ;
	foreach( $indexes as $index ) {
		$data[ $index ] = $post_row[ $index ] ;
	}

	// check the latest data in history
	$old_data_serialized = $xoops_db->get_var( "SELECT data FROM ".$d3forum_prefix."post_histories WHERE post_id=$post_id ORDER BY history_time DESC" ) ;
	if( !empty( $old_data_serialized ) ) {
		$old_data = unserialize( $old_data_serialized ) ;
		if( $old_data == $data ) return ;
	}

	$xoops_db->query( "INSERT INTO ".$d3forum_prefix."post_histories SET post_id=$post_id, history_time=UNIX_TIMESTAMP(), data='".mysql_real_escape_string( serialize( $data ) )."'" ) ;
}

?>