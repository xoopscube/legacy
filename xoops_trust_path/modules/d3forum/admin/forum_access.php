<?php

require_once dirname(dirname(__FILE__)).'/include/main_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/d3forum.textsanitizer.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$myts =& D3forumTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

// get right $forum_id
$forum_id = intval( @$_GET['forum_id'] ) ;
list( $forum_id , $forum_title ) = $db->fetchRow( $db->query( "SELECT forum_id,forum_title FROM ".$db->prefix($mydirname."_forums")." WHERE forum_id=$forum_id" ) ) ;
if( empty( $forum_id ) ) {
	$invalid_forum_id = true ;
	list( $forum_id ) = $db->fetchRow( $db->query( "SELECT MIN(forum_id) FROM ".$db->prefix($mydirname."_forums") ) ) ;
	if( empty( $forum_id ) ) {
		redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=category_access" , 5 , _MD_A_D3FORUM_ERR_CREATEFORUMFIRST ) ;
		exit ;
	} else {
		header( "Location: ".XOOPS_URL."/modules/$mydirname/admin/index.php?page=forum_access&forum_id=$forum_id" ) ;
		exit ;
	}
}


//
// transaction stage
//

// group update
if( ! empty( $_POST['group_update'] ) && empty( $invaild_forum_id ) ) {
	if ( ! $xoopsGTicket->check( true , 'd3forum_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	$db->query( "DELETE FROM ".$db->prefix($mydirname."_forum_access")." WHERE forum_id=$forum_id AND groupid>0" ) ;
	$result = $db->query( "SELECT groupid FROM ".$db->prefix("groups") ) ;
	while( list( $gid ) = $db->fetchRow( $result ) ) {
		if( ! empty( $_POST['can_reads'][$gid] ) ) {
			$can_post = empty( $_POST['can_posts'][$gid] ) ? 0 : 1 ;
			$can_edit = empty( $_POST['can_edits'][$gid] ) ? 0 : 1 ;
			$can_delete = empty( $_POST['can_deletes'][$gid] ) ? 0 : 1 ;
			$post_auto_approved = empty( $_POST['post_auto_approveds'][$gid] ) ? 0 : 1 ;
			$is_moderator = empty( $_POST['is_moderators'][$gid] ) ? 0 : 1 ;

			$db->query( "INSERT INTO ".$db->prefix($mydirname."_forum_access")." SET forum_id=$forum_id, groupid=$gid, can_post=$can_post, can_edit=$can_edit, can_delete=$can_delete, post_auto_approved=$post_auto_approved, is_moderator=$is_moderator" ) ;
		}
	}
	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=forum_access&amp;forum_id=$forum_id" , 3 , _MD_D3FORUM_MSG_UPDATED ) ;
	exit ;
}

// user update
if( ! empty( $_POST['user_update'] ) && empty( $invaild_forum_id ) ) {
	if ( ! $xoopsGTicket->check( true , 'd3forum_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	$db->query( "DELETE FROM ".$db->prefix($mydirname."_forum_access")." WHERE forum_id=$forum_id AND uid>0" ) ;
	$can_posts = is_array( @$_POST['can_posts'] ) ? $_POST['can_posts'] : array() ;
	$can_reads = is_array( @$_POST['can_reads'] ) ? $_POST['can_reads'] + $can_posts : $can_posts ;

	foreach( $can_reads as $uid => $can_read ) {
		$uid = intval( $uid ) ;
		if( $can_read ) {
			$can_post = empty( $_POST['can_posts'][$uid] ) ? 0 : 1 ;
			$can_edit = empty( $_POST['can_edits'][$uid] ) ? 0 : 1 ;
			$can_delete = empty( $_POST['can_deletes'][$uid] ) ? 0 : 1 ;
			$post_auto_approved = empty( $_POST['post_auto_approveds'][$uid] ) ? 0 : 1 ;
			$is_moderator = empty( $_POST['is_moderators'][$uid] ) ? 0 : 1 ;

			$db->query( "INSERT INTO ".$db->prefix($mydirname."_forum_access")." SET forum_id=$forum_id, uid=$uid, can_post=$can_post, can_edit=$can_edit, can_delete=$can_delete, post_auto_approved=$post_auto_approved, is_moderator=$is_moderator" ) ;
		}
	}
	
	$member_hander =& xoops_gethandler( 'member' ) ;
	if( is_array( @$_POST['new_uids'] ) ) foreach( $_POST['new_uids'] as $i => $uid ) {
		$can_post = empty( $_POST['new_can_posts'][$i] ) ? 0 : 1 ;
		$can_edit = empty( $_POST['new_can_edits'][$i] ) ? 0 : 1 ;
		$can_delete = empty( $_POST['new_can_deletes'][$i] ) ? 0 : 1 ;
		$post_auto_approved = empty( $_POST['new_post_auto_approveds'][$i] ) ? 0 : 1 ;
		$is_moderator = empty( $_POST['new_is_moderators'][$i] ) ? 0 : 1 ;
		if( empty( $uid ) ) {
			$criteria =& new Criteria( 'uname' , addslashes( @$_POST['new_unames'][$i] ) ) ;
			@list( $user ) = $member_handler->getUsers( $criteria ) ;
		} else {
			$user =& $member_handler->getUser( intval( $uid ) ) ;
		}
		if( is_object( $user ) ) {
			$db->query( "INSERT INTO ".$db->prefix($mydirname."_forum_access")." SET forum_id=$forum_id, uid=".$user->getVar('uid').", can_post=$can_post, can_edit=$can_edit, can_delete=$can_delete, post_auto_approved=$post_auto_approved, is_moderator=$is_moderator" ) ;
		}
	}
	
	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=forum_access&amp;forum_id=$forum_id" , 3 , _MD_D3FORUM_MSG_UPDATED ) ;
	exit ;
}



//
// form stage
//

// create group form
$group_handler =& xoops_gethandler( 'group' ) ;
$groups =& $group_handler->getObjects() ;
$group_trs = '' ;
foreach( $groups as $group ) {
	$gid = $group->getVar('groupid') ;

	$fars = $db->query( "SELECT can_post,can_edit,can_delete,post_auto_approved,is_moderator FROM ".$db->prefix($mydirname."_forum_access")." WHERE groupid=".$group->getVar('groupid')." AND forum_id=$forum_id" ) ;
	if( $db->getRowsNum( $fars ) > 0 ) {
		$can_read = true ;
		list( $can_post , $can_edit , $can_delete , $post_auto_approved , $is_moderator ) = $db->fetchRow( $fars ) ;
	} else {
		$can_post = $can_read = $can_edit = $can_delete = $post_auto_approved = $is_moderator = false ;
	}

	$can_read_checked = $can_read ? "checked='checked'" : "" ;
	$can_post_checked = $can_post ? "checked='checked'" : "" ;
	$can_edit_checked = $can_edit ? "checked='checked'" : "" ;
	$can_delete_checked = $can_delete ? "checked='checked'" : "" ;
	$post_auto_approved_checked = $post_auto_approved ? "checked='checked'" : "" ;
	$is_moderator_checked = $is_moderator ? "checked='checked'" : "" ;
	$group_trs .= "
		<tr>
			<td class='even'>".$group->getVar('name')."</td>
			<td class='even'><input type='checkbox' name='can_reads[$gid]' id='gcol_1_{$gid}' value='1' $can_read_checked /></td>
			<td class='even'><input type='checkbox' name='can_posts[$gid]' id='gcol_2_{$gid}' value='1' $can_post_checked /></td>
			<td class='even'><input type='checkbox' name='can_edits[$gid]' id='gcol_3_{$gid}' value='1' $can_edit_checked /></td>
			<td class='even'><input type='checkbox' name='can_deletes[$gid]' id='gcol_4_{$gid}' value='1' $can_delete_checked /></td>
			<td class='even'><input type='checkbox' name='post_auto_approveds[$gid]' id='gcol_5_{$gid}' value='1' $post_auto_approved_checked /></td>
			<td class='even'><input type='checkbox' name='is_moderators[$gid]' id='gcol_6_{$gid}' value='1' $is_moderator_checked /></td>
		</tr>\n" ;
}


// create user form
$fars = $db->query( "SELECT u.uid,u.uname,fa.can_post,fa.can_edit,fa.can_delete,fa.post_auto_approved,fa.is_moderator FROM ".$db->prefix($mydirname."_forum_access")." fa LEFT JOIN ".$db->prefix("users")." u ON fa.uid=u.uid WHERE fa.forum_id=$forum_id AND fa.groupid IS NULL ORDER BY u.uid ASC" ) ;
$user_trs = '' ;
while( list( $uid , $uname , $can_post , $can_edit , $can_delete , $post_auto_approved , $is_moderator ) = $db->fetchRow( $fars ) ) {

	$uid = intval( $uid ) ;
	$uname4disp = htmlspecialchars( $uname , ENT_QUOTES ) ;

	$can_post_checked = $can_post ? "checked='checked'" : "" ;
	$can_edit_checked = $can_edit ? "checked='checked'" : "" ;
	$can_delete_checked = $can_delete ? "checked='checked'" : "" ;
	$post_auto_approved_checked = $post_auto_approved ? "checked='checked'" : "" ;
	$is_moderator_checked = $is_moderator ? "checked='checked'" : "" ;
	$user_trs .= "
		<tr>
			<td class='even'>$uid</td>
			<td class='even'>$uname4disp</td>
			<td class='even'><input type='checkbox' name='can_reads[$uid]' id='ucol_1_{$uid}' value='1' checked='checked' /></td>
			<td class='even'><input type='checkbox' name='can_posts[$uid]' id='ucol_2_{$uid}' value='1' $can_post_checked /></td>
			<td class='even'><input type='checkbox' name='can_edits[$uid]' id='ucol_3_{$uid}' value='1' $can_edit_checked /></td>
			<td class='even'><input type='checkbox' name='can_deletes[$uid]' id='ucol_4_{$uid}' value='1' $can_delete_checked /></td>
			<td class='even'><input type='checkbox' name='post_auto_approveds[$uid]' id='ucol_5_{$uid}' value='1' $post_auto_approved_checked /></td>
			<td class='even'><input type='checkbox' name='is_moderators[$uid]' id='ucol_6_{$uid}' value='1' $is_moderator_checked /></td>
		</tr>\n" ;
}


// create new user form
$newuser_trs = '' ;
for( $i = 0 ; $i < 5 ; $i ++ ) {
	$newuser_trs .= "
		<tr>
			<td class='head'><input type='text' size='4' name='new_uids[$i]' value='' /></th>
			<td class='head'><input type='text' size='12' name='new_unames[$i]' value='' /></th>
			<td class='head'><input type='checkbox' name='new_can_reads[$i]' id='ncol_1_{$i}' checked='checked' disabled='disabled' /></th>
			<td class='head'><input type='checkbox' name='new_can_posts[$i]' id='ncol_2_{$i}' value='1' /></th>
			<td class='head'><input type='checkbox' name='new_can_edits[$i]' id='ncol_3_{$i}' value='1' /></td>
			<td class='head'><input type='checkbox' name='new_can_deletes[$i]' id='ncol_4_{$i}' value='1' /></td>
			<td class='head'><input type='checkbox' name='new_post_auto_approveds[$i]' id='ncol_5_{$i}' value='1' /></td>
			<td class='head'><input type='checkbox' name='new_is_moderators[$i]' id='ncol_6_{$i}' value='1' /></td>
		</tr>
	\n" ;
}


//
// display stage
//

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
$tpl =& new XoopsTpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_name' => $xoopsModule->getVar('name') ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'forum_id' => $forum_id ,
	'forum_title' => htmlspecialchars( $forum_title , ENT_QUOTES ) ,
	'forum_jumpbox_options' => d3forum_make_jumpbox_options( $mydirname , '1' , '1' , $forum_id ) ,
	'group_trs' => $group_trs ,
	'user_trs' => $user_trs ,
	'newuser_trs' => $newuser_trs ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3forum_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_forum_access.html' ) ;
xoops_cp_footer();

?>