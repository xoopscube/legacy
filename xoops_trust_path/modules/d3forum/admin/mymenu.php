<?php

/********* mymenu for D3 modules always require altsys>=0.5 ********/

// Deny direct access
if( preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] ) == 'mymenu' ) exit ;

// Skip for ORETEKI XOOPS
if( defined( 'XOOPS_ORETEKI' ) ) return ;

global $xoopsModule ;
if( ! is_object( $xoopsModule ) ) die( '$xoopsModule is not set' )  ;


// language files (modinfo.php)
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'modinfo.php' , $mydirname , $mytrustdirname ) ;

include dirname(dirname(__FILE__)).'/admin_menu.php' ;

$adminmenu = array_merge( $adminmenu , $adminmenu4altsys ) ;

$mymenu_uri = empty( $mymenu_fake_uri ) ? $_SERVER['REQUEST_URI'] : $mymenu_fake_uri ;
$mymenu_link = substr( strstr( $mymenu_uri , '/admin/' ) , 1 ) ;


// highlight
foreach( array_keys( $adminmenu ) as $i ) {
	if( $mymenu_link == $adminmenu[$i]['link'] ) {
		$adminmenu[$i]['selected'] = true ;
		$adminmenu_hilighted = true ;
		$GLOBALS['altsysAdminPageTitle'] = $adminmenu[$i]['title'] ;
	} else {
		$adminmenu[$i]['selected'] = false ;
	}
}
if( empty( $adminmenu_hilighted ) ) {
	foreach( array_keys( $adminmenu ) as $i ) {
		if( stristr( $mymenu_uri , $adminmenu[$i]['link'] ) ) {
			$adminmenu[$i]['selected'] = true ;
			$GLOBALS['altsysAdminPageTitle'] = $adminmenu[$i]['title'] ;
			break ;
		}
	}
}

// link conversion from relative to absolute
foreach( array_keys( $adminmenu ) as $i ) {
	if( stristr( $adminmenu[$i]['link'] , XOOPS_URL ) === false ) {
		$adminmenu[$i]['link'] = XOOPS_URL."/modules/$mydirname/" . $adminmenu[$i]['link'] ;
	}
}

// display
require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
$tpl = new D3Tpl() ;
$tpl->assign( array(
	'adminmenu' => $adminmenu ,
) ) ;
$tpl->display( 'db:altsys_inc_mymenu.html' ) ;

?>