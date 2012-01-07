<?php

$mytrustdirname = basename( dirname( __FILE__ ) ) ;
$mytrustdirpath = dirname( __FILE__ ) ;

// check $xoopsModule
if ( ! is_object( @$xoopsModule ) ) {
	$module_handler =& xoops_gethandler('module');
	$xoopsModule =& $module_handler->getByDirname($mydirname);
}

// check permission of 'module_admin' of this module
$moduleperm_handler =& xoops_gethandler( 'groupperm' ) ;

if( ! is_object( @$xoopsUser ) || ! $moduleperm_handler->checkRight( 'module_admin' , $xoopsModule->getVar( 'mid' ) , $xoopsUser->getGroups() ) ) die( 'only admin can access this area' ) ;

$xoopsOption['pagetype'] = 'admin' ;
require XOOPS_ROOT_PATH.'/include/cp_functions.php' ;

// D3LanguageManager
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;

if( ! empty( $_GET['lib'] ) ) {
	// common libs (eg. altsys)
	$lib = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $_GET['lib'] ) ;
	$page = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] ) ;

	if( is_file( XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ) ) {
		include XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ;
	} else if( is_file( XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ) ) {
		include XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ;
	} else {
		die( 'wrong request' ) ;
	}
} else {
	// language files (admin.php)
	require_once( $langmanpath ) ;
	$langman =& D3LanguageManager::getInstance() ;
	$langman->read( 'admin.php' , $mydirname , $mytrustdirname , false ) ;

	// fork each pages of this module
	$page = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] ) ;

	if( is_file( "$mytrustdirpath/admin/$page.php" ) ) {
		include "$mytrustdirpath/admin/$page.php" ;
	} else if( is_file( "$mytrustdirpath/admin/index.php" ) ) {
		include "$mytrustdirpath/admin/index.php" ;
	} else {
		die( 'wrong request' ) ;
	}
}
