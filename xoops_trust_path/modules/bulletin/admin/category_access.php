<?php
require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/gtickets.php' ;
require_once dirname(dirname(__FILE__)).'/class/bulletinTopic.php' ;
$db =& Database::getInstance() ;

/*
 * topic_access table clean up 2012-2-1 by Yoshis
*/
$sql = "SELECT topic_id FROM ".$db->prefix($mydirname."_topic_access")." GROUP BY topic_id";
$result = $db->query($sql);
while(list($topic_id)=$db->fetchRow( $result ) ){
	$sql = "SELECT count(*) FROM ".$db->prefix($mydirname."_topics")." WHERE topic_id=$topic_id";
	list( $cnt ) = $db->fetchRow( $db->query($sql) );
	if ($cnt==0){
		$sql = "DELETE FROM ".$db->prefix($mydirname."_topic_access")." WHERE topic_id=$topic_id";
		$db->queryF( $sql ) ;
	}
}

// get right $topic_id
$topic_id = isset( $_GET['topic_id'] ) ? intval( $_GET['topic_id'] ) : 0;
$sql = "SELECT topic_id,topic_title FROM ".$db->prefix($mydirname."_topics")." WHERE topic_id=$topic_id";
list( $topic_id , $topic_title ) = $db->fetchRow( $db->query( $sql ) ) ;
if( empty( $topic_id ) ) {
	$invalid_topic_id = true ;
	$sql = "SELECT MIN(topic_id) FROM ".$db->prefix($mydirname."_topics");
	list( $topic_id ) = $db->fetchRow( $db->query( $sql ) );
	if( empty( $topic_id ) ) {
		redirect_header( XOOPS_URL."/modules/$mydirname/index.php?page=makecategory" , 5 , _MD_A_bulletin_ERR_CREATECATEGORYFIRST ) ;
		exit ;
	} else {
		header( "Location: ".XOOPS_URL."/modules/$mydirname/admin/index.php?page=category_access&topic_id=$topic_id" ) ;
		exit ;
	}
}


//
// transaction stage
//

// group update
if( ! empty( $_POST['group_update'] ) && empty( $invaild_topic_id ) ) {
	if ( ! $xoopsGTicket->check( true , 'bulletin_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	$db->query( "DELETE FROM ".$db->prefix($mydirname."_topic_access")." WHERE topic_id=$topic_id AND groupid>0" ) ;
	$result = $db->query( "SELECT groupid FROM ".$db->prefix("groups") ) ;
	while( list( $gid ) = $db->fetchRow( $result ) ) {
		if( ! empty( $_POST['can_reads'][$gid] ) ) {
			$can_post = empty( $_POST['can_posts'][$gid] ) ? 0 : 1 ;
			$can_edit = empty( $_POST['can_edits'][$gid] ) ? 0 : 1 ;
			$can_delete = empty( $_POST['can_deletes'][$gid] ) ? 0 : 1 ;
			$post_auto_approved = empty( $_POST['post_auto_approveds'][$gid] ) ? 0 : 1 ;
			$sql = "INSERT INTO ".$db->prefix($mydirname."_topic_access")." SET topic_id=$topic_id, groupid=$gid, can_post=$can_post, can_edit=$can_edit, can_delete=$can_delete, post_auto_approved=$post_auto_approved";
			$db->query( $sql ) ;
		}
	}
	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=category_access&amp;topic_id=$topic_id" , 3 , _MD_BULLETIN_MSG_UPDATED ) ;
	exit ;
}

// user update
if( ! empty( $_POST['user_update'] ) && empty( $invaild_topic_id ) ) {
	if ( ! $xoopsGTicket->check( true , 'bulletin_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}
	$db->query( "DELETE FROM ".$db->prefix($mydirname."_topic_access")." WHERE topic_id=$topic_id AND uid>0" ) ;
	$can_posts = is_array( @$_POST['can_posts'] ) ? $_POST['can_posts'] : array() ;
	$can_reads = is_array( @$_POST['can_reads'] ) ? $_POST['can_reads'] + $can_posts : $can_posts ;

	foreach( $can_reads as $uid => $can_read ) {
		$uid = intval( $uid ) ;
		if( $can_read ) {
			$can_post = empty( $_POST['can_posts'][$uid] ) ? 0 : 1 ;
			$can_edit = empty( $_POST['can_edits'][$uid] ) ? 0 : 1 ;
			$can_delete = empty( $_POST['can_deletes'][$uid] ) ? 0 : 1 ;
			$post_auto_approved = empty( $_POST['post_auto_approveds'][$uid] ) ? 0 : 1 ;

			$db->query( "INSERT INTO ".$db->prefix($mydirname."_topic_access")." SET topic_id=$topic_id, uid=$uid, can_post=$can_post, can_edit=$can_edit, can_delete=$can_delete, post_auto_approved=$post_auto_approved" ) ;
		}
	}

	$member_hander =& xoops_gethandler( 'member' ) ;
	if( is_array( @$_POST['new_uids'] ) ) foreach( $_POST['new_uids'] as $i => $uid ) {
		$can_post = empty( $_POST['new_can_posts'][$i] ) ? 0 : 1 ;
		$can_edit = empty( $_POST['new_can_edits'][$i] ) ? 0 : 1 ;
		$can_delete = empty( $_POST['new_can_deletes'][$i] ) ? 0 : 1 ;
		$post_auto_approved = empty( $_POST['new_post_auto_approveds'][$i] ) ? 0 : 1 ;

		if( empty( $uid ) ) {
			$criteria = new Criteria( 'uname' , addslashes( @$_POST['new_unames'][$i] ) ) ;
			@list( $user ) = $member_handler->getUsers( $criteria ) ;
		} else {
			$user =& $member_handler->getUser( intval( $uid ) ) ;
		}
		if( is_object( $user ) ) {
			$db->query( "INSERT INTO ".$db->prefix($mydirname."_topic_access")." SET topic_id=$topic_id, uid=".$user->getVar('uid').", can_post=$can_post, can_edit=$can_edit, can_delete=$can_delete, post_auto_approved=$post_auto_approved" ) ;
		}
	}

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=category_access&amp;topic_id=$topic_id" , 3 , _MD_BULLETIN_MSG_UPDATED ) ;
	exit ;
}


//
// form stage
//

// create jump box options as array
//TODO WHY
$sql = "SELECT topic_id,topic_title,topic_pid FROM ".$db->prefix($mydirname."_topics")." ORDER BY topic_pid,topic_title";
$crs = $db->query( $sql ) ;
$topic_options = array() ;
while( list( $id , $title , $depth ) = $db->fetchRow( $crs ) ) {
	$topic_options[ $id ] = str_repeat( '--' , $depth ) . htmlspecialchars( $title , ENT_QUOTES ) ;
}
// Assign Selector ,add ver3.0beta3
$bt = new BulletinTopic( $mydirname ) ;
$topicselbox = $bt->makeTopicSelBox( false , $topic_id , 'topic_id' );

// create group form
$group_handler =& xoops_gethandler( 'group' ) ;
$groups =& $group_handler->getObjects() ;
$group_trs = '' ;
foreach( $groups as $group ) {
	$gid = $group->getVar('groupid') ;
	$sql = "SELECT can_post,can_edit,can_delete,post_auto_approved FROM ".$db->prefix($mydirname."_topic_access")." WHERE groupid=".$group->getVar('groupid')." AND topic_id=$topic_id";
	$fars = $db->query( $sql ) ;
	if( $db->getRowsNum( $fars ) > 0 ) {
		$can_read = true ;
		list( $can_post , $can_edit , $can_delete , $post_auto_approved  ) = $db->fetchRow( $fars ) ;
	} else {
		$can_post = $can_read = $can_edit = $can_delete = $post_auto_approved = false ;
	}
	$can_read_checked = $can_read ? "checked='checked'" : "" ;
	$can_post_checked = $can_post ? "checked='checked'" : "" ;
	$can_edit_checked = $can_edit ? "checked='checked'" : "" ;
	$can_delete_checked = $can_delete ? "checked='checked'" : "" ;
	$post_auto_approved_checked = $post_auto_approved ? "checked='checked'" : "" ;
	$group_trs .= "
		<tr>
			<td class='even'>".$group->getVar('name')."</td>
			<td class='even'><input type='checkbox' name='can_reads[$gid]' id='gcol_1_{$gid}' value='1' $can_read_checked /></td>
			<td class='even'><input type='checkbox' name='can_posts[$gid]' id='gcol_2_{$gid}' value='1' $can_post_checked /></td>
			<td class='even'><input type='checkbox' name='can_edits[$gid]' id='gcol_3_{$gid}' value='1' $can_edit_checked /></td>
			<td class='even'><input type='checkbox' name='can_deletes[$gid]' id='gcol_4_{$gid}' value='1' $can_delete_checked /></td>
			<td class='even'><input type='checkbox' name='post_auto_approveds[$gid]' id='gcol_5_{$gid}' value='1' $post_auto_approved_checked /></td>
		</tr>\n" ;
}


// create user form
$fars = $db->query( "SELECT u.uid,u.uname,fa.can_post,fa.can_edit,fa.can_delete,fa.post_auto_approved FROM ".$db->prefix($mydirname."_topic_access")." fa LEFT JOIN ".$db->prefix("users")." u ON fa.uid=u.uid WHERE fa.topic_id=$topic_id AND fa.groupid IS NULL ORDER BY u.uid ASC" ) ;
$user_trs = '' ;
while( list( $uid , $uname , $can_post , $can_edit , $can_delete , $post_auto_approved  ) = $db->fetchRow( $fars ) ) {

	$uid = intval( $uid ) ;
	$uname4disp = htmlspecialchars( $uname , ENT_QUOTES ) ;
	$can_post_checked = $can_post ? "checked='checked'" : "" ;
	$can_edit_checked = $can_edit ? "checked='checked'" : "" ;
	$can_delete_checked = $can_delete ? "checked='checked'" : "" ;
	$post_auto_approved_checked = $post_auto_approved ? "checked='checked'" : "" ;
	$user_trs .= "
		<tr>
			<td class='even'>$uid</td>
			<td class='even'>$uname4disp</td>
			<td class='even'><input type='checkbox' name='can_reads[$uid]' id='ucol_1_{$uid}' value='1' checked='checked' /></td>
			<td class='even'><input type='checkbox' name='can_posts[$uid]' id='ucol_2_{$uid}' value='1' $can_post_checked /></td>
			<td class='even'><input type='checkbox' name='can_edits[$uid]' id='ucol_3_{$uid}' value='1' $can_edit_checked /></td>
			<td class='even'><input type='checkbox' name='can_deletes[$uid]' id='ucol_4_{$uid}' value='1' $can_delete_checked /></td>
			<td class='even'><input type='checkbox' name='post_auto_approveds[$uid]' id='ucol_5_{$uid}' value='1' $post_auto_approved_checked /></td>
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
		</tr>
	\n" ;
}


//
// display stage
//

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
$tpl = new XoopsTpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_name' => $xoopsModule->getVar('name') ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'topic_id' => $topic_id ,
	'topic_title' => htmlspecialchars( $topic_title , ENT_QUOTES ) ,
	'topic_options' => $topic_options ,
	'group_trs' => $group_trs ,
	'user_trs' => $user_trs ,
	'newuser_trs' => $newuser_trs ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'bulletin_admin') ,
	'topicselbox' => $topicselbox ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_category_access.html' ) ;
xoops_cp_footer();
?>