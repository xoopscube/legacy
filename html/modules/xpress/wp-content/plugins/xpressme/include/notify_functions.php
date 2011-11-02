<?php

function onaction_publish_post_notify($new_status, $old_status, $post)
{
	if ($new_status == 'publish'){
		do_PostNotifications($post->ID,'newpost');
	}
}

function onaction_edit_post_notify($post_id)
{
	do_PostNotifications($post_id,'editpost');
}

function onaction_comment_notify($commentID){
	global $wpdb;
	$status = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_ID = $commentID");
	$post_id = $wpdb->get_var("SELECT comment_post_ID FROM $wpdb->comments WHERE comment_ID = $commentID");

	if ($status ==1){
		do_CommentNotifications($commentID, $post_id);
	} else {
		do_CommentWaiting($commentID, $post_id);
	}
}

function onaction_comment_apobe_notify($commentID){
	global $wpdb;
	$comment_type = $wpdb->get_var("SELECT comment_type FROM $wpdb->comments WHERE comment_ID = $commentID");
	$status = $wpdb->get_var("SELECT comment_approved FROM $wpdb->comments WHERE comment_ID = $commentID");
	if(is_null($status)) return;
	if ($status == 1){
			onaction_comment_notify($commentID);
	}
}

function Notification_triggerEvent($force_reserve = false,$category, $item_id, $event, $extra_tags=array(), $user_list=array(), $omit_user_id=null)
{
	global $xoops_db,$xoops_config;
	global $xoopsModule,$xoopsUser,$xoopsUserIsAdmin;

	//When notifying by a private message, 
	//it is evaded that the data base becomes read-only as a result of the check on the referrer and the method. 
	if ( defined("XPRESS_EVENT_DEBUG")) xpress_debug_message($message = 'call $notification_handler->triggerEvent');
	if (is_wp_cron_page_call() ){
		$_SERVER['HTTP_REFERER'] = 'http://'. $_SERVER[HTTP_HOST]  . $_SERVER['PHP_SELF'];
		$_SERVER['REQUEST_METHOD'] = 'POST';
		if (function_exists('xpress_debug')) xpress_debug($title = 'wp_cron_page_call',true);
	}
	if (is_xmlrpc_call() ){
		$_SERVER['HTTP_REFERER'] = 'http://'. $_SERVER[HTTP_HOST]  . $_SERVER['PHP_SELF'];
		$_SERVER['REQUEST_METHOD'] = 'POST';
	}
//	set_error_handler("xpress_error_handler");
//	if ($xoops_config->is_impress != true){  // impress cms is error
//		if ( !defined("XOOPS_MAINFILE_INCLUDED")) {
//			require_once $xoops_config->xoops_mainfile_path;	// load XOOPS System
//		}
//	}
	if (!$force_reserve && defined("XOOPS_MAINFILE_INCLUDED") ) {
		if ( defined("XPRESS_EVENT_DEBUG")) xpress_debug_message($message = 'call $notification_handler->triggerEvent');
		$module_id = get_xpress_modid() ;
		$notification_handler =& xoops_gethandler( 'notification' ) ;
		$notification_handler->triggerEvent($category, $item_id, $event, $extra_tags, $user_list, $module_id, $omit_user_id);
	} else {
		if ( defined("XPRESS_EVENT_DEBUG")) xpress_debug_message($message = 'not call $notification_handler->triggerEvent');
		$module_id = get_xpress_modid() ;
		Notification_reserve($category, $item_id, $event, $extra_tags, $user_list, $module_id, $omit_user_id);
	}
}

function do_CommentWaiting($commentID, $comment_post_ID)
{
//	require_once XOOPS_ROOT_PATH . '/include/notification_functions.php' ;
//	$notification_handler =& xoops_gethandler( 'notification' ) ;

	// Fixed Compile Error : /wp-includes/class-phpmailer.php - Cannot redeclare class PHPMailer
	$comments_notify = get_option('comments_notify');
	if($comments_notify) $force_reserve = true; else $force_reserve = false;
	
	Notification_triggerEvent($force_reserve,'global' , 0 , 'waiting') ;
}


function do_CommentNotifications($commentID, $comment_post_ID)
{
	global $wpdb, $xoops_config , $xoops_db;

	$table_term_relationships = $wpdb->term_relationships;
	$table_term_taxonomy = $wpdb->term_taxonomy;
	$table_terms = $wpdb->terms;
	$table_categories = $wpdb->categories;
	$wp_post = $wpdb->posts;
	$wp_options = $wpdb->options;
	$wp_users  = $wpdb->users;
	$wp_comments  = $wpdb->comments;
	
	$post_id = $comment_post_ID;

	$post_title = get_the_title($post_id);
	$post_url = get_permalink($post_id). '#comment';
	$blog_name = get_bloginfo('name');

	// query
	$sql = "SELECT post_author FROM ".$wp_post." WHERE ID=$comment_post_ID ";
	$post_author = $xoops_db->get_var($sql);

	$sql = "SELECT display_name  FROM $wp_users WHERE ID ='$post_author'";
	$user_name = $xoops_db->get_var($sql);

	$comment_tags = array( 'XPRESS_AUTH_NAME' =>$user_name,'XPRESS_BLOG_NAME' =>$blog_name,'XPRESS_POST_TITLE' => $post_title , 'XPRESS_POST_URL' => $post_url ) ;

	// Fixed Compile Error : /wp-includes/class-phpmailer.php - Cannot redeclare class PHPMailer
	$moderation_notify = get_option('moderation_notify');
	if($moderation_notify) $force_reserve = true; else $force_reserve = false;

	Notification_triggerEvent($force_reserve, 'global' , 0 , 'comment' , $comment_tags , false);
	Notification_triggerEvent($force_reserve, 'author' , $post_author , 'comment' , $comment_tags , false);
	Notification_triggerEvent($force_reserve, 'post' , $comment_post_ID , 'comment' , $comment_tags , false);

	// categorie notification
	if (get_xpress_db_version() < 6124){
		$sql2 = "SELECT c.cat_ID, c.cat_name FROM ".$table_categories." c, ".$table_post2cat." p2c WHERE c.cat_ID = p2c.category_id AND p2c.post_id=".$comment_post_ID;
	} else {
		$sql2  = "SELECT $table_term_relationships.object_id, $table_terms.term_id AS cat_ID, $table_terms.name AS cat_name ";
		$sql2 .= "FROM $table_term_relationships INNER JOIN ($table_term_taxonomy INNER JOIN $table_terms ON $table_term_taxonomy.term_id = $table_terms.term_id) ON $table_term_relationships.term_taxonomy_id = $table_term_taxonomy.term_taxonomy_id ";
		$sql2 .= "WHERE ($table_term_relationships.object_id =" . $comment_post_ID.") AND ($table_term_taxonomy.taxonomy='category')";		
	}
	$categories = $xoops_db->get_results($sql2);
	foreach($categories as $categorie){
		$cat_id = $categorie->cat_ID;
		$cat_name = $categorie->cat_name;
		$comment_tags = array( 'XPRESS_AUTH_NAME' =>$user_name,'XPRESS_BLOG_NAME' =>$blog_name,'XPRESS_CAT_TITLE' => $cat_name,'XPRESS_POST_TITLE' => $post_title , 'XPRESS_POST_URL' => $post_url ) ;
		Notification_triggerEvent($force_reserve, 'category' , $cat_id , 'comment' , $comment_tags , false);

	}
}

function do_PostNotifications($post_id,$not_event)
{
	global $wpdb, $xoops_config, $xoops_db;

	 // $not_event:		newpost,editpost ; $commentID, $comment_post_ID)
	
	$table_term_relationships = $wpdb->term_relationships;
	$table_term_taxonomy = $wpdb->term_taxonomy;
	$table_terms = $wpdb->terms;
	$table_categories = $wpdb->categories;
	$wp_post = $wpdb->posts;
	$wp_options = $wpdb->options;
	$wp_users  = $wpdb->users;
	$wp_comments  = $wpdb->comments;

	$post_title = get_the_title($post_id);
	$post_url = get_permalink($post_id);
	$blog_name = get_bloginfo('name');

	// query
	$sql = "SELECT post_author FROM ".$wp_post." WHERE ID=$post_id ";
	$post_author = $xoops_db->get_var($sql);

	$sql = "SELECT display_name  FROM $wp_users WHERE ID ='$post_author'";
	$user_name = $xoops_db->get_var($sql);

	$posts_tags = array( 'XPRESS_AUTH_NAME' =>$user_name,'XPRESS_BLOG_NAME' =>$blog_name,'XPRESS_POST_TITLE' => $post_title , 'XPRESS_POST_URL' => $post_url ) ;
	$force_reserve = false;
	switch ($not_event) {
		case 'newpost' :
			Notification_triggerEvent($force_reserve, 'global' , 0 , 'newpost' , $posts_tags , false);
			Notification_triggerEvent($force_reserve, 'author' , $post_author , 'newpost' , $posts_tags , false);

			// categorie notification
			if (get_xpress_db_version() < 6124){
				$sql2 = "SELECT c.cat_ID, c.cat_name FROM ".$table_categories." c, ".$table_post2cat." p2c WHERE c.cat_ID = p2c.category_id AND p2c.post_id=".$post_id;
			} else {
				$sql2  = "SELECT $table_term_relationships.object_id, $table_terms.term_id AS cat_ID, $table_terms.name AS cat_name ";
				$sql2 .= "FROM $table_term_relationships INNER JOIN ($table_term_taxonomy INNER JOIN $table_terms ON $table_term_taxonomy.term_id = $table_terms.term_id) ON $table_term_relationships.term_taxonomy_id = $table_term_taxonomy.term_taxonomy_id ";
				$sql2 .= "WHERE ($table_term_relationships.object_id =" . $post_id.") AND ($table_term_taxonomy.taxonomy='category')";		
			}
			$categories = $xoops_db->get_results($sql2);
			foreach($categories as $categorie){
				$cat_id = $categorie->cat_ID;
				$cat_name = $categorie->cat_name;
				$posts_tags = array( 'XPRESS_AUTH_NAME' =>$user_name,'XPRESS_BLOG_NAME' =>$blog_name,'XPRESS_CAT_TITLE' => $cat_name,'XPRESS_POST_TITLE' => $post_title , 'XPRESS_POST_URL' => $post_url ) ;
				Notification_triggerEvent($force_reserve, 'category' , $cat_id , 'newpost' , $posts_tags , false);
			}
			break;
		case 'editpost' :
			Notification_triggerEvent($force_reserve, 'post' , $post_id , 'editpost' , $posts_tags , false);
			break;
		default :
	}
}

//When the event cannot notify because the XOOPS system is not loaded, the event is stacked. 
function Notification_reserve($category, $item_id=0, $event, $extra_tags=array(), $user_list=array(), $module_id=0, $omit_user_id=null)
{
	global $xpress_config,$xoops_db;
	
	$xpress_prefix = get_wp_prefix();
	$notfiy_reserve = $xpress_prefix . 'notify_reserve';

	$extra_tags_arry = addslashes(serialize($extra_tags));
	$user_list_arry = addslashes(serialize($user_list));
//	$extra_tags_arry = mysql_real_escape_string(serialize($extra_tags));
//	$user_list_arry = mysql_real_escape_string(serialize($user_list));
	
	$notify_reserve_status = 'reserve';

	$sql  = "INSERT INTO $notfiy_reserve ";
	$sql .=    "(notify_reserve_status , category , item_id , event , extra_tags_arry , user_list_arry , module_id , omit_user_id)";
	$sql .=  "VALUES ";
	$sql .=    "('$notify_reserve_status' , '$category' , $item_id , '$event' , '$extra_tags_arry' , '$user_list_arry' , $module_id , '$omit_user_id')";
	if ( defined("XPRESS_EVENT_DEBUG")) xpress_debug_message($message = $sql);

	$xoops_db->query($sql);
}

//It calls when the XOOPS system is loaded, and the stacked event notification processing is done. 
function Notification_reserve_send()
{
	global $xpress_config,$xoops_db;
	if ( ! defined("XOOPS_MAINFILE_INCLUDED")) return;
	
	$notification_handler =& xoops_gethandler( 'notification' ) ;
	
	$xpress_prefix = get_wp_prefix();
	$notfiy_reserve_db = $xpress_prefix . 'notify_reserve';

	$extra_tags_arry = addslashes(serialize($extra_tags));
	$user_list_arry = addslashes(serialize($user_list));
	
	$sql  = "SELECT * ";
	$sql .= "FROM $notfiy_reserve_db ";
	$sql .= "WHERE  notify_reserve_status = 'reserve'";

	$notify_reserves = $xoops_db->get_results($sql);

	//So as not to process it by other sessions while processing it, status is changed. 
	foreach($notify_reserves as $notify){
		$notify_reserve_id = $notify->notify_reserve_id;
		$sql  = "UPDATE $notfiy_reserve_db SET  notify_reserve_status = 'sending' WHERE notify_reserve_id = $notify_reserve_id";
		$xoops_db->query($sql);
	}

	foreach($notify_reserves as $notify){
		$notify_reserve_id = $notify->notify_reserve_id;
		$category = $notify->category;
		$item_id = $notify->item_id;
		$event = $notify->event;
		$extra_tags = unserialize($notify->extra_tags_arry);
		$user_list = unserialize($notify->user_list_arry);
		$module_id = $notify->module_id;
		$omit_user_id = $notify->omit_user_id;
		$notification_handler->triggerEvent($category, $item_id, $event, $extra_tags, $user_list, $module_id, $omit_user_id);
		$sql  = "DELETE FROM  $notfiy_reserve_db WHERE notify_reserve_id = $notify_reserve_id";
		$xoops_db->query($sql);
	}
}

?>