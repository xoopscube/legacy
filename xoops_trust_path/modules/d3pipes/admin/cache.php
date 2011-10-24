<?php

require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/admin_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;


//
// transaction stage
//

if( ! empty( $_POST['do_deletefetchcache'] ) ) {

	d3pipes_common_delete_all_cache( $mydirname ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=cache" , 3 , _MD_A_D3PIPES_MSG_CACHEDELETED ) ;
	exit ;
}

if( ! empty( $_POST['do_clearlastfetch'] ) ) {
	$db->query( "UPDATE ".$db->prefix($mydirname."_pipes")." SET lastfetch_time=0" ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=cache" , 3 , _MD_A_D3PIPES_MSG_PIPEUPDATED ) ;
	exit ;
}


//
// form stage
//


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
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_cache.html' ) ;
xoops_cp_footer();

?>