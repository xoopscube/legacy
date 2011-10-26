<?php

require_once dirname(dirname(__FILE__)).'/include/main_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/d3forum.textsanitizer.php' ;
require_once XOOPS_ROOT_PATH.'/class/pagenav.php' ;
$myts =& D3forumTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

//
// form stage
//

$pos = intval( @$_GET['pos'] ) ;
$num = empty( $_GET['num'] ) ? 50 : intval( $_GET['num'] ) ;
$request = d3forum_common_simple_request( array( 'p.topic_id' => 'int' , 'p.post_id' => 'int' , 'ph.data' => 'like' ) ) ;

list( $hits ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_post_histories")." ph LEFT JOIN ".$db->prefix($mydirname."_posts")." p ON ph.post_id=p.post_id WHERE {$request['whr']}" ) ) ;
$result = $db->query( "SELECT ph.history_id,ph.post_id,ph.history_time,ph.data,p.subject FROM ".$db->prefix($mydirname."_post_histories")." ph LEFT JOIN ".$db->prefix($mydirname."_posts")." p ON ph.post_id=p.post_id WHERE {$request['whr']} ORDER BY ph.history_time DESC LIMIT $pos,$num") ;
$navi_obj = new XoopsPageNav( $hits , $num , $pos , 'pos' , htmlspecialchars( 'page=post_histories&'.$request['query'] ) ) ;

$histories4assign = array() ;
while( list( $history_id , $post_id , $history_time , $data_serialized , $subject ) = $db->fetchRow( $result ) ) {
	$histories4assign[] = array(
		'id' => $history_id ,
		'post_id' => $post_id ,
		'history_time' => $history_time ,
		'history_time_formatted' => formatTimestamp( $history_time ) ,
		'data' => unserialize( $data_serialized ) ,
		'subject_raw' => $subject ,
	) ;
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
	'histories' => $histories4assign ,
	'pagenavi' => $navi_obj->renderNav() ,
	'requests' => $request['requests'] ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_post_histories.html' ) ;
xoops_cp_footer();

?>