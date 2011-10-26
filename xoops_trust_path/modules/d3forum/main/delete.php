<?php

include dirname(dirname(__FILE__)).'/include/common_prepend.php';
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;

$post_id = intval( @$_GET['post_id'] ) ;

// get this "post" from given $post_id
$sql = "SELECT * FROM ".$db->prefix($mydirname."_posts")." WHERE post_id=$post_id" ;
if( ! $prs = $db->query( $sql ) ) die( _MD_D3FORUM_ERR_SQL.__LINE__ ) ;
if( $db->getRowsNum( $prs ) <= 0 ) die( _MD_D3FORUM_ERR_READPOST ) ;
$post_row = $db->fetchArray( $prs ) ;
$topic_id = intval( $post_row['topic_id'] ) ;

// get&check this topic ($topic4assign, $topic_row, $forum_id), count topic_view up, get $prev_topic, $next_topic
include dirname(dirname(__FILE__)).'/include/process_this_topic.inc.php' ;

// get&check this forum ($forum4assign, $forum_row, $cat_id, $isadminormod), override options
if( ! include dirname(dirname(__FILE__)).'/include/process_this_forum.inc.php' ) die( _MD_D3FORUM_ERR_READFORUM ) ;

// get&check this category ($category4assign, $category_row), override options
if( ! include dirname(dirname(__FILE__)).'/include/process_this_category.inc.php' ) die( _MD_D3FORUM_ERR_READCATEGORY ) ;

// get $post4assign
include dirname(dirname(__FILE__)).'/include/process_this_post.inc.php' ;

// check delete permission
if( empty( $can_delete ) ) die( _MD_D3FORUM_ERR_DELETEPOST ) ;

// count children
include_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;
$mytree = new XoopsTree( $db->prefix($mydirname."_posts") , "post_id" , "pid" ) ;
$children = $mytree->getAllChildId( $post_id ) ;

// special permission check for delete
if( $isadminormod ) {
	// admin delete
	// ok
} else if( ( $uid == $post_row['uid'] || $uid == $post_row['uid_hidden'] ) && $xoopsModuleConfig['selfdellimit'] > 0 ) {
	// self delete
	if( time() < $post_row['post_time'] + intval( $xoopsModuleConfig['selfdellimit'] ) ) {
		// before time limit
		if( count( $children ) > 0 ) {
			// child(ren) exist(s)
			redirect_header( XOOPS_URL."/modules/$mydirname/index.php?post_id=$post_id" , 2 , _MD_D3FORUM_DELCHILDEXISTS ) ;
			exit ;
		} else {
			// all green for self delete
		}
	} else {
		// after time limit
		redirect_header( XOOPS_URL."/modules/$mydirname/index.php?post_id=$post_id" , 2 , _MD_D3FORUM_DELTIMELIMITED ) ;
		exit ;
	}
} else {
	// no perm
	die( _MD_D3FORUM_DELNOTALLOWED ) ;
}


if( ! empty( $_POST['deletepostsok'] ) ) {
	// TRANSACTION PART
	if ( ! $xoopsGTicket->check( true , 'd3forum' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	// guest's delete (check password)
	if( empty( $uid ) ) {
		if( empty( $_POST['guest_pass'] ) || md5( $_POST['guest_pass'].'d3forum' ) != $post_row['guest_pass_md5'] ) {
			redirect_header( XOOPS_URL."/modules/$mydirname/index.php?post_id=$post_id" , 2 , _MD_D3FORUM_ERR_GUESTPASSMISMATCH ) ;
			exit ;
		}
	}

	require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
	d3forum_delete_post_recursive( $mydirname , $post_id ) ;
	d3forum_sync_topic( $mydirname , $topic_id ) ;

	if( $topic_row['topic_first_post_id'] == $post_id ) {
		redirect_header( XOOPS_URL."/modules/$mydirname/index.php?forum_id=$forum_id" , 2 , _MD_D3FORUM_MSG_POSTSDELETED ) ;
		exit ;
	} else {
		redirect_header( XOOPS_URL."/modules/$mydirname/index.php?topic_id=$topic_id" , 2 , _MD_D3FORUM_MSG_POSTSDELETED ) ;
		exit ;
	}

} else {
	// FORM PART

	// references to confirm the post will be deleted
	$reference_message4html = $myts->displayTarea( $post_row['post_text'] , $post_row['html'] , $post_row['smiley'] , $post_row['xcode'] , $xoopsModuleConfig['allow_textimg'] , $post_row['br'] , 0 , $post_row['number_entity'] , $post_row['special_entity'] ) ;
	$reference_time = intval( $post_row['post_time'] ) ;
	if( ! empty( $post_row['guest_name'] ) ) {
		$reference_name4html = htmlspecialchars( $post_row['guest_name'] , ENT_QUOTES ) ;
	} else if( $post_row['uid'] ) {
		$reference_name4html = XoopsUser::getUnameFromId( $post_row['uid'] ) ;
	} else {
		$reference_name4html = $xoopsModuleConfig['anonymous_name'] ;
	}
	$reference_subject4html = $myts->makeTboxData4Show( $post_row['subject'] , $post_row['number_entity'] , $post_row['special_entity'] ) ;

	// dare to set 'template_main' after header.php (for disabling cache)
	include XOOPS_ROOT_PATH."/header.php";
	$xoopsOption['template_main'] = $mydirname.'_main_delete.html' ;

	$xoopsTpl->assign( array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
		'mod_config' => $xoopsModuleConfig ,
		'mode' => 'delete' ,
		'post_id' => $post_id ,
		'reference_subject' => @$reference_subject4html ,
		'reference_message' => @$reference_message4html ,
		'reference_name' => @$reference_name4html ,
		'reference_time' => @$reference_time ,
		'reference_time_formatted' => formatTimestamp( @$reference_time , 'm' ) ,
		'children_count' => count( $children ) ,
		'category' => $category4assign ,
		'forum' => $forum4assign ,
		'topic' => $topic4assign ,
		'post' => $post4assign ,
		'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3forum') ,
		'xoops_module_header' => "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".str_replace('{mod_url}',XOOPS_URL.'/modules/'.$mydirname,$xoopsModuleConfig['css_uri'])."\" />" . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
		'xoops_pagetitle' => _DELETE ,
		'xoops_breadcrumbs' => array_merge( $xoops_breadcrumbs , array( array( 'name' => _DELETE ) ) ) ,
	) ) ;

	include XOOPS_ROOT_PATH.'/footer.php';
}

?>