<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/admin_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

// d3forum integration
$d3comment_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $xoopsModuleConfig['comment_dirname'] ) ;
$d3comment_forum_id = intval( $xoopsModuleConfig['comment_forum_id'] ) ;
if( ! file_exists( XOOPS_ROOT_PATH.'/modules/'.$d3comment_dirname.'/mytrustdirname.php' ) ) $d3comment_forum_id = 0 ;
if( $d3comment_forum_id > 0 ) {
	$d3comment_join4sql = "LEFT JOIN ".$db->prefix($d3comment_dirname."_topics")." t ON t.forum_id=$d3comment_forum_id AND t.topic_external_link_id=c.clipping_id" ;
} else {
	$d3comment_join4sql = '' ;
}

//
// transaction stage
//

$target_clipping_ids = array() ;
$olderthan = 14 ;

// protect
$whr_protect = '1' ;
if( ! empty( $_POST['protectbycomment'] ) && $d3comment_forum_id > 0 ) {
	$whr_protect .= " AND t.topic_id IS NULL" ;
}
if( ! empty( $_POST['protectbyhighlight'] ) ) {
	$whr_protect .= " AND ! c.highlight" ;
}


if( ! empty( $_POST['do_deleteunlinks'] ) || ! empty( $_POST['count_deleteunlinks'] ) ) {
	$result = $db->query( "SELECT c.clipping_id FROM ".$db->prefix($mydirname."_clippings")." c LEFT JOIN ".$db->prefix($mydirname."_pipes")." p ON c.pipe_id=p.pipe_id $d3comment_join4sql WHERE p.pipe_id IS NULL AND ($whr_protect)" ) ;

	while( list( $clipping_id ) = $db->fetchRow( $result ) ) {
		$target_clipping_ids[] = intval( $clipping_id ) ;
	}
}


if( ! empty( $_POST['do_deleteolder'] ) || ! empty( $_POST['count_deleteolder'] ) ) {
	$olderthan = intval( @$_POST['olderthan'] ) ;
	$result = $db->query( "SELECT clipping_id FROM ".$db->prefix($mydirname."_clippings")." c $d3comment_join4sql WHERE c.pubtime<UNIX_TIMESTAMP()-($olderthan*86400) AND ($whr_protect)" ) ;

	while( list( $clipping_id ) = $db->fetchRow( $result ) ) {
		$target_clipping_ids[] = intval( $clipping_id ) ;
	}
}


if( ( ! empty( $_POST['do_deleteunlinks'] ) || ! empty( $_POST['do_deleteolder'] ) ) && ! empty( $target_clipping_ids ) ) {
	if ( ! $xoopsGTicket->check( true , 'd3pipes_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	$db->query( "DELETE FROM ".$db->prefix($mydirname."_clippings")." WHERE clipping_id IN (".implode(",",$target_clipping_ids).")" ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=clipping" , 3 , _MD_A_D3PIPES_MSG_CLIPPINGUPDATED ) ;
	exit ;
}

//
// form stage
//

list( $total_clippings ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_clippings") ) ) ;
$selected_clippings = sizeof( $target_clipping_ids ) ;

//
// display stage
//

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
$tpl = new D3Tpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_name' => $xoopsModule->getVar('name') ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'total_clippings' => $total_clippings ,
	'selected_clippings' => empty( $_POST ) ? -1 : $selected_clippings ,
	'olderthan' => $olderthan ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'd3pipes_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_clipping.html' ) ;
xoops_cp_footer();

?>