<?php

require_once dirname(dirname(__FILE__)).'/include/main_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/common_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/transact_functions.php' ;
require_once dirname(dirname(__FILE__)).'/include/import_functions.php' ;
require_once dirname(dirname(__FILE__)).'/class/gtickets.php' ;
$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;


$module_handler =& xoops_gethandler( 'module' ) ;
$modules =& $module_handler->getObjects() ;
$importable_modules = array() ;
foreach( $modules as $module ) {
	$mid = $module->getVar('mid') ;
	$dirname = $module->getVar('dirname') ;
	$dirpath = XOOPS_ROOT_PATH.'/modules/'.$dirname ;
	$mytrustdirname = '' ;
	$tables = $module->getInfo('tables') ;
	if( file_exists( $dirpath.'/mytrustdirname.php' ) ) {
		include $dirpath.'/mytrustdirname.php' ;
	}
	if( $mytrustdirname == 'pico' && $dirname != $mydirname ) {
		// pico
		$importable_modules[$mid] = 'pico:'.$module->getVar('name')." ($dirname)" ;
	} else if( stristr( @$tables[0] , 'tinycontent' ) ) {
		// tinyd
		$importable_modules[$mid] = 'tinyd:'.$module->getVar('name')." ($dirname)" ;
	} else if( substr( @$tables[4] , -10 ) == '_mimetypes' && substr( @$tables[3] , -5 ) == '_meta' && substr( @$tables[2] , -6 ) == '_files' ) {
		$importable_modules[$mid] = 'smartsection:'.$module->getVar('name')." ($dirname)" ;
	}
}

//
// transaction stage
//

if( ! empty( $_POST['do_import'] ) && ! empty( $_POST['import_mid'] ) ) {
	@set_time_limit( 0 ) ;

	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	$import_mid = intval( @$_POST['import_mid'] ) ;
	if( empty( $importable_modules[ $import_mid ] ) ) die( _MD_A_PICO_ERR_INVALIDMID ) ;
	list( $fromtype , ) = explode( ':' , $importable_modules[ $import_mid ] ) ;
	switch( $fromtype ) {
		case 'pico' :
			pico_import_from_pico( $mydirname , $import_mid ) ;
			break ;
		case 'tinyd' :
			pico_import_from_tinyd( $mydirname , $import_mid ) ;
			break ;
		case 'smartsection' :
			pico_import_from_smartsection( $mydirname , $import_mid ) ;
			break ;
	}

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , _MD_A_PICO_MSG_IMPORTDONE ) ;
	exit ;

} else if( ! empty( $_POST['do_syncall'] ) ) {
	@set_time_limit( 0 ) ;

	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	pico_sync_all( $mydirname ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , _MD_A_PICO_MSG_SYNCALLDONE ) ;
	exit ;
} else if( ! empty( $_POST['do_clearbodycache'] ) ) {
	@set_time_limit( 0 ) ;

	if ( ! $xoopsGTicket->check( true , 'pico_admin' ) ) {
		redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
	}

	pico_clear_body_cache( $mydirname ) ;

	redirect_header( XOOPS_URL."/modules/$mydirname/admin/index.php?page=import" , 3 , _MD_A_PICO_MSG_CLEARBODYCACHEDONE ) ;
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
$tpl = new XoopsTpl() ;
$tpl->assign( array(
	'mydirname' => $mydirname ,
	'mod_name' => $xoopsModule->getVar('name') ,
	'mod_url' => XOOPS_URL.'/modules/'.$mydirname ,
	'mod_imageurl' => XOOPS_URL.'/modules/'.$mydirname.'/'.$xoopsModuleConfig['images_dir'] ,
	'mod_config' => $xoopsModuleConfig ,
	'import_from_options' => $importable_modules ,
	'gticket_hidden' => $xoopsGTicket->getTicketHtml( __LINE__ , 1800 , 'pico_admin') ,
) ) ;
$tpl->display( 'db:'.$mydirname.'_admin_import.html' ) ;
xoops_cp_footer();

?>