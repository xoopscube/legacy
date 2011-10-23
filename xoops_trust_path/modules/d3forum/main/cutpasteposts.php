<?php

include dirname(dirname(__FILE__)).'/include/common_prepend.php';

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

// count children
include_once XOOPS_ROOT_PATH."/class/xoopstree.php" ;
$mytree = new XoopsTree( $db->prefix($mydirname."_posts") , "post_id" , "pid" ) ;
$children = $mytree->getAllChildId( $post_id ) ;

// special permission check for cutpasteposts
if( ! $isadminormod ) die( _MD_D3FORUM_ERR_MODERATEFORUM ) ;

if( ! empty( $_POST['cutpastepostsok'] ) ) {
	// TRANSACTION PART
	require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
	$new_pid = intval( @$_POST['pid'] ) ;
	$new_forum_id = intval( @$_POST['forum_id'] ) ;

	if( empty( $new_pid ) && empty( $new_forum_id ) ) die( _MD_D3FORUM_ERR_NOSPECIFICID ) ;

	list( $new_topic_id , $new_forum_id ) = d3forum_cutpasteposts( $mydirname , $post_id , $new_pid , $new_forum_id , $forum_permissions , $isadmin ) ;
	d3forum_sync_topic( $mydirname , $topic_id ) ;
	d3forum_sync_topic( $mydirname , $new_topic_id ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/index.php?topic_id=$new_topic_id" , 2 , _MD_D3FORUM_CUTPASTESUCCESS ) ;
	exit ;

} else {
	// FORM PART

	// references to confirm the post will be deleted
	$reference_message4html = $myts->displayTarea( $post_row['post_text'] , $post_row['html'] , $post_row['smiley'] , $post_row['xcode'] , $xoopsModuleConfig['allow_textimg'] , $post_row['br'] ) ;
	$reference_time = intval( $post_row['post_time'] ) ;
	if( ! empty( $post_row['guest_name'] ) ) {
		$reference_name4html = htmlspecialchars( $post_row['guest_name'] , ENT_QUOTES ) ;
	} else if( $post_row['uid'] ) {
		$reference_name4html = XoopsUser::getUnameFromId( $post_row['uid'] ) ;
	} else {
		$reference_name4html = $xoopsModuleConfig['anonymous_name'] ;
	}
	$reference_subject4html = $myts->makeTboxData4Show( $post_row['subject'] ) ;

	// get target forums
	$jump_box_forums = array() ;
	foreach( $forum_permissions as $forum_id => $perms ) {
		if( $perms['is_moderator'] ) $jump_box_forums[] = $forum_id ;
	}
	$whr4forum_jump_box = empty( $jump_box_forums ) ? '0' : 'f.forum_id IN ('.implode(',',$jump_box_forums).')' ;

	// dare to set 'template_main' after header.php (for disabling cache)
	include XOOPS_ROOT_PATH."/header.php";
	$xoopsOption['template_main'] = $mydirname.'_main_cutpasteposts.html' ;

	$xoopsTpl->assign( array(
		'mydirname' => $mydirname ,
		'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
		'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
		'mod_config' => $xoopsModuleConfig ,
		'mode' => 'delete' ,
		'post_id' => $post_id ,
		'post' => array( 'pid' => intval( $post_row['pid'] ) ) ,
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
		'forum_jumpbox_options' => d3forum_make_jumpbox_options( $mydirname , '1' , $isadmin ? '1' : $whr4forum_jump_box , $forum_row['forum_id'] ) ,
		'xoops_module_header' => "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"".str_replace('{mod_url}',XOOPS_URL.'/modules/'.$mydirname,$xoopsModuleConfig['css_uri'])."\" />" . $xoopsTpl->get_template_vars( "xoops_module_header" ) ,
		'xoops_pagetitle' => _MD_D3FORUM_CUTPASTEPOSTS ,
		'xoops_breadcrumbs' => array_merge( $xoops_breadcrumbs , array( array( 'name' => _MD_D3FORUM_CUTPASTEPOSTS ) ) ) ,
	) ) ;

	include XOOPS_ROOT_PATH.'/footer.php';
}

?>